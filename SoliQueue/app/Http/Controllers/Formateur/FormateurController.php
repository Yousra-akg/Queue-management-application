<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use App\Models\Ticket;
use App\Models\Formateur;

class FormateurController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('formateur.sessions');
        }
        return view('formateur.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            // Allow access to both 'formateur' and 'admin' roles
            if ($user->hasRole('formateur') || $user->hasRole('admin')) {
                $request->session()->regenerate();
                return redirect()->route('formateur.sessions');
            }

            // If no valid role, deny access
            Auth::logout();
            return back()->withErrors(['email' => 'Accès réservé aux formateurs et administrateurs.'])->onlyInput('email');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('formateur.login');
    }

    public function selectionSession()
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Session> $sessions */
        $sessions = Session::orderBy('dateEntretien', 'desc')->get();
        foreach ($sessions as $session) {
            $session->updateStatusBasedOnTime();
        }
        return view('formateur.selection', compact('sessions'));
    }

    public function dashboard(Session $session)
    {
        $tickets = Ticket::where('session_id', $session->id)
            ->with('candidat')
            ->orderBy('numeroOrdre', 'asc')
            ->get();
            
        $waitingCount = $tickets->where('statut', 'en attente')->count();

        return view('formateur.dashboard', compact('session', 'tickets', 'waitingCount'));
    }

    public function updateTicketStatus(Request $request, Ticket $ticket)
    {
        if (!Auth::user()->can('manage_queue')) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
        }

        $request->validate([
            'statut' => 'required|in:en attente,en cours,terminée'
        ]);

        $ticket->update(['statut' => $request->statut]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour.']);
    }

    public function updateTicketOrder(Request $request, Session $session)
    {
        if (!Auth::user()->can('manage_queue')) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:tickets,id'
        ]);

        foreach ($request->order as $index => $id) {
            Ticket::where('id', $id)->update(['numeroOrdre' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Ordre mis à jour.']);
    }
}
