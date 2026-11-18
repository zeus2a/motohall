<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * HEADER_UI.PHP — BANDEAU DE L'INTRANET
 *
 * Refonte du 20/09/2026, reprise du 19/10/2026 :
 *   - la bascule de thème clair / sombre prend place entre l'horloge et le
 *     profil. C'est là qu'on la cherche : à droite, avec les autres réglages
 *     de la personne ;
 *   - les icônes de la bascule sont écrites en SVG dans le fichier, pas
 *     chargées par Lucide : le bouton reste utilisable même si Lucide n'a pas
 *     encore remplacé les balises <i>.
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
// Le 1er du mois s'écrit « 1er octobre », pas « 1 octobre ».
$jourNum = (int)date('j');
$dateJour = $enFr[(int)date('w')] . ' ' . $jourNum . ($jourNum === 1 ? 'er' : '')
          . ' ' . $moisFr[(int)date('n')] . ' ' . date('Y');

$connecte = isset($_SESSION['user_id']);
?>

<?php
/**
 * LA BASCULE DE THÈME — un seul bouton, deux icônes.
 * Laquelle s'affiche dépend de l'attribut data-theme posé sur <html> : c'est
 * la feuille de style qui décide, pas le JavaScript. Le script, lui, ne fait
 * que changer l'attribut au clic.
 */
$basculeTheme = '
<button type="button" class="bascule-theme" data-theme-toggle
        aria-pressed="false" aria-label="Changer de thème" title="Changer de thème">
    <svg class="icone-soleil" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4"></circle>
        <path d="M12 2v2"></path><path d="M12 20v2"></path>
        <path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path>
        <path d="M2 12h2"></path><path d="M20 12h2"></path>
        <path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path>
    </svg>
    <svg class="icone-lune" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
    </svg>
</button>';
?>

<?php if (!$connecte): ?>
<!-- ─── VISITEUR NON IDENTIFIÉ : on montre la marque, pas un profil ─────── -->
<header class="panneau-haut h-16 shrink-0 flex items-center justify-between gap-4 px-6
               sticky top-0 z-40">
    <div class="flex items-center gap-3 min-w-0">
        <div class="marque w-9 h-9 text-[0.85rem]">MH</div>
        <div class="min-w-0">
            <p class="font-display text-[1.05rem] font-semibold leading-tight truncate">
                MotoHall 2A
            </p>
            <p class="text-[0.85rem] text-slate-500 leading-tight truncate">
                Intranet du personnel — <?= htmlspecialchars($dateJour, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>

    <div class="flex items-center gap-3 shrink-0">
        <span class="badge">
            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
            Accès réservé
        </span>
        <?= $basculeTheme ?>
    </div>
</header>

<?php else: ?>
<header class="panneau-haut h-16 shrink-0 flex items-center justify-between gap-4 px-6
               sticky top-0 z-40">

    <!-- ─── TITRE DE LA PAGE ───────────────────────────────────────────── -->
    <div class="flex items-center gap-3 min-w-0">
        <!-- Bouton de menu : n'apparaît que sur petit écran (voir aegis.css) -->
        <button type="button" @click="menuOuvert = !menuOuvert"
                class="bouton-menu btn btn-neutre !px-2.5" aria-label="Ouvrir le menu">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <div class="min-w-0">
            <!-- !! LE TITRE DE PAGE EST UN <h1> (19/10/2026). Il était un
                 <span> : 8 pages sur 10 n'avaient donc AUCUN titre de
                 niveau 1, et un lecteur d'écran ne pouvait pas annoncer sur
                 quelle page on se trouve. La mise en forme ne change pas —
                 c'est .ariane h1 qui la donne (voir aegis.css). -->
            <nav class="ariane mb-0.5" aria-label="Fil d'Ariane">
                <a href="index.php" @click.prevent="navigate('index.php')">Intranet</a>
                <i data-lucide="chevron-right" class="w-3 h-3" aria-hidden="true"></i>
                <h1 class="actuel truncate"><?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?></h1>
            </nav>
            <p class="sous-titre-page truncate"><?= htmlspecialchars($dateJour, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- ─── HORLOGE, THÈME ET PROFIL ───────────────────────────────────── -->
    <div class="flex items-center gap-3 shrink-0">
        <div class="hidden lg:flex flex-col items-end leading-tight">
            <span class="font-display text-[1.05rem] tabulaire" x-text="time">--:--:--</span>
            <span class="text-[0.85rem] text-slate-500">heure locale</span>
        </div>

        <span class="hidden lg:block w-px h-9 bg-white/10" aria-hidden="true"></span>

        <?= $basculeTheme ?>

        <span class="hidden sm:block w-px h-9 bg-white/10" aria-hidden="true"></span>

        <a href="profil.php" @click.prevent="navigate('profil.php')"
           class="flex items-center gap-3 group" title="Mon compte">
            <div class="hidden sm:flex flex-col items-end leading-tight">
                <span class="text-[0.92rem] font-semibold text-slate-200 group-hover:text-white transition-colors truncate max-w-[180px]">
                    <?= htmlspecialchars($affiche, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="text-[0.85rem] text-slate-500"><?= htmlspecialchars($libelleRole, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center
                        text-[0.85rem] font-semibold text-slate-300 shrink-0
                        group-hover:border-[#38bdf8]/50 transition-colors">
                <?= htmlspecialchars($initiales !== '' ? $initiales : 'U', ENT_QUOTES, 'UTF-8') ?>
            </div>
        </a>
    </div>
</header>
<?php endif; ?>
