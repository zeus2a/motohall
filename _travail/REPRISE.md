# MotoHall 2A — CARNET DU PROJET

> À lire en premier quand on reprend ce projet. Les sections les plus récentes
> sont en haut. Dernière mise à jour : **20 septembre 2026**.

## 1. Où est quoi

| Quoi | Où |
|---|---|
| **Le site (code)** | `C:\zeus_dev\zeus_projets\Sites_Clients\motohall2a` |
| **Le site (en ligne)** | `https://motohall.zeusstack.fr` → `~/public_html/motohall.zeusstack.fr` sur o2switch, compte **capi3439** |
| **Ce dossier de travail** | `C:\zeus_dev\zeus_projets\Sites_Clients\motohall2a_travail` |
| Sauvegardes du code | `motohall2a_travail\_backups\` |
| Outils de chantier sortis du web | `motohall2a_travail\outils_chantier\` (voir son `LISEZ-MOI.md`) |
| Fichiers de mise en ligne | `motohall2a_travail\envoi\` |
| **Le rapport d'audit complet** | `zeus_projets\01_Documentation_Centrale\AUDIT_MOTOHALL2A_20260920.md` |
| Identifiants (base, compte du site) | `C:\zeus_dev\_acces\O2SWITCH.txt`, section **BASE MYSQL MOTOHALL** |

## 2. Ce que c'est

Un **intranet** pour un concessionnaire moto, **réservé au personnel** : chaque page exige une
connexion, et le profil de la personne détermine les espaces qu'elle voit. Le code a été écrit
les **1er et 2 avril 2026**, relu, corrigé et refait le **20 septembre 2026**.

> ⚠️ **Décision de Frédéric, 20/09/2026 : « il ne doit y avoir aucun accès libre, c'est un
> intranet pour les aider à travailler, pas pour les clients, donc des accès et profils. »**
> Autrement dit : **plus aucune page publique** (voir §3 quater).

**Un seul gabarit** (`templates\layout_secure.php` + `sidebar.php` + `header_ui.php` +
`footer_ui.php`). En-tête et pied de page **fixes**, seule la zone de contenu défile.

### Les profils (ce que chacun voit et peut ouvrir)

| Profil | Portail | Comptoir | Atelier | Agenda | Documentation | Tableau de bord | Membres du staff |
|---|---|---|---|---|---|---|---|
| **ADMIN** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **DIRECTEUR** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **VENDEUR** | ✓ | ✓ | — | — | ✓ | — | — |
| **MECANICIEN** | ✓ | — | ✓ | ✓ | ✓ | — | — |

Contrôlé **à trois niveaux** : le portail n'affiche que les univers autorisés, le menu latéral ne
liste que les pages autorisées, et **chaque page vérifie les droits côté serveur**
(`requireRole`) — masquer un lien ne protège rien.

## 3. État au 20/09/2026 : EN LIGNE, corrigé, vérifié

- **4 défauts graves corrigés et mesurés** (scripts de chantier sortis du web, fuite du
  dossier DIRECTION par le cache, mot de passe en clair, page d'erreur) — détail dans le
  rapport, section 4 bis.
- **Mis en ligne** le 20/09/2026 et **vérifié sur le site réel** : 7 pages en 200, zone
  Direction fermée sans mot de passe, connexion OK, création d'un rendez-vous OK, les
  anciens scripts de chantier en **404**, `includes\config.php` en **403**, listage des
  dossiers fermé. Détail dans le rapport, section 7.

## 3 quater. FERMETURE DES ACCÈS ET PORTAIL (20/09/2026, 4e passe)

**Demande de Frédéric :** « il ne doit y avoir aucun accès libre […] donc des accès et profils »,
puis « on ne doit pas être redirigé vers Direction mais vers une belle page portail où comptoir,
atelier et direction peuvent être choisis en fonction de la personne ayant les droits ».

**Ce qui a été fait :**

1. **Plus aucun accès libre.** `PAGE_PUBLIC` a été retiré de `index.php`, `comptoir.php`,
   `atelier.php`, `agenda.php` et `documents.php`. Il ne reste que **`login.php`** (la porte
   d'entrée) et **`error.php`** (page d'erreur, volontairement autonome).
   Vérifié en ligne : **les 9 adresses, y compris la racine `/`, renvoient vers la connexion.**
2. **`documents.php`** : son `$isStaff = !isset($_SESSION['user_id'])` était calculé **avant** le
   démarrage de la session, donc toujours vrai → la page était servie en accès libre avec le
   mauvais gabarit. Corrigé (elle exige la connexion et utilise la coque de l'intranet).
3. **Une seule coque.** `layout_public.php`, `sidebar_public.php` et `layout_portail.php` ne
   servaient plus à rien : rangés dans `motohall2a_travail\gabarits_retires\` (avec un LISEZ-MOI)
   et **supprimés du serveur** — ils étaient encore accessibles par une adresse directe.
4. **`templates\` est maintenant protégé** par le `.htaccess` (comme `includes\`, `tmp\`,
   `backup\`) : ces fichiers sont inclus par PHP, jamais appelés par une adresse.
5. **Nouvelle page portail** (`index.php` → `index_view.php`) : après connexion, on arrive sur un
   accueil qui salue la personne et présente **trois univers** — Comptoir, Atelier, Direction —
   chacun avec ses accès. Les univers non autorisés **ne sont pas affichés**.
6. **Les profils sont respectés partout** : portail, menu latéral, **et côté serveur**
   (`requireRole` sur comptoir, atelier, agenda, tableau de bord, staff). Un profil refusé est
   renvoyé au portail avec le message « Cet espace ne fait pas partie de votre profil. »
7. **Nouvelle page « Mon compte »** (`profil.php`) : chacun voit ses informations et change
   **son** mot de passe (mot de passe actuel exigé, jeton CSRF vérifié, hachage bcrypt).
8. **Connexion** : après identification, on arrive sur le **portail** (et non plus sur le tableau
   de bord) ; le prénom et le nom sont conservés en session pour la salutation.

**Mesuré (base de test, un compte de chaque profil) :**

| Contrôle | Résultat |
|---|---|
| Sans connexion, les 9 adresses | **toutes renvoient vers la connexion** |
| VENDEUR : portail | affiche **Comptoir** seulement |
| VENDEUR : pages ouvertes / refusées | comptoir ✓ · atelier, agenda, tableau de bord, staff **refusés** |
| MECANICIEN : portail | affiche **Atelier** seulement |
| MECANICIEN : pages ouvertes / refusées | atelier, agenda ✓ · comptoir, tableau de bord, staff **refusés** |
| ADMIN : portail | affiche **les trois univers** |
| Menu latéral par profil | ADMIN 9 liens · VENDEUR 5 · MECANICIEN 6 (cohérents avec les droits) |
| Mon compte : mauvais mot de passe actuel | **refusé** |
| Mon compte : changement puis reconnexion | **accepté, et la connexion avec le nouveau mot de passe fonctionne** |

**Mise en page, sur demande de Frédéric** (« le même genre de pied de page et de header UI que
Novaya », « en-tête et pied fixes, contenu qui défile ») : en-tête avec fil d'Ariane, horloge et
**profil de la personne** ; pied de page discret. Correction technique au passage : **`min-h-0`**
sur la colonne principale — sans lui, dans une colonne souple, c'est toute la page qui défile et
l'en-tête part avec le contenu.

⚠️ **La barre latérale n'a PAS été copiée sur Novaya** : Frédéric a tranché (« ton menu était
très beau », « le menu moins chargé sur MotoHall que sur Novaya »). On garde donc le menu sobre
d'origine, **sans effet de lumière**.

**Par Frédéric :**
1. ~~SSL à activer~~ **FAIT le 20/09/2026** : le certificat Let's Encrypt a été émis
   (valide jusqu'au 19/12/2026) et **le site est maintenant en HTTPS** :
   - le `.htaccess` en ligne force `https://` (redirection vérifiée) ;
   - `includes\_bootstrap.php` active le cookie `Secure` **automatiquement** quand la page
     est en HTTPS (il reste désactivé en local, où le site est en `http://127.0.0.1` :
     sinon la connexion serait impossible sur le poste).
   Mesure en ligne : les 7 pages en **HTTPS 200**, connexion réussie, cookie
   `secure + HttpOnly + SameSite=Lax`.
