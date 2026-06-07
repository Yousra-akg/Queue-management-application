<?php

namespace App\Http\Controllers;

use App\Services\AI\GeminiService;
use App\Services\AI\ContextService;
use App\Services\AI\ChatbotCommandHandler;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        private GeminiService $gemini,
        private ContextService $context,
        private ChatbotCommandHandler $handler
    ) {}

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'ticket_number' => 'nullable|string',
            'file' => 'nullable|file',
            'history' => 'nullable|string'
        ]);

        $message = $request->input('message') ?? 'Voici un fichier joint. Peux-tu le traiter ?';
        $ticketNumber = $request->input('ticket_number');
        $file = $request->file('file');
        $historyJson = $request->input('history');
        $history = [];
        if ($historyJson) {
            $history = json_decode($historyJson, true) ?? [];
        }

        // Récupérer le contexte métier (état DB, rôle)
        $context = $this->context->getContext($ticketNumber);

        // Envoyer à Gemini (Function Calling)
        $aiResponse = $this->gemini->generate($context, $message, $history);

        // Traiter l'action retournée par Gemini (avec le fichier optionnel)
        $result = $this->handler->handle($aiResponse, $file);

        return response()->json($result);
    }
}

