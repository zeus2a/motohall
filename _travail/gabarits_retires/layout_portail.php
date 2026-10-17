<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LAYOUT_PORTAIL.PHP — COQUE DU PORTAIL D'ACCUEIL
 * Créé le 20/09/2026.
 *
 * POURQUOI UNE COQUE À PART : l'accueil était servi avec le menu latéral des
 * espaces de travail, ce qui en faisait une page comme les autres. Un portail
 * d'accueil se reconnaît à autre chose : plein écran, centré, sans menu.
 * ═══════════════════════════════════════════════════════════════════════════
 */

if (!isset($pageView)) stop("Erreur de rendu : Fragment de vue manquant.");

if (defined('IS_FRAGMENT') && IS_FRAGMENT) {
    require_once ROOT_PATH . 'templates/views/' . $pageView . '.php';
    exit;
}

$connecte = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?> — Accueil</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: { 850: '#151e2e', 900: '#0f172a', 950: '#020617' },
                        accent: { 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7' }
                    },
                    fontFamily: { sans: ['Rajdhani', 'sans-serif'], display: ['Orbitron', 'sans-serif'] }
                }
            }
        }
    </script>

    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="assets/img/favicon-180.png">

    <link rel="stylesheet" href="assets/css/aegis.css?v=20260920b">
</head>

<body class="min-h-screen bg-[#020617] text-slate-200 font-sans flex flex-col">

    <!-- ─── EN-TÊTE ────────────────────────────────────────────────────── -->
    <header class="border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[10px] bg-[#38bdf8] flex items-center justify-center
                            text-[#04121c] font-display font-bold text-sm">MH</div>
                <span class="font-display text-[0.98rem] font-semibold text-white">MotoHall 2A</span>
            </div>

            <div class="flex items-center gap-3">
                <?php if ($connecte): ?>
                    <span class="badge badge-succes hidden sm:inline-flex">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        Connecté
                    </span>
                    <a href="dashboard.php" class="btn btn-neutre">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                        <span>Tableau de bord</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-neutre">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Connexion</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- ─── CONTENU ────────────────────────────────────────────────────── -->
    <main class="flex-1 flex items-center">
        <div class="w-full max-w-6xl mx-auto px-6 py-14">
            <?php require_once ROOT_PATH . 'templates/views/' . $pageView . '.php'; ?>
        </div>
    </main>

    <!-- ─── PIED ───────────────────────────────────────────────────────── -->
    <footer class="border-t border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-12 flex items-center justify-between text-[0.72rem] text-slate-500">
            <span>Intranet MotoHall 2A</span>
            <span><?= date('Y') ?> — <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </footer>

    <script>if (window.lucide) lucide.createIcons();</script>
</body>

</html>
