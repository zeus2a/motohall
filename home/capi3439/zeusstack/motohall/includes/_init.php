<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * _INIT.PHP v5.0 - Le Gardien d'AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

// 1. Parametres de Sécurité Cache
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// 2. Gestion des Erreurs (Production vs Dev)
if (defined('IS_DEV') && IS_DEV) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 3. Timezone Unifié
date_default_timezone_set('Europe/Paris');

// 4. Utilitaires CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf() {
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// 5. Audit Logging Simple
function log_system($action, $user = 'SYSTEM') {
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $log = "[$date] [$ip] [$user] $action\n";
    @file_put_key_contents(ROOT_PATH . 'logs/system.log', $log, FILE_APPEND);
}
