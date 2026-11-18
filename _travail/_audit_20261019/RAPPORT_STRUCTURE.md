# Audit de STRUCTURE — gabarits PHP de MotoHall 2A

**Racine auditée** : `C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel`
**Objet** : structure HTML des gabarits (pas le contenu métier). Audit en **lecture seule** — aucun fichier audité n'a été modifié.
**Mesure** : 2026-10-19 à 18:20:09 → 18:20:10, puis rejouée à l'identique (empreintes avant = après dans les deux passes).
**Rapport écrit** : 2026-10-19.

---

## 0. Ce qui a été mesuré, avec quoi, et sur quel état exact

### 0.1 Les deux outils

| Outil | Ce qu'il voit | Ce qu'il ne voit pas |
|---|---|---|
| **Compétence `verifier-structure-html`** (`structure_check.php` v2, PHP) | comptage des `<div>`, `<transition>`, `{{ }}`, pile LIFO, fermetures orphelines | **le contenu des blocs PHP est retiré avant analyse** → ne peut pas voir qu'une balise ouverte dans une branche `if` est fermée dans l'autre |
| **`_outils/audit_structure.py`** (écrit pour cet audit) | pile LIFO sur **40 types de balises** (les 19 demandés — `div`, `span`, `section`, `aside`, `header`, `footer`, `main`, `nav`, `form`, `a`, `button`, `p`, `template`, `h1`…`h6` — plus 21 autres : `label`, `table`, `tr`, `td`, `th`, `ul`, `li`, `dl`, `dt`, `dd`, `iframe`, `img`, `input`, `select`, `textarea`, `option`…), **équilibre branche par branche** des blocs `if/elseif/else/foreach/for/while/switch` en syntaxe alternative, étiquettes, `alt`, `type`, titres, `id`, ARIA, imbrications interdites | le HTML fabriqué dans une **chaîne PHP** (voir §0.3) |

Les deux ont été lancés sur les 14 fichiers. Journal complet et rejouable : [`mesures_20261019.txt`](_outils/mesures_20261019.txt) (317 lignes), données brutes : [`audit_structure.json`](_outils/audit_structure.json).

### 0.2 Empreinte des octets analysés

Les numéros de ligne de ce rapport valent **pour ces octets-là** (SHA-256 tronqué à 12 caractères) :

| Fichier | SHA-256 | Octets | Lignes |
|---|---|---|---|
| `layout_secure.php` | `BEA61EF894CD` | 12 203 | 213 |
| `header_ui.php` | `98EC4C6F96F6` | 7 120 | 144 |
| `sidebar.php` | `E575B0740D1D` | 7 433 | 150 |
| `footer_ui.php` | `D5CB4664017D` | 2 369 | 50 |
| `index_view.php` | `9A8E757ED4C4` | 13 750 | 246 |
| `dashboard_view.php` | `F5FE7E9C1D02` | 8 910 | 175 |
| `documents_view.php` | `28FEEC46D612` | 16 201 | 274 |
| `admin_users_view.php` | `46B93820F7B1` | 15 644 | 293 |
| `agenda_view.php` | `314A2D2CF0C0` | 13 145 | 274 |
| `comptoir_view.php` | `5E7295B42871` | 4 141 | 74 |
| `atelier_view.php` | `1B7082182175` | 4 217 | 73 |
| `profil_view.php` | `12C98253753A` | 7 360 | 140 |
| `login.php` | `7F42894F52F5` | 10 635 | 210 |
| `error.php` | `F1BF0A3B8D00` | 8 119 | 136 |

> ⚠️ **Six de ces fichiers ont été modifiés PENDANT l'audit** (par un autre intervenant, entre 18:18:57 et 18:19:34) : `layout_secure.php`, `header_ui.php`, `sidebar.php`, `footer_ui.php`, `login.php`, `error.php`. Le chantier a notamment ajouté le lien d'évitement, le `role="status"`, le `role="alertdialog"`, les `aria-label` des 4 boutons-icônes des partiels, `aria-current`, et le bouton de thème. **Les 8 vues n'ont pas bougé depuis le 20/09/2026.** Les chiffres ci-dessous sont ceux de l'état figé en §0.2 — si un fichier a rebougé depuis, se fier aux empreintes avant de citer une ligne.

> **Convention de citation** : la ligne citée est celle de la **balise ouvrante**. Quand une balise s'écrit sur plusieurs lignes, l'attribut visé se trouve sur une des lignes suivantes (ex. `admin_users_view.php:269` porte bien son `title` en L272) ; les cas utiles sont notés en intervalle (`L170-171`, `L202-203`).

> 🔎 **Ne pas confondre avec les copies d'avant-modification** : le chantier parallèle a déposé dans `_travail\_audit_20261019\_avant\` les 6 versions antérieures (`layout_secure.php` 11 155 o, `header_ui.php` 5 567 o, `sidebar.php`, `footer_ui.php`, `login.php` 8 811 o, `error.php` 7 726 o). **Toutes les lignes de ce rapport renvoient aux fichiers en place**, pas à ces copies — qui sont en revanche l'outil de comparaison pour vérifier ce qui a été changé pendant l'audit.

