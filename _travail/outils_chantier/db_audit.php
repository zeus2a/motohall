<?php 
require_once 'includes/config.php';
try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $res = $db->query('DESCRIBE users');
    echo json_encode($res->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "SQL ERROR: " . $e->getMessage();
}
