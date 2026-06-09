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

Base de Connaissance Solicode (pour répondre de manière précise et complète) :
1. Sur quels points vais-je être évalué ?
Le jury évalue principalement ton potentiel et ton savoir-être. Les critères clés sont :
- La motivation et l'intérêt pour le digital (curiosité, avoir testé des petits outils).
- L'autonomie et la capacité d'auto-apprentissage (recherche personnelle).
- Le travail d'équipe et la communication (collaborer, écouter, s'exprimer clairement).
- La logique et la persévérance (patience face à un bug).

2. Que se passe-t-il après l'entretien ?
- Délibération et résultats : Tu recevras une réponse (email, téléphone, affichage) annonçant ton admission.
- L'inscription administrative : Dépôt du dossier pour valider ta place.
- Le QCM de base : QCM noté sur 40 points sur la logique pour évaluer ton niveau de départ.

3. C'est quoi exactement le concept de Solicode ?
C'est un centre de formation solidaire et gratuit (avec la Fondation Mohammed V pour la Solidarité et l'OFPPT). Il vise l'insertion professionnelle rapide, l'égalité des chances, l'apprentissage par la pratique et la préparation aux besoins des entreprises locales.

4. Comment se déroulent les études ?
- Pas de cours magistraux.
- Pédagogie active (Learning by doing) : Résolution de problèmes/projets en équipe. Les formateurs sont des mentors.
- Rythme soutenu : Horaires d'entreprise, présence et ponctualité importantes.

5. Est-ce qu'on fait de la théorie ou de la pratique ?
- Pratique à 80-90% : Projets réels.
- La théorie nécessaire : Injectée au moment où tu en as besoin pour réaliser ton projet.

6. Quel diplôme obtient-on à la fin ?
Certifications cumulables avec l'OFPPT :
- Après la 1ère année : Certificat en Développement Web.
- Après la 2ème année : Certificat en Développement Mobile.
La vraie valeur réside dans le portfolio (projets) et les compétences immédiates.

7. Combien de temps dure l'entretien en moyenne ?
L'entretien dure en moyenne entre 20 et 30 minutes.
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
        $model = config('services.gemini.model', 'gemini-flash-latest');

        if (!$apiKey) {
            return [
                'action' => 'respond_user',
                'data' => [],
                'message' => 'Erreur : La clé API Gemini n\'est pas configurée.'
            ];
        }

        $maxRetries = 3;
        $attempt = 0;
        $response = null;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(30)
                    ->acceptJson()
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        $body
                    );

                if ($response->successful()) {
                    break;
                }

                if ($response->status() !== 503 && $response->status() !== 429) {
                    break; // Only retry on 503 Unavailable or 429 Too Many Requests
                }
            } catch (\Exception $e) {
                $lastException = $e;
            }

            $attempt++;
            if ($attempt < $maxRetries) {
                sleep(2); // Wait 2 seconds before retrying
            }
        }

        try {
            if (!$response || $response->failed()) {
                Log::error('Erreur API Gemini', [
                    'status' => $response ? $response->status() : 'Network Error',
                    'response' => $response ? $response->body() : ($lastException ? $lastException->getMessage() : 'Unknown error'),
                    'attempts' => $attempt
                ]);

                return [
                    'action' => 'respond_user',
                    'data' => [],
                    'message' => 'Impossible de contacter le service IA pour le moment. Veuillez réessayer.'
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

