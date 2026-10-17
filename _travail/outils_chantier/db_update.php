<?php
require_once __DIR__ . '/includes/config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier les colonnes
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $added = [];
    if (!in_array('role', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'VENDEUR'");
        $added[] = 'role';
    }
    if (!in_array('nom', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN nom VARCHAR(100) DEFAULT ''");
        $added[] = 'nom';
    }
    if (!in_array('prenom', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN prenom VARCHAR(100) DEFAULT ''");
        $added[] = 'prenom';
    }
    if (!in_array('email', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255) DEFAULT ''");
        $added[] = 'email';
    }

    echo "UPDATE SUCCESS: " . implode(', ', $added);
    
    // S'assurer que zeus est ADMIN
    $pdo->exec("UPDATE users SET role = 'ADMIN' WHERE username = 'zeus'");

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
