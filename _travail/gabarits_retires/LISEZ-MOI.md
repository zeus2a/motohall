# Gabarits retirés le 20/09/2026 — NE PAS REMETTRE DANS LE SITE

## Pourquoi ils sont ici

Frédéric a tranché le 20/09/2026 : **« il ne doit y avoir aucun accès libre, c'est un intranet
pour les aider à travailler, pas pour les clients. »**

Toutes les pages exigent désormais une connexion. Il ne reste donc **qu'une seule coque** :
`templates\layout_secure.php`, avec `templates\sidebar.php`, `header_ui.php` et `footer_ui.php`.

| Fichier rangé ici | Ce qu'il faisait | Pourquoi il ne sert plus |
|---|---|---|
| `layout_public.php` | la coque des pages en **accès libre** (avec le bandeau « Accès libre — aucune authentification requise ») | il n'y a plus de page en accès libre |
| `sidebar_public.php` | le menu des espaces libres (avec un bouton « Zone Direction ») | idem |
| `layout_portail.php` | la coque plein écran du **portail public** d'accueil | le portail est devenu l'accueil **interne**, servi par `layout_secure.php` |

**Rien n'est perdu** : ces trois fichiers existent aussi, intacts, dans les sauvegardes
horodatées de `motohall2a_travail\_backups\` (dont celle prise juste avant cette fermeture,
`motohall2a_avant_fermeture_acces_*`).

## Pourquoi ils ne doivent pas revenir

Ils portent la logique de l'**accès libre**. Les remettre dans le site rouvrirait des pages
au public, ce qui est exactement ce que Frédéric a demandé de fermer.

## Ce qu'il faut savoir si on veut un jour un portail public

Le portail public n'a de sens que pour des **clients**, et MotoHall 2A est un outil interne.
Si le besoin apparaissait, il faudrait le concevoir comme un **site séparé** (autre dossier,
autre adresse), et non en rouvrant les pages de l'intranet.