2. Se connecter avec `zeus` (mot de passe dans `_acces\O2SWITCH.txt`) puis **le changer**
   depuis la console Agents.
3. Décider d'ajouter le site à la **sauvegarde quotidienne** (aujourd'hui : Novaya
   seulement — non fait).

**⚠️ Piège rencontré le 20/09/2026 au soir — le DNS de la maison.**
Le routeur de Frédéric (`192.168.9.254`) avait gardé en mémoire un « ce nom n'existe pas »
reçu **avant** la création du sous-domaine, et il le servait encore (l'ordinateur et le
routeur répondaient `NXDOMAIN`). **Rien à réparer sur o2switch** : `ns1` et `ns2.o2switch.net`
ainsi que Google, Cloudflare et Quad9 renvoyaient tous `109.234.166.181`, et Let's Encrypt a
même pu émettre le certificat (donc le nom se résout sur Internet).

Détail qui a mis sur la voie : la box résolvait **`www.motohall.zeusstack.fr`** mais pas le
nom sans `www` — ce n'était donc pas un filtre, mais bien une entrée négative en cache sur
un seul nom.

**Solution appliquée (avec l'accord explicite de Frédéric)** — il ne pouvait pas attendre
l'expiration, qui peut aller jusqu'à 24 h :
- une **ligne ajoutée au fichier `hosts` de Windows** sur le poste :
  `109.234.166.181    motohall.zeusstack.fr` (avec un commentaire explicatif au-dessus) ;
- **sauvegarde du fichier avant modification** :
  `zeus_env\_boost_tmp\sauvegarde_hosts_20260920_220009\hosts` (71 octets, vérifiée identique) ;
- script réutilisable : `zeus_env\_boost_tmp\ajouter_ligne_hosts.ps1`
  (à lancer **en administrateur**, écrit en ASCII — jamais d'UTF-8, un BOM casserait le fichier).
- Mesure après : `motohall.zeusstack.fr` résout, et `https://motohall.zeusstack.fr/` répond
  **HTTP 200** depuis le poste, sans forçage.

⚠️ **À RETIRER** dès que la box aura oublié son « non » (le plus simple : redémarrer la box).
Sinon le poste garderait une adresse figée, et un jour où le serveur changerait d'adresse,
le site semblerait en panne alors qu'il ne le serait pas. La ligne est commentée dans le
fichier, elle est facile à retrouver.

**État vérifié en ligne le 20/09/2026** : 7 pages en 200, zone Direction fermée sans mot de
passe, connexion OK, création d'un rendez-vous OK, anciens scripts de chantier en **404**,
`includes\config.php` en **403**, listage des dossiers fermé, et **fuite du cache fermée**
(rejouée sur le site réel : le visiteur sans mot de passe reçoit le refus).

---

## 3 bis. REFONTE VISUELLE DU 20/09/2026 (soir)

Frédéric trouvait l'habillage « amateur ». Causes identifiées : les vues d'avril empilaient
les effets (halos néon, italiques, majuscules très espacées, textes de 8-9 px, formes
« bulles » de 3 rem d'arrondi) et un vocabulaire de gadget (« Quantum », « Nexus »,
« Node Secure », faux voyant « SQL: CONNECTED »).

**Direction artistique retenue :** celle de Novaya Suites, famille « Zeus Core » — la seule
systémique là-bas. Orbitron (titres) + Rajdhani (texte), fond `#020617`, **une seule couleur
d'accent** `#38bdf8`, bordures **blanches translucides** (`rgba(255,255,255,0.08)`), mouvement
lent sur les états du menu (500 ms), micro-typographie en capitales espacées séparée du texte.
*(Novaya a en réalité trois directions artistiques non fusionnées ; ne pas recopier les
autres familles ni ses incohérences — `accent-600` y vaut tantôt bleu tantôt violet.)*

**Ce qui a été fait :**
- `assets\css\aegis.css` **entièrement réécrit** : variables, composants (`.carte`, `.btn`,
  `.champ`, `.badge`, `.kpi-*`, `.tableau`, `.nav-lien` + `.nav-vignette`), et une **couche de
  normalisation** qui calme les excès des vues d'avril (italiques, `tracking` extrêmes, textes
  < 12 px, halos néon, rayons de 3 rem) **sans réécrire les vues** ;
- coques refaites : `sidebar.php`, `sidebar_public.php`, `header_ui.php`, `footer_ui.php`,
  `layout_secure.php`, `layout_public.php` ;
- **nouveau `templates\layout_portail.php`** : le portail d'accueil est désormais **plein
  écran, sans menu latéral** (`index.php` l'appelle) ;
- `login.php` et `error.php` refaits ; titres nettoyés (« Nexus Agenda » → « Agenda atelier »,
  etc.) ; bouton « Lancer Session » (qui ne faisait rien) devenu « Ouverture prochaine » ;
- **retiré** : les bips de notification (fichiers audio tronqués dans le code), le faux voyant
  « SQL: CONNECTED », les adresses IP internes affichées sur des pages publiques, le
  vocabulaire Quantum/Nexus/Aegis visible ;
- **corrigé au passage** : la fenêtre « Confirmer ? » des pages publiques (`confirmData` n'était
  pas déclaré → elle ne pouvait pas se masquer) et un `</div>` manquant dans l'ancien en-tête.

**⚠️ RÉGRESSION CORRIGÉE DANS LA SOIRÉE — à ne jamais refaire :** la nouvelle page d'erreur
utilisait la constante `SITE_NAME` sans la définir. Comme le `.htaccess` appelle `error.php`
**seule** (aucune configuration chargée), toutes les pages d'erreur sont revenues en **500** au
lieu de 404/403. Corrigé en la rendant **autonome** (elle définit ses constantes si elles
manquent). **`error.php` doit rester autonome.**

**⚠️ OPcache du serveur : `opcache.revalidate_freq = 60`.** Après un envoi de fichier, il faut
**attendre ~60 secondes** avant de conclure qu'un correctif ne fonctionne pas : un test trop
rapide fait croire à un échec (constaté sur ce projet).

**Pages intérieures — refaites dans la foulée :**
- `dashboard.php` + `dashboard_view.php` : **les faux chiffres ont disparu**. Le tableau de bord
  affiche maintenant quatre indicateurs **réellement mesurés** (documents classés par service,
  agents actifs, rendez-vous du jour, rendez-vous de la semaine) et **les cinq prochains
  rendez-vous** (date, heure, titre, client, plaque, état). L'ancien « journal d'activité »
  avec ses heures écrites en dur a été supprimé.
- `admin_users_view.php` : refaite. **Deux corrections de fond en plus du visuel** :
  1. `<?= $success ?>` et `<?= $error ?>` étaient affichés **sans échappement** (le message peut
     contenir un nom saisi ou une erreur de base) → passés par `htmlspecialchars()` ;
  2. les valeurs injectées dans les attributs Alpine passent par **`json_encode()`** (c'est la
     seule méthode correcte ; `addslashes` ne protège pas un attribut HTML).
- `admin-users.php` : **plus d'encodage à l'enregistrement** (`clean()` → `trim()`), ce qui
  supprime le **double encodage** (« D&G » était stocké « D&amp;G » puis affiché « D&amp;amp;G »).
  Mesuré après correction : le nom piégé « Dupont & Fils <test> » est stocké tel quel et affiché
  encodé **une seule fois**, sans injection.
- `agenda_view.php` : en-tête refait (dates de semaine **en français** au lieu de `2026-09-21 =>
  2026-09-27`, boutons sobres, « Nouveau rendez-vous » à la place de « R.D.V »).
- `documents_view.php` : en-tête et fil d'Ariane refaits.
- `comptoir_view.php` / `atelier_view.php` : textes nettoyés, adresses IP internes retirées,
  bouton « Lancer Session » (qui ne faisait rien) devenu « Ouverture prochaine ».

**Vérifié après ces changements** : `php -l` 34/34, les pages en 200 sans erreur PHP, les appels
AJAX de l'agenda et de l'explorateur de documents intacts, création/modification/désactivation
d'un compte testée de bout en bout sur la base de test.

**Deux manques de finition comblés le 20/09/2026 (soirée, 2e passe) :**

1. **Favicon** — il n'y en avait aucun (onglet du navigateur vide). Créé en vectoriel
   (`assets\img\favicon.svg`) : le M et le H sont **dessinés en traits**, pas écrits avec une
   police, car un favicon ne peut pas compter sur une police chargée depuis Internet.
   Décliné en `favicon-32.png`, `favicon-180.png` (écran d'accueil iOS) et `favicon.ico`
   (16/32/48) avec ImageMagick, et déclaré dans **les 5 coques**.

2. **Petit écran (tablette et téléphone)** — il n'y avait **aucune** règle `@media` : le menu
   faisait 280 px fixes et écrasait le contenu, alors que le site s'utilise sur **borne
   tactile**. Désormais, en dessous de 1024 px, le menu devient un **tiroir** qui glisse depuis
   la gauche (`.menu-lateral` + `.menu-lateral--ouvert`), ouvert par un bouton en haut à gauche
   (`.bouton-menu`), avec un voile cliquable pour le refermer. Ajouté aussi :
   `prefers-reduced-motion` (respect du réglage « réduire les animations » du système) et un
   centrage du contenu au-delà de 1600 px.

**Vérifié en ligne** : les 4 fichiers d'icône répondent 200 avec le bon type MIME, le favicon est
déclaré sur toutes les pages, le tiroir et ses règles sont bien servis.

---

## 3 ter. BUG DE PRODUCTION TROUVÉ ET CORRIGÉ (20/09/2026, 3e passe)

**La page Documentation ne naviguait pas en ligne.** Les cartes de dossiers envoyaient le nom en
MAJUSCULES (`ATELIER`, `MAGASIN`, `DIRECTION`) alors que les dossiers s'appellent `atelier`,
`magasin`, `direction` sur le serveur. Sur **Windows la casse ne compte pas** — donc cela
fonctionnait parfaitement sur le poste de Frédéric ; sur le **serveur Linux**, `realpath()`
échouait et la page répondait **`400 Bad Request`**.

Mesure en ligne, avant correction :

| Nom envoyé | Résultat |
|---|---|
| `ATELIER`, `MAGASIN`, `DIRECTION` | **400 — refusé** |
| `atelier`, `magasin`, `direction` | 200 — fonctionne |

**C'est le piège classique du développement sur Windows pour un serveur Linux** : le site a
été écrit et testé sur le poste, où cette erreur est **invisible**.

**Ce qui a été corrigé, aux trois endroits concernés :**
1. `documents_view.php` : le chemin envoyé est le **vrai nom du dossier** (minuscules) ; le
   libellé affiché reste en français (« Atelier », « Magasin », « Direction ») ;
2. `atelier_view.php` : le lien vers la librairie envoyait `?dir=ATELIER` → devenu `?dir=atelier` ;
3. `documents.php` : nouvelle fonction **`aegis_resoudre_dossier()`** qui résout le dossier
   **en tolérant la casse**, segment par segment — pour que ce piège ne revienne pas si
   quelqu'un écrit un lien avec la mauvaise orthographe.

**Sécurité vérifiée après la correction** (c'était le risque : une résolution plus souple
pouvait ouvrir une faille) :
`..`, `../`, `../../`, `../includes`, `..%2F..%2Fincludes`, `atelier/../../includes`, `/etc`,
`NIMPORTEQUOI` → **tous refusés en 400**. Et le verrou du dossier Direction tient pour un
visiteur sans mot de passe, **dans les trois casses** (403).

**Mesuré en ligne après correction** : `ATELIER`, `atelier`, `MAGASIN`, `DIRECTION`, `direction`
→ **200 avec contenu** ; `../includes` et `NIMPORTEQUOI` → **400 refusé**.

**Corrections d'interface qui se voient (aucune n'empêche le site de tourner) :**
- `documents.php:9` : `$isStaff = !isset($_SESSION['user_id'])` est **inversé** → la page
  Documents sert toujours le gabarit public, même pour un administrateur.
- `templates\header_ui.php` : il manque un `</div>` (13 ouvertes / 12 fermées).
- `templates\layout_public.php` : `confirmData` n'est pas déclaré dans `x-data` → la fenêtre
  « Confirmer ? » ne peut pas se masquer.
- `includes\_init.php:43` : `file_put_key_contents` n'existe pas (c'est `file_put_contents`).
- `agenda.php` et `login.php` : aucun jeton CSRF (la console admin en a un, elle).
- `dashboard_view.php` et `footer_ui.php` : chiffres inventés (« + 12 % », « 92 % ») et faux
  voyant « SQL: CONNECTED » écrit en dur.
- `dashboard_view.php:17` : icône `motorcycle` inexistante dans Lucide 1.47 → `motorbike`.

## 3 quinquies. SAUVEGARDE QUOTIDIENNE (20/09/2026, 5e passe)

**Le problème** : la sauvegarde de nuit ne prenait que **Novaya**. MotoHall n'était couvert
nulle part — ni sa base, ni les documents déposés sur le site.

**L'objection de Frédéric, et elle était juste** : « si la sauvegarde fait un backup de
zeus_dev, on a tout ce qu'il y a à l'intérieur ». Vrai pour le **code** (l'étape 4 emporte tout
`zeus_dev`, donc le dossier du projet). Mais **deux choses n'y sont pas** :
1. **la base de données** — elle vit sur le serveur o2switch (comptes, rendez-vous, clients) ;
2. **le dossier `documents/` de production** — les manuels et procédures déposés depuis le
   site ; la copie locale ne contient que 4 fichiers d'exemple.

**Ce qui a été fait, sans rien mélanger** (consigne du même soir : « ne pas mélanger les
projets ») — MotoHall est déclaré comme **projet distinct**, avec ses propres archives :

| Fichier modifié | Ajout |
|---|---|
| `zeus_env\outils\ssh_o2switch.py` | le nom `motohall` (même hébergement que Novaya, compte 1, mais projet à part) |
| `sauvegarder_base_o2switch.py` | sa base, lue dans `public_html/motohall.zeusstack.fr/includes/config.php` |
| `sauvegarder_fichiers_o2switch.py` | son dossier `public_html/motohall.zeusstack.fr` — et les **fichiers témoins** du contrôle sont maintenant **propres à chaque projet** |
| `sauvegarder_tout.py` | une étape **2b** « LE PROJET MOTOHALL (à part, sans mélange) », et le journal inscrit `base_motohall=ok fichiers_motohall=ok` |

**⚠️ FAUX ROUGE CORRIGÉ AU PASSAGE** : le contrôle final de l'archive cherchait les fichiers
témoins de **Novaya** (`novaya-suites/index.php`…) même dans la sauvegarde de MotoHall, et
criait donc « il manque des fichiers qui comptent » alors que l'archive était complète.

**Mesuré le 20/09/2026 à 22 h 48** :
- base MotoHall : **2 tables, 108 lignes de SQL, intégrité vérifiée**, archive
  `capi3439_motohall_20260920_224723.sql.gz` (1,2 Ko) ;
- fichiers MotoHall : **39 fichiers, 67 entrées, intégrité vérifiée**, archive
  `fichiers_motohall_20260920_224746.tar.gz` ;
- passage complet : **15 secondes**, verdict `OK`, journal portant
  `base_motohall=ok fichiers_motohall=ok` ;
- **Novaya n'a pas été touché** : sa sauvegarde repasse au vert (1 169 fichiers).

Les archives MotoHall partent sur le NAS automatiquement : la copie prend tout
`zeus_backup\bases` et `\fichiers`, sans filtre par nom.

## 4. Ce qui reste à faire

**Par Frédéric :**
1. **Se connecter** avec `zeus` (mot de passe dans `_acces\O2SWITCH.txt`, section BASE MYSQL
   MOTOHALL) et **le changer** depuis « Mon compte ».
2. **Créer les comptes du personnel** depuis « Membres du staff », en choisissant le bon profil :
   `VENDEUR` pour le magasin, `MECANICIEN` pour l'atelier, `DIRECTEUR` pour la direction.
   C'est ce choix qui ouvre les bons espaces à chacun.
3. Décider d'ajouter le site à la **sauvegarde quotidienne** (aujourd'hui : Novaya seulement).
4. La ligne ajoutée au fichier `hosts` de Windows (voir § DNS plus haut) est à retirer quand la
   box aura oublié son cache.

