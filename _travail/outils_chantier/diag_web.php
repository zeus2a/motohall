<?php
define('PAGE_PUBLIC', true);
require_once __DIR__ . '/includes/_auth.php';
header('Content-Type: text/plain');

try {
    $db = getDB();
    $today = new DateTime();
    $monday = clone $today;
    $monday->modify('Monday this week');
    $sunday = clone $monday;
    $sunday->modify('+6 days');
    
    $start = $monday->format('Y-m-d');
    $end = $sunday->format('Y-m-d');
    
    echo "INTERVAL: $start to $end\n\n";
    
    $stmt = $db->prepare("SELECT * FROM agenda_events WHERE event_date >= ? AND event_date <= ?");
    $stmt->execute([$start, $end]);
    $res = $stmt->fetchAll();
    
    echo "SQL: SELECT * FROM agenda_events WHERE event_date >= '$start' AND event_date <= '$end'\n";
    echo "FOUND: " . count($res) . " events\n\n";
    
    var_dump($res);
    
    echo "\n\nALL EVENTS IN DB:\n";
    $all = $db->query("SELECT id, title, event_date FROM agenda_events")->fetchAll();
    var_dump($all);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
