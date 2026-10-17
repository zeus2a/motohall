# MOTO HALL 2A — l'intranet d'un concessionnaire (PHP + MySQL)

> **Niveau : STAFF ENGINEER 2026** — règle n° 1 (le plancher) et règle n° 2 (les
> sous-agents au même niveau). Les six exigences : ① rien n'est affirmé sans sa mesure
> ② on lit avant d'écrire, et on cherche l'outil qui existe déjà ③ un seul endroit par
> chose ④ le changement est chirurgical, mesuré et réversible ⑤ un contrôle doit pouvoir
> dire NON ⑥ le travail est reprenable par un autre sans me parler.


**LA RÈGLE DE RECONNAISSANCE, PARTOUT : le dossier de travail est celui qui contient un
`AGENTS.md`.** S'il n'en a pas, il n'est pas prêt : on ne l'ouvre pas, on le signale.

---

## 1. CE DOSSIER EST LE BON — et voici ou est le site

| Quoi | Le chemin, mesure le 17/10/2026 |
|---|---|
| **Le site** | `home\capi3439\public_html\motohall.zeusstack.fr\` — **25 fichiers** |
| **Le moteur** (hors du dossier web) | `home\capi3439\zeusstack\motohall\` — **17 fichiers** : `includes\`, `templates\` |
| **L'atelier** | `_travail\` — 2 434 fichiers : `REPRISE.md`, `_backups\` (**232 fichiers** de copies datees), `_prod_copie_20260921\`, `gabarits_retires\`, `outils_chantier\` |
| Le total | **2 477 fichiers**, 33 Mo |

⚠️ **Le nom du dossier porte `_laravel` et ce projet n'est PAS un Laravel** : c'est la
trace d'un projet precedent. Les noms **ne disent pas** ce que contient un dossier — seul
`AGENTS.md` le dit, et c'est pourquoi il est ici.

⚠️ **Le departement `moto-hall` existe aussi, et il est vide de site** : c'est un nom de
projet sans code. Ne pas y ouvrir de session.

---

## 2. CE QUE CE PROJET EST

Un **intranet** de concession moto : quatre profils — **Admin**, **Direction**,
**Vendeur**, **Mecanicien** — et des ecrans par metier (agenda, comptoir, atelier,
documents, dashboard, admin des comptes). Le moteur est monte dans
`~/zeusstack/motohall/`, **hors** du dossier web.

L'envoi et les tables sont dans `_travail\envoi\` : `htaccess_serveur.txt`,
`tables_motohall.sql`.

---

## 3. LE DEPLOIEMENT

    & 'C:\zeus_dev\zeus_env\python\python.exe' 'C:\zeus_dev\zeus_env\outils\ssh_o2switch.py' "commande" --compte novaya

Compte **capi3439**, base `capi3439_motohall`. Identifiants lus dans
`_acces\O2SWITCH.txt`.

---

## 4. CE QU'IL NE FAUT PAS FAIRE

1. **Croire que c'est du Laravel** parce que le dossier s'appelle `_laravel` : il n'y a
   ni `artisan`, ni `composer.json`. Le code est du PHP pose tel quel.
2. **Supprimer `_travail\_backups\`** : 232 fichiers de copies datees, c'est le seul
   retour en arriere du projet.
3. **Modifier un gabarit sans passer par `gabarits_retires\`** : les anciens y sont
   gardes avec un `LISEZ-MOI.md`.
4. **Un commentaire dans le code** (regle 87). **PowerShell pour le web** (regle zero).

---

## 5. LES TROIS PREMIERES CHOSES A FAIRE ICI

1. Lire `_travail\REPRISE.md` en entier.
2. Compter les tables de `capi3439_motohall` avant de toucher au SQL.
3. Ecrire ce qu'on a fait dans `REPRISE.md`, avec le chiffre.