**Points techniques encore ouverts (aucun n'empêche le site de tourner) :**
- `includes\_init.php:43` : `file_put_key_contents` n'existe pas → le journal système ne s'écrit
  jamais. **C'est la seule erreur que PHPStan signale sur tout le projet.**
- **Aucun jeton CSRF** sur l'agenda (création d'un rendez-vous) ni sur la page de connexion.
- Les vues gardent des classes d'avril (`font-black`, `italic`, `text-[9px]`, halos néon) que la
  feuille de style **neutralise** : le rendu est bon, mais le code source gagnerait à être
  nettoyé (travail mécanique, sans gain visuel).
- L'API (`api\`, 5 fichiers) est **morte et absente du serveur** : à retirer du projet un jour.

## 5. Les trois pièges de ce projet (ils ont déjà coûté trois fausses alertes)

### 5 ter. ⚠️ NE JAMAIS METTRE UN HACHAGE DANS UNE COMMANDE SHELL (20/09/2026)

Un hachage bcrypt contient des « **$** » (`$2y$12$…`). En l'insérant dans une commande
entre guillemets doubles, **bash interprète `$2` et `$12` comme des variables** et les
remplace par du vide : la base reçoit un hachage **cassé**, et **plus personne ne peut se
connecter**. C'est arrivé en changeant le mot de passe du compte `zeus` en production.

