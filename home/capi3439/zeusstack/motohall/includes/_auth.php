<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * _AUTH.PHP v5.0 - L'OPÉRATEUR DE SÉCURITÉ AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

require_once __DIR__ . '/_bootstrap.php';

// 1. Détection de la page actuelle
$currentPage = basename($_SERVER['PHP_SELF']);

// 2. Gestion de l'Authentification
$isLoggedIn = isset($_SESSION['user_id']);

// 3. Logique de Redirection
//    !! 20/09/2026 : avant, toute visite non connectée affichait « session
//       expirée », ce qui était faux et inquiétant — il ne s'était rien passé.
//       On distingue maintenant les deux cas : une simple demande d'identifiants
//       (connexion_requise) et une vraie déconnexion (deconnecte).
if (!defined('PAGE_PUBLIC') || PAGE_PUBLIC !== true) {
    if (!$isLoggedIn && $currentPage !== 'login.php') {
        // On renvoie vers LE PORTAIL (index.php) : c'est là que se trouve le
        // formulaire d'identification, sur la page d'accueil du site.
        header('Location: index.php?connexion_requise=1');
        exit;
    }
}

// 4. Utilitaires de Rôle (Standard Pro 2026)
function getRole() {
    return $_SESSION['role_name'] ?? $_SESSION['role'] ?? 'GUEST';
}

function requireRole($authorizedRoles = []) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    
    $currentRole = strtoupper(getRole());
    $authorizedRoles = array_map('strtoupper', (array)$authorizedRoles);
    
    if (!in_array($currentRole, $authorizedRoles)) {
        header('Location: index.php?error=access_denied&role=' . $currentRole);
        exit;
    }
}

// 5. Audit de Connexion (Optionnel - Silencieux)
if ($isLoggedIn && !isset($_SESSION['LAST_AUDIT'])) {
    $_SESSION['LAST_AUDIT'] = time();
}
