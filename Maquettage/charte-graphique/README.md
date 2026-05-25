# Charte Graphique : Application de Gestion des Files d’Attente (SoliCode)

**Projet :** Système de gestion des entretiens en temps réel  
**Date :** 3 Mars 2026  
**Objectif :** Clarté, Réduction du stress, Efficacité administrative.

---

## 1. Identité Visuelle & Concept
L'interface doit être **épurée (Clean Design)** pour minimiser la charge cognitive des candidats stressés et maximiser la productivité des administrateurs.

---

## 2. Palette de Couleurs (Design System)

| Usage | Nom | Hexadécimal | Rendu |
| :--- | :--- | :--- | :--- |
| **Primaire** | Bleu SoliCode | `#1A73E8` | 🟦 Confiance & Tech |
| **Succès** | Vert Action | `#34A853` | 🟩 "C'est votre tour" |
| **Alerte** | Orange Rappel | `#FBBC05` | 🟨 Attention modérée |
| **Danger** | Rouge Annulation | `#EA4335` | 🟥 Retards / Urgence |
| **Fond** | Gris Surface | `#F8F9FA` | ⬜ Confort visuel |
| **Texte** | Noir Ardoise | `#202124` | ⬛ Lisibilité max |

---

## 3. Typographie
Utilisation de polices **Sans-Serif** pour une lecture instantanée sur mobile.

- **Police principale :** `Inter` ou `Roboto`.
- **Titres (H1, H2) :** `Bold` (Gras), Espacement des lettres serré.
- **Corps de texte :** `Regular`, Taille 16px minimum.
- **Numéros de Ticket :** `Extra-Bold`, Taille 48px+ (Doit sauter aux yeux).

---

## 4. Composants d'Interface (UI Elements)

### 📱 Côté Candidat (Mobile)
- **Le Ticket :** Carte blanche avec bordures arrondies (`border-radius: 12px`) et ombre légère.
- **Indicateur de temps :** Compte à rebours dynamique en haut de l'écran.
- **Boutons :** - `Primaire` : Reporter le tour.
  - `Ghost (Bordure seule)` : Annuler mon ticket.

### 💻 Côté Administrateur (Desktop)
- **Tableau de bord :** Grille de colonnes "En attente", "En cours", "Terminé".
- **Badges de statut :** - `En attente` : Badge Gris.
  - `Appelé` : Badge Vert clignotant.
  - `Absent` : Badge Rouge.

---

## 5. Iconographie (Lucide Icons)

Pour garantir une interface moderne et une reconnaissance visuelle instantanée, l'application utilise exclusivement la bibliothèque **Lucide**. Les icônes sont traitées en style *Outlined* avec une épaisseur de trait constante (2px).

| Icône Lucide | Nom du composant | Usage fonctionnel |
| :--- | :--- | :--- |
| 🕒 | `clock` | **Temps d'attente :** Affichage du temps estimé avant le passage. |
| 👥 | `users` | **Position :** Rang actuel du candidat dans la file active. |
| 🔔 | `bell-ring` | **Notifications :** Alertes visuelles lors de l'appel au guichet. |
| 📅 | `calendar-days` | **Session :** Identification des créneaux d'entretiens planifiés. |
| 🚫 | `circle-slash` | **Annulation :** Action de retirer un ticket ou fermer une session. |
| ✅ | `check-circle` | **Statut :** Confirmation d'un entretien terminé ou validé. |

> **Note technique :** Les icônes doivent adopter la couleur de leur contexte (ex: Bleu Primaire pour la position, Rouge pour l'annulation) pour renforcer la sémantique visuelle.

## 6. Micro-interactions
1. **Mise à jour :** Animation de "Pulse" (battement) sur le numéro de position quand il change.
2. **Transition :** Glissement fluide (Slide) lors du passage d'un candidat de "Attente" à "En cours".
---
