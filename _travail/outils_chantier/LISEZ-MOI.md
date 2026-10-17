# Outils de chantier de MotoHall 2A — SORTIS DU DOSSIER WEB le 20/09/2026

## Pourquoi ils sont ici et pas dans le site

Ces sept fichiers étaient posés à la racine du site (`motohall2a\`). Mesure du
20/09/2026, **sans aucun mot de passe** :

| Fichier | Ce qu'il faisait, appelé par n'importe qui |
|---|---|
| `db_update.php` | **modifiait la base** (ajout de colonnes, et forçait un compte en ADMIN) |
| `db_force_check.php` | **créait une table** |
| `db_dump_agenda.php` | renvoyait les rendez-vous **avec le nom des clients** |
| `diag_agenda.php` | renvoyait le contenu complet d'un rendez-vous |
| `diag_web.php` | renvoyait la requête SQL **et** les données |
| `db_audit.php` | renvoyait la structure de la table `users` |
| `db_agenda_setup.php` | créait la table `agenda_events` (celui-ci demandait le mot de passe) |

C'étaient des outils de mise au point, utiles pendant le chantier d'avril 2026,
mais **dangereux dès que le site est joignable depuis Internet**. Ils ont donc
été **déplacés** ici — **rien n'a été supprimé**.

## Ce qu'on perd, et ce qu'on garde

- **Rien n'est perdu** : les sept fichiers sont intacts dans ce dossier, et il en
  existe une copie complète dans
  `motohall2a_travail\_backups\motohall2a_avant_corrections_20260920_213716\`.
- **Aucun lien du site ne pointait vers eux** (vérifié) : les déplacer ne casse
  aucune page.
- Les structures de tables qu'ils contiennent (`users`, `agenda_events`) restent
  la référence pour créer la base : voir `db_force_check.php` et `db_update.php`.

## Si on en a besoin plus tard

Ces outils ne doivent **jamais** être remis dans un dossier accessible par le web.
Pour s'en servir en local, les copier dans le dossier du site **sur le poste
uniquement**, jamais sur l'hébergement.

Le projet d'origine est `C:\zeus_dev\zeus_projets\Sites_Clients\motohall2a`.
