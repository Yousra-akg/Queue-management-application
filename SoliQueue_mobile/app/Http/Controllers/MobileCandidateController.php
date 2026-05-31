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

    public function showLogin(Request $request)
    {
        $error = $request->query('error');
        \Illuminate\Support\Facades\Log::info("LOADED EXTENSIONS: " . implode(', ', get_loaded_extensions()));
        return view('mobile.login', compact('error'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'cin' => 'required|string'
        ]);

        try {
            $response = $this->apiService->login($request->input('cin'));
            $etudiant = $response['data'];
            return redirect('/ticket-ready?candidate_id=' . $etudiant['id']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Login exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return redirect('/?error=' . urlencode($e->getMessage()));
        }
    }

    public function showGenerationTicket(Request $request)
    {
        $studentId = $request->query('candidate_id');
        if (!$studentId) {
            return redirect('/');
        }

        try {
            $response = $this->apiService->getCandidateById($studentId);
            $etudiant = $response['data'];
            $error = $request->query('error');
            return view('mobile.generation-ticket', compact('etudiant', 'error'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("showGenerationTicket exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n" . $e->getTraceAsString());
            return redirect('/?error=' . urlencode('Candidat introuvable.'));
        }
    }

    public function generateTicket(Request $request)
    {
        try {
            $studentId = (int) ($request->input('etudiant_id') ?? $request->query('etudiant_id'));
            
            if (!$studentId) {
                throw new \Exception("ID étudiant invalide ou manquant.");
            }
            $response = $this->apiService->generateTicket($studentId);
            $ticket = $response['data'];
            
            $studentName = $request->input('etudiant_name') ?? 'Candidat';
            return redirect('/portal?ticket_id=' . $ticket['id'] . '&student_name=' . urlencode($studentName));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("generateTicket exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $studentId = (int) ($request->input('etudiant_id') ?? $request->query('etudiant_id'));
            return redirect('/ticket-ready?candidate_id=' . $studentId . '&error=' . urlencode($e->getMessage()));
        }
    }

    public function showPortal(Request $request)
    {
        $ticketId = $request->query('ticket_id');
        $studentName = $request->query('student_name', 'Candidat');
        if (!$ticketId) {
            return redirect('/');
        }

        try {
            $response = $this->apiService->getTicketById($ticketId);
            $ticket = $response['data'];

            $sessionStatus = $this->apiService->getSessionStatus($ticket['session_id']);
            $sessionInfo = $sessionStatus['data'];
            
            return view('mobile.portail-interactif', compact('ticket', 'sessionInfo', 'studentName'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("showPortal exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n" . $e->getTraceAsString());
            return redirect('/?error=' . urlencode('Session ou ticket introuvable.'));
        }
    }

    public function validatePresence(Request $request)
    {
        try {
            $ticketId = (int) $request->input('ticket_id');
            $code = $request->input('code_presence');
            
            if (!$ticketId) {
                throw new \Exception("Identifiant du ticket manquant.");
            }

            $this->apiService->validatePresence($ticketId, $code);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    public function getQueueData(Request $request)
    {
        try {
            $sessionId = (int) $request->query('session_id');
            if (!$sessionId) {
                throw new \Exception("Session manquante.");
            }
            $response = $this->apiService->getLiveQueue($sessionId);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
