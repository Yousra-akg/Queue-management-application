<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CandidatService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $candidatService;

    /**
     * Vers où rediriger après connexion.
     */
    protected $redirectTo = '/bienvenue';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CandidatService $candidatService)
    {
        $this->candidatService = $candidatService;
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Surcharger le nom d'utilisateur utilisé pour l'authentification.
     */
    public function username()
    {
        return 'cin';
    }

    /**
     * Gère la requête de connexion.
     * On surcharge pour permettre la connexion via CIN sans mot de passe.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        try {
            $candidat = $this->candidatService->loginByCin($request->cin);
            Auth::login($candidat, $request->filled('remember'));
            // On redirige explicitement vers la page de bienvenue pour éviter d'être renvoyé 
            // vers des routes internes de données (comme /queue-status) par le système "intended" de Laravel.
            return redirect()->route('candidat.bienvenue');
        } catch (\Exception $e) {
            return $this->sendFailedLoginResponse($request);
        }
    }

    /**
     * Validation de la requête.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
        ]);
    }
}
