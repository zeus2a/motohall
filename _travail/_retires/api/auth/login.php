<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * AEGIS API - AUTH LOGIN v5.0
 * ═══════════════════════════════════════════════════════════════════════════
 */

// L'initialisation est gérée par api/index.php (_bootstrap.php)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = clean($input['username'] ?? $_POST['username'] ?? '');
$password = $input['password'] ?? $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Identifiants requis']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // !! CORRIGÉ LE 20/09/2026 : le repli « support legacy text clair » a été
        //    retiré ici aussi (voir login.php) — seul un hachage bcrypt est accepté.
        $valid = password_verify($password, $user['password']);

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_name'] = strtoupper($user['role']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Authentification réussie',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => strtoupper($user['role'])
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Mot de passe incorrect']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Compte introuvable']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur : ' . $e->getMessage()]);
}
