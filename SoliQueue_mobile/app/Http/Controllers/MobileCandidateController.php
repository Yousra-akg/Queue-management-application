<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MobileApiService;

class MobileCandidateController extends Controller
{
    protected $apiService;

    public function __construct(MobileApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function showGenerationTicket()
    {
        try {
            $response = $this->apiService->getRandomStudent();
            $etudiant = $response['data'];
            return view('mobile.generation-ticket', compact('etudiant'));
        } catch (\Exception $e) {
            return response("Erreur API : " . $e->getMessage(), 500);
        }
    }

    public function generateTicket(Request $request)
    {
        try {
            // On accepte l'ID depuis le formulaire OU l'URL pour plus de fiabilité
            $studentId = (int) ($request->input('etudiant_id') ?? $request->query('etudiant_id'));
            
            if (!$studentId) {
                throw new \Exception("ID étudiant invalide ou manquant dans la requête.");
            }
            $response = $this->apiService->generateTicket($studentId);
            
            session(['current_ticket' => $response['data']]);
            session(['current_student_name' => $request->input('etudiant_name')]);
            
            return redirect()->route('mobile.portal');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showPortal()
    {
        $ticket = session('current_ticket');
        $studentName = session('current_student_name', 'Candidat');
        if (!$ticket) {
            return redirect()->route('mobile.home');
        }

        try {
            $sessionStatus = $this->apiService->getSessionStatus($ticket['session_id']);
            $sessionInfo = $sessionStatus['data'];
            
            return view('mobile.portail-interactif', compact('ticket', 'sessionInfo', 'studentName'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function validatePresence(Request $request)
    {
        try {
            $ticket = session('current_ticket');
            $code = $request->input('code_presence');
            
            $this->apiService->validatePresence($ticket['id'], $code);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    public function getQueueData()
    {
        try {
            $ticket = session('current_ticket');
            $response = $this->apiService->getLiveQueue($ticket['session_id']);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
