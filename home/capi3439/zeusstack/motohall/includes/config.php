<?php
/**
 * AEGIS CONFIGURATION - v4.6 (PERFORMANCE BOOST)
 * Note : Utilisation de 127.0.0.1 pour eviter le lag DNS de Windows.
 */
define('SITE_NAME', 'MotoHall 2A');
define('SITE_URL', 'http://127.0.0.1/motohall2a'); // Passage d'IP en IP directe

// Base de Donnees WAMP (Optimization)
define('DB_HOST', '127.0.0.1'); // FIX : On force l'IP pour supprimer la lenteur
define('DB_NAME', 'motohall2a');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4'); // Standard moderne 2026

// Chemins Internes
define('ROOT_PATH', realpath(__DIR__ . '/../') . '/');
define('INC_PATH', __DIR__ . '/');

// Parametres PHP
date_default_timezone_set('Europe/Paris');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
