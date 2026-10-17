<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * DASHBOARD.PHP — TABLEAU DE BORD DE LA DIRECTION
 * Refonte du 20/09/2026.
 *
 * !! CE QUI A CHANGÉ, ET POURQUOI : la version précédente affichait quatre
 *    compteurs dont TROIS ÉTAIENT INVENTÉS (« + 12 % », « 08 .TASK », « 92 % »)
 *    et un « journal d'activité » avec des heures écrites en dur. Un tableau de
 *    bord qui invente ses chiffres ne sert à rien : on ne peut pas décider
 *    dessus. Ici, TOUT vient de la base ou du disque, et rien n'est affiché si
 *    la donnée n'existe pas.
 * ═══════════════════════════════════════════════════════════════════════════
 */

// 1. Sécurisation (Direction / Admin uniquement)
require_once __DIR__ . '/amorce.php';
requireRole(['ADMIN', 'DIRECTEUR']);

// 2. Statistiques — toutes réelles
$agentsActifs   = 0;
$agentsTotal    = 0;
$docsParService = ['atelier' => 0, 'magasin' => 0, 'direction' => 0];
$docCount       = 0;
$rdvAujourdhui  = 0;
$rdvSemaine     = 0;
$prochainsRdv   = [];

try {
    // --- Agents ---
    $agentsTotal  = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $agentsActifs = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE actif = 1")->fetchColumn();

    // --- Rendez-vous ---
    $lundi   = date('Y-m-d', strtotime('monday this week'));
    $dimanche = date('Y-m-d', strtotime('sunday this week'));

    $req = $pdo->prepare("SELECT COUNT(*) FROM agenda_events WHERE event_date = ?");
    $req->execute([date('Y-m-d')]);
    $rdvAujourdhui = (int)$req->fetchColumn();

    $req = $pdo->prepare("SELECT COUNT(*) FROM agenda_events WHERE event_date BETWEEN ? AND ?");
    $req->execute([$lundi, $dimanche]);
    $rdvSemaine = (int)$req->fetchColumn();

    $req = $pdo->prepare(
        "SELECT title, client_name, plate_number, event_date, start_time, status
         FROM agenda_events
         WHERE event_date >= ?
         ORDER BY event_date ASC, start_time ASC
         LIMIT 5"
    );
    $req->execute([date('Y-m-d')]);
    $prochainsRdv = $req->fetchAll();
} catch (Exception $e) {
    error_log('DASHBOARD_STATS: ' . $e->getMessage());
}

// --- Documents, comptés par service (scan réel du disque) ---
$cheminDocuments = __DIR__ . '/documents/';
if (is_dir($cheminDocuments)) {
    foreach (['atelier', 'magasin', 'direction'] as $service) {
        $dossier = $cheminDocuments . $service;
        if (!is_dir($dossier)) {
            continue;
        }
        $parcours = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dossier, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($parcours as $fichier) {
            if ($fichier->isFile()) {
                $docsParService[$service]++;
                $docCount++;
            }
        }
    }
}

// 3. Rendu
$pageTitle = "Tableau de bord";
$pageView  = "dashboard_view";

require_once ROOT_PATH . 'templates/layout_secure.php';
