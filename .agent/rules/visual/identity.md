---
trigger: always_on
type: rule
id: soliqueue-visual-identity
---

# CHARTE GRAPHIQUE ET CONVENTIONS VISUELLES - SOLIQUEUE

## 1. Respect de la Charte Graphique
*   Les composants et pages Web de SoliQueue doivent respecter une identité visuelle moderne, futuriste et fluide (courbes prononcées avec `rounded-2xl` ou `rounded-[2.5rem]`, animations de chargement et dégradés élégants).
*   Consulter les maquettes sous `Maquettage/` pour les alignements et espacements.

## 2. Palette de Couleurs Intégrée

Le thème de l'application utilise une palette basée sur le bleu Google `#1A73E8` combiné à des tons sombres et épurés de gris/bleu marine.

| Token / Couleur | Code Hex / Tailwind | Rôle Visuel |
|---|---|---|
| Primaire (Bleu Google) | `#1A73E8` (bg-blue-600) | Boutons d'action principaux, focus d'inputs, titres actifs |
| Doux (Bleu Clair) | `bg-blue-50` / `text-blue-700` | Pastilles d'informations, sélections d'éléments, avatars par défaut |
| Fond de Carte (White) | `bg-white` | Conteneurs des formulaires, sessions, et tableaux |
| Fond de Page (Slate) | `bg-[#F8F9FA]` (geometric-bg) | Arrière-plan de l'espace formateur et candidat |
| Succès (Présent) | `bg-green-50` / `text-green-600` | Statut **Présent**, pastille animée clignotante 🟢 |
| Danger (Absent/Erreur) | `bg-red-50` / `text-red-600` | Bouton retrait candidat, alerte de capacité dépassée |

## 3. Typographie
*   **Police Principale** : **Inter** (`font-sans`) pour toutes les descriptions, tableaux et textes de corps afin de maximiser la lisibilité.
*   **Police Moderne d'En-têtes** : **Outfit** ou Inter avec graisses très marquées (`font-black`, `tracking-tighter`, `uppercase`) pour donner du caractère aux titres de sections et d'acteurs.

## 4. Composants Clés dans SoliQueue

### Badges des Tickets d'Attente
```html
<!-- En attente -->
<span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
  En attente
</span>

<!-- En cours (Entretien en cours) -->
<span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
  <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
  En cours
</span>

<!-- Terminé -->
<span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
  Terminée
</span>
```

### Boutons d'Action Primaires (Ajustés au bleu #1A73E8)
*   **Bouton standard** : `py-3 px-6 bg-[#1A73E8] text-white text-xs font-black rounded-2xl uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center gap-2`
