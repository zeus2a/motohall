# Rapport de relecture — texte affiché (MotoHall 2A)

**Date :** 19 octobre 2026
**Racine relue :** `C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel`
**Nature :** lecture seule. Aucun fichier du site n'a été modifié. Cette relecture n'a écrit que dans `_travail\_audit_20261019\` : ce rapport, les 24 copies figées (`_corpus_relecture\`) et leur manifeste.
**Périmètre :** 24 fichiers `.php` nommés dans la demande. Le projet en compte **29** : les 5 fichiers de `home\capi3439\zeusstack\motohall\includes\` (`_auth.php`, `_bootstrap.php`, `_functions.php`, `_init.php`, `config.php`) ne figuraient pas dans la liste et n'ont pas été relus (voir §6).
**Corpus figé :** les fichiers ont été **modifiés pendant la relecture** (reprise visuelle du 19/10/2026, entre 18 h 18 et 18 h 25). La relecture porte donc sur une copie figée à **18 h 26**, déposée dans `_travail\_audit_20261019\_corpus_relecture\`, avec les empreintes SHA-256 de chaque fichier dans `_corpus_relecture\_MANIFESTE.tsv`. **Toutes les lignes citées renvoient à cette copie** : si la reprise a continué après 18 h 26, comparer d'abord la copie figée au fichier vivant.

---

## Conventions de lecture

- Le texte cité est **exactement** celui du fichier, accents et ponctuation compris. Les citations sont des extraits de la ligne indiquée (le plus souvent le seul fragment fautif, pour que la correction soit applicable telle quelle).
- **Apostrophes :** les 24 fichiers ne contiennent **aucune** apostrophe typographique (U+2019). Toutes les apostrophes du texte affiché sont des apostrophes droites `'`. La correction proposée les remplace par `’` (U+2019), caractère bien transmis par ce rapport.
- **Espaces insécables :** aucune des 24 fichiers ne contient d'espace insécable (U+00A0). Le rapport **ne peut pas transporter ce caractère** sans ambiguïté (il est normalisé à l'écriture). Pour les lignes dont c'est le seul défaut, la colonne « texte proposé » reprend donc le texte à l'identique : **le seul changement à faire est de remplacer l'espace par U+00A0** avant `:`, `;`, `?` et `!`. La colonne « problème » le signale pour chaque ligne.
- **Ce qui est compté :** textes affichés, infobulles (`title`), textes de remplacement (`alt`) et exemples de champs (`placeholder`). **Ne sont pas comptés** les noms accessibles (`aria-label`), qui ne s'affichent jamais : ils sont examinés au §6.
- **Contrôles automatisés effectués** sur les 24 fichiers : apostrophes U+2019 (0), espaces U+00A0/U+202F (0), points de suspension ASCII `...` dans du texte affiché (0), doubles espaces dans du texte (0), espaces avant virgule ou point (0), mots français non accentués (0).

---

## 1. Synthèse (l'essentiel d'abord)

