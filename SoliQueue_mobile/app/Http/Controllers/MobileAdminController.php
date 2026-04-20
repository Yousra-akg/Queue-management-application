<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MobileApiService;

class MobileAdminController extends Controller
{
    protected $apiService;

    public function __construct(MobileApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function dashboard()
    {
        try {
            $response = $this->apiService->getDashboardStats();
            $data = $response['data'];
            
            $stats = $data['stats'];
            $sessions = $data['sessions'];
            
            return view('mobile.admin-dashboard', compact('stats', 'sessions'));
        } catch (\Exception $e) {
            return response('Impossible de joindre le serveur API. Erreur: ' . $e->getMessage(), 500);
        }
    }
}
