<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LAYOUT_SECURE.PHP — COQUE DE L'INTRANET
 *
 * Refonte du 20/09/2026, puis reprise du 19/10/2026 :
 *   - le thème n'est plus écrit en dur : il est porté par l'attribut
 *     data-theme posé sur <html>, avec deux jeux de valeurs (voir aegis.css) ;
 *   - la page déclare explicitement aux moteurs de recherche de NE PAS
 *     l'indexer : c'est un outil de travail interne, pas un site public ;
 *   - un lien « aller au contenu » est ajouté pour la navigation au clavier.
 * ═══════════════════════════════════════════════════════════════════════════
 */

if (!isset($pageView))
    stop("Erreur de rendu\xC2\xA0: fragment de vue manquant.");

// MODE FRAGMENT (navigation instantanée) : on ne rend que la vue.
if (defined('IS_FRAGMENT') && IS_FRAGMENT) {
    require_once ROOT_PATH . 'templates/views/' . $pageView . '.php';
    exit;
}

// Le site est à la racine du sous-domaine en production, dans un sous-dossier
// sur le poste. Les adresses des fichiers sont donc calculées, jamais écrites.
$baseAssets = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'))), '/') . '/';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="sombre">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($pageTitle ?? 'Direction', ENT_QUOTES, 'UTF-8') ?></title>

    <!-- ─── PAS D'INDEXATION (19/10/2026) ───────────────────────────────────
         MotoHall 2A est l'outil de travail d'une concession : il n'a rien à
         faire dans un moteur de recherche. Trois verrous sont posés, et il en
         faut trois, car chacun peut être contourné seul :
           1. cette balise, pour les moteurs qui lisent le HTML ;
           2. l'en-tête X-Robots-Tag dans .htaccess, pour les fichiers qui ne
              sont pas du HTML (CSS, images, documents…) ;
           3. un fichier robots.txt à la racine, pour les robots bien élevés.
      -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex, notranslate">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta name="bingbot" content="noindex, nofollow">
    <meta name="theme-color" content="#020617">

    <!-- ─── THÈME : CE SCRIPT PASSE AVANT LE DESSIN DE LA PAGE ──────────────
         Il est volontairement dans le <head> et sans « defer » : il doit
         poser l'attribut data-theme AVANT que la page soit peinte. Sinon une
         personne qui travaille en clair verrait un éclair sombre à chaque
         changement de page. Il pèse moins de 3 Ko et ne dépend de rien.
      -->
    <script src="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/js/theme.js?v=20261019"></script>

    <!-- ─── POLICES ─────────────────────────────────────────────────────────
         Seule ressource encore servie de l'extérieur.
      -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- !! L'ORDRE DU <head> A ÉTÉ CORRIGÉ LE 19/10/2026, ET C'EST UN VRAI
         DÉFAUT QUI A ÉTÉ VU À L'ÉCRAN. Les deux feuilles de style étaient
         déclarées APRÈS les scripts, dont `lucide.min.js` — 442 433 octets,
         chargés en bloquant. Tant que ce fichier n'est pas arrivé, le
         navigateur n'a pas seulement attendu le script : il n'avait même pas
         ENCORE DEMANDÉ les feuilles de style, puisqu'elles venaient après.
         Résultat mesuré sur une capture du site en ligne : la page s'affichait
         SANS AUCUN STYLE pendant tout le téléchargement de Lucide.
         Les feuilles passent donc en premier : elles partent immédiatement,
         en parallèle, et la page est peinte dès qu'elles arrivent. -->

    <!-- ─── FEUILLE DE STYLE COMPILÉE ───────────────────────────────────────
         Fabriquée une fois pour toutes par l'atelier Tailwind v4
         (_travail\outils_chantier\tailwind-v4 -> npm run build).
      -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/css/tailwind.css?v=20260921">

    <!-- !! ORDRE IMPORTANT : aegis.css vient APRÈS tailwind.css. C'est elle qui
         porte la charte, les deux thèmes et le pont vers les anciennes classes.
         Si on l'inverse, la feuille compilée reprend le dessus. -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/css/aegis.css?v=20261019">

    <!-- ─── BIBLIOTHÈQUES SERVIES PAR LE SITE ───────────────────────────────
         Versions figées, dans le site : Alpine 3.17.3 et Lucide 1.47.0.
      -->
    <script defer src="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/js/alpine.min.js?v=20260921"></script>
    <script src="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/js/lucide.min.js?v=20260921"></script>

    <link rel="icon" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-180.png">
