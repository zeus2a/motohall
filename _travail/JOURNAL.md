# JOURNAL DES MODIFICATIONS — MotoHall 2A

> **Le registre de ce qui a été fait, fichier par fichier.**
> `REPRISE.md` raconte *pourquoi* ; ce journal dit *quoi*, *où*, et *si c'est en
> ligne*. Créé le 18/11/2026.
>
> **La règle, et elle vaut pour moi comme pour le prochain :**
> 1. **Toujours** une copie avant de toucher un fichier —
>    `php _travail\outils_chantier\avant_de_modifier.php <fichier>`.
> 2. **Toujours** une ligne ici, avec la date, la raison et le chemin de la copie.
> 3. **Toujours** un point de retour git à la fin d'une intervention (commit + étiquette).
> 4. **Toujours** le contrôle : `php -l` sur le fichier, et le contrôle de
>    structure pour un gabarit. Un fichier modifié sans contrôle n'existe pas.

---

## 1. Les interventions, dans l'ordre

| Date | Objet | Fichiers | Preuve | En ligne |
|---|---|---|---|---|
| 21/09/2026 | Tailwind v4, rangement du projet, moteur hors du web | voir `REPRISE.md` §10 à §13 | 68 402 o identiques aux deux bouts | oui |
| 19/10/2026 | Refonte visuelle, deux thèmes, indexation, faille des documents, 82 corrections de texte | **22 fichiers** (voir §2) | contrastes mesurés sur pixels réels, faille fermée et éprouvée | **oui** |
| 18/11/2026 | Socle de qualité : versionnement, outillage, audits | voir §3 | commit `c81d138` + étiquette `v2026.10.19-reprise` | — |

---

## 2. Intervention du 19/10/2026 — le détail

**Copie avant modification :** `_travail\_backups\avant_refonte_visuelle_20261019\`
(13 fichiers, octet pour octet) — le reste est couvert par le point de retour git.

| Fichier | Ce qui a changé | Contrôle |
|---|---|---|
| `assets/css/aegis.css` | réécriture : 2 thèmes, rôles, 5 marches typographiques, pont de thème | 205/205 accolades, 23 couples × 2 thèmes mesurés, 0 échec |
| `assets/js/theme.js` | **créé** — bascule clair/sombre, anti-flash, mémoire | sonde navigateur : attribut posé avant peinture |
| `robots.txt` | **créé** — 1 793 o, un nom par robot | 200 `text/plain` |
| `.htaccess` | X-Robots-Tag, CSP, HSTS, Permissions-Policy, www→301, `documents/` fermé | 7/7 en-têtes mesurés |
| `fichier.php` | **créé** — service de fichiers avec contrôle de rôle | 14 cas de traversée, matrice des rôles |
| `documents.php` | liens vers `fichier.php`, échappement des noms | `php -l`, liens vérifiés en ligne |
| `index.php`, `login.php`, `error.php`, `profil.php`, `agenda.php`, `admin-users.php` | thème, noindex, textes | `php -l` ×6 |
| `templates/layout_secure.php` | thème, noindex, lien d'évitement, **ordre du head corrigé** | structure 10/10 |
| `templates/header_ui.php` | bascule de thème, titre `<h1>`, aria | structure 10/10 |
| `templates/sidebar.php`, `footer_ui.php` | rôles du thème, libellés alignés | structure 8/8, 2/2 |
| `templates/views/*.php` (8 vues) | étiquettes, clavier, tiny-text, textes | structure 20/20, 13/13, 27/27, 37/37, 27/27, 9/9, 7/7, 18/18 |
| `includes/_functions.php` | `aegis_resoudre_dans()` partagée | 14 cas, 0 échec |
| `includes/_bootstrap.php` | message de panne de base + statut 503 | `php -l` |

**Reste ouvert :** les 15 points de relecture « à arbitrer » (voir `REPRISE.md` §14 quaterdecies).

---

## 3. Intervention du 18/11/2026 — socle de qualité

| Fichier | Ce qui a changé | Contrôle |
|---|---|---|
| `.gitignore` | exclusion des artefacts d'audit (aperçus, captures, profil Chrome, liens) | `git status` propre |
| `_travail/outils_chantier/avant_de_modifier.php` | **créé** — copie datée + manifeste, refus hors projet et fichiers absents | éprouvé sur 3 cas dont 2 refus |
| `_travail/JOURNAL.md` | **créé** — ce registre | — |
| `AGENTS.md` | la règle de travail ajoutée (§6) | — |

**Point de retour :** commit `c81d138`, étiquette `v2026.10.19-reprise`.

**Audits lancés le même jour** (rapports dans `_travail\_audit_20261118\`) :
- audit de sécurité complet, 10 catégories ;
- analyse statique PHPStan 2.2 / PHP-CS-Fixer 3.95 / Rector 2.0.

---

## 4. Comment se servir de ce journal

- **Avant** de toucher un fichier : la commande de copie, puis la ligne dans §3 ou §2.
- **Après** : le contrôle écrit dans la colonne « Contrôle », pas « ça a l'air bon ».
- **À la fin d'une intervention** : `git add -A`, un commit qui dit *ce qui change et
  pourquoi*, une étiquette datée.
- **Pour reprendre le travail** : lire `REPRISE.md` **puis** ce journal. L'un dit le
  pourquoi, l'autre dit l'état.
