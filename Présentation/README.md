---
marp: true
theme: default
_class: lead
_paginate: false
paginate: true
backgroundColor: #ffffff
style: |
  section {
    font-size: 22px;
    color: #333;
    line-height: 1.6;
    padding: 60px 80px;
  }
  footer { width: 100%; text-align: right; font-size: 14px; color: #888; }
  .logo-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: absolute;
    top: 40px;   
    left: 60px;
    right: 60px;
  }
  .logo-header img { height: 140px; margin: 0; margin-left:10px; margin-right:10px }
  h1 { color: #088dc7; font-size: 2.8em; margin-top: 100px; text-align: left; }
  h2 { color: #088dc7; font-size: 2em; border-bottom: 2px solid #088dc7; margin-bottom: 40px;}
  h3 { text-align: left; color: #444; margin-top: 0; }

  .sommaire-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
  }
  .sommaire-item {
    display: flex;
    align-items: center;
    background: #f4faff;
    border-radius: 12px;
    padding: 15px 20px;
    border-left: 5px solid #088dc7;
  }
  .sommaire-num {
    background: #088dc7; color: white; width: 35px; height: 35px;
    display: flex; justify-content: center; align-items: center;
    border-radius: 50%; font-weight: bold; margin-right: 15px; flex-shrink: 0;
  }
  
  .img-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
  }
  .img-methodo {
    width: 85%;
    height: auto;
    max-height: 450px;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }

  .dt-card {
    background: #f0f7fa;
    padding: 30px;
    border-radius: 10px;
    border-top: 6px solid #088dc7;
    text-align: left;
    margin-top: 20px;
    width: 100%;
  }

  /* --- FIX COULEURS TECH STACK --- */
  .tech-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
  }
  .badge-simple {
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 600;
    background-color: #545353ff; /* Gris foncé unique */
    color: #ffffff !important;
    font-size: 0.85em;
    border: 1px solid #222;
  }
  .maquette-grid {
    display: flex;
    gap: 15px;
    justify-content: center;
    align-items: flex-start;
    height: 350px;
  }

---

<div class="logo-header">
  <img src="images/ofppt-logo.png" alt="Logo Left">
  <img src="images/logo-solicode.png" alt="Logo Right">
</div>

# **Projet de Fin de Formation**
### Application de gestion de files d’attente

**Réalisé par :** <span class="highlight">Yousra Akajou</span>  
**Encadré par :** <span class="highlight">M. ESSARRAJ Fouad</span>  
**Filière :** Développement Mobile et Web

---

## Sommaire

<div class="sommaire-grid">
  <div class="sommaire-item"><div class="sommaire-num">1</div><div class="sommaire-text">Contexte du projet</div></div>
  <div class="sommaire-item"><div class="sommaire-num">2</div><div class="sommaire-text">Méthodologie de travail</div></div>
  <div class="sommaire-item"><div class="sommaire-num">3</div><div class="sommaire-text">Branche Fonctionnelle</div></div>
  <div class="sommaire-item"><div class="sommaire-num">4</div><div class="sommaire-text">Branche Technique</div></div>
  <div class="sommaire-item"><div class="sommaire-num">5</div><div class="sommaire-text">Conception</div></div>
    <div class="sommaire-item"><div class="sommaire-num">6</div><div class="sommaire-text">Démonstration</div></div>
  <div class="sommaire-item"><div class="sommaire-num">7</div><div class="sommaire-text">Conclusion</div></div>
</div>

---
## 1. Contexte du projet

---

## 2. Méthodologie : Design Thinking



<div class="img-container">
  <img src="images/designThinking.png" class="img-methodo" alt="Design Thinking">
</div>

---

## Méthodologie : Scrum (Agile)



<div class="img-container">
  <img src="images/scrum.jpg" class="img-methodo" alt="Scrum">
</div>

---

## Méthodologie : Processus 2TUP



<div class="img-container">
  <img src="images/2TUP.PNG" class="img-methodo" alt="2TUP">
</div>

---

## 3. Branche Fonctionnelle : Design Thinking
### 1. EMPATHIE

<div class="img-container">
  <div class="dt-card" style="border-top-color: #f39c12;">
    <h4>Comprendre l'utilisateur</h4>
    <blockquote style="font-style: italic; background: white; padding: 15px; border-radius: 8px;">
      <p>- Le candidat souhaite un ticket numérique, voir sa position et le temps d’attente en temps réel, et pouvoir annuler ou reporter son passage.</p>
      <p>-L’équipe SoliCode souhaite gérer facilement les sessions, réorganiser les files en cas de retard ou absence, et suivre l’ordre des candidats.</p>
      <p>- L’administrateur Hamouda souhaite une interface simple et intuitive, avec visibilité globale sur les tickets, notifications automatiques et statistiques pour optimiser les sessions.</p>
    </blockquote>
  </div>
