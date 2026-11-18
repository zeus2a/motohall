"""
Le contrôle qui clôt la question des deux feuilles Tailwind.

LA QUESTION
-----------
La compilation produit 36 830 octets ; le fichier en ligne en fait 68 402. La
feuille plus petite est-elle complète, ou manque-t-il des classes ?

LE PRINCIPE, ET POURQUOI IL EST PLUS FORT QUE LE PRÉCÉDENT
----------------------------------------------------------
Les contrôles faits jusqu'ici lisaient les FICHIERS SOURCES. Or ce qui compte
est ce que le navigateur reçoit. On prend donc le HTML RÉELLEMENT SERVI par le
site, on en extrait chaque jeton de classe (y compris ceux des attributs
Alpine), et on vérifie qu'il est défini dans l'une des deux feuilles.

Usage :
    python controler_html_servi.py <page.html> <feuille1.css> [<feuille2.css> ...]
"""
from __future__ import annotations

import re
import sys

# Les valeurs d'attributs qui contiennent des classes.
RX_CLASS = re.compile(r"""(?:^|\s)(?:class|:class|x-bind:class)\s*=\s*(["'])(.*?)\1""", re.DOTALL)
RX_LITTERAL = re.compile(r"'([^']*)'|\"([^\"]*)\"")
RX_EXTRAIRE = re.compile(r"""class="([^"]*)\"""")

# Jetons qui ne sont pas des classes : extraits d'expressions Alpine.
BRUIT = re.compile(
    r"^(\d|[-+*/=<>!&|?:.,;()\[\]{}]|ENT_QUOTES|UTF-8|true|false|null|undefined|"
    r"currentPage|loading|toast|confirmData|navigate|showToast|updateClock|"
    r"[a-z]+\.php|[a-zA-Z]+\.(getDay|length))"
)


def classes_de_html(texte: str) -> set[str]:
    jetons: set[str] = set()
    for attr in RX_CLASS.finditer(texte):
        valeur = attr.group(2)
        sources = [valeur.split()]
        for m in RX_LITTERAL.finditer(valeur):
            sources.append((m.group(1) if m.group(1) is not None else m.group(2)).split())
        for groupe in sources:
            for brut in groupe:
                for jeton in brut.split():
                    if BRUIT.match(jeton):
                        continue
                    jetons.add(jeton)
    return jetons


def definies(chemin: str) -> set[str]:
    with open(chemin, "r", encoding="utf-8", errors="replace") as f:
        t = f.read()
    t = re.sub(r"/\*.*?\*/", " ", t, flags=re.DOTALL)
    return {m.group(1) for m in re.finditer(r"\.(-?[_a-zA-Z][_a-zA-Z0-9\-]*)", t)}


def main() -> int:
    page = sys.argv[1]
    feuilles = sys.argv[2:]

    with open(page, "r", encoding="utf-8", errors="replace") as f:
        html = f.read()

    utilisees = classes_de_html(html)
    couvertes: set[str] = set()
    for f in feuilles:
        couvertes |= definies(f)

    perdues = sorted(utilisees - couvertes)

    print(f"page lue                : {page}")
    print(f"classes utilisees       : {len(utilisees)}")
    print(f"classes definies en CSS : {len(couvertes)}")
    print(f"NON DEFINES             : {len(perdues)}")
    if perdues:
        print()
        print("--- jetons non definis (a examiner : classe oubliée, ou bruit d'expression) ---")
        for c in perdues:
            print(f"  {c}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
