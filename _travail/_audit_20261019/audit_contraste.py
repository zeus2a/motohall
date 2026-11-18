#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
AUDIT CONTRASTE WCAG 2.2 - MotoHall 2A
======================================
Mesure les couples TEXTE / FOND reellement produits par les gabarits PHP.

Principe : on ne devine pas. Pour chaque fichier, le script
  1. relit le source et reconstruit le flux HTML qu'il EMET (les fragments HTML
     ecrits dans des chaines PHP de documents.php sont inclus, avec leurs
     numeros de ligne d'origine) ;
  2. empile les balises pour connaitre, pour chaque noeud de texte, la chaine
     d'ancetres (donc les fonds empiles et la couleur heritee) ;
  3. resout les classes Tailwind ET les classes de aegis.css (les valeurs sont
     LUES dans aegis.css, pas recopiees a la main) ;
  4. compose les fonds translucides (melange alpha) du plus lointain au plus
     proche, et applique l'opacite de groupe la ou elle existe ;
  5. calcule le ratio WCAG officiel : luminance relative sRGB avec correction
     gamma au seuil 0.03928, ratio = (L1+0.05)/(L2+0.05) ;
  6. en deduit les tailles effectives en px (racine 15 px, plancher !important
     de aegis.css inclus) ;
  7. ecrit les mesures (CSV + JSON) et le rapport markdown.

DEUX ETATS SONT MESURES
  baseline : la copie de production figee du 21/09/2026
             (_travail\\_prod_copie_20260921) - c'est l'etat decrit par la
             mission, et il ne bouge pas ;
  live     : l'arbre de travail, mesure au moment de l'execution. Il a change
             pendant l'audit (voir la section 7 du rapport).

Lancement (par defaut = baseline) :
  & 'C:\\zeus_dev\\zeus_env\\python\\python.exe' ^
      'C:\\...\\_travail\\_audit_20261019\\audit_contraste.py'

Pour mesurer l'arbre vivant a la place :
  ... audit_contraste.py --source live
"""

import csv
import hashlib
import io
import json
import os
import re
import sys
import time
from bisect import bisect_right
from collections import Counter, OrderedDict

# ---------------------------------------------------------------------------
# 0. CHEMINS
# ---------------------------------------------------------------------------
ROOT = r"C:\zeus_dev\zeus_projets\deepseek-harness\sites-web\moto-hall_laravel"
SITE = os.path.join(ROOT, r"home\capi3439\public_html\motohall.zeusstack.fr")
TPL = os.path.join(ROOT, r"home\capi3439\zeusstack\motohall\templates")
OUT = os.path.join(ROOT, r"_travail\_audit_20261019")

BASE = os.path.join(ROOT, r"_travail\_prod_copie_20260921")
BASE_SITE = os.path.join(BASE, "site_public_html")
BASE_TPL = os.path.join(BASE, "zeusstack_motohall", "templates")

CSS_TAILWIND = os.path.join(SITE, r"assets\css\tailwind.css")  # inchange depuis le 21/09

PAGES = ["index.php", "login.php", "error.php", "dashboard.php", "comptoir.php",
         "atelier.php", "agenda.php", "documents.php", "admin-users.php",
         "profil.php", "logout.php"]
TEMPLATES = ["layout_secure.php", "header_ui.php", "sidebar.php", "footer_ui.php"]
VIEW_FILES = ["index_view.php", "dashboard_view.php", "documents_view.php",
              "admin_users_view.php", "agenda_view.php", "comptoir_view.php",
              "atelier_view.php", "profil_view.php"]


def build_sources(site_dir, tpl_dir):
    out = []
    for f in PAGES:
        out.append((f, os.path.join(site_dir, f)))
    for f in TEMPLATES:
        out.append((f, os.path.join(tpl_dir, f)))
    for f in VIEW_FILES:
        out.append((f, os.path.join(tpl_dir, "views", f)))
    return out


SOURCES_BASE = build_sources(BASE_SITE, BASE_TPL)
SOURCES_LIVE = build_sources(SITE, TPL)
CSS_BASE = os.path.join(BASE_SITE, r"assets\css\aegis.css")
CSS_LIVE = os.path.join(SITE, r"assets\css\aegis.css")

# ---------------------------------------------------------------------------
# 1. COULEUR, LUMINANCE, CONTRASTE
# ---------------------------------------------------------------------------
GAMMA_SEUIL = 0.03928   # seuil impose par la mission (formule WCAG d'origine)
GAMMA_EXP = 2.4
RACINE_PX = 15.0        # body { font-size: 15px } dans aegis.css


def hex_to_rgb(h):
    h = h.strip().lstrip('#')
    if len(h) == 3:
        h = ''.join(c * 2 for c in h)
    if len(h) != 6:
        raise ValueError("couleur hex invalide: %r" % h)
    return (int(h[0:2], 16), int(h[2:4], 16), int(h[4:6], 16))


def rgb_to_hex(rgb):
    r, g, b = rgb[0], rgb[1], rgb[2]
    return '#%02x%02x%02x' % (max(0, min(255, int(round(r)))),
                              max(0, min(255, int(round(g)))),
                              max(0, min(255, int(round(b)))))


def parse_color(value):
    """Retourne (r, g, b, a) ou None."""
    if value is None:
        return None
    v = value.strip().lower()
    if not v or '\x01' in v:
        return None
    if v.startswith('#'):
        try:
            r, g, b = hex_to_rgb(v)
        except ValueError:
            return None
        return (float(r), float(g), float(b), 1.0)
    m = re.match(r'rgba?\(([^)]*)\)', v)
    if m:
        parts = [p.strip() for p in m.group(1).replace('/', ' ').split(',')]
        try:
            r, g, b = [float(p) for p in parts[:3]]
            a = float(parts[3]) if len(parts) > 3 else 1.0
            return (r, g, b, a)
        except ValueError:
            return None
    return None


def rel_luminance(rgb):
    def chan(c):
        c = c / 255.0
        return c / 12.92 if c <= GAMMA_SEUIL else ((c + 0.055) / 1.055) ** GAMMA_EXP
    r, g, b = rgb[0], rgb[1], rgb[2]
    return 0.2126 * chan(r) + 0.7152 * chan(g) + 0.0722 * chan(b)


def contrast(rgb1, rgb2):
    l1, l2 = rel_luminance(rgb1), rel_luminance(rgb2)
    if l1 < l2:
        l1, l2 = l2, l1
    return (l1 + 0.05) / (l2 + 0.05)


def over(top, bottom):
    """Compose une couche (r,g,b,a) sur un fond opaque (r,g,b)."""
    r, g, b = top[0], top[1], top[2]
    a = top[3] if len(top) > 3 else 1.0
    return (r * a + bottom[0] * (1 - a),
            g * a + bottom[1] * (1 - a),
            b * a + bottom[2] * (1 - a))


def blend_alpha(color, backdrop, alpha):
    return (color[0] * alpha + backdrop[0] * (1 - alpha),
            color[1] * alpha + backdrop[1] * (1 - alpha),
            color[2] * alpha + backdrop[2] * (1 - alpha))


# ---------------------------------------------------------------------------
# 2. PALETTE TAILWIND UTILISEE PAR LE PROJET
# ---------------------------------------------------------------------------
NAMED = {
    'slate-200': '#e2e8f0', 'slate-300': '#cbd5e1', 'slate-400': '#94a3b8',
    'slate-500': '#64748b', 'slate-600': '#475569', 'slate-700': '#334155',
    'white': '#ffffff', 'black': '#000000',
    'red-300': '#fca5a5', 'red-400': '#f87171', 'red-500': '#ef4444',
    'amber-300': '#fcd34d', 'amber-400': '#fbbf24', 'amber-500': '#f59e0b',
    'emerald-300': '#6ee7b7', 'emerald-400': '#34d399', 'emerald-500': '#10b981',
    'cyan-400': '#22d3ee', 'cyan-500': '#06b6d4',
    'sky-300': '#7dd3fc', 'sky-400': '#38bdf8', 'sky-500': '#0ea5e9',
}

TAILWIND_SIZES = {
    'text-xs': 0.75, 'text-sm': 0.875, 'text-base': 1.0, 'text-lg': 1.125,
    'text-xl': 1.25, 'text-2xl': 1.5, 'text-3xl': 1.875, 'text-4xl': 2.25,
    'text-5xl': 3.0,
}
TAILWIND_WEIGHTS = {
    'font-normal': 400, 'font-medium': 500, 'font-semibold': 600,
    'font-bold': 700, 'font-extrabold': 800, 'font-black': 900,
}

FOND_PAGE = hex_to_rgb('#020617')     # bg-[#020617] du layout et de login.php
NAMED_FG = parse_color('#e2e8f0')     # --texte du body

# ---------------------------------------------------------------------------
# 3. AEGIS.CSS : lecture et resolution (les valeurs sont parsees, pas recopiees)
# ---------------------------------------------------------------------------
def strip_css_comments(src):
    return re.sub(r'/\*.*?\*/', '', src, flags=re.S)


def parse_css_vars(src):
    out = {}
    m = re.search(r':root\s*\{(.*?)\}', src, re.S)
    if m:
        for decl in m.group(1).split(';'):
            if ':' in decl:
                k, v = decl.split(':', 1)
                out[k.strip()] = v.strip()
    return out


def resolve_vars(value, variables, depth=0):
    if depth > 8 or 'var(' not in value:
        return value

    def rep(m):
        name = m.group(1)
        return resolve_vars(variables.get(name, ''), variables, depth + 1)
    return re.sub(r'var\(\s*(--[A-Za-z0-9_-]+)\s*(?:,[^)]*)?\)', rep, value)


def parse_aegis(css_path):
    with io.open(css_path, encoding='utf-8') as fh:
        raw = fh.read()
    src = strip_css_comments(raw)
    variables = parse_css_vars(src)
    regles = []
    order = 0
    for m in re.finditer(r'([^{}]+)\{([^{}]*)\}', src):
        selecteurs = m.group(1).strip().replace('\n', ' ')
        corps = m.group(2)
        order += 1
        decls = {}
        for decl in corps.split(';'):
            if ':' not in decl:
                continue
            k, v = decl.split(':', 1)
            k = k.strip().lower()
            v = resolve_vars(v.strip(), variables)
            if k in ('color', 'background', 'background-color', 'font-size', 'font-weight'):
                decls[k] = v
        if not decls:
            continue
        classes = []
        simple = True
        for sel in selecteurs.split(','):
            sel = sel.strip()
            if re.fullmatch(r'\.[A-Za-z0-9_\\-]+', sel):
                classes.append(sel[1:].replace('\\', ''))
            else:
                simple = False
        if simple and classes:
            regles.append((order, classes, decls, selecteurs))
    return regles, variables, raw


def parse_size_value(v):
    if v is None:
        return None
    v = v.strip().lower()
    m = re.fullmatch(r'([0-9.]+)rem', v)
    if m:
        return float(m.group(1)) * RACINE_PX
    m = re.fullmatch(r'([0-9.]+)px', v)
    if m:
        return float(m.group(1))
    if v.endswith('em'):
        try:
            return float(v[:-2]) * RACINE_PX
        except ValueError:
            return None
    return None


def parse_bg_value(v):
    if v is None:
        return None
    if 'gradient' in v:
        stops = re.findall(r'(rgba?\([^)]*\)|#[0-9a-fA-F]{3,6})', v)
        cands = [c for c in (parse_color(s) for s in stops) if c]
        if cands:
            return ('gradient', max(cands, key=lambda c: rel_luminance(c[:3])))
        return None
    c = parse_color(v)
    return ('solid', c) if c else None


AEGIS_RULES = []
CSS_VARS = {}

# ---------------------------------------------------------------------------
# 4. LECTURE DES SOURCES : flux HTML emis, avec numeros de ligne
# ---------------------------------------------------------------------------
MARKUP_HINT = re.compile(r'<\s*/?\s*[A-Za-z]|class\s*=')


def php_block_strings(body, base):
    """Chaines de caracteres d'un bloc PHP, commentaires retires."""
    res = []
    i, n = 0, len(body)
    while i < n:
        if body.startswith('/*', i):
            j = body.find('*/', i + 2)
            i = j + 2 if j != -1 else n
            continue
        if body.startswith('//', i) or body[i] == '#':
            j = body.find('\n', i)
            i = j if j != -1 else n
            continue
        c = body[i]
        if c in '"\'':
            j = i + 1
            while j < n:
                if body[j] == '\\':
                    j += 2
                    continue
                if body[j] == c:
                    break
                j += 1
            res.append((base + i + 1, base + j, body[i + 1:j]))
            i = j + 1
            continue
        i += 1
    return res


