<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * FOOTER_UI.PHP — BARRE DE STATUT
 * Refonte du 20/09/2026, reprise du 19/10/2026 (rôles du thème, texte + gros).
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

<footer class="panneau-bas h-9 shrink-0 flex items-center justify-between gap-4 px-6">

    <div class="flex items-center gap-4 min-w-0">
        <span class="truncate">Intranet <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></span>
        <?php if ($connecte): ?>
        <span class="hidden sm:block w-px h-3 bg-white/10" aria-hidden="true"></span>
        <span class="hidden sm:inline truncate">
            Connecté&nbsp;: <?= htmlspecialchars((string)($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            (<?= htmlspecialchars($libelleRole, ENT_QUOTES, 'UTF-8') ?>)
        </span>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-4 shrink-0">
        <?php if ($connecte): ?>
        <span class="hidden md:inline" title="Mémoire réellement utilisée par cette page">
            Page&nbsp;: <?= number_format($memoire, 1, ',', ' ') ?> Mo
        </span>
        <span class="hidden md:block w-px h-3 bg-white/10" aria-hidden="true"></span>
        <?php endif; ?>
        <span>Accès réservé au personnel</span>
    </div>
</footer>
