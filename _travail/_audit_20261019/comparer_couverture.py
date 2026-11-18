"""Compare deux feuilles compilees du point de vue de la COUVERTURE RELLE du site,
et verifie que les variables CSS utilisees existent bien des deux cotes.

Usage :
    python comparer_couverture.py <bonne.css> <neuve.css> <aegis.css>
"""
import re
import sys

bonne, neuve, aegis = sys.argv[1:4]


def lire(c):
    with open(c, "r", encoding="utf-8", errors="replace") as f:
        t = f.read()
    return re.sub(r"/\*.*?\*/", " ", t, flags=re.DOTALL)


def classes(t):
    return {m.group(1) for m in re.finditer(r"\.(-?[_a-zA-Z][_a-zA-Z0-9\-]*)", t)}


def variables_definies(t):
    return {m.group(1) for m in re.finditer(r"(--[_a-zA-Z0-9\-]+)\s*:", t)}


def variables_utilisees(t):
    return {m.group(1) for m in re.finditer(r"var\(\s*(--[_a-zA-Z0-9\-]+)", t)}


tb, tn, ta = lire(bonne), lire(neuve), lire(aegis)
cb, cn, ca = classes(tb), classes(tn), classes(ta)

print("=== 1. TAILLE ET NOMBRE DE CLASSES ===")
print(f"bonne : {len(tb):>7} o   {len(cb):>4} classes")
print(f"neuve : {len(tn):>7} o   {len(cn):>4} classes")
print(f"aegis : {len(ta):>7} o   {len(ca):>4} classes")

print()
print("=== 2. VARIABLES CSS ===")
def_b = variables_definies(tb) | variables_definies(ta)
def_n = variables_definies(tn) | variables_definies(ta)
use_b = variables_utilisees(tb) | variables_utilisees(ta)
use_n = variables_utilisees(tn) | variables_utilisees(ta)
print(f"variables definies  bonne={len(def_b)}  neuve={len(def_n)}")
mb = sorted(use_b - def_b)
mn = sorted(use_n - def_n)
print(f"var() NON definies  bonne={len(mb)}  neuve={len(mn)}")
if mb:
    print("   bonne, manquantes :", ", ".join(mb[:20]))
if mn:
    print("   neuve, manquantes :", ", ".join(mn[:20]))

print()
print("=== 3. CLASSES PRESENTES DANS LA BONNE ET PAS DANS LA NEUVE ===")
perdues = sorted(cb - cn)
print(f"nombre : {len(perdues)}")
# On ne garde que celles qui ressemblent a une classe Tailwind avec un prefixe
# de couleur / espacement utilise par le site ailleurs, pour voir si une
# "famille" entiere a disparu.
familles = {}
for c in perdues:
    familles.setdefault(c.split("-")[0], []).append(c)
for f in sorted(familles, key=lambda k: -len(familles[k]))[:25]:
    ex = ", ".join(familles[f][:6])
    print(f"   {f:12s} {len(familles[f]):>3}   ex. {ex}")
