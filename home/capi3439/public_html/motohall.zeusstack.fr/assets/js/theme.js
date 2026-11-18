/**
 * MotoHall 2A — bascule de thème (clair / sombre)
 * Créé le 19/10/2026.
 *
 * CE FICHIER EST CHARGÉ DANS LE <head> SANS « defer », ET C'EST VOULU :
 * il doit poser l'attribut data-theme AVANT que le navigateur peigne la page.
 * Sinon, une personne qui travaille en clair verrait un éclair sombre à
 * chaque page — le défaut le plus voyant d'un thème mal branché.
 *
 * TROIS RÈGLES, DANS CET ORDRE :
 *   1. le choix explicite de la personne (mémorisé dans le navigateur) ;
 *   2. à défaut, le réglage du système (clair ou sombre) ;
 *   3. à défaut, SOMBRE — l'identité visuelle du site.
 *
 * Aucune dépendance : ni Alpine, ni Lucide, ni le réseau. Si ce fichier ne
 * charge pas, le site reste simplement en sombre (le thème par défaut écrit
 * dans le HTML).
 */
(function () {
    'use strict';

    var CLE = 'motohall-theme';
    var SOMBRE = 'sombre';
    var CLAIR = 'clair';

    function themeMemorise() {
        try {
            var v = window.localStorage.getItem(CLE);
            return v === CLAIR || v === SOMBRE ? v : null;
        } catch (e) {
            return null;   // navigation privée ou stockage refusé : on l'ignore
        }
    }

    function themeDuSysteme() {
        try {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches
                ? CLAIR
                : SOMBRE;
        } catch (e) {
            return SOMBRE;
        }
    }

    function memoriser(theme) {
        try {
            window.localStorage.setItem(CLE, theme);
        } catch (e) {
            /* rien : le choix ne survivra pas à la page, mais elle s'affiche */
        }
    }

    /**
     * Applique un thème au document ET met à jour ce qui en dépend :
     *   - l'attribut data-theme (c'est lui que la feuille de style écoute) ;
     *   - la couleur de la barre du navigateur sur téléphone.
     */
    function appliquer(theme) {
        document.documentElement.setAttribute('data-theme', theme);

        var barre = document.querySelector('meta[name="theme-color"]');
        if (barre) {
            barre.setAttribute('content', theme === CLAIR ? '#eef2f8' : '#020617');
        }

        var boutons = document.querySelectorAll('[data-theme-toggle]');
        for (var i = 0; i < boutons.length; i++) {
            var versClair = theme === SOMBRE;
            boutons[i].setAttribute('aria-pressed', theme === CLAIR ? 'true' : 'false');
            boutons[i].setAttribute(
                'aria-label',
                versClair ? 'Passer en mode clair' : 'Passer en mode sombre'
            );
            boutons[i].setAttribute(
                'title',
                versClair ? 'Passer en mode clair' : 'Passer en mode sombre'
            );
        }
    }

    function themeCourant() {
        var actuel = document.documentElement.getAttribute('data-theme');
        return actuel === CLAIR ? CLAIR : SOMBRE;
    }

    // 1. LE PLUS TÔT POSSIBLE : poser le thème avant le premier rendu.
    appliquer(themeMemorise() || themeDuSysteme());

    // 2. LE BOUTON. Délégation sur le document : le bouton peut être n'importe
    //    où, y compris dans une partie de page rechargée en JavaScript.
    document.addEventListener('click', function (evenement) {
        var bouton = evenement.target.closest ? evenement.target.closest('[data-theme-toggle]') : null;
        if (!bouton) {
            return;
        }
        evenement.preventDefault();
        var suivant = themeCourant() === CLAIR ? SOMBRE : CLAIR;
        memoriser(suivant);
        appliquer(suivant);
    });

    // 3. SI LE SYSTÈME CHANGE D'AVIS et que la personne n'a rien choisi,
    //    on suit. C'est le comportement attendu d'un site bien élevé.
    try {
        if (window.matchMedia) {
            var requete = window.matchMedia('(prefers-color-scheme: light)');
            var surChangement = function () {
                if (!themeMemorise()) {
                    appliquer(themeDuSysteme());
                }
            };
            if (requete.addEventListener) {
                requete.addEventListener('change', surChangement);
            } else if (requete.addListener) {
                requete.addListener(surChangement);
            }
        }
    } catch (e) {
        /* rien : le thème reste celui déjà posé */
    }

    // 4. Rend le choix accessible aux autres scripts (utile pour un test).
    window.motohallTheme = {
        lire: themeCourant,
        poser: function (theme) {
            var t = theme === CLAIR ? CLAIR : SOMBRE;
            memoriser(t);
            appliquer(t);
        }
    };
}());
