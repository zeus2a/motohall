<?php
/**
 * FABRIQUER LES APERÇUS — banc d'essai visuel de MotoHall 2A
 * Créé le 19/10/2026.
 *
 * À QUOI ÇA SERT
 * --------------
 * Voir le rendu réel de la refonte (coque, portail, page de connexion, page
 * d'erreur) dans les DEUX thèmes, sans base de données et sans toucher au
 * site. On produit des fichiers .html que Chrome photographie ensuite.
 *
 * POURQUOI CE SCRIPT PREND UN ARGUMENT
 * ------------------------------------
 * Les gabarits s'incluent en « require_once ». Deux rendus dans le MÊME
 * processus et le second est vide (PHP saute l'inclusion déjà faite). Chaque
 * écran est donc fabriqué par un processus neuf : une invocation = un écran.
 *
 *   php fabriquer_apercus.php <scenario>
 *
 * Les scénarios : portail-visiteur, portail-admin, portail-vendeur, comptoir,
 * atelier, profil, connexion, erreur404, erreur403.
 *
 * CE QUI EST SIMULÉ, ET CE QUI NE L'EST PAS
 * -----------------------------------------
 * Simulé : la session, les trois fonctions du moteur (stop, getRole, csrf) et
 *          la connexion à MySQL.
 * Réel   : les fichiers de gabarit, la feuille de style, les scripts.
 * La page de connexion est rendue depuis une COPIE de son fichier, placée
 * dans sortie_site/ avec un moteur bouchon : c'est le seul moyen de l'exécuter
 * sans base. Son balisage est celui du vrai fichier, à l'identique.
 */

declare(strict_types=1);

$ici    = __DIR__;
$projet = dirname($ici, 3);
$site   = $projet . '/home/capi3439/public_html/motohall.zeusstack.fr';
$moteur = $projet . '/home/capi3439/zeusstack/motohall';

if (!is_dir($site) || !is_dir($moteur)) {
    fwrite(STDERR, "!! dossier introuvable\n  site   : $site\n  moteur : $moteur\n");
    exit(1);
}

$sortie     = $ici . '/sortie';
$sortieSite = $ici . '/sortie_site';
foreach ([$sortie, $sortieSite] as $d) {
    if (!is_dir($d)) {
        mkdir($d, 0777, true);
    }
}

$scenario = $argv[1] ?? '';
if ($scenario === '') {
    fwrite(STDERR, "usage : php fabriquer_apercus.php <scenario>\n");
    exit(2);
}

// ---------------------------------------------------------------------------
// Les briques que le moteur fournirait normalement.
// ---------------------------------------------------------------------------

define('SITE_NAME', 'MotoHall 2A');
define('ROOT_PATH', $moteur . '/');
define('INC_PATH',  $moteur . '/includes/');
define('IS_FRAGMENT', false);

function stop($message = 'Accès refusé')
{
    die((string)$message);
}

$GLOBALS['__role'] = 'GUEST';

function getRole()
{
    return $GLOBALS['__role'];
}

function csrf()
{
    return 'jeton-de-test-non-signe';
}

// Le script se présente à la racine du site : les chemins calculés par la
// coque tombent ainsi sur « /assets/… », ramenés en relatif plus bas.
$_SERVER['PHP_SELF']       = '/index.php';
$_SERVER['SCRIPT_NAME']    = '/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME']    = '127.0.0.1';

/**
 * Écrit l'aperçu dans les deux thèmes à partir d'UN SEUL rendu.
 *
 * Le balisage ne dépend pas du thème : seules les valeurs des variables CSS
 * changent, et elles sont choisies par l'attribut data-theme. On écrit donc
 * deux fichiers identiques sauf le script qui force le thème en fin de page
 * (le script de thème du site, lui, lit le réglage du système).
 */
