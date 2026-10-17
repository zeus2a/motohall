<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LAYOUT_PUBLIC.PHP — COQUE DES ESPACES LIBRES
 * (Comptoir, Atelier, Agenda, Documentation)
 * Refonte visuelle du 20/09/2026.
 *
 * !! CORRECTION AU PASSAGE : la version précédente utilisait « confirmData »
 *    dans la fenêtre de confirmation SANS l'avoir déclaré, et sans valeur de
 *    repli : l'expression Alpine échouait et la fenêtre ne pouvait jamais se
 *    masquer. C'est réparé ici (voir x-data plus bas).
 * ═══════════════════════════════════════════════════════════════════════════
 */

if (!isset($pageView)) stop("Erreur de rendu : Fragment de vue manquant.");

// MODE FRAGMENT (navigation instantanée) : on ne rend que la vue.
if (defined('IS_FRAGMENT') && IS_FRAGMENT) {
    require_once ROOT_PATH . 'templates/views/' . $pageView . '.php';
    exit;
}

$role     = strtoupper(getRole());
$estStaff = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($pageTitle ?? 'Espaces libres', ENT_QUOTES, 'UTF-8') ?></title>

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

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="assets/img/favicon-180.png">

    <link rel="stylesheet" href="assets/css/aegis.css?v=20260920b">
</head>

<body class="flex h-screen overflow-hidden bg-[#020617] text-slate-200 font-sans"
      x-data="{
         currentPage: '<?= htmlspecialchars(basename($_SERVER['PHP_SELF']), ENT_QUOTES, 'UTF-8') ?>',
         loading: false,
         menuOuvert: false,
         time: '--:--:--',
         date: '',
         toast: { show: false, message: '', type: 'success' },
         showToast(msg, type = 'success') {
            this.toast.message = msg;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 5000);
         },
         async navigate(page) {
             if (this.currentPage === page) return;
             this.loading = true;
             try {
                 const res = await fetch(page + '?fragment=true');
                 const html = await res.text();
                 this.$refs.content.style.opacity = 0;
                 setTimeout(() => {
                    this.$refs.content.innerHTML = html;
                    this.currentPage = page;
                    window.history.pushState({}, '', page);
                    this.$refs.content.style.opacity = 1;
                    if (window.lucide) lucide.createIcons();
                    this.loading = false;
                 }, 180);
             } catch (e) { console.error('Navigation :', e); this.loading = false; }
         },
         updateClock() {
            const now = new Date();
            this.time = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            this.date = now.toLocaleDateString('fr-FR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });
         },
         confirmData: { show: false, message: '', callback: null },
         showConfirm(msg, cb) {
            this.confirmData.message = msg;
            this.confirmData.callback = cb;
            this.confirmData.show = true;
         },
         confirmAction() {
            if (this.confirmData.callback) this.confirmData.callback();
            this.confirmData.show = false;
         }
      }"
      x-init="updateClock(); setInterval(() => updateClock(), 1000);
        <?php if (isset($success) && $success): ?> showToast('<?= addslashes($success) ?>', 'success'); <?php endif; ?>
        <?php if (isset($error) && $error): ?> showToast('<?= addslashes($error) ?>', 'error'); <?php endif; ?>">

    <!-- ─── LISERÉ DE CHARGEMENT ───────────────────────────────────────── -->
    <div x-show="loading" x-transition.opacity
         class="fixed top-0 left-0 right-0 h-[2px] bg-[#38bdf8] z-[9999]"></div>

    <!-- ─── NOTIFICATION ───────────────────────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="toast.show" x-transition.opacity.duration.200ms
             class="fixed top-5 right-5 z-[9999] max-w-sm">
            <div class="carte flex items-start gap-3 py-3 px-4"
                 :class="toast.type === 'success' ? 'border-emerald-500/30' : 'border-red-500/30'">
                <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'"
                   class="w-5 h-5 mt-0.5 shrink-0"
                   :class="toast.type === 'success' ? 'text-emerald-400' : 'text-red-400'"></i>
                <p class="text-[0.85rem] text-slate-200" x-text="toast.message"></p>
                <button @click="toast.show = false" class="ml-2 text-slate-500 hover:text-slate-300">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </template>

    <!-- ─── CONFIRMATION ───────────────────────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="confirmData.show" x-transition.opacity
             class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/70 backdrop-blur-sm p-6">
            <div @click.away="confirmData.show = false" class="carte w-full max-w-sm text-center apparition">
                <div class="w-11 h-11 rounded-full bg-red-500/10 border border-red-500/20 flex items-center
                            justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400"></i>
                </div>
                <h3 class="font-display text-[1rem] text-white mb-2">Confirmation</h3>
                <p class="text-[0.85rem] text-slate-400 mb-6" x-text="confirmData.message"></p>
                <div class="grid grid-cols-2 gap-3">
                    <button @click="confirmData.show = false" class="btn btn-neutre">Annuler</button>
                    <button @click="confirmAction()" class="btn btn-danger">Confirmer</button>
                </div>
            </div>
        </div>
    </template>

    <!-- ─── VOILE DU MENU (petit écran uniquement) ─────────────────────── -->
    <div x-show="menuOuvert" x-transition.opacity @click="menuOuvert = false"
         class="voile-menu" style="display:none;"></div>

    <!-- ─── MENU DES ESPACES LIBRES ────────────────────────────────────── -->
    <?php require_once __DIR__ . '/sidebar_public.php'; ?>

    <!-- ─── COLONNE PRINCIPALE ─────────────────────────────────────────── -->
    <div class="flex-1 flex flex-col h-full min-w-0">

        <!-- En-tête : titre de la page, horloge, accès Direction -->
        <header class="h-16 shrink-0 border-b border-white/10 flex items-center justify-between gap-6 px-6
                       bg-[#020617]/85 backdrop-blur-md sticky top-0 z-40">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" @click="menuOuvert = !menuOuvert"
                        class="bouton-menu btn btn-neutre !px-2.5" title="Ouvrir le menu">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <div class="min-w-0">
                    <h1 class="titre-page truncate"><?= htmlspecialchars($pageTitle ?? 'Espaces libres', ENT_QUOTES, 'UTF-8') ?></h1>
                    <p class="sous-titre-page">
                        <?php if ($estStaff): ?>
                            Connecté : <?= htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        <?php else: ?>
                            Accès libre — aucune authentification requise
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 shrink-0">
                <span class="hidden md:block font-display text-[1.05rem] text-white tabulaire" x-text="time">--:--:--</span>
                <?php if (!$estStaff): ?>
                <a href="login.php" class="btn btn-neutre hidden sm:inline-flex">
                    <i data-lucide="shield-check"></i>
                    <span>Zone Direction</span>
                </a>
                <?php endif; ?>
            </div>
        </header>

        <main class="contenu-page flex-1 overflow-y-auto p-6 transition-opacity duration-200" x-ref="content">
            <?php require_once ROOT_PATH . 'templates/views/' . $pageView . '.php'; ?>
        </main>

        <footer class="h-9 shrink-0 border-t border-white/10 flex items-center justify-between px-6
                       text-[0.72rem] text-slate-500 bg-[#020617]/60">
            <span>MotoHall 2A — Espaces libres</span>
            <span class="hidden sm:inline"><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></span>
        </footer>
    </div>

    <script>if (window.lucide) lucide.createIcons();</script>
</body>

</html>
