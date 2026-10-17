<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * HEADER_UI.PHP — BANDEAU DE L'INTRANET
 * Refonte du 20/09/2026, structure reprise de Novaya Suites :
 * titre de page à gauche, horloge et profil de la personne connectée à droite.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$role = strtoupper(getRole());
$titre = $pageTitle ?? 'Intranet';

$rolesLisibles = [
    'ADMIN'      => 'Administrateur',
    'DIRECTEUR'  => 'Direction',
    'VENDEUR'    => 'Vendeur',
    'MECANICIEN' => 'Mécanicien',
];
$libelleRole = $rolesLisibles[$role] ?? $role;

$pseudo   = (string)($_SESSION['username'] ?? 'Utilisateur');
$prenom   = trim((string)($_SESSION['prenom'] ?? ''));
$nomFam   = trim((string)($_SESSION['nom'] ?? ''));
$nomComplet = trim($prenom . ' ' . $nomFam);
$affiche  = $nomComplet !== '' ? $nomComplet : $pseudo;
$initiales = strtoupper(substr($prenom !== '' ? $prenom : $pseudo, 0, 1)
                      . ($nomFam !== '' ? substr($nomFam, 0, 1) : ''));

$enFr   = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
$moisFr = [1=>'janvier','février','mars','avril','mai','juin','juillet',
           'août','septembre','octobre','novembre','décembre'];
$dateJour = $enFr[(int)date('w')] . ' ' . (int)date('j') . ' ' . $moisFr[(int)date('n')] . ' ' . date('Y');

$connecte = isset($_SESSION['user_id']);
?>

<?php if (!$connecte): ?>
<!-- ─── VISITEUR NON IDENTIFIÉ : on montre la marque, pas un profil ─────── -->
<header class="h-16 shrink-0 border-b border-white/10 flex items-center justify-between gap-4 px-6
               bg-[#020617]/85 backdrop-blur-md sticky top-0 z-40">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-9 h-9 rounded-[10px] bg-[#38bdf8] flex items-center justify-center
                    text-[#04121c] font-display font-bold text-sm shrink-0">MH</div>
        <div class="min-w-0">
            <p class="font-display text-[0.95rem] font-semibold text-white leading-tight truncate">
                MotoHall 2A
            </p>
            <p class="text-[0.72rem] text-slate-500 leading-tight truncate">
                Intranet du personnel — <?= htmlspecialchars($dateJour, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>

    <span class="badge shrink-0">
        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
        Accès réservé
    </span>
</header>

<?php else: ?>
<header class="h-16 shrink-0 border-b border-white/10 flex items-center justify-between gap-4 px-6
               bg-[#020617]/85 backdrop-blur-md sticky top-0 z-40">

    <!-- ─── TITRE DE LA PAGE ───────────────────────────────────────────── -->
    <div class="flex items-center gap-3 min-w-0">
        <!-- Bouton de menu : n'apparaît que sur petit écran (voir aegis.css) -->
        <button type="button" @click="menuOuvert = !menuOuvert"
                class="bouton-menu btn btn-neutre !px-2.5" title="Ouvrir le menu">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <div class="min-w-0">
            <nav class="ariane mb-0.5">
                <a href="index.php" @click.prevent="navigate('index.php')">Intranet</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="text-slate-400 truncate"><?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?></span>
            </nav>
            <p class="sous-titre-page truncate"><?= htmlspecialchars($dateJour, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- ─── HORLOGE ET PROFIL ──────────────────────────────────────────── -->
    <div class="flex items-center gap-4 shrink-0">
        <div class="hidden lg:flex flex-col items-end leading-tight">
            <span class="font-display text-[1.05rem] text-white tabulaire" x-text="time">--:--:--</span>
            <span class="text-[0.7rem] text-slate-500">heure locale</span>
        </div>

        <span class="hidden lg:block w-px h-9 bg-white/10"></span>

        <a href="profil.php" @click.prevent="navigate('profil.php')"
           class="flex items-center gap-3 group" title="Mon compte">
            <div class="hidden sm:flex flex-col items-end leading-tight">
                <span class="text-[0.85rem] font-medium text-slate-200 group-hover:text-white transition-colors truncate max-w-[160px]">
                    <?= htmlspecialchars($affiche, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="text-[0.7rem] text-slate-500"><?= htmlspecialchars($libelleRole, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center
                        text-[0.78rem] font-semibold text-slate-300 shrink-0
                        group-hover:border-[#38bdf8]/50 transition-colors">
                <?= htmlspecialchars($initiales !== '' ? $initiales : 'U', ENT_QUOTES, 'UTF-8') ?>
            </div>
        </a>
    </div>
</header>
<?php endif; ?>
