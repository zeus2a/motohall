<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * INDEX_VIEW.PHP — LE PORTAIL, UNE FOIS CONNECTÉ
 *
 * REFAIT LE 18/11/2026. Cette vue avait DEUX états : visiteuse et connectée.
 * L'état visiteuse énumérait les trois univers de l'entreprise et le contenu
 * de chacun — « Comptoir, Atelier, Direction », puis « Tableau de bord »,
 * « Membres du personnel », « Documentation stratégique et RH »…
 *
 * Autrement dit : la carte du bâtiment, lisible par n'importe qui, sans même
 * un mot de passe. Frédéric l'a formulé exactement : « on donne des infos aux
 * hackers sur le contenu ».
 *
 * La page de connexion vit désormais dans son propre gabarit
 * (templates/layout_connexion.php), et CETTE vue n'est plus rendue QUE pour
 * une personne identifiée. Les branches visiteuses ont donc été SUPPRIMÉES,
 * pas seulement désactivées : un code mort qui énumère les services reste un
 * code qu'un futur édit peut réactiver par accident.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$role = strtoupper(getRole());
$estDirection = in_array($role, ['ADMIN', 'DIRECTEUR'], true);
$estAtelier   = in_array($role, ['ADMIN', 'DIRECTEUR', 'MECANICIEN'], true);
$estComptoir  = in_array($role, ['ADMIN', 'DIRECTEUR', 'VENDEUR'], true);

$prenom = trim((string)($_SESSION['prenom'] ?? ''));
$nomAffiche = $prenom !== '' ? $prenom : (string)($_SESSION['username'] ?? '');

$rolesLisibles = [
    'ADMIN'      => 'Administrateur',
    'DIRECTEUR'  => 'Direction',
    'VENDEUR'    => 'Vendeur',
    'MECANICIEN' => 'Mécanicien',
];
$libelleRole = $rolesLisibles[$role] ?? $role;

/**
 * Les univers que CE profil ouvre réellement. Un univers non autorisé n'est
 * même pas construit : il ne peut donc pas s'afficher par erreur.
 */
$univers = [];

if ($estComptoir) {
    $univers[] = [
        'titre'     => 'Comptoir',
        'sousTitre' => 'Vente et accueil client',
        'icone'     => 'monitor',
        'teinte'    => '#34d399',
        'texte'     => "Les outils du magasin\xC2\xA0: vente, commandes et documentation commerciale.",
        'acces'     => [
            ['titre' => 'Comptoir', 'texte' => 'Vue d’ensemble du comptoir', 'icone' => 'layout-grid', 'lien' => 'comptoir.php'],
            ['titre' => 'Documentation commerciale', 'texte' => 'Procédures, tarifs et formulaires', 'icone' => 'file-text', 'lien' => 'documents.php'],
        ],
    ];
}

if ($estAtelier) {
    $univers[] = [
        'titre'     => 'Atelier',
        'sousTitre' => 'Technique et interventions',
        'icone'     => 'wrench',
        'teinte'    => '#fbbf24',
        'texte'     => 'Le suivi des interventions et la documentation technique.',
        'acces'     => [
            ['titre' => 'Atelier', 'texte' => 'Vue d’ensemble de l’atelier', 'icone' => 'layout-grid', 'lien' => 'atelier.php'],
            ['titre' => 'Agenda', 'texte' => 'Rendez-vous de la semaine', 'icone' => 'calendar-days', 'lien' => 'agenda.php'],
            ['titre' => 'Documentation technique', 'texte' => 'Manuels, éclatés moteur et fiches', 'icone' => 'folder-open', 'lien' => 'documents.php?dir=atelier'],
        ],
    ];
}

if ($estDirection) {
    $univers[] = [
        'titre'     => 'Direction',
        'sousTitre' => 'Pilotage et personnel',
        'icone'     => 'shield-check',
        'teinte'    => '#38bdf8',
        'texte'     => 'Les indicateurs de l’entreprise, la gestion du personnel et les documents réservés.',
        'acces'     => [
            ['titre' => 'Tableau de bord', 'texte' => 'Indicateurs et prochains rendez-vous', 'icone' => 'layout-grid', 'lien' => 'dashboard.php'],
            ['titre' => 'Membres du personnel', 'texte' => 'Comptes, droits et activations', 'icone' => 'users', 'lien' => 'admin-users.php'],
            ['titre' => 'Documentation', 'texte' => 'Documents stratégiques et RH', 'icone' => 'folder-open', 'lien' => 'documents.php'],
        ],
    ];
}

