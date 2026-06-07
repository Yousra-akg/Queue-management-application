<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function generate(array $context, string $message, array $history = []): array
    {
        $systemPrompt = <<<PROMPT
Vous êtes l'assistant IA officiel de "SoliQueue", l'application de gestion de file d'attente de Solicode.
Votre rôle est d'analyser la requête de l'utilisateur (Candidat, Formateur, ou Administrateur) et de décider quelle action exécuter :

1. respond_user : Pour répondre à TOUTE question de l'utilisateur. Si l'utilisateur demande des statistiques (taux de présence, nombre de entretiens, etc.), tu DOIS lire les données fournies dans le "Contexte métier actuel" et lui donner la réponse précise directement dans ton message.
2. next_candidate : Pour passer au candidat suivant dans la file d'attente (réservé au Formateur).
3. mark_absent : Pour marquer un candidat comme absent (réservé au Formateur).
4. close_entretien : Pour terminer la entretien en cours du formateur (réservé au Formateur).
5. import_excel : Pour importer un fichier Excel de candidats et les assigner à une entretien (réservé à l'Admin).
6. assign_candidates : Pour assigner tous les candidats sans entretien à une entretien spécifique, ou à la prochaine entretien disponible (réservé à l'Admin).

Tu dois TOUJOURS retourner UNIQUEMENT un objet JSON valide. Ne retourne JAMAIS de markdown (pas de ```json), ni de texte brut en dehors du JSON.

Format de réponse attendu :
{
  "action": "respond_user | next_candidate | mark_absent | close_entretien | import_excel | create_entretien | assign_candidates",
  "data": {},
  "message": "Votre réponse conversationnelle formatée en HTML basique (<b>, <i>, <br>) si nécessaire"
}

Règles :
- Pour créer une entretien, utilise l'action "create_entretien" et mets la date, l'heure_debut, l'heure_fin et la capacite dans "data".
- Pour assigner des candidats, utilise "assign_candidates" (tu peux fournir "date" dans data si le user mentionne une date comme 'demain').
- Pour TOUTE question informative ou de statistiques, utilise l'action "respond_user" et rédige ta réponse complète en lisant les données du contexte. Ne fais pas semblant de "récupérer" les stats, tu les as DÉJÀ dans ton contexte, donne le chiffre exact !
- Si l'utilisateur demande à passer au candidat suivant, utilise l'action "next_candidate" et data = {}. Le backend s'occupera du reste.
- Si le formateur demande à marquer le candidat actuel comme absent, utilise "mark_absent".
- Si le formateur demande de clôturer ou terminer la entretien, utilise "close_entretien".
- Si le message n'est pas clair, utilise "respond_user" pour demander des précisions.
PROMPT;

        $prompt = $systemPrompt . "\n\nContexte métier actuel : " . json_encode($context);
        
        if (!empty($history)) {
            $prompt .= "\n\nHistorique de la conversation :\n";
            foreach ($history as $msg) {
                $role = ($msg['role'] ?? '') === 'user' ? "Utilisateur" : "IA";
                $content = $msg['content'] ?? '';
                $prompt .= $role . ": " . $content . "\n";
            }
        }
        
        $prompt .= "\n\nMessage de l'utilisateur : " . $message;

        $body = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => $prompt
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "responseMimeType" => "application/json",
                "temperature" => 0.2,
            ]
        ];

        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-2.5-flash');

        if (!$apiKey) {
            return [
                'action' => 'respond_user',
                'data' => [],
                'message' => 'Erreur : La clé API Gemini n\'est pas configurée.'
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->acceptJson()
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    $body
                );

            if ($response->failed()) {
                Log::error('Erreur API Gemini', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return [
                    'action' => 'respond_user',
                    'data' => [],
                    'message' => 'Impossible de contacter le service IA pour le moment.'
                ];
            }

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Parfois Gemini renvoie le JSON dans des blocs de code markdown
            $text = preg_replace('/```json/i', '', $text);
            $text = preg_replace('/```/', '', $text);
            $text = trim($text);

            Log::info('Réponse Gemini brute', ['response' => $text]);

            $decoded = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Gemini invalide', [
                    'error' => json_last_error_msg(),
                    'response' => $text
                ]);

                return [
                    'action' => 'respond_user',
                    'data' => [],
                    'message' => 'L\'IA a renvoyé une réponse invalide.'
                ];
            }

            return $decoded;

        } catch (\Throwable $e) {
            Log::error('Exception Service Gemini', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return [
                'action' => 'respond_user',
                'data' => [],
                'message' => 'Une erreur inattendue s\'est produite.'
            ];
        }
    }
}

