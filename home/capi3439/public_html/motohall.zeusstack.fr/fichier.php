<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * FICHIER.PHP — LE SERVICE DE FICHIERS DE LA DOCUMENTATION
 * Créé le 19/10/2026.
 *
 * POURQUOI CE FICHIER EXISTE
 * --------------------------
 * Mesure du 19/10/2026, faite depuis le serveur lui-même :
 *
 *     /documents/direction/Finances/Bilan_Mars_2026.csv  ->  200  text/csv
 *
 * C'est-à-dire : LE DOSSIER DIRECTION ÉTAIT TÉLÉCHARGEABLE SANS AUCUNE
 * CONNEXION. Pas besoin de compte, pas besoin d'être du personnel : il suffisait
 * de connaître l'adresse. Les fichiers sont petits aujourd'hui, mais le
 * lendemain d'un dépôt de vrai document, c'est une fuite.
 *
 * La cause n'est pas un oubli de contrôle : les liens de téléchargement étaient
 * des ADRESSES DIRECTES (documents/…), donc servies par le serveur web, sans
 * que PHP n'entre jamais dans le coup. Aucun contrôle de rôle ne pouvait s'y
 * appliquer.
 *
 * CE QUE FAIT CE FICHIER
 * ----------------------
 * Il devient le seul chemin d'accès aux documents. Il vérifie DEUX choses :
 *   1. que la personne est identifiée (sinon 403) ;
 *   2. que son profil a le droit d'ouvrir CE dossier (Direction = ADMIN ou
 *      DIRECTEUR, sinon 403).
 * Puis il sert le fichier. Le dossier `documents/` est fermé par le .htaccess :
 * plus aucune adresse directe ne fonctionne.
 *
 * ⚠️ L'ORDRE COMPTE : on vérifie AVANT de lire quoi que ce soit sur le disque,
 *    et on ne renvoie jamais le chemin résolu dans un message d'erreur.
 * ═══════════════════════════════════════════════════════════════════════════
 */

// Cette page gère elle-même l'accès (elle répond 403, pas une redirection :
// une image ou un cadre qui reçoit une page de connexion n'affiche rien
// d'utile). PAGE_PUBLIC empêche _auth.php de rediriger avant qu'on ait pu
// répondre ; le contrôle est fait juste en dessous, et il est le seul.
define('PAGE_PUBLIC', true);
require_once __DIR__ . '/amorce.php';

/** Réponse courte, en texte, pour un appel de fichier. */
function refuser(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-store');
    echo $message;
    exit;
}

// ─── 1. IDENTIFICATION ────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    refuser(403, "Accès refusé : identification nécessaire.");
}

// ─── 2. DROITS SUR LE DOSSIER ─────────────────────────────────────────────
$demande = (string)($_GET['f'] ?? '');
$demande = str_replace('\\', '/', $demande);

$role = strtoupper(getRole());
$roleDirection = in_array($role, ['ADMIN', 'DIRECTEUR'], true);

if (preg_match('#^direction(/|$)#i', $demande) === 1 && !$roleDirection) {
    refuser(403, "Accès refusé : cet espace ne fait pas partie de votre profil.");
}

// ─── 3. RÉSOLUTION DU CHEMIN ──────────────────────────────────────────────
// Même fonction que documents.php (elle vit dans le moteur) : tolérante à la
// casse, et incapable de sortir du dossier documents/.
$racine = __DIR__ . '/documents';
$chemin = aegis_resoudre_dans($racine, $demande, 'file');

if ($chemin === null) {
    refuser(404, "Fichier introuvable.");
}

// ─── 4. LE FICHIER ────────────────────────────────────────────────────────
// !! Le site démarre un tampon de sortie compressé (ob_gzhandler dans
//    _bootstrap.php). Il est parfait pour du HTML et catastrophique pour un
//    fichier binaire : il le compresse et fausse l'annonce de taille. On vide
//    donc les tampons avant d'envoyer.
while (ob_get_level() > 0) {
    ob_end_clean();
}

$nom   = basename($chemin);
$taille = filesize($chemin);
$ext   = strtolower(pathinfo($nom, PATHINFO_EXTENSION));

// Le type doit être EXACT : le site envoie « X-Content-Type-Options: nosniff »,
// donc le navigateur ne devine plus rien. Se tromper ici casse l'affichage.
$types = [
    'txt'  => 'text/plain; charset=utf-8',
    'csv'  => 'text/csv; charset=utf-8',
    'pdf'  => 'application/pdf',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'svg'  => 'image/svg+xml',
    'json' => 'application/json; charset=utf-8',
    'zip'  => 'application/zip',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
];

$type = $types[$ext] ?? 'application/octet-stream';

// ?dl=1 force le téléchargement ; sans lui on affiche dans le navigateur
// (c'est ce qu'utilise la visionneuse du site).
$telecharger = isset($_GET['dl']);

// Nom de fichier : on donne les deux formes prévues par la norme, sinon un nom
// accentué comme « Procédure_Sécurité.txt » sort écorché chez certains clients.
$nomAscii  = preg_replace('/[^\x20-\x7E]/', '_', $nom);
$nomEncode = rawurlencode($nom);

header('Content-Type: ' . $type);
header('Content-Length: ' . $taille);
header('Content-Disposition: '
    . ($telecharger ? 'attachment' : 'inline')
    . '; filename="' . str_replace('"', '', (string)$nomAscii) . '"'
    . "; filename*=UTF-8''" . $nomEncode);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=600');
header('Content-Security-Policy: default-src \'none\'; sandbox');

readfile($chemin);
exit;
