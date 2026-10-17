<?php
require_once __DIR__ . '/includes/_bootstrap.php';
header('Content-Type: text/plain');

try {
    $db = getDB();
    echo "Connexion BDD : OK\n";

    $query = "
        CREATE TABLE IF NOT EXISTS agenda_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            client_name VARCHAR(255) NULL,
            plate_number VARCHAR(50) NULL,
            event_date DATE NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            status ENUM('PENDING', 'IN_PROGRESS', 'DONE', 'CANCELED') DEFAULT 'PENDING',
            mechanic_name VARCHAR(150) NULL,
            created_by INT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($query);
    echo "Table agenda_events : CRÉÉE / VÉRIFIÉE\n";

    $stmt = $db->query("SHOW TABLES LIKE 'agenda_events'");
    if ($stmt->fetch()) {
        echo "Vérification Finale : LA TABLE EXISTE BIEN.\n";
    } else {
        echo "Vérification Finale : ÉCHEC, TABLE INTROUVABLE.\n";
    }

} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}
