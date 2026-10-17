<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * PROFIL.PHP — MON COMPTE
 * Créé le 20/09/2026.
 *
 * Chaque personne connectée peut consulter son profil et changer SON mot de
 * passe. Personne ne peut modifier le compte d'un autre ici : la gestion des
 * comptes est réservée à la Direction, depuis « Membres du staff ».
 *
 * Sécurité : le mot de passe actuel est exigé (pour qu'une session laissée
 * ouverte ne suffise pas à changer le mot de passe), un jeton CSRF est vérifié,
 * et le nouveau mot de passe est enregistré HACHÉ (bcrypt).
 * ═══════════════════════════════════════════════════════════════════════════
 */

require_once __DIR__ . '/amorce.php';

$message = null;
$erreur  = null;

// Informations affichées (celles de la personne connectée).
$monCompte = aegis_get_user($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $erreur = "Session expirée. Merci de réessayer.";
    } else {
        $actuel   = (string)($_POST['actuel'] ?? '');
        $nouveau  = (string)($_POST['nouveau'] ?? '');
        $confirme = (string)($_POST['confirme'] ?? '');

        if ($monCompte === null) {
            $erreur = "Compte introuvable.";
        } elseif ($actuel === '' || $nouveau === '') {
            $erreur = "Merci de remplir les trois champs.";
        } elseif (!password_verify($actuel, (string)$monCompte['password'])) {
            $erreur = "Le mot de passe actuel est incorrect.";
        } elseif (strlen($nouveau) < 8) {
            $erreur = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
        } elseif ($nouveau !== $confirme) {
            $erreur = "Les deux saisies du nouveau mot de passe ne sont pas identiques.";
        } elseif ($nouveau === $actuel) {
            $erreur = "Le nouveau mot de passe est identique à l'ancien.";
        } else {
            try {
                $maj = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $maj->execute([
                    password_hash($nouveau, PASSWORD_DEFAULT),
                    (int)$_SESSION['user_id'],
                ]);
                $message = "Votre mot de passe a bien été changé.";
                // L'ancien hachage ne doit plus traîner en mémoire.
                $monCompte['password'] = '';
            } catch (Exception $e) {
                error_log('PROFIL_PASSWORD: ' . $e->getMessage());
                $erreur = "Le changement n'a pas pu être enregistré. Réessayez dans un instant.";
            }
        }
    }
}

$pageTitle = "Mon compte";
$pageView  = "profil_view";

require_once ROOT_PATH . 'templates/layout_secure.php';
