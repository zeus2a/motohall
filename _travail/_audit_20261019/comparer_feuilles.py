import re
import sys

CHEMIN_BON = sys.argv[1]
CHEMIN_NEUF = sys.argv[2]

RX = re.compile(r"\.(-?[_a-zA-Z][_a-zA-Z0-9\-]*)")


def selecteurs(chemin: str) -> set[str]:
    with open(chemin, "r", encoding="utf-8") as f:
        txt = f.read()
    trouves: set[str] = set()
    for m in RX.finditer(txt):
        trouves.add(m.group(1))
    return trouves


bon = selecteurs(CHEMIN_BON)
neuf = selecteurs(CHEMIN_NEUF)

perdues = sorted(bon - neuf)
gagnees = sorted(neuf - bon)

print(f"classes distinctes dans la BONNE : {len(bon)}")
print(f"classes distinctes dans la NEUVE : {len(neuf)}")
print(f"PERDUES : {len(perdues)}")
print(f"GAGNEES : {len(gagnees)}")
print()
print("--- 60 premieres perdues ---")
for c in perdues[:60]:
    print("  ", c)
print()
print("--- 30 premieres gagnees ---")
for c in gagnees[:30]:
    print("  ", c)
