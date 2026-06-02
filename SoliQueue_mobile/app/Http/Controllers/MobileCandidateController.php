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
        $error = session('error') ?? $request->query('error');
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
            return redirect('/ticket-ready')->with('candidate_id', $etudiant['id']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Login exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return redirect('/')->with('error', $e->getMessage());
        }
    }

    public function showGenerationTicket(Request $request)
    {
        $studentId = session('candidate_id') ?? $request->query('candidate_id');
        if (!$studentId) {
            return redirect('/');
        }

        try {
            $response = $this->apiService->getCandidateById((int)$studentId);
            $etudiant = $response['data'];
            $error = session('error') ?? $request->query('error');
            return view('mobile.generation-ticket', compact('etudiant', 'error'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("showGenerationTicket exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n" . $e->getTraceAsString());
            return redirect('/')->with('error', 'Candidat introuvable.');
        }
    }

    public function generateTicket(Request $request)
    {
        try {
            $studentId = (int) ($request->input('etudiant_id') ?? session('candidate_id'));
            
            if (!$studentId) {
                throw new \Exception("ID étudiant invalide ou manquant.");
            }
            $response = $this->apiService->generateTicket($studentId);
            $ticket = $response['data'];
            
            $studentName = $request->input('etudiant_name') ?? 'Candidat';
            return redirect('/portal')
                ->with('ticket_id', $ticket['id'])
                ->with('student_name', $studentName);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("generateTicket exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $studentId = (int) ($request->input('etudiant_id') ?? session('candidate_id'));
            return redirect('/ticket-ready')
                ->with('candidate_id', $studentId)
                ->with('error', $e->getMessage());
        }
    }

    public function showPortal(Request $request)
    {
        $ticketId = session('ticket_id') ?? $request->query('ticket_id');
        $studentName = session('student_name') ?? $request->query('student_name', 'Candidat');
        if (!$ticketId) {
            return redirect('/');
        }

        try {
            $response = $this->apiService->getTicketById((int)$ticketId);
            $ticket = $response['data'];

            // Sauvegarder dans la session de façon persistante
            session([
                'ticket_id' => (int)$ticketId,
                'student_name' => $studentName,
                'candidate_id' => (int)($ticket['candidat_id'] ?? 0)
            ]);

            $sessionStatus = $this->apiService->getSessionStatus($ticket['session_id']);
            $sessionInfo = $sessionStatus['data'];
            
            return view('mobile.portail-interactif', compact('ticket', 'sessionInfo', 'studentName'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("showPortal exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n" . $e->getTraceAsString());
            return redirect('/')->with('error', 'Session ou ticket introuvable.');
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
            $candidateId = (int) ($request->query('candidate_id') ?? session('candidate_id'));
            $response = $this->apiService->getLiveQueue($sessionId, $candidateId);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function markNotificationRead(int $id)
    {
        try {
            $response = $this->apiService->markNotificationRead($id);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}