### 0.3 Limite mesurée, à connaître avant de conclure « tout va bien »

`header_ui.php` L50-65 construit **tout le bouton de thème dans une chaîne PHP** (`$basculeTheme = '<button …>…</button>';`), repris aux L89 et L124. Ce HTML est **invisible pour les deux outils** (PHP retiré avant analyse) — même angle mort que les heredocs documentés dans la compétence. Il a donc été **relu à la main** : ce bouton est conforme (`type="button"`, `aria-label`, `title`, `aria-pressed`, 2 SVG `aria-hidden="true"`).

Conséquence à retenir : **un futur déséquilibre dans cette chaîne ne sera détecté par aucun contrôle automatique du projet.**

---

## 1. Équilibre et imbrication des balises — 14 fichiers

Verdict rendu par la pile LIFO sur le HTML seul (blocs PHP, `<script>`, `<style>`, commentaires remplacés par des espaces — les numéros de ligne restent exacts).

| Fichier | Verdict d'équilibre | Détail (ouvertes/fermées) |
|---|---|---|
| `layout_secure.php` | **OK** | div 10/10 · span 1/1 · main 1/1 · a 1/1 · button 3/3 · p 3/3 · template 2/2 |
| `header_ui.php` | **OK** | div 10/10 · span 8/8 · header **2/2** (une par branche) · nav 1/1 · a 2/2 · button 1/1 · p 3/3 |
| `sidebar.php` | **OK** | div 8/8 · span 18/18 · aside 1/1 · nav 1/1 · a 9/9 · p 9/9 |
| `footer_ui.php` | **OK** | div 2/2 · span 6/6 · footer 1/1 |
| `index_view.php` | **OK** | div 20/20 · span 9/9 · section 1/1 · form 1/1 · a 1/1 · button 1/1 · p 9/9 |
| `dashboard_view.php` | **OK** | div 13/13 · span 10/10 · a 5/5 · p 5/5 |
| `documents_view.php` | **OK** | div 27/27 · span 3/3 · a 3/3 · button 4/4 · p 8/8 · template **10/10** |
| `admin_users_view.php` | **OK** | div 37/37 · span 13/13 · form 2/2 · button 9/9 · p 9/9 · template 2/2 |
| `agenda_view.php` | **OK** | div 27/27 · span 5/5 · form 1/1 · button 6/6 · p 6/6 · template 7/7 |
| `comptoir_view.php` | **OK** | div 9/9 · span 3/3 · a 1/1 · p 5/5 |
| `atelier_view.php` | **OK** | div 7/7 · span 3/3 · a 2/2 · p 5/5 |
| `profil_view.php` | **OK** | div 18/18 · span 4/4 · form 1/1 · a 1/1 · button 1/1 · p 7/7 |
| `login.php` | **OK** | div 11/11 · span 4/4 · header 1/1 · footer 1/1 · main 1/1 · form 1/1 · button 2/2 · h1 1/1 |
| `error.php` | **OK** | div 6/6 · span 5/5 · header 1/1 · footer 1/1 · main 1/1 · a 2/2 · h1 1/1 |

