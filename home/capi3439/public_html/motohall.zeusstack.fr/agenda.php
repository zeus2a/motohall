<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * AGENDA.PHP v6.0 - LE MOTEUR NEXUS PLANNING AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

// Agenda de l'atelier — INTRANET.
// !! FERMÉ LE 20/09/2026 : cette page était en ACCÈS LIBRE, ce qui laissait
//    lire les rendez-vous (noms de clients, immatriculations) à n'importe qui.
//    Elle exige maintenant une connexion, comme toutes les autres pages.
require_once __DIR__ . '/amorce.php';

// Profils autorisés : l'atelier et la Direction (l'agenda est celui de l'atelier).
requireRole(['ADMIN', 'DIRECTEUR', 'MECANICIEN']);

// ACTION API: Récupération des rendez-vous en JSON
if (isset($_GET['ajax_data'])) {
    while (ob_get_level()) ob_end_clean(); // Nettoyage total des buffers
    error_reporting(0);
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $db = getDB();
        
        // Calcul robuste des dates (Lundi-Dimanche)
        $today = new DateTime();
        $monday = clone $today;
        $monday->modify('Monday this week');
        $sunday = clone $monday;
        $sunday->modify('+6 days');

        $start = $_GET['start'] ?? $monday->format('Y-m-d');
        $end = $_GET['end'] ?? $sunday->format('Y-m-d');
        
        $stmt = $db->prepare("SELECT * FROM agenda_events WHERE event_date >= ? AND event_date <= ? ORDER BY event_date ASC, start_time ASC");
        $stmt->execute([$start, $end]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ACTION API: Création d'un rendez-vous
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_event') {
    while (ob_get_level()) ob_end_clean();
    error_reporting(0);
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');

    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO agenda_events (title, client_name, plate_number, event_date, start_time, end_time, status, mechanic_name, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'] ?? 'Sans Titre',
            $_POST['client_name'] ?? '',
            $_POST['plate_number'] ?? '',
            $_POST['event_date'] ?? date('Y-m-d'),
            $_POST['start_time'] ?? '08:00',
            $_POST['end_time'] ?? '09:00',
            $_POST['status'] ?? 'PENDING',
            $_POST['mechanic_name'] ?? '',
            $_SESSION['user_id'] ?? 0
        ]);
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ROUTAGE VUE
$pageTitle = "Agenda atelier";
$pageView  = "agenda_view";

require_once ROOT_PATH . 'templates/layout_secure.php';
