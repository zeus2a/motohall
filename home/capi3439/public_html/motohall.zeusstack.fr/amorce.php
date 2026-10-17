<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * AMORCE.PHP — LE POINT D'ENTRÉE UNIQUE DU CODE
 * Créé le 20/09/2026.
 *
 * À QUOI ÇA SERT
 * --------------
 * Le « moteur » du site (le dossier includes\ qui contient la configuration,
 * donc LE MOT DE PASSE DE LA BASE, et le dossier templates\ qui contient les
 * gabarits) doit vivre HORS du dossier servi par le web — c'est ce que fait
 * déjà Novaya Suites, et c'est ce que Frédéric a demandé le 20/09/2026.
 *
 * Mais le site doit continuer à fonctionner AUSSI en local, où le moteur est
 * simplement à côté des pages. Plutôt que d'écrire un chemin différent dans
 * chaque page (et de se tromper un jour), TOUTES les pages passent par ce
 * fichier : c'est LUI qui sait où se trouve le moteur.
 *
 * Ordre de recherche :
 *   1. à côté, dans le dossier du site  (poste de Frédéric, essais locaux) ;
 *   2. hors du dossier web              (en production : ~/zeusstack/motohall/,
 *                                        soit deux niveaux au-dessus du dossier
 *                                        du site).
 *
 * !! ~/zeusstack/ EST AUSSI LE MOTEUR DE NOVAYA (zeusstack_diag.php, l'état du
 *    site...). MotoHall y vit dans SON sous-dossier (zeusstack/motohall/), comme
 *    Frédéric l'a voulu, et la sauvegarde de Novaya EXCLUT ce sous-dossier :
 *    les deux projets ne se retrouvent donc jamais dans les mêmes archives.
 *
 * Si ni l'un ni l'autre n'existe, on le DIT clairement au lieu d'afficher une
 * page blanche : une erreur muette coûte des heures.
 * ═══════════════════════════════════════════════════════════════════════════
 */

$candidats = [
    // 1. le moteur est à côté des pages (poste local, essais)
    __DIR__ . '/includes/_auth.php',
    // 2. le moteur est hors du dossier web (production) : ~/zeusstack/motohall/
    dirname(__DIR__, 2) . '/zeusstack/motohall/includes/_auth.php',
];

$trouve = null;
foreach ($candidats as $candidat) {
    if (is_file($candidat)) {
        $trouve = $candidat;
        break;
    }
}

if ($trouve === null) {
    http_response_code(500);
    error_log('AMORCE: moteur introuvable. Cherche : ' . implode(' | ', $candidats));
    die("Configuration introuvable : le moteur du site n'a pas été trouvé. "
        . "Contactez l'administrateur.");
}

require_once $trouve;