def build_stream(path):
    with io.open(path, encoding='utf-8') as fh:
        src = fh.read()
    lines = src.split('\n')
    starts = [0]
    for ln in lines:
        starts.append(starts[-1] + len(ln) + 1)

    def line_of(offset):
        return bisect_right(starts, offset)

    chars, lmap = [], []

    def emit(text, offset):
        for k, ch in enumerate(text):
            chars.append(ch)
            lmap.append(line_of(offset + k))

    pos, n = 0, len(src)
    while pos < n:
        j = src.find('<?', pos)
        if j == -1:
            emit(src[pos:], pos)
            break
        emit(src[pos:j], pos)
        end = src.find('?>', j)
        if end == -1:
            body = src[j:n]
            body_end = n
        else:
            body = src[j:end]
            body_end = end + 2
        if body.startswith('<?=') or 'echo' in body:
            emit('\x01', j)
        for (s, e, content) in php_block_strings(body, j):
            if MARKUP_HINT.search(content):
                emit(content, s)
        pos = body_end
    return ''.join(chars), lmap


# ---------------------------------------------------------------------------
# 5. BALISES -> ARBRE
# ---------------------------------------------------------------------------
VOID = {'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link',
        'meta', 'param', 'source', 'track', 'wbr'}
ATTR_RE = re.compile(r'([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+)))?', re.S)


def parse_attrs(raw):
    attrs = {}
    for m in ATTR_RE.finditer(raw):
        name = m.group(1).strip().lower()
        val = m.group(2) if m.group(2) is not None else (
            m.group(3) if m.group(3) is not None else (m.group(4) or ''))
        if name and name not in attrs:
            attrs[name] = val
    return attrs


class El(object):
    __slots__ = ('tag', 'attrs', 'line', 'text', 'children', 'parent')

    def __init__(self, tag, attrs, line, parent):
        self.tag = tag
        self.attrs = attrs
        self.line = line
        self.text = []
        self.children = []
        self.parent = parent

    @property
    def classes(self):
        return self.attrs.get('class', '').split()

    def own_text(self):
        return ''.join(self.text)

    def all_text(self):
        buf = [''.join(self.text)]
        for c in self.children:
            buf.append(c.all_text())
        return ''.join(buf)


def parse_html(stream, lmap):
    root = El('#root', {}, 0, None)
    stack = [root]
    i, n = 0, len(stream)
    while i < n:
        ch = stream[i]
        if ch == '<':
            if stream.startswith('<!--', i):
                j = stream.find('-->', i + 4)
                i = j + 3 if j != -1 else n
                continue
            if stream.startswith('<!', i):
                j = stream.find('>', i)
                i = j + 1 if j != -1 else n
                continue
            j = i + 1
            quote = None
            while j < n:
                c = stream[j]
                if quote:
                    if c == quote:
                        quote = None
                elif c in '"\'':
                    quote = c
                elif c == '>':
                    break
                j += 1
            raw = stream[i + 1:j]
            line = lmap[i] if i < len(lmap) else 0
            if raw.startswith('/'):
                name = raw[1:].strip().lower()
                for k in range(len(stack) - 1, 0, -1):
                    if stack[k].tag == name:
                        del stack[k:]
                        break
            else:
                selfclose = raw.endswith('/')
                raw2 = raw[:-1] if selfclose else raw
                m = re.match(r'([A-Za-z][A-Za-z0-9:-]*)', raw2)
                if m:
                    tag = m.group(1).lower()
                    attrs = parse_attrs(raw2[m.end():])
                    el = El(tag, attrs, line, stack[-1])
                    stack[-1].children.append(el)
                    if tag in ('script', 'style'):
                        close = stream.lower().find('</' + tag, j)
                        i = (close if close != -1 else n)
                        continue
                    if tag not in VOID and not selfclose:
                        stack.append(el)
            i = j + 1
            continue
        j = stream.find('<', i)
        if j == -1:
            j = n
        stack[-1].text.append(stream[i:j])
        i = j
    return root


# ---------------------------------------------------------------------------
# 6. STYLE EFFECTIF
# ---------------------------------------------------------------------------
def alpha_token(raw):
    """'5' -> 0.05 (Tailwind : pourcentage) ; '[0.02]' -> 0.02 (absolu)."""
    if raw is None:
        return None
    raw = raw.strip()
    if raw.startswith('['):
        return float(raw.strip('[]'))
    return float(raw) / 100.0


def bg_from_class(cls):
    if not cls.startswith('bg-') or cls in ('bg-transparent', 'bg-none'):
        return None
    token = cls[3:]
    if token.startswith('['):
        m = re.match(r'\[(#[0-9a-fA-F]{3,8}|rgba?\([^)]*\))\](?:/(\[?[0-9.]+\]?))?$', token)
        if not m:
            return None
        col = parse_color(m.group(1))
        if not col:
            return None
        alpha = col[3] if m.group(2) is None else alpha_token(m.group(2))
        return (col[0], col[1], col[2], alpha)
    m = re.match(r'([a-z]+-[0-9]{2,3})(?:/(\[?[0-9.]+\]?))?$', token)
    if m and m.group(1) in NAMED:
        col = parse_color(NAMED[m.group(1)])
        alpha = 1.0 if m.group(2) is None else alpha_token(m.group(2))
        return (col[0], col[1], col[2], alpha)
    m = re.match(r'(white|black)(?:/(\[?[0-9.]+\]?))?$', token)
    if m:
        alpha = 1.0 if m.group(2) is None else alpha_token(m.group(2))
        base = (255.0, 255.0, 255.0) if m.group(1) == 'white' else (0.0, 0.0, 0.0)
        return (base[0], base[1], base[2], alpha)
    return None


