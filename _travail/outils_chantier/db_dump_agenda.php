<?php
require_once __DIR__ . '/includes/_bootstrap.php';
header('Content-Type: text/plain');

try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM agenda_events");
    $events = $stmt->fetchAll();
    echo "NB EVENTS: " . count($events) . "\n";
    foreach ($events as $e) {
        echo "[ID {$e['id']}] DATE: {$e['event_date']} | TITLE: {$e['title']} | CLIENT: {$e['client_name']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
