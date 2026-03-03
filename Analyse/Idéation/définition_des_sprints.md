# Définition des Sprints et Cas d'Utilisation – Queue Management App
**Projet :** Application de Gestion des Files d’Attente – Entretiens SoliCode  
**Date :** 2 Mars 2026

---

Ce document présente la planification agile du projet **Queue Management App**, divisée en deux sprints majeurs : le **MVP** et les **fonctionnalités avancées**.

---

## Sprint 1 : MVP (Candidats & Formateurs)
**Objectif :** Déployer les fonctionnalités essentielles pour permettre aux candidats de s’inscrire et obtenir leur numéro de passage, et aux formateurs de gérer les sessions et suivre la file d’attente.

### Cas d'Utilisation (UC) du Sprint 1 :

#### Espace Candidat
1. **UC1 : Réception du numéro de passage**  
   Le candidat reçoit son numéro de passage au jour de l’entretien.
2. **UC2 : Voir l’ordre de passage approximatif**  
   Connaître sa position dans la file et le temps estimé avant son tour.
3. **UC3 : Annuler ou reporter son passage**  
   Permet de gérer les imprévus (ex. retard ou indisponibilité).

#### Espace Formateur
1. **UC4 : Mise à jour numérique de l’état des tickets**  
   Modifier le statut des tickets (En attente / En cours / Terminé).
2. **UC14 : Gestion améliorée de la file**  
   Déplacer ou prioriser certains candidats si nécessaire.
3. **UC6 : Interface simple pour visualiser la progression**  
   Vue rapide sur les candidats présents et l’état de chaque ticket.

#### Espace Administrateur

1. **UCA1 : Authentification Admin**
Se connecter pour accéder à l’interface de gestion.

2. **UCA2 : Création et gestion des sessions**
Définir date, heure, capacité des sessions.

3. **UCA3 : Suivi de la file d’attente globale**
Voir tous les candidats inscrits pour chaque session.

4. **UCA4 : Gestion manuelle des tickets**
Modifier ou réinitialiser les tickets si nécessaire.

---

## Sprint 2 : Fonctionnalités Avancées (Candidats & Formateurs)
**Objectif :** Améliorer l’expérience et le suivi avec des outils numériques et notifications pour fluidifier la gestion.

### Cas d'Utilisation (UC) du Sprint 2 :

#### Espace Candidat
1. **UC9 : Notifications de passage**  
   Être alerté quelques minutes avant son tour et lors de son tour.
2. **UC10 : Historique et suivi du ticket**  
   Voir si le ticket a été annulé ou reporté.

#### Espace Formateur
1. **UC12 : Notifications pour candidats**  
   Envoyer automatiquement notifications pour reports ou annulations.
2. **UC13 : Statistiques des sessions**  
   Analyser le temps moyen d’attente et le nombre de passages.



#### Espace Administrateur

1. **UC A5 : Notifications administratives**
Recevoir alertes sur absences ou problèmes.

2. **UC A6 : Statistiques globales**
Analyse du nombre de candidats, retards, annulations et temps moyen.

3. **UC A7 : Réorganisation automatique ou priorisation**
Permet de déplacer un candidat ou ajuster la file en cas de besoins spécifiques.

---

### Notes importantes :
 
- Sprint 1 garantit le fonctionnement minimal pour que l’application soit opérationnelle le jour de l’entretien.  
- Sprint 2 enrichit l’expérience avec des notifications, suivi amélioré et statistiques pour optimiser l’organisation des sessions.