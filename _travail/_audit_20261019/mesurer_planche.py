"""
Mesure des contrastes SUR LA PLANCHE DE PREUVE.

La planche (preuve_sombre.html / preuve_clair.html) est composée de lignes de
64 px de haut, dans un ordre connu. On lit donc chaque ligne à sa position
exacte — aucune zone devinée — et on sort les trois couleurs dominantes de la
bande :

  1re couleur = le FOND de la ligne ;
  2e et 3e  = les couleurs posées dessus (le texte, ou l'aplat d'un bouton).

On calcule le ratio WCAG entre la 1re et chacune des autres. C'est la seule
mesure qui dit ce que le navigateur a réellement peint.

Usage :
    python mesurer_planche.py <preuve-sombre.png> <preuve-clair.png>
"""
from __future__ import annotations

import sys
from collections import Counter

from PIL import Image

HAUTEUR_LIGNE = 64

# L'ordre EXACT des lignes des deux planches.
LIGNES = [
    "role · texte / fond",
    "role · texte / carte",
    "role · texte-doux / carte",
    "role · texte-faible / carte",
    "role · accent / carte",
    "role · succes / carte",
    "role · alerte / carte",
    "role · danger / carte",
    "PONT text-slate-500",
    "PONT text-slate-600",
    "PONT text-white",
    "PONT text-slate-400",
    "PONT text-slate-300",
    "PONT titre-section",
    "PONT .meta",
    "PONT text-[0.72rem]",
    "PONT text-[10px]",
    "PONT text-emerald-400",
    "PONT text-red-400",
    "PONT text-[#38bdf8]",
    "COMPOSANT titre-page",
    "COMPOSANT panneau lateral",
    "COMPOSANT ariane",
]


def luminance(rgb):
    c = []
    for v in rgb[:3]:
        x = v / 255.0
        c.append(x / 12.92 if x <= 0.03928 else ((x + 0.055) / 1.055) ** 2.4)
    return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]


def ratio(a, b):
    la, lb = luminance(a), luminance(b)
    return (max(la, lb) + 0.05) / (min(la, lb) + 0.05)


def hexa(rgb):
    return "#%02x%02x%02x" % tuple(rgb[:3])


def mesurer(chemin: str, titre: str) -> None:
    image = Image.open(chemin).convert("RGB")
    largeur, hauteur = image.size
    print()
    print("=" * 104)
    print(f"{titre}  —  image {largeur}x{hauteur}")
    print("=" * 104)
    print(f"{'ligne':34s} {'fond':9s} {'couleur posee':14s} {'ratio':>7s}  verdict")
    print("-" * 104)

    for i, etiquette in enumerate(LIGNES):
        y0 = i * HAUTEUR_LIGNE
        y1 = min(y0 + HAUTEUR_LIGNE, hauteur)
        if y0 >= hauteur:
            break

        bande = image.crop((0, y0, min(900, largeur), y1))
        pixels = list(bande.get_flattened_data()) if hasattr(bande, "get_flattened_data") else list(bande.getdata())
        compteur = Counter(pixels)

        # Le fond : la couleur la plus fréquente de la bande.
        fond, _ = compteur.most_common(1)[0]

        # La couleur posée dessus : la plus fréquente qui soit nettement
        # différente du fond. Le seuil de 40 évite d'attraper les pixels de
        # bordure (antialiasing) qui, eux, ne veulent rien dire.
        posee = None
        for couleur, nombre in compteur.most_common(40):
            if abs(luminance(couleur) - luminance(fond)) > 0.02 and nombre >= 25:
                posee = couleur
                break

        if posee is None:
            print(f"{etiquette:34s} {hexa(fond):9s} {'(aucun texte)':14s}")
            continue

        r = ratio(posee, fond)
        verdict = "OK AAA" if r >= 7 else ("OK AA" if r >= 4.5 else "SOUS 4,5")
        print(f"{etiquette:34s} {hexa(fond):9s} {hexa(posee):14s} {r:7.2f}  {verdict}")


def main() -> int:
    for chemin, titre in ((sys.argv[1], "THEME SOMBRE"), (sys.argv[2], "THEME CLAIR")):
        mesurer(chemin, titre)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