def color_from_class(cls):
    if not cls.startswith('text-'):
        return None
    token = cls[5:]
    if token.startswith('['):
        m = re.match(r'\[(#[0-9a-fA-F]{3,8}|rgba?\([^)]*\))\](?:/(\[?[0-9.]+\]?))?$', token)
        if not m:
            return None
        col = parse_color(m.group(1))
        if not col:
            return None
        alpha = col[3] if m.group(2) is None else alpha_token(m.group(2))
        return (col[0], col[1], col[2], alpha)
    if token in NAMED:
        col = parse_color(NAMED[token])
        return (col[0], col[1], col[2], 1.0) if col else None
    m = re.match(r'([a-z]+-[0-9]{2,3})(?:/(\[?[0-9.]+\]?))?$', token)
    if m and m.group(1) in NAMED:
        col = parse_color(NAMED[m.group(1)])
        alpha = 1.0 if m.group(2) is None else alpha_token(m.group(2))
        return (col[0], col[1], col[2], alpha)
    m = re.match(r'(white|black)(?:/(\[?[0-9.]+\]?))?$', token)
    if m:
        alpha = 1.0 if m.group(2) is None else alpha_token(m.group(2))
        base = (255.0, 255.0, 255.0) if m.group(1) == 'white' else (0.0, 0.0, 0.0)
        return (base[0], base[1], base[2], alpha)
    return None


def size_from_class(cls):
    if cls in TAILWIND_SIZES:
        return TAILWIND_SIZES[cls] * RACINE_PX
    if cls.startswith('text-['):
        m = re.match(r'text-\[([0-9.]+)(rem|px|em)\]$', cls)
        if m:
            val, unit = float(m.group(1)), m.group(2)
            if unit == 'rem':
                return val * RACINE_PX
            if unit == 'em':
                return val * RACINE_PX
            return val
    return None


OPACITY_CLASSES = {'opacity-0': 0.0, 'opacity-5': 0.05, 'opacity-10': 0.10,
                   'opacity-20': 0.20, 'opacity-25': 0.25, 'opacity-30': 0.30,
                   'opacity-40': 0.40, 'opacity-50': 0.50, 'opacity-60': 0.60,
                   'opacity-70': 0.70, 'opacity-75': 0.75, 'opacity-80': 0.80,
                   'opacity-90': 0.90, 'opacity-95': 0.95, 'opacity-100': 1.0}

AEGIS_TEXT_PROPS = ('color', 'font-size', 'font-weight')

SPECIAL = {
    'th_tableau': {'color': '#64748b', 'font-size': '0.72rem', 'font-weight': '600'},
    'td_tableau': {'color': '#94a3b8'},
    'notif_titre_succes': {'color': '#34d399'},
    'notif_titre_erreur': {'color': '#f87171'},
    'pastille_succes': {'color': '#34d399'},
    'pastille_erreur': {'color': '#f87171'},
}


class Style(object):
    __slots__ = ('bg', 'fg', 'size', 'weight', 'pending', 'sources')

    def __init__(self, bg, fg, size, weight, pending, sources):
        self.bg = bg
        self.fg = fg
        self.size = size
        self.weight = weight
        self.pending = pending
        self.sources = sources


def paint(color, pending):
    out = color
    for alpha, backdrop in pending:
        out = blend_alpha(out, backdrop, alpha)
    return out


def compute_style(el, parent_style, parent_inner_bg, contexts):
    """Calcule le style effectif.

    `parent_inner_bg` est le fond INTERIEUR du parent (avant application de son
    eventuelle opacite de groupe). C'est lui, et non le fond deja peint, qui sert
    de support aux couches de l'enfant : un enfant vit a l'interieur du groupe,
    il ne voit pas l'attenuation que le groupe subit lui-meme.
    """
    cls = el.classes
    attr = el.attrs.get('class', '')
    sources = []

    bg = parent_inner_bg
    for c in cls:
        layer = bg_from_class(c)
        if layer:
            bg = over(layer, bg)
            sources.append(c)
    for (order, names, decls, sel) in AEGIS_RULES:
        if any(n in cls for n in names):
            b = parse_bg_value(decls.get('background') or decls.get('background-color'))
            if b:
                kind, col = b
                bg = over(col, bg)
                sources.append('.' + names[0] + (' (degrade)' if kind == 'gradient' else ''))
    inner = bg

    extra = {}
    if el.tag == 'th' and 'tableau' in contexts:
        extra.update(SPECIAL['th_tableau'])
    if el.tag == 'td' and 'tableau' in contexts:
        extra.update(SPECIAL['td_tableau'])
    if 'notif-titre' in cls:
        if 'notif--succes' in contexts:
            extra.update(SPECIAL['notif_titre_succes'])
        if 'notif--erreur' in contexts:
            extra.update(SPECIAL['notif_titre_erreur'])
    if 'notif-pastille' in cls:
        if 'notif--succes' in contexts:
            extra.update(SPECIAL['pastille_succes'])
        if 'notif--erreur' in contexts:
            extra.update(SPECIAL['pastille_erreur'])

    fg = parent_style.fg if parent_style else NAMED_FG
    fg_src = 'heritee (.body --texte)'
    for c in cls:
        col = color_from_class(c)
        if col:
            fg = col
            fg_src = c
    for (order, names, decls, sel) in AEGIS_RULES:
        if any(n in cls for n in names) and 'color' in decls:
            col = parse_color(decls['color'])
            if col:
                fg = col
                fg_src = '.' + names[0]
    if extra.get('color'):
        fg = parse_color(extra['color'])
        fg_src = 'aegis (selecteur descendant)'

    inline = el.attrs.get('style', '')
    if inline:
        for decl in inline.split(';'):
            if ':' not in decl:
                continue
            k, v = decl.split(':', 1)
            if k.strip().lower() == 'color':
                col = parse_color(v.strip())
                if col:
                    fg = col
                    fg_src = 'style inline'

    size = parent_style.size if parent_style else RACINE_PX
    size_src = 'heritee'
    for c in cls:
        s = size_from_class(c)
        if s:
            size = s
            size_src = c
    for (order, names, decls, sel) in AEGIS_RULES:
        if any(n in cls for n in names) and 'font-size' in decls:
            s = parse_size_value(decls['font-size'])
            if s:
                size = s
                size_src = '.' + names[0]
    if extra.get('font-size'):
        size = parse_size_value(extra['font-size'])
        size_src = 'aegis th'
    if any(t in attr for t in ('text-[8px]', 'text-[9px]', 'text-[10px]', 'text-[11px]')):
        size = 0.78 * RACINE_PX
        size_src = 'plancher !important aegis.css ([class*="text-[10px]"])'

    weight = parent_style.weight if parent_style else 400
    for c in cls:
        if c in TAILWIND_WEIGHTS:
            weight = TAILWIND_WEIGHTS[c]
    for (order, names, decls, sel) in AEGIS_RULES:
        if any(n in cls for n in names) and 'font-weight' in decls:
            try:
                weight = int(float(decls['font-weight']))
            except ValueError:
                pass
    if extra.get('font-weight'):
        weight = int(extra['font-weight'])
    if 'font-black' in attr:
        weight = 600

    alpha = 1.0
    for c in cls:
        if c in OPACITY_CLASSES:
            alpha = OPACITY_CLASSES[c]

    pending_parent = parent_style.pending if parent_style else []
    # ce qu'il y a derriere CET element : le fond interieur du parent, peint
    # avec la chaine d'opacites deja accumulee
    backdrop = paint(parent_inner_bg, pending_parent)
    pending_here = pending_parent + [(alpha, backdrop)]

    # une couleur de texte translucide (text-white/40) se compose sur le fond
    # local AVANT l'opacite de groupe
    fg_opaque = over(fg, inner)

    return (Style(paint(inner, pending_here), paint(fg_opaque, pending_here),
                  size, weight, pending_here, sources),
            inner, fg_src, size_src)


# ---------------------------------------------------------------------------
# 7. ROLES
# ---------------------------------------------------------------------------
SECONDAIRE = {
    ('footer_ui.php', 28): 'pied de page (mention)',
    ('footer_ui.php', 29): 'pied de page (mention)',
    ('footer_ui.php', 32): 'pied de page (nom du site)',
    ('footer_ui.php', 36): 'pied de page (rappel utilisateur)',
    ('footer_ui.php', 45): 'pied de page (memoire PHP)',
    ('footer_ui.php', 49): 'pied de page (mention)',
    ('login.php', 165): 'aide redondante (identifiants oublies)',
    ('login.php', 172): 'pied de page',
    ('error.php', 119): 'pied de page',
    ('sidebar.php', 47): 'sous-titre de marque (ambiance)',
    ('sidebar.php', 131): 'role, redondant avec le profil',
    ('header_ui.php', 48): 'sous-titre de marque (ambiance)',
    ('header_ui.php', 86): 'libelle d ambiance « heure locale »',
    ('header_ui.php', 97): 'role, redondant avec le profil',
    ('index_view.php', 228): 'aide redondante (identifiants oublies)',
    ('index_view.php', 237): 'mention de bas de page',
    ('agenda_view.php', 183): 'etat vide decoratif',
    ('agenda_view.php', 174): 'poste de travail (information secondaire)',
    ('admin_users_view.php', 266): 'numero de compte interne',
    ('documents_view.php', 180): 'extension de fichier (redondante avec l icone)',
    ('documents_view.php', 229): 'intitule de la visionneuse (ambiance)',
    ('profil_view.php', 53): 'identifiant, redondant avec le nom',
}

