# Définition des Sprints – SoliQueue

Ce document décrit les deux sprints principaux de développement de l’application SoliQueue, en mettant en évidence les fonctionnalités disponibles pour chaque rôle : candidat, formateur et administrateur.

---

## Sprint 1 – MVP (Fonctionnalités Essentielles)

Ce premier sprint correspond au MVP de SoliQueue. Il met en place le moteur de gestion de file d'attente.
### Fonctionnalités par rôle

**Candidat (Accès Direct via QCM) :** - Accéder à l'interface via son identifiant de réussite QCM  
- Obtenir un ticket de passage unique (SOLI-XX)  
- Consulter sa position et le timer d'attente en temps réel  
- Valider sa présence physique via le code secret à 4 chiffres
- Suivi de l'état de la file active (vue globale)

**Formateur :** - Se connecter à l'espace Web  
- Sélectionner et rejoindre une session d'entretien  
- Appeler le candidat suivant  
- Mettre à jour le statut du ticket (Terminé / Absent)
- Déplacer ou prioriser certains candidats si nécessaire.

**Administrateur :** - Authentification sécurisée (Login/Password)
- Création et gestion des sessions d'entretien (CRUD)  
- Affectation des candidats aux sessions 
- Dashboard de statistiques globales (Taux de présence)

---

## Sprint 2 – Fonctionnalités Avancées

Ce deuxième sprint introduit les outils de monitoring et d'analyse pour une gestion plus fine et intelligente.

### Fonctionnalités par rôle

**Candidat :**  
- Notifications visuelles prioritaires sur l'interface mobile

**Formateur :** - Consulter le tableau de bord de la session en cours  
- Visualiser les statistiques de présence de sa session

**Administrateur :** 
- Suivi de l'activité en temps réel sur toutes les sessions  
