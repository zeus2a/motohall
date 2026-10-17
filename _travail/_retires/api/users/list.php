<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * AEGIS API - USER LIST v5.0
 * ═══════════════════════════════════════════════════════════════════════════
 */

// L'initialisation est gérée par api/index.php (_bootstrap.php)

try {
    $stmt = $pdo->query("SELECT id, username, nom, prenom, email, role, actif, date_creation FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'count' => count($users),
        'users' => $users
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
