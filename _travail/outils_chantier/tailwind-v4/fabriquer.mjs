/**
 * ═══════════════════════════════════════════════════════════════════════════
 * FABRIQUER LA FEUILLE DE STYLE DE MOTOHALL 2A  (Tailwind v4)
 * Créé le 21/09/2026.
 *
 * CE SCRIPT EXISTE À CAUSE D'UN PIÈGE MESURÉ. Dans Tailwind v4, la directive
 * « @source » qui dit où sont les fichiers du site ne fonctionne QUE si on lui
 * donne un CHEMIN ABSOLU DE DOSSIER. Mesuré le 21/09/2026 :
 *
 *     @source "../../../motohall2a/**\/*.php"   -> NE LIT RIEN (181 classes perdues)
 *     @source "C:/…/motohall2a/**\/*.php"       -> NE LIT RIEN
 *     @source "../../../motohall2a"             -> NE LIT RIEN
 *     @source "C:/…/motohall2a"                 -> FONCTIONNE
 *
 * Écrire ce chemin absolu à la main dans un fichier serait une bombe à retard :
 * le jour où le projet change de dossier, la feuille se fabriquerait SANS les
 * classes du site — et sans le moindre message d'erreur.
 *
 * D'où ce script : il CALCULE le chemin à partir de sa propre position, écrit
 * la feuille d'entrée, puis lance Tailwind. Rien à corriger à la main.
 *
 * UTILISATION (dans ce dossier) :
 *     npm run build
 * ═══════════════════════════════════════════════════════════════════════════
 */

import { spawnSync } from 'node:child_process';
import { existsSync, mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

// !! CHEMIN MIS À JOUR LE 21/09/2026, après le rangement de MotoHall dans
//    « deepseek-harness\sites-web\moto-hall\ », aligné sur Novaya Suites.
//    Trois niveaux au-dessus de l'atelier, on est à la racine du projet ; le
//    site est ensuite dans home\capi3439\public_html\…, EXACTEMENT comme sur
//    le serveur. C'est ce qui fait que le même code marche des deux côtés.
// !! DEUX DOSSIERS À LIRE, ET PAS UN SEUL (corrigé le 21/09/2026, mesuré).
//    Depuis que le moteur vit HORS du dossier web, la plupart des classes
//    Tailwind ne sont plus dans le site : elles sont dans les GABARITS
//    (templates\views\*.php), qui sont dans le moteur. Une compilation qui ne
//    lit que le site produit une feuille de 53 562 octets au lieu de 68 402 —
//    le site s'afficherait à moitié sans style, SANS AUCUNE ERREUR.
//    C'est la taille du fichier produit qui l'a révélé.
const ici = dirname(fileURLToPath(import.meta.url));   // …\moto-hall\_travail\outils_chantier\tailwind-v4
const sourceSite = resolve(ici, '..', '..', '..',
                           'home', 'capi3439', 'public_html', 'motohall.zeusstack.fr');
const sourceMoteur = resolve(ici, '..', '..', '..',
                             'home', 'capi3439', 'zeusstack', 'motohall');

const site = sourceSite;
const sortie = join(site, 'assets', 'css', 'tailwind.css');
const entree = join(ici, '_genere.css');

if (!existsSync(site)) {
    console.error(`!! dossier du site introuvable : ${site}`);
    console.error('   L\'atelier doit rester dans …\\moto-hall\\_travail\\outils_chantier\\');
    process.exit(1);
}
if (!existsSync(sourceMoteur)) {
    console.error(`!! moteur introuvable : ${sourceMoteur}`);
    console.error('   Les gabarits portent la plupart des classes : sans eux, la');
    console.error('   feuille serait incomplete, et le site a moitie sans style.');
    process.exit(1);
}

// Le chemin est écrit avec des « / » : Tailwind n'aime pas les « \ » de Windows.
const cheminSite = sourceSite.replace(/\\/g, '/');
const cheminMoteur = sourceMoteur.replace(/\\/g, '/');

const feuilleEntree = `/* FICHIER FABRIQUÉ AUTOMATIQUEMENT — NE PAS MODIFIER À LA MAIN.
   Produit par fabriquer.mjs le ${new Date().toISOString()}.
   Le chemin ci-dessous est calculé : c'est une obligation de Tailwind v4. */

@import "tailwindcss";

@source "${cheminSite}";
@source "${cheminMoteur}";

@theme {
    --color-slate-850: #151e2e;
    --color-slate-900: #0f172a;
    --color-slate-950: #020617;
    --color-accent-400: #38bdf8;
    --color-accent-500: #0ea5e9;
    --color-accent-600: #0284c7;
    --font-sans: Inter, system-ui, sans-serif;
    --font-display: Inter, system-ui, sans-serif;
}
`;

mkdirSync(dirname(entree), { recursive: true });
writeFileSync(entree, feuilleEntree, 'utf8');
console.log(`-- source 1 (le site)   : ${cheminSite}`);
console.log(`-- source 2 (le moteur) : ${cheminMoteur}`);
console.log(`-- feuille produite : ${sortie}`);

// !! ON LANCE NODE DIRECTEMENT SUR LE FICHIER JAVASCRIPT DE TAILWIND, ET NON SUR
//    LE « .cmd » de Windows. Mesuré le 21/09/2026 : passer par le .cmd oblige à
//    ouvrir un shell, et un programme en bac à sable ne peut pas ouvrir de tube
//    nommé — la compilation échouait avec « code null », sans message clair.
//    En visant le .mjs, il n'y a plus ni shell ni tube : ça marche.
const cli = join(ici, 'node_modules', '@tailwindcss', 'cli', 'dist', 'index.mjs');
const resultat = spawnSync(process.execPath, [cli, '-i', entree, '-o', sortie, '--minify'], {
    cwd: ici,
    stdio: 'inherit',        // !!! « inherit » et non « pipe » : un programme en
});                          //     bac à sable ne peut pas ouvrir de tube nommé.

if (resultat.status !== 0) {
    console.error(`!! la compilation a échoué (code ${resultat.status})`);
    process.exit(resultat.status ?? 1);
}

console.log('-- feuille fabriquée.');
