# -*- coding: utf-8 -*-
"""Mesures complementaires : racines de fragment, icones Lucide, formulaires."""
import re
import sys

if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8")

ROOT = r"C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel"
F = [
    ("home/capi3439/zeusstack/motohall/templates/layout_secure.php", "layout_secure.php"),
    ("home/capi3439/zeusstack/motohall/templates/header_ui.php", "header_ui.php"),
    ("home/capi3439/zeusstack/motohall/templates/sidebar.php", "sidebar.php"),
    ("home/capi3439/zeusstack/motohall/templates/footer_ui.php", "footer_ui.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/index_view.php", "index_view.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/dashboard_view.php", "dashboard_view.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/documents_view.php", "documents_view.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/admin_users_view.php", "admin_users_view.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/agenda_view.php", "agenda_view.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/comptoir_view.php", "comptoir_view.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/atelier_view.php", "atelier_view.php"),
    ("home/capi3439/zeusstack/motohall/templates/views/profil_view.php", "profil_view.php"),
    ("home/capi3439/public_html/motohall.zeusstack.fr/login.php", "login.php"),
    ("home/capi3439/public_html/motohall.zeusstack.fr/error.php", "error.php"),
]

PHP = re.compile(r"<\?(?!xml)[\s\S]*?(?:\?>|\Z)")
VOID = {"input", "br", "hr", "img", "meta", "link", "path", "circle", "rect",
        "line", "polyline", "polygon", "use", "source", "col", "area", "base",
        "embed", "param", "track", "wbr", "ellipse"}
TAG = re.compile(r'<(\/?)([a-zA-Z][a-zA-Z0-9-]*)((?:"[^"]*"|\'[^\']*\'|[^>"\'])*?)(\/?)>')


def mask(s, p):
    def repl(m):
        t = m.group(0)
        nl = t.count("\n")
        return "\n" * nl + " " * (len(t) - nl)
    return re.sub(p, repl, s, flags=re.I)


def main():
    tot_i = 0
    for rel, lab in F:
        src = open(ROOT + "/" + rel, encoding="utf-8").read()
        h = mask(src, PHP.pattern)
        h = mask(h, r"<script\b[\s\S]*?</script\s*>")
        h = mask(h, r"<style\b[\s\S]*?</style\s*>")
        h = mask(h, r"<!--[\s\S]*?-->")

        depth, top = 0, []
        for m in TAG.finditer(h):
            tag = m.group(2).lower()
            cl = m.group(1) == "/"
            if tag in VOID or m.group(4) == "/":
                continue
            if cl:
                depth -= 1
                if depth == 0:
                    top.append("/" + tag)
            else:
                if depth == 0:
                    top.append("%s@L%d" % (tag, h.count("\n", 0, m.start()) + 1))
                depth += 1

        luc = len(re.findall(r"<i\b[^>]*data-lucide", h))
        i_all = len(re.findall(r"<i\b", h))
        tot_i += luc
        print("%-20s racines=%-28s <i>=%-3d lucide=%d"
              % (lab, " ".join(top), i_all, luc))
        for f in re.findall(r"<form\b[^>]*>", h):
            print("      form:", re.sub(r"\s+", " ", f)[:100])
    print("TOTAL <i data-lucide> = %d" % tot_i)


if __name__ == "__main__":
    main()
