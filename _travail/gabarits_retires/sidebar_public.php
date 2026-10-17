<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * SIDEBAR_PUBLIC.PHP — MENU DES ESPACES LIBRES (COMPTOIR / ATELIER / DOCUMENTS)
 * Refonte visuelle du 20/09/2026.
 * ═══════════════════════════════════════════════════════════════════════════
 */
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
                <p class="text-[0.72rem] text-slate-500 leading-tight">Espaces libres</p>
            </div>
        </div>
    </div>

    <!-- ─── NAVIGATION ─────────────────────────────────────────────────── -->
    <nav class="flex-1 overflow-y-auto px-3 py-4">

        <p class="titre-section px-3 mb-2">Magasin</p>

        <a href="comptoir.php" @click.prevent="navigate('comptoir.php')"
           class="nav-lien" :class="currentPage === 'comptoir.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="monitor"></i></span>
            <span>Comptoir</span>
        </a>

        <p class="titre-section px-3 mt-6 mb-2">Atelier</p>

        <a href="atelier.php" @click.prevent="navigate('atelier.php')"
           class="nav-lien" :class="currentPage === 'atelier.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="wrench"></i></span>
            <span>Atelier</span>
        </a>

        <a href="agenda.php" @click.prevent="navigate('agenda.php')"
           class="nav-lien" :class="currentPage === 'agenda.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="calendar-days"></i></span>
            <span>Agenda</span>
        </a>

        <p class="titre-section px-3 mt-6 mb-2">Ressources</p>

        <a href="documents.php" @click.prevent="navigate('documents.php')"
           class="nav-lien" :class="currentPage === 'documents.php' && 'nav-lien--actif'">
            <span class="nav-vignette"><i data-lucide="folder-open"></i></span>
            <span>Documentation</span>
        </a>

        <a href="index.php" class="nav-lien">
            <span class="nav-vignette"><i data-lucide="home"></i></span>
            <span>Portail d'accueil</span>
        </a>
    </nav>

    <!-- ─── BAS DE MENU ────────────────────────────────────────────────── -->
    <div class="p-3 border-t border-white/10">
        <a href="login.php" class="btn btn-neutre btn-bloc">
            <i data-lucide="shield-check"></i>
            <span>Zone Direction</span>
        </a>
    </div>
</aside>
