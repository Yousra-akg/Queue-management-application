<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Candidat;
use App\Services\SessionService;
use App\Services\CandidatService;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;

class SessionManagementController extends Controller
{
    protected $sessionService;
    protected $candidatService;
    protected $ticketService;

    public function __construct(
        SessionService $sessionService,
        CandidatService $candidatService,
        TicketService $ticketService
    ) {
        $this->sessionService = $sessionService;
        $this->candidatService = $candidatService;
        $this->ticketService = $ticketService;
    }

    public function dashboard()
    {
        $stats = $this->sessionService->getDashboardStatsAdmin();
        $sessions = $this->sessionService->getSessionsWithStatusUpdate()->take(4);
        $sessions->loadCount('candidats');
        $activites = $this->sessionService->getRecentActivitiesFeed(5);

        return view('admin.dashboard', array_merge($stats, compact('sessions', 'activites')));
    }

    public function affectations()
    {
        $availableCandidates = $this->candidatService->getUnassigned();
        $sessions = $this->sessionService->getSessionsWithStatusUpdate();
        $sessions->load(['candidats'])->loadCount('candidats');
        
        return view('admin.affectations', [
            'availableCandidates' => $availableCandidates,
            'sessions' => $sessions
        ]);
    }

    public function sessions()
    {
        $sessions = $this->sessionService->getSessionsWithStatusUpdate();
        $sessions->loadCount('candidats');
        return view('admin.sessions.index', [
            'sessions' => $sessions
        ]);
    }

    public function candidats()
    {
        $candidats = $this->candidatService->all()->sortByDesc('id')->values();
        $candidats->load('session');
        return view('admin.candidats.index', [
            'candidats' => $candidats
        ]);
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'dateEntretien' => 'required|date',
            'heureDebut' => 'required',
            'heureFin' => 'required',
            'capaciteMax' => 'required|integer|min:1',
            'codePresence' => 'required|string|max:10',
            'statut' => 'required|in:planifiée,en cours,terminée,annulée',
        ]);

        try {
            $this->sessionService->createSession($validated, Auth::guard('web')->id());
            return redirect()->back()->with('success', 'Session créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()]);
        }
    }

    public function updateSession(Request $request, Session $session)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'dateEntretien' => 'required|date',
            'heureDebut' => 'required',
            'heureFin' => 'required',
            'capaciteMax' => 'required|integer|min:1',
            'codePresence' => 'required|string|max:10',
            'statut' => 'required|in:planifiée,en cours,terminée,annulée',
        ]);

        try {
            $this->sessionService->updateSession($session->id, $validated);
            return redirect()->back()->with('success', 'Session mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
        }
    }

    public function destroySession(Session $session)
    {
        try {
            $this->sessionService->deleteSession($session->id);
            return redirect()->back()->with('success', 'Session supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
        }
    }

    public function storeCandidate(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'cin' => 'required|string|max:20|unique:candidats,cin',
            'scoreQCM' => 'required|numeric|min:0|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $this->candidatService->createCandidate($validated, $request->file('photo'));
            return redirect()->back()->with('success', 'Candidat ajouté avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'ajout : ' . $e->getMessage()]);
        }
    }

    public function updateCandidate(Request $request, Candidat $candidat)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'cin' => 'required|string|max:50|unique:candidats,cin,' . $candidat->id,
            'scoreQCM' => 'required|numeric|min:0|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $this->candidatService->updateCandidate($candidat->id, $validated, $request->file('photo'));
            return redirect()->back()->with('success', 'Candidat mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
        }
    }

    public function destroyCandidate(Candidat $candidat)
    {
        try {
            $this->candidatService->deleteCandidate($candidat->id);
            return redirect()->back()->with('success', 'Candidat supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
        }
    }

    public function assignCandidates(Request $request, Session $session)
    {
        $request->validate([
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidats,id'
        ]);

        try {
            $this->candidatService->assignCandidatesToSession($session->id, $request->candidate_ids);
            return response()->json(['message' => 'Candidats affectés avec succès.', 'session_id' => $session->id]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function unassignCandidate(Candidat $candidate)
    {
        try {
            $this->candidatService->unassignCandidateFromSession($candidate->id);
            return response()->json(['message' => 'Candidat retiré de la session.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
