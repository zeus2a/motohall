<?php
/**
 * Contrôle de aegis_resoudre_dans() — la fonction qui décide QUEL fichier est servi.
 *
 * C'est le point le plus sensible de la nouvelle chaîne : si elle laisse passer
 * un « ../ », elle ouvre tout le site. On l'éprouve donc AVANT de déployer,
 * avec les cas qui comptent, et on affiche le résultat.
 *
 * Usage : php tester_resolution.php <dossier_racine_test>
 */

// Le chemin est CALCULÉ, pas écrit à la main : ce dossier d'audit peut bouger.
$projet = dirname(__DIR__, 2);
require_once $projet . '/home/capi3439/zeusstack/motohall/includes/_functions.php';

$racine = $argv[1] ?? null;
if ($racine === null) {
    fwrite(STDERR, "usage : php tester_resolution.php <racine>\n");
    exit(2);
}

$cas = [
    // [demande, attendu, réponse attendue (chemin ou null), ce que ça vérifie]
    ['atelier/Guides/Moteur_KTM.txt',        'file', 'oui',  'fichier normal, casse exacte'],
    ['ATELIER/GUIDES/MOTEUR_KTM.TXT',        'file', 'oui',  'tolérance de la casse (piège Windows/Linux)'],
    ['magasin/Inventaire_Stock.csv',         'file', 'oui',  'chemin à un niveau'],
    ['atelier',                              'dir',  'oui',  'dossier'],
    ['direction/Finances',                   'dir',  'oui',  'sous-dossier'],
    ['',                                     'dir',  'oui',  'la racine elle-même'],
    // --- ce qui doit ÊTRE REFUSÉ ---
    ['../includes/_auth.php',                'file', null,   'remontée par « ../ »'],
    ['atelier/../../includes/_auth.php',     'file', null,   'remontée au milieu du chemin'],
    ['..%2F..%2Fincludes',                   'file', null,   'remontée encodée'],
    ['/etc/passwd',                          'file', null,   'chemin absolu'],
    ['NIMPORTEQUOI',                         'file', null,   'inexistant'],
    ['atelier/Guides',                       'file', null,   'un dossier demandé comme fichier'],
    ['',                                     'file', null,   'racine demandée comme fichier'],
    ['atelier/Guides/Moteur_KTM.txt',        'dir',  null,   'un fichier demandé comme dossier'],
];

$echecs = 0;
$total  = 0;

foreach ($cas as [$demande, $attendu, $verdict, $libelle]) {
    $total++;
    $obtenu = aegis_resoudre_dans($racine, $demande, $attendu);

    $ok = ($verdict === 'oui') ? ($obtenu !== null) : ($obtenu === null);

    printf(
        "  %-4s  %-38s %-5s  %-58s %s\n",
        $ok ? 'OK' : 'ECHEC',
        '"' . $demande . '"',
        $attendu,
        $libelle,
        $obtenu === null ? 'refusé' : substr($obtenu, strlen(realpath($racine)) + 1)
    );

    if (!$ok) {
        $echecs++;
    }
}

echo "\n";
printf("  %d cas, %d échec(s)\n", $total, $echecs);
exit($echecs === 0 ? 0 : 1);
