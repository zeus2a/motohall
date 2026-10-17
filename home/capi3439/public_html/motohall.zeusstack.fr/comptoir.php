<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * COMPTOIR.PHP — ESPACE DE VENTE
 *
 * !! FERMÉ LE 20/09/2026 : cette page était en ACCÈS LIBRE (PAGE_PUBLIC).
 *    MotoHall 2A est un INTRANET pour le personnel, pas un site pour les
 *    clients : toute page exige désormais une connexion. Seules `login.php`
 *    (la porte d'entrée) et `error.php` restent accessibles.
 * ═══════════════════════════════════════════════════════════════════════════
 */

require_once __DIR__ . '/amorce.php';

// Profils autorisés : le magasin, et la Direction qui voit tout.
requireRole(['ADMIN', 'DIRECTEUR', 'VENDEUR']);

$pageTitle = "Comptoir";
$pageView  = "comptoir_view";

require_once ROOT_PATH . 'templates/layout_secure.php';
