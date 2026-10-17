<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LOGOUT.PHP v5.0 - LA SORTIE SÉCURISÉ AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

require_once __DIR__ . '/amorce.php';

// Destruction Session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Redirect vers le PORTAIL (page d'accueil du site)
header('Location: index.php?status=logged_out');
exit;
