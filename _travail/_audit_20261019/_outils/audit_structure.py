# -*- coding: utf-8 -*-
"""Audit STRUCTURE (lecture seule) des gabarits PHP de moto-hall.

Mesure, sans rien modifier :
  1. equilibre + imbrication des balises (pile LIFO) sur le HTML seul
     (PHP, <script>, <style>, commentaires remplaces par des espaces :
     les numeros de ligne restent EXACTS) ;
  2. equilibre PAR BRANCHE de chaque bloc if/elseif/else/foreach/
     for/while/switch en syntaxe alternative (ce que le controle plat
     ne peut pas voir) ;
  3. etiquette de champ (label[for] / aria-label / aria-labelledby) ;
  4. img sans alt, bouton sans type, bouton-icone sans nom accessible,
     lien sans texte, div avec @click ;
  5. hierarchie des titres, id dupliques.

Usage : python audit_structure.py  (sortie texte + JSON a cote)
"""
import hashlib
import json
import os
import re
import sys
from collections import OrderedDict, defaultdict

if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8")

ROOT = r"C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel"

FILES = [
    (r"home\capi3439\zeusstack\motohall\templates\layout_secure.php", "layout_secure.php"),
    (r"home\capi3439\zeusstack\motohall\templates\header_ui.php", "header_ui.php"),
    (r"home\capi3439\zeusstack\motohall\templates\sidebar.php", "sidebar.php"),
    (r"home\capi3439\zeusstack\motohall\templates\footer_ui.php", "footer_ui.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\index_view.php", "index_view.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\dashboard_view.php", "dashboard_view.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\documents_view.php", "documents_view.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\admin_users_view.php", "admin_users_view.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\agenda_view.php", "agenda_view.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\comptoir_view.php", "comptoir_view.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\atelier_view.php", "atelier_view.php"),
    (r"home\capi3439\zeusstack\motohall\templates\views\profil_view.php", "profil_view.php"),
    (r"home\capi3439\public_html\motohall.zeusstack.fr\login.php", "login.php"),
    (r"home\capi3439\public_html\motohall.zeusstack.fr\error.php", "error.php"),
]

COUNTERS = ["div", "span", "section", "aside", "header", "footer", "main", "nav",
            "form", "a", "button", "p", "template", "h1", "h2", "h3", "h4", "h5", "h6",
            "label", "table", "thead", "tbody", "tr", "td", "th", "ul", "ol", "li",
            "dl", "dt", "dd", "iframe", "img", "input", "select", "textarea",
            "option", "style", "script"]

VOID = {"input", "br", "hr", "img", "meta", "link", "path", "circle", "rect", "line",
        "polyline", "polygon", "use", "source", "col", "area", "base", "embed",
        "param", "track", "wbr", "ellipse"}

TAG_RE = re.compile(r'<(\/?)([a-zA-Z][a-zA-Z0-9-]*)((?:"[^"]*"|\'[^\']*\'|[^>"\'])*?)(\/?)>')
ATTR_RE = re.compile(r'([:@a-zA-Z_][-:@a-zA-Z0-9_.]*)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+)))?')
PHP_RE = re.compile(r'<\?(?!xml)[\s\S]*?(?:\?>|\Z)')

# openers en syntaxe alternative
OPEN_RE = re.compile(r'\b(if|elseif|foreach|for|while|switch|try)\s*[\(\w]')
ALT_OPEN_RE = re.compile(r'\b(if|elseif|foreach|for|while|switch)\s*\([^;]*?\)\s*:')
END_RE = re.compile(r'\b(endif|endforeach|endfor|endwhile|endswitch|endtry)\b')
ELSE_RE = re.compile(r'\belse\s*:')


def mask(src, pattern, flags=0):
    """Remplace chaque correspondance par des espaces en CONSERVANT les \\n.

    -> les offsets et donc les numeros de ligne restent ceux du fichier.
    """
    def repl(m):
        txt = m.group(0)
        nl = txt.count("\n")
        return "\n" * nl + " " * (len(txt) - nl)
    return re.sub(pattern, repl, src, flags=flags)


