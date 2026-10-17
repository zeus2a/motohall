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
