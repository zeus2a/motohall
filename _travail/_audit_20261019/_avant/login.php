<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LOGIN.PHP — CONNEXION À L'ESPACE DIRECTION
 * Refonte visuelle du 20/09/2026 : page sobre, centrée, lisible.
 *
 * RAPPEL DE SÉCURITÉ (corrigé le 20/09/2026) : seul un mot de passe HACHÉ est
 * accepté. La comparaison « en clair » qui existait ici a été retirée.
 * ═══════════════════════════════════════════════════════════════════════════
 */

// 1. Déclarer la page comme publique pour l'orchestrateur
define('PAGE_PUBLIC', true);
require_once __DIR__ . '/amorce.php';

// 2. Si déjà connecté, redirection vers le tableau de bord
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

// 3. Logique de Connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, role, prenom, nom FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // !! Seul un hachage bcrypt est accepté (voir la note en tête de fichier).
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role_name'] = strtoupper($user['role']);
                // Le prénom sert à saluer la personne sur l'accueil (20/09/2026).
                $_SESSION['prenom']    = (string)($user['prenom'] ?? '');
                $_SESSION['nom']       = (string)($user['nom'] ?? '');

                header('Location: index.php');
                exit;
            }

            $error = "Identifiants invalides.";
        } else {
            $error = "Identifiants invalides.";
        }
    } catch (Exception $e) {
        error_log('LOGIN_ERROR: ' . $e->getMessage());
        $error = "Connexion impossible pour le moment. Réessayez dans un instant.";
    }
}

// 4. Chemin de base des fichiers du site (ajouté le 21/09/2026).
//    Les mêmes raisons que dans error.php : les adresses des fichiers sont
//    CALCULÉES, pas écrites, pour continuer de marcher aussi bien en production
//    (« / ») que sur le poste de Frédéric (« /motohall2a »).
$base = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/login.php'))), '/') . '/';
?>
<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- ─── FEUILLE COMPILÉE + LUCIDE, SERVIES PAR LE SITE (21/09/2026) ─────
         AVANT : le « Play CDN » de Tailwind et « lucide@latest » chez unpkg.com —
         donc deux adresses extérieures, et une version qui pouvait changer
         toute seule. MAINTENANT : deux fichiers du site, à versions figées.
         C'est la PREMIÈRE page que voit l'équipe : elle ne dépend plus de
         personne d'autre que nous.
      -->
    <link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/css/tailwind.css?v=20260921">
    <script src="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/js/lucide.min.js?v=20260921"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-180.png">

    <link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/css/aegis.css?v=20260921">
</head>

<body class="min-h-screen bg-[#020617] text-slate-200 font-sans flex flex-col">

    <header class="border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-3">
            <div class="w-9 h-9 rounded-[10px] bg-[#38bdf8] flex items-center justify-center
                        text-[#04121c] font-display font-bold text-sm">MH</div>
            <span class="font-display text-[0.98rem] font-semibold text-white">MotoHall 2A</span>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[400px] apparition">

            <div class="mb-8">
                <p class="titre-section mb-2">Intranet du personnel</p>
                <h1 class="font-display text-2xl font-semibold text-white">Connexion</h1>
                <p class="mt-2 text-[0.87rem] text-slate-400">
                    Identifiez-vous pour accéder à MotoHall 2A. Chaque page est réservée au
                    personnel : votre profil détermine ensuite les espaces que vous verrez.
                </p>
            </div>

            <?php if ($error): ?>
            <div class="mb-5 flex items-start gap-3 rounded-[12px] border border-red-500/30 bg-red-500/10 px-4 py-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 mt-0.5 shrink-0"></i>
                <p class="text-[0.85rem] text-red-300"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['connexion_requise'])): ?>
            <div class="mb-5 flex items-start gap-3 rounded-[12px] border border-white/10 bg-white/[0.03] px-4 py-3">
                <i data-lucide="lock" class="w-5 h-5 text-slate-400 mt-0.5 shrink-0"></i>
                <p class="text-[0.85rem] text-slate-300">
                    Merci de vous identifier pour continuer.
                </p>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'logged_out'): ?>
            <div class="mb-5 flex items-start gap-3 rounded-[12px] border border-emerald-500/30 bg-emerald-500/10 px-4 py-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 mt-0.5 shrink-0"></i>
                <p class="text-[0.85rem] text-emerald-300">Vous êtes bien déconnecté.</p>
            </div>
            <?php endif; ?>

            <form method="POST" class="carte">
                <div class="mb-4">
                    <label for="username" class="etiquette">Identifiant</label>
                    <input type="text" id="username" name="username" required autocomplete="username"
                           autofocus class="champ" placeholder="Votre identifiant">
                </div>

                <div class="mb-6">
                    <label for="password" class="etiquette">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                           autocomplete="current-password" class="champ" placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-accent btn-large btn-bloc">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Se connecter</span>
                </button>
            </form>

            <!--
              !! 20/09/2026 : il y avait ici un lien « Retour au portail » vers
                 index.php. Comme TOUTES les pages exigent désormais une
                 connexion, ce lien renvoyait vers cette même page : une boucle.
                 Il a donc été retiré.
            -->
            <p class="mt-6 text-center text-[0.78rem] text-slate-500">
                Identifiants oubliés ? Adressez-vous à la Direction.
            </p>
        </div>
    </main>

    <footer class="border-t border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-12 flex items-center justify-between text-[0.72rem] text-slate-500">
            <span>Intranet MotoHall 2A</span>
            <span><?= date('Y') ?></span>
        </div>
    </footer>

    <script>if (window.lucide) lucide.createIcons();</script>
</body>

</html>