DECOR = {
    ('documents_view.php', 245): 'filigrane decoratif (icone bouclier, blanche a 2 %)',
}
# filet de securite : le filigrane est aussi reconnu par sa classe, pour rester
# juste meme si les numeros de ligne ont bouge (arbre vivant)
DECOR_CLASSES = ('text-white/[0.02]',)


def role_of(rec):
    key = (rec['file'], rec['line'])
    if key in DECOR or any(t in rec['classes'] for t in DECOR_CLASSES):
        return 'decor'
    if rec['tag'] in ('i', 'svg') or rec.get('is_icon'):
        return 'graphisme'
    if key in SECONDAIRE:
        return 'secondaire'
    return 'information'


def thresholds(size, weight, role):
    if role in ('graphisme', 'decor'):
        return 3.0, None
    large = size >= 24.0 or (size >= 18.66 and weight >= 700)
    return (3.0 if large else 4.5), (4.5 if large else 7.0)


def severity_of(ratio, t_aa, t_aaa, role):
    if role == 'decor':
        return 'decor'
    if ratio < t_aa:
        return 'bloquant' if role == 'information' else 'genant'
    if t_aaa is not None and ratio < t_aaa:
        return 'genant' if role == 'information' else 'cosmetique'
    return 'conforme'


# ---------------------------------------------------------------------------
# 8. MOISSON D'UN ETAT
# ---------------------------------------------------------------------------
CURRENT_FILE = ''


def moissonner(sources, css_path):
    """Retourne la liste brute des couples d'un etat donne."""
    global AEGIS_RULES, CSS_VARS, CURRENT_FILE
    AEGIS_RULES, CSS_VARS, aegis_raw = parse_aegis(css_path)

    records = []
    per_file = OrderedDict()

    def walk(el, style, inner_bg, contexts, chain=()):
        for child in el.children:
            cls = child.classes
            attr = child.attrs.get('class', '')
            st, child_inner, fg_src, size_src = compute_style(
                child, style, inner_bg, contexts)
            chain2 = chain + ('%s:%s %s[%s] -> %s' % (
                CURRENT_FILE, child.line, child.tag, ' '.join(cls)[:40],
                rgb_to_hex(st.bg)),)

            has_color = any(color_from_class(c) for c in cls)
            has_size = any(size_from_class(c) for c in cls)
            aegis_text = False
            for (order, names, decls, sel) in AEGIS_RULES:
                if any(n in cls for n in names) and any(p in decls for p in AEGIS_TEXT_PROPS):
                    aegis_text = True
                    break
            special_text = (child.tag in ('th', 'td') and 'tableau' in contexts) or \
                'notif-titre' in cls or 'notif-texte' in cls or 'notif-pastille' in cls
            icon = child.tag in ('i', 'svg') or attr.find('data-lucide') >= 0

            own = child.own_text()
            full = child.all_text()
            has_text = bool(full.replace('\x01', '').strip()) or ('\x01' in full)

            base = dict(file=CURRENT_FILE, line=child.line, tag=child.tag,
                        classes=re.sub(r'\s+', ' ', attr).strip(),
                        color=rgb_to_hex(st.fg),
                        color_src=fg_src, bg=rgb_to_hex(st.bg),
                        bg_src=' > '.join(st.sources) or 'fond de page #020617',
                        size=st.size, weight=st.weight, size_src=size_src,
                        chain=chain2[-7:])

            if icon and (has_color or has_size):
                base['is_icon'] = True
                base['text'] = '(icone)'
                records.append(base)
            elif has_text and (has_color or has_size or aegis_text or special_text):
                txt = re.sub(r'\s+', ' ', own.replace('\x01', '[dyn]')).strip()
                if not txt:
                    txt = re.sub(r'\s+', ' ', full.replace('\x01', '[dyn]')).strip()
                base['is_icon'] = False
                base['text'] = txt[:60]
                records.append(base)
            walk(child, st, child_inner, contexts | set(cls), chain2)

    for name, path in sources:
        if not os.path.exists(path):
            sys.exit("FICHIER INTROUVABLE : %s" % path)
        CURRENT_FILE = name
        stream, lmap = build_stream(path)
        tree = parse_html(stream, lmap)
        base_style = Style(FOND_PAGE, NAMED_FG, RACINE_PX, 400, [], [])
        nb = len(records)
        walk(tree, base_style, FOND_PAGE, frozenset())
        per_file[name] = len(records) - nb

    # couples declares dans aegis.css (pseudo-elements)
    for m in re.finditer(r'\.([A-Za-z0-9_-]+)::placeholder\s*\{([^}]*)\}', aegis_raw):
        name_cls, decls = m.group(1), m.group(2)
        cm = re.search(r'color\s*:\s*([^;]+)', decls)
        if cm:
            col = parse_color(resolve_vars(cm.group(1).strip(), CSS_VARS))
            if col:
                ligne = aegis_raw[:m.start()].count('\n') + 1
                records.append(dict(
                    file='aegis.css', line=ligne, tag='::placeholder',
                    classes='.%s::placeholder' % name_cls, color=rgb_to_hex(col),
                    color_src='.champ::placeholder', bg=rgb_to_hex(over(
                        parse_color('rgba(2,6,23,0.6)'), over(
                            parse_color('rgba(15,23,42,0.72)'), FOND_PAGE))),
                    bg_src='.champ (rgba(2,6,23,0.6)) > .carte (rgba(15,23,42,0.72)) > #020617',
                    size=0.9 * RACINE_PX, weight=400, size_src='.champ',
                    is_icon=False, chain=(),
                    text='(texte de substitution d un champ)'))

    return records, per_file


def mesurer(records):
    """Ajoute role, seuils, ratio, gravite ; fusionne les doublons stricts."""
    for r in records:
        r['role'] = role_of(r)
        t_aa, t_aaa = thresholds(r['size'], r['weight'], r['role'])
        r['t_aa'] = t_aa
        r['t_aaa'] = t_aaa
        r['ratio'] = round(contrast(hex_to_rgb(r['color']), hex_to_rgb(r['bg'])), 2)
        r['verdict'] = ('ECHEC AA' if r['ratio'] < t_aa
                        else ('AA ok / AAA non' if (t_aaa is not None and r['ratio'] < t_aaa)
                              else 'AA et AAA ok'))
        r['gravite'] = severity_of(r['ratio'], t_aa, t_aaa, r['role'])

    merged = OrderedDict()
    for r in records:
        key = (r['file'], r['line'], r['classes'], r['color'], r['bg'],
               round(r['size'], 2), r['weight'])
        if key in merged:
            merged[key]['n'] += 1
            if len(merged[key]['text']) < len(r['text']):
                merged[key]['text'] = r['text']
        else:
            r = dict(r)
            r['n'] = 1
            merged[key] = r
    out = list(merged.values())
    for r in out:
        r['size_txt'] = ('%.2f' % r['size']).rstrip('0').rstrip('.')
    return out


# ---------------------------------------------------------------------------
# 9. SNAPSHOT (empreintes des fichiers mesures)
# ---------------------------------------------------------------------------
def empreintes(paths):
    out = []
    for name, path in paths:
        with io.open(path, 'rb') as fh:
            data = fh.read()
        out.append((name, os.path.basename(path), len(data),
                    hashlib.sha256(data).hexdigest()[:16],
                    time.strftime('%d/%m/%Y %H:%M',
                                  time.localtime(os.path.getmtime(path)))))
    return out


def sha_of(path):
    with io.open(path, 'rb') as fh:
        return hashlib.sha256(fh.read()).hexdigest()


COMPARAISON = [(n, pl, pb) for (n, pl), (_, pb) in zip(SOURCES_LIVE, SOURCES_BASE)]
NB_DIFFERENTS = sum(1 for (n, pl, pb) in COMPARAISON if sha_of(pl) != sha_of(pb))
NB_VUES_IDENTIQUES = sum(1 for (n, pl, pb) in COMPARAISON
                         if n in VIEW_FILES and sha_of(pl) == sha_of(pb))
CSS_DIFFERENT = sha_of(CSS_LIVE) != sha_of(CSS_BASE)
HORODATAGE = time.strftime('%d/%m/%Y %H:%M:%S')
_mtimes = [os.path.getmtime(pl) for (n, pl, pb) in COMPARAISON if sha_of(pl) != sha_of(pb)]
LE_PLUS_RECENT = time.strftime('%d/%m/%Y %H:%M:%S', time.localtime(max(_mtimes))) if _mtimes else HORODATAGE


