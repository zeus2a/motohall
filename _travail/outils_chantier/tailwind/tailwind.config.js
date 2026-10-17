/**
 * ═══════════════════════════════════════════════════════════════════════════
 * TAILWIND — REGLAGES DE MOTOHALL 2A
 * Créé le 21/09/2026, pour remplacer le « Play CDN » par une feuille compilée.
 *
 * ⚠️ LA VERSION DE TAILWIND EST FIXÉE À 3.4.17, ET CE N'EST PAS UN DÉTAIL :
 *    c'est exactement la version que servait https://cdn.tailwindcss.com
 *    (mesuré le 21/09/2026). La version 4 change des choses invisibles au
 *    premier regard — la couleur des bordures par défaut, le nom de plusieurs
 *    ombres, le réglage des contours. Compiler avec la 4 aurait modifié
 *    l'apparence du site sans qu'on l'ait demandé.
 *
 * ⚠️ LE THÈME CI-DESSOUS EST LA COPIE EXACTE du bloc « tailwind.config »
 *    qui était écrit dans templates/layout_secure.php. Rien n'a été ajouté,
 *    rien n'a été retiré : la feuille compilée doit rendre PAREIL.
 * ═══════════════════════════════════════════════════════════════════════════
 */

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "../../../motohall2a/**/*.php",
        "../../../motohall2a/**/*.html",
        "../../../motohall2a/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                slate: { 850: '#151e2e', 900: '#0f172a', 950: '#020617' },
                accent: { 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7' },
            },
            fontFamily: {
                sans: ['Rajdhani', 'sans-serif'],
                display: ['Orbitron', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
