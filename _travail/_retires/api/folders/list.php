<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * AEGIS API - FOLDER LIST v5.0
 * ═══════════════════════════════════════════════════════════════════════════
 */

$dir_documents = __DIR__ . '/../../documents/';
$service = clean($_GET['service'] ?? '');
$full_path = realpath($dir_documents . $service);

// Sécurité : Interdire la remontée hors de /documents/
if ($full_path === false || strpos($full_path, realpath($dir_documents)) !== 0) {
    http_response_code(403);
    die(json_encode(['success' => false, 'error' => 'Permission denied']));
}

$entries = array_diff(scandir($full_path), ['.', '..']);
$result = [];

foreach ($entries as $entry) {
    $path = $full_path . DIRECTORY_SEPARATOR . $entry;
    if (is_dir($path)) {
        $result[] = [
            'name' => $entry,
            'type' => 'folder',
            'path' => $service . ($service ? '/' : '') . $entry
        ];
    }
}

echo json_encode(['success' => true, 'folders' => $result]);
exit;
