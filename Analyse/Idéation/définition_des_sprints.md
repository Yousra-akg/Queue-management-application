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
1. **UC1 : Accès à l’application après QCM réussi**  
   Seuls les candidats ayant réussi le QCM reçoivent le lien vers l’application.
2. **UC2 : Réception du numéro de passage**  
   Le candidat reçoit son numéro de passage au jour de l’entretien.
3. **UC3 : Voir l’ordre de passage approximatif**  
   Connaître sa position dans la file et le temps estimé avant son tour.
4. **UC4 : Annuler ou reporter son passage**  
   Permet de gérer les imprévus (ex. retard ou indisponibilité).

#### Espace Formateur
1. **UC6 : Suivi manuel de la file d’attente**  
   Savoir qui est passé et qui attend.
2. **UC7 : Gestion des changements**  
   Ajuster manuellement l’ordre de passage si nécessaire.
4. **UC8 : Interface simple pour visualiser la progression**  
   Vue rapide sur les candidats présents et l’état de chaque ticket.

---

## Sprint 2 : Fonctionnalités Avancées (Candidats & Formateurs)
**Objectif :** Améliorer l’expérience et le suivi avec des outils numériques et notifications pour fluidifier la gestion.

### Cas d'Utilisation (UC) du Sprint 2 :

#### Espace Candidat
1. **UC9 : Notifications de passage**  
   Être alerté quelques minutes avant son tour.
2. **UC10 : Historique et suivi du ticket**  
   Voir si le ticket a été annulé ou reporté.

#### Espace Formateur
1. **UC11 : Mise à jour numérique de l’état des tickets**  
   Modifier le statut des tickets (En attente / En cours / Terminé).
2. **UC12 : Notifications pour candidats**  
   Envoyer automatiquement notifications pour reports ou annulations.
3. **UC13 : Statistiques des sessions**  
   Analyser le temps moyen d’attente et le nombre de passages.
4. **UC14 : Gestion améliorée de la file**  
   Déplacer ou prioriser certains candidats si nécessaire.
5. **UC15 : Interface plus ergonomique et dynamique**  
   Mise à jour automatique de la file et meilleure visibilité globale.

---

### Notes importantes :
 
- Sprint 1 garantit le fonctionnement minimal pour que l’application soit opérationnelle le jour de l’entretien.  
- Sprint 2 enrichit l’expérience avec des notifications, suivi amélioré et statistiques pour optimiser l’organisation des sessions.