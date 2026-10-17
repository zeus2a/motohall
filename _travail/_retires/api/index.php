<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * API INDEX.PHP v5.0 - LE ROUTEUR REST AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

header('Content-Type: application/json; charset=utf-8');

// 1. Initialisation Core (Léger)
require_once __DIR__ . '/../includes/_bootstrap.php';

// 2. Détection dynamique de la route
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); // e.g. /motohall2a/api
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = trim(str_replace($scriptDir, '', $uri), '/');

// 3. Routage Centralisé
try {
    switch (true) {
        
        // --- AUTHENTIFICATION ---
        case (strpos($route, 'auth/') === 0):
            $file = __DIR__ . '/' . $route . '.php';
            if (file_exists($file)) {
                require $file;
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint Auth introuvable']);
            }
            break;

        // --- AUTRES (Verrouillés par défaut) ---
        default:
            // Protection API Systemielle
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                die(json_encode(['error' => 'Authentification requise pour cette route.']));
            }

            // Tentative de chargement du script
            $file = __DIR__ . '/' . $route . '.php';
            if (file_exists($file)) {
                require $file;
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint introuvable', 'route' => $route]);
            }
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage()
    ]);
}
