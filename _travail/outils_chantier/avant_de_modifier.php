<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * AVANT_DE_MODIFIER.PHP — LA COPIE AVANT TOUCHER
 * Créé le 18/11/2026, à la demande de Frédéric :
 *   « toujours faire une copie avant de corriger ou modifier le code d'un
 *     fichier ».
 *
 * À QUOI ÇA SERT, ET POURQUOI GIT NE SUFFIT PAS
 * ---------------------------------------------
 * Git garde l'historique, mais il ne garde que ce qui a été COMMITÉ — et le
 * travail en cours d'une journée n'est jamais commité. Une copie datée prise
 * juste avant l'édition, elle, existe toujours, même si la session plante.
 *
 * Cette copie répond à une seule question : « remets-moi ce fichier tel qu'il
 * était il y a dix minutes ». C'est le geste réflexe, pas la stratégie.
 *
 * UTILISATION — une commande, avant chaque édition :
 *
 *     php _travail\outils_chantier\avant_de_modifier.php <fichier> [<fichier> ...]
 *
 * Exemple :
 *     php _travail\outils_chantier\avant_de_modifier.php `
 *         home\capi3439\zeusstack\motohall\templates\layout_secure.php
 *
 * La copie va dans `_travail\_backups\<AAAA-MM-JJ_HHMMSS>\` en gardant
 * l'arborescence, et un fichier `_MANIFESTE.txt` y est ajouté : qui, quand,
 * quel fichier, quelle empreinte. Ce manifeste rend la copie utilisable par
 * quelqu'un qui n'a pas fait l'édition.
 *
 * ⚠️ Le script REFUSE de copier vers l'extérieur du projet, et REFUSE de
 *    toucher un fichier qui n'existe pas (une faute de frappe ne doit pas
 *    créer une copie vide qui donnera l'illusion d'être protégé).
 * ═══════════════════════════════════════════════════════════════════════════
 */

declare(strict_types=1);

$racine = dirname(__DIR__, 2);           // …\moto-hall_laravel
$dossierBackups = $racine . DIRECTORY_SEPARATOR . '_travail' . DIRECTORY_SEPARATOR . '_backups';

if ($argc < 2) {
    fwrite(STDERR, "usage : php avant_de_modifier.php <fichier> [<fichier> ...]\n");
    exit(2);
}

$horodatage = date('Y-m-d_His');
$destination = $dossierBackups . DIRECTORY_SEPARATOR . 'avant_modif_' . $horodatage;

$manifeste = [];
$erreurs = 0;
$copies = 0;

foreach (array_slice($argv, 1) as $demande) {
    $chemin = realpath($demande);

    if ($chemin === false) {
        // On essaie aussi le chemin relatif à la racine du projet.
        $chemin = realpath($racine . DIRECTORY_SEPARATOR . $demande);
    }

    if ($chemin === false || !is_file($chemin)) {
        fwrite(STDERR, "!! fichier introuvable : $demande\n");
        $erreurs++;
        continue;
    }

    // Refus de sortir du projet : une copie ailleurs ne serait pas protégée.
    if (strpos($chemin, $racine) !== 0) {
        fwrite(STDERR, "!! hors du projet, refusé : $demande\n");
        $erreurs++;
        continue;
    }

    $relatif = substr($chemin, strlen($racine) + 1);
    $cible = $destination . DIRECTORY_SEPARATOR . $relatif;

    $dossierCible = dirname($cible);
    if (!is_dir($dossierCible)) {
        mkdir($dossierCible, 0777, true);
    }

    if (!copy($chemin, $cible)) {
        fwrite(STDERR, "!! copie impossible : $relatif\n");
        $erreurs++;
        continue;
    }

    $empreinte = hash_file('sha256', $chemin);
    $identique = hash_file('sha256', $cible) === $empreinte;

    $manifeste[] = sprintf(
        "%-58s %8d o  sha256:%s  %s",
        $relatif,
        filesize($chemin),
        substr((string)$empreinte, 0, 16),
        $identique ? 'copie verifiee' : '!! EMPREINTE DIFFERENTE'
    );

    echo "  sauvegarde : $relatif\n";
    $copies++;
}

if ($copies > 0) {
    $entete = [
        "MANIFESTE DE LA COPIE — MotoHall 2A",
        "date       : " . date('d/m/Y H:i:s'),
        "dossier    : " . $destination,
        "fichiers   : " . $copies,
        "",
        "Comment revenir en arriere : copier le fichier sauvegarde par-dessus",
        "le fichier du projet, en gardant le meme chemin relatif.",
        "",
        str_repeat('-', 100),
    ];
    file_put_contents(
        $destination . DIRECTORY_SEPARATOR . '_MANIFESTE.txt',
        implode("\n", $entete) . "\n" . implode("\n", $manifeste) . "\n"
    );

    echo "\n  -> " . $copies . " fichier(s) sauvegarde(s) dans :\n";
    echo "     " . $destination . "\n";
    echo "     (le manifeste _MANIFESTE.txt dit exactement quoi remettre et ou)\n";
}

if ($erreurs > 0) {
    echo "\n  !! " . $erreurs . " fichier(s) en erreur — voir ci-dessus.\n";
    exit(1);
}

exit(0);