</head>

<body class="flex h-screen overflow-hidden font-sans"
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

    <a href="#contenu" class="lien-evitement">Aller au contenu</a>

    <!-- ─── LISERÉ DE CHARGEMENT ──────────────────────────────────────────── -->
    <div x-show="loading" x-transition.opacity
         class="fixed top-0 left-0 right-0 h-[2px] bg-[#38bdf8] z-[9999]"></div>

    <!-- ─── NOTIFICATION ──────────────────────────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="toast.show" x-transition.opacity.duration.200ms
             class="fixed top-5 right-5 z-[200]">
            <div class="notif" :class="toast.type === 'success' ? 'notif--succes' : 'notif--erreur'"
                 role="status" aria-live="polite">
                <span class="notif-pastille">
                    <i :data-lucide="toast.type === 'success' ? 'check' : 'alert-triangle'" class="w-4 h-4"></i>
                </span>
                <div class="min-w-0">
                    <p class="notif-titre" x-text="toast.type === 'success' ? 'Enregistré' : 'Attention'"></p>
                    <p class="notif-texte" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false"
                        class="ml-2 shrink-0 text-slate-500 hover:text-slate-300"
                        aria-label="Fermer la notification">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </template>

    <!-- ─── CONFIRMATION ──────────────────────────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="confirmData.show" class="modale-voile" style="display:none;"
             role="alertdialog" aria-modal="true" aria-labelledby="confirmation-titre">
            <div class="modale-panneau !max-w-sm text-center" @click.away="confirmData.show = false">
                <div class="w-11 h-11 rounded-full bg-red-500/10 border border-red-500/20 flex items-center
                            justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400"></i>
                </div>
                <h3 id="confirmation-titre" class="font-display text-[1.05rem] mb-2">Confirmation</h3>
                <p class="text-[0.92rem] text-slate-400 mb-6" x-text="confirmData.message"></p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="confirmData.show = false" class="btn btn-neutre">Annuler</button>
                    <button type="button" @click="confirmAction()" class="btn btn-danger">Confirmer</button>
                </div>
            </div>
        </div>
    </template>

    <!-- ─── MENU (seulement si la personne est identifiée) ────────────────── -->
    <?php $connecte = isset($_SESSION['user_id']); ?>

    <?php if ($connecte): ?>
        <div x-show="menuOuvert" x-transition.opacity @click="menuOuvert = false"
             class="voile-menu" style="display:none;" aria-hidden="true"></div>

        <?php require_once __DIR__ . '/sidebar.php'; ?>
    <?php endif; ?>

    <!-- ─── COLONNE PRINCIPALE ────────────────────────────────────────────── -->
    <!-- !! min-h-0 EST INDISPENSABLE : dans une colonne souple (flexbox), un
         enfant ne défile correctement que si son parent accepte de rétrécir.
         Sans lui, c'est TOUTE la page qui défile et l'en-tête part avec le
         contenu, au lieu de rester fixe en haut. -->
    <div class="flex-1 flex flex-col h-full min-w-0 min-h-0">

        <?php require_once __DIR__ . '/header_ui.php'; ?>

        <main id="contenu" class="contenu-page flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-6 transition-opacity duration-200"
              x-ref="content" tabindex="-1">
            <?php require_once ROOT_PATH . 'templates/views/' . $pageView . '.php'; ?>
        </main>

        <?php require_once __DIR__ . '/footer_ui.php'; ?>
    </div>

    <script>if (window.lucide) lucide.createIcons();</script>
</body>

</html>
