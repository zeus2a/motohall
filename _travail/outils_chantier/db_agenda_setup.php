<?php
/**
 * SETUP DB AGENDA
 */
require_once __DIR__ . '/includes/_auth.php';

try {
    $db = getDB();

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
    echo "TABLE agenda_events CREATED SUCCESSFULLY.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