def line_of(text, pos):
    return text.count("\n", 0, pos) + 1


def parse_attrs(chunk):
    """Retourne (liste ordonnee [(nom, valeur)], liste des noms dupliques)."""
    out, seen, dups = [], set(), []
    for m in ATTR_RE.finditer(chunk or ""):
        name = m.group(1)
        val = m.group(2) if m.group(2) is not None else (
            m.group(3) if m.group(3) is not None else (m.group(4) or ""))
        if name in seen:
            dups.append(name)
        seen.add(name)
        out.append((name, val))
    return out, dups


def attr(attrs, name):
    for k, v in attrs:
        if k == name:
            return v
    return None


def analyse(src, label):
    res = {"file": label, "lines": src.count("\n") + 1}
    h = mask(src, PHP_RE.pattern)
    h = mask(h, r'<script\b[\s\S]*?</script\s*>', re.I)
    h = mask(h, r'<style\b[\s\S]*?</style\s*>', re.I)
    h = mask(h, r'<!--[\s\S]*?-->')

    # ---------- 1. comptage + pile LIFO ----------
    counts = OrderedDict((t, [0, 0]) for t in COUNTERS)
    stack, errors, events = [], [], []
    for m in TAG_RE.finditer(h):
        closing = m.group(1) == "/"
        tag = m.group(2).lower()
        selfclose = m.group(4) == "/"
        line = line_of(h, m.start())
        attrs, dups = parse_attrs(m.group(3))
        ev = {"line": line, "tag": tag, "closing": closing, "attrs": attrs,
              "dups": dups, "raw": m.group(0), "start": m.start()}
        events.append(ev)
        if tag in counts:
            counts[tag][1 if closing else 0] += 1
        if tag in VOID or selfclose:
            continue
        if closing:
            idx = None
            for k in range(len(stack) - 1, -1, -1):
                if stack[k]["tag"] == tag:
                    idx = k
                    break
            if idx is None:
                errors.append({"line": line, "kind": "orpheline",
                               "msg": "</%s> sans ouverture" % tag})
                continue
            if idx != len(stack) - 1:
                top = stack[-1]
                errors.append({"line": line, "kind": "croisee",
                               "msg": "</%s> alors qu'on attendait </%s> (ouvert L%d)"
                                      % (tag, top["tag"], top["line"])})
            del stack[idx:]
        else:
            stack.append({"tag": tag, "line": line})
    unclosed = [{"line": s["line"], "tag": s["tag"]} for s in stack]

    res["counts"] = {t: c for t, c in counts.items() if c != [0, 0]}
    # un element vide (input, img...) n'a pas de fermeture : jamais "desequilibre"
    res["unbalanced"] = {t: c for t, c in counts.items()
                         if c[0] != c[1] and t not in VOID}
    res["crossing"] = [e for e in errors if e["kind"] == "croisee"]
    res["orphan"] = [e for e in errors if e["kind"] == "orpheline"]
    res["unclosed"] = unclosed

    # ---------- 2. blocs de controle PHP : equilibre par branche ----------
    php_events = []
    for m in PHP_RE.finditer(src):
        block = m.group(0)
        start_line = line_of(src, m.start())
        code = block[2:]
        if code.endswith("?>"):
            code = code[:-2]
        # ouvreurs non fermes dans CE bloc
        opened = []
        for om in ALT_OPEN_RE.finditer(code):
            opened.append(om.group(1))
        closes = [c for c in END_RE.findall(code)]
        else_hits = len(ELSE_RE.findall(code))
        # decisions : chaque endif ferme un ouvreur, dans l'ordre
        seq = []
        for m2 in re.finditer(
                r'\b(elseif\s*\([^;]*?\)\s*:)|(\belse\s*:)|'
                r'\b(if|foreach|for|while|switch)\s*\([^;]*?\)\s*:|'
                r'\b(endif|endforeach|endfor|endwhile|endswitch)\b',
                code):
            if m2.group(1):
                seq.append(("elif", None))
            elif m2.group(2):
                seq.append(("else", None))
            elif m2.group(4):
                seq.append(("end", m2.group(4)))
            elif m2.group(3):
                seq.append(("open", m2.group(3)))
        for kind, name in seq:
            php_events.append({"line": start_line, "start": m.start() + 2,
                               "kind": kind, "name": name})

    merged = [{"line": e["line"], "start": e["start"], "kind": "tag", "tag": e["tag"],
               "closing": e["closing"]} for e in events]
    merged += [{"line": e["line"], "start": e["start"], "kind": e["kind"],
                "name": e["name"], "tag": None} for e in php_events]
    merged.sort(key=lambda e: (e["start"], 0 if e["kind"] != "tag" else 1))

    frames, blocks = [], []
    for e in merged:
        if e["kind"] == "open":
            frames.append({"name": e["name"], "line": e["line"],
                           "branches": [defaultdict(int)]})
        elif e["kind"] in ("else", "elif"):
            if frames:
                frames[-1]["branches"].append(defaultdict(int))
        elif e["kind"] == "end":
            if frames:
                f = frames.pop()
                blocks.append(f)
        else:  # tag
            if e["tag"] in VOID:
                continue
            delta = -1 if e["closing"] else 1
            for f in frames:
                f["branches"][-1][e["tag"]] += delta
    # blocs ouverts non refermes (PHP mal ferme) : on les garde pour le rapport
    blocks.extend(frames)

    bad = []
    for f in blocks:
        for i, br in enumerate(f["branches"]):
            nz = {t: d for t, d in br.items() if d}
            if nz:
                bad.append({"line": f["line"], "name": f["name"], "branche": i + 1,
                            "delta": nz})
    res["ctrl_blocks"] = len(blocks)
    res["ctrl_branches"] = sum(len(f["branches"]) for f in blocks)
    res["ctrl_unbalanced"] = bad
    res["ctrl_detail"] = [{"line": f["line"], "name": f["name"],
                           "branches": len(f["branches"]),
                           "sizes": [sum(1 for _ in b) for b in f["branches"]]}
                          for f in sorted(blocks, key=lambda x: x["line"])]

    # ---------- 3. champs et etiquettes ----------
    labels_for = set()
    for e in events:
        if e["tag"] == "label" and not e["closing"]:
            v = attr(e["attrs"], "for")
            if v:
                labels_for.add(v)
    fields = []
    for e in events:
        if e["closing"] or e["tag"] not in ("input", "select", "textarea"):
            continue
        a = e["attrs"]
        typ = (attr(a, "type") or "text").lower()
        if typ in ("hidden",):
            continue
        eid = attr(a, "id")
        has = bool(eid and eid in labels_for) or bool(
            attr(a, "aria-label") or attr(a, "aria-labelledby") or attr(a, "title"))
        fields.append({"line": e["line"], "tag": e["tag"], "type": typ,
                       "name": attr(a, "name") or "", "id": eid,
                       "labelled": has})
    res["fields"] = fields
    res["unlabelled"] = [f for f in fields if not f["labelled"]]

    # ---------- 4. img / boutons / liens / cliquables ----------
    res["img"] = [{"line": e["line"], "alt": attr(e["attrs"], "alt")}
                  for e in events if e["tag"] == "img" and not e["closing"]]
    res["img_noalt"] = [i for i in res["img"] if i["alt"] is None]

    plain = re.sub(r"<[^>]*>", " ", h)

    def text_between(a, b):
        """(texte visible, nom fourni a l'execution ?).

        Le texte est lu sur la version MASQUEE (les balises se retirent
        proprement). Si ce texte est vide alors que la source brute contient
        un echo PHP ou un x-text, le nom est fourni a l'execution : on le
        signale par dyn=True plutot que de crier au lien vide a tort.
        """
        txt = re.sub(r"\s+", " ", re.sub(r"<[^>]*>", " ", h[a:b])).strip()
        raw = src[a:b]
        dyn = ("<?=" in raw) or ("x-text" in raw)
        return txt, dyn

    buttons = []
    open_btn = []
    for e in events:
        if e["tag"] != "button":
            continue
        if e["closing"]:
            if open_btn:
                st = open_btn.pop()
                st["text"], st["dyn"] = text_between(st["end"], e["start"])
                st["line_end"] = e["line"]
                buttons.append(st)
            continue
        open_btn.append({"line": e["line"], "attrs": e["attrs"],
                         "end": e["raw"] and e["start"] + len(e["raw"]),
                         "start": e["start"]})
    for b in buttons:
        a = b["attrs"]
        b["type"] = attr(a, "type")
        b["name"] = attr(a, "aria-label") or attr(a, "title") or attr(a, "aria-labelledby")
        b["xtext"] = attr(a, "x-text")
    res["buttons"] = [{"line": b["line"], "type": b["type"], "text": b["text"],
                       "name": b["name"], "xtext": b["xtext"], "dyn": b["dyn"]}
                      for b in buttons]
    res["buttons_no_type"] = [b["line"] for b in buttons if b["type"] is None]
    res["buttons_icon_only_no_name"] = [
        {"line": b["line"], "text": b["text"], "xtext": b["xtext"], "dyn": b["dyn"]}
        for b in buttons if b["text"] == "" and not b["name"] and not b["xtext"]]
    res["buttons_icon_only_dynamic"] = [
        {"line": b["line"], "xtext": b["xtext"]}
        for b in buttons if b["text"] == "" and not b["name"] and b["xtext"]]

    # boutons dans un form ?
    forms, fstack = [], []
    for e in events:
        if e["tag"] == "form":
            if e["closing"]:
                if fstack:
                    fstack.pop()
            else:
                fstack.append(e["line"])
        if e["tag"] == "button" and not e["closing"]:
            forms.append({"line": e["line"], "in_form": bool(fstack),
                          "type": attr(e["attrs"], "type")})
    res["buttons_in_form"] = forms
    res["buttons_no_type_in_form"] = [b["line"] for b in forms
                                      if b["in_form"] and b["type"] is None]
    res["buttons_no_type_outside_form"] = [b["line"] for b in forms
                                           if not b["in_form"] and b["type"] is None]

    links, open_a = [], []
    for e in events:
        if e["tag"] != "a":
            continue
        if e["closing"]:
            if open_a:
                st = open_a.pop()
                st["text"], st["dyn"] = text_between(st["end"], e["start"])
                links.append(st)
            continue
        open_a.append({"line": e["line"], "attrs": e["attrs"],
                       "start": e["start"], "end": e["start"] + len(e["raw"])})
    for l in links:
        l["name"] = attr(l["attrs"], "aria-label") or attr(l["attrs"], "title")
        l["href"] = attr(l["attrs"], "href") or (":href" if attr(l["attrs"], ":href") else None)
    res["links"] = [{"line": l["line"], "text": l["text"][:80], "href": l["href"],
                     "name": l["name"], "dyn": l["dyn"]} for l in links]
    res["links_no_text_no_name"] = [{"line": l["line"], "href": l["href"]}
                                    for l in links
                                    if l["text"] == "" and not l["name"] and not l["dyn"]]
    res["links_javascript"] = [{"line": l["line"], "href": l["href"]}
                               for l in links
                               if str(l["href"] or "").lower().startswith("javascript:")]

    # elements non natifs portant un @click (les .away/.outside/.window sont
    # des ecouteurs de fermeture, PAS des zones cliquables : exclus)
    def click_mods(attrs):
        return [k for k, v in attrs
                if (k == "@click" or k == "x-on:click"
                    or k in ("@click.prevent", "@click.stop", "@click.prevent.stop"))
                and not any(x in k for x in (".away", ".outside", ".window", ".document"))]
    res["div_click"] = [{"line": e["line"], "tag": e["tag"], "attrs": click_mods(e["attrs"])}
                        for e in events
                        if not e["closing"]
                        and e["tag"] not in ("button", "a", "input", "select", "textarea")
                        and click_mods(e["attrs"])]

    # ---------- 5. titres, ids, attributs dupliques ----------
    heads, open_h = [], []
    for e in events:
        if e["tag"] not in ("h1", "h2", "h3", "h4", "h5", "h6"):
            continue
        if e["closing"]:
            if open_h:
                st = open_h.pop()
                st["text"], st["dyn"] = text_between(st["end"], e["start"])
                heads.append(st)
            continue
        open_h.append({"line": e["line"], "level": int(e["tag"][1]),
                       "start": e["start"], "end": e["start"] + len(e["raw"])})
    res["headings"] = [{"line": h["line"], "level": h["level"],
                        "text": h["text"][:60], "dyn": h["dyn"]} for h in heads]

    ids = defaultdict(list)
    for e in events:
        if e["closing"]:
            continue
        v = attr(e["attrs"], "id")
        if v:
            ids[v].append(e["line"])
    res["ids"] = {k: v for k, v in ids.items()}
    res["dup_ids"] = {k: v for k, v in ids.items() if len(v) > 1}
    res["dup_attrs"] = [{"line": e["line"], "tag": e["tag"], "dups": e["dups"]}
                        for e in events if e["dups"]]

    # ---------- 6. etiquettes orphelines, accessibilite diverse ----------
    all_ids = set(ids.keys())
    res["labels_no_for"] = [{"line": e["line"]} for e in events
                            if e["tag"] == "label" and not e["closing"]
                            and not attr(e["attrs"], "for")]
    res["labels_broken_for"] = [{"line": e["line"], "for": attr(e["attrs"], "for")}
                                for e in events
                                if e["tag"] == "label" and not e["closing"]
                                and attr(e["attrs"], "for")
                                and attr(e["attrs"], "for") not in all_ids]
    res["labels_total"] = len([e for e in events
                               if e["tag"] == "label" and not e["closing"]])
    res["ids_total"] = sum(len(v) for v in ids.values())

    inv = defaultdict(list)
    for e in events:
        if e["closing"]:
            continue
        for k, v in e["attrs"]:
            if k.startswith("aria-") or k in ("role", "tabindex", "autofocus",
                                              "contenteditable", "accesskey"):
                inv[k].append(e["line"])
    res["aria"] = {k: v for k, v in inv.items()}

    # style/script en plein corps (fragment) : invalide hors <head>
    res["style_in_body"] = [line_of(src, m.start())
                            for m in re.finditer(r"<style\b", src)]
    res["script_in_body"] = [line_of(src, m.start())
                             for m in re.finditer(r"<script\b", src)]

    # imbrications invalides (le navigateur ferme tout seul : degats silencieux)
    res["bad_nesting"] = []
    st2 = []
    BLOCK_IN_P = {"div", "p", "section", "table", "ul", "ol", "dl", "form",
                  "h1", "h2", "h3", "h4", "h5", "h6", "header", "footer",
                  "aside", "nav", "main", "button"}
    for m in TAG_RE.finditer(h):
        tag = m.group(2).lower()
        closing = m.group(1) == "/"
        if tag in VOID or m.group(4) == "/":
            continue
        if closing:
            for k in range(len(st2) - 1, -1, -1):
                if st2[k]["tag"] == tag:
                    del st2[k:]
                    break
            continue
        parents = [s["tag"] for s in st2]
        if "p" in parents and tag in BLOCK_IN_P:
            pri = len(parents) - 1 - parents[::-1].index("p")
            res["bad_nesting"].append(
                {"line": line_of(h, m.start()), "tag": tag,
                 "in": "p", "p_line": st2[pri]["line"]})
        if tag == "a" and "a" in parents:
            res["bad_nesting"].append(
                {"line": line_of(h, m.start()), "tag": "a", "in": "a", "p_line": 0})
        if tag == "button" and "a" in parents:
            res["bad_nesting"].append(
                {"line": line_of(h, m.start()), "tag": "button", "in": "a", "p_line": 0})
        st2.append({"tag": tag, "line": line_of(h, m.start())})

    # & non echappe : on separe le "&&" des liaisons Alpine (sans effet) des
    # vraies valeurs d'URL / d'attribut (HTML non conforme)
    res["amp_unencoded"] = []
    for e in events:
        for k, v in e["attrs"]:
            hits = [m2.group(0) for m2 in
                    re.finditer(r"&(?!#[0-9]+;|#x[0-9a-fA-F]+;|[a-zA-Z][a-zA-Z0-9]*;)", v)]
            if not hits:
                continue
            doubles = v.count("&&")
            res["amp_unencoded"].append({"line": e["line"], "tag": e["tag"], "attr": k,
                                         "n": len(hits), "andand": doubles,
                                         "val": v[:60]})

    res["php_blocks"] = len(PHP_RE.findall(src))
    return res


