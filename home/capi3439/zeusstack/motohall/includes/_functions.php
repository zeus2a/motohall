<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * _FUNCTIONS.PHP v5.0 - LES UTILITAIRES MÉTIER AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

/**
 * Nettoyage des sorties (Alias de clean() pour compatibilité)
 */
function escape($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Formatage de date standard (FR)
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (!$date) return 'N/A';
    return date($format, strtotime($date));
}

/**
 * Récupération des données utilisateur complètes depuis BDD
 */
function aegis_get_user($pdo) {
    if (!isset($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Détermination de l'icône Lucide selon l'extension
 */
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return match($ext) {
        'pdf'               => 'file-text',
        'jpg','jpeg','png'  => 'image',
        'zip','rar','7z'    => 'archive',
        'doc','docx'        => 'file-digit',
        'xls','xlsx'        => 'file-spreadsheet',
        default             => 'file'
    };
}

/**
 * Détermination de la couleur CSS (Tailwind) selon l'extension
 */
function getFileColor($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return match($ext) {
        'pdf'               => '#ef4444', // Red 500
        'zip','rar'         => '#f59e0b', // Amber 500
        'jpg','jpeg','png'  => '#10b981', // Emerald 500
        default             => '#06b6d4'  // Cyan 500
    };
}

/**
 * Résout un chemin demandé SOUS un dossier racine, en TOLÉRANT LA CASSE.
 *
 * !! POURQUOI CETTE FONCTION EXISTE (20/09/2026, déplacée ici le 19/10/2026) :
 *    les pages envoyaient « ATELIER » alors que le dossier s'appelle « atelier ».
 *    Sur Windows la casse ne compte pas, donc cela fonctionnait sur le poste de
 *    Frédéric ; sur le serveur Linux, realpath() échouait et la page répondait
 *    « 400 Bad Request » — mesuré en ligne sur les trois dossiers racine.
 *
 *    Elle vit maintenant dans le moteur parce que DEUX fichiers en ont besoin :
 *    documents.php (qui liste) et fichier.php (qui sert le fichier). Deux copies
 *    de cette logique, c'est une faille qui s'ouvre le jour où on n'en corrige
 *    qu'une.
 *
 * @param string $racine  dossier auquel on ne doit jamais échapper
 * @param string $demande chemin relatif, avec des « / »
 * @param string $attendu 'dir' pour un dossier, 'file' pour un fichier
 * @return string|null    chemin réel, ou null si refusé / inexistant
 */
function aegis_resoudre_dans(string $racine, string $demande, string $attendu = 'dir'): ?string
{
    $racineReelle = realpath($racine);
    if ($racineReelle === false) {
        return null;
    }

    // 1. On découpe en segments et on refuse tout « .. » AVANT de toucher au
    //    disque : aucune remontée n'est possible, même déguisée.
    $segments = [];
    foreach (explode('/', str_replace('\\', '/', $demande)) as $segment) {
        $segment = trim($segment);
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            return null;
        }
        $segments[] = $segment;
    }

    if ($segments === []) {
        return $attendu === 'dir' ? $racineReelle : null;
    }

    // 2. On reconstruit le chemin segment par segment, en comparant sans tenir
    //    compte de la casse.
    $chemin = $racineReelle;
    $dernierIndex = count($segments) - 1;

    foreach ($segments as $index => $segment) {
        $dossierAttendu = ($index !== $dernierIndex) || $attendu === 'dir';
        $trouve = null;

        foreach (scandir($chemin) ?: [] as $entree) {
            if ($entree === '.' || $entree === '..' || strcasecmp($entree, $segment) !== 0) {
                continue;
            }
            $complet = $chemin . DIRECTORY_SEPARATOR . $entree;
            if ($dossierAttendu ? is_dir($complet) : is_file($complet)) {
                $trouve = $entree;
                break;
            }
        }

        if ($trouve === null) {
            return null;
        }
        $chemin .= DIRECTORY_SEPARATOR . $trouve;
    }

    // 3. Le résultat doit être RÉELLEMENT sous la racine — ceinture et bretelles
    //    contre les liens symboliques et les cas tordus.
    $resultat = realpath($chemin);
    if ($resultat === false
        || strpos($resultat, $racineReelle . DIRECTORY_SEPARATOR) !== 0) {
        return null;
    }

    return $resultat;
}
