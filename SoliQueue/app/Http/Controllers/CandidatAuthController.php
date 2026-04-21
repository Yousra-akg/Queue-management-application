<?php

namespace App\Http\Controllers;

use App\Services\CandidatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CandidatAuthController extends Controller
{
    protected $candidatService;

    public function __construct(CandidatService $candidatService)
    {
        $this->candidatService = $candidatService;
    }

    /**
     * Affiche la page de connexion (Home).
     */
    public function index()
    {
        if (Session::has('candidat_id')) {
            return redirect()->route('candidat.bienvenue');
        }
        return view('auth.login');
    }

    /**
     * Gère la connexion par CIN.
     */
    public function login(Request $request)
    {
        $request->validate([
            'cin' => 'required|string',
        ]);

        try {
            $this->candidatService->loginByCin($request->cin);
            return redirect()->route('candidat.bienvenue');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Déconnexion.
     */
    public function logout()
    {
        Session::forget('candidat_id');
        return redirect()->route('login')->with('success', 'Vous avez été déconnecté.');
    }
}
