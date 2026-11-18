<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ADMIN-USERS.PHP v5.0 - LA CONSOLE AGENTS AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

// 1. Sécurisation (ADMIN UNIQUEMENT - ACCÈS RÉSERVÉ)
require_once __DIR__ . '/amorce.php';

if (strtoupper(getRole()) !== 'ADMIN') {
    header('Location: dashboard.php?error=accès_réservé_à_l_administrateur');
    exit;
}

$error = null;
$success = null;

// 2. Traitement des Actions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Vérification CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = "Alerte de sécurité\xC2\xA0: jeton CSRF invalide.";
    } else {
        
        // --- AJOUT AGENT ---
        if ($_POST['action'] === 'add') {
            try {
                // !! CORRIGÉ LE 20/09/2026 : on n'encode PLUS à l'enregistrement.
                //    Avant, clean() appliquait htmlspecialchars AVANT la base, puis
                //    l'affichage l'appliquait une seconde fois : un agent nommé
                //    « D&G » était stocké « D&amp;G » et affiché « D&amp;amp;G ».
                //    L'échappement se fait maintenant UNIQUEMENT à l'affichage.
                $username = trim((string)$_POST['username']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $role     = trim((string)$_POST['role']);
                $email    = trim((string)($_POST['email'] ?? ''));
                $nom      = trim((string)($_POST['nom'] ?? ''));
                $prenom   = trim((string)($_POST['prenom'] ?? ''));

                $insert = $pdo->prepare("INSERT INTO users (username, password, role, email, nom, prenom, actif, date_creation)
                                         VALUES (:user, :pass, :role, :email, :nom, :prenom, 1, NOW())");
                $insert->execute([
                    'user'   => $username,
                    'pass'   => $password,
                    'role'   => $role,
                    'email'  => $email,
                    'nom'    => $nom,
                    'prenom' => $prenom
                ]);
                $success = "Le compte «\xC2\xA0" . $username . "\xC2\xA0» a été créé.";
            } catch (Exception $e) {
                $error = "Échec du déploiement du membre\xC2\xA0: " . $e->getMessage();
            }
        }

        // --- ÉDITION MEMBRE ---
        if ($_POST['action'] === 'edit') {
            try {
                $id = (int)$_POST['user_id'];
                // !! Même correction que pour l'ajout : plus d'encodage ici.
                $username = trim((string)$_POST['username']);
                $role     = trim((string)$_POST['role']);
                $nom      = trim((string)($_POST['nom'] ?? ''));
                $prenom   = trim((string)($_POST['prenom'] ?? ''));
                $email    = trim((string)($_POST['email'] ?? ''));

                $params = [
                    'user'   => $username,
                    'role'   => $role,
                    'nom'    => $nom,
                    'prenom' => $prenom,
                    'email'  => $email,
                    'id'     => $id
                ];

                $sql = "UPDATE users SET username = :user, role = :role, nom = :nom, prenom = :prenom, email = :email";
                
                // Si un nouveau mot de passe est saisi
                if (!empty($_POST['password'])) {
                    $sql .= ", password = :pass";
                    $params['pass'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $sql .= " WHERE id = :id";
                
                $update = $pdo->prepare($sql);
                $update->execute($params);
                $success = "Le profil de «\xC2\xA0" . $username . "\xC2\xA0» a été mis à jour avec succès.";
            } catch (Exception $e) {
                $error = "Échec de la modification\xC2\xA0: " . $e->getMessage();
            }
        }
    }
}

// 3. Traitement des Actions (GET - Toggle Statut)
if (isset($_GET['toggle'])) {
    if (!verify_csrf($_GET['csrf_token'] ?? '')) {
        $error = "Alerte de sécurité\xC2\xA0: jeton CSRF invalide.";
    } else {
        $id = (int)$_GET['toggle'];
        try {
            $pdo->prepare("UPDATE users SET actif = IF(actif=1,0,1) WHERE id = ?")->execute([$id]);
            header('Location: admin-users.php?success=statut_updated');
            exit;
        } catch (Exception $e) {
            $error = "Erreur de base de données\xC2\xA0: " . $e->getMessage();
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'statut_updated') {
    $success = "Statut du membre mis à jour avec succès.";
}

// 4. Récupération des Données
$users = $pdo->query("SELECT id, username, nom, prenom, email, role, actif, date_creation FROM users ORDER BY role ASC, username ASC")->fetchAll();

// 5. Rendu Layout
$pageTitle = "Membres du personnel";
$pageView  = "admin_users_view";

require_once ROOT_PATH . 'templates/layout_secure.php';