$nombreUnivers = count($univers);
?>

<div class="apparition space-y-6">

    <!-- ─── MESSAGE ────────────────────────────────────────────────────── -->
    <?php if (!empty($error)): ?>
    <div class="carte flex items-center gap-3 border-amber-500/30">
        <i data-lucide="info" class="w-5 h-5 text-amber-400 shrink-0"></i>
        <p class="text-[0.92rem] text-amber-300"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <!-- ─── BIENVENUE ──────────────────────────────────────────────────── -->
    <div class="carte">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="titre-section mb-2">Portail de l’intranet</p>
                <h2 class="font-display text-2xl">
                    Bonjour <?= htmlspecialchars($nomAffiche, ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="mt-2 text-[0.95rem] text-slate-400 max-w-2xl">
                    <?php if ($nombreUnivers === 0): ?>
                        Votre profil ne vous ouvre aucun espace pour le moment.
                        Adressez-vous à la Direction.
                    <?php elseif ($nombreUnivers === 1): ?>
                        Voici votre espace de travail. Il vous est ouvert par votre profil.
                    <?php else: ?>
                        Choisissez un espace&nbsp;: vous ne voyez que ceux que votre profil vous ouvre.
                    <?php endif; ?>
                </p>
            </div>
            <div class="text-right shrink-0">
                <span class="badge badge-accent"><?= htmlspecialchars($libelleRole, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
    </div>

    <?php if ($nombreUnivers > 0): ?>
    <div class="grid grid-cols-1 <?= $nombreUnivers >= 3 ? 'md:grid-cols-3' : ($nombreUnivers === 2 ? 'md:grid-cols-2' : 'md:grid-cols-1') ?> gap-4">

        <?php foreach ($univers as $u): ?>
        <section class="carte flex flex-col" style="border-top: 2px solid <?= $u['teinte'] ?>33;">

            <div class="flex items-center gap-3 min-w-0 mb-4">
                <div class="w-10 h-10 rounded-[10px] flex items-center justify-center shrink-0"
                     style="background: <?= $u['teinte'] ?>1a; border: 1px solid <?= $u['teinte'] ?>40;">
                    <i data-lucide="<?= htmlspecialchars($u['icone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="w-5 h-5" style="color: <?= $u['teinte'] ?>;"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-display text-[1.15rem] leading-tight">
                        <?= htmlspecialchars($u['titre'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <p class="text-[0.85rem] text-slate-500"><?= htmlspecialchars($u['sousTitre'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>

            <p class="text-[0.92rem] text-slate-400 leading-relaxed mb-5">
                <?= htmlspecialchars($u['texte'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div class="space-y-1.5 mt-auto">
                <?php foreach ($u['acces'] as $acces): ?>
                <a href="<?= htmlspecialchars($acces['lien'], ENT_QUOTES, 'UTF-8') ?>"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] border border-white/10
                          bg-white/[0.02] transition-colors duration-200 hover:bg-white/[0.05]"
                   style="border-left: 2px solid <?= $u['teinte'] ?>55;">
                    <i data-lucide="<?= htmlspecialchars($acces['icone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="w-4 h-4 shrink-0" style="color: <?= $u['teinte'] ?>;"></i>
                    <span class="min-w-0 flex-1">
                        <span class="block text-[0.92rem] text-slate-200 truncate"><?= htmlspecialchars($acces['titre'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="block text-[0.85rem] text-slate-500 truncate"><?= htmlspecialchars($acces['texte'], ENT_QUOTES, 'UTF-8') ?></span>
                    </span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600 shrink-0"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ─── RAPPEL ─────────────────────────────────────────────────────── -->
    <p class="text-[0.85rem] text-slate-500">
        Espace réservé au personnel. Votre profil détermine les espaces auxquels vous avez
        accès&nbsp;; pour un droit supplémentaire, adressez-vous à la Direction.
    </p>
</div>
