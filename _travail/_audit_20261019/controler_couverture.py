"""
Contrôle de couverture des classes CSS du site MotoHall 2A.

Principe (celui qui a déjà attrapé le piège de la v4 dans ce projet) :
  1. on lit TOUS les fichiers PHP du site et du moteur ;
  2. on extrait chaque jeton présent dans un attribut class="…", y compris
     quand il est construit par Alpine (:class="currentPage === 'x' && 'nav-lien--actif'") ;
  3. on construit l'ensemble des sélecteurs réellement définis dans
     tailwind.css et aegis.css ;
  4. on déclare « orpheline » toute classe qui n'est définie nulle part.

Usage :
    python controler_couverture.py <dossier_site> <dossier_moteur> <tailwind.css> <aegis.css>
"""

import os
import re
import sys

# --- Jetons qu'on NE compte pas : ils appartiennent à Alpine.js ou à des
#     attributs qui ne sont pas des classes CSS.
IGNORES = {
    "", "-", "&&", "||", "===", "==", "!", "?",
}

# Un attribut class / :class / x-bind:class, avec ses apostrophes éventuelles.
RX_ATTR = re.compile(
    r"""(?:^|\s)(?::class|class|x-bind:class)\s*=\s*(["'])(.*?)\1""",
    re.DOTALL,
)

# Les littéraux de chaînes à l'intérieur d'une expression Alpine.
RX_LITTERAL = re.compile(r"'([^']*)'|\"([^\"]*)\"")

# Un nom de classe plausible.
RX_JETON = re.compile(r"^-?[_a-zA-Z][_a-zA-Z0-9:_\-\[\]\/\.%#\(\),'+*~=!>@]*$")


def jetons_de_php(chemin: str) -> set[str]:
    """Extrait les classes de tous les attributs class d'un fichier PHP."""
    with open(chemin, "r", encoding="utf-8", errors="replace") as f:
        texte = f.read()

    trouves: set[str] = set()

    for attr in RX_ATTR.finditer(texte):
        valeur = attr.group(2)

        # 1. découpage naïf sur les espaces — couvre class="a b c"
        morceaux = valeur.split()
        # 2. les littéraux entre guillemets couvrent :class="x ? 'a b' : 'c'"
        litteraux: list[str] = []
        for m in RX_LITTERAL.finditer(valeur):
            litteraux.append(m.group(1) if m.group(1) is not None else m.group(2))

        for source in (morceaux, litteraux):
            for brut in source:
                for jeton in brut.split():
                    if jeton in IGNORES:
                        continue
                    if RX_JETON.match(jeton):
                        trouves.add(jeton)
    return trouves


def selecteurs_de_css(chemin: str) -> set[str]:
    """Extrait tous les noms de classe définis dans une feuille CSS."""
    with open(chemin, "r", encoding="utf-8", errors="replace") as f:
        texte = f.read()
    # On retire les commentaires, sinon ils polluent.
    texte = re.sub(r"/\*.*?\*/", " ", texte, flags=re.DOTALL)
    trouves: set[str] = set()
    for m in re.finditer(r"\.(-?[_a-zA-Z][_a-zA-Z0-9\-]*)", texte):
        trouves.add(m.group(1))
    return trouves


def main() -> int:
    dossier_site, dossier_moteur, css_tailwind, css_aegis = sys.argv[1:5]

    jetons: dict[str, list[str]] = {}
    for racine in (dossier_site, dossier_moteur):
        for dossier, _, fichiers in os.walk(racine):
            for nom in fichiers:
                if not nom.endswith(".php"):
                    continue
                chemin = os.path.join(dossier, nom)
                for jeton in jetons_de_php(chemin):
                    jetons.setdefault(jeton, []).append(chemin)

    definies = selecteurs_de_css(css_tailwind) | selecteurs_de_css(css_aegis)

    # Les classes purement maison (préfixes connus) et les jetons non-CSS.
    orphelines = {
        j: v for j, v in jetons.items()
        if j not in definies
    }

    print(f"fichiers PHP lus           : {sum(len(v) for v in [ ]) or 'n/a'}")
    print(f"jetons de classe distincts : {len(jetons)}")
    print(f"classes definies en CSS    : {len(definies)}")
    print(f"ORPHELINES (utilisees sans etre definies) : {len(orphelines)}")
    print()
    if orphelines:
        print("--- orphelines --- (la classe, puis un fichier qui l'utilise)")
        for j in sorted(orphelines):
            print(f"  {j:42s} {os.path.basename(orphelines[j][0])}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
