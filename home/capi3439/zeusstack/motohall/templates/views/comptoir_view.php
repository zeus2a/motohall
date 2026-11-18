<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * COMPTOIR_VIEW.PHP — ESPACE DE VENTE
 * Refonte du 20/09/2026 : accroche courte et trois accès clairement
 * hiérarchisés, au lieu d'un bloc géant et de cartes « bulles ».
 *
 * !! HONNÊTETÉ : le bouton « Lancer Session » du logiciel de caisse ne faisait
 *    RIEN (aucun gestionnaire). Il est remplacé par une pastille « Bientôt
 *    disponible », comme les autres fonctions non branchées. Un bouton qui ne
 *    fait rien fait plus de mal qu'un bouton absent.
 * ═══════════════════════════════════════════════════════════════════════════
 */
?>

<div class="apparition space-y-6">

    <p class="text-[0.9rem] text-slate-400 max-w-2xl">
        Comptoir&nbsp;: vente et accueil client. Les accès ci-dessous s'ouvrent sans mot de passe.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- ─── LOGICIEL DE CAISSE ─────────────────────────────────────── -->
        <div class="carte flex flex-col">
            <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10 flex items-center
                        justify-center mb-4">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-[#38bdf8]"></i>
            </div>
            <h3 class="font-display text-[1rem] font-semibold text-white mb-2">Logiciel de caisse</h3>
            <p class="text-[0.87rem] text-slate-400 leading-relaxed mb-5">
                Facturation, stocks et commandes clients.
            </p>
            <div class="mt-auto">
                <span class="badge">Bientôt disponible</span>
            </div>
        </div>

        <!-- ─── DOCUMENTATION COMMERCIALE ──────────────────────────────── -->
        <a href="documents.php" class="carte flex flex-col transition-colors duration-200 hover:border-[#38bdf8]/40">
            <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10 flex items-center
                        justify-center mb-4">
                <i data-lucide="file-text" class="w-5 h-5 text-[#38bdf8]"></i>
            </div>
            <h3 class="font-display text-[1rem] font-semibold text-white mb-2">Documentation commerciale</h3>
            <p class="text-[0.87rem] text-slate-400 leading-relaxed mb-5">
                Procédures, formulaires, tarifs et documents fournisseurs.
            </p>
            <span class="mt-auto inline-flex items-center gap-2 text-[0.83rem] font-semibold text-[#38bdf8]">
                Ouvrir la documentation
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </span>
        </a>

        <!-- ─── CATALOGUE PIÈCES ───────────────────────────────────────── -->
        <div class="carte flex flex-col">
            <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10 flex items-center
                        justify-center mb-4">
                <i data-lucide="layers" class="w-5 h-5 text-[#38bdf8]"></i>
            </div>
            <h3 class="font-display text-[1rem] font-semibold text-white mb-2">Catalogue des pièces</h3>
            <p class="text-[0.87rem] text-slate-400 leading-relaxed mb-5">
                Références, fournisseurs et éclatés de pièces de rechange.
            </p>
            <div class="mt-auto">
                <span class="badge">Bientôt disponible</span>
            </div>
        </div>
    </div>

    <p class="text-[0.78rem] text-slate-500 pt-2">
        Les documents du magasin sont rangés par type&nbsp;: procédures, formulaires, images et inventaires.
    </p>
</div>