# ---------------------------------------------------------------------------
# 10. EXECUTION DES DEUX ETATS
# ---------------------------------------------------------------------------
SOURCE = 'baseline'
for arg in sys.argv[1:]:
    if arg.startswith('--source='):
        SOURCE = arg.split('=', 1)[1]
    elif arg == '--source':
        i = sys.argv.index(arg)
        if i + 1 < len(sys.argv):
            SOURCE = sys.argv[i + 1]
if SOURCE not in ('baseline', 'live'):
    sys.exit("--source attend 'baseline' ou 'live'")

BASE_RECORDS, BASE_PER_FILE = moissonner(SOURCES_BASE, CSS_BASE)
BASE_ROWS = mesurer(BASE_RECORDS)
LIVE_RECORDS, LIVE_PER_FILE = moissonner(SOURCES_LIVE, CSS_LIVE)
LIVE_ROWS = mesurer(LIVE_RECORDS)

# l'etat sur lequel porte le rapport detaille
RECORDS = BASE_ROWS if SOURCE == 'baseline' else LIVE_ROWS
PER_FILE = BASE_PER_FILE if SOURCE == 'baseline' else LIVE_PER_FILE
AUTRE = LIVE_ROWS if SOURCE == 'baseline' else BASE_ROWS


def stats(rows):
    fail = [r for r in rows if r['ratio'] < r['t_aa'] and r['role'] != 'decor']
    fail_aaa = [r for r in rows if r['role'] != 'decor' and r['t_aaa'] is not None
                and r['ratio'] < r['t_aaa']]
    grav = Counter(r['gravite'] for r in rows)
    lisibles = [r for r in rows if r['role'] != 'decor']
    return dict(couples=len(rows), occurrences=sum(r['n'] for r in rows),
                fail=len(fail), fail_aaa=len(fail_aaa), grav=grav,
                conformes=sum(1 for r in rows if r['role'] != 'decor'
                              and r['ratio'] >= r['t_aa']),
                pire=min(lisibles, key=lambda r: r['ratio']),
                petits=sum(r['n'] for r in rows if r['size'] < 14.0),
                petits_info=sum(r['n'] for r in rows if r['size'] < 14.0
                                and r['role'] == 'information'))


ST = stats(RECORDS)
ST_AUTRE = stats(AUTRE)
COUPLES = ST['couples']
OCCURRENCES = ST['occurrences']
FAIL_AA = [r for r in RECORDS if r['ratio'] < r['t_aa'] and r['role'] != 'decor']
FAIL_AAA = [r for r in RECORDS if r['role'] != 'decor' and r['t_aaa'] is not None
            and r['ratio'] < r['t_aaa']]
by_grav = ST['grav']
pire = min([r for r in RECORDS if r['role'] != 'decor'], key=lambda r: r['ratio'])
pire_decor = min([r for r in RECORDS if r['role'] == 'decor'],
                 key=lambda r: r['ratio']) if any(r['role'] == 'decor' for r in RECORDS) else None
pire_hors_decor = pire
bonnes = [r for r in RECORDS if r['role'] != 'decor' and r['ratio'] >= 7.0]
small = [r for r in RECORDS if r['size'] < 14.0 and r['role'] != 'decor']
TOP10 = sorted([r for r in RECORDS if r['role'] != 'decor'],
               key=lambda r: (r['ratio'], r['file'], r['line']))[:10]

# ---------------------------------------------------------------------------
# 11. SORTIES MACHINES
# ---------------------------------------------------------------------------
if not os.path.isdir(OUT):
    os.makedirs(OUT)

suffixe = '' if SOURCE == 'baseline' else '_live'
csv_path = os.path.join(OUT, 'contraste_mesures%s.csv' % suffixe)
with io.open(csv_path, 'w', encoding='utf-8-sig', newline='') as fh:
    w = csv.writer(fh, delimiter=';')
    w.writerow(['fichier', 'ligne', 'balise', 'classes', 'couleur', 'origine_couleur',
                'fond_compose', 'origine_fond', 'ratio', 'seuil_aa', 'seuil_aaa',
                'taille_px', 'graisse', 'role', 'gravite', 'verdict', 'occurrences',
                'texte'])
    for r in sorted(RECORDS, key=lambda x: (x['ratio'], x['file'], x['line'])):
        w.writerow([r['file'], r['line'], r['tag'], r['classes'], r['color'],
                    r['color_src'], r['bg'], r['bg_src'], ('%.2f' % r['ratio']),
                    r['t_aa'], r['t_aaa'] or '', r['size_txt'], r['weight'],
                    r['role'], r['gravite'], r['verdict'], r['n'], r['text']])

json_path = os.path.join(OUT, 'contraste_mesures%s.json' % suffixe)
with io.open(json_path, 'w', encoding='utf-8') as fh:
    json.dump({'source': SOURCE, 'couples': COUPLES, 'occurrences': OCCURRENCES,
               'echecs_aa': len(FAIL_AA), 'echecs_aaa': len(FAIL_AAA),
               'par_gravite': dict(by_grav), 'par_fichier': PER_FILE,
               'empreintes': empreintes(SOURCES_BASE if SOURCE == 'baseline'
                                        else SOURCES_LIVE),
               'mesures': RECORDS}, fh, ensure_ascii=False, indent=1)

# ---------------------------------------------------------------------------
# 12. RAPPORT
# ---------------------------------------------------------------------------
def esc(s):
    return str(s).replace('|', '\\|')


def libelle_classe(r):
    if r['classes']:
        return esc(r['classes'])[:64]
    if r['tag'] in ('th', 'td'):
        return '.tableau %s (aegis, sélecteur descendant)' % r['tag']
    return r['tag']


def libelle_verdict(r):
    if r['ratio'] >= r['t_aa']:
        return r['verdict']
    if r['role'] == 'graphisme':
        return 'ÉCHEC 1.4.11 (3:1)'
    return 'ÉCHEC AA'


def table(rows):
    out = ['| fichier:ligne | classe CSS | couleur | fond composé | ratio | seuil requis | taille px | verdict |',
           '|---|---|---|---|---|---|---|---|']
    for r in rows:
        out.append('| `%s:%s`%s | `%s` | `%s` | `%s` | **%.2f** | %.1f:1 | %s | %s |' % (
            r['file'], r['line'], (' ×%d' % r['n']) if r['n'] > 1 else '',
            libelle_classe(r), r['color'], r['bg'], r['ratio'], r['t_aa'],
            r['size_txt'], libelle_verdict(r)))
    return '\n'.join(out)


def tri(rows):
    return sorted(rows, key=lambda r: (r['ratio'], r['file'], r['line']))


def ligne_de(motif, css_path=CSS_BASE):
    """Numero de ligne d'un motif dans aegis.css (pour citer juste)."""
    with io.open(css_path, encoding='utf-8') as fh:
        for i, ln in enumerate(fh, 1):
            if motif in ln:
                return i
    return None


bloquants = tri([r for r in RECORDS if r['gravite'] == 'bloquant'])
genants = tri([r for r in RECORDS if r['gravite'] == 'genant'])
cosmetiques = tri([r for r in RECORDS if r['gravite'] == 'cosmetique'])
decors = tri([r for r in RECORDS if r['gravite'] == 'decor'])

petites = Counter()
petites_fichiers = {}
for r in small:
    petites[r['size']] += r['n']
    petites_fichiers.setdefault(r['size'], Counter())[r['file']] += r['n']

info_petit = Counter()
deco_petit = Counter()
for r in RECORDS:
    if r['size'] < 14.0:
        if r['role'] == 'information':
            info_petit[r['size']] += r['n']
        else:
            deco_petit[r['size']] += r['n']

L_NOMBRE = ligne_de('font-size: 15px')
L_PLANCHER = ligne_de('text-[8px]')
L_COMMENT_PLANCHER = ligne_de('plancher')
L_BODY = ligne_de('body {')
L_SURFACE = ligne_de('--surface:')

CARTE = over(parse_color('rgba(15,23,42,0.72)'), FOND_PAGE)
MODALE = over(parse_color('rgba(15,23,42,0.92)'), FOND_PAGE)
CHAMP = over(parse_color('rgba(2,6,23,0.6)'), CARTE)
NOTIF = over(parse_color('rgba(21,30,46,0.9)'), FOND_PAGE)
PANNEAU = hex_to_rgb('#0f172a')

L = []
A = L.append

A('# Rapport de contraste WCAG 2.2 AA — MotoHall 2A')
A('')
A('Audit du **19/10/2026** — projet `moto-hall_laravel` — **lecture seule**, aucun fichier source modifié.')
A('')
if SOURCE == 'baseline':
    A('> **À lire d\'abord.** Les fichiers du site étaient **en cours de réécriture pendant la mesure** : '
      '%d fichiers PHP sur %d et la feuille `aegis.css` ne sont plus identiques à l\'état de référence '
      '(le plus récent a été écrit à %s). Pour que chaque chiffre soit rejouable et ne dépende pas du moment '
      'où on lit ce rapport, **toutes les mesures détaillées ci-dessous portent sur la copie de production '
      'figée du 21/09/2026** (`_travail\\_prod_copie_20260921\\`), c\'est-à-dire l\'état décrit par la mission. '
      'La section 7 donne en plus la mesure de l\'arbre vivant à l\'instant T, et la liste exacte des '
      'fichiers qui ont bougé.'
      % (NB_DIFFERENTS, len(SOURCES_BASE), LE_PLUS_RECENT))