function ecrireApercu(string $base, string $nom, string $html, string $prefixe = ''): void
{
    $html = str_replace('"/assets/', '"' . $prefixe . 'assets/', $html);
    $html = str_replace("'/assets/", "'" . $prefixe . 'assets/', $html);

    foreach (['sombre' => '', 'clair' => '--clair'] as $theme => $suffixe) {
        $force = "<script>document.documentElement.setAttribute('data-theme', '$theme');</script>\n";
        $copie = str_replace('</body>', $force . '</body>', $html);
        $chemin = $base . DIRECTORY_SEPARATOR . $nom . $suffixe . '.html';
        file_put_contents($chemin, $copie);
        echo '  ' . basename($chemin) . '  ' . number_format(strlen($copie)) . " o\n";
    }
}

// ---------------------------------------------------------------------------
// Les sessions simulées
// ---------------------------------------------------------------------------

$visiteur = [];
$admin = [
    'user_id' => 1, 'username' => 'zeus', 'role_name' => 'ADMIN',
    'prenom' => 'Frédéric', 'nom' => 'Nassau',
];
$vendeur = [
    'user_id' => 4, 'username' => 'vendeur1', 'role_name' => 'VENDEUR',
    'prenom' => '', 'nom' => '',
];

// ---------------------------------------------------------------------------
// Les pages hors coque
// ---------------------------------------------------------------------------

if (str_starts_with($scenario, 'erreur')) {
    $code = substr($scenario, 6);
    $_GET = ['code' => $code];
    $_SERVER['SCRIPT_NAME'] = '/error.php';
    ob_start();
    require $site . '/error.php';
    $html = (string)ob_get_clean();
    ecrireApercu($sortie, $scenario, $html);
    exit(0);
}

if ($scenario === 'connexion') {
    // copie du vrai fichier + moteur bouchon, pour éviter MySQL
    foreach (['login.php', 'amorce.php'] as $f) {
        copy($site . '/' . $f, $sortieSite . '/' . $f);
    }
    if (!is_dir($sortieSite . '/includes')) {
        mkdir($sortieSite . '/includes', 0777, true);
    }
    file_put_contents(
        $sortieSite . '/includes/_auth.php',
        "<?php\n"
        . "if (!defined('SITE_NAME')) { define('SITE_NAME', 'MotoHall 2A'); }\n"
        . "if (!function_exists('getRole')) { function getRole() { return 'GUEST'; } }\n"
        . "if (!function_exists('requireRole')) { function requireRole(\$r = []) { } }\n"
    );

    $_GET  = [];
    $_POST = [];
    $_SERVER['SCRIPT_NAME'] = '/login.php';
    $_SERVER['PHP_SELF']    = '/login.php';
    $_SESSION = [];

    ob_start();
    require $sortieSite . '/login.php';
    $html = (string)ob_get_clean();
    ecrireApercu($sortie, 'connexion', $html);
    exit(0);
}

// ---------------------------------------------------------------------------
// Les écrans dans la coque
// ---------------------------------------------------------------------------

$plans = [
    'portail-visiteur' => ['index_view',    'Portail',     $visiteur, 'GUEST', []],
    'portail-admin'    => ['index_view',    'Portail',     $admin,    'ADMIN', []],
    'portail-vendeur'  => ['index_view',    'Portail',     $vendeur,  'VENDEUR', []],
    'comptoir'         => ['comptoir_view', 'Comptoir',    $admin,    'ADMIN', []],
    'atelier'          => ['atelier_view',  'Atelier',     $admin,    'ADMIN', []],
    'profil'           => ['profil_view',   'Mon compte',  $admin,    'ADMIN', [
        'monCompte' => [
            'prenom' => 'Frédéric', 'nom' => 'Nassau',
            'email' => 'direction@motohall.fr',
            'date_creation' => '2026-04-01 09:00:00', 'actif' => 1,
        ],
        'message' => 'Votre mot de passe a bien été modifié.',
        'erreur'  => null,
    ]],
];

if (!isset($plans[$scenario])) {
    fwrite(STDERR, "scénario inconnu : $scenario\n");
    exit(2);
}

[$vue, $titre, $session, $role, $extra] = $plans[$scenario];

$pageTitle = $titre;
$pageView  = $vue;
$_SESSION  = $session;
$GLOBALS['__role'] = $role;
extract($extra, EXTR_SKIP);

ob_start();
require ROOT_PATH . 'templates/layout_secure.php';
$html = (string)ob_get_clean();

ecrireApercu($sortie, $scenario, $html);
