# Rapport de contraste WCAG 2.2 AA — MotoHall 2A

Audit du **19/10/2026** — projet `moto-hall_laravel` — **lecture seule**, aucun fichier source modifié.

> **À lire d'abord.** Les chiffres portent sur l'**arbre de travail au moment de l'exécution** (mesure du 19/10/2026 18:27:09), qui était **en cours de reprise** : 9 fichiers PHP sur 23 et `aegis.css` diffèrent encore de l'état de référence du 21/09. La section 8 redonne la comparaison avec cet état de référence.

Les chiffres portent sur l'**arbre de travail au moment de l'exécution** (empreintes en section 8).

## 1. Ce que dit la mesure

| mesure | valeur |
|---|---|
| couples texte/fond mesurés | **260** |
| balises correspondantes dans les gabarits | 260 |
| fichiers PHP lus | 23 |
| dont fichiers portant du balisage | 15 |
| **échecs AA — bloquants** (texte porteur d'information) | **33** |
| échecs AA — gênants (texte secondaire ou graphisme) | 11 |
| **total des échecs AA** | **44** |
| AAA manqués (couples qui passent AA) — gênants | 36 |
| AAA manqués (couples qui passent AA) — cosmétiques | 0 |
| échecs AAA au total | 73 |
| couples déjà conformes AA | 214 |
| couples atteignant déjà 7:1 (AAA) | 173 |
| éléments sous 14 px | 99 (dont 93 porteurs d'information) |
| **pire ratio mesuré** | **1.58:1** — `index_view.php:185` (`block text-[0.74rem] text-slate-600 truncate` sur `#101b2c`) |

La racine typographique est de **15 px** (`aegis.css:79`), donc `0.72rem` = **10.8 px** et `0.78rem` = **11.7 px**. Le plancher `!important` de `aegis.css:119` ramène tout `text-[8px]` … `text-[11px]` à **11.7 px** : **3 balises** mesurées à cette taille dans les sources.

Le détail de chaque couple est dans `contraste_mesures.csv` (séparateur `;`, ouvrable au tableur) et `contraste_mesures.json`.

Pour être complet : le ratio le plus bas du site (**1.05:1**, `documents_view.php:258`) est une **icône en filigrane** à 2 % de blanc — du décor volontaire, hors périmètre (section 2.4).

---

## 2. Échecs par gravité

La gravité se déduit mécaniquement de deux choses : le seuil (AA ou AAA) et le rôle du texte (*porteur d'information* ou *secondaire/décor*). Un texte informatif sous AA est **bloquant** ; un texte informatif qui passe AA mais reste sous AAA est **gênant** ; un texte secondaire sous AA est **gênant** ; il n'y a de **cosmétique** que si le texte secondaire passe déjà AA.

### 2.1 Bloquant — 33 couples

| fichier:ligne | classe CSS | couleur | fond composé | ratio | seuil requis | taille px | verdict |
|---|---|---|---|---|---|---|---|
| `index_view.php:185` | `block text-[0.74rem] text-slate-600 truncate` | `#313d50` | `#101b2c` | **1.58** | 4.5:1 | 11.1 | ÉCHEC AA |
| `admin_users_view.php:268` | `text-[0.72rem] text-slate-600` | `#475569` | `#0f1a2b` | **2.30** | 4.5:1 | 10.8 | ÉCHEC AA |
| `documents.php:152` | `text-[10px] opacity-60 font-bold mt-1 uppercase tracking-tighter` | `#9a2e34` | `#1a0c1c` | **2.53** | 4.5:1 | 11.7 | ÉCHEC AA |
| `index_view.php:184` | `block text-[0.86rem] text-slate-400 truncate` | `#5f6c80` | `#101b2c` | **3.25** | 4.5:1 | 12.9 | ÉCHEC AA |
| `documents_view.php:241` | `text-[0.85rem] text-cyan-400/50 uppercase tracking-[0.4em] font-` | `#18768c` | `#0f1a2b` | **3.33** | 4.5:1 | 12.75 | ÉCHEC AA |
| `index_view.php:173` | `block text-[0.74rem] text-slate-500 truncate` | `#64748b` | `#141f2f` | **3.48** | 4.5:1 | 11.1 | ÉCHEC AA |
| `admin_users_view.php:119` | `mt-1.5 text-[0.75rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.25 | ÉCHEC AA |
| `admin_users_view.php:242` | `text-[0.78rem] text-slate-500 truncate` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.7 | ÉCHEC AA |
| `dashboard_view.php:68` | `mt-2 text-[0.75rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.25 | ÉCHEC AA |
| `dashboard_view.php:90` | `text-[0.78rem] text-slate-500 mt-1` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.7 | ÉCHEC AA |
| `dashboard_view.php:99` | `.tableau th (aegis, sélecteur descendant)` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 10.8 | ÉCHEC AA |
| `dashboard_view.php:100` | `.tableau th (aegis, sélecteur descendant)` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 10.8 | ÉCHEC AA |
| `dashboard_view.php:101` | `.tableau th (aegis, sélecteur descendant)` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 10.8 | ÉCHEC AA |
| `dashboard_view.php:102` | `.tableau th (aegis, sélecteur descendant)` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 10.8 | ÉCHEC AA |
| `dashboard_view.php:103` | `.tableau th (aegis, sélecteur descendant)` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 10.8 | ÉCHEC AA |
| `index_view.php:121` | `mt-2 text-[0.78rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.7 | ÉCHEC AA |
| `index_view.php:146` | `text-[0.78rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.7 | ÉCHEC AA |
| `profil_view.php:61` | `text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 13.2 | ÉCHEC AA |
| `profil_view.php:65` | `text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 13.2 | ÉCHEC AA |
| `profil_view.php:71` | `text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 13.2 | ÉCHEC AA |
| `profil_view.php:81` | `text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 13.2 | ÉCHEC AA |
| `profil_view.php:93` | `mt-5 text-[0.8rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 12 | ÉCHEC AA |
| `profil_view.php:119` | `mt-1.5 text-[0.75rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.25 | ÉCHEC AA |
| `profil_view.php:134` | `mt-4 text-[0.78rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.7 | ÉCHEC AA |
| `documents_view.php:277` | `text-[11px] text-white/40 leading-loose font-bold mb-10 w-4/5 mx` | `#6b6f75` | `#09101a` | **3.78** | 4.5:1 | 11.7 | ÉCHEC AA |
| `sidebar.php:49` | `text-[0.85rem] text-slate-500 leading-tight` | `#64748b` | `#0b1424` | **3.87** | 4.5:1 | 12.75 | ÉCHEC AA |
| `sidebar.php:141` | `text-[0.85rem] text-slate-500 truncate` | `#64748b` | `#0b1424` | **3.87** | 4.5:1 | 12.75 | ÉCHEC AA |
| `atelier_view.php:70` | `text-[0.78rem] text-slate-500 pt-2` | `#64748b` | `#020617` | **4.24** | 4.5:1 | 11.7 | ÉCHEC AA |
| `comptoir_view.php:71` | `text-[0.78rem] text-slate-500 pt-2` | `#64748b` | `#020617` | **4.24** | 4.5:1 | 11.7 | ÉCHEC AA |
| `header_ui.php:78` | `text-[0.85rem] text-slate-500 leading-tight truncate` | `#64748b` | `#020617` | **4.24** | 4.5:1 | 12.75 | ÉCHEC AA |
| `header_ui.php:124` | `text-[0.85rem] text-slate-500` | `#64748b` | `#020617` | **4.24** | 4.5:1 | 12.75 | ÉCHEC AA |
| `header_ui.php:139` | `text-[0.85rem] text-slate-500` | `#64748b` | `#020617` | **4.24** | 4.5:1 | 12.75 | ÉCHEC AA |
| `login.php:194` | `mt-6 text-center text-[0.78rem] text-slate-500` | `#64748b` | `#020617` | **4.24** | 4.5:1 | 11.7 | ÉCHEC AA |

### 2.2 Gênant — 47 couples

| fichier:ligne | classe CSS | couleur | fond composé | ratio | seuil requis | taille px | verdict |
|---|---|---|---|---|---|---|---|
| `index_view.php:187` | `w-3.5 h-3.5 text-slate-600 shrink-0` | `#313d50` | `#101b2c` | **1.58** | 3.0:1 | 15 | ÉCHEC 1.4.11 (3:1) |
| `documents.php:191` | `w-4 h-4 ml-auto opacity-20 transition-transform text-white` | `#3f424f` | `#0f1223` | **1.86** | 3.0:1 | 15 | ÉCHEC 1.4.11 (3:1) |
| `index_view.php:181` | `w-4 h-4 shrink-0 text-slate-500` | `#425065` | `#101b2c` | **2.11** | 3.0:1 | 15 | ÉCHEC 1.4.11 (3:1) |
| `index_view.php:175` | `w-4 h-4 text-slate-600 shrink-0` | `#475569` | `#141f2f` | **2.19** | 3.0:1 | 15 | ÉCHEC 1.4.11 (3:1) |
| `agenda_view.php:183` | `text-center text-[0.75rem] text-slate-600 py-8` | `#475569` | `#0f1a2b` | **2.30** | 4.5:1 | 11.25 | ÉCHEC AA |
| `dashboard_view.php:88` | `w-6 h-6 text-slate-600 mx-auto mb-3` | `#475569` | `#0f1a2b` | **2.30** | 3.0:1 | 15 | ÉCHEC 1.4.11 (3:1) |
| `documents_view.php:78` | `w-4 h-4 text-slate-600` | `#475569` | `#0f1a2b` | **2.30** | 3.0:1 | 15 | ÉCHEC 1.4.11 (3:1) |
| `documents_view.php:207` | `w-6 h-6 text-slate-600 mx-auto mb-3` | `#475569` | `#0f1a2b` | **2.30** | 3.0:1 | 15 | ÉCHEC 1.4.11 (3:1) |
| `index_view.php:228` | `mt-4 text-[0.78rem] text-slate-500` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 11.7 | ÉCHEC AA |
| `profil_view.php:53` | `text-[0.82rem] text-slate-500 truncate` | `#64748b` | `#0f1a2b` | **3.67** | 4.5:1 | 12.3 | ÉCHEC AA |
| `index_view.php:237` | `text-[0.78rem] text-slate-500` | `#64748b` | `#020617` | **4.24** | 4.5:1 | 11.7 | ÉCHEC AA |
| `documents.php:148` | `p-8 rounded-[2rem] bg-red-500/10 border border-red-500/20 text-r` | `#ef4444` | `#1a0c1c` | **5.01** | 4.5:1 | 15 | AA ok / AAA non |
| `documents.php:151` | `font-display font-black uppercase tracking-widest text-xs` | `#ef4444` | `#1a0c1c` | **5.01** | 4.5:1 | 11.25 | AA ok / AAA non |
| `documents.php:161` | `text-red-500 font-bold p-4 uppercase text-[10px]` | `#ef4444` | `#020617` | **5.36** | 4.5:1 | 11.7 | AA ok / AAA non |
| `documents_view.php:248` | `px-6 py-4 rounded-xl bg-red-500 hover:bg-red-400 text-black shad` | `#000000` | `#ef4444` | **5.58** | 4.5:1 | 12.75 | AA ok / AAA non |
| `index_view.php:120` | `badge badge-accent` | `#5cc8fb` | `#253b4f` | **6.11** | 4.5:1 | 15 | AA ok / AAA non |
| `profil_view.php:62` | `badge badge-accent` | `#5cc8fb` | `#253b4f` | **6.11** | 4.5:1 | 13.2 | AA ok / AAA non |
| `admin_users_view.php:262` | `badge badge-danger` | `#fca5a5` | `#383745` | **6.15** | 4.5:1 | 12.3 | AA ok / AAA non |
| `profil_view.php:86` | `badge badge-danger` | `#fca5a5` | `#383745` | **6.15** | 4.5:1 | 13.2 | AA ok / AAA non |
| `admin_users_view.php:260` | `badge badge-succes` | `#4ade80` | `#233e40` | **6.57** | 4.5:1 | 12.3 | AA ok / AAA non |
| `profil_view.php:84` | `badge badge-succes` | `#4ade80` | `#233e40` | **6.57** | 4.5:1 | 13.2 | AA ok / AAA non |
| `layout_secure.php:177` | `btn btn-danger` | `#fca5a5` | `#303147` | **6.68** | 4.5:1 | 15 | AA ok / AAA non |
| `admin_users_view.php:250` | `flex items-center gap-2 text-slate-400` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 12.3 | AA ok / AAA non |
| `admin_users_view.php:254` | `flex items-center gap-2 text-slate-400` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 12.3 | AA ok / AAA non |
| `agenda_view.php:115` | `text-[0.82rem] text-slate-400 mt-0.5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 12.3 | AA ok / AAA non |
| `atelier_view.php:29` | `text-[0.87rem] text-slate-400 leading-relaxed mb-5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.05 | AA ok / AAA non |
| `atelier_view.php:45` | `text-[0.87rem] text-slate-400 leading-relaxed mb-5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.05 | AA ok / AAA non |
| `atelier_view.php:60` | `text-[0.87rem] text-slate-400 leading-relaxed mb-5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.05 | AA ok / AAA non |
| `comptoir_view.php:31` | `text-[0.87rem] text-slate-400 leading-relaxed mb-5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.05 | AA ok / AAA non |
| `comptoir_view.php:46` | `text-[0.87rem] text-slate-400 leading-relaxed mb-5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.05 | AA ok / AAA non |
| `comptoir_view.php:62` | `text-[0.87rem] text-slate-400 leading-relaxed mb-5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.05 | AA ok / AAA non |
| `dashboard_view.php:89` | `text-[0.85rem] text-slate-400` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 12.75 | AA ok / AAA non |
| `dashboard_view.php:110` | `whitespace-nowrap text-slate-300` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 15 | AA ok / AAA non |
| `dashboard_view.php:113` | `whitespace-nowrap tabulaire text-slate-300` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 15 | AA ok / AAA non |
| `dashboard_view.php:116` | `text-slate-200` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 15 | AA ok / AAA non |
| `dashboard_view.php:119` | `.tableau td (aegis, sélecteur descendant)` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 15 | AA ok / AAA non |
| `dashboard_view.php:125` | `.tableau td (aegis, sélecteur descendant)` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 15 | AA ok / AAA non |
| `documents_view.php:149` | `text-[0.92rem] text-slate-400 leading-relaxed` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.8 | AA ok / AAA non |
| `documents_view.php:208` | `text-[0.88rem] text-slate-400` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.2 | AA ok / AAA non |
| `error.php:109` | `text-[0.88rem] text-slate-400 leading-relaxed mb-7` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.2 | AA ok / AAA non |
| `index_view.php:109` | `mt-2 text-[0.9rem] text-slate-400 max-w-2xl` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 13.5 | AA ok / AAA non |
| `index_view.php:158` | `text-[0.86rem] text-slate-400 leading-relaxed mb-5` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 12.9 | AA ok / AAA non |
| `index_view.php:206` | `text-[0.85rem] text-slate-400 mb-4` | `#94a3b8` | `#0f1a2b` | **6.81** | 4.5:1 | 12.75 | AA ok / AAA non |
| `admin_users_view.php:111` | `faible` | `#93a3bd` | `#0f1a2b` | **6.83** | 4.5:1 | 15 | AA ok / AAA non |
| `admin_users_view.php:188` | `faible` | `#93a3bd` | `#0f1a2b` | **6.83** | 4.5:1 | 15 | AA ok / AAA non |
| `admin_users_view.php:193` | `faible` | `#93a3bd` | `#0f1a2b` | **6.83** | 4.5:1 | 15 | AA ok / AAA non |
| `index_view.php:103` | `titre-section mb-2` | `#93a3bd` | `#0f1a2b` | **6.83** | 4.5:1 | 15 | AA ok / AAA non |

### 2.3 Cosmétique — 0 couples

_Aucun._

### 2.4 Décor assumé — hors périmètre (2 couples)

Mesuré quand même, mais sans verdict : ces éléments ne portent aucune information.

| fichier:ligne | élément | couleur | fond | ratio | pourquoi c'est du décor |
|---|---|---|---|---|---|
| `documents_view.php:258` | `absolute w-64 h-64 text-white/[0.02] animate-pulse` | `#141f2f` | `#0f1a2b` | 1.05:1 | icône décorative |
| `documents_view.php:245` | `px-6 py-4 rounded-xl bg-white/5 hover:bg-emerald-5` | `#166f5b` | `#1b2536` | 2.53:1 | filigrane decoratif (icone bouclier, blanche a 2 %) |

---

## 3. Les 10 pires

Classement par ratio croissant, hors décor (le filigrane décoratif de la visionneuse de documents est traité en 2.4 : il est volontairement à 1.05:1).

| # | ratio | fichier:ligne | classe | couleur | fond | taille | rôle | texte mesuré |
|---|---|---|---|---|---|---|---|---|
| 1 | **1.58:1** | `index_view.php:185` | `block text-[0.74rem] text-slate-600 trun` | `#313d50` | `#101b2c` | 11.1 px | information | [dyn] |
| 2 | **1.58:1** | `index_view.php:187` | `w-3.5 h-3.5 text-slate-600 shrink-0` | `#313d50` | `#101b2c` | 15 px | graphisme | (icone) |
| 3 | **1.86:1** | `documents.php:191` | `w-4 h-4 ml-auto opacity-20 transition-tr` | `#3f424f` | `#0f1223` | 15 px | graphisme | (icone) |
| 4 | **2.11:1** | `index_view.php:181` | `w-4 h-4 shrink-0 text-slate-500` | `#425065` | `#101b2c` | 15 px | graphisme | (icone) |
| 5 | **2.19:1** | `index_view.php:175` | `w-4 h-4 text-slate-600 shrink-0` | `#475569` | `#141f2f` | 15 px | graphisme | (icone) |
| 6 | **2.30:1** | `admin_users_view.php:268` | `text-[0.72rem] text-slate-600` | `#475569` | `#0f1a2b` | 10.8 px | information | Compte n° [dyn] |
| 7 | **2.30:1** | `agenda_view.php:183` | `text-center text-[0.75rem] text-slate-60` | `#475569` | `#0f1a2b` | 11.25 px | secondaire | Aucun rendez-vous |
| 8 | **2.30:1** | `dashboard_view.php:88` | `w-6 h-6 text-slate-600 mx-auto mb-3` | `#475569` | `#0f1a2b` | 15 px | graphisme | (icone) |
| 9 | **2.30:1** | `documents_view.php:78` | `w-4 h-4 text-slate-600` | `#475569` | `#0f1a2b` | 15 px | graphisme | (icone) |
| 10 | **2.30:1** | `documents_view.php:207` | `w-6 h-6 text-slate-600 mx-auto mb-3` | `#475569` | `#0f1a2b` | 15 px | graphisme | (icone) |

Ce que dit la mesure, ligne par ligne :

- `index_view.php:185` — **1.58:1** : la couleur `text-slate-600` est trop proche du fond `#101b2c`.
- `index_view.php:187` — **1.58:1** : la couleur `text-slate-600` est trop proche du fond `#101b2c`.
- `documents.php:191` — **1.86:1** : une opacité de groupe (`opacity-20`) atténue à la fois ce texte et son fond.

Le mécanisme dominant de ce bas du tableau est la **superposition de trois causes** : un gris foncé (`slate-500`/`slate-600`), posé sur une surface déjà très sombre, parfois encore atténué par une opacité ou une couleur translucide. Chacun pris seul resterait tolérable ; empilés, ils font tomber le ratio sous 2:1. **La correction ne demande donc aucun changement de structure** : remplacer le gris par `slate-400` (`#94a3b8`) et retirer l'opacité suffit dans tous les cas mesurés ici.

Deux précisions de terrain, qui changent l'ordre des priorités :

1. Les couples n° 1, 2, 4 et 5 (`index_view.php` lignes 181 à 187) vivent dans la **branche visiteur** du portail — celle que voit une personne non identifiée. Ils ne concernent donc **pas** les écrans de travail, mais la première page que découvre l'équipe.
2. Les lignes `documents.php:148` à `214` proviennent d'un **point d'entrée AJAX historique** (`?ajax_list=`, lignes 137-223) qui émet encore son propre HTML. La vue actuelle interroge l'autre endpoint (`?ajax_data=`) : ces 4 couples sont donc mesurés parce qu'ils sont **encore dans le fichier**, mais ils ne s'affichent que si quelqu'un appelle l'ancienne adresse.

---

## 4. Texte trop petit (< 14 px)

WCAG 2.2 n'impose **aucune taille de texte minimale** : ce n'est donc pas un échec de conformité. C'est un risque de lisibilité, réel sur une borne d'atelier et un écran de comptoir. Le tableau distingue ce qui porte une information de ce qui n'est que décor.

| taille effective | occurrences « porteur d'information » | occurrences secondaire / interface / décor | fichiers concernés (les 6 premiers) |
|---|---|---|---|
| 10.80 px (0.72 rem) | 6 | 0 | dashboard_view.php (5), admin_users_view.php (1) |
| 11.10 px (0.74 rem) | 2 | 0 | index_view.php (2) |
| 11.25 px (0.75 rem) | 6 | 1 | documents.php (2), dashboard_view.php (1), documents_view.php (1), admin_users_view.php (1), agenda_view.php (1), profil_view.php (1) |
| 11.70 px (0.78 rem) | 11 | 2 | index_view.php (4), documents.php (2), login.php (1), dashboard_view.php (1), documents_view.php (1), admin_users_view.php (1) |
| 12.00 px (0.8 rem) | 3 | 0 | profil_view.php (2), dashboard_view.php (1) |
| 12.30 px (0.82 rem) | 6 | 1 | admin_users_view.php (5), agenda_view.php (1), profil_view.php (1) |
| 12.45 px (0.83 rem) | 3 | 0 | atelier_view.php (2), comptoir_view.php (1) |
| 12.75 px (0.85 rem) | 21 | 2 | header_ui.php (5), login.php (4), sidebar.php (4), documents_view.php (4), admin_users_view.php (2), error.php (1) |
| 12.90 px (0.86 rem) | 3 | 0 | index_view.php (3) |
| 13.05 px (0.87 rem) | 7 | 0 | comptoir_view.php (3), atelier_view.php (3), login.php (1) |
| 13.12 px (0.875 rem) | 1 | 0 | documents.php (1) |
| 13.20 px (0.88 rem) | 17 | 0 | profil_view.php (12), admin_users_view.php (2), error.php (1), index_view.php (1), documents_view.php (1) |
| 13.50 px (0.9 rem) | 4 | 0 | index_view.php (1), comptoir_view.php (1), atelier_view.php (1), aegis.css (1) |
| 13.80 px (0.92 rem) | 3 | 0 | header_ui.php (1), sidebar.php (1), documents_view.php (1) |

Total : **99 éléments sous 14 px**, dont **93 porteurs d'information** et 6 de décor.

Le plus petit texte **porteur d'information** mesure **10.8 px** (`th`, 1.1 px de moins qu'un libellé courant). Les 3 éléments à 10.5 px sont, eux, tous classés secondaires : le rôle dans la barre latérale, le même rôle dans le bandeau et le libellé « heure locale ». La taille la plus fréquente est **12.8 px** (22 éléments) : c'est le `0.78rem` de `.meta`, `.etiquette` et des libellés de formulaire.

À noter : le commentaire de `aegis.css:118` annonce « plancher à 12 px », mais la valeur écrite à la ligne 119 est `0.78rem`, soit **11.7 px** sur la racine de 15 px. Le commentaire et le code ne disent pas la même chose — c'est un constat, pas une faute.

---

## 5. Ce qui est déjà bon — ne pas casser

214 couples sur 260 sont conformes AA, et 173 atteignent déjà AAA (7:1). Les toucher serait une régression.

| couple couleur / fond | ratio | seuil AA | tailles | où (exemples) |
|---|---|---|---|---|
| `#ffffff` sur `#020617` | 20.17:1 | 4.5:1 | 17.25 à 22.50 px | admin_users_view.php:43, documents_view.php:14, login.php:139 |
| `#ffffff` sur `#09101a` | 19.08:1 | 3.0:1 | 28.12 px | documents_view.php:276 |
| `#ffffff` sur `#0f1a2b` | 17.46:1 | 4.5:1 | 14.25 à 33.75 px | admin_users_view.php:239, agenda_view.php:114, atelier_view.php:28 |
| `#eaf0f9` sur `#080f20` | 16.67:1 | 4.5:1 | 15.00 px | admin_users_view.php:126, admin_users_view.php:199, agenda_view.php:233 |
| `#eaf0f9` sur `#0b0f1f` | 16.63:1 | 4.5:1 | 15.00 px | documents_view.php:19 |
| `#e2e8f0` sur `#020617` | 16.36:1 | 4.5:1 | 13.80 à 15.75 px | error.php:85, error.php:90, header_ui.php:123 |
| `#eaf0f9` sur `#0f1a2b` | 15.24:1 | 4.5:1 | 15.00 à 30.00 px | admin_users_view.php:159, admin_users_view.php:82, agenda_view.php:198 |
| `#e2e8f0` sur `#0b1424` | 14.95:1 | 4.5:1 | 13.80 à 15.75 px | sidebar.php:138, sidebar.php:46 |
| `#e2e8f0` sur `#0f1a2b` | 14.16:1 | 4.5:1 | 11.25 à 15.00 px | admin_users_view.php:249, dashboard_view.php:96, documents_view.php:278 |
| `#eaf0f9` sur `#172232` | 13.98:1 | 4.5:1 | 15.00 px | admin_users_view.php:135, admin_users_view.php:208, agenda_view.php:254 |
| `#e2e8f0` sur `#141f2f` | 13.45:1 | 4.5:1 | 12.90 px | index_view.php:172 |
| `#e2e8f0` sur `#14213a` | 13.02:1 | 4.5:1 | 15.75 px | layout_secure.php:173 |
| `#cbd5e1` sur `#0a0d1e` | 12.99:1 | 4.5:1 | 12.75 px | login.php:156 |
| `#eaf0f9` sur `#1c2941` | 12.70:1 | 4.5:1 | 15.00 px | layout_secure.php:176 |
| `#cbd5e1` sur `#0f1223` | 12.50:1 | 4.5:1 | 12.75 px | header_ui.php:141 |
| `#fcd34d` sur `#0f1a2b` | 12.11:1 | 4.5:1 | 13.20 px | index_view.php:95 |
| `#cfd0d3` sur `#0f1223` | 12.04:1 | 4.5:1 | 13.12 px | documents.php:190 |
| `#6ee7b7` sur `#031822` | 11.90:1 | 4.5:1 | 12.75 px | login.php:165 |
| `#cbd5e1` sur `#0f1a2b` | 11.76:1 | 4.5:1 | 13.20 px | profil_view.php:66, profil_view.php:72 |
| `#6ee7b7` sur `#0f1a2b` | 11.45:1 | 4.5:1 | 13.20 px | admin_users_view.php:62, profil_view.php:25 |

Les points structurels qui tiennent le site :

- Le fond `#020617` est le plus sombre possible sans être noir, et c'est ce qui donne de la marge à tous les textes clairs : le blanc y atteint 20.17:1.
- `slate-400` (`#94a3b8`) est le meilleur gris du projet : **7.25:1** sur une carte, **7.87:1** sur le fond de page. C'est la couleur à utiliser partout où l'on a besoin d'un texte secondaire lisible.
- Les quatre pastilles d'état passent AA malgré leur taille de 10.8 px : `badge-succes` **8.18:1**, `badge-alerte` **9.35:1**, `badge-danger` **5.97:1**, `badge-accent` **7.39:1**.
- `btn-accent` (`#04121c` sur `#38bdf8`) : **8.84:1**. Le seul bouton plein du site, et c'est le plus lisible de tous — le modèle à imiter pour les autres boutons.
- Les liens de navigation `nav-lien` (`#94a3b8` sur le panneau `#0f172a`) : **6.96:1**, soit AAA tout juste. La navigation ne fait pas partie du problème.
- La palette d'état de `aegis.css` (succès/alerte/danger/accent) est unique et bien tenue : aucune vue n'invente une teinte pour un état.

---

## 6. Méthode, réserves et rejeu

### Comment les fonds sont composés

| surface | écriture dans le code | valeur opaque composée |
|---|---|---|
| fond de page | `bg-[#020617]` (layout_secure.php, login.php, error.php) | `#020617` |
| carte | `.carte` / `.glass` → `rgba(15,23,42,0.72)` sur le fond de page | `#0b1225` |
| panneau latéral | `bg-[#0f172a]` (sidebar.php) | `#0f172a` |
| panneau modal | `.modale-panneau` → `rgba(15,23,42,0.92)` sur le fond de page | `#0e1628` |
| champ de saisie | `.champ` → `rgba(2,6,23,0.6)` sur une carte | `#060b1c` |
| notification | `.notif` → `rgba(21,30,46,0.9)` sur le fond de page | `#131c2c` |
| pastille de visite | `bg-white/[0.01]` sur une carte, puis `opacity: 60%` | `#0d1426` |

Les couches `bg-white/5`, `bg-white/10`, `bg-white/[0.01]`, `bg-white/[0.02]`, `bg-white/[0.03]` et les fonds d'état (`bg-red-500/10`, `bg-emerald-500/10`, `bg-amber-500/10`, `bg-cyan-500/10`) sont composées par mélange alpha **sur la surface qui les porte**, jamais directement sur le fond de page : une pastille posée dans une carte se compose sur la carte, pas sur `#020617`.

### Ce que le script ne mesure pas — écrit noir sur blanc

- **Les états `:hover`, `:focus`, `:active`, `:disabled`.** Ils changent de couleur (`hover:bg-white/5`, `btn-danger:hover`) mais ne sont pas des apparences au repos. **ESTIMÉ** : quelques survols de bouton descendent sous 3:1, sans jamais porter une information seule.
- **Les dégradés.** `nav-lien--actif` est un `linear-gradient` : le script retient l'arrêt le plus clair (hypothèse défavorable), donc le ratio réel est compris entre les deux arrêts.
- **Les fonds écrits en PHP** (`style="background: <?= $teinte ?>1a"`, index_view.php) : ce sont des vignettes d'icône à 10 % de teinte sur une carte, l'écart avec la carte est négligeable — mais c'est **ESTIMÉ**, pas mesuré.
- **Le `backdrop-filter: blur`** des cartes `.glass` : il ne change pas la luminance moyenne, donc la composition alpha reste une bonne approximation. Une approximation, pas une certitude.
- **Les images de texte** : il n'y en a aucune. Le site ne contient qu'une seule balise `<img>` (`documents_view.php:267`, la visionneuse) et elle affiche le fichier choisi par l'utilisateur : ce n'est pas du texte.

### Compter juste

Les « occurrences » sont des **balises dans les gabarits**, pas des éléments à l'écran : une boucle `foreach` sur 12 comptes affiche 12 fois le même couple.

**Sur le décompte des fichiers.** La mission annonce « 29 fichiers PHP ». La liste qu'elle donne en contient **23**, et ce sont bien 23 fichiers qui rendent du HTML — dont 15 portent du balisage et 8 sont des contrôleurs purs (`index.php`, `dashboard.php`, `comptoir.php`, `atelier.php`, `agenda.php`, `admin-users.php`, `profil.php`, `logout.php` : 0 occurrence de `class=`, mesuré). Pour retrouver 29 il faut ajouter les **5 fichiers de `includes\`** (`config`, `_auth`, `_bootstrap`, `_functions`, `_init`), qui ne contiennent **aucune balise HTML** (mesuré : 0 `<tag` et 0 `class=`), plus `aegis.css`. Ces 5 includes ne peuvent donc produire aucun couple de contraste et ne sont pas audités en tant que pages.

### Rejeu

```powershell
& 'C:\zeus_dev\zeus_env\python\python.exe' 'C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel\_travail\_audit_20261019\audit_contraste.py'
```

Pour mesurer l'arbre vivant au lieu de la copie figée, ajouter `--source live` à la fin de cette même commande :

```powershell
& 'C:\zeus_dev\zeus_env\python\python.exe' 'C:\...\audit_contraste.py' --source live
```

Le script mesure **toujours les deux états** et n'écrit que celui demandé (par défaut la copie du 21/09) ; `--source live` écrit `contraste_mesures_live.csv` et `contraste_mesures_live.json`.

---

## 7. L'arbre de travail a changé pendant l'audit

Ce n'est pas une impression : les empreintes SHA-256 des fichiers ont été comparées. **9 fichiers PHP sur 23** de l'arbre vivant diffèrent de la copie figée du 21/09 — et `aegis.css` aussi :

| fichier | dernière écriture (vivant) | octets | identique à la copie 21/09 ? |
|---|---|---|---|
| `login.php` | 19/10/2026 18:19:26 | 10635 | **non** |
| `error.php` | 19/10/2026 18:24:31 | 8578 | **non** |
| `layout_secure.php` | 19/10/2026 18:18:57 | 12203 | **non** |
| `header_ui.php` | 19/10/2026 18:24:28 | 7488 | **non** |
| `sidebar.php` | 19/10/2026 18:19:13 | 7433 | **non** |
| `footer_ui.php` | 19/10/2026 18:19:16 | 2369 | **non** |
| `documents_view.php` | 19/10/2026 18:24:22 | 17315 | **non** |
| `admin_users_view.php` | 19/10/2026 18:24:16 | 16012 | **non** |
| `agenda_view.php` | 19/10/2026 18:24:34 | 13205 | **non** |
| `aegis.css` | 19/10/2026 18:24:28 | 39560 | **non** |

Les 14 autres fichiers (dont 5 vues sur 8) sont **identiques** octet pour octet à la copie du 21/09 : la reprise était donc partielle au moment de la mesure, ce qui interdit d'en tirer un « avant / après » propre.

Mesure de l'état vivant au 19/10/2026 18:27:09 :

| mesure | copie 21/09 (référence) | arbre vivant (instant T) |
|---|---|---|
| couples mesurés | 260 | 261 |
| échecs AA | 44 | 60 |
| bloquants | 33 | 38 |
| pire ratio (hors décor) | 1.58:1 (`index_view.php:185`) | 1.60:1 (`index_view.php:185`) |
| éléments < 14 px | 99 | 191 |

**À lire avec précaution** : ces deux colonnes ne comparent pas « avant / après » mais « état de référence » et « état en cours de reprise ». De plus, les rôles (*porteur d'information*, secondaire, décor) sont repérés par `fichier:ligne` **dans la copie du 21/09** : sur l'arbre vivant, dont les lignes ont bougé, la répartition bloquant / gênant est donc **indicative**, alors que le nombre d'échecs AA et le pire ratio restent exacts. Quand la reprise sera terminée, relancer avec `--source live` donnera le chiffre qui compte.

### Empreintes des fichiers mesurés (copie 21/09)

| fichier | source | octets | sha256 (16 premiers) | écrit le |
|---|---|---|---|---|
| `index.php` | index.php | 1867 | `2437514b9cb1d71c` | 21/09/2026 03:16 |
| `login.php` | login.php | 8811 | `d1cbc9ea5abed7c2` | 21/09/2026 03:16 |
| `error.php` | error.php | 7726 | `5d48d7ca18c72055` | 21/09/2026 03:15 |
| `dashboard.php` | dashboard.php | 3256 | `3b169c1a34ef9879` | 21/09/2026 03:15 |
| `comptoir.php` | comptoir.php | 1072 | `c7604c001bc9080f` | 21/09/2026 03:15 |
| `atelier.php` | atelier.php | 990 | `7dd69360e21daf25` | 21/09/2026 03:15 |
| `agenda.php` | agenda.php | 3268 | `3ac5cac61949d5c8` | 21/09/2026 03:15 |
| `documents.php` | documents.php | 10758 | `9e89f6eb25afe354` | 21/09/2026 03:15 |
| `admin-users.php` | admin-users.php | 5380 | `4559f1a2ed3f6f77` | 21/09/2026 03:15 |
| `profil.php` | profil.php | 3035 | `fc83a37c5903df35` | 21/09/2026 03:16 |
| `logout.php` | logout.php | 964 | `11c7a6ac1ad18f0a` | 21/09/2026 03:16 |
| `layout_secure.php` | layout_secure.php | 11155 | `925359012d375a5c` | 21/09/2026 03:22 |
| `header_ui.php` | header_ui.php | 5567 | `886c82595f794eb8` | 21/09/2026 03:22 |
| `sidebar.php` | sidebar.php | 6796 | `50c19dc98dd7bb9d` | 21/09/2026 03:22 |
| `footer_ui.php` | footer_ui.php | 2349 | `c4ecd128d9f5a79d` | 21/09/2026 03:22 |
| `index_view.php` | index_view.php | 13750 | `9a8e757ed4c41291` | 21/09/2026 03:23 |
| `dashboard_view.php` | dashboard_view.php | 8910 | `f5fe7e9c1d025503` | 21/09/2026 03:23 |
| `documents_view.php` | documents_view.php | 16201 | `28feec46d612a2fe` | 21/09/2026 03:23 |
| `admin_users_view.php` | admin_users_view.php | 15644 | `46b93820f7b12dca` | 21/09/2026 03:23 |
| `agenda_view.php` | agenda_view.php | 13145 | `314a2d2cf0c01496` | 21/09/2026 03:23 |
| `comptoir_view.php` | comptoir_view.php | 4141 | `5e7295b42871658d` | 21/09/2026 03:23 |
| `atelier_view.php` | atelier_view.php | 4217 | `1b7082182175d24a` | 21/09/2026 03:23 |
| `profil_view.php` | profil_view.php | 7360 | `12c98253753a55c2` | 21/09/2026 03:23 |

`aegis.css` : `bce29e3e880647c8` (sha256, 16 premiers caractères) — 20272 octets.

