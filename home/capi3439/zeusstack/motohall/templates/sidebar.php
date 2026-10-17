<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * SIDEBAR.PHP — MENU DE L'INTRANET
 * Refonte du 20/09/2026.
 *
 * !! LE MENU SUIT LE PROFIL : il n'affiche que les espaces auxquels la personne
 *    a réellement droit (c'est le principe demandé par Frédéric : « des accès et
 *    des profils »). Masquer ne suffit jamais à protéger — chaque page vérifie
 *    aussi les droits côté serveur — mais afficher un lien qui mène à un refus
 *    serait une faute de goût.
 *
 *    Direction  : tout
 *    Vendeur    : comptoir + documentation
 *    Mécanicien : atelier + agenda + documentation
 * ═══════════════════════════════════════════════════════════════════════════
 */

$role = strtoupper(getRole());

$estDirection = in_array($role, ['ADMIN', 'DIRECTEUR'], true);
$estAtelier   = in_array($role, ['ADMIN', 'DIRECTEUR', 'MECANICIEN'], true);
$estComptoir  = in_array($role, ['ADMIN', 'DIRECTEUR', 'VENDEUR'], true);

$rolesLisibles = [
    'ADMIN'      => 'Administrateur',
    'DIRECTEUR'  => 'Direction',
    'VENDEUR'    => 'Vendeur',
    'MECANICIEN' => 'Mécanicien',
];
$libelleRole = $rolesLisibles[$role] ?? $role;
?>

<aside class="menu-lateral flex flex-col h-full shrink-0 border-r border-white/10 bg-[#0f172a]"
       :class="menuOuvert && 'menu-lateral--ouvert'"
       style="width: var(--largeur-menu);">

    <!-- ─── MARQUE ─────────────────────────────────────────────────────── -->
    <div class="px-5 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-[10px] bg-[#38bdf8] flex items-center justify-center
                        text-[#04121c] font-display font-bold text-sm shrink-0">MH</div>
            <div class="min-w-0">
                <p class="font-display text-[0.95rem] font-semibold text-white leading-tight truncate">
                    MotoHall 2A
                </p>
                <p class="text-[0.72rem] text-slate-500 leading-tight">Intranet du personnel</p>
            </div>
        </div>
    </div>

    <!-- ─── NAVIGATION ─────────────────────────────────────────────────── -->
    <nav class="flex-1 overflow-y-auto px-3 py-4">

        <p class="titre-section px-3 mb-2">Mon espace</p>

        <a href="index.php" @click.prevent="navigate('index.php')"
           class="nav-lien" :class="currentPage === 'index.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="home"></i></span>
            <span>Portail</span>
        </a>

        <a href="profil.php" @click.prevent="navigate('profil.php')"
           class="nav-lien" :class="currentPage === 'profil.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="user-cog"></i></span>
            <span>Mon compte</span>
        </a>

        <?php if ($estComptoir): ?>
        <p class="titre-section px-3 mt-6 mb-2">Comptoir</p>

        <a href="comptoir.php" @click.prevent="navigate('comptoir.php')"
           class="nav-lien" :class="currentPage === 'comptoir.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="monitor"></i></span>
            <span>Espace de vente</span>
        </a>
        <?php endif; ?>

        <?php if ($estAtelier): ?>
        <p class="titre-section px-3 mt-6 mb-2">Atelier</p>

        <a href="atelier.php" @click.prevent="navigate('atelier.php')"
           class="nav-lien" :class="currentPage === 'atelier.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="wrench"></i></span>
            <span>Espace technique</span>
        </a>

        <a href="agenda.php" @click.prevent="navigate('agenda.php')"
           class="nav-lien" :class="currentPage === 'agenda.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="calendar-days"></i></span>
            <span>Agenda</span>
        </a>
        <?php endif; ?>

        <p class="titre-section px-3 mt-6 mb-2">Ressources</p>

        <a href="documents.php" @click.prevent="navigate('documents.php')"
           class="nav-lien" :class="currentPage === 'documents.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="folder-open"></i></span>
            <span>Documentation</span>
        </a>

        <?php if ($estDirection): ?>
        <p class="titre-section px-3 mt-6 mb-2">Direction</p>

        <a href="dashboard.php" @click.prevent="navigate('dashboard.php')"
           class="nav-lien" :class="currentPage === 'dashboard.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="layout-grid"></i></span>
            <span>Tableau de bord</span>
        </a>

        <a href="admin-users.php" @click.prevent="navigate('admin-users.php')"
           class="nav-lien" :class="currentPage === 'admin-users.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="users"></i></span>
            <span>Membres du staff</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- ─── BAS DE MENU ────────────────────────────────────────────────── -->
    <div class="p-3 border-t border-white/10">
        <div class="flex items-center gap-3 px-3 py-2 mb-2">
            <div class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center
                        justify-center text-[0.78rem] font-semibold text-slate-300 shrink-0">
                <?= htmlspecialchars(strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[0.82rem] text-slate-200 font-semibold truncate">
                    <?= htmlspecialchars($_SESSION['username'] ?? 'Utilisateur', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p class="text-[0.7rem] text-slate-500 truncate"><?= htmlspecialchars($libelleRole, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <a href="logout.php" class="nav-lien">
            <span class="nav-vignette"><i data-lucide="log-out"></i></span>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>
