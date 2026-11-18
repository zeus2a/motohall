<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * INDEX.PHP — PORTAIL DE L'INTRANET (page d'accueil du site)
 *
 * C'est ici qu'on arrive sur https://motohall.zeusstack.fr
 *
 * !! CE QUI A CHANGÉ LE 20/09/2026 (demande de Frédéric) :
 *      « c'est ici que l'on doit arriver, sur le portail ».
 *    La racine affiche donc LE PORTAIL, et non plus une page de connexion.
 *    Le portail est PUBLIC dans le seul sens où il n'exige pas encore
 *    d'identifiants — mais il n'expose AUCUNE donnée de l'entreprise : pas un
 *    document, pas un rendez-vous, pas un nom de client. Il présente les trois
 *    espaces et propose de s'identifier.
 *    Dès qu'une donnée est concernée (comptoir, atelier, agenda, documents,
 *    tableau de bord, staff, compte), la page exige la connexion.
 * ═══════════════════════════════════════════════════════════════════════════
 */

define('PAGE_PUBLIC', true);
require_once __DIR__ . '/amorce.php';

$pageTitle = "Portail";
$pageView  = "index_view";

// Un message clair si la personne a été renvoyée ici faute d'être identifiée.
$error = null;
if (isset($_GET['connexion_requise'])) {
    $error = "Merci de vous identifier pour ouvrir cet espace.";
} elseif (isset($_GET['error']) && $_GET['error'] === 'access_denied') {
    $error = "Votre profil ne donne pas accès à cet espace.";
} elseif (isset($_GET['status']) && $_GET['status'] === 'logged_out') {
    $error = "Vous êtes bien déconnecté.";
}

require_once ROOT_PATH . 'templates/layout_secure.php';
