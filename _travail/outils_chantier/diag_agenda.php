<?php
require_once __DIR__ . '/includes/_bootstrap.php';
$db = getDB();
$res = $db->query("SELECT * FROM agenda_events WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
echo "DEBUG ID 1: " . json_encode($res) . "\n";

$start = date('Y-m-d', strtotime('monday this week'));
$end = date('Y-m-d', strtotime('sunday this week'));
echo "DEBUG PERIOD: $start to $end\n";

$stmt = $db->prepare("SELECT * FROM agenda_events WHERE event_date >= ? AND event_date <= ?");
$stmt->execute([$start, $end]);
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "DEBUG FOUND: " . count($all) . " events\n";
foreach($all as $e) {
    echo "  - " . $e['title'] . " (" . $e['event_date'] . ")\n";
}
