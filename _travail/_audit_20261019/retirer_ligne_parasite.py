"""
Retire la ligne parasite « -- code de sortie : 0 » laissée par l'outil SSH.

Cause : pour récupérer des fichiers depuis le serveur, la sortie de
ssh_o2switch.py a été redirigée telle quelle. Cet outil ajoute une ligne de
compte rendu APRÈS le contenu du fichier ; elle s'est donc retrouvée à la fin
de quatre gabarits, et le serveur l'a affichée en clair au milieu des pages.

On retire la ligne ENTIÈRE et rien d'autre : le test est une égalité stricte,
pas une recherche de motif.

Usage : python retirer_ligne_parasite.py <fichier> [<fichier> ...]
"""
from __future__ import annotations

import sys

PARASITE = "-- code de sortie : 0"


def nettoyer(chemin: str) -> bool:
    with open(chemin, "r", encoding="utf-8", newline="") as f:
        lignes = f.readlines()

    gardees = [l for l in lignes if l.strip() != PARASITE]
    if len(gardees) == len(lignes):
        print(f"  inchange   {chemin}")
        return False

    with open(chemin, "w", encoding="utf-8", newline="") as f:
        f.writelines(gardees)
    print(f"  nettoye    {chemin}  ({len(lignes) - len(gardees)} ligne(s) retiree(s))")
    return True


def main() -> int:
    for chemin in sys.argv[1:]:
        nettoyer(chemin)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