1. **Aucune faute d'orthographe ni d'accent.** Le contrôle porte sur les 24 fichiers : tous les accents sont justes, y compris « Déconnexion » (`sidebar.php:147`), « Mécanicien », « Bientôt », « Éclatés », « Réessayez ». L'hypothèse de départ (« Deconnexion » sans accent) **n'est pas confirmée** : le défaut réel est ailleurs (typographie, vocabulaire).
2. **Le défaut dominant est typographique :** 30 lignes du texte affiché contiennent une ou plusieurs **apostrophes droites** `'` au lieu de `’` (0 occurrence d'apostrophe typographique dans le projet), et 23 lignes portent une **espace sécable** avant `:`, `;` ou `?`.
3. **Deux textes sont techniquement faux** et méritent une décision rapide : le mot de passe est annoncé « sous forme **chiffrée** » alors qu'il est **haché** (bcrypt) — `admin_users_view.php:120` et `profil_view.php:135` ; et le message de création annonce « Membre … **activé sur le Node** » alors qu'il crée un compte (`admin-users.php:52`).
4. **13 couples de vocabulaire** pour le même concept (§3), dont 12 sont corrigés dans le tableau. Le terme le plus coûteux est la dénomination des personnes et des espaces : « Membres du staff » / « Comptes du personnel » / « agent » / « Membre » ; « Comptoir » / « Espace de vente » ; « Atelier » / « Espace technique » ; un même écran d'agenda porte **quatre** noms.
5. **Aucune correction de gravité bloquante** : rien, dans le texte, n'empêche une action. Trois points de comportement non textuels sont signalés en §5 (bouton « Retour » encore muet dans un cas, message d'erreur jamais affiché, nom accessible d'un bouton à bascule).

---

## 2. Corrections proposées

> Coloration de la gravité : **bloquant** = le texte empêche ou fausse une action ; **gênant** = le texte est faux, trompeur ou fautif pour un lecteur ; **cosmétique** = la langue est entendue mais la typographie ou l'usage laisse à désirer.

| # | fichier:ligne | texte actuel (exact) | problème | texte proposé (exact) | gravité |
|---|---|---|---|---|---|
| 1 | index.php:31 | `Cet espace ne fait pas partie de votre profil.` | Un espace « ne fait pas partie » d'un profil : la tournure ne dit pas ce qui se passe (l'accès est refusé à ce profil). | `Votre profil ne donne pas accès à cet espace.` | cosmétique |
| 2 | login.php:142 | `personnel : votre profil détermine ensuite les espaces que vous verrez.` | Espace avant « : » non insécable (U+00A0). | texte identique, espace avant « : » en U+00A0 | cosmétique |
| 3 | login.php:195 | `Identifiants oubliés ? Adressez-vous à la Direction.` | Espace avant « ? » non insécable. | texte identique, espace avant « ? » en U+00A0 | cosmétique |
| 4 | error.php:37 | `La demande n\'a pas pu être traitée par le serveur.` | Apostrophe droite au lieu de l'apostrophe typographique (dans la chaîne PHP, elle est écrite `\'` : la correction supprime aussi l'antislash). | `La demande n’a pas pu être traitée par le serveur.` | cosmétique |
| 5 | error.php:39 | `Vous n\'avez pas les droits nécessaires pour consulter cette page.` | Apostrophe droite (écrite `\'` dans la chaîne PHP). | `Vous n’avez pas les droits nécessaires pour consulter cette page.` | cosmétique |
| 6 | error.php:40 | `La page demandée a été déplacée, supprimée, ou l\'adresse est erronée.` | Construction rompue : « a été déplacée, supprimée, **ou l'adresse est erronée** » — le verbe de la troisième branche change. + apostrophe droite (écrite `\'`). | `La page demandée a été déplacée ou supprimée, ou l’adresse est erronée.` | gênant |
| 7 | error.php:41 | `Une erreur interne est survenue. L\'incident a été enregistré.` | Apostrophe droite (écrite `\'` dans la chaîne PHP). | `Une erreur interne est survenue. L’incident a été enregistré.` | cosmétique |
| 8 | error.php:54 | `404 — Page introuvable \| MotoHall 2A` (titre de l'onglet) | Séparateur « \| » ici et « — » dans `layout_secure.php:34` ; le nom du site est tantôt en fin, tantôt en tête. | `MotoHall 2A — Page introuvable (404)` | cosmétique |
| 9 | agenda.php:75 | `Agenda atelier` | Titre de page télégraphique (article manquant) et différent du libellé de menu, qui est « Agenda ». | `Agenda` | gênant |
| 10 | documents.php:98 | `Accès Refusé. Niveau de sécurité insuffisant.` | Majuscule injustifiée à « Refusé » (intérieur de phrase). | `Accès refusé. Niveau de sécurité insuffisant.` | cosmétique |
| 11 | documents.php:151 | `Accès Refusé` | Majuscule injustifiée. | `Accès refusé` | cosmétique |
| 12 | documents.php:152 | `Niveau de sécurité insuffisant pour le District Direction.` | « District Direction » n'a aucun sens en français ; le dossier s'appelle « direction » et c'est un rôle qui verrouille. | `Niveau de sécurité insuffisant pour le dossier Direction.` | gênant |
| 13 | admin-users.php:24 et :101 | `ALERTE SÉCURITÉ : Jeton CSRF invalide.` | Deux mots entiers en capitales ; « Jeton » capitalisé en milieu de phrase ; espace avant « : » non insécable. | `Alerte de sécurité : jeton CSRF invalide.` (espace avant « : » en U+00A0) | cosmétique |
| 14 | admin-users.php:52 | `$success = "Membre '" . $username . "' activé sur le Node.";` | Apostrophes droites employées comme guillemets ; « activé » est faux (l'action **crée** le compte) ; « sur le Node » est du jargon interne incompréhensible. | `$success = "Le membre « " . $username . " » a été créé.";` | gênant |
| 15 | admin-users.php:54 | `Échec déploiement Membre : ` | Article manquant (« le déploiement ») ; « Membre » capitalisé en milieu de phrase ; espace avant « : » non insécable. | `Échec du déploiement du membre : ` (espace avant « : » en U+00A0) | gênant |
| 16 | admin-users.php:90 | `$success = "Profil de '" . $username . "' mis à jour avec succès.";` | Apostrophes droites comme guillemets ; tournure télégraphique sans article. | `$success = "Le profil de « " . $username . " » a été mis à jour avec succès.";` | gênant |
| 17 | admin-users.php:92 | `Échec de modification : ` | Espace avant « : » non insécable (et « de la modification » se lit mieux). | `Échec de la modification : ` (espace avant « : » en U+00A0) | cosmétique |
| 18 | admin-users.php:109 | `Erreur de base de données : ` | Espace avant « : » non insécable. | texte identique, espace avant « : » en U+00A0 | cosmétique |
| 19 | admin-users.php:115 | `Statut de l'agent mis à jour avec succès.` | « agent » : aucun autre écran n'emploie ce mot pour une personne (membre). + apostrophe droite. | `Statut du membre mis à jour avec succès.` | gênant |
| 20 | admin-users.php:122 | `Membres du staff` | « staff » (anglicisme) alors que tout le reste de l'interface dit « personnel » (« Intranet du personnel », « Accès réservé au personnel »). | `Membres du personnel` | gênant |
| 21 | profil.php:44 | `Le nouveau mot de passe est identique à l'ancien.` | Apostrophe droite. | `Le nouveau mot de passe est identique à l’ancien.` | cosmétique |
| 22 | profil.php:57 | `Le changement n'a pas pu être enregistré. Réessayez dans un instant.` | Apostrophe droite. | `Le changement n’a pas pu être enregistré. Réessayez dans un instant.` | cosmétique |
| 23 | amorce.php:53-54 | `Configuration introuvable : le moteur du site n'a pas été trouvé.` / `Contactez l'administrateur.` | Deux apostrophes droites (la phrase est coupée en deux chaînes PHP, lignes 53 et 54) ; espace avant « : » non insécable. | `Configuration introuvable : le moteur du site n’a pas été trouvé.` / `Contactez l’administrateur.` (espace avant « : » en U+00A0) | cosmétique |
| 24 | layout_secure.php:16 | `Erreur de rendu : Fragment de vue manquant.` | Majuscule à « Fragment » en milieu de phrase ; espace avant « : » non insécable. | `Erreur de rendu : fragment de vue manquant.` (espace avant « : » en U+00A0) | cosmétique |
| 25 | header_ui.php:38 | `samedi 1 octobre 2026` (sortie du 1er du mois) | Le premier jour du mois s'écrit « 1er » : `$dateJour` concatène `(int)date('j')`, donc « 1 octobre » douze fois par an, sur **toutes** les pages. | `samedi 1er octobre 2026` | gênant |
| 26 | sidebar.php:80 | `Espace de vente` | La même page s'appelle « Comptoir » dans le titre de section juste au-dessus, dans son titre de page et dans le tableau de bord. | `Comptoir` | gênant |
| 27 | sidebar.php:91 | `Espace technique` | La même page s'appelle « Atelier » dans le titre de section juste au-dessus, dans son titre de page et dans le tableau de bord. | `Atelier` | gênant |
| 28 | sidebar.php:125 | `Membres du staff` | Anglicisme « staff » + vocabulaire à unifier (§3). | `Membres du personnel` | gênant |
| 29 | footer_ui.php:35 | `Connecté : ` | Espace avant « : » non insécable. | texte identique, espace avant « : » en U+00A0 | cosmétique |
| 30 | footer_ui.php:44 | `Page : <?= $memoire ?> Mo` (sortie : « Page : 0.9 Mo ») | Point décimal anglais ; en français la virgule (`round()` produit « 0.9 »). + espace avant « : » non insécable. | `Page : <?= number_format($memoire, 1, ',', ' ') ?> Mo` (sortie : « Page : 0,9 Mo » ; espace avant « : » en U+00A0) | gênant |
| 31 | index_view.php:30-31 (+ profil_view.php:13-14, admin_users_view.php:127-128 et :200-201) | `Vendeur — magasin` / `Mécanicien — atelier` | Le même rôle s'affiche « Vendeur » et « Mécanicien » dans l'en-tête, le pied de page et les badges (`header_ui.php:22-23`, `sidebar.php:30-31`, `footer_ui.php:22-23`, `admin_users_view.php:21-22`), et en forme longue sur le portail, la page Mon compte et les listes déroulantes. Deux libellés pour la même donnée. | `Vendeur` / `Mécanicien` | cosmétique |
| 32 | index_view.php:47 | `Les outils du magasin : vente, commandes et documentation commerciale.` | Espace avant « : » non insécable. | texte identique, espace avant « : » en U+00A0 | cosmétique |
| 33 | index_view.php:50 | `Espace de vente` / `Vue d\'ensemble du comptoir` | Dans la **même carte**, l'univers s'appelle déjà « Comptoir » (ligne 43) et le lien de la même destination est « Comptoir » dans le tableau de bord ; apostrophe droite au sous-titre (écrite `\'`). | `Comptoir` / `Vue d’ensemble du comptoir` | gênant |
| 34 | index_view.php:65 | `Espace technique` / `Vue d\'ensemble de l\'atelier` | Dans la même carte, l'univers s'appelle déjà « Atelier » (ligne 58) ; deux apostrophes droites (écrites `\'`). | `Atelier` / `Vue d’ensemble de l’atelier` | gênant |
| 35 | index_view.php:66 | `Agenda des interventions` | Quatrième nom du même écran (§3). | `Agenda` | gênant |
| 36 | index_view.php:78 | `Les indicateurs de l\'entreprise, la gestion du personnel et les documents réservés.` | Apostrophe droite (écrite `\'` dans la chaîne PHP). | `Les indicateurs de l’entreprise, la gestion du personnel et les documents réservés.` | cosmétique |
| 37 | index_view.php:82 | `Membres du staff` | Anglicisme + vocabulaire à unifier. | `Membres du personnel` | gênant |
| 38 | index_view.php:103 | `Portail de l'intranet` | Apostrophe droite. | `Portail de l’intranet` | cosmétique |
| 39 | index_view.php:111 | `Choisissez un espace ci-dessous : vous voyez ceux que votre profil vous ouvre.` | Espace avant « : » non insécable. | texte identique, espace avant « : » en U+00A0 | cosmétique |
| 40 | index_view.php:113 | `Voici les trois espaces de l'entreprise.` | Apostrophe droite. | `Voici les trois espaces de l’entreprise.` | cosmétique |
| 41 | index_view.php:121 | `<?= htmlspecialchars(date('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>` (sortie : « 19/10/2026 ») | La même date est déjà affichée dans l'en-tête, deux lignes plus haut, au format long « dimanche 19 octobre 2026 » : deux formats pour une seule information sur un seul écran. | Supprimer la ligne (la date figure déjà dans l'en-tête) ou l'écrire au format long, identique à l'en-tête. | cosmétique |
| 42 | index_view.php:215 | `placeholder="votre identifiant"` | Le même champ, sur la page de connexion, s'écrit `Votre identifiant` (`login.php:173`) : deux casses pour un seul champ. | `placeholder="Votre identifiant"` | cosmétique |
| 43 | index_view.php:229 | `Identifiants oubliés ? Adressez-vous à la Direction.` | Espace avant « ? » non insécable. | texte identique, espace avant « ? » en U+00A0 | cosmétique |
| 44 | index_view.php:240 | `accès ; pour un droit supplémentaire` | Espace avant « ; » non insécable. | texte identique, espace avant « ; » en U+00A0 | cosmétique |
| 45 | index_view.php:242 | `Aucun document, aucun rendez-vous et aucune donnée client n'est visible sans` (la phrase se termine ligne 243) | Apostrophe droite (texte HTML). | `Aucun document, aucun rendez-vous et aucune donnée client n’est visible sans` | cosmétique |
| 46 | dashboard_view.php:25 | `Agents actifs` | « Agent » n'est employé nulle part ailleurs pour une personne ; le détail de la même carte parle de « comptes ». | `Membres actifs` | gênant |
| 47 | dashboard_view.php:27 | `' compte(s) désactivé(s)'` (sortie : « 3 compte(s) désactivé(s) ») | Pluriel entre parenthèses : forme qui n'existe pas en français ; la valeur affichée est dynamique. | Accord calculé : `1 compte désactivé` / `3 comptes désactivés` | gênant |
| 48 | dashboard_view.php:33 | `Rendez-vous aujourd'hui` | Apostrophe droite. | `Rendez-vous aujourd’hui` | cosmétique |
| 49 | dashboard_view.php:81 et :91 | `Ouvrir l'agenda` / `Les rendez-vous se créent depuis l'agenda de l'atelier.` | Trois apostrophes droites, sur deux lignes voisines du même écran. | `Ouvrir l’agenda` / `Les rendez-vous se créent depuis l’agenda de l’atelier.` | cosmétique |
| 50 | dashboard_view.php:103 et profil_view.php:81 | `État` (en-tête de colonne) / `État du compte` | « État » ici, « Statut » ailleurs (`admin-users.php:115`) pour la même notion. | `Statut` / `Statut du compte` | cosmétique |
| 51 | dashboard_view.php:148 | `Membres du staff` | Anglicisme + vocabulaire à unifier. | `Membres du personnel` | gênant |
| 52 | documents_view.php:19-21 | `title="Recharger la liste"` posé sur un bouton intitulé `Actualiser` | Deux verbes pour la même action dans le même contrôle (le bouton recharge la page). | `title="Actualiser la liste"` | cosmétique |
| 53 | documents_view.php:36 | `throw new Error('Network Response Fail');` | Message d'erreur en anglais dans une interface française ; il est affiché à l'utilisateur par l'alerte de la ligne 46. | `throw new Error('le serveur n’a pas répondu.');` | gênant |
| 54 | documents_view.php:46 | `alert('Erreur d\'accès au dossier : ' + err.message);` | Redondance (l'alerte finit par « Erreur … : Erreur … » quelle que soit la panne) ; apostrophe droite ; espace avant « : » non insécable. | `alert('Impossible d’ouvrir le dossier : ' + err.message);` (espace avant « : » en U+00A0) | gênant |
| 55 | documents_view.php:246 | `Exporter` | Le lien porte l'attribut `download` : il **télécharge** le fichier, il n'exporte rien. Le bouton jumeau de la ligne 279 dit autre chose pour la même action. | `Télécharger` | gênant |
| 56 | documents_view.php:267 | `alt="Visuel Sécurisé"` | Texte alternatif sans rapport avec l'image (c'est l'aperçu du document) ; majuscule injustifiée. | `alt="Aperçu du document"` | cosmétique |
| 57 | documents_view.php:276 | `Format Non-Visualisable` | Capitales de titre à l'anglaise + trait d'union fautif (« non » ne prend pas de trait d'union). | `Format non visualisable` | gênant |
| 58 | documents_view.php:277 | `Le système interne ne dispose pas des décodeurs pour ce type de fichier. L'ouverture via l'application de bureau dédiée (Excel, Word, Winrar) est obligatoire.` | « via » (anglicisme), « est obligatoire » (c'est une nécessité, pas une obligation : personne n'oblige), « Winrar » (le nom s'écrit WinRAR) ; apostrophe droite. | `Le système interne ne dispose pas des décodeurs nécessaires pour ce type de fichier. L’ouverture par l’application de bureau dédiée (Excel, Word, WinRAR) est nécessaire.` | gênant |
| 59 | documents_view.php:279 | `Extraire le Fichier` | Capitales de titre ; « extraire » ne décrit pas l'action (c'est un téléchargement, comme ligne 246). | `Télécharger le fichier` | gênant |
| 60 | admin_users_view.php:43 | `Comptes du personnel` | Titre de la vue différent du titre de page (`admin-users.php:122`), du menu (`sidebar.php:125`), du portail et du tableau de bord — qui disent tous « Membres du staff ». | `Membres du personnel` | gênant |
| 61 | admin_users_view.php:94 et :172 | `Identifiant de connexion` | Le même champ s'appelle « Identifiant » à la connexion (`login.php:171`, `index_view.php:213`). | `Identifiant` | cosmétique |
| 62 | admin_users_view.php:120 | `Il sera enregistré sous forme chiffrée : personne ne pourra le relire.` | Faux techniquement : le mot de passe est **haché** (bcrypt), pas chiffré — « chiffré » laisse croire qu'on peut le déchiffrer. + espace avant « : » non insécable. | `Il sera enregistré sous forme hachée : personne ne pourra le relire.` (espace avant « : » en U+00A0) | gênant |
| 63 | admin_users_view.php:125 et :198 | `Niveau d'accès` | La même donnée (Administrateur / Direction / Vendeur / Mécanicien) s'appelle « Profil » sur la page Mon compte (`profil_view.php:61`). | `Rôle` | cosmétique |
| 64 | admin_users_view.php:272 | `'Désactiver' : 'Réactiver' ?> ce compte ?` | Espace avant « ? » non insécable (les deux libellés sont concernés). | texte identique, espace avant « ? » en U+00A0 | cosmétique |
| 65 | admin_users_view.php:287 | `title="Modifier ce compte"` | La fenêtre qu'il ouvre s'intitule « Modifier le membre » (ligne 159) : « compte » et « membre » pour la même personne. | `title="Modifier ce membre"` | cosmétique |
| 66 | agenda_view.php:80 | `alert('Erreur: ' + e)` | Espace avant « : » **absente** (le français en exige une, insécable). | `alert('Erreur : ' + e)` (espace avant « : » en U+00A0) | cosmétique |
| 67 | agenda_view.php:114 | `Planning de l'atelier` | Quatrième nom du même écran (§3) ; « planning » est un anglicisme ; apostrophe droite. | `Agenda` | gênant |
| 68 | agenda_view.php:116 | `Vue semaine — les rendez-vous se créent avec le bouton à droite.` | « à droite » est faux : sous 1 024 px, le bouton passe **sous** le titre (la barre est `flex-col` puis `lg:flex-row`) ; style télégraphique. | `Vue par semaine. Créez un rendez-vous avec le bouton « Nouveau rendez-vous ».` | gênant |
| 69 | agenda_view.php:129 | `+ ' — ' +` (sortie : « 19 octobre — 25 octobre 2026 ») | Tiret cadratin (U+2014) pour une plage de dates, alors que la même page écrit les plages horaires avec un tiret demi-cadratin (ligne 164, U+2013). | `19 octobre – 25 octobre 2026` (même tiret que ligne 164) | cosmétique |
| 70 | agenda_view.php:208 | `Objet de l'intervention` | Apostrophe droite. | `Objet de l’intervention` | cosmétique |
| 71 | agenda_view.php:210 | `Ex. : remplacement pneu arrière` | « de » manquant (« remplacement **du** pneu ») ; espace avant « : » non insécable. | `Ex. : remplacement du pneu arrière` (espace avant « : » en U+00A0) | cosmétique |
| 72 | comptoir_view.php:19 | `Espace de vente et d'accueil client.` | Troisième nom de la page « Comptoir » (§3) ; apostrophe droite. | `Comptoir : vente et accueil client.` (espace avant « : » en U+00A0) | gênant |
| 73 | comptoir_view.php:72 | `Les documents du magasin sont rangés par type : procédures, formulaires, images et inventaires.` | Espace avant « : » non insécable. | texte identique, espace avant « : » en U+00A0 | cosmétique |
| 74 | atelier_view.php:17 | `Espace technique : planning des interventions, ordres de réparation et documentation atelier.` | « Espace technique » au lieu d'« Atelier » ; « documentation atelier » (article manquant) ; espace avant « : » non insécable. | `Atelier : planning des interventions, ordres de réparation et documentation de l’atelier.` (espace avant « : » en U+00A0) | gênant |
| 75 | atelier_view.php:28 | `Agenda de l'atelier` | Apostrophe droite. | `Agenda de l’atelier` | cosmétique |
| 76 | atelier_view.php:30 | `Rendez-vous de la semaine et charge de travail de l'équipe.` | Apostrophe droite. | `Rendez-vous de la semaine et charge de travail de l’équipe.` | cosmétique |
| 77 | atelier_view.php:33 | `Ouvrir le planning` | Le même lien est intitulé « Ouvrir l'agenda » dans le tableau de bord (`dashboard_view.php:81`) ; « planning » ≠ « agenda ». | `Ouvrir l’agenda` | gênant |
| 78 | atelier_view.php:61 | `Éclatés moteur, manuels d'atelier et fiches fournisseurs.` | Apostrophe droite (« Éclatés moteur » télégraphique : voir §5). | `Éclatés moteur, manuels d’atelier et fiches fournisseurs.` | cosmétique |
| 79 | atelier_view.php:64 | `Ouvrir la librairie` | « Librairie » = commerce de livres ; c'est le faux ami de l'anglais *library*. La destination s'appelle « Documentation » partout ailleurs. | `Ouvrir la documentation` | gênant |
| 80 | atelier_view.php:71 | `Les documents de l'atelier sont rangés par type : guides, manuels, procédures et formulaires.` | Apostrophe droite ; espace avant « : » non insécable. | `Les documents de l’atelier sont rangés par type : guides, manuels, procédures et formulaires.` (espace avant « : » en U+00A0) | cosmétique |
| 81 | profil_view.php:61 | `Profil` (libellé de la ligne qui affiche Administrateur, Direction, Vendeur ou Mécanicien) | Le libellé n'annonce pas ce qu'il montre : la valeur est le **rôle**, pas un profil. | `Rôle` | gênant |
| 82 | profil_view.php:94 | `Pour modifier les informations d'un autre membre, utilisez` (le lien « Membres du staff » est ligne 95) | Apostrophe droite ; le lien de la ligne 95 dit « Membres du staff » (à remplacer par « Membres du personnel », cf. V5) et il est annoncé sans guillemets. | `Pour modifier les informations d’un autre membre, utilisez` (lien ligne 95 : `Membres du personnel`) | gênant |
| 83 | profil_view.php:135 | `Votre mot de passe est enregistré sous forme chiffrée : personne — pas même la` (la phrase se termine ligne 136) | Même erreur technique qu'à la ligne 62 (haché, non chiffré) ; espace avant « : » non insécable. | `Votre mot de passe est enregistré sous forme hachée : personne — pas même la Direction — ne peut le relire.` (espace avant « : » en U+00A0) | gênant |

---

## 3. Incohérences de vocabulaire

### 3.1 Concepts, appellations relevées et terme retenu

| # | Concept | Appellations trouvées (fichier:ligne) | Terme retenu | Lignes du §2 |
|---|---|---|---|---|
| V1 | L'espace de vente | « Comptoir » (`sidebar.php:74`, `comptoir.php:18`, `dashboard_view.php:162`, `index_view.php:43`) / « Espace de vente » (`sidebar.php:80`, `index_view.php:50`, `comptoir_view.php:19`) | **Comptoir** | 26, 33, 72 |
| V2 | L'espace technique | « Atelier » (`sidebar.php:85`, `atelier.php:17`, `dashboard_view.php:169`, `index_view.php:58`) / « Espace technique » (`sidebar.php:91`, `index_view.php:65`, `atelier_view.php:17`) | **Atelier** | 27, 34, 74 |
| V3 | L'agenda de l'atelier | « Agenda » (`sidebar.php:98`) / « Agenda atelier » (`agenda.php:75`) / « Agenda des interventions » (`index_view.php:66`) / « Planning de l'atelier » (`agenda_view.php:114`) / « Ouvrir le planning » (`atelier_view.php:33`) / « Ouvrir l'agenda » (`dashboard_view.php:81`) | **Agenda** | 9, 35, 67, 77 |
| V4 | La bibliothèque de documents | « Documentation » (`sidebar.php:108`, `documents.php:226`, `documents_view.php:14`, `index_view.php:83`, `dashboard_view.php:155`, `comptoir_view.php:50`) / « Librairie » (`atelier_view.php:64`) / « documentation atelier » (`atelier_view.php:17`) | **Documentation** (l'espace ; « document » reste le mot pour un fichier) | 74, 79 |
| V5 | Les personnes qui ont un compte | « Membres du staff » (`sidebar.php:125`, `admin-users.php:122`, `index_view.php:82`, `dashboard_view.php:148`, `profil_view.php:95`) / « Comptes du personnel » (`admin_users_view.php:43`) / « agent » (`admin-users.php:115`) / « Membre » (`admin-users.php:54`, `admin_users_view.php:159`) / « personnel » (`sidebar.php:49`, `login.php:138`, `footer_ui.php:48`) | **Membres du personnel** ; « membre » pour une personne ; « compte » réservé à l'objet technique | 19, 20, 28, 37, 46, 51, 60, 65, 82 |
| V6 | La page de compte personnelle | « Mon compte » (`profil.php:63`, `sidebar.php:70`, `header_ui.php:134`) / « Profil » (`profil_view.php:61`) / « Mes informations » (`profil_view.php:41`) | **Mon compte** (la page) ; « Mes informations » conservé comme sous-titre ; « Profil » réservé au mot courant, jamais comme libellé de champ | 81 |
| V7 | Le rôle de la personne | « Niveau d'accès » (`admin_users_view.php:125`, `:198`) / « Profil » (`profil_view.php:61`) / libellé du badge (`index_view.php:120`, `header_ui.php:139`) | **Rôle** | 63, 81 |
| V8 | Les libellés de rôle | « Vendeur » / « Mécanicien » (`header_ui.php:22-23`, `sidebar.php:30-31`, `footer_ui.php:22-23`, `admin_users_view.php:21-22`) contre « Vendeur — magasin » / « Mécanicien — atelier » (`index_view.php:30-31`, `profil_view.php:13-14`, `admin_users_view.php:127-128`, `:200-201`) | **Vendeur** / **Mécanicien** | 31 |
| V9 | L'état d'un rendez-vous ou d'un compte | « État » (`dashboard_view.php:103`, `profil_view.php:81`) / « Statut » (`admin-users.php:115`) | **Statut** | 50 |
| V10 | Le nom de connexion | « Identifiant » (`login.php:171`, `index_view.php:213`) / « Identifiant de connexion » (`admin_users_view.php:94`, `:172`) | **Identifiant** | 61 |
| V11 | Télécharger un fichier | « Télécharger » (`documents_view.php:196`, `documents.php:211`) / « Exporter » (`documents_view.php:246`) / « Extraire le Fichier » (`documents_view.php:279`) | **Télécharger** | 55, 59 |
| V12 | Rafraîchir une liste | « Actualiser » (libellé, `documents_view.php:21`) / « Recharger la liste » (bulle, `documents_view.php:19`) | **Actualiser** | 52 |
| V13 | L'objet inscrit à l'agenda | « rendez-vous » (`index_view.php:66`, `agenda_view.php:140`, `dashboard_view.php:78`) / « intervention » (`agenda_view.php:208`, `atelier_view.php:17`, `index_view.php:66`) | **Rendez-vous** pour l'objet de l'agenda ; « intervention » pour le travail d'atelier | *à arbitrer* (§5, point 7) |

**Total : 13 couples, dont 12 corrigés** (lignes indiquées dans la dernière colonne).

### 3.2 Couples vérifiés et jugés cohérents (aucune correction)

- **« Tableau de bord » / « Dashboard »** : « Dashboard » n'apparaît **jamais** à l'écran ; il ne vit que dans le nom de fichier `dashboard.php` et les commentaires. Terme unique : « Tableau de bord ».
- **« Documentation » / « Documents »** : « Documents » n'est employé que pour des fichiers réels (« Documents stratégiques et RH », « documents fournisseurs », « Documents classés ») ; l'espace, lui, s'appelle toujours « Documentation ». Rien à corriger : ce ne sont pas deux noms du même objet.
- **« Portail » / « Intranet »** : « Intranet » est le nom du site (racine du fil d'Ariane), « Portail » celui de la page d'accueil. Hiérarchie normale.

---

## 4. Dates

`header_ui.php:38` construit `$dateJour` ainsi :

```php
$dateJour = $enFr[(int)date('w')] . ' ' . (int)date('j') . ' ' . $moisFr[(int)date('n')] . ' ' . date('Y');
```

soit « dimanche 19 octobre 2026 ». **Le format lui-même est correct** (jour en minuscules, mois en minuscules, pas de virgule, pas de zéro initial) — à une exception près : le premier du mois donne « 1 octobre » au lieu de « 1er octobre » (ligne 25 du §2, douze jours par an, sur toutes les pages).

| Endroit | Format produit | Exemple | Verdict |
|---|---|---|---|
| `header_ui.php:116` (sous-titre de page) et `:79` (en-tête visiteur) | long, sans zéro initial | `dimanche 19 octobre 2026` | à conserver ; corriger « 1er » |
| `index_view.php:121` (carte de bienvenue) | court | `19/10/2026` | **en double** avec l'en-tête, sur le même écran, dans un autre format (ligne 41) |
| `dashboard_view.php:111`, `admin_users_view.php:256`, `profil_view.php:74` | court | `19/10/2026` | cohérent avec la règle proposée |
| `agenda_view.php:129` | long sans année sur la 1re borne | `19 octobre — 25 octobre 2026` | correct ; seul le tiret est à aligner (ligne 69) |
| `agenda_view.php:40` (nom du jour des cases) | abrégé | `sam. 19 oct.` | correct pour une case de calendrier |
| `layout_secure.php:118-119` | long, avec zéro initial | `samedi 01 octobre 2026` | **calculé mais jamais affiché** (`date` n'est utilisé par aucun `x-text`) : source d'écart latent avec `$dateJour` ; à supprimer ou à aligner |
| `dashboard_view.php:114`, `agenda_view.php:164`, `header_ui.php:123` | heures | `08:00`, `08:00 – 09:00`, `14:32:07` | correct |

**Règle proposée, une fois pour tout l'intranet :** date en toutes lettres dans l'en-tête (avec « 1er »), date courte `jj/mm/aaaa` partout ailleurs (tableaux, fiches), et une seule occurrence de la date par écran. Appliquée, cette règle ne demande qu'une correction de fond (ligne 25) et la suppression du doublon `index_view.php:121` (ligne 41).

---

## 5. À arbitrer (doutes assumés — non comptés)

Ces points ne sont pas des fautes certaines : ils demandent une décision.

1. **`error.php:118-126` — le bouton « Retour » (en partie réglé le 19/10).** Il n'est plus un `javascript:history.back()` : c'est un `<button>` protégé par `if (history.length > 1)`. Mais ouvert directement (signet, page 404 servie par le `.htaccess`), l'historique est vide : le bouton ne fait **rien**, alors que son libellé promet un retour. Deux issues : supprimer le bouton (l'« Accueil » juste à côté suffit) ou le faire pointer vers `index.php` avec un autre libellé.
2. **`comptoir_view.php:19` — « Les accès ci-dessous s'ouvrent sans mot de passe. »** Depuis la fermeture du 20/09/2026, cette phrase se lit mal : la page est protégée, et deux des trois cartes annoncent « Bientôt disponible ». Formulation possible : « Aucun mot de passe supplémentaire n'est demandé. » ou suppression de la phrase.
3. **`admin-users.php:12` — un message écrit qui ne s'affiche jamais.** `header('Location: dashboard.php?error=accès_réservé_à_l_administrateur')` : aucun code ne lit `$_GET['error']` dans `dashboard.php`. Une personne non administrateur est renvoyée sans un mot. Texte à créer (ou redirection à revoir).
4. **`agenda_view.php:234-237` — la valeur enregistrée revient à l'écran.** La liste déroulante affiche « Pont 1 — service rapide » mais enregistre `Pont 1 (Service Rapide)` ; la case du calendrier affiche ensuite `evt.mechanic_name` (ligne 176), donc « Pont 1 (Service Rapide) », avec parenthèses et majuscule. Aligner la valeur sur le libellé — attention aux rendez-vous déjà saisis.
5. **Deux titres de page par défaut différents** : `layout_secure.php:34` prévoit `'Direction'`, `header_ui.php:17` prévoit `'Intranet'`. Unifier.
6. **`login.php:165` et `index.php:33` — « Vous êtes bien déconnecté. »** Accord au masculin par défaut. Formulation neutre possible : « Votre session a bien été fermée. »
7. **« rendez-vous » / « intervention »** (couple V13) : `agenda_view.php:208` demande l'« Objet de l'intervention » pour le titre d'un **rendez-vous**. Les deux mots peuvent coexister (le rendez-vous *pour* une intervention), mais la phrase les confond. À trancher avec le métier.
8. **`agenda_view.php:77` et `documents_view.php:38`** — `alert(data.error)` affiche le message brut renvoyé par le serveur (une erreur SQL peut donc apparaître en clair). Prévoir un message français générique.
9. **`atelier_view.php:46` — « des O.R. »** : l'abréviation n'est pas définie dans la phrase (le titre dit « Ordres de réparation »). Écrire « des ordres de réparation » ou définir « O.R. » une fois.
10. **`atelier_view.php:61` — « Éclatés moteur »** : style télégraphique du métier. Acceptable si le personnel le lit ainsi ; sinon « Vues éclatées de moteur ».
11. **Majuscules d'affichage (CSS).** `documents_view.php:241`, `:245-251` et `:276-279` posent la classe `uppercase` : deux phrases entières s'affichent en capitales (ligne 277), ce qui se lit mal. Recommandation de charte : capitales pour les étiquettes courtes seulement, pas pour un paragraphe.
12. **`admin_users_view.php:260-262` — « Actif » / « Désactivé »** : un adjectif et un participe. Couple acceptable ; couple strictement parallèle possible : « Actif » / « Inactif ».
13. **`profil_view.php:41` — « Mes informations »** (sous-titre de la page Mon compte) : conservé. Ce n'est pas une incohérence, mais un choix de rédaction à valider.
14. **`header_ui.php:101` — `aria-label="Ouvrir le menu"` sur un bouton qui ouvre *et* ferme.** Le nom accessible ne décrit que la moitié de l'action. Deux issues : un libellé neutre (« Menu ») ou un libellé qui suit l'état.
15. **La marque s'écrit « MotoHall 2A »** dans les 24 fichiers, alors que le `AGENTS.md` du projet titre « MOTO HALL 2A ». L'interface est homogène ; c'est l'orthographe officielle qu'il faut confirmer.

---

## 6. Hors périmètre

- **5 fichiers non relus** (absents de la liste) : `home\capi3439\zeusstack\motohall\includes\_auth.php`, `_bootstrap.php`, `_functions.php`, `_init.php`, `config.php`. C'est ce qui explique l'écart entre les **29 fichiers `.php`** du projet et les **24** nommés dans la demande.
- **Un texte visible y a été repéré**, et il porte exactement les mêmes défauts : `_bootstrap.php:48` — `die("ALERTE SYSTÈME : Connexion BDD impossible. Contactez Zeus Admin.");` (capitales, « BDD », « Zeus Admin », espace avant « : »). Non compté ici, à traiter avec les 5 fichiers si vous les ouvrez à la relecture.
- **Commentaires PHP :** non relus, comme demandé. Ils sont nombreux et respectent souvent mieux la typographie que le texte affiché (« … », espaces). Une faute y a été vue au passage, non comptée : `logout.php:4` « LA SORTIE SÉCURISÉ » (pour « SÉCURISÉE »). Celle de `layout_secure.php` (« seul ressource ») a été corrigée le 19/10/2026.
- **Textes ajoutés par la reprise visuelle du 19/10/2026** (bascule de thème, noms accessibles) : relus, et **non comptés**, car ils ne s'affichent pas à l'écran. Ils sont propres, à une exception : `sidebar.php:39` — `aria-label="Navigation de l'intranet"` contient une apostrophe droite (à écrire « Navigation de l’intranet »). Tous les autres (`Changer de thème`, `Fermer la fenêtre`, `Fermer la visionneuse`, `Fermer la notification`, `Aller au contenu`, `Fil d'Ariane`, `Ouvrir le menu`) sont correctement écrits et accentués.

---

## 7. Comptage

| Mesure | Nombre |
|---|---|
| **Corrections proposées (§2, lignes du tableau)** | **83** |
| dont bloquantes | **0** |
| dont gênantes | **37** |
| dont cosmétiques | **46** |
| **Coquilles réelles** (orthographe, accents, accords, grammaire, ponctuation typographique, casse, majuscules, libellé faux, traduction) | **55** |
| **Alignements de vocabulaire** (lignes du §2 appliquant un terme retenu du §3) | **28** |
| **Incohérences de vocabulaire (couples de concepts)** | **13** — dont 12 corrigés, 1 à arbitrer (V13) |
| Points à arbitrer (non comptés, §5) | 15 |

**Trois chiffres à retenir :**

1. **55 coquilles réelles** — aucune faute d'orthographe ni d'accent ; ce sont pour l'essentiel des apostrophes droites (30 lignes), des espaces insécables manquantes (23 lignes), sept lignes de capitales fautives, deux « chiffrée » à remplacer par « hachée », un « 1er » manquant, deux textes techniquement faux (`admin-users.php:52`, `documents_view.php:277`), le reste étant de la grammaire et des libellés trompeurs.
2. **13 incohérences de vocabulaire**, dont 12 se corrigent par un tableau de termes unique (§3.1) et une seule laissée à la décision du métier.
3. **0 correction bloquante** : le texte n'empêche aucune action. Les trois risques fonctionnels (bouton « Retour » muet dans un cas, message d'erreur jamais affiché, nom accessible d'un bouton à bascule) sont hors du texte et signalés au §5.