</div>

---

## Branche Fonctionnelle : Design Thinking
### 2. DÉFINITION

<div class="img-container">
  <div class="dt-card" style="border-top-color: #f39c12;">
    <h4>Cadrage du problème</h4>
    <blockquote style="font-style: italic; background: white; padding: 15px; border-radius: 8px;">
     <p> - Comment pourrions-nous permettre aux candidats de prendre un ticket numérique et de voir leur position et temps d’attente en temps réel, pour réduire le stress et l’incertitude ?</p>
      <p>- Comment pourrions-nous structurer l’application pour que l’administration puisse gérer facilement les sessions, réorganiser les files en cas de retard ou d’absence, et suivre l’ordre des candidats ? </p>
      <p>- Comment pourrions-nous concevoir une interface administrateur simple, claire et dynamique, permettant de gérer efficacement les tickets, notifications et statistiques ?  </p>
    </blockquote>
  </div>
</div>

---

## Branche Fonctionnelle : Design Thinking
### 3. IDÉATION

<div class="img-container">
  <div class="dt-card" style="border-top-color: #f39c12;">
    <h4>Solutions retenues</h4>
    <p>• Interface <strong>"Single Question"</strong> pour éviter la surcharge cognitive.</p>
    <p>• <strong>Timer dynamique</strong> par catégorie de question.</p>
    <p>• <strong>Dashboard</strong> temps réel pour le suivi des formateurs.</p>
  </div>
</div>

---

## Branche Fonctionnelle : Cas d'utilisation



<div class="img-container">
  <h3>Interaction Utilisateur (UML)</h3>
  <img src="images/use-case.png" class="img-methodo" alt="Use Case">
</div>

---
## Branche Fonctionnelle : Maquettes (UI/UX)



<div class="maquette-grid">
  <div style="text-align: center;">
    <img src="images/maquette.png" class="img-methodo" style="height: 360px; width: auto;" alt="Maquette Desktop">
    <p style="font-size: 0.3rem; color: #666;">Interface Administration</p>
  </div>
</div>

---

## 4. Branche Technique : Tech Stack
<div class="sommaire-grid">
  <div class="dt-card" style="margin-top:0;">
    <h4>Les technologies à utiliser</h4>
    <ul>
      <li><strong>Base de données:</strong> MySQL </li>
      <li><strong>Framework:</strong> Laravel 12</li>
      <li><strong>Architecture:</strong> N-Tiers</li>
      <strong>Controller:</strong> Requêtes HTTP
      <strong>Service:</strong> Logique métier
      <strong>Model:</strong> Base de données
      <li><strong>Architecture:</strong> MVC</li>
      <li><strong> Blade :</strong>Templates réutilisables (components, layouts).</li>
    </ul>
  </div>
  <div class="dt-card" style="margin-top:0; border-top-color: #27ae60;">
    <ul>
      <li><strong> AJAX :</strong> Interactions dynamiques (ex: Modales) sans rechargement de page.</li>
      <li><strong>Alpine.js :</strong>  Librairie JavaScript pour les interactions dynamiques.</li>
      <li><strong>Spatie :</strong> Librairie pour la gestion des permissions et rôles.</li>
      <li><strong>Vite :</strong>   Outil de build rapide.</li>
      <li><strong>Lucide :</strong> Librairie d'icônes.</li>
      <li><strong>Tailwind CSS :</strong>Développement rapide, responsive.</li>
    </ul>
  </div>
</div>

---


## 5. Conception : Diagramme de classe


 <h3>Modélisation des données (MLD)</h3>
<div class="img-container">
 
  <img src="images/diagramme-class.png" style="width: 100%;" alt="Diagramme de classe">
</div>

---

## 5. Démonstration : Environnement & Outils

<div class="sommaire-grid">
  <div class="dt-card" style="margin-top:0;">
    <h4>Environnement de Développement</h4>
    <ul>
      <li><strong>IDE :</strong> VS Code & Antigravity </li>
      <li><strong>Monitoring DB :</strong> Workbench Sql</li>
    </ul>
  </div>
  <div class="dt-card" style="margin-top:0; border-top-color: #27ae60;">
    <h4>Gestion & Déploiement</h4>
    <ul>
      <li><strong>Modelisation UML :</strong>Mermaid/PlantUML</li>
      <li><strong>Gestion de version :</strong> Git (GitHub)</li>
      <li><strong>Navigateur :</strong> Chrome DevTools</li>
    </ul>
  </div>
</div>

<br>

---
## 6. Conclusion

- **Objectifs atteints** : Application QCM fonctionnelle et responsive.
- **Compétences** : Maîtrise du cycle Agile et de la stack Full-stack.
- **Perspectives** : Intégration d'un module d'IA pour l'analyse prédictive.

<br>

### Merci pour votre attention !