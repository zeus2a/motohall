"""
Contrôle de contraste SUR LES CAPTURES, pas sur les intentions.

Principe : on lit les pixels réellement produits par Chrome pour chaque écran,
on prend les couleurs dominantes d'une zone de texte, et on calcule le ratio
WCAG entre le texte et le fond de la même zone. C'est la seule mesure qui
prouve que la feuille de style applique bien les rôles attendus — un jeton
défini mais jamais appliqué passerait inaperçu autrement.

Usage :
    python mesurer_captures.py <dossier_captures>
"""
from __future__ import annotations

import os
import sys
from collections import Counter

from PIL import Image

# La palette attendue, pour rapprocher un pixel observé d'un rôle.
ATTENDUS = {
    "#4d5d76": "--texte-faible (clair)",
    "#39485f": "--texte-doux (clair)",
    "#0c1526": "--texte (clair)",
    "#0369a1": "--accent (clair)",
    "#93a3bd": "--texte-faible (sombre)",
    "#b7c4d8": "--texte-doux (sombre)",
    "#eaf0f9": "--texte (sombre)",
    "#5cc8fb": "--accent (sombre)",
}


def rgb_vers_hex(rgb: tuple[int, int, int]) -> str:
    return "#%02x%02x%02x" % rgb[:3]


def luminance(rgb: tuple[int, int, int]) -> float:
    c = []
    for v in rgb[:3]:
        x = v / 255.0
        c.append(x / 12.92 if x <= 0.03928 else ((x + 0.055) / 1.055) ** 2.4)
    return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]


def ratio(a: tuple[int, int, int], b: tuple[int, int, int]) -> float:
    la, lb = luminance(a), luminance(b)
    return (max(la, lb) + 0.05) / (min(la, lb) + 0.05)


def zones_de(chemin: str) -> dict[str, tuple[int, int, int, int]]:
    """Les zones à mesurer. Elles diffèrent entre les écrans larges (avec menu)
    et les écrans étroits (connexion, erreur)."""
    if "connexion" in chemin or "erreur" in chemin:
        return {
            "sous-titre de page": (10, 250, 400, 285),
            "texte courant": (10, 285, 400, 330),
            "bouton d'accent": (150, 480, 250, 520),
        }
    return {
        "intitule de section (menu)": (26, 222, 110, 240),
        "texte courant du menu": (66, 150, 160, 172),
        "texte secondaire (sous-titre)": (70, 74, 215, 90),
    }


def mesurer(chemin: str) -> None:
    image = Image.open(chemin).convert("RGB")
    largeur, hauteur = image.size
    nom = os.path.basename(chemin)

    print()
    print("=" * 92)
    print(f"{nom}   ({largeur}×{hauteur})")
    print("=" * 92)

    for etiquette, boite in zones_de(chemin).items():
        x0, y0, x1, y1 = boite
        x1, y1 = min(x1, largeur), min(y1, hauteur)
        if x0 >= x1 or y0 >= y1:
            continue

        pixels = list(image.crop((x0, y0, x1, y1)).getdata())
        compteur = Counter(pixels)
        total = len(pixels)

        # Le fond = la couleur la plus fréquente de la zone.
        fond, n_fond = compteur.most_common(1)[0]
        # Le texte = parmi les 6 couleurs les plus fréquentes, celle qui
        # s'éloigne le plus du fond (c'est le glyphe).
        candidats = [c for c, _ in compteur.most_common(6)]
        texte = max(candidats, key=lambda c: ratio(c, fond))
        r = ratio(texte, fond)

        proche = ""
        for hexa, role in ATTENDUS.items():
            e = tuple(int(hexa[i:i + 2], 16) for i in (1, 3, 5))
            if sum(abs(a - b) for a, b in zip(e, texte)) < 24:
                proche = f"  [role attendu : {role}]"
                break

        verdict = "OK AAA" if r >= 7 else ("OK AA" if r >= 4.5 else "SOUS LE SEUIL")
        print(f"  {etiquette:32s} texte {rgb_vers_hex(texte)}  fond {rgb_vers_hex(fond)}"
              f"  ratio {r:6.2f}  {verdict}{proche}")


def main() -> int:
    dossier = sys.argv[1]
    fichiers = sorted(
        os.path.join(dossier, f) for f in os.listdir(dossier) if f.endswith(".png")
    )
    for f in fichiers:
        mesurer(f)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