**Bilan** : 0 déséquilibre, 0 fermeture orpheline, 0 imbrication croisée, 0 balise jamais fermée. Les 14 fichiers sont déployables du point de vue de l'équilibre. `documents_view.php` est le seul avec **deux racines** (le `<div>` de L9 fermé L202, puis le `<template x-teleport>` de L205) — c'est licite (le `x-teleport` l'expédie dans `body`) mais c'est une exception à signaler, car les 3 autres vues à modale gardent la modale dans la racine.

---

## 2. Équilibre PAR BRANCHE — le cas `if/else` que le comptage global ne voit pas

C'est le contrôle qui manquait : une balise ouverte dans une branche et non fermée dans **la même** branche produit un HTML faux à l'exécution alors que le total du fichier reste pair.

**Résultat mesuré : 34 blocs de contrôle, 41 branches, 0 branche déséquilibrée.**

| Fichier | Blocs | Branches | Verdict | Blocs (ligne d'ouverture) |
|---|---|---|---|---|
| `layout_secure.php` | 3 | 3 | OK | L133 `if` · L134 `if` · L186 `if` |
| `header_ui.php` | 1 | **2** | OK | L68 `if…else` |
| `sidebar.php` | 3 | 3 | OK | L73 · L84 · L111 (`if`) |
| `footer_ui.php` | 2 | 2 | OK | L32 · L42 (`if`) |
| `index_view.php` | 9 | **12** | OK | L92 · L110 (2 br.) · L118 · L132 `foreach` · L150 · L163 `foreach` · L164 (2 br.) · L198 · L238 (2 br.) |
| `dashboard_view.php` | 4 | 5 | OK | L57 `foreach` · L86 (2 br.) · L107 `foreach` · L121 |
| `documents_view.php` | 1 | 1 | OK | L103 `foreach` |
| `admin_users_view.php` | 4 | 5 | OK | L59 · L66 · L221 `foreach` · L257 (2 br.) |
| `agenda_view.php` | 0 | 0 | — | aucun PHP de contrôle (tout est Alpine/JS) |
| `comptoir_view.php` | 0 | 0 | — | — |
| `atelier_view.php` | 0 | 0 | — | — |
| `profil_view.php` | 4 | 5 | OK | L22 · L29 · L83 (2 br.) · L92 |
| `login.php` | 3 | 3 | OK | L146 · L153 · L162 |
| `error.php` | 0 | 0 | — | — |

### Le cas redouté existe-t-il ici ?

**Non — il a été cherché, et il n'est pas là.** Deux endroits présentaient exactement le profil à risque (une balise structurelle **dupliquée dans chaque branche**), et les deux sont corrects :

1. **`header_ui.php` L68** — `<header>` est ouvert **et fermé** dans la branche visiteur (L70→L91) **et** dans la branche identifiée (L94→L143). Supprimer un seul `</header>` d'une seule branche laisserait le fichier à 2/2 (invisible) tout en cassant la page à l'exécution pour un seul profil. Ici : chaque branche a un delta de 0.
2. **`documents_view.php` L164** — dans le `foreach` L163, la branche `autorise` ouvre un `<a>` (L165→L176) et la branche visiteur un `<div>` (L179→L188) : deux structures **différentes** mais chacune refermée dans sa branche (delta 0 et 0).

Aucune balise n'est ouverte dans une branche pour être fermée après le `endif`/`endforeach` correspondant.

---

## 3. Champs sans étiquette — 6 champs, tous dans le même fichier

26 champs visibles analysés (les 6 `<input type="hidden">` — `action`, `csrf_token`, `user_id` — sont exclus à juste titre : ils ne prennent pas de focus et n'ont pas à être étiquetés).

| fichier:ligne | champ | ce qui manque |
|---|---|---|
| `admin_users_view.php:171` | `<input type="text" name="username">` | pas d'`id`, pas d'`aria-label` → le `<label>` de L170 ne le désigne pas |
| `admin_users_view.php:177` | `<input type="text" name="prenom">` | idem (label L176) |
| `admin_users_view.php:181` | `<input type="text" name="nom">` | idem (label L180) |
| `admin_users_view.php:187` | `<input type="email" name="email">` | idem (label L186) |
| `admin_users_view.php:192` | `<input type="password" name="password">` | idem (label L191) |
| `admin_users_view.php:197` | `<select name="role">` | idem (label L196) |

**Cause exacte** : dans la fenêtre « Modifier le membre » (L148-217), les six `<label class="etiquette">` (L170, 176, 180, 186, 191, 196) n'ont **aucun attribut `for`** et les six champs **aucun `id`**. Le label est un **frère** du champ, pas son enveloppe : aucune association implicite. Conséquence : au lecteur d'écran, ces six champs sont annoncés « champ de saisie » sans nom — et la fenêtre « Nouveau membre », juste au-dessus (L93-131), est elle parfaitement étiquetée (`for="add-username"` / `id="add-username"`, etc.). C'est donc une régression locale, pas un choix.

Les 20 autres champs sont corrects : `index_view.php:213/218`, `agenda_view.php:207,214,218,226,231,243,247`, `profil_view.php:110,116,123`, `login.php:171,177`, `admin_users_view.php:93,100,104,110,115,124`. Aucun `<label for>` cassé (pointant vers un `id` inexistant) : 0.

---

## 4. Boutons

27 boutons analysés, dont **9 dans un `<form>`** — et les 9 sont typés : **6 `type="submit"`** (`index_view.php:222`, `login.php:182`, `profil_view.php:128`, `agenda_view.php:256`, `admin_users_view.php:137` et `:209`) et **3 `type="button"`** (`agenda_view.php:253`, `admin_users_view.php:134` et `:206`).

### 4.1 Boutons sans `type="button"` dans un `<form>` — **AUCUN**

Le piège classique (un `<button>` sans `type` dans un `<form>` qui soumet le formulaire par accident) **n'existe pas ici** : 0 cas. Vérifié bouton par bouton dans les 5 formulaires (`index_view` L211, `login.php` L169, `profil_view` L106, `agenda_view` L204, `admin_users_view` L88 et L164).

### 4.2 Boutons sans attribut `type` — 4, tous **hors** formulaire

| fichier:ligne | bouton | risque actuel |
|---|---|---|
| `admin_users_view.php:51` | `<button @click="$dispatch('open-modal-add')" class="btn btn-accent">` | nul aujourd'hui ; **devient un submit** si la barre d'en-tête est un jour enveloppée dans un `<form>` |
| `admin_users_view.php:83` | `<button @click="modalAdd = false" …>` (croix de fermeture) | idem |
| `admin_users_view.php:159` | `<button @click="modalEdit = false" …>` (croix de fermeture) | idem |
| `documents_view.php:236` | `<button @click="viewer.show = false" …>` (bouton Fermer de la visionneuse) | idem |

Ce sont des défauts **latents** : rien ne casse aujourd'hui, mais la règle « tout bouton qui n'est pas un submit porte `type="button"` » n'est pas tenue, et ces 4 boutons sont précisément ceux qu'on déplace en refondant une modale.

### 4.3 Boutons-icônes sans nom accessible — 3

Un bouton dont le contenu n'est qu'une icône doit porter `aria-label` (ou `title`). Mesure : contenu textuel vide, ni `aria-label`, ni `aria-labelledby`, ni `title`.

| fichier:ligne | contenu | nom accessible |
|---|---|---|
| `admin_users_view.php:83` | `<i data-lucide="x">` seul | **aucun** → annoncé « bouton » |
| `admin_users_view.php:159` | `<i data-lucide="x">` seul | **aucun** |
| `agenda_view.php:199` | `<i data-lucide="x">` seul | **aucun** |

Les autres boutons-icônes sont conformes, et il faut le noter car le chantier vient d'en corriger une partie : `layout_secure.php:155` (`aria-label="Fermer la notification"`, ajouté ce jour), `header_ui.php:100` (`aria-label="Ouvrir le menu"`), `header_ui.php:51-52` — dans la chaîne PHP `$basculeTheme` (`aria-label` + `title` + `aria-pressed`), `documents_view.php:71` (`title`), `documents_view.php:79` (nom fourni par `x-text="b.name"` à l'exécution), `agenda_view.php:123` et `:130` (`title`), `admin_users_view.php:269` et `:276` (`title`).

### 4.4 Icônes décoratives

87 `<i data-lucide="…">` dans les 14 fichiers ; **0** porte `aria-hidden="true"`. En pratique, `lucide.createIcons()` remplace le `<i>` par un `<svg>` sans nom accessible, que les lecteurs d'écran ignorent : le risque est faible, mais le nouveau code fait l'inverse (8 `aria-hidden="true"` ajoutés sur les icônes et séparateurs de `header_ui`, `footer_ui`, `layout_secure`). Deux conventions cohabitent dans le même produit.

### 4.5 `<img>` et liens sans texte

- `<img>` : **1 seul** dans les 14 fichiers (`documents_view.php:254`), et il a un `alt` (`alt="Visuel Sécurisé"`). Le `<iframe>` de `documents_view.php:249` a un `title`. **0 image sans `alt`.**
- Liens : **27** analysés, **0** sans texte ni nom accessible. Les liens réduits à une icône portent un `title` (`documents_view.php:183` « Télécharger ce fichier »).

---

## 5. Hiérarchie des titres

### 5.1 Par fichier (ordre d'apparition, tel qu'écrit)

| Fichier | Suite des titres |
|---|---|
| `layout_secure.php` | h3(L173) |
| `header_ui.php` | *(aucun)* |
| `sidebar.php` | *(aucun)* |
| `footer_ui.php` | *(aucun)* |
| `index_view.php` | h2(L104) > h3(L143) > h2(L202) |
| `dashboard_view.php` | h2(L78) > h2(L141) |
| `documents_view.php` | h2(L14) > h3(L140) > h4(L226) > h3(L263) |
| `admin_users_view.php` | h2(L43) > h3(L82) > h3(L158) |
| `agenda_view.php` | h2(L114) > h3(L198) |
| `comptoir_view.php` | h3(L30) > h3(L45) > h3(L61) |
| `atelier_view.php` | h3(L28) > h3(L44) > h3(L59) |
| `profil_view.php` | h2(L41) > h2(L103) |
| `login.php` | h1(L139) |
| `error.php` | h1(L105) |

### 5.2 Par page assemblée (coque + partiels + vue) et verdict

| Page | Suite des niveaux | h1 | Verdict |
|---|---|---|---|
| `index.php` | h2 > h3 > h2 > h3 | **0** | ❌ aucun h1 ; le premier titre est h2 |
| `dashboard.php` | h2 > h2 > h3 | **0** | ❌ aucun h1 |
| `documents.php` | h2 > h3 > h4 > h3 > h3 | **0** | ❌ aucun h1 ; le h4 (L226) suit bien un h3 → pas de saut, mais un h3 (L263) vient **après** un h4 |
| `admin-users.php` | h2 > h3 > h3 > h3 | **0** | ❌ aucun h1 |
| `agenda.php` | h2 > h3 > h3 | **0** | ❌ aucun h1 |
| `comptoir.php` | h3 > h3 > h3 > h3 | **0** | ❌ aucun h1 **et saut d'entrée** : la page commence directement en h3 |
| `atelier.php` | h3 > h3 > h3 > h3 | **0** | ❌ aucun h1 **et saut d'entrée** en h3 |
| `profil.php` | h2 > h2 > h3 | **0** | ❌ aucun h1 |
| `login.php` | h1 | 1 | ✅ un h1, aucun autre titre |
| `error.php` | h1 | 1 | ✅ un h1 (mais le code « 404 », visuellement le plus gros texte, est un `<p>` en L101) |

**Ce qu'il faut retenir** : 8 pages sur 10 n'ont **aucun `<h1>`** ; c'est la coque qui est en cause, pas les vues — `header_ui.php` affiche le titre de page (`$titre = $pageTitle ?? 'Intranet'`) dans un `<span>` de fil d'Ariane (L109), donc **le titre visible de chaque page n'est pas un titre HTML**. Les libellés de section du menu sont des `<p class="titre-section">` (`sidebar.php` L57, 74, 85, 102, 112 ; `index_view.php` L103 ; `login.php` L138) : cinq sections de navigation sans titre de niveau 2.

**Aucun saut de niveau *interne*** (type h2 → h4) dans les 10 pages. Le seul défaut de niveau est à **l'entrée** : `comptoir_view.php` et `atelier_view.php` commencent en **h3** sans qu'aucun h1 ni h2 ne les précède — deux pages dont le premier titre est déjà une sous-section.

---

## 6. `id` dupliqués — aucun

| Mesure | Résultat |
|---|---|
| `id` dans les 14 fichiers | **22**, dont **0 dupliqué dans un même fichier** |
| `id` par page assemblée | index 4 · dashboard 2 · documents 2 · admin-users 8 · agenda 9 · comptoir 2 · atelier 2 · profil 5 |
| `id` dupliqués dans une page assemblée | **0** |
| `<label for>` pointant vers un `id` absent | **0** |

Détail des 22 `id` : `username` et `password` (`index_view.php:214/219` **et** `login.php:172/178` — deux pages différentes, donc pas de collision) ; `evt-title`, `evt-client`, `evt-plate`, `evt-date`, `evt-pont`, `evt-debut`, `evt-fin` (`agenda_view.php`) ; `add-username`, `add-prenom`, `add-nom`, `add-email`, `add-password`, `add-role` (`admin_users_view.php`) ; `actuel`, `nouveau`, `confirme` (`profil_view.php`) ; `contenu`, `confirmation-titre` (`layout_secure.php`).

Aucun attribut dupliqué sur une même balise non plus (0 cas détecté sur les 14 fichiers).

---

## 7. Attributs HTML invalides ou dangereux

| # | fichier:ligne | Constat | Gravité |
|---|---|---|---|
| 1 | `error.php:118` | `<a href="javascript:history.back()" class="btn btn-neutre">` — URI `javascript:` : bloquée par toute CSP stricte, cassée sans JS, impossible à ouvrir dans un nouvel onglet, et un lien ne devrait pas exécuter de code. C'est un **bouton**, pas un lien. | **réelle** |
| 2 | `agenda_view.php:266-269` | `<style>` (règles `::-webkit-calendar-picker-indicator`) **dans le corps de page**, en plein fragment de vue. Un `<style>` appartient au `<head>` ; ici il est réinjecté à chaque navigation de fragment. | **réelle** |
| 3 | `agenda_view.php:147` | `<template x-for="(day, index) in weekDays" :key="day">` — la clé est l'objet `Date` lui-même, et le getter `weekDays` (L24-33) **fabrique de nouveaux `Date` à chaque accès** (`let d = new Date(start)`). La clé change donc à chaque rendu : Alpine détruit et recrée les 7 colonnes au lieu de les réutiliser, avec le `x-for` des événements imbriqué (L160). Une clé primitive est attendue (`:key="formatDateSQL(day)"`). | **réelle (Alpine)** |
| 4 | `agenda_view.php:204` | `<form @submit.prevent="submitEvent">` sans `method` ni `action` : par défaut GET sur l'URL courante ; sans le `.prevent`, la page partirait en rechargement avec les champs en clair dans l'URL. | moyenne |
| 5 | `documents_view.php:205-210` | La visionneuse est un `<template x-teleport>` placé **hors de la racine** de la vue (le `<div>` ouvert L9 est fermé L202 — seule vue dans ce cas) ; elle porte en plus son **propre `x-data`** (L207-209) *et* un écouteur `@open-viewer.window` (L210) recevant le `$dispatch` émis depuis un autre sous-arbre (L176). Cela fonctionne (l'événement remonte à `window`), mais c'est le montage le plus indirect des 5 modales : état local + bus d'événement global pour la même chose. | moyenne |
| 6 | `layout_secure.php:73`, `login.php:97`, `error.php:76` | `href` des feuilles Google Fonts avec `&` non échappé (`&family=`, `&display=swap`). `&family` n'étant pas une entité, le navigateur garde le `&` littéral : **aucune casse**, mais HTML non conforme. | faible |
| 7 | `admin_users_view.php:270` | `&` non échappé dans l'`@click` (`&csrf_token=`) — même statut que ci-dessus. | faible |
| 8 | `agenda_view.php:9` (valeur à L49) | `&` non échappés dans l'URL `fetch` en template string (`&start=`, `&end=`, `&_=`) — idem. | faible |

**Faux positifs écartés** (mesurés, pas devinés) : les 12 `&&` des liaisons `:class`, `x-show` et `x-if` (`sidebar.php` L36, 59, 66, 76, 87, 94, 104, 114, 121 ; `documents_view.php` L102, 151, 193) ne sont **pas** un problème : `&&` n'est pas une référence de caractère nommée, le navigateur le conserve tel quel.

Aucune imbrication interdite détectée : **0** bloc (`div`, `p`, `table`, `h1`-`h6`…) à l'intérieur d'un `<p>`, **0** `<a>` dans un `<a>`, **0** `<button>` dans un `<a>`. C'est notable sur 14 fichiers où des cartes entières sont des liens (`comptoir_view.php:40`, `atelier_view.php:23` et `:54`) : les `<h3>` et `<p>` qu'elles contiennent y sont licites.

---

## 8. Focus et clavier

| # | fichier:ligne | Constat |
|---|---|---|
| 1 | `documents_view.php:133` | `<div @click="openFolder('…')" class="carte cursor-pointer …">` — **carte dossier cliquable en `<div>`** : ni `role`, ni `tabindex`, ni gestionnaire clavier → inatteignable au clavier, invisible au lecteur d'écran en tant que contrôle. Le `<h3>`/`<p>` qu'elle contient la rendent non convertible en `<button>` (contenu de flux interdit dans un bouton) : la correction est `role="button" tabindex="0"` + `@keydown.enter` / `@keydown.space.prevent`. |
| 2 | `documents_view.php:157` | Idem pour chaque sous-dossier (`<div @click="openFolder(f.path)">`). Ici le contenu (`<i>` + `<span>`) permettrait un vrai `<button type="button">`. |
| 3 | `documents_view.php:175` | Idem pour l'ouverture d'un fichier (`<div … @click="$dispatch('open-viewer', …)">`) — contient un `<div>` et des `<p>` : même correction `role`/`tabindex`/`keydown`. |
| 4 | `layout_secure.php:187` | `<div x-show="menuOuvert" … @click="menuOuvert = false" class="voile-menu" aria-hidden="true">` — **acceptable** : c'est un voile décoratif, correctement `aria-hidden`, et l'action équivalente est accessible au clavier par le bouton de menu (`header_ui.php:100`). À ne pas « corriger ». |
| 5 | 5 modales | **Aucune gestion du focus, nulle part** : dans les 14 fichiers, le seul `tabindex` est celui de `<main>` (`layout_secure.php:203`), et il n'existe **aucun** `.focus()`, `$nextTick`, `@keydown.escape` ni piège de focus. À l'ouverture de la fenêtre de confirmation (`layout_secure.php:166`), de « Nouveau membre » (`admin_users_view.php:75`), de « Modifier » (`admin_users_view.php:149`), du nouveau rendez-vous (`agenda_view.php:192`) ou de la visionneuse (`documents_view.php:206`), **le focus reste sur le bouton déclencheur, derrière le voile** : au clavier on continue de tabuler dans la page masquée, et **Échap ne ferme rien**. |
| 6 | `login.php:172-173` | `autofocus` sur le champ `#username` : place le curseur d'office, ce qui court-circuite le parcours au clavier et la lecture du titre par un lecteur d'écran. Acceptable sur une page de connexion dédiée, à assumer explicitement. |
| 7 | `layout_secure.php:136` + `:202` | ✅ **Lien d'évitement conforme** : `<a href="#contenu" class="lien-evitement">` → `<main id="contenu" tabindex="-1">`. La classe existe bien dans `aegis.css` (bloc `.lien-evitement`, dont `.lien-evitement:focus` est en L276) et le `:focus-visible` global aussi (L258). |

---

## 9. ARIA — usage et inventaire

**20 attributs ARIA** (dont 2 `role`) dans le HTML analysé, plus 1 `tabindex` et 1 `autofocus`, et 4 attributs ARIA de plus dans la chaîne PHP du bouton de thème. Inventaire complet :

| fichier:ligne | attribut | usage | verdict |
|---|---|---|---|
| `layout_secure.php:146` | `role="status"` + `aria-live="polite"` | notification | ✅ correct |
| `layout_secure.php:155` | `aria-label="Fermer la notification"` | bouton-icône | ✅ |
| `layout_secure.php:166-167` | `role="alertdialog"` + `aria-modal="true"` + `aria-labelledby="confirmation-titre"` | modale de confirmation | ✅ la cible `id` existe (L173) |
| `layout_secure.php:187` | `aria-hidden="true"` | voile du menu | ✅ élément non focalisable |
| `layout_secure.php:202-203` | `tabindex="-1"` | cible du lien d'évitement | ✅ |
| `header_ui.php:100` | `aria-label="Ouvrir le menu"` | bouton-icône | ✅ |
| `header_ui.php:106` | `aria-label="Fil d'Ariane"` | `<nav>` | ✅ |
| `header_ui.php:108,122,126` · `footer_ui.php:33,46` · `login.php:118,126` | `aria-hidden="true"` | icônes et séparateurs | ✅ tous sur des éléments non focalisables |
| `sidebar.php:36` | `aria-label="Navigation de l'intranet"` | `<aside>` | ✅ |
| `sidebar.php:55` | `aria-label="Navigation principale"` | `<nav>` | ✅ (les deux `<nav>` de la page sont maintenant nommés et distincts) |
| `sidebar.php:61,68,78,89,96,106,116,123` | `:aria-current="… ? 'page' : null"` | lien actif | ✅ `null` retire l'attribut — pas de `aria-current="false"` |
| `login.php:116` | `aria-pressed="false"` | bouton de thème | ⚠️ **valeur figée** : le HTML dit toujours « non pressé ». À vérifier côté `assets/js/theme.js` (qui référence bien `data-theme-toggle` et `aria-pressed`) — si le script ne met pas `aria-pressed` à jour, l'état annoncé est faux en thème clair. |

**Anomalies ARIA cherchées et absentes** : 0 `aria-hidden="true"` sur un élément focalisable, 0 `role="button"` sans `tabindex`, 0 `aria-labelledby` pointant dans le vide, 0 `role` inventé.

**Manques mesurables**, en revanche :
1. **4 modales sur 5 n'ont aucun rôle de dialogue** : `admin_users_view.php:75`, `:149`, `agenda_view.php:192`, `documents_view.php:206` n'ont ni `role="dialog"`, ni `aria-modal`, ni `aria-labelledby`. Seule la confirmation de `layout_secure.php:166` en a.
2. Les icônes Lucide ne portent pas `aria-hidden` (87 occurrences, §4.4) alors que les icônes SVG écrites à la main en portent.

---

## 10. Les 5 corrections les plus rentables

### ① `admin_users_view.php:170` → `:197` — 6 champs enfin étiquetés
*(6 des 26 champs de l'intranet sont aujourd'hui sans nom pour un lecteur d'écran ; une seule fenêtre, six `id` à ajouter.)*

Dans la fenêtre « Modifier le membre » (L148-217), ajouter un `id` et le `for` correspondant :

```php
170:  <label class="etiquette" for="edit-username">Identifiant de connexion</label>
171:  <input type="text" id="edit-username" name="username" required class="champ" :value="u.username">
…
176:  <label class="etiquette" for="edit-prenom">Prénom</label>
177:  <input type="text" id="edit-prenom" name="prenom" class="champ" :value="u.prenom">
180:  <label class="etiquette" for="edit-nom">Nom</label>
181:  <input type="text" id="edit-nom" name="nom" class="champ" :value="u.nom">
186:  <label class="etiquette" for="edit-email">Adresse e-mail …</label>
187:  <input type="email" id="edit-email" name="email" class="champ" :value="u.email">
191:  <label class="etiquette" for="edit-password">Nouveau mot de passe …</label>
192:  <input type="password" id="edit-password" name="password" class="champ" autocomplete="new-password">
196:  <label class="etiquette" for="edit-role">Niveau d'accès</label>
197:  <select id="edit-role" name="role" required class="champ" :value="u.role">
```
*(Les `id` en `edit-…` ne collisionnent pas avec ceux de la fenêtre « Nouveau membre », en `add-…`.)*

### ② 3 boutons-icônes sans nom — `agenda_view.php:199`, `admin_users_view.php:83` et `:159`
*(Fermer une fenêtre est l'action la plus utilisée d'un formulaire modal ; sans nom, elle est indéchiffrable au lecteur d'écran. La même correction vient d'être faite en `layout_secure.php:155` : il ne reste qu'à l'aligner.)*

```php
agenda_view.php:199
  <button type="button" @click="showModal = false" class="text-slate-500 hover:text-slate-300"
          aria-label="Fermer la fenêtre">

admin_users_view.php:83
  <button type="button" @click="modalAdd = false" class="text-slate-500 hover:text-slate-300"
          aria-label="Fermer la fenêtre">

admin_users_view.php:159
  <button type="button" @click="modalEdit = false" class="text-slate-500 hover:text-slate-300"
          aria-label="Fermer la fenêtre">
```
(Les deux boutons d'`admin_users_view` gagnent aussi le `type="button"` qui leur manque — 2 des 4 défauts du §4.2 corrigés au passage.)

### ③ `documents_view.php:133`, `:157`, `:175` — les dossiers redeviennent utilisables au clavier
*(Toute la navigation « Documentation » — 3 zones sur cette page — n'existe pas au clavier : c'est la seule page dont le cœur d'usage est souris-seulement.)*

```php
133:  <div role="button" tabindex="0"
          @click="openFolder('<?= htmlspecialchars($d['dossier'], ENT_QUOTES, 'UTF-8') ?>')"
          @keydown.enter.prevent="openFolder('<?= htmlspecialchars($d['dossier'], ENT_QUOTES, 'UTF-8') ?>')"
          @keydown.space.prevent="openFolder('<?= htmlspecialchars($d['dossier'], ENT_QUOTES, 'UTF-8') ?>')"
          class="carte cursor-pointer transition-colors duration-200 hover:border-[#38bdf8]/40">
```
Même traitement en `:157` (`@click="openFolder(f.path)"` → `role`, `tabindex`, `@keydown.enter.prevent`, `@keydown.space.prevent` sur `openFolder(f.path)`) et en `:175` (sur l'expression `$dispatch('open-viewer', { url: f.url, type: f.ext, name: f.name })`).

### ④ `layout_secure.php` — un `<h1>` pour les 8 pages, d'un seul coup
*(8 pages sur 10 n'ont aucun h1 ; les vues n'y sont pour rien, c'est la coque. Une ligne répare les huit.)*

Juste après `<main … tabindex="-1">` (L202-203), avant le `require_once` de L204 :

```php
204:  <h1 class="sr-only"><?= htmlspecialchars(($pageTitle ?? 'Intranet') . ' — ' . SITE_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
```
`sr-only` existe bien dans la feuille compilée (`assets/css/tailwind.css`, `.sr-only{clip-path:inset(50%);…}`) : aucun changement visuel, un titre de page réel pour tous les profils, y compris visiteur sur `index.php`.

**Et, dans le même lot** : `comptoir_view.php:30,45,61` et `atelier_view.php:28,44,59` doivent passer de `<h3>` à `<h2>` (ce sont les sections de premier niveau de la page, pas des sous-sections) — sans quoi ces deux pages passeront d'un « saut d'entrée en h3 » à un saut h1 → h3.

### ⑤ `error.php:118` — supprimer le lien `javascript:`
*(Seul attribut réellement dangereux des 14 fichiers : bloqué par une CSP stricte, faux bouton, comportement différent au clic-milieu.)*

```php
118:  <button type="button" class="btn btn-neutre" onclick="history.back()">
119:      <i data-lucide="arrow-left" class="w-4 h-4"></i>
120:      <span>Retour</span>
121:  </button>
```
*(`.btn` est une classe autonome d'`aegis.css` : elle s'applique aussi bien à un `<button>` ; remplacer le `</a>` de L121 par `</button>`.)*

**Bonus quasi gratuit, à faire dans la même passe** : `type="button"` sur `admin_users_view.php:51` et `documents_view.php:236` (les 2 boutons sans `type` restants du §4.2), et `:key="formatDateSQL(day)"` à la place de `:key="day"` en `agenda_view.php:147`.

---

## 11. Comptage final par catégorie

| Catégorie | Nombre | Où |
|---|---|---|
| Fichiers déséquilibrés (balises) | **0** / 14 | — |
| Branches PHP déséquilibrées | **0** / 41 branches (34 blocs) | — |
| Imbrications croisées | **0** | — |
| Fermetures orphelines / balises jamais fermées | **0** | — |
| Champs visibles sans étiquette | **6** | `admin_users_view.php` L171, 177, 181, 187, 192, 197 |
| `<label>` sans `for` | **6** (les mêmes) | `admin_users_view.php` L170, 176, 180, 186, 191, 196 |
| `<label for>` cassés | **0** | — |
| Boutons sans `type` **dans un form** | **0** | — |
| Boutons sans `type` hors form | **4** | `admin_users_view.php` L51, 83, 159 ; `documents_view.php` L236 |
| Boutons-icônes sans nom accessible | **3** | `admin_users_view.php` L83, 159 ; `agenda_view.php` L199 |
| `<img>` sans `alt` | **0** / 1 image | — |
| Liens sans texte ni nom | **0** / 27 liens | — |
| Zones cliquables non focalisables (`div` + `@click`) | **3** (+1 cas acceptable) | `documents_view.php` L133, 157, 175 ; (`layout_secure.php` L187 = voile décoratif, conforme) |
| Pages sans `<h1>` | **8** / 10 | index, dashboard, documents, admin-users, agenda, comptoir, atelier, profil |
| Pages à saut de niveau d'entrée | **2** | comptoir (h3), atelier (h3) |
| Sauts de niveau ascendants | **0** | — |
| `id` dupliqués (fichier ou page) | **0** | — |
| Attributs dupliqués sur une balise | **0** | — |
| Attributs invalides / dangereux | **8** constats (3 réels · 2 de robustesse · 3 `&` non échappés, sans casse) | §7 |
| Modales sans gestion du focus ni Échap | **5** | `layout_secure.php:166`, `admin_users_view.php:75`, `:149`, `agenda_view.php:192`, `documents_view.php:206` |
| Modales sans `role="dialog"` | **4** | `admin_users_view.php:75`, `:149`, `agenda_view.php:192`, `documents_view.php:206` |
| ARIA incorrects | **0** (1 point à vérifier : `aria-pressed` figé, `login.php:116`) | §9 |
| `<style>` dans le corps | **1** | `agenda_view.php:266` |

---

## 12. Réponse aux trois chiffres demandés

- **Fichiers déséquilibrés : 0** sur 14 (et **0** branche PHP déséquilibrée sur 41 branches / 34 blocs de contrôle).
- **Champs sans étiquette : 6** — tous dans `admin_users_view.php` (fenêtre « Modifier le membre »), L171, 177, 181, 187, 192, 197.
- **Boutons sans `type` : 4** — `admin_users_view.php` L51, 83, 159 et `documents_view.php` L236. **Aucun n'est dans un `<form>`** : le piège du submit accidentel est donc absent aujourd'hui, mais 3 de ces 4 boutons sont des boutons de fermeture de modale, exactement ceux qu'un futur `<form>` engloberait.

> **Rappel de méthode** : ces trois chiffres valent pour les empreintes de la §0.2 (état du 2026-10-19 18:20:10). Six fichiers ayant été modifiés pendant l'audit, revérifier les empreintes avant de citer une ligne. Le contrôle d'équilibre est rejouable en une commande :
> `php structure_check.php <fichier>` (compétence) et `python _outils/audit_structure.py` (branches, étiquettes, ARIA) — les deux sont dans `_outils/`, avec leur journal.
