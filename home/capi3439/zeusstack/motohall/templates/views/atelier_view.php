<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ATELIER_VIEW.PHP — ESPACE TECHNIQUE
 * Refonte du 20/09/2026.
 *
 * !! CE QUI A ÉTÉ RETIRÉ : un bandeau « Service: Active » avec une adresse IP
 *    du réseau interne (192.168.9.36) qui s'affichait sur une page PUBLIQUE, et
 *    un bouton « Ordres de réparation » qui ne menait nulle part.
 * ═══════════════════════════════════════════════════════════════════════════
 */
?>

<div class="apparition space-y-6">

    <p class="text-[0.9rem] text-slate-400 max-w-2xl">
        Espace technique : planning des interventions, ordres de réparation et documentation atelier.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- ─── AGENDA ─────────────────────────────────────────────────── -->
        <a href="agenda.php" class="carte flex flex-col transition-colors duration-200 hover:border-[#38bdf8]/40">
            <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10 flex items-center
                        justify-center mb-4">
                <i data-lucide="calendar-days" class="w-5 h-5 text-[#38bdf8]"></i>
            </div>
            <h3 class="font-display text-[1rem] font-semibold text-white mb-2">Agenda de l'atelier</h3>
            <p class="text-[0.87rem] text-slate-400 leading-relaxed mb-5">
                Rendez-vous de la semaine et charge de travail de l'équipe.
            </p>
            <span class="mt-auto inline-flex items-center gap-2 text-[0.83rem] font-semibold text-[#38bdf8]">
                Ouvrir le planning
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </span>
        </a>

        <!-- ─── ORDRES DE RÉPARATION ───────────────────────────────────── -->
        <div class="carte flex flex-col">
            <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10 flex items-center
                        justify-center mb-4">
                <i data-lucide="clipboard-list" class="w-5 h-5 text-[#38bdf8]"></i>
            </div>
            <h3 class="font-display text-[1rem] font-semibold text-white mb-2">Ordres de réparation</h3>
            <p class="text-[0.87rem] text-slate-400 leading-relaxed mb-5">
                Création, suivi et clôture des O.R. des véhicules en intervention.
            </p>
            <div class="mt-auto">
                <span class="badge">Bientôt disponible</span>
            </div>
        </div>

        <!-- ─── DOCUMENTATION TECHNIQUE ────────────────────────────────── -->
        <a href="documents.php?dir=atelier" class="carte flex flex-col transition-colors duration-200 hover:border-[#38bdf8]/40">
            <div class="w-10 h-10 rounded-[10px] bg-white/5 border border-white/10 flex items-center
                        justify-center mb-4">
                <i data-lucide="folder-open" class="w-5 h-5 text-[#38bdf8]"></i>
            </div>
            <h3 class="font-display text-[1rem] font-semibold text-white mb-2">Documentation technique</h3>
            <p class="text-[0.87rem] text-slate-400 leading-relaxed mb-5">
                Éclatés moteur, manuels d'atelier et fiches fournisseurs.
            </p>
            <span class="mt-auto inline-flex items-center gap-2 text-[0.83rem] font-semibold text-[#38bdf8]">
                Ouvrir la librairie
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </span>
        </a>
    </div>

    <p class="text-[0.78rem] text-slate-500 pt-2">
        Les documents de l'atelier sont rangés par type : guides, manuels, procédures et formulaires.
    </p>
</div>
