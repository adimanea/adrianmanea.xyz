# COGA / Accessibility Audit — adrianmanea.xyz

**Scope:** Hugo site (`hugo.toml`, theme `hugo-book` + overrides in `layouts/` and `assets/`),
content in `content/ro` and `content/en`, static assets in `static/` (CV PDFs, images, self-hosted fonts).
**Focus:** cognitive accessibility (COGA — [W3C "Making Content Usable for People with Cognitive and
Learning Disabilities](https://www.w3.org/TR/coga-usable/) + WCAG 2.2 cognitive success criteria:
2.4.11, 2.5.7, 2.5.8, 3.2.6, 3.3.7, 3.3.8, plus 1.4.10, 1.4.12, 2.2.1/2.2.2, 3.3.1–3.3.6) and the
WCAG 2.2 A/AA baseline they sit on.
**Environment:** openSUSE Tumbleweed KDE (Linux). Tool notes flag anything that is **not** available on Linux.

Verified against the repo on the audit date: `public/` build has `<html lang="ro|en">` (good), **no skip
link**, GA loaded in `<head>` on every page, CV PDFs are **untagged** (`pdfinfo`: `Tagged: no`, empty `Title`).

---

## 1. Urgent (blocks a whole group of users or creates legal exposure)

### U1. Google Analytics fires on every page without consent — legal risk (ePrivacy/GDPR), and no `[privacy.googleAnalytics]` config
- `layouts/partials/docs/inject/head.html` calls `{{ template "_internal/google_analytics.html" . }}`
  unconditionally; `hugo.toml` has `[services.googleAnalytics] id = "G-3VT5LTHWW6"` but **no**
  `[privacy.googleAnalytics]` section, so `anonymizeIP` is off and Do-Not-Track is ignored.
- There is **no cookie banner at all** on this site, so there is no consent mechanism whatsoever.
- **Fix:** either (a) add `[privacy.googleAnalytics] anonymizeIP = true` + `respectDoNotTrack = true`
  and load GA only after an explicit consent choice (dynamic `gtag` injection, like a minimal banner),
  or (b) replace GA with consent-free, self-hosted analytics (Plausible/Umami) or drop it. Whichever you
  choose, it must match what a privacy page says — add a short privacy note.

### U2. No skip-to-content link (WCAG 2.4.1, A)
- The theme's `themes/hugo-book/layouts/baseof.html` has no skip link; keyboard users must tab through the
  whole sidebar menu on **every** page. (Verified: zero "skip" elements in the built `public/`.)
- **Fix:** override the theme — copy `themes/hugo-book/layouts/baseof.html` to `layouts/baseof.html` and add,
  as the first element in `<body>`:
  `<a href="#book-page-content" class="skip-link">Sari la conținut / Skip to content</a>`
  plus `id="book-page-content" tabindex="-1"` on the `.book-page` div, and CSS making `.skip-link`
  visually hidden until `:focus-visible`.

### U3. Menu/toc toggle checkboxes have no usable focus indicator (WCAG 2.4.7, AA; 2.4.11 AA)
- `baseof.html` uses `<input type="checkbox" class="hidden toggle" id="menu-control">`; the theme's
  `_main.scss` rule `input.toggle:focus-visible + label` never matches (the next sibling is another input,
  not the label). Keyboard users lose focus on the menu button.
- **Fix:** in `assets/_custom.scss` add a working selector, e.g.
  `input.toggle:focus-visible ~ .book-page label.book-menu-overlay, input#menu-control:focus-visible + input + main label[for="menu-control"] { outline: 2px solid var(--teal); outline-offset: 2px; }`
  — or simpler: override `baseof.html` (see U2) and replace the hidden checkboxes with real
  `<button aria-expanded>` controls.

### U4. Untagged, title-less CV PDFs (WCAG 1.1.1 / 1.3.1 / 3.1.1 in documents; PDF/UA)
- `static/docs/cv-manea-ro.pdf` and `cv-manea-eng.pdf`: `pdfinfo` reports `Tagged: no`, `Title:` empty,
  PDF 1.5. Linked from `content/ro/cercetare/_index.md`.
- **Fix:** regenerate the CVs as tagged PDF/UA-1 (see `materiale_coga.md` in `materiale_edu` for the
  LaTeX recipe, or export from LibreOffice with "Tagged PDF" + document title/language set), or replace
  the PDF link with an HTML CV page. Verify with veraPDF (Linux OK) and `pdfinfo`.

### U5. Math is client-side-only and partially unrendered; no reflow handling for display math
- `BookKatex = true`; the theme injects self-hosted KaTeX + `assets/katex.json` (good: no CDN). But
  `katex.json` defines only `$$…$$`, `\(…\)`, `\[…\]` — **not** the inline `$…$` delimiter that
  `hugo.toml`'s goldmark passthrough config also declares, so inline math written with `$…$` is not
  rendered. KaTeX default output is `htmlAndMathml` (MathML reaches screen readers — keep it that way;
  do **not** set `output: "html"`).
- Display math (`$$…$$`) has no `overflow-x: auto` wrapper → wide formulas force horizontal page scroll
  on mobile (WCAG 1.4.10 Reflow, AA).
- **Fix:** add `{"left":"$","right":"$","display":false}` to `katex.json`, keep `output: "htmlAndMathml"`,
  and add CSS: `.katex-display { overflow-x: auto; overflow-y: hidden; padding-bottom: 0.3em; }`.

### U6. Theme is forced to light, ignoring the OS dark preference; toggle has no accessible state
- `layouts/partials/docs/inject/head.html` runs `localStorage.getItem('theme') || 'light'` before paint;
  `assets/_custom.scss` has `html[data-theme="light"] { … !important }` blocks that defeat the
  `@media (prefers-color-scheme: dark)` rules. Users with dark-mode OS (common with low vision / light
  sensitivity) get light mode by default.
- The theme button in `menu-after.html` swaps emoji+text but has **no `aria-pressed`**.
- **Fix:** default to `window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'`
  when no stored choice; add `aria-pressed` to `#theme-toggle` and update it in `updateButtonText()`;
  remove the `!important` overrides or scope them so the media query wins.

---

## 2. Medium priority (WCAG AA failures / clear COGA issues)

### M1. Vague link text "aici" / "here" (WCAG 2.4.4, A)
- ~10 links in `content/ro/publicatii/journalism/_index.md` and `content/ro/cercetare/_index.md` and
  `content/en/research/_index.md` use `[aici]`/`[here]`. Screen-reader users navigating by links hear
  only "aici, link".
- **Fix:** rewrite as descriptive text, e.g. `[Toate articolele mele din Școala9](…)`.

### M2. Language switcher uses flag emoji with no accessible name (WCAG 4.1.2 / 3.1.2 spirit)
- `menu-after.html` renders `🇬🇧 English` / `🇷🇴 Română`. Screen readers announce "regional indicator
  G B…"; Windows renders flags as plain letters. **Fix:** keep the text label, wrap the flag in
  `<span aria-hidden="true">`, and add `hreflang`/`lang` attributes plus `aria-label` on the link.

### M3. Contact page hides email/phone as obfuscated text — bad for COGA (clear contact = 3.2.6 Consistent Help)
- `content/{ro,en}/contact/_index.md` show `` `adrian [at] adrianmanea [dot] xyz` `` and a spelled-out
  phone number. Screen readers read the brackets; copy-paste fails; nobody can just tap it.
- **Fix:** use real links — `<a href="mailto:adrian@adrianmanea.xyz">adrian@adrianmanea.xyz</a>` and
  `<a href="tel:+40750408128">+40 750 408 128</a>` (keep the obfuscation only if you get spam; a
  `mailto:` is far more usable). Keep the **same contact block in the same menu position on both
  languages** (you already do — good, that is exactly 3.2.6).

### M4. Missing `description` front matter on section pages → garbage `og:description`/meta description
- Only `_index.md` files set `description`; section pages (`poligon`, `cercetare`, `didactica`, …) fall
  back to `.Summary` which can contain raw markup. **Fix:** add a one-sentence `description` to every
  `_index.md`.

### M5. No `prefers-reduced-motion` handling anywhere (WCAG 2.2.2-adjacent, COGA)
- Theme + custom SCSS have zero `@media (prefers-reduced-motion: reduce)` rules (menu transitions, hover
  transforms, KaTeX none). **Fix:** add a global block disabling transitions/animations.

### M6. Landmarks and current page not announced
- Three `<nav>`-like regions (sidebar menu, ToC, language) have no distinguishing `aria-label`; the active
  sidebar link has class `.active` but no `aria-current="page"`. **Fix:** `aria-label="Meniu principal"`
  / `aria-label="Cuprins"` etc. in the overridden `baseof.html`/partials; add `aria-current="page"` in
  `menu-filetree.html` override.

### M7. Raw `<img>` in Markdown forces `unsafe = true`
- `content/{ro,en}/_index.md` use `<img class="menu-hidden-only" src="/images/profile.jpg" alt="Adrian Manea">`.
  It has alt (good), but raw HTML in Markdown requires `markup.goldmark.renderer.unsafe = true`, which lets
  any future raw HTML through unchecked. **Fix:** prefer Markdown image syntax `![Adrian Manea](/images/profile.jpg)`
  and move `menu-hidden-only` into the theme's `render-image.html` override; keep `unsafe = true` only if needed
  (you also use inline HTML spans nowhere else, so try turning it off).

### M8. COGA content patterns: long uninterrupted prose, no summaries
- Pages are single long paragraphs (e.g. `ro/poligon/_index.md`). COGA guidance: give a short
  summary/key-points at the top of long pages, keep one idea per paragraph, define jargon on first use,
  avoid idioms. **Fix:** add a 2–3 bullet "Pe scurt / TL;DR" box at the top of each long section page
  (the theme has `{{< hint >}}`/`box` shortcodes); run a plain-language pass (see tools below).

---

## 3. Low priority (polish)

- **L1.** `static/` has only `favicon.png`, but the built site references `favicon.svg` too — add
  `static/favicon.svg`, `apple-touch-icon.png`, and a `<meta name="theme-color">` matching both themes.
- **L2.** Self-hosted woff2 fonts with `font-display: swap` — good; add `<link rel="preload">` for the
  main serif latin-ext subset so Romanian diacritics don't flash in fallback font.
- **L3.** Emoji in the theme-toggle button (🌙/☀️) — wrap in `aria-hidden` spans (the text label already
  says what it does).
- **L4.** `BookSearch = false` — if you enable search later, override `docs/search.html` to add
  `role="status" aria-live="polite"` on `#book-search-results` (the theme doesn't).
- **L5.** Check the `404.html` and print stylesheet (`_print.scss`) once — print styles matter for a
  writer's site.
- **L6.** `sw.js` service worker — fine for offline, but make sure it doesn't serve stale CSS after theme
  changes (version the cache key on deploy).

---

## 4. Step-by-step TODO (ordered by priority, then by complexity) with tools

Tools on **openSUSE Tumbleweed (Linux)** — install with `zypper install …` / `npm install -g …` unless noted:

| Tool | Purpose | Linux? |
|---|---|---|
| `hugo` (extended not needed), `dart-sass` | build the site | yes (`zypper install hugo`; dart-sass binary from GitHub releases — the npm `sass` does **not** work with Hugo's `css.Sass`) |
| Firefox **Accessibility Inspector** (F12 → Accessibility) or **axe DevTools** / **WAVE** browser extensions | find a11y issues in the real DOM | yes, in Firefox/Chrome/Edge on Linux |
| `pa11y-ci` + `axe-core` (`npm i -g pa11y-ci`) | automated CI scan against `hugo server` | yes (Node) |
| `vnu.jar` (W3C Nu validator, Java) or `html-validate` (npm) | HTML validation of built `public/` | yes (`zypper install java-17-openjdk`; `wget https://github.com/validator/validator/releases/download/latest/vnu.jar`) |
| `markdownlint-cli` (`npm i -g markdownlint-cli`) | lint Markdown (rule MD045 = image alt) | yes |
| `lychee` (`cargo install lychee` or binary) | check links incl. `ref`/`relref` after build | yes |
| Lighthouse (Chrome DevTools or `lighthouse` npm CLI) | a11y + perf score | yes |
| **Orca** screen reader (`zypper install orca speech-dispatcher-espeak`) + Firefox | manual screen-reader pass — the essential COGA check | **Linux-native** (NVDA/JAWS are Windows-only; VoiceOver is macOS-only) |
| KDE **Contrast** widget or WebAIM Contrast Checker (web) | measure color contrast | yes (Colour Contrast Analyser app is Windows/macOS-only — do not rely on it) |
| LanguageTool CLI/desktop or `proselint`/`vale` | plain-language / readability pass on prose (COGA) | yes (LanguageTool has a Linux desktop app and CLI) |
| `poppler-utils` (`pdfinfo`, `pdffonts`, `pdftotext`), `qpdf`, `mutool` | inspect PDFs (tagging, fonts, reading order) | yes (`zypper install poppler-tools qpdf mupdf-tools`) |
| **veraPDF** (Java GUI+CLI from verapdf.org) | validate PDF/UA-1 | yes. **PAC 2024 is Windows-only; Adobe Acrobat Pro is Win/mac-only** — on Linux use veraPDF + Orca in Evince/Okular |
| `typst` | if you later port notes (see `materiale_coga.md`) | yes (binary / `zypper install typst`) |

### TODO list

1. **Baseline scan (30 min).** `zypper install hugo`; `hugo server` → scan `http://localhost:1313` (ro + en)
   with axe DevTools and WAVE; run `npx pa11y-ci http://localhost:1313/` and save reports. Run
   `java -jar vnu.jar public/` after `hugo --minify`. Fix everything reported at level A/AA first.
2. **Kill the consent violation (U1).** Add to `hugo.toml`:
   ```toml
   [privacy.googleAnalytics]
   anonymizeIP = true
   respectDoNotTrack = true
   ```
   and gate the GA tag: remove it from `inject/head.html`, add a small consent banner in
   `inject/body.html` (copy the pattern from `poligon-hugo`, but actually **delay** the `gtag` load until
   "Accept"), or switch to self-hosted Plausible/Umami. Add a short privacy page and link it in the footer.
3. **Skip link + landmarks (U2, U3, M6).** Copy `themes/hugo-book/layouts/baseof.html` → `layouts/baseof.html`;
   add the skip link (U2), `aria-label`s on the nav regions, `aria-current` support; fix the toggle focus
   ring in `assets/_custom.scss` (U3).
4. **Theme & language toggles (U6, M2).** Edit `layouts/partials/docs/inject/head.html`
   (`matchMedia` default) and `menu-after.html` (`aria-pressed`, `aria-hidden` emoji, text labels,
   `hreflang`). Remove the `!important` theme blocks in `_custom.scss` that override `prefers-color-scheme`.
5. **Math (U5).** Edit `themes/hugo-book/assets/katex.json` (add inline `$…$`, keep `htmlAndMathml` —
   override via `assets/katex.json` in your repo so theme updates don't clobber it); add
   `.katex-display { overflow-x:auto }` to `assets/_custom.scss`. Verify with Orca that formulas are
   announced (MathML) and not double-read.
6. **Link text + contact (M1, M3).** Rewrite `[aici]`/`[here]` links in `content/ro/publicatii/journalism/_index.md`,
   `content/ro/cercetare/_index.md`, `content/en/research/_index.md`; replace obfuscated contact text with
   `mailto:`/`tel:` links in both `contact/_index.md` files. Add `markdownlint` MD045 + a custom
   "no bare 'aici/here' link" grep to CI.
7. **PDFs (U4).** Regenerate both CV PDFs as tagged PDF/UA-1 (or link an HTML CV); verify:
   `pdfinfo static/docs/cv-manea-eng.pdf | grep -i 'tagged\|title\|lang'` → expect `Tagged: yes`;
   run veraPDF with the PDF/UA-1 profile; read it with Orca in Evince. Put the check in CI (GitHub Actions,
   `ubuntu-latest` has Java).
8. **Meta descriptions (M4).** Add `description = "…"` to every `_index.md`.
9. **Reduced motion + focus (M5, U3).** Add the global `@media (prefers-reduced-motion: reduce)` block;
   keyboard-tab the whole site and confirm every interactive element is reachable, visible on focus
   (2.4.7/2.4.11) and ≥24×24 px target (2.5.8) — the sidebar toggle labels especially.
10. **Content COGA pass (M8).** Add a short "Pe scurt / In short" bullet box at the top of long pages;
    run LanguageTool (ro+en) and a readability check on `content/**`; define jargon on first use.
11. **CI wiring.** `.github/workflows/` already exists — add a job: `hugo --minify` → `vnu.jar` →
    `pa11y-ci` → `lychee` → `markdownlint` → veraPDF on `static/docs/*.pdf`. Fail the build on errors.
12. **Re-audit.** Re-run axe/WAVE/Lighthouse + an Orca pass (headings list, links list, landmarks) on the
    production URL after deploy; record results at the bottom of this file.

### Acceptance checks (what "done" means)
- axe/WAVE/pa11y report **zero** A/AA violations on ro + en home, a section page, the journalism page, contact.
- Keyboard-only: skip link appears on first Tab and jumps to content; all menus/toggles operable with visible focus.
- Orca: one `<h1>` per page, headings outline sensible, links list is descriptive, math announced via MathML, PDF CVs tagged (`Tagged: yes`) and read in order.
- GA network requests fire **only** after consent (check DevTools Network with consent declined).
- `prefers-reduced-motion: reduce` and OS dark mode are honored without a stored choice.
