<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LAYOUT_CONNEXION.PHP — LA PAGE QUI FERME LA PORTE
 * Créé le 18/11/2026.
 *
 * CE QUE CETTE PAGE A LE DROIT DE DIRE, ET CE QU'ELLE N'A PAS LE DROIT DE DIRE
 * ---------------------------------------------------------------------------
 * ELLE PEUT dire : le nom de la concession, que l'espace est réservé au
 * personnel, qu'il faut des identifiants, et à qui les demander.
 *
 * ELLE NE DOIT JAMAIS dire : combien de services existent, comment ils
 * s'appellent, ce qu'ils contiennent, ni le nom des écrans. L'ancien portail
 * public énumérait « Comptoir, Atelier, Direction » et la liste des accès de
 * chacun — c'était la carte du bâtiment affichée à la rue. Un visiteur n'a
 * besoin de rien de tout cela pour se connecter ; seul un curieux en veut.
 *
 * Variables attendues : $error (chaîne ou null), plus les constantes du moteur.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$baseAssets = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'))), '/') . '/';

// Le message d'erreur porte parfois une information, parfois une simple
// information de contexte (déconnexion, accès refusé). On distingue les deux
// pour ne pas crier en rouge pour un « vous êtes bien déconnecté ».
$erreurEstUnEchec = ($error !== null) && !isset($_GET['status']) && !isset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="fr" data-theme="sombre">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>

    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex, notranslate">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta name="theme-color" content="#020617">
    <meta name="description" content="Accès réservé au personnel.">

    <!-- Le thème est posé AVANT le premier rendu (voir assets/js/theme.js). -->
    <script src="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/js/theme.js?v=20261118"></script>

    <link rel="preload" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="stylesheet" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/css/tailwind.css?v=20261118">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/css/aegis.css?v=20261118">

    <link rel="icon" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-180.png">
    <link rel="manifest" href="<?= htmlspecialchars($baseAssets, ENT_QUOTES, 'UTF-8') ?>site.webmanifest">
</head>

<body class="connexion">

    <!-- ─── EN-TÊTE : la marque, et le choix du thème ──────────────────── -->
    <header class="connexion-entete">
        <div class="connexion-marque">
            <!-- La roue, reprise du favicon : la marque du site, pas un décor. -->
            <svg viewBox="0 0 64 64" aria-hidden="true" focusable="false">
                <rect width="64" height="64" rx="14" fill="#0f172a"/>
                <rect x="1" y="1" width="62" height="62" rx="13" fill="none"
                      stroke="#38bdf8" stroke-opacity="0.35" stroke-width="2"/>
                <g fill="none" stroke="#38bdf8" stroke-linecap="round">
                    <circle cx="32" cy="32" r="18" stroke-width="6.5"/>
                    <circle cx="32" cy="32" r="11" stroke-width="2.5" stroke-opacity="0.75"/>
                    <path d="M32 21 V13" stroke-width="4.5"/>
                    <path d="M32 43 V51" stroke-width="4.5"/>
                    <path d="M21 32 H13" stroke-width="4.5"/>
                    <path d="M43 32 H51" stroke-width="4.5"/>
                </g>
                <circle cx="32" cy="32" r="5" fill="#38bdf8"/>
            </svg>
            <div class="min-w-0">
                <p class="connexion-marque__nom"><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></p>
                <p class="connexion-marque__role">Espace collaborateurs</p>
            </div>
        </div>

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
        </button>
    </header>

    <!-- ─── LE CENTRE : une carte, et rien d'autre ─────────────────────── -->
    <main class="connexion-centre">
        <div class="connexion-carte">

            <h1 class="connexion-titre">Connexion</h1>
            <p class="connexion-accroche">
                Identifiez-vous pour ouvrir votre espace de travail.
            </p>

            <?php if ($error !== null): ?>
                <div class="connexion-alerte <?= $erreurEstUnEchec ? '' : 'connexion-alerte--info' ?>"
                     role="<?= $erreurEstUnEchec ? 'alert' : 'status' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <?php if ($erreurEstUnEchec): ?>
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 8v4"></path><path d="M12 16h.01"></path>
                        <?php else: ?>
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 16v-4"></path><path d="M12 8h.01"></path>
                        <?php endif; ?>
                    </svg>
                    <span><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php" autocomplete="on">
                <div class="connexion-champ">
                    <label class="etiquette" for="username">Identifiant</label>
                    <input type="text" id="username" name="username" required
                           class="champ" placeholder="votre identifiant"
                           autocomplete="username" autocapitalize="none" spellcheck="false"
                           <?= $connecte ?? false ? '' : 'autofocus' ?>>
                </div>

                <div class="connexion-champ">
                    <label class="etiquette" for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                           class="champ" placeholder="••••••••"
                           autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-accent btn-large btn-bloc">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <path d="m10 17 5-5-5-5"></path><path d="M15 12H3"></path>
                    </svg>
                    <span>Connexion sécurisée</span>
                </button>
            </form>

            <p class="connexion-note">
                Identifiants oubliés ? Adressez-vous à la Direction.<br>
                L'accès est nominatif et journalisé.
            </p>
        </div>
    </main>

    <!-- ─── LE PIED ────────────────────────────────────────────────────── -->
    <footer class="connexion-pied">
        <span><strong><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></strong> — intranet du personnel</span>
        <span>Connexion chiffrée · <?= date('Y') ?></span>
    </footer>

</body>

</html>