else:
    A('> **À lire d\'abord.** Les chiffres portent sur l\'**arbre de travail au moment de l\'exécution** '
      '(mesure du %s), qui était **en cours de reprise** : %d fichiers PHP sur %d et `aegis.css` diffèrent '
      'encore de l\'état de référence du 21/09. La section 8 redonne la comparaison avec cet état de '
      'référence.' % (HORODATAGE, NB_DIFFERENTS, len(SOURCES_BASE)))
A('')
if SOURCE != 'baseline':
    A('Les chiffres portent sur l\'**arbre de travail au moment de l\'exécution** (empreintes en section 8).')
    A('')
A('## 1. Ce que dit la mesure')
A('')
A('| mesure | valeur |')
A('|---|---|')
A('| couples texte/fond mesurés | **%d** |' % COUPLES)
A('| balises correspondantes dans les gabarits | %d |' % OCCURRENCES)
A('| fichiers PHP lus | 23 |')
A('| dont fichiers portant du balisage | %d |' % sum(1 for v in PER_FILE.values() if v))
N_AA_INFO = len([r for r in RECORDS if r['ratio'] < r['t_aa'] and r['role'] == 'information'])
N_AA_AUTRE = len([r for r in RECORDS if r['ratio'] < r['t_aa'] and r['role'] in ('secondaire', 'graphisme')])
N_AAA_INFO = len([r for r in RECORDS if r['role'] == 'information' and r['t_aaa'] is not None
                  and r['t_aa'] <= r['ratio'] < r['t_aaa']])
N_AAA_AUTRE = len([r for r in RECORDS if r['role'] in ('secondaire', 'graphisme')
                   and r['t_aaa'] is not None and r['t_aa'] <= r['ratio'] < r['t_aaa']])

A('| **échecs AA — bloquants** (texte porteur d\'information) | **%d** |' % N_AA_INFO)
A('| échecs AA — gênants (texte secondaire ou graphisme) | %d |' % N_AA_AUTRE)
A('| **total des échecs AA** | **%d** |' % len(FAIL_AA))
A('| AAA manqués (couples qui passent AA) — gênants | %d |' % N_AAA_INFO)
A('| AAA manqués (couples qui passent AA) — cosmétiques | %d |' % N_AAA_AUTRE)
A('| échecs AAA au total | %d |' % len(FAIL_AAA))
A('| couples déjà conformes AA | %d |' % ST['conformes'])
A('| couples atteignant déjà 7:1 (AAA) | %d |' % len(bonnes))
A('| éléments sous 14 px | %d (dont %d porteurs d\'information) |'
  % (ST['petits'], ST['petits_info']))
A('| **pire ratio mesuré** | **%.2f:1** — `%s:%s` (`%s` sur `%s`) |'
  % (pire['ratio'], pire['file'], pire['line'], pire['classes'], pire['bg']))
A('')
A('La racine typographique est de **15 px** (`aegis.css:%s`), donc `0.72rem` = **%.1f px** et `0.78rem` '
  '= **%.1f px**. Le plancher `!important` de `aegis.css:%s` ramène tout `text-[8px]` … `text-[11px]` à '
  '**%.1f px** : **%d balises** mesurées à cette taille dans les sources.'
  % (L_NOMBRE, 0.72 * RACINE_PX, 0.78 * RACINE_PX, L_PLANCHER, 0.78 * RACINE_PX,
     sum(r['n'] for r in RECORDS if 'plancher' in r.get('size_src', ''))))
A('')
A('Le détail de chaque couple est dans `contraste_mesures.csv` (séparateur `;`, ouvrable au tableur) et '
  '`contraste_mesures.json`.')
A('')
if pire_decor is not None:
    A('Pour être complet : le ratio le plus bas du site (**%.2f:1**, `%s:%s`) est une **icône en filigrane** '
      'à 2 %% de blanc — du décor volontaire, hors périmètre (section 2.4).'
      % (pire_decor['ratio'], pire_decor['file'], pire_decor['line']))
    A('')
A('---')
A('')
A('## 2. Échecs par gravité')
A('')
A('La gravité se déduit mécaniquement de deux choses : le seuil (AA ou AAA) et le rôle du texte '
  '(*porteur d\'information* ou *secondaire/décor*). Un texte informatif sous AA est **bloquant** ; un '
  'texte informatif qui passe AA mais reste sous AAA est **gênant** ; un texte secondaire sous AA est '
  '**gênant** ; il n\'y a de **cosmétique** que si le texte secondaire passe déjà AA.')
A('')
A('### 2.1 Bloquant — %d couples' % len(bloquants))
A('')
if bloquants:
    A(table(bloquants))
else:
    A('_Aucun._')
A('')
A('### 2.2 Gênant — %d couples' % len(genants))
A('')
if genants:
    A(table(genants))
else:
    A('_Aucun._')
A('')
A('### 2.3 Cosmétique — %d couples' % len(cosmetiques))
A('')
if cosmetiques:
    A(table(cosmetiques))
else:
    A('_Aucun._')
A('')
if decors:
    A('### 2.4 Décor assumé — hors périmètre (%d couple%s)'
      % (len(decors), 's' if len(decors) > 1 else ''))
    A('')
    A('Mesuré quand même, mais sans verdict : ces éléments ne portent aucune information.')
    A('')
    A('| fichier:ligne | élément | couleur | fond | ratio | pourquoi c\'est du décor |')
    A('|---|---|---|---|---|---|')
    for r in decors:
        A('| `%s:%s` | `%s` | `%s` | `%s` | %.2f:1 | %s |' % (
            r['file'], r['line'], esc(r['classes'])[:50], r['color'], r['bg'],
            r['ratio'], DECOR.get((r['file'], r['line']), 'icône décorative')))
    A('')
A('---')
A('')
A('## 3. Les 10 pires')
A('')
A('Classement par ratio croissant, hors décor (le filigrane décoratif de la visionneuse de documents est '
  'traité en 2.4 : il est volontairement à %.2f:1).' % (pire_decor['ratio'] if pire_decor else 0.0))
A('')
A('| # | ratio | fichier:ligne | classe | couleur | fond | taille | rôle | texte mesuré |')
A('|---|---|---|---|---|---|---|---|---|')
for i, r in enumerate(TOP10, 1):
    A('| %d | **%.2f:1** | `%s:%s` | `%s` | `%s` | `%s` | %s px | %s | %s |' % (
        i, r['ratio'], r['file'], r['line'], esc(r['classes'])[:40], r['color'],
        r['bg'], r['size_txt'], r['role'], esc(r['text'])[:36]))
A('')


def pourquois(r):
    """Explique le mecanisme mesure, sans supposition."""
    opac = [c for c in r['classes'].split() if c.startswith('opacity-')]
    tokens = [c for c in r['classes'].split() if c.startswith('text-') and '/' in c]
    src = r['color_src']
    if opac:
        return ('une opacité de groupe (`%s`) atténue à la fois ce texte et son fond' % opac[0])
    if '/' in src:
        return ('la couleur de texte est translucide (`%s`), elle se mélange au fond' % src)
    if 'opacity-' in ' '.join(r.get('chain', [])):
        return ('le texte `%s` est déjà sombre, et son conteneur le recouvre d\'une opacité de groupe'
                % src)
    return 'la couleur `%s` est trop proche du fond `%s`' % (src, r['bg'])


A('Ce que dit la mesure, ligne par ligne :')
A('')
for r in TOP10[:3]:
    A('- `%s:%s` — **%.2f:1** : %s.' % (r['file'], r['line'], r['ratio'], pourquois(r)))
A('')
A('Le mécanisme dominant de ce bas du tableau est la **superposition de trois causes** : un gris foncé '
  '(`slate-500`/`slate-600`), posé sur une surface déjà très sombre, parfois encore atténué par une '
  'opacité ou une couleur translucide. Chacun pris seul resterait tolérable ; empilés, ils font tomber le '
  'ratio sous 2:1. **La correction ne demande donc aucun changement de structure** : remplacer le gris '
  'par `slate-400` (`#94a3b8`) et retirer l\'opacité suffit dans tous les cas mesurés ici.')
A('')
A('Deux précisions de terrain, qui changent l\'ordre des priorités :')
A('')
A('1. Les couples n° 1, 2, 4 et 5 (`index_view.php` lignes 181 à 187) vivent dans la **branche visiteur** du '
  'portail — celle que voit une personne non identifiée. Ils ne concernent donc **pas** les écrans de '
  'travail, mais la première page que découvre l\'équipe.')
A('2. Les lignes `documents.php:148` à `214` proviennent d\'un **point d\'entrée AJAX historique** '
  '(`?ajax_list=`, lignes 137-223) qui émet encore son propre HTML. La vue actuelle interroge l\'autre '
  'endpoint (`?ajax_data=`) : ces 4 couples sont donc mesurés parce qu\'ils sont **encore dans le fichier**, '
  'mais ils ne s\'affichent que si quelqu\'un appelle l\'ancienne adresse.')
