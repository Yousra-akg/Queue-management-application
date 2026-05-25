<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Candidat;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SessionManagementController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function dashboard()
    {
        $totalCandidats = Candidat::count();
        $totalSessions = Session::count();
        $sessionsTerminees = Session::where('statut', 'terminée')->count();
        
        $totalPresents = Candidat::where('is_present', true)->count();
        $tauxPresence = $totalCandidats > 0 ? round(($totalPresents / $totalCandidats) * 100, 1) : 0;

        $sessions = Session::withCount('candidats')->orderBy('dateEntretien', 'desc')->take(4)->get();
        foreach ($sessions as $session) {
            $session->updateStatusBasedOnTime();
        }
        
        // Dynamic Activity Feed
        $latestSessions = Session::orderBy('created_at', 'desc')->take(2)->get()->map(function($s) {
            return [
                'type' => 'session',
                'titre' => 'Nouvelle Session : ' . $s->nom,
                'temps' => $s->created_at->diffForHumans(),
                'couleur' => 'blue-600',
                'date' => $s->created_at
            ];
        });

        $latestAssignments = Candidat::whereNotNull('session_id')
            ->with('session')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($c) {
                return [
                    'type' => 'candidat',
                    'titre' => $c->prenom . ' ' . $c->nom . ' assigné à ' . ($c->session->nom ?? 'Session'),
                    'temps' => $c->updated_at->diffForHumans(),
                    'couleur' => 'emerald-500',
                    'date' => $c->updated_at
                ];
            });

        $activites = $latestSessions->merge($latestAssignments)->sortByDesc('date')->take(5);

        return view('admin.dashboard', compact(
            'totalCandidats',
            'totalSessions',
            'sessionsTerminees',
            'tauxPresence',
            'sessions',
            'activites'
        ));
    }

    public function affectations()
    {
        $availableCandidates = Candidat::whereNull('session_id')->get();
        $allSessions = Session::all();
        foreach ($allSessions as $session) {
            $session->updateStatusBasedOnTime();
        }

        $sessions = Session::with('candidats')->withCount('candidats')->orderBy('dateEntretien', 'desc')->get();
        
        return view('admin.affectations', [
            'availableCandidates' => $availableCandidates,
            'sessions' => $sessions
        ]);
    }

    public function sessions()
    {
        // View for CRUD sessions only
        $sessions = Session::withCount('candidats')->orderBy('dateEntretien', 'desc')->get();
        foreach ($sessions as $session) {
            $session->updateStatusBasedOnTime();
        }
        return view('admin.sessions.index', [
            'sessions' => $sessions
        ]);
    }

    public function candidats()
    {
        // View for CRUD candidats only
        $candidats = Candidat::with('session')->orderBy('id', 'desc')->get();
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

        $validated['user_id'] = Auth::guard('web')->id();

        if (stripos($validated['nom'], 'session ') !== 0) {
            $validated['nom'] = 'Session ' . ucfirst($validated['nom']);
        }

        Session::create($validated);

        return redirect()->back()->with('success', 'Session créée avec succès.');
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

        if (stripos($validated['nom'], 'session ') !== 0) {
            $validated['nom'] = 'Session ' . ucfirst($validated['nom']);
        }

        $session->update($validated);

        return redirect()->back()->with('success', 'Session mise à jour avec succès.');
    }

    public function destroySession(Session $session)
    {
        $session->delete();
        return redirect()->back()->with('success', 'Session supprimée avec succès.');
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

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('candidats', 'public');
        }

        Candidat::create($validated);

        return redirect()->back()->with('success', 'Candidat ajouté avec succès.');
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

        if ($request->hasFile('photo')) {
            if ($candidat->photo) {
                Storage::disk('public')->delete($candidat->photo);
            }
            $validated['photo'] = $request->file('photo')->store('candidats', 'public');
        }

        $candidat->update($validated);

        return redirect()->back()->with('success', 'Candidat mis à jour avec succès.');
    }

    public function destroyCandidate(Candidat $candidat)
    {
        if ($candidat->photo) {
            Storage::disk('public')->delete($candidat->photo);
        }
        $candidat->delete();
        return redirect()->back()->with('success', 'Candidat supprimé avec succès.');
    }

    public function assignCandidates(Request $request, Session $session)
    {
        $request->validate([
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidats,id'
        ]);

        $currentCount = $session->candidats()->count();
        $newCount = count($request->candidate_ids);

        if ($currentCount + $newCount > $session->capaciteMax) {
            return response()->json([
                'message' => 'La session a atteint sa capacité maximale (' . $session->capaciteMax . ' places).'
            ], 422);
        }

        foreach ($request->candidate_ids as $candidateId) {
            $candidat = Candidat::find($candidateId);
            $candidat->update(['session_id' => $session->id]);
            // Generate ticket automatically SOLI-XX
            $this->ticketService->generateTicket($candidat->id);
        }
        return response()->json(['message' => 'Candidats affectés avec succès.', 'session_id' => $session->id]);
    }

    public function unassignCandidate(Candidat $candidate)
    {
        // Bloquer le retrait si la session est terminée et que le candidat était présent
        if ($candidate->session && $candidate->session->statut === 'terminée' && $candidate->is_present) {
            return response()->json([
                'message' => 'Impossible de retirer un candidat présent d\'une session terminée.'
            ], 422);
        }

        $candidate->update(['session_id' => null]);
        // Supprimer le ticket s'il existe
        $candidate->ticket()->delete();

        return response()->json(['message' => 'Candidat retiré de la session.']);
    }
}
