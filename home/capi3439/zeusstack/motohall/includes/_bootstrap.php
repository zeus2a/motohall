<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * _BOOTSTRAP.PHP v5.0 - Le Cœur d'AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

// 1. Démarrage Session Sécurisée
if (session_status() === PHP_SESSION_NONE) {
    session_name('ZEUSSID');
    // !! 20/09/2026 : le cookie "secure" n'est plus écrit en dur.
    //    AVANT : 'cookie_secure' => false (le cookie pouvait partir en clair).
    //    MAINTENANT : il est activé AUTOMATIQUEMENT quand la page est en HTTPS.
    //    Pourquoi pas "true" en dur : en local (http://127.0.0.1) le navigateur
    //    refuserait alors d'envoyer le cookie, et la connexion serait impossible.
    $estEnHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => $estEnHttps,
        'cookie_samesite' => 'Lax'
    ]);
}

// 2. Détection du Mode Fragment (Performance SPA)
define('IS_FRAGMENT', (isset($_GET['fragment']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')));

// 2. Optimisation Performance (Gzip)
if (!isset($_GET['ajax_list']) && !isset($_GET['fragment'])) {
    if (extension_loaded('zlib')) ob_start('ob_gzhandler');
}

// 3. Configuration & Constantes
require_once __DIR__ . '/config.php';

// 4. Connexion Base de Données (Persistent & 127.0.0.1)
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES    => false,
        PDO::ATTR_PERSISTENT          => true, // <--- CONNEXION PERSISTANTE (ACCÉLÉRATION)
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("DB_ERROR: " . $e->getMessage());
    // !! CE TEXTE EST LU PAR LE PERSONNEL, PAS PAR UN TECHNICIEN (19/10/2026).
    //    Il disait « ALERTE SYSTÈME : Connexion BDD impossible. Contactez Zeus
    //    Admin. » — des majuscules qui font peur, « BDD » que personne ne
    //    comprend, et un « Zeus Admin » qui n'existe pas dans l'entreprise.
    //    On dit ce qui se passe, ce qu'il faut faire, et qui prévenir.
    //    Le statut 503 est correct : le service est momentanément indisponible,
    //    et ce n'est pas une erreur du visiteur.
    http_response_code(503);
    die("Le site ne parvient pas à joindre sa base de données pour le moment. "
        . "Réessayez dans quelques instants. Si le problème dure, prévenez la Direction.");
}

/**
 * Accesseur Global BDD
 */
function getDB() {
    global $pdo;
    return $pdo;
}

// 4. Utilitaires de Sécurité Globaux
function clean($data) {
    if (is_array($data)) return array_map('clean', $data);
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function stop($message = "Accès refusé") {
    die($message);
}

// 5. Utilitaires & Logique Système
require_once __DIR__ . '/_init.php';
require_once __DIR__ . '/_functions.php';
