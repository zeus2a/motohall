<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * LOGIN.PHP — SIMPLE RENVOI VERS LA PAGE D'ENTRÉE
 *
 * REFAIT LE 18/11/2026. Ce fichier portait la vérification du mot de passe ;
 * elle vit maintenant dans `index.php`, et à un seul endroit.
 *
 * POURQUOI CE DÉPLACEMENT
 * -----------------------
 * Il y avait DEUX pages capables d'afficher un formulaire de connexion :
 * `login.php` (pour les erreurs) et `index.php` (le portail public). Deux
 * formulaires, c'est deux fois le risque d'en oublier un le jour où l'on
 * ajoute une protection — jeton CSRF, limitation des tentatives, message
 * d'erreur neutre.
 *
 * Il n'y en a plus qu'un : `index.php`. Ce fichier conserve son nom pour que
 * les adresses déjà connues (favoris, liens, redirections de `_auth.php`)
 * continuent de fonctionner, et pour ne casser aucun signet.
 * ═══════════════════════════════════════════════════════════════════════════
 */

// On ne charge même pas le moteur : il n'y a rien à calculer pour renvoyer
// quelqu'un vers la page d'entrée. C'est aussi une page qui répond vite,
// puisqu'elle n'ouvre pas de session ni de base de données.
$cible = 'index.php';

// On conserve les motifs de renvoi éventuels (déconnexion, accès refusé), pour
// que le message s'affiche correctement après le saut.
$params = [];
foreach (['status', 'error', 'connexion_requise'] as $cle) {
    if (isset($_GET[$cle])) {
        $params[$cle] = (string)$_GET[$cle];
    }
}
if ($params !== []) {
    $cible .= '?' . http_build_query($params);
}

header('Location: ' . $cible, true, 302);
exit;