**La bonne méthode** (celle qui a servi à réparer) : envoyer le hachage par l'**entrée
standard** et l'utiliser via une variable shell — le contenu d'une variable n'est jamais
réévalué :

```bash
read -r MDP
read -r HASH
MYSQL_PWD="$MDP" mysql -u … -e "UPDATE users SET password='$HASH' WHERE …"
```

Et **toujours vérifier par une vraie connexion** au site après un changement de mot de
passe : c'est cette vérification qui a révélé la casse (le script annonçait « mise à jour
faite », la connexion répondait « False »).

1. **Le serveur PHP de développement renvoie la page d'accueil en HTTP 200 pour un fichier
   absent.** Pour savoir si un script répond encore, il faut lire le **contenu**, pas le
   code HTTP.
2. **Un cookie de session est posé même pour un visiteur anonyme** (`session_start()` dans
   `_bootstrap.php`). « Cookie présent » ne prouve pas « connecté » : la seule preuve est
   d'ouvrir une page **réservée** (`dashboard.php`).

## 6. Comment refaire les mesures

- Scripts de mesure (Python) : `zeus_env\_boost_tmp\audit_motohall_20260920\`
  (audit local, vérifications après correction, vérification en ligne).
- Scripts de mise en ligne : création de la base, création des tables et du compte admin,
  envoi par SFTP (empreintes SHA-256 vérifiées).
- **Base de test locale** : base `motohall2a` sur le port **3399**
  (`zeus_env\outils\base_de_test.ps1 demarrer`), copie d'essai du site dans
  `zeus_env\_boost_tmp\motohall_essai` (son `config.php` pointe sur le port 3399).

## 7. Le mot de passe de la base en ligne

Il est dans `_acces\O2SWITCH.txt` **et** dans `includes\config.php` sur le serveur.
Ce fichier est protégé par le `.htaccess` (mesuré : **403**). Il ne doit **jamais** partir
sur un service en ligne, ni dans un dépôt public.

## 8. Le rangement de `~/zeusstack/` (20/09/2026, 23 h) — et ce qu'il a cassé

**Ce que Frédéric a fait**, de sa main : il a rangé les fichiers d'audit de Novaya dans un
sous-dossier. Mesuré sur le serveur (`ls -la`, 22 h 51) :

| Emplacement | Contenu mesuré |
|---|---|
| `~/zeusstack/novaya/` | `zeusstack_diag.php` (206 519 o), `zeusstack_etat_novaya.json` (224 318 o), `zeusstack_suivi_novaya.txt` (52 o) |
| `~/zeusstack/motohall/` | `includes/`, `templates/`, `tmp/` — **0 fichier : ce sont des dossiers vides** |
| `~/zeusstack/zeusstack_diag.php` | **n'existe plus** (c'est le but) |

C'est **le bon rangement** : `~/zeusstack/` est maintenant organisé **par projet**, et la
sauvegarde de Novaya exclut `zeusstack/motohall` — les deux projets ne se mélangent jamais.

### 8 bis. Deux casses réelles, réparées le même soir

Le déplacement a cassé deux choses — **il faut les chercher à chaque fois qu'on déplace un
fichier**, c'est la leçon :

1. **La fiche de Novaya était HORS SERVICE.** `zeusstack_site_novaya.php` cherchait le moteur
   à l'ancien emplacement et répondait **HTTP 500 — « MOTEUR INTROUVABLE »** (mesuré).
   Corrigé : le nouveau chemin est essayé **en premier**, l'ancien et le local restent en
   secours (3 emplacements au lieu de 2). Réparé et mesuré :
   **500 → 401** avec le titre `ZeusStack Diag — NOVAYA SUITES` — c'est **le moteur qui
   répond**, donc la fiche l'a bien retrouvé. Ancien fichier gardé sur le serveur :
   `zeusstack_site_novaya_AVANT_20260920_225736.php`.
2. **La sauvegarde de Novaya aurait rendu un FAUX ROUGE à 3 h du matin.** Le témoin
   `zeusstack/zeusstack_diag.php` de `sauvegarder_fichiers_o2switch.py` pointait l'ancien
   chemin : l'archive aurait été complète et le rapport aurait annoncé « il manque des
   fichiers qui comptent ». Corrigé en `zeusstack/novaya/zeusstack_diag.php`, et vérifié sur
   le serveur : **les 3 témoins existent** (mesuré).

### 8 ter. LE DÉMÉNAGEMENT DU MOTEUR DE MOTOHALL — **FAIT** le 21/09/2026 à 02 h 45

**Ce que Frédéric a demandé** (21/09/2026, 02 h 30) : « A — terminer : envoyer le moteur dans
`~/zeusstack/motohall/`, envoyer `amorce.php` + les 10 pages, retirer le moteur du site. »

**L'état, avant et après — mesuré, pas raconté :**

| | Avant | Après |
|---|---|---|
| Les 10 pages demandent | `includes/_auth.php` | `amorce.php` (11 fichiers envoyés, empreinte SHA-256 relue après chaque envoi) |
| Le moteur | dans le site (`includes/` 5 + `templates/` 12) | **`~/zeusstack/motohall/`** — 17 fichiers |
| Le cache des documents | `<site>/tmp/cache/` | **`~/zeusstack/motohall/tmp/cache/`** |
| `~/zeusstack/motohall/` | 3 dossiers vides | 18 fichiers (17 + 1 cache écrit par le site) |

**LA DÉCOUVERTE QUI A RENDU L'OPÉRATION SIMPLE :** `config.php` calcule
`ROOT_PATH = realpath(__DIR__ . '/../') . '/'`, c'est-à-dire **le dossier parent de
`includes/`**. En déplaçant `includes/`, `templates/` **et** `tmp/` **ensemble**, cette ligne
reste juste toute seule : `ROOT_PATH` devient `~/zeusstack/motohall/`, les gabarits y sont, le
cache aussi. **Aucune page à modifier, et le `config.php` du serveur (celui qui porte le mot de
passe de la base) n'a pas été touché.** C'est cette vérification — lire les 5 fichiers du moteur
avant d'y toucher — qui a évité une panne : sans elle, on aurait envoyé le `config.php` local
(base d'essai, `root` sans mot de passe) **par-dessus celui de production**.

**L'ordre suivi, pour qu'il n'y ait aucune fenêtre de panne :** le moteur a été **copié**
(`cp -a`, puis `diff -r` : « includes : identique », « templates : identique ») **avant** que
quoi que ce soit ne soit retiré ; les pages ont été envoyées ensuite (le site tournait alors
toujours sur sa propre copie, que `amorce.php` trouve en premier) ; le retrait n'a eu lieu
qu'après. Et ce n'est **pas une suppression** : `mv` vers
`~/backups/moteur_retire_20260921_024004/` (17 fichiers) — retour arrière en une commande.

**Les preuves, dans l'ordre où elles ont été obtenues :**

1. **Connexion réelle au site**, mot de passe lu dans `_acces\O2SWITCH.txt` (jamais affiché),
   puis les 8 pages ouvertes une par une : **200** partout, titres conformes (Portail, Tableau de
   bord, Documentation, Agenda atelier, Comptoir, Atelier, Membres du staff, Mon compte).
2. **Après le retrait, la même mesure a été refaite et donne exactement la même chose** — c'est
   ça, la preuve que le moteur hors du web est bien celui qui sert.
3. **Mesuré par PHP lui-même** : `is_file()` sur l'ancien emplacement → « n'existe pas » ;
   sur `~/zeusstack/motohall/includes/_auth.php` → « EXISTE ». L'amorce ne *peut donc* utiliser
   que le second.
4. **`ROOT_PATH` valait bien `~/zeusstack/motohall/`**, gabarits trouvés, cache présent,
   écriture possible.
5. **Le cache s'écrit vraiment là où il faut** : une vraie requête connectée
   (`documents.php?ajax_list=`) a déposé `doc_75a57d44fcaf76af….html`, **2 257 octets = exactement
   la taille du fragment servi**, dans le dossier du moteur — et rien dans le site.
6. **La page d'erreur tient toujours debout** : une adresse qui n'existe pas rend
   **HTTP 404** avec `<title>404 — Page introuvable | MotoHall 2A</title>`. C'est le contrôle
   important : `error.php` est le seul fichier qui ne passe **pas** par le moteur, et c'est lui
   qui répond quand tout est cassé.
7. **Aucune ligne « moteur introuvable »** dans `error_log` (la dernière erreur du journal date
   de 20 h 07, c'est l'ancien bug de `error.php`, déjà corrigé).

**Deux pièges rencontrés, et ce qu'ils ont coûté :**

- **Le cache PHP du serveur garde 60 s** : juste après l'envoi, la page de diagnostic de Novaya
  servait encore l'ancienne version compilée. Il faut **attendre**, pas conclure. Même chose pour
  les chemins (`realpath_cache_ttl` = 120 s) : après le retrait, 155 s d'attente avant de mesurer.
- **Les fichiers de secours laissés par l'outil d'envoi** (`*_AVANT_*.php`, 10 fichiers) restaient
  dans le dossier web, donc **téléchargeables et exécutables**. Ils ont été retirés après avoir
  vérifié que les mêmes versions existent en local (`_backups\avant_demenagement_moteur_20260921_023826`)
  et dans l'archive serveur.

### 8 quater. La leçon, en une phrase — et elle s'est vérifiée DEUX FOIS le même soir

**Déplacer un fichier ne casse pas seulement ceux qui le lisent : ça casse aussi les
CONTRÔLES qui vérifient sa présence.** Le même déplacement a cassé une page (la fiche de Novaya)
et un témoin de sauvegarde — et les deux étaient silencieux : la page ne se plaint que si on
l'ouvre, le témoin ne parle qu'à 3 h du matin.

**Et le piège s'est retendu à l'identique une heure plus tard, pour MotoHall** : ses témoins
cherchaient `motohall.zeusstack.fr/includes/config.php` et `…/templates/layout_secure.php`,
qui venaient justement de partir du site. Corrigés **avant** la sauvegarde de 3 h, puis la
sauvegarde a été **lancée pour de vrai** : code de sortie **0**,
`fichiers_motohall_20260921_024353.tar.gz`, 70 entrées, « les fichiers clés sont bien là
(index.php, .htaccess, le moteur) ». Un faux rouge de moins.

Recherche faite avec `zeus_env\_boost_tmp\audit_motohall_20260920\motohall_qui_lit_audit_novaya.py`.

### 8 quinquies. ⚠️ L'OUTIL DE RECHERCHE REND « AUCUN RÉSULTAT » SUR CERTAINS DOSSIERS

**Mesuré le 21/09/2026 :** chercher `ROOT_PATH` dans le **fichier** `includes\config.php` trouve
la ligne 17. Chercher **le même motif** dans le **dossier** `includes\` rend
« aucun résultat ». Idem pour `templates\` et `assets\` — alors que ces dossiers contiennent
évidemment des correspondances.

**Conséquence, et c'est grave :** la conclusion « personne n'utilise `ROOT_PATH` » aurait été
**fausse**, et c'est exactement sur cette conclusion qu'on décide de déplacer ou de supprimer un
fichier. La carte complète des dépendances a donc été refaite avec un balayage Python qui lit
**chaque fichier un par un** (`zeus_env\_boost_tmp\qui_utilise_quoi_motohall.py`), et c'est
**celui-là** qui a servi à décider.

**La cause exacte n'est pas mesurée** (le nom du dossier ? une règle d'exclusion ?) et ce n'est
pas utile de la connaître : la règle pratique est de **ne jamais conclure « rien trouvé » sur une
recherche par dossier dans ce projet** — on passe un fichier précis, ou on balaie en Python.

## 9. L'ÉQUIPE ET LES PROFILS (21/09/2026, 03 h 40)

### 9 bis. L'organisation réelle, telle que Frédéric l'a décrite

| Où | Qui | Profil | Ce qu'il voit |
|---|---|---|---|
| — | **Frédéric** | `ADMIN` | tout, **+ la gestion des comptes** |
| Bureau | **Anthony Natali** — DG / RH | `DIRECTEUR` | tout, sauf les comptes |
| Bureau | **Krystina** — comptabilité / administratif | `DIRECTEUR` | comme Anthony (choix de Frédéric : « même accès que lui ») |
| **Rez-de-chaussée** | les vendeurs | `VENDEUR` | portail, comptoir, documents |
| **1er étage** | les mécaniciens | `MECANICIEN` | portail, agenda, atelier, documents |

⚠️ **« rch » = rez-de-chaussée** (précision de Frédéric) : en bas la vente — accessoires, casques,
vêtements, motos ; au 1er l'atelier — réparation, entretien moto et scooter. C'est **exactement**
le découpage que le site faisait déjà : aucun code à changer.

### 9 ter. Ce qui a été décidé, et pourquoi rien n'a bougé dans le code

Frédéric a choisi : (1) **Krystina prend le profil `DIRECTION`**, pas un profil « Comptabilité »
séparé ; (2) **lui seul gère les comptes**. Les deux étaient **déjà** le comportement du site
(vérifié : `admin-users.php` réserve la page à `ADMIN` dès la ligne 11). Les libellés
« Vendeur — magasin » et « Mécanicien — atelier » sont **conservés** — il les trouve clairs.

**Ce qui a été mesuré avant de décider** : les listes de profils sont écrites **à la main à
14 endroits** (7 gardes de page, 7 lignes de menu, 5 tables de libellés — 39 lignes au total).
C'est cette dispersion qui a produit un **faux rouge** pendant le relevé : un contrôle maison
cherchait `requireRole([...])` et n'a pas vu la garde de `admin-users.php`, écrite
`if (strtoupper(getRole()) !== 'ADMIN')`. **La page a été annoncée ouverte à tout le monde alors
qu'elle était fermée.** Le fichier a été lu, et le faux rouge corrigé avant d'être rapporté.

**Reste en réserve, non fait** : centraliser ces listes dans un seul fichier `_roles.php`
(groupes Direction / Atelier / Comptoir + libellés), pour qu'ajouter un profil soit **une ligne
au lieu de quatorze**. À faire le jour où un 5e profil apparaît vraiment.

### 9 quater. Les comptes créés, et comment ils ont été vérifiés

| id | identifiant | Nom | Profil | Vérifié |
|---|---|---|---|---|
| 1 | `zeus` | Frédéric | `ADMIN` | connexion + 7 pages |
| 2 | `anatali` | Anthony Natali | `DIRECTEUR` | connexion + 7 pages |
| 3 | `krystina` | Krystina Natali | `DIRECTEUR` | connexion + 7 pages |
| 4 | `vendeur1` | compte d'attente | `VENDEUR` | connexion + 7 pages |

**Créés par la page « Membres du staff » elle-même** (POST + jeton CSRF de la page), pas par une
commande SQL. Trois raisons, et elles comptent : c'est le chemin que Frédéric aurait pris à la
main, donc c'est **lui** qu'on teste ; c'est le site qui hache le mot de passe
(`password_hash`), donc **aucun hachage n'est écrit à la main** — c'est précisément l'accident
du 20/09/2026 (les `$` d'un bcrypt avalés par le shell) qu'on ne peut plus reproduire ; et la
protection CSRF reste en place.

**Contrôle des droits : 28 mesures, 0 écart.** Une vraie connexion par compte, puis les 7 pages
ouvertes pour chacun, comparées à une matrice écrite d'avance :

| compte | portail | tableau de bord | comptoir | atelier | agenda | documents | staff |
|---|---|---|---|---|---|---|---|
| `zeus` ADMIN | 200 | 200 | 200 | 200 | 200 | 200 | 200 |
| `anatali` DIRECTEUR | 200 | 200 | 200 | 200 | 200 | 200 | **refusé** |
| `krystina` DIRECTEUR | 200 | 200 | 200 | 200 | 200 | 200 | **refusé** |
| `vendeur1` VENDEUR | 200 | **refusé** | 200 | **refusé** | **refusé** | 200 | **refusé** |

Les mots de passe sont bien hachés en base (vérifié : **60 caractères, début `$2y$`** pour les
quatre comptes — le mot de passe lui-même n'est jamais affiché).

### 9 quinquies. ⚠️ ENCORE UN FAUX ROUGE, ET C'EST INSTRUCTIF

Le script de création annonçait `?? reponse inattendue` pour les **trois** comptes. **Ils étaient
pourtant bien créés.** Ma détection cherchait le texte « activé sur le Node » dans la réponse et
les identifiants dans un motif `name="username" value="…"` — or ce motif attrape aussi le
**gabarit du formulaire d'édition** (`value="u.username"`, une expression Alpine non évaluée).

**Ce qui a tranché : la base de données.** Un `SELECT` sur `users` a montré 4 lignes, avec les
bons profils. **Troisième faux rouge de la soirée produit par un contrôle écrit à la main** —
après la garde de `admin-users.php` et le témoin de sauvegarde. La règle qui en sort, et elle
vaut pour toutes les prochaines fois : **quand un contrôle maison dit « échec », on va lire à la
source** (la base, le fichier, le serveur) **avant de le croire et avant de le rapporter.**

## 10. PLUS AUCUN CDN : FEUILLE COMPILÉE ET BIBLIOTHÈQUES DANS LE SITE (21/09/2026, 03 h 05)

### 10 bis. Ce qui a changé, et les chiffres

| | Avant | Après |
|---|---|---|
| Les styles | `cdn.tailwindcss.com` — **407 279 octets** de programme téléchargé et exécuté **à chaque page**, dans le navigateur | `assets/css/tailwind.css` — **24 204 octets**, fabriqués une fois pour toutes |
| Alpine | `unpkg.com/alpinejs@3.x.x` — « la dernière version » | `assets/js/alpine.min.js`, **figé à 3.17.3** |
| Lucide | `unpkg.com/lucide@latest` — idem | `assets/js/lucide.min.js`, **figé à 1.47.0** |
| Dépendance extérieure | 3 (Tailwind, unpkg ×2) | **0** (restent les polices Google, qui ne peuvent pas changer toutes seules) |

⚠️ **Les deux versions figées sont EXACTEMENT celles que le CDN servait ce jour-là** (mesuré :
`alpinejs@3.x.x` → 3.17.3, `lucide@latest` → 1.47.0). C'est ce qui rend l'opération sans effet
visible : on n'a rien mis à jour, on a **arrêté de dépendre**.

### 10 ter. ⚠️ LE PIÈGE DE LA VERSION DE TAILWIND (et pourquoi 3.4.17)

`zeus_env\node` a **Tailwind 4.3.3** installé. Le site tournait sur **3.4.17** (c'est ce que sert
le Play CDN, mesuré). **Compiler avec la 4 aurait changé l'apparence du site sans qu'on l'ait
demandé** — la v4 modifie des réglages par défaut (couleur des bordures, noms des ombres,
contours). Deux chantiers ont donc eu lieu :

1. **Un atelier de compilation DANS le projet** :
   `motohall2a_travail\outils_chantier\tailwind\` — avec son `package.json` qui épingle
   `"tailwindcss": "3.4.17"` (**sans accent circonflexe**), son `tailwind.config.js` (copie
   **exacte** du bloc qui était écrit dans `layout_secure.php`) et son `tailwind.src.css`.
   Pour refaire la feuille : `npm run build` dans ce dossier. **435 ms.**
2. **Le cache npm est dans `zeus_env\node\.npm-cache`** : npm voulait écrire dans le profil
   Windows, hors de `zeus_dev`, et le bac à sable l'a refusé. C'est mieux ainsi — un cache n'a
   rien à faire dans le profil de Frédéric. **Ne jamais lancer `npm install` dans
   `zeus_env\node` lui-même** (leçon des 447 paquets du 20/09/2026).

### 10 quater. ⚠️ LES DEUX PIÈGES RENCONTRÉS, ET ILS ÉTAIENT RÉELS

**1. `error.php` et `login.php` chargeaient AUSSI le CDN.** Le contrôle « plus aucune adresse
extérieure » a trouvé les deux — dont **la page de connexion, la première que voit l'équipe**.
Sans ce contrôle, on aurait changé la coquille et laissé la porte d'entrée sur le CDN.

**2. `error.php` utilisait des chemins RELATIFS (`assets/css/…`).** Cette page répond à
**n'importe quelle adresse** (c'est le `ErrorDocument` du `.htaccess`) : sur
`/un/dossier/profond/inexistant`, un chemin relatif cherche `/un/dossier/profond/assets/…` —
donc rien. **Avec le CDN ça ne se voyait pas**, puisque l'adresse était complète. Depuis que la
feuille est dans le site, il fallait calculer le chemin :
`$base = dirname($_SERVER['SCRIPT_NAME'])` → « / » en production, « /motohall2a » sur le poste.
**Vérifié en vrai** : `https://motohall.zeusstack.fr/un/dossier/tres/profond/inexistant` rend la
404 maison **avec ses deux feuilles et son icône**, en adresses absolues.

