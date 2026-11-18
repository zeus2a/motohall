"""
Éprouve la Content-Security-Policy AVANT de l'envoyer en production.

POURQUOI CE SCRIPT
------------------
La CSP qu'on veut poser contient « 'unsafe-eval' », parce qu'Alpine.js évalue
ses expressions avec new Function(). Le risque n'est donc PAS de bloquer Alpine
— on le lui autorise — mais de bloquer autre chose par mégarde : une police, une
feuille de style en ligne, une icône.

LE TÉMOIN, ET POURQUOI C'EST CELUI-LÀ
-------------------------------------
La coque contient <span x-text="time">--:--:--</span>. Cette valeur est écrite
par Alpine au démarrage (updateClock). Donc :
    - si le texte affiche une heure  -> Alpine tourne, la CSP est bonne ;
    - s'il reste « --:--:-- »        -> Alpine est mort, la CSP est à revoir.
C'est une mesure binaire, impossible à interpréter de travers.

MÉTHODE
-------
On ne peut pas poser d'en-tête HTTP sur un fichier local. La CSP est donc
injectée en <meta http-equiv>, qui applique exactement les mêmes règles pour
script-src / style-src / img-src / font-src / connect-src. (Seul
frame-ancestors est ignoré en mode meta, et il est déjà couvert par
X-Frame-Options.)

Usage :
    python tester_csp.py <dossier_apercu> <chrome.exe>
"""
from __future__ import annotations

import re
import subprocess
import sys
import tempfile
from pathlib import Path

POLITIQUE = (
    "default-src 'self'; "
    "script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
    "font-src 'self' https://fonts.gstatic.com data:; "
    "img-src 'self' data:; "
    "connect-src 'self'; "
    "frame-src 'self'; "
    "object-src 'none'; "
    "base-uri 'self'; "
    "form-action 'self'"
)

# Le témoin : la valeur écrite par Alpine dans l'horloge.
RX_HORLOGE = re.compile(r'x-text="time"[^>]*>([^<]*)<')
HEURE = re.compile(r"\d{2}:\d{2}:\d{2}")


def rendre_dom(chrome: str, page: Path) -> str:
    """Chrome sans interface : rend la page, exécute le JavaScript, rend le DOM."""
    sortie = subprocess.run(
        [chrome, "--headless=old", "--disable-gpu", "--virtual-time-budget=8000",
         "--dump-dom", page.as_uri()],
        capture_output=True, text=True, encoding="utf-8", errors="replace",
    )
    return sortie.stdout or ""


def mesurer(nom: str, source: Path, avec_csp: bool, chrome: str, tmp: Path) -> tuple[bool, str]:
    html = source.read_text(encoding="utf-8")

    if avec_csp:
        balise = f'<meta http-equiv="Content-Security-Policy" content="{POLITIQUE}">\n'
        html = html.replace("<head>", "<head>\n" + balise, 1)

    page = tmp / f"{nom}.html"
    page.write_text(html, encoding="utf-8")

    dom = rendre_dom(chrome, page)
    m = RX_HORLOGE.search(dom)
    if not m:
        return False, "horloge introuvable dans le DOM (la page n'a pas été rendue ?)"
    texte = m.group(1).strip()
    return bool(HEURE.search(texte)), f'horloge = "{texte}"'


def main() -> int:
    dossier = Path(sys.argv[1])
    chrome = sys.argv[2]

    source = dossier / "portail-admin.html"
    if not source.is_file():
        print(f"!! page introuvable : {source}")
        print("   (fabriquer d'abord : php fabriquer_apercus.php portail-admin)")
        return 2

    tmp = Path(tempfile.mkdtemp(prefix="csp_"))
    # Les feuilles et les scripts sont en chemin relatif : on les rend visibles
    # depuis le dossier temporaire par un lien symbolique vers les assets.
    lien = tmp / "assets"
    if not lien.exists():
        lien.symlink_to(dossier / "assets", target_is_directory=True)

    print("=" * 78)
    print("ÉPREUVE DE LA CSP — le témoin est l'horloge d'Alpine")
    print("=" * 78)

    ok_sans, d_sans = mesurer("sans_csp", source, False, chrome, tmp)
    print(f"  1. SANS CSP (témoin de référence) : {'OK ' if ok_sans else 'KO '} {d_sans}")

    ok_avec, d_avec = mesurer("avec_csp", source, True, chrome, tmp)
    print(f"  2. AVEC CSP                      : {'OK ' if ok_avec else 'KO '} {d_avec}")

    print()
    if ok_sans and ok_avec:
        print("  >>> CSP VALIDE : Alpine tourne avec la politique active.")
        return 0
    if not ok_sans:
        print("  >>> Le témoin lui-même ne marche pas : le test est sans valeur.")
        return 1
    print("  >>> CSP A REVOIR : Alpine ne s'exécute plus sous cette politique.")
    return 1


if __name__ == "__main__":
    raise SystemExit(main())
