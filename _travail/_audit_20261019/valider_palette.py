"""
MotoHall 2A — validateur de palette (version 2, avec fonds composés).

Calcule le ratio de contraste WCAG 2.2 de chaque couple texte/fond réellement
utilisé par l'interface, pour les deux thèmes.

Ce qui change par rapport à la version 1 :
  - on distingue la BORDURE DÉCORATIVE (aucune exigence : la surface suffit à
    délimiter la carte) de la BORDURE DE CONTRÔLE (3:1 obligatoire : c'est elle,
    seule, qui fait qu'on voit un champ ou un bouton) ;
  - les PASTILLES sont mesurées sur leur fond réellement composé (accent à 10 %
    posé sur une carte), pas sur le blanc ou le noir ;
  - le bouton d'accent est corrigé : en thème clair, l'aplat est plus foncé.

Usage :  python valider_palette.py
"""

from __future__ import annotations


def hex_vers_rgb(h: str) -> tuple[int, int, int]:
    h = h.strip().lstrip("#")
    if len(h) == 3:
        h = "".join(c * 2 for c in h)
    return int(h[0:2], 16), int(h[2:4], 16), int(h[4:6], 16)


def composer(fond: str, dessus: str, alpha: float) -> tuple[int, int, int]:
    f = hex_vers_rgb(fond)
    d = hex_vers_rgb(dessus)
    return tuple(round(d[i] * alpha + f[i] * (1 - alpha)) for i in range(3))  # type: ignore[return-value]


def luminance(rgb: tuple[int, int, int]) -> float:
    c = []
    for v in rgb:
        x = v / 255.0
        c.append(x / 12.92 if x <= 0.03928 else ((x + 0.055) / 1.055) ** 2.4)
    return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]


def _rgb(x: str | tuple[int, int, int]) -> tuple[int, int, int]:
    return hex_vers_rgb(x) if isinstance(x, str) else x


def ratio(a, b) -> float:
    la, lb = luminance(_rgb(a)), luminance(_rgb(b))
    return (max(la, lb) + 0.05) / (min(la, lb) + 0.05)


# ───────────────────────────────────────────────────────────────────────────
# PALETTES DÉFINITIVES
# ───────────────────────────────────────────────────────────────────────────

SOMBRE = {
    "fond":          "#020617",
    "fond-panneau":  "#0b1424",
    "surface":       "#0f1a2b",
    "texte":         "#eaf0f9",
    "texte-doux":    "#b7c4d8",
    "texte-faible":  "#93a3bd",
    "accent":        "#5cc8fb",
    "accent-plein":  "#38bdf8",
    "accent-plein-texte": "#04121c",
    "succes":        "#4ade80",
    "alerte":        "#fcd34d",
    "danger":        "#fca5a5",
    "bordure-controle": "#5b6d8c",
}

CLAIR = {
    "fond":          "#eef2f8",
    "fond-panneau":  "#ffffff",
    "surface":       "#ffffff",
    "texte":         "#0c1526",
    "texte-doux":    "#39485f",
    "texte-faible":  "#4d5d76",
    "accent":        "#0369a1",
    "accent-plein":  "#0369a1",
    "accent-plein-texte": "#ffffff",
    "succes":        "#166534",
    "alerte":        "#92400e",
    "danger":        "#b91c1c",
    "bordure-controle": "#64748b",
}

AAA, AA, GRAND = 7.0, 4.5, 3.0


def lignes(theme: dict[str, str], nom: str) -> list[tuple[str, object, object, float]]:
    f, s, p = theme["fond"], theme["surface"], theme["fond-panneau"]
    out: list[tuple[str, object, object, float]] = [
        (f"{nom} · texte / fond",              theme["texte"],        f, AAA),
        (f"{nom} · texte doux / fond",         theme["texte-doux"],   f, AAA),
        (f"{nom} · texte faible / fond",       theme["texte-faible"], f, AA),
        (f"{nom} · texte / carte",             theme["texte"],        s, AAA),
        (f"{nom} · texte doux / carte",        theme["texte-doux"],   s, AAA),
        (f"{nom} · texte faible / carte",      theme["texte-faible"], s, AA),
        (f"{nom} · texte / panneau",           theme["texte"],        p, AAA),
        (f"{nom} · texte faible / panneau",    theme["texte-faible"], p, AA),
        (f"{nom} · accent / fond",             theme["accent"],       f, AA),
        (f"{nom} · accent / carte",            theme["accent"],       s, AA),
        (f"{nom} · succes / carte",            theme["succes"],       s, AA),
        (f"{nom} · alerte / carte",            theme["alerte"],       s, AA),
        (f"{nom} · danger / carte",            theme["danger"],       s, AA),
        (f"{nom} · bordure de controle / carte", theme["bordure-controle"], s, GRAND),
        (f"{nom} · bordure de controle / fond",  theme["bordure-controle"], f, GRAND),
        (f"{nom} · bouton accent (texte/aplat)",
         theme["accent-plein-texte"], theme["accent-plein"], AA),
    ]
    # Pastilles : le fond est l'aplat à 10 % posé sur la carte.
    for cle, seuil in (("accent", AA), ("succes", AA), ("alerte", AA), ("danger", AA)):
        fond_pastille = composer(s, theme[cle], 0.12)
        out.append((f"{nom} · pastille {cle} (fond 12 %)", theme[cle], fond_pastille, seuil))
    # Pastille neutre : texte doux sur rgba(148,163,184,.08) posé sur la carte.
    fond_neutre = composer(s, "#94a3b8", 0.10)
    out.append((f"{nom} · pastille neutre", theme["texte-doux"], fond_neutre, AA))
    # Champ de saisie : texte sur rgba(2,6,23,.6) posé sur la carte.
    fond_champ = composer(s, "#020617", 0.60) if nom == "sombre" else composer(s, "#f1f5f9", 0.90)
    out.append((f"{nom} · texte dans un champ", theme["texte"], fond_champ, AAA))
    return out


def rapport(titre: str, lst: list[tuple[str, object, object, float]]) -> int:
    print()
    print("=" * 100)
    print(titre)
    print("=" * 100)
    print(f"{'couple':46s} {'ratio':>7s}  {'seuil':>5s}  verdict")
    print("-" * 100)
    echecs = 0
    for label, fg, bg, seuil in lst:
        r = ratio(fg, bg)  # type: ignore[arg-type]
        if r >= seuil:
            verdict = "OK"
        elif r >= AA:
            verdict = "OK (AA)"
        else:
            verdict = f"ECHEC (il manque {seuil - r:.2f})"
            echecs += 1
        print(f"{label:46s} {r:7.2f}  {seuil:5.1f}  {verdict}")
    return echecs


def main() -> int:
    e1 = rapport("THEME SOMBRE (par defaut)", lignes(SOMBRE, "sombre"))
    e2 = rapport("THEME CLAIR", lignes(CLAIR, "clair"))
    print()
    print("=" * 100)
    print(f"BILAN : {e1} echec(s) en sombre · {e2} echec(s) en clair")
    print("=" * 100)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