### 10 quinquies. Les preuves, et le contrôle qui compte

Le contrôle décisif est la **couverture des classes** : le Play CDN ne pouvait rien oublier
(il lisait la page), une feuille compilée ne contient que ce qu'elle a trouvé écrit dans les
fichiers. Une classe oubliée ne fait **aucune erreur** : la page s'affiche, simplement sans le
style. Deux mesures, avant et après l'envoi :

- **sur les fichiers du projet** : 391 classes utilisées → **331 dans tailwind.css, 41 dans
  aegis.css, 0 orpheline** (19 jetons classés « sans objet », chacun avec sa raison écrite) ;
- **sur le HTML réellement servi** : 337 classes → **0 introuvable**.

Et le reste, mesuré : aucun CDN dans le HTML des 3 pages testées (portail, connexion, 404) ;
les 4 fichiers servis (200, bon type, bonnes tailles) ; **connexion réelle + les 8 pages** en
200 avec leurs titres ; page d'erreur debout en adresse profonde ; 7 fichiers de secours
retirés après vérification que les anciennes versions existent ailleurs.

### 10 sexies. LA RÈGLE DES VERSIONS, POUR TOUS LES PROJETS

**La version appartient au PROJET, pas à la machine.** C'est la réponse à la question de
Frédéric (« quelle version prendre pour faire évoluer le site et les autres projets ? ») :

