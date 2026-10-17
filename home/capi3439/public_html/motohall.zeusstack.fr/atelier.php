<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ATELIER.PHP — ESPACE TECHNIQUE
 *
 * !! FERMÉ LE 20/09/2026 : cette page était en ACCÈS LIBRE (PAGE_PUBLIC).
 *    MotoHall 2A est un INTRANET pour le personnel, pas un site pour les
 *    clients : toute page exige désormais une connexion.
 * ═══════════════════════════════════════════════════════════════════════════
 */

require_once __DIR__ . '/amorce.php';

// Profils autorisés : l'atelier, et la Direction qui voit tout.
requireRole(['ADMIN', 'DIRECTEUR', 'MECANICIEN']);

$pageTitle = "Atelier";
$pageView  = "atelier_view";

require_once ROOT_PATH . 'templates/layout_secure.php';
