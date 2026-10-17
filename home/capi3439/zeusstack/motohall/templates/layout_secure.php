<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LAYOUT_SECURE.PHP — COQUE DE L'ESPACE DIRECTION
 * Refonte visuelle du 20/09/2026.
 *
 * CE QUI A CHANGÉ :
 *   - les bips de notification (fichiers audio tronqués dans le code) sont
 *     RETIRÉS : ils ne fonctionnaient pas et faisaient « gadget » ;
 *   - l'écran de chargement plein écran devient un fin liseré en haut de page ;
 *   - les couleurs des notifications suivent la palette (succès = vert doux,
 *     alerte = rouge doux) au lieu de néons ;
 *   - la fenêtre de confirmation est sobre et lisible.
 * ═══════════════════════════════════════════════════════════════════════════
 */

if (!isset($pageView))
    stop("Erreur de rendu : Fragment de vue manquant.");

// MODE FRAGMENT (navigation instantanée) : on ne rend que la vue.
if (defined('IS_FRAGMENT') && IS_FRAGMENT) {
    require_once ROOT_PATH . 'templates/views/' . $pageView . '.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($pageTitle ?? 'Direction', ENT_QUOTES, 'UTF-8') ?></title>

    <!-- ─── FEUILLE DE STYLE COMPILÉE (21/09/2026) ──────────────────────────
         AVANT : https://cdn.tailwindcss.com — le « Play CDN » — fabriquait les
         styles DANS LE NAVIGATEUR, à chaque page chargée. C'est son éditeur
         lui-même qui déconseille ce procédé en production.

         MAINTENANT : la feuille est fabriquée UNE FOIS POUR TOUTES, sur le
         poste, par Tailwind 3.4.17 — exactement la version que le CDN servait
         (mesuré le 21/09/2026). Résultat : 23 741 octets au lieu de 407 279
         octets de programme téléchargé et exécuté à chaque visite.

         ⚠️ POUR LA REFAIRE après une modification de page (un fichier CSS ne se
            met pas à jour tout seul — c'est le prix, et il est faible) :
              C:\zeus_dev\zeus_projets\Sites_Clients\motohall2a_travail\
                  outils_chantier\tailwind\        ->  npm run build
            Le fichier produit va tout seul dans le site. Il ne reste qu'à
            l'envoyer sur le serveur.
      -->
    <link rel="stylesheet" href="assets/css/tailwind.css?v=20260921">

    <!-- ─── BIBLIOTHÈQUES SERVIES PAR LE SITE (21/09/2026) ──────────────────
         AVANT : « alpinejs@3.x.x » et « lucide@latest », chez unpkg.com.
         Deux adresses qui veulent dire « la dernière version » : elles peuvent
         donc changer sous nos pieds un matin sans qu'on ait rien touché, et
         elles dépendaient d'un site extérieur au nôtre.

         MAINTENANT : les deux fichiers sont DANS le site, à des versions
         FIGÉES — Alpine 3.17.3 et Lucide 1.47.0, mesurées le 21/09/2026 :
         c'est exactement ce que le CDN servait ce jour-là, donc rien ne change
         à l'affichage, mais plus rien ne peut changer tout seul.
      -->
    <script defer src="assets/js/alpine.min.js?v=20260921"></script>
    <script src="assets/js/lucide.min.js?v=20260921"></script>

    <!-- Polices : seul ressource encore servie de l'extérieur. Les adresses
         fixent déjà les graisses, donc rien ne peut changer tout seul ; elle
         pourra être rapatriée plus tard si on veut couper le dernier lien. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="assets/img/favicon-180.png">

    <!-- !! ORDRE IMPORTANT : aegis.css vient APRÈS tailwind.css. C'est elle qui
         porte la charte (couleurs, formes, briques) et qui corrige les restes
         d'avril. Si on l'inverse, la feuille compilée reprend le dessus et le
         site change d'allure. -->
    <link rel="stylesheet" href="assets/css/aegis.css?v=20260921">
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

    <!-- ─── LISERÉ DE CHARGEMENT (remplace l'écran plein) ──────────────── -->
    <div x-show="loading" x-transition.opacity
         class="fixed top-0 left-0 right-0 h-[2px] bg-[#38bdf8] z-[9999]"></div>

    <!-- ─── NOTIFICATION (structure reprise de Novaya Suites) ──────────── -->
    <template x-teleport="body">
        <div x-show="toast.show" x-transition.opacity.duration.200ms
             class="fixed top-5 right-5 z-[200]">
            <div class="notif" :class="toast.type === 'success' ? 'notif--succes' : 'notif--erreur'">
                <span class="notif-pastille">
                    <i :data-lucide="toast.type === 'success' ? 'check' : 'alert-triangle'" class="w-4 h-4"></i>
                </span>
                <div class="min-w-0">
                    <p class="notif-titre" x-text="toast.type === 'success' ? 'Enregistré' : 'Attention'"></p>
                    <p class="notif-texte" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false"
                        class="ml-2 shrink-0 text-slate-500 hover:text-slate-300">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </template>

    <!-- ─── CONFIRMATION ───────────────────────────────────────────────── -->
    <template x-teleport="body">
        <div x-show="confirmData.show" class="modale-voile" style="display:none;">
            <div class="modale-panneau !max-w-sm text-center" @click.away="confirmData.show = false">
                <div class="w-11 h-11 rounded-full bg-red-500/10 border border-red-500/20 flex items-center
                            justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400"></i>
                </div>
                <h3 class="font-display text-[1rem] text-white mb-2">Confirmation</h3>
                <p class="text-[0.85rem] text-slate-400 mb-6" x-text="confirmData.message"></p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="confirmData.show = false" class="btn btn-neutre">Annuler</button>
                    <button type="button" @click="confirmAction()" class="btn btn-danger">Confirmer</button>
                </div>
            </div>
        </div>
    </template>

    <!-- ─── MENU (seulement si la personne est identifiée) ─────────────── -->
    <?php $connecte = isset($_SESSION['user_id']); ?>

    <?php if ($connecte): ?>
        <!-- Voile du menu (petit écran uniquement) -->
        <div x-show="menuOuvert" x-transition.opacity @click="menuOuvert = false"
             class="voile-menu" style="display:none;"></div>

        <?php require_once __DIR__ . '/sidebar.php'; ?>
    <?php endif; ?>

    <!-- ─── COLONNE PRINCIPALE ─────────────────────────────────────────── -->
    <!-- !! min-h-0 EST INDISPENSABLE : dans une colonne souple (flexbox), un
         enfant ne défile correctement que si son parent accepte de rétrécir.
         Sans lui, c'est TOUTE la page qui défile et l'en-tête part avec le
         contenu, au lieu de rester fixe en haut. -->
    <div class="flex-1 flex flex-col h-full min-w-0 min-h-0">

        <?php require_once __DIR__ . '/header_ui.php'; ?>

        <main class="contenu-page flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-6 transition-opacity duration-200"
              x-ref="content">
            <?php require_once ROOT_PATH . 'templates/views/' . $pageView . '.php'; ?>
        </main>

        <?php require_once __DIR__ . '/footer_ui.php'; ?>
    </div>

    <script>if (window.lucide) lucide.createIcons();</script>
</body>

</html>
