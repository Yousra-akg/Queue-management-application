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
**Filière :** Développement Mobile 

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
<img src="images/contexte.png" class="img-methodo" alt="Design Thinking">

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

## Méthodologie : 2TUP



<div class="img-container">
  <img src="images/2tup.png" class="img-methodo" alt="2TUP">
</div>

---

## 3. Branche Fonctionnelle : Design Thinking
### 1. EMPATHIE
### Carte d'empathie Candidat

<div class="img-container">
  <img src="images/carte_empathie2.png" class="img-methodo" alt="Scrum">
</div>



---
## Branche Fonctionnelle : Design Thinking
### 1. EMPATHIE
### Carte d'empathie Formateur

<div class="img-container">
  <img src="images/carte_empathie3.png" class="img-methodo" alt="Scrum">
</div>

---
## Branche Fonctionnelle : Design Thinking
### 1. EMPATHIE

### Carte d'empathie Administrateur

<div class="img-container">
  <img src="images/carte_empathie1.png" class="img-methodo" alt="Scrum">
</div>

---

## Branche Fonctionnelle : Design Thinking
### 2. DÉFINITION

<div class="img-container">
  <div class="dt-card" style="border-top-color: #f39c12;">
    <h4>Cadrage du problème</h4>
    <blockquote style="font-style: italic; background: white; padding: 15px; border-radius: 8px;">
     <p> Les candidats souffrent d'une attente opaque et stressante sans visibilité sur leur rang, tandis que l'administration peine à gérer les flux manuellement sur papier, ce qui rend le processus d'entretien désorganisé et inefficace.</p>
      <p>- Comment pourrions-nous digitaliser la file d'attente pour offrir une transparence totale aux candidats et un pilotage centralisé aux organisateurs ? </p>
    </blockquote>
  </div>
</div>

---

## Branche Fonctionnelle : Cas d'utilisation
### Diagramme cas d'utilisation global: Partie Public
<div class="img-container">
  <img src="images/UC_public_global.png" class="img-methodo" alt="Scrum">
</div>

---

## Branche Fonctionnelle : Cas d'utilisation
### Diagramme cas d'utilisation global: Partie Admin
### Espace Admin
<div class="img-container">
  <img src="images/UC_admin_global.png" class="img-methodo" alt="Scrum">
</div>

---

## Branche Fonctionnelle : Cas d'utilisation
### Diagramme cas d'utilisation global: Partie Admin
### Espace Formateur
<div class="img-container">
  <img src="images/UC_formateur_global.png" class="img-methodo" alt="Scrum">
</div>

---

## Branche Fonctionnelle : Cas d'utilisation
### Diagramme cas d'utilisation global: Mobile
### Espace Formateur
<div class="img-container">
  <img src="images/UC_mobile_global.png" class="img-methodo" alt="Scrum">
</div>

---

## Branche Fonctionnelle : Cas d'utilisation - Sprint 1
<div class="img-container">
  <img src="images/Uc_sprint1.png" class="img-methodo" alt="Scrum">
</div>

---

## Branche Fonctionnelle : Cas d'utilisation - Sprint 2
<div class="img-container">
  <img src="images/UC_sprint2.png" class="img-methodo" alt="Scrum">
</div>

---
## Branche Fonctionnelle : Maquette web



<div class="maquette-grid">
  <div style="text-align: center;">
    <img src="images/maquette_web.png" class="img-methodo" style="height: 360px; width: auto;" alt="Maquette Desktop">
    <p style="font-size: 0.3rem; color: #666;">Interface Administration</p>
  </div>
</div>

---

## Branche Fonctionnelle : Maquette mobile

<div class="maquette-grid">
  <div style="text-align: center;">
    <img src="images/maquette_mobile.png" class="img-methodo" style="height: 360px; width: auto;" alt="Maquette Desktop">
    <p style="font-size: 0.3rem; color: #666;">Interface Mobile</p>
  </div>
</div>

---

## 4. Branche Technique : Tech Stack
<div class="sommaire-grid">

  <div class="dt-card" style="margin-top:0;">
    <h4>Backend</h4>
    <ul>
      <li><strong>Framework :</strong> Laravel 12</li>
      <li><strong>Base de données :</strong> MySQL</li>
      <li><strong>Architecture :</strong> MVC / N-Tiers</li>
    </ul>
  </div>

  <div class="dt-card" style="margin-top:0; border-top-color: #27ae60;">
    <h4>Frontend</h4>
    <ul>
      <li><strong>Preline</strong></li>
      <li><strong>Alpine.js</strong></li>
      <li><strong>AJAX</strong></li>
    </ul>
  </div>

</div>

---


## 5. Conception : Diagramme de classe


 <h3>Modélisation des données</h3>
<div class="img-container">
 
  <img src="images/diagramme_classe.png" style="width: 100%;" alt="Diagramme de classe">
</div>

---

## 6. Conclusion

### Merci pour votre attention !