def main():
    report = []
    for rel, label in FILES:
        path = os.path.join(ROOT, rel)
        with open(path, "rb") as f:
            raw = f.read()
        # l'empreinte est celle des OCTETS exactement analyses
        sha = hashlib.sha256(raw).hexdigest()[:12]
        src = raw.decode("utf-8", errors="replace")
        r = analyse(src, label)
        r["sha"] = sha
        r["bytes"] = len(raw)
        report.append(r)

    out = os.path.join(os.path.dirname(os.path.abspath(__file__)),
                       "audit_structure.json")
    with open(out, "w", encoding="utf-8") as f:
        json.dump(report, f, ensure_ascii=False, indent=1)

    tot = defaultdict(int)
    print("=" * 78)
    print("0. EMPREINTE DES OCTETS ANALYSES (sha256 tronque, ligne + octets)")
    print("=" * 78)
    for r in report:
        print("    %s  %4dL  %6do  %s" % (r["sha"], r["lines"], r["bytes"], r["file"]))

    print()
    print("=" * 78)
    print("1. EQUILIBRE / IMBRICATION (HTML seul, PHP+script+style+commentaires masques)")
    print("=" * 78)
    for r in report:
        unbal = r["unbalanced"]
        verdict = "OK" if not unbal and not r["crossing"] and not r["orphan"] else "DESEQUILIBRE"
        detail = ", ".join("%s %d/%d" % (t, c[0], c[1]) for t, c in unbal.items()) or "-"
        if r["crossing"]:
            detail += " | CROISEE: " + "; ".join(
                "L%d %s" % (e["line"], e["msg"]) for e in r["crossing"])
        print("%-22s %-12s %s" % (r["file"], verdict, detail))
        if r["unclosed"]:
            print("    non ferme : " + ", ".join("<%s> L%d" % (u["tag"], u["line"])
                                                 for u in r["unclosed"]))
        if unbal:
            tot["fichiers_desequilibres"] += 1
        print("    " + " ".join("%s=%d/%d" % (t, c[0], c[1])
                                for t, c in r["counts"].items() if t in COUNTERS[:14]))

    print()
    print("=" * 78)
    print("2. EQUILIBRE PAR BRANCHE (blocs if/else/foreach en syntaxe alternative)")
    print("=" * 78)
    nb = 0
    for r in report:
        nb += r["ctrl_blocks"]
        flag = "OK" if not r["ctrl_unbalanced"] else "!! DESEQUILIBRE"
        print("%-22s blocs=%-3d branches=%-3d %s" % (r["file"], r["ctrl_blocks"],
                                                     r["ctrl_branches"], flag))
        for b in r["ctrl_unbalanced"]:
            print("    L%d (%s) branche %d : %s" % (b["line"], b["name"], b["branche"],
                                                   b["delta"]))
            tot["branches_desequilibrees"] += 1
        if r["ctrl_detail"]:
            print("      detail : " + " | ".join(
                "L%d %s x%d" % (d["line"], d["name"], d["branches"])
                for d in r["ctrl_detail"]))
    print("TOTAL blocs de controle analyses : %d" % nb)

    print()
    print("=" * 78)
    print("3. CHAMPS SANS ETIQUETTE")
    print("=" * 78)
    for r in report:
        if r["fields"]:
            print("%-22s champs visibles=%d  sans etiquette=%d"
                  % (r["file"], len(r["fields"]), len(r["unlabelled"])))
        for f in r["unlabelled"]:
            print("    L%-4d <%s type=%s name=%s id=%s>"
                  % (f["line"], f["tag"], f["type"], f["name"], f["id"]))
            tot["champs_sans_etiquette"] += 1

    print()
    print("=" * 78)
    print("4. IMG / BOUTONS / LIENS / CLIQUABLES NON NATIFS")
    print("=" * 78)
    for r in report:
        if (r["img"] or r["buttons"] or r["links_no_text_no_name"] or r["div_click"]
                or r["links_javascript"] or r["amp_unencoded"] or r["style_in_body"]
                or r["bad_nesting"] or r["labels_no_for"]):
            print("--- %s" % r["file"])
        for i in r["img_noalt"]:
            print("    img SANS ALT L%d" % i["line"])
            tot["img_sans_alt"] += 1
        if r["img"]:
            print("    img: %d (alt present: %d)" % (len(r["img"]),
                                                     len(r["img"]) - len(r["img_noalt"])))
        if r["buttons_no_type_in_form"]:
            print("    BOUTON SANS TYPE DANS UN FORM : %s" % r["buttons_no_type_in_form"])
            tot["boutons_sans_type_dans_form"] += len(r["buttons_no_type_in_form"])
        if r["buttons_no_type_outside_form"]:
            print("    bouton sans type (hors form) : %s"
                  % r["buttons_no_type_outside_form"])
            tot["boutons_sans_type_hors_form"] += len(r["buttons_no_type_outside_form"])
        for b in r["buttons_icon_only_no_name"]:
            print("    BOUTON-ICONE SANS NOM L%d (texte='%s' x-text=%s)"
                  % (b["line"], b["text"], b["xtext"]))
            tot["boutons_icone_sans_nom"] += 1
        for l in r["links_no_text_no_name"]:
            print("    LIEN SANS TEXTE NI NOM L%d href=%s" % (l["line"], l["href"]))
            tot["liens_sans_texte"] += 1
        for l in r["links_javascript"]:
            print("    LIEN javascript: L%d href=%s" % (l["line"], l["href"]))
            tot["liens_javascript"] += 1
        for d in r["div_click"]:
            print("    <%s> @click non focalisable L%d %s" % (d["tag"], d["line"], d["attrs"]))
            tot["click_non_natif"] += 1
        for b in r["buttons_icon_only_dynamic"]:
            print("    (bouton-icone a nom dynamique L%d x-text=%s : conforme)"
                  % (b["line"], b["xtext"]))
        if r["style_in_body"]:
            print("    <style> dans le corps : L%s" % r["style_in_body"])
            tot["style_dans_le_corps"] += len(r["style_in_body"])
        if r["bad_nesting"]:
            for b in r["bad_nesting"]:
                print("    IMBRICATION INVALIDE L%d <%s> dans <%s> (ouvert L%d)"
                      % (b["line"], b["tag"], b["in"], b["p_line"]))
                tot["imbrications_invalides"] += 1
        if r["labels_no_for"]:
            print("    <label> SANS for : L%s" % [l["line"] for l in r["labels_no_for"]])
            tot["labels_sans_for"] += len(r["labels_no_for"])
        if r["labels_broken_for"]:
            print("    <label for> CASSE : %s" % r["labels_broken_for"])
            tot["labels_cassees"] += len(r["labels_broken_for"])
        for a in r["amp_unencoded"]:
            kind = "&& (binding Alpine)" if a["andand"] else "vraie URL/attribut"
            print("    & NON ECHAPPE L%d %s=%s (%s)"
                  % (a["line"], a["attr"], a["val"], kind))
            if a["andand"]:
                tot["amp_andand_alpine"] += 1
            else:
                tot["amp_vraie_url"] += 1

    print()
    print("=" * 78)
    print("5. TITRES / IDS / ATTRIBUTS DUPLIQUES")
    print("=" * 78)
    for r in report:
        hs = r["headings"]
        seq = " > ".join("h%d(L%d)" % (h["level"], h["line"]) for h in hs) or "(aucun)"
        print("%-22s %s" % (r["file"], seq))
        if not hs:
            tot["fichiers_sans_titre"] += 1
        if r["dup_ids"]:
            print("    ID DUPLIQUES : %s" % r["dup_ids"])
            tot["id_dupliques"] += sum(len(v) - 1 for v in r["dup_ids"].values())
        else:
            print("    ids=%-2d dupliques=0" % r["ids_total"])
        if r["dup_attrs"]:
            print("    ATTRIBUTS DUPLIQUES : %s" % r["dup_attrs"])
            tot["attributs_dupliques"] += len(r["dup_attrs"])

    # ---------- pages composees (layout + partials + vue) ----------
    by = {r["file"]: r for r in report}
    pages = [
        ("index.php", "index_view.php", True),
        ("dashboard.php", "dashboard_view.php", True),
        ("documents.php", "documents_view.php", True),
        ("admin-users.php", "admin_users_view.php", True),
        ("agenda.php", "agenda_view.php", True),
        ("comptoir.php", "comptoir_view.php", True),
        ("atelier.php", "atelier_view.php", True),
        ("profil.php", "profil_view.php", True),
    ]
    print()
    print("=" * 78)
    print("6. PAGES COMPOSEES : titre + ids (layout_secure + header + sidebar + footer + vue)")
    print("=" * 78)
    for page, view, _ in pages:
        seq = []
        for part in ("header_ui.php", "sidebar.php", view, "footer_ui.php",
                     "layout_secure.php"):
            for h in by[part]["headings"]:
                seq.append((part, h))
        levels = [h["level"] for _, h in seq]
        h1 = levels.count(1)
        jumps = ["h%d->h%d L%d/%s" % (levels[i], levels[i + 1], seq[i + 1][1]["line"],
                                      seq[i + 1][0])
                 for i in range(len(levels) - 1) if levels[i + 1] - levels[i] > 1]
        first = levels[0] if levels else None
        print("%-16s h1=%d  suite=%s"
              % (page, h1, " > ".join("h%d" % l for l in levels) or "(aucun)"))
        if h1 == 0:
            tot["pages_sans_h1"] += 1
        if first and first > 1:
            print("        !! premier titre = h%d (aucun h1, saut d'entree)" % first)
            tot["pages_saut_entree"] += 1
        if jumps:
            print("        !! sauts de niveau : %s" % ", ".join(jumps))
            tot["sauts_de_niveau"] += len(jumps)
        # ids de la page composee
        ids = defaultdict(list)
        for part in ("header_ui.php", "sidebar.php", view, "footer_ui.php",
                     "layout_secure.php"):
            for k, v in by[part]["ids"].items():
                for line in v:
                    ids[k].append("%s:L%d" % (part, line))
        dup = {k: v for k, v in ids.items() if len(v) > 1}
        print("        ids=%d  dupliques=%s" % (sum(len(v) for v in ids.values()),
                                                dup or "aucun"))
        if dup:
            tot["id_dupliques_page"] += sum(len(v) - 1 for v in dup.values())

    for page, f in (("login.php", "login.php"), ("error.php", "error.php")):
        hs = by[f]["headings"]
        levels = [h["level"] for h in hs]
        print("%-16s h1=%d  suite=%s" % (page, levels.count(1),
                                         " > ".join("h%d" % l for l in levels) or "(aucun)"))

    print()
    print("=" * 78)
    print("7. INVENTAIRE aria-* / role / tabindex / autofocus")
    print("=" * 78)
    anyis = False
    for r in report:
        if r["aria"]:
            anyis = True
            print("%-22s %s" % (r["file"], r["aria"]))
            tot["attributs_aria"] += sum(len(v) for v in r["aria"].values())
    if not anyis:
        print("AUCUN attribut aria-*, role=, tabindex, autofocus dans les 14 fichiers")

    print()
    print("=" * 78)
    print("COMPTAGE FINAL")
    print("=" * 78)
    for k, v in tot.items():
        print("%-34s %d" % (k, v))
    print("%-34s %d" % ("fichiers analyses", len(report)))
    print("%-34s %d" % ("champs visibles analyses", sum(len(r["fields"]) for r in report)))
    print("%-34s %d" % ("boutons analyses", sum(len(r["buttons"]) for r in report)))
    print("%-34s %d" % ("liens analyses", sum(len(r["links"]) for r in report)))
    print("%-34s %d" % ("<label> analyses", sum(r["labels_total"] for r in report)))
    print("JSON : %s" % out)


if __name__ == "__main__":
    main()