| Projet | Version | Où c'est écrit |
|---|---|---|
| **MotoHall 2A** | **3.4.17** (figée) | `motohall2a_travail\outils_chantier\tailwind\package.json` |
| Nouveaux projets | **4.x** (la branche vivante) | le `package.json` du nouveau projet |
| `zeus_env\node` | 4.3.3 — **la plus récente** | `zeus_env\node\package.json` |

**Pourquoi on ne met PAS tout le monde à la même version :** si on montait MotoHall en v4
aujourd'hui, il faudrait revérifier **chaque page à l'œil** (la v4 change des réglages par
défaut) pour **zéro gain** — le site fonctionne. Et si un jour on montait la version de la
machine pour un autre projet, on changerait MotoHall **sans le vouloir**. Une version figée par
projet, c'est ce qui permet d'en faire évoluer un sans toucher aux autres.

**Ce qui rend une version « stable et fiable », en pratique — les quatre gestes, et ils sont
tous faits pour MotoHall :**
1. un **numéro exact** écrit dans le projet (jamais `^`, jamais `latest`) ;
2. le **fichier fabriqué rangé avec le projet** (`assets/css/tailwind.css`) ;
3. **l'atelier qui permet de le refabriquer** (`outils_chantier\tailwind\` + `npm run build`) ;
4. **la raison écrite** de ce choix (ce paragraphe) — sans quoi la prochaine session
   « mettra à jour » et cassera l'apparence.

