<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ERROR.PHP — PAGE D'ERREUR UNIQUE
 * Refonte visuelle du 20/09/2026.
 *
 * !! SÉCURITÉ (corrigée le 20/09/2026) : le paramètre « code » vient de
 *    l'adresse. Il n'était ni validé ni échappé : une adresse du type
 *    error.php?code=<script>…</script> faisait exécuter du code dans le
 *    navigateur (mesuré). On n'accepte donc PLUS QUE trois chiffres.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$codeDemande = (string)($_GET['code'] ?? '');
$code = preg_match('/^[0-9]{3}$/', $codeDemande) ? $codeDemande : '404';

// !! CETTE PAGE DOIT ÊTRE AUTONOME. Elle est appelée par le .htaccess (404, 403,
//    500...) et peut donc s'exécuter SANS que la configuration ait été chargée :
//    ni SITE_NAME, ni la base de données. C'est pour cela que les constantes sont
//    définies ici si elles manquent — sans quoi la page d'erreur provoquait
//    elle-même une erreur 500 (constaté et corrigé le 20/09/2026).
if (!defined('SITE_NAME')) { define('SITE_NAME', 'MotoHall 2A'); }

// !! LES CHEMINS DES FICHIERS SONT CALCULÉS, PAS ÉCRITS (ajouté le 21/09/2026).
//    Cette page est appelée pour N'IMPORTE QUELLE adresse du site
//    (/dossier/inexistant, par exemple). Une adresse relative « assets/css/… »
//    chercherait alors « /dossier/assets/css/… » — c'est-à-dire rien.
//    Ça ne se voyait pas avant : Tailwind et Lucide venaient d'un CDN, donc
//    d'une adresse complète. Depuis qu'ils sont DANS le site, il faut ce calcul,
//    sinon la page d'erreur s'afficherait sans style dès qu'on s'éloigne de la
//    racine.
//    dirname(SCRIPT_NAME) vaut « / » en production (le site est à la racine du
//    sous-domaine) et « /motohall2a » sur le poste de Frédéric : les deux marchent.
$base = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/error.php'))), '/') . '/';

$errors = [
    '400' => ['titre' => 'Requête invalide',        'texte' => 'La demande n\'a pas pu être traitée par le serveur.',                          'icone' => 'alert-circle'],
    '401' => ['titre' => 'Accès restreint',         'texte' => 'Une identification valide est nécessaire pour consulter cette page.',         'icone' => 'lock'],
    '403' => ['titre' => 'Accès interdit',          'texte' => 'Vous n\'avez pas les droits nécessaires pour consulter cette page.',          'icone' => 'shield-alert'],
    '404' => ['titre' => 'Page introuvable',        'texte' => 'La page demandée a été déplacée, supprimée, ou l\'adresse est erronée.',       'icone' => 'search-x'],
    '500' => ['titre' => 'Erreur du serveur',       'texte' => 'Une erreur interne est survenue. L\'incident a été enregistré.',              'icone' => 'server-crash'],
    '502' => ['titre' => 'Erreur de passerelle',    'texte' => 'Le serveur a renvoyé une réponse invalide.',                                  'icone' => 'wifi-off'],
    '503' => ['titre' => 'Service indisponible',    'texte' => 'Le service est momentanément indisponible. Merci de réessayer plus tard.',     'icone' => 'clock'],
];

$e = $errors[$code] ?? ['titre' => 'Erreur', 'texte' => 'Une anomalie est survenue.', 'icone' => 'alert-triangle'];
?>
<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($e['titre'], ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- ─── FEUILLE COMPILÉE + LUCIDE, SERVIES PAR LE SITE (21/09/2026) ─────
         AVANT : le « Play CDN » de Tailwind et « lucide@latest » chez unpkg.com.
         MAINTENANT : deux fichiers du site, à versions figées. Les chemins sont
         calculés au-dessus ($base) parce que cette page répond à n'importe
         quelle adresse — voir le commentaire en haut du fichier.
      -->
    <link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/css/tailwind.css?v=20260921">
    <script src="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/js/lucide.min.js?v=20260921"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/img/favicon-180.png">

    <link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/css/aegis.css?v=20260921">
</head>

<body class="min-h-screen bg-[#020617] text-slate-200 font-sans flex flex-col">

    <header class="border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-3">
            <div class="w-9 h-9 rounded-[10px] bg-[#38bdf8] flex items-center justify-center
                        text-[#04121c] font-display font-bold text-sm">MH</div>
            <span class="font-display text-[0.98rem] font-semibold text-white">MotoHall 2A</span>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="carte max-w-md w-full text-center apparition">
            <div class="w-12 h-12 rounded-[12px] bg-[#38bdf8]/10 border border-[#38bdf8]/20
                        flex items-center justify-center mx-auto mb-5">
                <i data-lucide="<?= htmlspecialchars($e['icone'], ENT_QUOTES, 'UTF-8') ?>" class="w-6 h-6 text-[#38bdf8]"></i>
            </div>

            <p class="font-display text-4xl font-semibold text-white mb-2">
                <?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>
            </p>

            <h1 class="font-display text-[1.05rem] font-semibold text-white mb-3">
                <?= htmlspecialchars($e['titre'], ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <p class="text-[0.88rem] text-slate-400 leading-relaxed mb-7">
                <?= htmlspecialchars($e['texte'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div class="grid grid-cols-2 gap-3">
                <a href="index.php" class="btn btn-neutre">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Accueil</span>
                </a>
                <a href="javascript:history.back()" class="btn btn-neutre">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Retour</span>
                </a>
            </div>
        </div>
    </main>

    <footer class="border-t border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-12 flex items-center justify-between text-[0.72rem] text-slate-500">
            <span>Intranet MotoHall 2A</span>
            <span><?= date('Y') ?></span>
        </div>
    </footer>

    <script>if (window.lucide) lucide.createIcons();</script>
</body>

</html>
