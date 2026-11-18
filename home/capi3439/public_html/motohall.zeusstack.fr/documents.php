<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * DOCUMENTS.PHP v5.0 - LE CONTRÔLEUR DE LIBRAIRIE AEGISS 2026
 * ═══════════════════════════════════════════════════════════════════════════
 */

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * DOCUMENTS.PHP — DOCUMENTATION DE L'ENTREPRISE
 *
 * !! FERMÉ LE 20/09/2026. La version précédente calculait
 *        $isStaff = !isset($_SESSION['user_id']);
 *    AVANT le démarrage de la session (qui a lieu dans _bootstrap.php), donc
 *    cette variable valait TOUJOURS vrai : la page était servie en accès libre
 *    et avec le gabarit « public ». MotoHall 2A étant un intranet pour le
 *    personnel, la page exige désormais une connexion, comme les autres.
 * ═══════════════════════════════════════════════════════════════════════════
 */

require_once __DIR__ . '/amorce.php';

$dir_documents = __DIR__ . '/documents/';

/**
 * !! LES LIENS NE SONT PLUS DES ADRESSES DIRECTES (19/10/2026).
 *
 *    AVANT : $base_url = 'documents/' — donc les fichiers étaient servis par le
 *    serveur web, sans que PHP n'entre dans le coup. Mesure du 19/10/2026,
 *    depuis le serveur : /documents/direction/Finances/Bilan_Mars_2026.csv
 *    répondait « 200 text/csv » À UN VISITEUR SANS AUCUNE CONNEXION. Aucun
 *    contrôle de rôle ne pouvait s'appliquer, puisqu'aucun code ne s'exécutait.
 *
 *    MAINTENANT : tout passe par fichier.php, qui vérifie l'identification ET
 *    le profil avant de servir. Le dossier documents/ est fermé par le
 *    .htaccess, donc l'ancienne adresse ne répond plus.
 */
$base_url = 'fichier.php?f=';

/**
 * Résout un dossier situé sous documents/ (tolérance de casse, refus des
 * remontées « ../ »).
 *
 * L'implémentation vit désormais dans le moteur (includes/_functions.php,
 * fonction aegis_resoudre_dans), parce que fichier.php en a besoin aussi :
 * deux copies de cette logique, c'est une faille le jour où on n'en corrige
 * qu'une. Ce nom reste pour ne pas toucher aux appels existants.
 */
function aegis_resoudre_dossier(string $racine, string $demande): ?string
{
    return aegis_resoudre_dans($racine, $demande, 'dir');
}

/**
 * API AJAX JSON PURE (Aegis Drive Explorer)
 */
if (isset($_GET['ajax_data'])) {
    header('Content-Type: application/json; charset=utf-8');
    $folder = $_GET['ajax_data'];
    $clean_folder = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $folder);
    
    // VERROU DE SÉCURITÉ DISTRICT DIRECTION
    $is_authorized = in_array(strtoupper(getRole()), ['ADMIN', 'DIRECTEUR']);
    if (stripos($folder, 'DIRECTION') !== false && !$is_authorized) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé. Niveau de sécurité insuffisant.']);
        exit;
    }

    $full_path = aegis_resoudre_dossier($dir_documents, $folder);
    if ($full_path === null || !is_dir($full_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'Dossier introuvable ou accès refusé.']);
        exit;
    }

    $entries = array_diff(scandir($full_path), ['.','..']);
    $out = ['folders' => [], 'files' => []];
    
    foreach ($entries as $entry) {
        $path = $full_path . DIRECTORY_SEPARATOR . $entry;
        $rel_path = ($folder ? $folder . '/' : '') . $entry;
        
        if (is_dir($path)) {
            $out['folders'][] = ['name' => $entry, 'path' => $rel_path];
        } else {
            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            $url = $base_url . rawurlencode($rel_path);
            $out['files'][] = [
                'name' => $entry,
                'path' => $rel_path, 
                'ext' => $ext, 
                'url' => $url
            ];
        }
    }
    
    echo json_encode($out);
    exit;
}

/**
 * API AJAX INTÉGRÉE (Fragment List) - LEGACY (Sera supprimée)
 */