**Novaya Suites et Corse Music Events sont dans la même situation que MotoHall avant ce
soir** : Play CDN 3.4.17 (mesuré : `cdn.tailwindcss.com` dans les gabarits de Novaya, et dans
`zeus-index-master.php` de CME, déclaré en 3.4.1 côté WordPress — ce n'est qu'un numéro de
cache, le CDN sert la 3.4.17). La recette est maintenant écrite et éprouvée : elle se rejoue.

## 11. PASSAGE EN TAILWIND v4 (21/09/2026, 03 h 15)

Frédéric a demandé la v4 (et non la dernière 3.x) : « version 4 ». C'est un changement
**majeur**, donc il a été mesuré avant d'être envoyé.

### 11 bis. ⚠️ LE PIÈGE QUI A FAILLI TOUT CASSER SILENCIEUSEMENT : `@source`

En v4, le réglage `content` de la v3 disparaît : on dit où sont les fichiers avec `@source`.
**Quatre écritures ont été essayées, et une seule fonctionne** (mesuré le 21/09/2026, classe
témoin `p-6` / `h-10` / `min-w-[210px]`) :

| Écriture | Résultat |
|---|---|
| `@source "../../../motohall2a/**/*.php"` (relatif + glob) | **NE LIT RIEN** — feuille 42 977 o, classes absentes |
| `@source "C:/…/motohall2a/**/*.php"` (absolu + glob) | **NE LIT RIEN** — 52 713 o |
| `@source "../../../motohall2a"` (relatif, dossier) | **NE LIT RIEN** — 44 592 o |
| `@source "C:/…/motohall2a"` (**absolu + dossier**) | **FONCTIONNE** — 68 402 o, tout est là |

**Ce que ça aurait donné sans contrôle :** la première compilation a produit une feuille où
**181 classes du site manquaient** (`p-6`, `h-10`, `text-sm`…). Aucune erreur, aucun message :
le site se serait affiché **à moitié sans style**, et on aurait cherché la panne partout sauf
là. C'est le contrôle « chaque classe utilisée est-elle définie ? » qui l'a attrapé.

**La parade :** l'écriture du chemin absolu à la main serait une bombe à retard (le jour où le
projet change de dossier, la feuille se fabrique sans les classes du site). Le chemin est donc
**calculé** par `outils_chantier\tailwind-v4\fabriquer.mjs`, qui écrit la feuille d'entrée puis
lance Tailwind. **`npm run build`**, et rien à corriger à la main.

### 11 ter. ⚠️ DEUXIÈME PIÈGE : `spawnSync` sur un `.cmd` Windows

