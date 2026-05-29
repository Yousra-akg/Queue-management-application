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

    public function showLogin()
    {
        return view('mobile.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cin' => 'required|string'
        ]);

        try {
            $response = $this->apiService->login($request->input('cin'));
            $etudiant = $response['data'];
            return redirect()->route('mobile.ticket_ready', ['candidate_id' => $etudiant['id']]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showGenerationTicket(Request $request)
    {
        $studentId = $request->query('candidate_id');
        if (!$studentId) {
            return redirect()->route('mobile.home');
        }

        try {
            $response = $this->apiService->getCandidateById($studentId);
            $etudiant = $response['data'];
            return view('mobile.generation-ticket', compact('etudiant'));
        } catch (\Exception $e) {
            return redirect()->route('mobile.home')->with('error', 'Candidat introuvable.');
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
            
            return redirect()->route('mobile.portal', [
                'ticket_id' => $ticket['id'],
                'student_name' => $request->input('etudiant_name') ?? 'Candidat'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showPortal(Request $request)
    {
        $ticketId = $request->query('ticket_id');
        $studentName = $request->query('student_name', 'Candidat');
        if (!$ticketId) {
            return redirect()->route('mobile.home');
        }

        try {
            $response = $this->apiService->getTicketById($ticketId);
            $ticket = $response['data'];

            $sessionStatus = $this->apiService->getSessionStatus($ticket['session_id']);
            $sessionInfo = $sessionStatus['data'];
            
            return view('mobile.portail-interactif', compact('ticket', 'sessionInfo', 'studentName'));
        } catch (\Exception $e) {
            return redirect()->route('mobile.home')->with('error', 'Session ou ticket introuvable.');
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
