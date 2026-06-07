<?php

namespace App\Services\AI;

use App\Models\Candidat;
use App\Models\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContextService
{
    public function getContext(?string $ticketNumber = null): array
    {
        $user = Auth::guard('web')->user() ?? Auth::guard('candidat')->user();
        
        $context = [
            'date_actuelle' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        // 1. Contexte si Formateur
        if ($user && $user->hasRole('formateur')) {
            $context['role'] = 'formateur';
            $context['formateur_nom'] = $user->nom;
            
            // Trouver la session en cours
            $session = Session::where('statut', 'en cours')
                ->with(['tickets' => function($q) {
                    $q->whereIn('statut', ['en attente', 'en cours'])->with('candidat');
                }])->first();

            if ($session) {
                $prochain_ticket = $session->tickets->where('statut', 'en attente')->sortBy('numeroOrdre')->first();
                $prochain_candidat = $prochain_ticket ? "Ticket {$prochain_ticket->codeUnique} ({$prochain_ticket->candidat->nom} {$prochain_ticket->candidat->prenom})" : "Aucun";
                
                $context['session_actuelle'] = [
                    'id' => $session->id,
                    'nombre_candidats_attente' => $session->tickets->where('statut', 'en attente')->count(),
                    'ticket_en_cours' => $session->tickets->where('statut', 'en cours')->first()?->codeUnique,
                    'prochain_candidat' => $prochain_candidat
                ];
            } else {
                $context['session_actuelle'] = 'Aucune session en cours.';
            }
        }
        
        // 2. Contexte si Administrateur
        elseif ($user && $user->hasRole('admin')) {
            $context['role'] = 'admin';
            $context['admin_nom'] = $user->nom;
            
            $ticketsToday = \App\Models\Ticket::whereDate('created_at', Carbon::today())->get();
            $totalTickets = $ticketsToday->count();
            $absents = $ticketsToday->where('statut', 'absent')->count();
            $presents = $totalTickets - $absents;
            $tauxPresence = $totalTickets > 0 ? round(($presents / $totalTickets) * 100, 1) . '%' : 'N/A';

            $formateursStats = \App\Models\User::role('formateur')->get()->map(function($f) {
                $count = \App\Models\Ticket::whereHas('session', function($q) use ($f) {
                    $q->where('user_id', $f->id);
                })->where('statut', 'terminée')->count();
                return ['nom' => $f->nom, 'entretiens_termines' => $count];
            })->toArray();

            $stats = [
                'total_candidats_aujourdhui' => Candidat::whereDate('created_at', Carbon::today())->count(),
                'sessions_actives' => Session::where('statut', 'en cours')->count(),
                'taux_presence_aujourdhui' => $tauxPresence . " ($presents présents, $absents absents)",
                'sessions_prevues_demain' => Session::whereDate('dateEntretien', Carbon::tomorrow())->count(),
                'statistiques_formateurs' => $formateursStats
            ];
            $context['statistiques_globales'] = $stats;
        }

        // 3. Contexte Candidat (avec ou sans connexion)
        else {
            $context['role'] = 'candidat';
            
            // FAQ de base pour limiter les hallucinations de l'IA
            // FAQ de base fournie par l'utilisateur pour l'IA
            $context['infos_solicode'] = [
                'concept' => 'Solicode est un centre de formation solidaire et gratuit (en partenariat avec la Fondation Mohammed V pour la Solidarité et l\'OFPPT). Son concept repose sur l\'insertion professionnelle rapide, l\'égalité des chances, l\'apprentissage par la pratique, et la préparation directe aux besoins du marché du travail.',
                'deroulement_etudes' => 'Pas de cours magistraux. Pédagogie active (Learning by doing) : projets et problèmes à résoudre. Les formateurs sont des mentors. Rythme soutenu comme en entreprise.',
                'theorie_ou_pratique' => 'Pratique à 80-90% avec des projets réels. La théorie est injectée uniquement au moment où on en a besoin pour réaliser le projet.',
                'diplome' => 'Certifications avec l\'OFPPT : Certificat en Développement Web (1ère année), Certificat en Développement Mobile (2ème année). La vraie valeur reste le portfolio et les compétences acquises.',
                'duree_entretien' => 'L\'entretien dure en moyenne 15 à 20 minutes.',
                'critères_évaluation' => 'Évaluation du potentiel et savoir-être : Motivation/intérêt pour le digital, autonomie/auto-apprentissage, travail d\'équipe/communication, logique/persévérance.',
                'après_entretien' => 'Délibération du jury (réponse par email/téléphone/affichage), puis inscription administrative (dépôt du dossier physique), et passage d\'un QCM de base noté sur 40 points.',
                'documents' => 'Aucun document n\'est requis pour l\'entretien.'
            ];

            $ticketsEnCours = \App\Models\Ticket::where('statut', 'en cours')->pluck('codeUnique')->implode(', ');
            $context['informations_file_attente'] = [
                'ticket_actuellement_en_cours' => $ticketsEnCours ?: 'Aucun entretien en cours',
                'personnes_passees_aujourdhui' => \App\Models\Ticket::whereDate('created_at', Carbon::today())->whereIn('statut', ['terminée', 'en_cours'])->count()
            ];

            if ($ticketNumber) {
                // S'il a fourni son ticket dans le chat, on l'ajoute au contexte
                $candidat = Candidat::whereHas('ticket', function($q) use ($ticketNumber) {
                    $q->where('codeUnique', $ticketNumber);
                })->with('ticket')->first();

                if ($candidat) {
                    $context['votre_statut'] = [
                        'numero_ticket' => $candidat->ticket->codeUnique,
                        'statut' => $candidat->ticket->statut,
                        'heure_arrivee' => $candidat->ticket->heure_arrivee,
                    ];
                }
            }
        }

        return $context;
    }
}