if (isset($_GET['ajax_list'])) {
    header('Content-Type: text/html; charset=utf-8');
    $folder = $_GET['ajax_list'];

    // --- VERROU DE SÉCURITÉ DISTRICT DIRECTION ---
    // !! CORRIGÉ LE 20/09/2026. Ce contrôle passe MAINTENANT AVANT la lecture du
    //    cache. Avant, le fragment mis en cache par un administrateur était servi
    //    à un visiteur SANS mot de passe pendant 60 secondes (fuite mesurée :
    //    réponse identique, empreinte 3f5511cda309 au lieu du refus 691d07ca5967).
    $is_authorized = in_array(strtoupper(getRole()), ['ADMIN', 'DIRECTEUR']);
    if (stripos($folder, 'DIRECTION') !== false && !$is_authorized) {
        die("<li class='p-8 rounded-[2rem] bg-red-500/10 border border-red-500/20 text-red-500 flex flex-col items-center gap-4 animate-in zoom-in'>
                <i data-lucide='shield-alert' class='w-12 h-12'></i>
                <div class='text-center'>
                    <p class='font-display font-black uppercase tracking-widest text-xs'>Accès refusé</p>
                    <p class='text-[10px] opacity-60 font-bold mt-1 uppercase tracking-tighter'>Niveau de sécurité insuffisant pour l’espace Direction.</p>
                </div>
             </li>");
    }

    $full_path = aegis_resoudre_dossier($dir_documents, $folder);

    // Sécurité : le dossier doit exister ET rester sous /documents/
    if ($full_path === null || !is_dir($full_path)) {
        die("<li class='text-red-500 font-bold p-4 uppercase text-[10px]'>Dossier introuvable</li>");
    }

    // NETTOYAGE SÉCURITÉ (après les contrôles : le cache n'est calculé que pour
    // une demande déjà autorisée)
    $clean_folder = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $folder);

    // !! Le RÔLE fait partie du nom du cache : même si un contrôle était un jour
    //    déplacé par erreur, un visiteur ne pourrait pas lire le fragment d'un
    //    administrateur (ceinture et bretelles).
    $cache_file = ROOT_PATH . 'tmp/cache/doc_' . md5($clean_folder . '|' . strtoupper(getRole())) . '.html';

    // 1. TENTATIVE CACHE (60 SECONDES)
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < 60)) {
        echo file_get_contents($cache_file);
        exit;
    }

    $entries = array_diff(scandir($full_path), ['.','..']);
    $html = '';
    
    foreach ($entries as $entry) {
        $path = $full_path . DIRECTORY_SEPARATOR . $entry;
        $rel_path = ($folder ? $folder . '/' : '') . $entry;

        // !! ÉCHAPPEMENT (19/10/2026). Le nom du fichier est écrit par un
        //    utilisateur : il peut contenir une apostrophe, un guillemet ou une
        //    balise. Il partait BRUT dans les attributs et dans le texte —
        //    un nom de fichier pouvait donc casser la ligne, voire injecter du
        //    code. On l'échappe UNE fois, ici, et les deux branches s'en servent.
        $safe_entry = htmlspecialchars($entry, ENT_QUOTES, 'UTF-8');
        $safe_rel   = htmlspecialchars($rel_path, ENT_QUOTES, 'UTF-8');

        if (is_dir($path)) {
            $html .= "<li class='folder-node group' data-path='$safe_rel'>
                <div class='flex items-center gap-3 py-4 px-6 rounded-2xl bg-white/5 hover:bg-cyan-500/10 border border-white/5 cursor-pointer transition-all' @click='expandFolder(\$el)'>
                    <i data-lucide='folder' class='w-5 h-5 text-cyan-400 fill-cyan-400/10 group-hover:scale-110 transition-transform'></i>
                    <span class='font-bold text-white/80 tracking-widest text-sm uppercase'>$safe_entry</span>
                    <i data-lucide='chevron-right' class='w-4 h-4 ml-auto opacity-20 transition-transform text-white'></i>
                </div>
                <ul class='ml-10 mt-3 border-l border-white/5 pl-8 hidden space-y-2' data-content></ul>
            </li>";
        } else {
            $url = $base_url . rawurlencode($rel_path);
            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            $icon = match($ext) { 'pdf'=>'file-text', 'jpg','png','jpeg'=>'image', 'zip','rar'=>'archive', default=>'file' };
            $color = match($ext) { 'pdf'=>'text-red-400', 'zip','rar'=>'text-amber-400', default=>'text-cyan-400' };

            $safe_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $safe_ext = htmlspecialchars($ext, ENT_QUOTES, 'UTF-8');

            $html .= "<li class='flex items-center justify-between group py-4 px-6 rounded-2xl bg-white/[0.02] hover:bg-white/5 border border-white/5 transition-all'>
                <div class='flex items-center gap-3 w-full cursor-pointer' @click.prevent=\"\$dispatch('open-viewer', { url: '$safe_url', type: '$safe_ext', name: '$safe_entry' })\">
                    <i data-lucide='$icon' class='w-4 h-4 opacity-50' style='color:$color'></i>
                    <span class='text-xs font-bold text-white/60 uppercase tracking-widest group-hover:text-cyan-400 transition-colors line-clamp-1'>$safe_entry</span>
                </div>
                <!-- BOUTON DE SECOURS : TELECHARGEMENT FORCE -->
                <a href='$safe_url&amp;dl=1' download class='p-2 rounded-xl bg-white/5 hover:bg-emerald-500/20 text-white/20 hover:text-emerald-400 transition-all ml-4' title='Télécharger'>
                    <i data-lucide='download' class='w-4 h-4'></i>
                </a>
            </li>";
        }
    }

    // 2. SAUVEGARDE CACHE
    file_put_contents($cache_file, $html);
    
    echo $html;
    exit();
}

// 4. Préparation Rendu
$pageTitle = "Documentation";
$pageView  = "documents_view";

// 5. Toujours la coque de l'intranet (l'accès libre n'existe plus)
require_once ROOT_PATH . 'templates/layout_secure.php';
