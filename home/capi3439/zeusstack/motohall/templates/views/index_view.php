<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * INDEX_VIEW.PHP — PORTAIL DE L'INTRANET
 * Refonte du 20/09/2026.
 *
 * C'est la page d'accueil du site : https://motohall.zeusstack.fr
 *
 * DEUX ÉTATS, selon que la personne est identifiée ou non :
 *   - VISITEUR : le portail présente les trois espaces de l'entreprise et
 *     propose de s'identifier. AUCUNE donnée n'est exposée (pas un document,
 *     pas un rendez-vous, pas un nom de client) et aucun espace n'est ouvrable.
 *   - PERSONNE IDENTIFIÉE : elle voit SES espaces, avec les accès cliquables.
 *     Un espace que son profil n'autorise pas n'apparaît pas.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$connecte = isset($_SESSION['user_id']);
$role = strtoupper(getRole());
$estDirection = in_array($role, ['ADMIN', 'DIRECTEUR'], true);
$estAtelier   = in_array($role, ['ADMIN', 'DIRECTEUR', 'MECANICIEN'], true);
$estComptoir  = in_array($role, ['ADMIN', 'DIRECTEUR', 'VENDEUR'], true);

$prenom = trim((string)($_SESSION['prenom'] ?? ''));
$nomAffiche = $prenom !== '' ? $prenom : (string)($_SESSION['username'] ?? '');

$rolesLisibles = [
    'ADMIN'      => 'Administrateur',
    'DIRECTEUR'  => 'Direction',
    'VENDEUR'    => 'Vendeur — magasin',
    'MECANICIEN' => 'Mécanicien — atelier',
];
$libelleRole = $rolesLisibles[$role] ?? $role;

/**
 * Les trois univers de l'entreprise.
 * `autorise` dit si la personne peut l'ouvrir ; `acces` liste les pages.
 */
$univers = [];

if ($estComptoir || !$connecte) {
    $univers[] = [
        'titre'     => 'Comptoir',
        'sousTitre' => 'Vente et accueil client',
        'icone'     => 'monitor',
        'teinte'    => '#34d399',
        'texte'     => 'Les outils du magasin : vente, commandes et documentation commerciale.',
        'autorise'  => $estComptoir,
        'acces'     => [
            ['titre' => 'Espace de vente', 'texte' => 'Vue d\'ensemble du comptoir', 'icone' => 'layout-grid', 'lien' => 'comptoir.php'],
            ['titre' => 'Documentation commerciale', 'texte' => 'Procédures, tarifs et formulaires', 'icone' => 'file-text', 'lien' => 'documents.php'],
        ],
    ];
}

if ($estAtelier || !$connecte) {
    $univers[] = [
        'titre'     => 'Atelier',
        'sousTitre' => 'Technique et interventions',
        'icone'     => 'wrench',
        'teinte'    => '#fbbf24',
        'texte'     => 'Le suivi des interventions et la documentation technique.',
        'autorise'  => $estAtelier,
        'acces'     => [
            ['titre' => 'Espace technique', 'texte' => 'Vue d\'ensemble de l\'atelier', 'icone' => 'layout-grid', 'lien' => 'atelier.php'],
            ['titre' => 'Agenda des interventions', 'texte' => 'Rendez-vous de la semaine', 'icone' => 'calendar-days', 'lien' => 'agenda.php'],
            ['titre' => 'Documentation technique', 'texte' => 'Manuels, éclatés moteur et fiches', 'icone' => 'folder-open', 'lien' => 'documents.php?dir=atelier'],
        ],
    ];
}

if ($estDirection || !$connecte) {
    $univers[] = [
        'titre'     => 'Direction',
        'sousTitre' => 'Pilotage et personnel',
        'icone'     => 'shield-check',
        'teinte'    => '#38bdf8',
        'texte'     => 'Les indicateurs de l\'entreprise, la gestion du personnel et les documents réservés.',
        'autorise'  => $estDirection,
        'acces'     => [
            ['titre' => 'Tableau de bord', 'texte' => 'Indicateurs et prochains rendez-vous', 'icone' => 'layout-grid', 'lien' => 'dashboard.php'],
            ['titre' => 'Membres du staff', 'texte' => 'Comptes, droits et activations', 'icone' => 'users', 'lien' => 'admin-users.php'],
            ['titre' => 'Documentation', 'texte' => 'Documents stratégiques et RH', 'icone' => 'folder-open', 'lien' => 'documents.php'],
        ],
    ];
}
?>

