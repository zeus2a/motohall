-- Tables du site MotoHall 2A — base de PRODUCTION capi3439_motohall
-- Creees le 20/09/2026 par l'agent DSH.
-- Structures reprises a l'identique des scripts du projet (db_force_check.php,
-- db_update.php). AUCUN DROP : ces ordres ne peuvent rien detruire.
-- Base neuve et vide, comme decide par Frederic.

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'VENDEUR',
    nom VARCHAR(100) DEFAULT '',
    prenom VARCHAR(100) DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    actif TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

SHOW TABLES;
DESCRIBE users;
