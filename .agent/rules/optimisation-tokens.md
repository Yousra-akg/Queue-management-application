---
trigger: always_on
---

# Optimisation des Tokens

## Objectif

Réduire la consommation de tokens dans les interactions de l'agent StageFlow, en optimisant l'**Input** (ce que l'agent lit), l'**Output** (ce que l'agent écrit) et la **Réflexion** (processus interne).

## Instructions

### 1. Directives de Communication (Output)

*   **Supprimer** toutes les formules de politesse facultatives ("Bien sûr", "Avec plaisir", "Voici le code").
*   **Aller droit au but** technique sans préambule superflu.
*   **Utiliser** le format `diff` pour les modifications locales au lieu de réécrire le fichier en entier.
*   **Limiter** les explications à une phrase de moins de 15 mots lorsque c'est suffisant.
*   **Ne pas ajouter** de commentaires superflus dans le code de production, sauf logique complexe.

### 2. Contexte (Input)

*   **Lecture différée (Lazy Reading)** : Ne lire les fichiers que s'ils sont indispensables à la résolution de la tâche.
*   **Focus Sélectif** : Ignorer les modules ou fichiers non touchés par la modification (ex. ne pas analyser le CSS si on modifie un service).

### 3. Réflexion (Thinking)

*   **Être concis** dans le processus de pensée interne.
*   **Éviter** de reformuler mot pour mot la demande de l'utilisateur.

## Interdictions

*   **INTERDICTION** d'introductions ou conclusions polies.
*   **INTERDICTION** de renvoyer un fichier complet si un diff suffit.
*   **INTERDICTION** de faire des résumés redondants après modification.
