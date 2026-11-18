<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * INDEX.PHP — LA PAGE D'ENTRÉE DU SITE
 *
 * REFAIT LE 18/11/2026. C'est le changement le plus important de la journée,
 * et voici pourquoi.
 *
 * CE QUI ÉTAIT FAUX
 * -----------------
 * La racine affichait un « portail » PUBLIC qui présentait les trois univers de
 * l'entreprise — Comptoir, Atelier, Direction — avec, pour chacun, la liste de
 * ses accès : « Espace de vente », « Agenda des interventions », « Tableau de
 * bord », « Membres du personnel », « Documentation stratégiques et RH »…
 *
 * C'était une DIVULGATION D'INFORMATION, et Frédéric l'a dit en une phrase :
 * « là on donne des infos aux hackers sur le contenu ». Il a raison. Un
 * visiteur anonyme n'avait pas besoin d'ouvrir quoi que ce soit : la page lui
 * donnait la carte du bâtiment, le nom des salles et le contenu de chacune.
 * C'est exactement ce qu'on cherche quand on prépare une attaque.
 *
 * CE QUE FAIT CETTE PAGE MAINTENANT
 * ---------------------------------
 *   - VISITEUR NON IDENTIFIÉ : une page de connexion, et RIEN d'autre. Pas un
 *     nom d'espace, pas un compteur, pas une liste. On ne sait même pas
 *     combien de services existent.
 *   - PERSONNE IDENTIFIÉE : le portail, avec SES espaces — comme avant.
 *
 * ÇA N'ENLÈVE RIEN AU TRAVAIL, ÇA L'AJOUTE
 * ----------------------------------------
 * Le portail n'est pas supprimé : il est déplacé APRÈS la porte. On y arrive
 * en se connectant, ce qui est le chemin normal de toute façon.
 *
 * L'authentification vit ici, et une seule fois : `login.php` ne fait plus que
 * renvoyer ici. Un seul endroit où l'on vérifie un mot de passe, c'est un seul
 * endroit à contrôler.
 * ═══════════════════════════════════════════════════════════════════════════
 */

// La page est publique au sens de l'orchestrateur : c'est elle qui montre la
// porte d'entrée, donc elle ne peut pas exiger une session pour s'afficher.
define('PAGE_PUBLIC', true);
require_once __DIR__ . '/amorce.php';

$connecte = isset($_SESSION['user_id']);
$error    = null;

/* ─── 1. L'IDENTIFICATION ─────────────────────────────────────────────────
   Seul endroit du site où un mot de passe est vérifié. */

if (!$connecte && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    // Une saisie vide ne déclenche même pas de requête : c'est une politesse
    // et cela évite de solliciter la base pour rien.
    if ($username === '' || $password === '') {
        $error = 'Merci de renseigner votre identifiant et votre mot de passe.';
    } else {
        try {
            // !! « AND actif = 1 » A ÉTÉ AJOUTÉ LE 18/11/2026, ET C'ÉTAIT UN
            //    TROU : la requête ne regardait pas l'état du compte. Un compte
            //    DÉSACTIVÉ depuis « Membres du personnel » pouvait donc encore
            //    se connecter, avec son ancien mot de passe. La désactivation
            //    ne désactivait rien.
            $stmt = $pdo->prepare(
                "SELECT id, username, password, role, prenom, nom
                   FROM users
                  WHERE username = ? AND actif = 1
                  LIMIT 1"
            );
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Un seul et même message, que le compte existe ou non, et que le
            // mot de passe soit bon ou non : cela empêche de deviner quels
            // identifiants existent. C'est volontaire.
            if ($user && password_verify($password, (string)$user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role_name'] = strtoupper((string)$user['role']);
                $_SESSION['prenom']    = (string)($user['prenom'] ?? '');
                $_SESSION['nom']       = (string)($user['nom'] ?? '');

                // On renvoie sur la racine : la page s'affichera alors dans sa
                // version « connectée », c'est-à-dire le portail.
                header('Location: index.php');
                exit;
            }

            $error = 'Identifiant ou mot de passe incorrect.';
        } catch (Exception $e) {
            error_log('LOGIN_ERROR: ' . $e->getMessage());
            $error = 'La connexion est momentanément impossible. Réessayez dans un instant.';
        }
    }
}

/* ─── 2. LES MESSAGES VENUS D'AILLEURS ────────────────────────────────────
   Les autres pages renvoient ici avec un motif dans l'adresse. */

if ($error === null) {
    if (isset($_GET['connexion_requise'])) {
        $error = "Merci de vous identifier pour ouvrir cet espace.";
    } elseif (isset($_GET['error']) && $_GET['error'] === 'access_denied') {
        $error = "Cet espace ne fait pas partie de votre profil.";
    } elseif (isset($_GET['status']) && $_GET['status'] === 'logged_out') {
        $error = "Vous êtes bien déconnecté.";
    }
}

/* ─── 3. LE RENDU ─────────────────────────────────────────────────────────
   Deux pages, deux gabarits, et le visiteur ne voit que le premier. */

if (!$connecte) {
    require_once ROOT_PATH . 'templates/layout_connexion.php';
    exit;
}

$pageTitle = "Portail";
$pageView  = "index_view";

require_once ROOT_PATH . 'templates/layout_secure.php';
