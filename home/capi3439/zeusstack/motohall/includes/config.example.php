<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * CONFIG.EXAMPLE.PHP — LE MODÈLE À RECOPIER
 *
 * Le vrai fichier `config.php` n'est PAS dans ce dépôt : il porte l'hôte, le
 * nom de la base et le mot de passe. Sur le serveur, ce mot de passe est celui
 * de la production.
 *
 * POUR DÉMARRER SUR UN POSTE :
 *     copier ce fichier en `config.php`, dans le même dossier,
 *     puis renseigner les quatre valeurs DB_*.
 *
 * ⚠️ `config.php` ne part JAMAIS sur un service en ligne, et ne se commite
 *    jamais. Il est déjà dans `.gitignore`.
 * ═══════════════════════════════════════════════════════════════════════════
 */

define('SITE_NAME', 'MotoHall 2A');
define('SITE_URL',  'https://motohall.zeusstack.fr');

// Base de données. Sur le serveur o2switch : hôte « localhost ».
// Sur un poste de développement : 127.0.0.1 et la base d'essai.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'motohall2a');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Le moteur vit UN NIVEAU AU-DESSUS de `includes/` : cette ligne est ce qui
// permet au dossier `templates/`, au cache et à la configuration de rester
// ensemble, que le site soit à la racine du sous-domaine ou dans un dossier.
define('ROOT_PATH', realpath(__DIR__ . '/../') . '/');
define('INC_PATH', __DIR__ . '/');
