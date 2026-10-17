<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * FOOTER_UI.PHP — BARRE DE STATUT
 * Refonte du 20/09/2026.
 *
 * !! CE QUI A ÉTÉ RETIRÉ, ET POURQUOI : la version d'avril affichait
 *    « SQL: CONNECTED » écrit en dur, sans vérifier quoi que ce soit, et un
 *    badge « ACTIVE NODE ». C'étaient de FAUX voyants verts : ils rassuraient
 *    sans rien mesurer. Seule la mémoire ci-dessous est une vraie mesure
 *    (memory_get_usage) ; le reste est du texte, assumé comme tel.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$memoire = round(memory_get_usage() / 1024 / 1024, 1);
$role = strtoupper(getRole());
$connecte = isset($_SESSION['user_id']);

$rolesLisibles = [
    'ADMIN'      => 'Administrateur',
    'DIRECTEUR'  => 'Direction',
    'VENDEUR'    => 'Vendeur',
    'MECANICIEN' => 'Mécanicien',
];
$libelleRole = $rolesLisibles[$role] ?? $role;
?>

<footer class="h-9 shrink-0 border-t border-white/10 flex items-center justify-between gap-4 px-6
               text-[0.72rem] text-slate-500 bg-[#020617]/60">

    <div class="flex items-center gap-4 min-w-0">
        <span class="truncate">Intranet <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></span>
        <?php if ($connecte): ?>
        <span class="hidden sm:block w-px h-3 bg-white/10"></span>
        <span class="hidden sm:inline truncate">
            Connecté : <?= htmlspecialchars((string)($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            (<?= htmlspecialchars($libelleRole, ENT_QUOTES, 'UTF-8') ?>)
        </span>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-4 shrink-0">
        <?php if ($connecte): ?>
        <span class="hidden md:inline" title="Mémoire réellement utilisée par cette page">
            Page : <?= $memoire ?> Mo
        </span>
        <span class="hidden md:block w-px h-3 bg-white/10"></span>
        <?php endif; ?>
        <span>Accès réservé au personnel</span>
    </div>
</footer>