Le script de fabrication lance Tailwind. Première version : `spawnSync` sur
`node_modules\.bin\tailwindcss.cmd` → **échec avec « code null »**, sans message clair (un
`.cmd` a besoin d'un shell, et un programme en bac à sable ne peut pas ouvrir de tube nommé).
**La parade :** viser directement le fichier JavaScript —
`node_modules\@tailwindcss\cli\dist\index.mjs` — que l'on lance avec `process.execPath`. Plus
de shell, plus de tube.

*(À savoir aussi : `npm install` de la v4 a échoué en bac à sable avec `EPERM / syscall spawn`
— Tailwind v4 installe un moteur natif, et son installation ouvre un tube nommé. Il a fallu
une autorisation élargie, une fois, pour l'installation seule.)*

### 11 quater. L'IMPACT RÉEL DE LA v4 SUR CE SITE, MESURÉ

Comparaison des deux feuilles, **classe par classe** (340 classes du site gérées par Tailwind) :

| | Nombre |
|---|---|
| Style **identique** | 108 |
| Style **réécrit** (même valeur, autre écriture) | 221 |
| **Perdue** | **0** *(le seul « perdu » est `group`, un artefact de lecture : `group` n'a jamais eu de règle à lui)* |

Les 221 « réécritures » sont toutes du même genre — **la valeur ne change pas** :
`max-width:24rem` → `max-width:var(--container-sm)` ; `padding-left:.625rem` →
`padding-inline:calc(var(--spacing) * 2.5)` ; `animation:spin 1s linear infinite` →
`animation:var(--animate-spin)` ; `rgba(2,6,23,.6)` → `oklab(…/.6)`.

**Le piège visuel numéro un de la v4 a été cherché, et il n'existe pas ici :** en v4, une
bordure **sans couleur** prend la couleur du texte (en v3, un gris clair). Mesuré :
**0 endroit** dans tout le site utilise `border`/`border-t`/`border-b`… sans couleur précise.
Les autres pièges connus ne s'appliquent pas non plus : **aucun `ring`**, aucune ombre au nom
renommé, aucun rayon au nom renommé.

### 11 quinquies. Le contrôle final, et ce qui est en ligne

- **Couverture des classes sur la v4 : 331 définies dans tailwind.css + 41 dans aegis.css,
  ZÉRO orpheline** — exactement les mêmes chiffres qu'avec la v3.
- **En ligne, après envoi :** 337 classes dans le HTML réellement servi, **0 introuvable** ;
  connexion réelle + **les 8 pages** en 200 ; page 404 debout sur une adresse profonde.
- La feuille servie pèse **68 402 octets** (empreinte `d671dc02…`), contre 24 204 pour la v3.
  La v4 embarque les variables de thème, d'où la différence — ce n'est pas du code en plus.

**Retour arrière en une commande :** l'ancienne feuille (v3.4.17, 24 204 o) est gardée dans
`_backups\avant_tailwind_3.4.19_20260921_030808\tailwind_3.4.17_compilee.css`, et **l'atelier
v3** (`outils_chantier\tailwind\`) sait la refabriquer à l'identique.

### 11 sexies. LES DEUX ATELIERS, ET LEQUEL SERT

| Atelier | Version | À quoi il sert |
|---|---|---|
| `outils_chantier\tailwind-v4\` | **4.3.3** | **LA FEUILLE EN LIGNE** — `npm run build` (passe par `fabriquer.mjs`) |
| `outils_chantier\tailwind\` | 3.4.17 | **secours** : sait refabriquer l'ancienne feuille si on doit revenir en arrière |

**La règle des versions, mise à jour :** MotoHall est maintenant sur la **branche vivante**
(4.3.3), comme le souhaitait Frédéric. La règle reste la même — **la version appartient au
projet, pas à la machine** : c'est le `package.json` de chaque atelier qui la fixe, sans accent
circonflexe. `zeus_env\node` garde la sienne, pour les nouveaux projets.

## 13. LE RANGEMENT DU PROJET (21/09/2026, 04 h 15) — aligné sur Novaya

### 13 bis. Où est quoi, maintenant

Frédéric avait créé une copie des fichiers de production dans
`zeus_projets\deepseek-harness\sites-web\moto-hall\home\capi3439\` — **la même disposition que
Novaya Suites**. Le projet a été entièrement rangé là. **Les deux anciens dossiers
(`Sites_Clients\motohall2a` et `motohall2a_travail`) n'existent plus.**

```
zeus_projets\deepseek-harness\sites-web\moto-hall\
    LISEZ-MOI.txt
    home\capi3439\public_html\motohall.zeusstack.fr\   LE SITE (dossier de travail)
    home\capi3439\zeusstack\motohall\                  LE MOTEUR (includes, templates, tmp)
    _travail\                                          L'ATELIER
        REPRISE.md, envoi\, gabarits_retires\, outils_chantier\, _backups\
        _prod_copie_20260921\    la copie de production de Frédéric, mise de côté
        _retires\                api\ (mort) et backup\ (vieille feuille d'avril)
```

**Pourquoi cette disposition tombe juste sans toucher au code :** `amorce.php` cherche le moteur
**deux niveaux au-dessus du dossier du site**, dans `zeusstack/motohall/`. Depuis
`home\capi3439\public_html\motohall.zeusstack.fr\`, cela donne exactement
`home\capi3439\zeusstack\motohall\` — **la disposition du serveur, à l'identique.** Le même code
marche des deux côtés.

**Le déménagement a été fait par déplacement, jamais par suppression** : ta copie de production a
été mise de côté intacte, et `api\` + `backup\` sont dans `_retires\`, pas à la poubelle.

### 13 ter. ⚠️ LE PIÈGE DU RANGEMENT : TAILWIND LIT DEUX DOSSIERS, PAS UN

Après le déménagement, la première compilation a produit une feuille de **53 562 octets au lieu
de 68 402**. Cause : la plupart des classes Tailwind ne sont **pas** dans le site — elles sont
dans les **gabarits** (`templates/views/*.php`), qui vivent maintenant **hors du dossier du
site**. La compilation ne lisait plus que le site.

**Aucune erreur, aucun message** : le site se serait affiché **à moitié sans style**. C'est la
**taille du fichier produit** qui l'a révélé — d'où l'intérêt de comparer un chiffre avant et
après, à chaque fois.

`fabriquer.mjs` déclare donc **deux sources** — le site **et** le moteur — et il **refuse de
compiler** si l'une des deux manque.

### 13 quater. La preuve du déménagement, et c'est la plus forte possible

**La feuille de style refabriquée depuis le NOUVEAU rangement est OCTET POUR OCTET identique à
celle qui tourne en ligne :** `d671dc029dc1687fddd4c179b6d4e35039a2942784c0add4027e472c3f028c91`,
68 402 octets. Même empreinte des deux côtés. Ce n'est pas « ça a l'air de marcher » : c'est
« **c'est le même fichier** ».

Le reste de la vérification finale : **29 fichiers PHP contrôlés, tous valides** ; les 8 fichiers
indispensables chacun à sa place ; **357 classes (site + gabarits), 0 introuvable** ;
`includes/` et `templates/` bien absents du dossier du site.

### 13 quinquies. Le fichier de configuration, et pourquoi il ne part jamais

Le dossier de travail garde sa configuration d'**essai** (base locale). Le serveur garde la
sienne, qui porte le mot de passe de la base **en ligne** : **elle n'a pas été touchée de toute la
soirée**, et c'est vérifié. Le seul exemplaire local de la version de production est conservé à
part, pour mémoire, dans
`_travail\_prod_copie_20260921\zeusstack_motohall\includes\config.php`.

**Les deux règles écrites en tête du `LISEZ-MOI.txt` du projet :**
1. **Le fichier de configuration ne s'envoie JAMAIS au serveur.**
2. **Après avoir modifié une page, la feuille de style ne se met pas à jour toute seule** :
   `cd _travail\outils_chantier\tailwind-v4` puis `npm run build` (moins d'une seconde).

### 13 sexies. Ce qui a été mesuré sur la copie de production de Frédéric

| | |
|---|---|
| Fichiers de production | 42 |
| Retrouvés dans sa copie, empreinte identique | 38 |
| **Manquants** | **4 — les documents du site** (`Inventaire_Stock.csv`, `Bilan_Mars_2026.csv`, `Procédure_Sécurité.txt`, `Guides/Moteur_KTM.txt`) |
| Différents | 0 |

Les 4 documents ont été rapatriés et vérifiés (empreintes comparées). **Rien n'a été écrasé.**

⚠️ **LEÇON DE LA SOIRÉE, ET ELLE VAUT POUR TOUTES LES PROCHAINES :** sur les cinq contrôles
écrits à la main ce soir-là, **cinq ont produit un faux rouge** — la garde de `admin-users.php`,
le témoin de sauvegarde, la création des comptes, l'échappement des virgules CSS, et le
comparateur de miroir (chemins `./` contre `public_html/`). **Chaque fois, la cause était la
même : deux écritures différentes pour la même chose.** La règle qui en sort : *quand un contrôle
maison annonce un échec, on va lire à la source avant de le croire — et avant de le rapporter.*