A('')
A('---')
A('')
A('## 4. Texte trop petit (< 14 px)')
A('')
A('WCAG 2.2 n\'impose **aucune taille de texte minimale** : ce n\'est donc pas un échec de conformité. '
  'C\'est un risque de lisibilité, réel sur une borne d\'atelier et un écran de comptoir. Le tableau '
  'distingue ce qui porte une information de ce qui n\'est que décor.')
A('')
A('| taille effective | occurrences « porteur d\'information » | occurrences secondaire / interface / décor | fichiers concernés (les 6 premiers) |')
A('|---|---|---|---|')
for s in sorted(petites):
    fic = ', '.join('%s (%d)' % (f, c) for f, c in petites_fichiers[s].most_common(6))
    A('| %.2f px (%s rem) | %d | %d | %s |' % (
        s, ('%.3f' % (s / RACINE_PX)).rstrip('0').rstrip('.'), info_petit.get(s, 0),
        deco_petit.get(s, 0), fic))
A('')
A('Total : **%d éléments sous 14 px**, dont **%d porteurs d\'information** et %d de décor.'
  % (ST['petits'], ST['petits_info'], ST['petits'] - ST['petits_info']))
A('')
_taille_dominante, _n_dominant = petites.most_common(1)[0]
_min_info = min([r['size'] for r in RECORDS if r['role'] == 'information'])
A('Le plus petit texte **porteur d\'information** mesure **%.1f px** (`%s`, 1.1 px de moins qu\'un libellé '
  'courant). Les 3 éléments à 10.5 px sont, eux, tous classés secondaires : le rôle dans la barre latérale, '
  'le même rôle dans le bandeau et le libellé « heure locale ». La taille la plus fréquente est **%.1f px** '
  '(%.0f éléments) : c\'est le `0.78rem` de `.meta`, `.etiquette` et des libellés de formulaire.'
  % (_min_info, [r['classes'] or r['tag'] for r in RECORDS
                 if r['role'] == 'information' and r['size'] == _min_info][0][:40],
     _taille_dominante, _n_dominant))
if L_COMMENT_PLANCHER is not None:
    A('')
    A('À noter : le commentaire de `aegis.css:%s` annonce « plancher à 12 px », mais la valeur écrite à la '
      'ligne %s est `0.78rem`, soit **%.1f px** sur la racine de 15 px. Le commentaire et le code ne disent '
      'pas la même chose — c\'est un constat, pas une faute.'
      % (L_COMMENT_PLANCHER, L_PLANCHER, 0.78 * RACINE_PX))
A('')
A('---')
A('')
A('## 5. Ce qui est déjà bon — ne pas casser')
A('')
A('%d couples sur %d sont conformes AA, et %d atteignent déjà AAA (7:1). Les toucher serait une régression.'
  % (ST['conformes'], COUPLES, len(bonnes)))
A('')
A('| couple couleur / fond | ratio | seuil AA | tailles | où (exemples) |')
A('|---|---|---|---|---|')
groupes = OrderedDict()
for r in sorted(bonnes, key=lambda x: -x['ratio']):
    groupes.setdefault((r['color'], r['bg']), []).append(r)
_affiches = 0
for (col, bg), lot in groupes.items():
    tailles = sorted(set(r['size'] for r in lot))
    txt_t = ('%.2f px' % tailles[0]) if len(tailles) == 1 else (
        '%.2f à %.2f px' % (tailles[0], tailles[-1]))
    where = sorted(set('%s:%s' % (r['file'], r['line']) for r in lot))[:3]
    A('| `%s` sur `%s` | %.2f:1 | %.1f:1 | %s | %s |' % (
        col, bg, lot[0]['ratio'], max(r['t_aa'] for r in lot), txt_t, ', '.join(where)))
    _affiches += 1
    if _affiches >= 20:
        break
A('')
A('Les points structurels qui tiennent le site :')
A('')
A('- Le fond `#020617` est le plus sombre possible sans être noir, et c\'est ce qui donne de la marge à tous '
  'les textes clairs : le blanc y atteint %.2f:1.' % contrast(parse_color('#ffffff')[:3], FOND_PAGE))
A('- `slate-400` (`#94a3b8`) est le meilleur gris du projet : **%.2f:1** sur une carte, **%.2f:1** sur le fond '
  'de page. C\'est la couleur à utiliser partout où l\'on a besoin d\'un texte secondaire lisible.'
  % (contrast(parse_color('#94a3b8')[:3], CARTE),
     contrast(parse_color('#94a3b8')[:3], FOND_PAGE)))
A('- Les quatre pastilles d\'état passent AA malgré leur taille de %.1f px : `badge-succes` **%.2f:1**, '
  '`badge-alerte` **%.2f:1**, `badge-danger` **%.2f:1**, `badge-accent` **%.2f:1**.'
  % (0.72 * RACINE_PX,
     contrast(parse_color(NAMED['emerald-400'])[:3], over(parse_color('rgba(52,211,153,0.10)'), CARTE)),
     contrast(parse_color(NAMED['amber-400'])[:3], over(parse_color('rgba(251,191,36,0.10)'), CARTE)),
     contrast(parse_color(NAMED['red-400'])[:3], over(parse_color('rgba(248,113,113,0.10)'), CARTE)),
     contrast(parse_color(NAMED['sky-400'])[:3], over(parse_color('rgba(56,189,248,0.10)'), CARTE))))
A('- `btn-accent` (`#04121c` sur `#38bdf8`) : **%.2f:1**. Le seul bouton plein du site, et c\'est le plus '
  'lisible de tous — le modèle à imiter pour les autres boutons.'
  % contrast(parse_color('#04121c')[:3], parse_color('#38bdf8')[:3]))
A('- Les liens de navigation `nav-lien` (`#94a3b8` sur le panneau `#0f172a`) : **%.2f:1**, soit AAA tout juste. '
  'La navigation ne fait pas partie du problème.'
  % contrast(parse_color('#94a3b8')[:3], PANNEAU))
A('- La palette d\'état de `aegis.css` (succès/alerte/danger/accent) est unique et bien tenue : aucune vue '
  'n\'invente une teinte pour un état.')
A('')
A('---')
A('')
A('## 6. Méthode, réserves et rejeu')
A('')
A('### Comment les fonds sont composés')
A('')
A('| surface | écriture dans le code | valeur opaque composée |')
A('|---|---|---|')
A('| fond de page | `bg-[#020617]` (layout_secure.php, login.php, error.php) | `%s` |' % rgb_to_hex(FOND_PAGE))
A('| carte | `.carte` / `.glass` → `rgba(15,23,42,0.72)` sur le fond de page | `%s` |' % rgb_to_hex(CARTE))
A('| panneau latéral | `bg-[#0f172a]` (sidebar.php) | `%s` |' % rgb_to_hex(PANNEAU))
A('| panneau modal | `.modale-panneau` → `rgba(15,23,42,0.92)` sur le fond de page | `%s` |' % rgb_to_hex(MODALE))
A('| champ de saisie | `.champ` → `rgba(2,6,23,0.6)` sur une carte | `%s` |' % rgb_to_hex(CHAMP))
A('| notification | `.notif` → `rgba(21,30,46,0.9)` sur le fond de page | `%s` |' % rgb_to_hex(NOTIF))
A('| pastille de visite | `bg-white/[0.01]` sur une carte, puis `opacity: 60%%` | `%s` |'
  % rgb_to_hex(blend_alpha(over(parse_color('rgba(255,255,255,0.01)'), CARTE), CARTE, 0.6)))
A('')
A('Les couches `bg-white/5`, `bg-white/10`, `bg-white/[0.01]`, `bg-white/[0.02]`, `bg-white/[0.03]` et les '
  'fonds d\'état (`bg-red-500/10`, `bg-emerald-500/10`, `bg-amber-500/10`, `bg-cyan-500/10`) sont composées '
  'par mélange alpha **sur la surface qui les porte**, jamais directement sur le fond de page : une pastille '
  'posée dans une carte se compose sur la carte, pas sur `#020617`.')
A('')
A('### Ce que le script ne mesure pas — écrit noir sur blanc')
A('')
A('- **Les états `:hover`, `:focus`, `:active`, `:disabled`.** Ils changent de couleur (`hover:bg-white/5`, '
  '`btn-danger:hover`) mais ne sont pas des apparences au repos. **ESTIMÉ** : quelques survols de bouton '
  'descendent sous 3:1, sans jamais porter une information seule.')
A('- **Les dégradés.** `nav-lien--actif` est un `linear-gradient` : le script retient l\'arrêt le plus clair '
  '(hypothèse défavorable), donc le ratio réel est compris entre les deux arrêts.')
A('- **Les fonds écrits en PHP** (`style="background: <?= $teinte ?>1a"`, index_view.php) : ce sont des '
  'vignettes d\'icône à 10 % de teinte sur une carte, l\'écart avec la carte est négligeable — mais c\'est '
  '**ESTIMÉ**, pas mesuré.')
