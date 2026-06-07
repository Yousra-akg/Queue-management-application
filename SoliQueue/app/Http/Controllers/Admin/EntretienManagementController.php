<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entretien;
use App\Models\Candidat;
use App\Services\EntretienService;
use App\Services\CandidatService;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;

class EntretienManagementController extends Controller
{
    protected $entretienService;
    protected $candidatService;
    protected $ticketService;

    public function __construct(
        EntretienService $entretienService,
        CandidatService $candidatService,
        TicketService $ticketService
    ) {
        $this->entretienService = $entretienService;
        $this->candidatService = $candidatService;
        $this->ticketService = $ticketService;
    }

    public function dashboard()
    {
        $stats = $this->entretienService->getDashboardStatsAdmin();
        $entretiens = $this->entretienService->getEntretiensWithStatusUpdate()->take(4);
        $entretiens->loadCount('candidats');
        $activites = $this->entretienService->getRecentActivitiesFeed(5);

        return view('admin.dashboard', array_merge($stats, compact('entretiens', 'activites')));
    }

    public function affectations()
    {
        $availableCandidates = $this->candidatService->getUnassigned();
        $entretiens = $this->entretienService->getEntretiensWithStatusUpdate();
        $entretiens->load(['candidats'])->loadCount('candidats');
        
        return view('admin.affectations', [
            'availableCandidates' => $availableCandidates,
            'entretiens' => $entretiens
        ]);
    }

    public function entretiens()
    {
        $entretiens = $this->entretienService->getEntretiensWithStatusUpdate();
        $entretiens->loadCount('candidats');
        $entretiens->load('formateurs'); // Load formateurs with pivot salle_id
        
        $salles = \App\Models\Salle::all();
        $formateurs = \App\Models\User::role('formateur')->get();
        return view('admin.entretiens.index', [
            'entretiens' => $entretiens,
            'salles' => $salles,
            'formateurs' => $formateurs
        ]);
    }

    public function candidats()
    {
        $candidats = $this->candidatService->all()->sortByDesc('id')->values();
        $candidats->load('entretien');
        return view('admin.candidats.index', [
            'candidats' => $candidats
        ]);
    }

    public function storeEntretien(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:entretiens,nom',
            'dateEntretien' => 'required|date',
            'heureDebut' => 'required',
            'heureFin' => 'required',
            'capaciteMax' => 'required|integer|min:1',
            'codePresence' => 'required|string|size:4',
            'statut' => 'required|in:planifiée,en cours,terminée,annulée',
        ]);

        try {
            $this->entretienService->createEntretien($validated, Auth::guard('web')->id());
            return redirect()->back()->with('success', 'Entretien créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()]);
        }
    }

    public function updateEntretien(Request $request, Entretien $entretien)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:entretiens,nom,' . $entretien->id,
            'dateEntretien' => 'required|date',
            'heureDebut' => 'required',
            'heureFin' => 'required',
            'capaciteMax' => 'required|integer|min:1',
            'codePresence' => 'required|string|size:4',
            'statut' => 'required|in:planifiée,en cours,terminée,annulée',
        ]);

        try {
            $this->entretienService->updateEntretien($entretien->id, $validated);
            return redirect()->back()->with('success', 'Entretien mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
        }
    }

    public function destroyEntretien(Entretien $entretien)
    {
        try {
            $this->entretienService->deleteEntretien($entretien->id);
            return redirect()->back()->with('success', 'Entretien supprimée avec succès.');
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

    public function assignCandidates(Request $request, Entretien $entretien)
    {
        $request->validate([
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidats,id'
        ]);

        try {
            $this->candidatService->assignCandidatesToEntretien($entretien->id, $request->candidate_ids);
            return response()->json(['message' => 'Candidats affectés avec succès.', 'entretien_id' => $entretien->id]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function unassignCandidate(Candidat $candidate)
    {
        try {
            $this->candidatService->unassignCandidateFromEntretien($candidate->id);
            return response()->json(['message' => 'Candidat retiré de la entretien.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}