<div class="apparition space-y-6">

    <!-- ─── MESSAGE ────────────────────────────────────────────────────── -->
    <?php if (!empty($error)): ?>
    <div class="carte flex items-center gap-3 border-amber-500/30">
        <i data-lucide="info" class="w-5 h-5 text-amber-400 shrink-0"></i>
        <p class="text-[0.88rem] text-amber-300"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <!-- ─── BIENVENUE ──────────────────────────────────────────────────── -->
    <div class="carte">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="titre-section mb-2">Portail de l'intranet</p>
                <h2 class="font-display text-2xl font-semibold text-white">
                    <?= $connecte
                        ? 'Bonjour ' . htmlspecialchars($nomAffiche, ENT_QUOTES, 'UTF-8')
                        : 'Bienvenue chez MotoHall 2A' ?>
                </h2>
                <p class="mt-2 text-[0.9rem] text-slate-400 max-w-2xl">
                    <?php if ($connecte): ?>
                        Choisissez un espace ci-dessous : vous voyez ceux que votre profil vous ouvre.
                    <?php else: ?>
                        Voici les trois espaces de l'entreprise. Identifiez-vous pour ouvrir celui
                        qui correspond à votre poste.
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($connecte): ?>
            <div class="text-right shrink-0">
                <span class="badge badge-accent"><?= htmlspecialchars($libelleRole, ENT_QUOTES, 'UTF-8') ?></span>
                <p class="mt-2 text-[0.78rem] text-slate-500"><?= htmlspecialchars(date('d/m/Y'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- ─── LES TROIS UNIVERS ──────────────────────────────────────── -->
        <div class="<?= $connecte ? 'lg:col-span-3' : 'lg:col-span-2' ?> space-y-4">
            <div class="grid grid-cols-1 <?= $connecte ? 'md:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-3' ?> gap-4">
                <?php foreach ($univers as $u): ?>
                <section class="carte flex flex-col" style="border-top: 2px solid <?= $u['teinte'] ?>33;">

                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-[10px] flex items-center justify-center shrink-0"
                                 style="background: <?= $u['teinte'] ?>1a; border: 1px solid <?= $u['teinte'] ?>40;">
                                <i data-lucide="<?= htmlspecialchars($u['icone'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="w-5 h-5" style="color: <?= $u['teinte'] ?>;"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-display text-[1.05rem] font-semibold text-white leading-tight">
                                    <?= htmlspecialchars($u['titre'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <p class="text-[0.78rem] text-slate-500"><?= htmlspecialchars($u['sousTitre'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>

                        <?php if (!$u['autorise']): ?>
                        <span class="badge shrink-0" title="Cet espace demande une identification">
                            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                            Réservé
                        </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-[0.86rem] text-slate-400 leading-relaxed mb-5">
                        <?= htmlspecialchars($u['texte'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <div class="space-y-1.5 mt-auto">
                        <?php foreach ($u['acces'] as $acces): ?>
                            <?php if ($u['autorise']): ?>
                            <a href="<?= htmlspecialchars($acces['lien'], ENT_QUOTES, 'UTF-8') ?>"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] border border-white/10
                                      bg-white/[0.02] transition-colors duration-200 hover:bg-white/[0.05]"
                               style="border-left: 2px solid <?= $u['teinte'] ?>55;">
                                <i data-lucide="<?= htmlspecialchars($acces['icone'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="w-4 h-4 shrink-0" style="color: <?= $u['teinte'] ?>;"></i>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-[0.86rem] text-slate-200 truncate"><?= htmlspecialchars($acces['titre'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="block text-[0.74rem] text-slate-500 truncate"><?= htmlspecialchars($acces['texte'], ENT_QUOTES, 'UTF-8') ?></span>
                                </span>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600 shrink-0"></i>
                            </a>
                            <?php else: ?>
                            <!-- Visiteur : on montre ce que contient l'espace, sans lien -->
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] border border-white/5
                                        bg-white/[0.01] opacity-60">
                                <i data-lucide="<?= htmlspecialchars($acces['icone'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="w-4 h-4 shrink-0 text-slate-500"></i>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-[0.86rem] text-slate-400 truncate"><?= htmlspecialchars($acces['titre'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="block text-[0.74rem] text-slate-600 truncate"><?= htmlspecialchars($acces['texte'], ENT_QUOTES, 'UTF-8') ?></span>
                                </span>
                                <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-600 shrink-0"></i>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ─── IDENTIFICATION (visiteur seulement) ────────────────────── -->
        <?php if (!$connecte): ?>
        <div class="lg:col-span-1">
            <div class="carte lg:sticky lg:top-4">
                <div class="carte-entete">
                    <h2 class="carte-titre">Identification</h2>
                    <i data-lucide="log-in" class="w-4 h-4 text-[#38bdf8]"></i>
                </div>

                <p class="text-[0.85rem] text-slate-400 mb-4">
                    Réservé au personnel de MotoHall 2A. Vos identifiants vous sont remis par la
                    Direction.
                </p>

                <form method="POST" action="login.php" class="space-y-3">
                    <div>
                        <label class="etiquette" for="username">Identifiant</label>
                        <input type="text" id="username" name="username" required autocomplete="username"
                               class="champ" placeholder="votre identifiant">
                    </div>
                    <div>
                        <label class="etiquette" for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required
                               autocomplete="current-password" class="champ" placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-accent btn-bloc">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Se connecter</span>
                    </button>
                </form>

                <p class="mt-4 text-[0.78rem] text-slate-500">
                    Identifiants oubliés ? Adressez-vous à la Direction.
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- ─── RAPPEL ─────────────────────────────────────────────────────── -->
    <p class="text-[0.78rem] text-slate-500">
        <?php if ($connecte): ?>
            Espace réservé au personnel. Votre profil détermine les espaces auxquels vous avez
            accès ; pour un droit supplémentaire, adressez-vous à la Direction.
        <?php else: ?>
            Aucun document, aucun rendez-vous et aucune donnée client n'est visible sans
            identification.
        <?php endif; ?>
    </p>
</div>