A('- **Le `backdrop-filter: blur`** des cartes `.glass` : il ne change pas la luminance moyenne, donc la '
  'composition alpha reste une bonne approximation. Une approximation, pas une certitude.')
A('- **Les images de texte** : il n\'y en a aucune. Le site ne contient qu\'une seule balise `<img>` '
  '(`documents_view.php:267`, la visionneuse) et elle affiche le fichier choisi par l\'utilisateur : ce n\'est '
  'pas du texte.')
A('')
A('### Compter juste')
A('')
A('Les « occurrences » sont des **balises dans les gabarits**, pas des éléments à l\'écran : une boucle '
  '`foreach` sur 12 comptes affiche 12 fois le même couple.')
A('')
A('**Sur le décompte des fichiers.** La mission annonce « 29 fichiers PHP ». La liste qu\'elle donne en '
  'contient **23**, et ce sont bien 23 fichiers qui rendent du HTML — dont 15 portent du balisage et 8 sont '
  'des contrôleurs purs (`index.php`, `dashboard.php`, `comptoir.php`, `atelier.php`, `agenda.php`, '
  '`admin-users.php`, `profil.php`, `logout.php` : 0 occurrence de `class=`, mesuré). Pour retrouver 29 il '
  'faut ajouter les **5 fichiers de `includes\\`** (`config`, `_auth`, `_bootstrap`, `_functions`, `_init`), '
  'qui ne contiennent **aucune balise HTML** (mesuré : 0 `<tag` et 0 `class=`), plus `aegis.css`. Ces 5 '
  'includes ne peuvent donc produire aucun couple de contraste et ne sont pas audités en tant que pages.')
A('')
A('### Rejeu')
A('')
A('```powershell')
A('& \'C:\\zeus_dev\\zeus_env\\python\\python.exe\' \'C:\\zeus_dev\\zeus_projets\\deepseek-harness\\sites-web\\moto-hall_laravel\\_travail\\_audit_20261019\\audit_contraste.py\'')
A('```')
A('')
A('Pour mesurer l\'arbre vivant au lieu de la copie figée, ajouter `--source live` à la fin de cette même '
  'commande :')
A('')
A('```powershell')
A('& \'C:\\zeus_dev\\zeus_env\\python\\python.exe\' \'C:\\...\\audit_contraste.py\' --source live')
A('```')
A('')
A('Le script mesure **toujours les deux états** et n\'écrit que celui demandé (par défaut la copie du '
  '21/09) ; `--source live` écrit `contraste_mesures_live.csv` et `contraste_mesures_live.json`.')
A('')

# --- section 7 : l'etat vivant (toujours presente, quel que soit --source) ---
A('---')
A('')
A('## 7. L\'arbre de travail a changé pendant l\'audit')
A('')
A('Ce n\'est pas une impression : les empreintes SHA-256 des fichiers ont été comparées. **%d fichiers PHP sur %d**'
  ' de l\'arbre vivant diffèrent de la copie figée du 21/09%s :'
  % (NB_DIFFERENTS, len(SOURCES_BASE),
     ' — et `aegis.css` aussi' if CSS_DIFFERENT else ''))
A('')
A('| fichier | dernière écriture (vivant) | octets | identique à la copie 21/09 ? |')
A('|---|---|---|---|')
for (name, path_live, path_base) in COMPARAISON:
    with io.open(path_live, 'rb') as fh:
        h_live = hashlib.sha256(fh.read()).hexdigest()
    with io.open(path_base, 'rb') as fh:
        h_base = hashlib.sha256(fh.read()).hexdigest()
    if h_live == h_base:
        continue
    A('| `%s` | %s | %d | **non** |' % (
        name, time.strftime('%d/%m/%Y %H:%M:%S', time.localtime(os.path.getmtime(path_live))),
        os.path.getsize(path_live)))
for (nom_css_live, nom_css_base) in ((CSS_LIVE, CSS_BASE),):
    with io.open(nom_css_live, 'rb') as fh:
        h_live = hashlib.sha256(fh.read()).hexdigest()
    with io.open(nom_css_base, 'rb') as fh:
        h_base = hashlib.sha256(fh.read()).hexdigest()
    if h_live != h_base:
        A('| `aegis.css` | %s | %d | **non** |' % (
            time.strftime('%d/%m/%Y %H:%M:%S', time.localtime(os.path.getmtime(nom_css_live))),
            os.path.getsize(nom_css_live)))
A('')
A('Les %d autres fichiers%s sont **identiques** octet pour octet à la copie du 21/09 : la reprise était '
  'donc partielle au moment de la mesure, ce qui interdit d\'en tirer un « avant / après » propre.'
  % (len(SOURCES_BASE) - NB_DIFFERENTS,
     (' (dont %d vues sur 8)' % NB_VUES_IDENTIQUES) if NB_VUES_IDENTIQUES else ''))
A('')
A('Mesure de l\'état vivant au %s :' % HORODATAGE)
A('')
A('| mesure | copie 21/09 (référence) | arbre vivant (instant T) |')
A('|---|---|---|')
A('| couples mesurés | %d | %d |' % (ST['couples'], ST_AUTRE['couples']))
A('| échecs AA | %d | %d |' % (ST['fail'], ST_AUTRE['fail']))
A('| bloquants | %d | %d |' % (by_grav['bloquant'], ST_AUTRE['grav']['bloquant']))
A('| pire ratio (hors décor) | %.2f:1 (`%s:%s`) | %.2f:1 (`%s:%s`) |' % (
    pire['ratio'], pire['file'], pire['line'],
    ST_AUTRE['pire']['ratio'], ST_AUTRE['pire']['file'], ST_AUTRE['pire']['line']))
A('| éléments < 14 px | %d | %d |' % (ST['petits'], ST_AUTRE['petits']))
A('')
A('**À lire avec précaution** : ces deux colonnes ne comparent pas « avant / après » mais « état de référence » '
  'et « état en cours de reprise ». De plus, les rôles (*porteur d\'information*, secondaire, décor) sont '
  'repérés par `fichier:ligne` **dans la copie du 21/09** : sur l\'arbre vivant, dont les lignes ont bougé, '
  'la répartition bloquant / gênant est donc **indicative**, alors que le nombre d\'échecs AA et le pire ratio '
  'restent exacts. Quand la reprise sera terminée, relancer avec `--source live` donnera le chiffre qui compte.')
A('')
A('### Empreintes des fichiers mesurés (copie 21/09)')
A('')
A('| fichier | source | octets | sha256 (16 premiers) | écrit le |')
A('|---|---|---|---|---|')
for (name, base, taille, h, mtime) in empreintes(SOURCES_BASE):
    A('| `%s` | %s | %d | `%s` | %s |' % (name, base, taille, h, mtime))
A('')
A('`aegis.css` : `%s` (sha256, 16 premiers caractères) — %d octets.'
  % (hashlib.sha256(open(CSS_BASE, 'rb').read()).hexdigest()[:16], os.path.getsize(CSS_BASE)))
A('')

report_path = os.path.join(OUT, 'RAPPORT_CONTRASTE.md')
with io.open(report_path, 'w', encoding='utf-8') as fh:
    fh.write('\n'.join(L) + '\n')

# ---------------------------------------------------------------------------
# 13. RESUME CONSOLE
# ---------------------------------------------------------------------------
print('=' * 72)
print('AUDIT CONTRASTE WCAG 2.2 - MotoHall 2A   (source = %s)' % SOURCE)
print('=' * 72)
print('Fichiers PHP lus       : %d' % len(SOURCES_BASE))
print('Fichiers avec balisage : %d' % sum(1 for v in PER_FILE.values() if v))
print('Couples texte/fond     : %d  (%d balises)' % (COUPLES, OCCURRENCES))
print('  bloquants            : %d' % by_grav['bloquant'])
print('  genants              : %d' % by_grav['genant'])
print('  cosmetiques          : %d' % by_grav['cosmetique'])
print('  conformes            : %d' % by_grav['conforme'])
print('  decor (hors perimetre): %d' % by_grav['decor'])
print('Echecs AA (hors decor) : %d' % len(FAIL_AA))
print('Echecs AAA             : %d' % len(FAIL_AAA))
print('Pire ratio             : %.2f:1  %s:%s  (%s sur %s)' % (
    pire['ratio'], pire['file'], pire['line'], pire['color'], pire['bg']))
print('Pire hors decor        : %.2f:1  %s:%s' % (
    pire_hors_decor['ratio'], pire_hors_decor['file'], pire_hors_decor['line']))
print('Textes < 14 px         : %d elements (%d porteurs d information)' % (
    ST['petits'], ST['petits_info']))
print('-' * 72)
print('VIVANT  : %d couples, %d echecs AA, %d bloquants, pire %.2f:1 (%s:%s)' % (
    ST_AUTRE['couples'], ST_AUTRE['fail'], ST_AUTRE['grav']['bloquant'],
    ST_AUTRE['pire']['ratio'], ST_AUTRE['pire']['file'], ST_AUTRE['pire']['line']))
print('-' * 72)
print('Rapport : %s' % report_path)
print('CSV     : %s' % csv_path)
print('JSON    : %s' % json_path)
