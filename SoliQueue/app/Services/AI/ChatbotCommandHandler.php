<?php

namespace App\Services\AI;

use App\Services\QueueService;
use App\Models\Entretien;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CandidatsImport; // We will create this later if Excel is fully needed, but for now we'll stub it.

class ChatbotCommandHandler
{
    public function __construct(
        private QueueService $queueService
    ) {}

    public function handle(array $aiResponse, $uploadedFile = null, $entretienId = null): array
    {
        $action = $aiResponse['action'] ?? 'respond_user';
        $data = $aiResponse['data'] ?? [];
        $message = $aiResponse['message'] ?? 'Je ne suis pas sûr de comprendre.';
        $user = Auth::guard('web')->user() ?? Auth::guard('candidat')->user();

        // Helper function to get the current Entretien
        $getCurrentEntretien = function() use ($entretienId, $user) {
            if ($entretienId) {
                return Entretien::find($entretienId);
            }
            if ($user && $user->hasRole('formateur')) {
                return Entretien::where('statut', 'en cours')->where('user_id', $user->id)->first();
            }
            return Entretien::where('statut', 'en cours')->first();
        };

        // 1. Action: next_candidate (Formateur uniquement)
        if ($action === 'next_candidate') {
            if (!$user || !$user->hasRole('formateur')) {
                return ['message' => "Vous n'avez pas l'autorisation d'appeler le prochain candidat."];
            }
            
            $entretien = $getCurrentEntretien();
            if (!$entretien) {
                return ['message' => "Vous n'avez aucune entretien en cours ou spécifiée."];
            }

            try {
                $this->queueService->callNextCandidat($entretien->id, $user->id);
                return ['message' => "C'est fait ! Le candidat suivant a été appelé.", 'action' => 'next_candidate'];
            } catch (\Exception $e) {
                return ['message' => "Impossible de passer au candidat suivant : " . $e->getMessage()];
            }
        }

        // 2. Action: mark_absent (Formateur uniquement)
        if ($action === 'mark_absent') {
            if (!$user || !$user->hasRole('formateur')) {
                return ['message' => "Vous n'avez pas l'autorisation de marquer un candidat absent."];
            }
            
            $entretien = $getCurrentEntretien();
            if (!$entretien) {
                return ['message' => "Vous n'avez aucune entretien en cours ou spécifiée."];
            }
            
            $ticketEnCours = $entretien->tickets()->where('statut', 'en cours')->first();
            if (!$ticketEnCours) {
                return ['message' => "Aucun candidat n'est actuellement en cours d'entretien."];
            }

            try {
                $this->queueService->updateCandidatStatus($ticketEnCours->id, 'absent');
                return ['message' => "Le candidat (Ticket {$ticketEnCours->codeUnique}) a bien été marqué comme absent.", 'action' => 'mark_absent'];
            } catch (\Exception $e) {
                return ['message' => "Impossible de marquer le candidat absent : " . $e->getMessage()];
            }
        }

        // 3. Action: close_entretien (Formateur uniquement)
        if ($action === 'close_entretien') {
            if (!$user || !$user->hasRole('formateur')) {
                return ['message' => "Vous n'avez pas l'autorisation de terminer une entretien."];
            }
            
            $entretien = $getCurrentEntretien();
            if (!$entretien) {
                return ['message' => "Vous n'avez aucune entretien en cours ou spécifiée."];
            }
            
            try {
                $entretien->update(['statut' => 'terminée']);
                return ['message' => "La entretien a bien été clôturée.", 'action' => 'close_entretien'];
            } catch (\Exception $e) {
                return ['message' => "Impossible de terminer la entretien : " . $e->getMessage()];
            }
        }

        // 4. Action: import_excel (Admin uniquement)
        if ($action === 'import_excel') {
            if (!$user || !$user->hasRole('admin')) {
                return ['message' => "Seul un administrateur peut importer des candidats."];
            }

            if (!$uploadedFile) {
                return ['message' => "Veuillez me fournir un fichier Excel joint avec votre message."];
            }

            try {
                Excel::import(new CandidatsImport, $uploadedFile);
                return ['message' => "Le fichier Excel a été traité avec succès ! Les candidats ont été ajoutés à la base de données."];
            } catch (\Exception $e) {
                return ['message' => "Erreur lors de l'importation : " . $e->getMessage()];
            }
        }

        // 5. Action: get_stats (Admin uniquement)
        if ($action === 'get_stats') {
            return ['message' => $message];
        }

        // 6. Action: create_entretien (Admin uniquement)
        if ($action === 'create_entretien') {
            if (!$user || !$user->hasRole('admin')) {
                return ['message' => "Seul un administrateur peut créer des entretiens."];
            }

            try {
                $uniqueCode = strtoupper(\Illuminate\Support\Str::random(4));
                $date = $data['date'] ?? \Carbon\Carbon::tomorrow()->format('Y-m-d');
                Entretien::create([
                    'user_id' => $user->id,
                    'dateEntretien' => $date,
                    'heureDebut' => $data['heure_debut'] ?? '09:00',
                    'heureFin' => $data['heure_fin'] ?? '17:00',
                    'capaciteMax' => $data['capacite'] ?? 20,
                    'codePresence' => $uniqueCode, // Required field, 4 characters
                    'statut' => 'planifiée'
                ]);
                return ['message' => "La entretien a été créée avec succès pour la date du **{$date}** (Code : **$uniqueCode**) !"];
            } catch (\Exception $e) {
                return ['message' => "Impossible de créer la entretien : " . $e->getMessage()];
            }
        }

        // 7. Action: assign_candidates (Admin uniquement)
        if ($action === 'assign_candidates') {
            if (!$user || !$user->hasRole('admin')) {
                return ['message' => "Seul un administrateur peut assigner des candidats en masse."];
            }

            try {
                $date = $data['date'] ?? null;
                
                $query = Entretien::whereIn('statut', ['planifiée', 'en cours']);
                if ($date) {
                    $query->whereDate('dateEntretien', $date);
                }
                $entretien = $query->orderBy('dateEntretien', 'asc')->first();

                if (!$entretien) {
                    return ['message' => "Je n'ai pas trouvé de entretien disponible " . ($date ? "pour le $date" : "prochainement") . ". Voulez-vous que j'en crée une ?"];
                }

                $unassigned = \App\Models\Candidat::whereNull('entretien_id')->get();
                if ($unassigned->isEmpty()) {
                    return ['message' => "Tous les candidats sont déjà assignés à une entretien."];
                }

                $count = 0;
                foreach ($unassigned as $c) {
                    $c->update(['entretien_id' => $entretien->id]);
                    $count++;
                }

                return ['message' => "C'est fait ! $count candidat(s) ont été assignés à la entretien prévue le **{$entretien->dateEntretien}**."];
            } catch (\Exception $e) {
                return ['message' => "Erreur lors de l'assignation : " . $e->getMessage()];
            }
        }

        // 8. Action par défaut: respond_user
        return ['message' => $message];
    }
}

