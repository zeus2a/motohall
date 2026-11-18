# MotoHall 2A — l'intranet d'une concession moto

> Outil de travail interne : agenda d'atelier, comptoir de vente, documentation,
> tableau de bord de la direction. **Accès réservé au personnel.**
>
> Ce n'est pas un site public : il ne doit pas être indexé, et il ne l'est pas
> (trois verrous : `robots.txt`, en-tête `X-Robots-Tag`, balise `meta robots`).

## Ce que c'est

Un intranet en **PHP 8.4** et **MySQL**, sans framework, pour quatre profils :

| Profil | Ce qu'il ouvre |
|---|---|
| `ADMIN` | tout, plus la gestion des comptes |
| `DIRECTEUR` | tout, sauf la gestion des comptes |
| `VENDEUR` | comptoir, documentation |
| `MECANICIEN` | atelier, agenda, documentation |

Chaque page vérifie les droits **côté serveur**. Masquer un lien ne protège rien.

## Comment le code est rangé

```
home/capi3439/
    public_html/motohall.zeusstack.fr/   LE SITE (ce que le web sert)
    zeusstack/motohall/                  LE MOTEUR (hors du web)
        includes/   configuration, session, base, fonctions
        templates/  coques et vues
```

Le moteur est **dehors** du dossier servi par le web : c'est ce qui fait
qu'aucune adresse ne peut l'atteindre. Les pages le trouvent par `amorce.php`.

## Installation sur un poste

1. Copier `home/capi3439/zeusstack/motohall/includes/config.example.php`
   en `config.php`, et renseigner les quatre valeurs `DB_*`.
2. Créer la base et les tables : `_travail/envoi/tables_motohall.sql`.
3. Servir le dossier du site (`php -S 127.0.0.1:8080 -t home/capi3439/public_html/motohall.zeusstack.fr`).

## Feuilles de style

- `assets/css/aegis.css` — écrite à la main : charte, deux thèmes (clair et
  sombre), composants. **C'est là qu'on touche le style.**
- `assets/css/tailwind.css` — **compilée**, ne pas modifier à la main.
  Après une modification de page :
  `cd _travail/outils_chantier/tailwind-v4 && npm run build`
- Les polices sont **dans le site** (`assets/fonts/`) : aucun appel à un CDN.

## Conventions de travail

Elles sont dans [`AGENTS.md`](AGENTS.md), et elles ne sont pas décoratives :

1. **Une copie avant de toucher un fichier** — `_travail/outils_chantier/avant_de_modifier.php`.
2. **Un point de retour git** à la fin de chaque intervention (commit + étiquette).
3. **Une ligne au journal** — [`_travail/JOURNAL.md`](_travail/JOURNAL.md).
4. **Un contrôle** : `php -l` sur le fichier, et le contrôle de structure pour
   un gabarit. Un fichier modifié sans contrôle n'existe pas.

Le carnet du projet, avec le *pourquoi* de chaque décision et les mesures qui
les appuient, est [`_travail/REPRISE.md`](_travail/REPRISE.md).

## Ce qui n'est pas dans ce dépôt

- `includes/config.php` — les identifiants de la base. Voir `config.example.php`.
- `_travail/_backups/` — les copies datées.
- `_travail/_audit_*/` — les rapports d'audit internes.
