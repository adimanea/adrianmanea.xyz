# COGA / Accessibility Audit — adrianmanea.xyz

[TOC]

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

---

## Final Automated and Manual Checks

Yes. There's a well-established stack for this, and on openSUSE Tumbleweed almost all of it runs natively. Here's the standard toolchain, what "all green" means, and exact commands.

### The standards you're checking against

| Target | What it is | Tool that checks it |
|---|---|---|
| **WCAG 2.2 AA** (web) | The W3C standard; includes the COGA-relevant SCs 2.4.11, 2.5.7, 2.5.8, 3.2.6, 3.3.7, 3.3.8 | axe-core, HTML_CodeSniffer, IBM Equal Access, WAVE, Lighthouse |
| Valid HTML5 | prerequisite for everything else | W3C Nu validator (`vnu.jar`) |
| **PDF/UA-1** (ISO 14289) for your PDFs | Tagged PDF standard | **veraPDF** (the reference validator) |
| EN 301 549 / EAA | The EU procurement standard (in force since June 2025) — relevant if you sell courses to schools/NGOs; it references WCAG. You can produce a **VPAT/ACR** from your test reports | VPAT template (ITI) + your axe/veraPDF reports |

### Install on openSUSE Tumbleweed

```bash
sudo zypper install hugo nodejs20 npm java-21-openjdk poppler-utils qpdf mupdf-tools \
  orca speech-dispatcher-espeak lychee          # lychee falls back to: cargo install lychee
npm install -g pa11y-ci axe-core html-validate markdownlint-cli2 lighthouse @lhci/cli
# veraPDF (Java, runs fine on Linux): download GUI+CLI from https://verapdf.org/downloads
# axe DevTools / WAVE / IBM Equal Access extensions: install in Firefox or Chrome on Linux
```

**Not available on Linux** (flagging per your request): NVDA & JAWS (Windows-only screen readers → use **Orca**), PAC 2024 and Adobe Acrobat Pro's accessibility checker (Windows / Win+mac), Colour Contrast Analyser (Win/mac → use KDE's Contrast widget or WebAIM's web checker), Accessibility Insights desktop app (Windows; its *web extension* works in Linux browsers).

### The "all green" automated pipeline (per Hugo site)

1. **Build:** `hugo --minify --gc` → `public/`
2. **HTML validation — must be 0 errors:** `java -jar vnu.jar public/` (download `vnu.jar` from github.com/validator/validator releases)
3. **Automated accessibility — must be 0 issues at WCAG2AA:** `pa11y-ci` with a `.pa11yci.json` at the repo root:
   ```json
   { "defaults": { "standard": "WCAG2AA", "runners": ["axe", "htmlcs"], "threshold": 0,
                   "chromeLaunchConfig": { "args": ["--no-sandbox"] } },
     "urls": ["http://localhost:1313/", "http://localhost:1313/en/",
              "http://localhost:1313/materiale/ecuatia-dreptei/", "http://localhost:1313/contact/"] }
   ```
   Run `hugo server` in one terminal, `pa11y-ci` in another. `threshold: 0` = any violation fails.
4. **Lighthouse CI** (`lhci autorun` with `lighthouserc.js`, budget: accessibility category = 100 on key pages) — catches things axe misses (target size, color contrast, tap targets).
5. **Extra rule coverage:** IBM Equal Access Accessibility Checker — browser extension for manual passes, or Node CLI `@ibm/iba-ace-cli` in CI (`iba-ace-cli --ruleset latest ...`). Use it alongside axe, not instead.
6. **Links:** `lychee public/` (plus `hugo --printPathWarnings` for broken `ref`/`relref`).
7. **Content lint:** `markdownlint-cli2 "content/**/*.md"` with MD045 (image alt) enabled + a custom grep for bare `[aici]`/`[here]` link text and double `^# ` headings.
8. **PDFs:** `verapdf --flavour ua1 static/documents/*.pdf` → 0 failed rules; plus `pdfinfo f.pdf | grep -E 'Tagged|Lang|Title'` → `Tagged: yes`. Same for `materiale_edu/*/tex/build/*.pdf`.
9. **CI:** one GitHub Actions workflow (`.github/workflows/a11y.yml`) running steps 1–8 on every push; green = build passes. Ubuntu runners have Java and Node, so vnu/veraPDF/pa11y-ci all work there.

### What automation can't cover (the COGA part — do these manually, they're the point of your specialization)

Automated tools catch only ~30–40% of WCAG issues, and almost none of the COGA ones. The manual gates that "all green" must also include:

- **Keyboard-only pass**: Tab from page load — skip link appears first, every control reachable with a *visible* focus ring (2.4.7/2.4.11), carousel pausable, lightbox focus-trapped, no traps.
- **Screen-reader pass with Orca** (Firefox; Evince/Okular for PDFs): one h1 per page, sensible headings/links/landmarks lists, math announced via MathML, PDFs read in order with bookmarks.
- **axe DevTools / WAVE** spot-checks in the browser on the live site (they see the real DOM, unlike static scans).
- **Contrast**: axe/Lighthouse + KDE Contrast on your palette tokens (≥ 4.5:1 text, ≥ 3:1 UI).
- **Media/user-preference emulation** in DevTools: `prefers-reduced-motion: reduce` (carousel stops), `prefers-color-scheme: dark` (respected), forced-colors mode, and the **WCAG 1.4.12 text-spacing bookmarklet** (from webstandards.org) — all must keep the site usable.
- **Plain-language/readability** (COGA core): LanguageTool CLI for Romanian prose, `vale`/`proselint` for English, plus a reading-level check (e.g. `textstat`).
- **Cognitive walkthrough**: can a first-time visitor find contact/prices in ≤ 3 clicks from any page (3.2.6 consistent help), is help in the same place on every page, no time limits, no CAPTCHA-style auth (3.3.8), no re-asking for info (3.3.7)?
- Optionally, real **user testing** with people with cognitive disabilities — COGA's own guidance says this is the only way to validate the cognitive aspects.

So: automated tools give you the green CI badge for WCAG 2.2 AA + PDF/UA-1; the manual Orca/keyboard/readability gates are what make it genuinely COGA-compliant — and they're also your portfolio evidence.

---

## Apple / WebKit-Specific Checks (iPhone, iPad, macOS Safari)

Yes — and it's worth a dedicated layer in your audits, because in education Apple devices are over-represented and WebKit has its own quirks. Here's the full picture.

### 1. Why the Apple world is a separate axis for you

- **Every browser on iPhone/iPad is WebKit.** Chrome, Firefox, Edge on iOS are all WKWebView skins — Apple mandates the engine. So "test Safari on iOS" = "test all iOS browsers."
- **iPads request desktop-class sites by default** (since iPadOS 13), but with a touch screen and ~1024 px width — you get your desktop layout with no hover. Test both the desktop and mobile breakpoints on iPad, plus Split View / Slide Over (~700 px) and iPhone SE (320 px, the WCAG 1.4.10 reflow test width).
- **VoiceOver is the reference screen reader for WebKit** (macOS, iOS/iPadOS, visionOS). Safari + VoiceOver has **best-in-class MathML support** — your KaTeX `htmlAndMathml` output and the MathML on poligon-edu.ro will actually be read aloud properly on Apple devices. That's a genuine asset for your math materials.
- Apple's own bar is stricter than WCAG in one place: the **HIG requires 44×44 pt minimum tap targets** (WCAG 2.5.8 asks 24×24 CSS px). Since you target classrooms, follow Apple's 44 pt.

### 2. Concrete findings in your repos already

- Viewport meta is fine: both themes use `width=device-width, initial-scale=1.0` — **no `user-scalable=no`**, so pinch-zoom is not blocked (never add it; disabling zoom is an automatic WCAG 1.4.4/1.4.10 failure).
- **Real issue found:** `assets/_custom.scss` in *both* sites uses `height: 100vh !important` (adrianmanea.xyz:525; poligon-hugo:579, and the lightbox overlay `width:100vw; height:100vh` at poligon-hugo:639). On iOS Safari, `100vh` includes the area behind the collapsing toolbar → content is hidden under it. Fix: `height: 100vh; height: 100dvh;` (and `dvw` for the overlay), keeping the vh fallback.
- The theme menu uses `&:hover > ul, &:focus-within > ul` — good, it already pairs hover with focus-within. Keep that pattern everywhere: on touch devices hover doesn't exist, so anything revealed on hover must also work on tap/focus.

### 3. Apple-exclusive tools (all macOS-only — none run on Linux, and you can't legally VM macOS on Linux hardware)

| Tool | What it's for |
|---|---|
| **Xcode** (free, Mac App Store) → **Simulator** | Run iPhone/iPad simulators; test at real device widths |
| **Accessibility Inspector** (Xcode → Open Developer Tool) | Inspect the accessibility tree of any app/page; has a built-in **Audit** that flags WCAG-style issues (contrast, missing labels, small targets) |
| **Safari Web Inspector** (Develop → Show Web Inspector, enable in Settings → Advanced) | **Accessibility pane** = the a11y tree in real Safari; **Audit tab** = Lighthouse-derived automated checks running *in WebKit itself* |
| **VoiceOver** (macOS: Cmd+F5; iOS: Settings → Accessibility → VoiceOver) + **VoiceOver Utility** | The screen reader; use the **rotor** (VO+U → Headings / Links / Landmarks) to verify your heading tree, link names, skip link, carousel and lightbox announcements |
| **Safari Technology Preview** | Early WebKit — catches engine changes before they hit students' devices |
| iOS Settings → Accessibility: **Zoom, Reduce Motion, Increase Contrast, Differentiate Without Color, On/Off Labels, Reduce Transparency, Dynamic Type (text size), Voice Control, Switch Control, Guided Access** | Each maps to a web platform feature your CSS/JS must honor (see §5) |
| **bugs.webkit.org** | File engine bugs you find (e.g., a MathML or `aria-roledescription` announcement issue) |
| **wpt.fyi** | Web Platform Tests dashboard — check whether a CSS/JS feature you use is green in Safari before relying on it |

Apple guidance to track: **Human Interface Guidelines → Accessibility** (developer.apple.com/design/human-interface-guidelines/accessibility) and the **Apple Developer Accessibility** docs; WWDC "What's new in Safari and WebKit" sessions each June.

### 4. Automated WebKit checks you can run from your Linux machine

1. **GitHub Actions `macos-latest` runner** — free for public repos (both your Hugo repos are public). Real WebKit in CI:
   - Playwright with `--project=webkit` on the macOS runner (real WebKit build), inject **axe-core**, assert zero WCAG2AA violations at iPhone/iPad/macOS viewports; emulate `reducedMotion: 'reduce'`, `colorScheme: 'dark'`, `hasTouch: true` (catches hover-only UI).
   - Or real Safari via `safaridriver` (`sudo safaridriver --enable` works on Actions runners) + Selenium/WebDriver + axe.
   - Playwright's WebKit also has a Linux build — an approximation you can run locally, but only the macOS runner is the genuine engine.
2. **Cloud device labs** (trigger from Linux CI or manually): **BrowserStack** (has a free tier for open-source projects), **Sauce Labs**, **LambdaTest/TestMu AI** — real Safari on macOS and real iPhones/iPads; run axe via their Automate; useful for one-off manual sessions too since you have no Mac.
3. **In-browser on a Mac**: axe DevTools extension for Safari, Safari Web Inspector's Audit tab, Accessibility Inspector's Audit.
4. Platform-independent checks from before (vnu.jar, lychee, markdownlint, veraPDF for PDFs — Evince/Okular on Linux, Preview + VoiceOver on macOS) stay valid; PDFs on iPad open in Books/Files — tagged PDF/UA matters there too.

### 5. Manual checks to perform on Apple devices (the ones that matter for your sites)

- **VoiceOver + Safari (macOS and iPhone)**: rotor → Headings shows exactly one h1 and a clean outline per page (fixes the multi-h1 material pages first); Links list must be descriptive (no "aici/here"); Landmarks list shows menu/main/ToC with labels; skip link is the first stop; carousel announces as carousel with slide position and is pausable; lightbox opens as a modal dialog and focus returns on close; **math is spoken from MathML** (verify a formula on `ecuatia-dreptei` — Safari+VO is the best combo for this); emoji flags/🌙 announce sanely or are aria-hidden.
- **iOS touch**: every target ≥ 44×44 pt (WCAG 2.5.8's 24 px is the floor — Apple's is the one to satisfy in classrooms); nothing is hover-only; the sidebar menu opens/closes by tap with a visible focus state; no horizontal page scroll at 320 px (the `100dvh` fix + `overflow-x:auto` on `.katex-display`).
- **Display accommodations**: Settings → Accessibility → **Reduce Motion** → `prefers-reduced-motion` must stop the poligon carousel; **Increase Contrast** and **Differentiate Without Color** → verify meaning survives without color (your LaTeX/figure color-coding fix matters here; on web, links must be underlined or otherwise non-color-only); **On/Off Labels** → switches get text; **Dynamic Type** to ~310% (≈ WCAG 1.4.4's 200%) → no clipped text in cards/captions; **dark mode** → `prefers-color-scheme: dark` respected with no stored choice; pinch zoom works (viewport already OK).
- **Voice Control** (iOS/macOS "click" voice commands) uses accessible names — so your accessible-name fixes (aria-labels on icon buttons, real link text) directly improve Voice Control accuracy.
- **Switch Control** (motor accessibility, common in special education): sequential scanning must reach every control — another reason the carousel dots must be real buttons and the lightbox keyboard-operable.
- **iPad multitasking**: Split View (~744 px) and Slide Over — re-run the reflow and menu checks at those widths.
- **visionOS (Vision Pro)**: Safari there renders standard web content — worth one pass in the simulator if you ever get access.

### 6. Apple-specific metadata/quality touches for your two sites

- `apple-touch-icon.png` (180×180) — poligon-hugo has it; **adrianmanea.xyz only ships `favicon.png`** → add `static/apple-touch-icon.png` + the link tags (the pattern already exists in poligon's `inject/head.html`).
- `<meta name="theme-color">` with media-specific dark value (your existing dark mismatch note) — this colors the iOS Safari toolbar.
- `apple-mobile-web-app-capable` / `mobile-web-app-capable` if you ever want "Add to Home Screen" to feel app-like; semantic HTML also powers iOS **Reader Mode** — another reason single-h1 + clean headings matter.
- Keep an eye on **Safari Technology Preview** release notes for engine changes affecting `:focus-visible` (supported since 15.4), `<dialog>`, `aria-roledescription` announcements, service workers.

### Suggested device matrix to keep green

iPhone SE (320 px) · iPhone 15/16 · iPad (desktop-class 1024 px) · iPad Split View (~744 px) · macOS Safari stable · Safari Technology Preview · CI: Playwright WebKit on `macos-latest` + axe (0 violations) · optional BrowserStack real-device pass per release.

---

## Linux-Specific Checks (openSUSE Tumbleweed KDE and other distros)

### 1. Why Linux is its own axis

- It's your development platform **and** a common platform in schools/institutions (especially in the EU), and the whole free accessibility stack is native here — no Mac or Windows machine needed for the core passes.
- **Orca** is the reference free screen reader on Linux (GNOME; also runs on KDE Plasma via the AT-SPI2 bridge). NVDA/JAWS/VoiceOver do not exist here — see the Windows/Apple sections for those.

### 2. Linux-native tools

| Tool | Install / where | What it's for |
|---|---|---|
| **Orca** + speech-dispatcher | `sudo zypper install orca speech-dispatcher-espeak-ng` (voices: espeak-ng, festival; Orca modifier key = Insert or Caps Lock) | Screen reader for Firefox/Chromium and for PDFs in Evince/Okular; structural navigation + flat review (Orca key + arrows) to walk headings, links, landmarks, form fields |
| **Firefox DevTools → Accessibility pane** | Firefox on any distro; the **"Check for issues"** button runs axe-derived checks | A11y tree of the live page + automated issue check, all on Linux |
| **accerciser** | `sudo zypper install accerciser` | Inspect the AT-SPI accessibility tree — catches missing accessible names/roles on your custom controls (theme toggle, carousel dots, lightbox) |
| **dogtail / LDTP** | `pip install dogtail` | GUI test automation on Linux (can drive Firefox/Chromium in CI-style scripts) |
| **brltty** | `sudo zypper install brltty` | Braille display support for Orca |
| **KWin Zoom / GNOME Magnifier** | System Settings → Accessibility → Zoom | Screen magnification to ≥ 200% (WCAG 1.4.4) |
| **KDE "Contrast"** app / WebAIM web checker | KDE Plasma widget, or web | Contrast measurement on Linux (Colour Contrast Analyser is Win/mac-only) |
| **Evince** (Document Viewer) / **Okular** + Orca | `zypper install evince okular` | Read tagged PDFs aloud; `pdftotext -layout` for reading-order sanity |
| KDE/GNOME accessibility settings | System Settings → Accessibility | High-contrast theme, large cursor, sticky/slow/bounce keys (motor impairments), visual bell |

### 3. Automated checks — all native on Tumbleweed

- The entire pipeline from **Final Automated and Manual Checks** runs natively: `pa11y-ci` and Lighthouse need Chromium (`sudo zypper install chromium`), plus `vnu.jar` (Java), `lychee`, `markdownlint-cli2`, `html-validate`, `veraPDF`, `poppler-utils`.
- Playwright's **WebKit Linux build** is a local approximation of Safari's engine; real WebKit only exists on macOS (see the Apple section).
- CI: `ubuntu-latest` GitHub runners (free for public repos) run the same suite.

### 4. Manual checks on Linux

- **Orca + Firefox**: structural navigation to list headings/links/landmarks (expect one h1 per page, descriptive link names, labelled nav regions, working skip link); carousel announces and pauses; lightbox dialog focus-managed; **Firefox reads MathML natively with Orca** (no add-on needed — unlike NVDA+Chrome, see Windows section).
- **Orca + Evince/Okular**: tagged PDFs navigate by headings/bookmarks in your `materiale_edu` PDFs.
- **Keyboard-only pass** on the whole site; KWin Zoom to ≥ 200 %; enable the system **high-contrast theme**; Firefox `prefers-contrast: more`; the **WCAG 1.4.12 text-spacing bookmarklet**; DevTools emulation of `forced-colors` and `prefers-contrast` in Chromium/Firefox.
- **accerciser** to verify the AT-SPI tree exposes correct name/role/state for `#theme-toggle`, carousel buttons/dots, and the lightbox overlay.
- Session note: test on both **Plasma Wayland** (Tumbleweed default) and X11 — AT-SPI2 works on both, but behavior can differ.

### 5. Linux matrix to keep green

openSUSE Tumbleweed KDE (Wayland + X11) · GNOME · Firefox + Chromium · Orca (+ brltty) · KWin Zoom · high-contrast theme · CI: ubuntu-latest with pa11y-ci/axe/vnu/lychee/veraPDF.

---

## Windows-Specific Checks (NVDA, JAWS, Narrator, Section 508)

### 1. Why Windows is its own axis

- Windows carries the largest desktop screen-reader market: **NVDA** (free) and **JAWS** (paid) dominate, **Narrator** is built in. If you ever sell to US institutions, procurement there runs on **Section 508 (WCAG 2.0 AA) + VPAT/ACR** — your axe/veraPDF reports become sales documents, so keep them archived per release.

### 2. Windows-only tools

| Tool | What it's for |
|---|---|
| **NVDA** (free, nvda-project.org) | Primary Windows screen reader for Chrome/Edge/Firefox |
| **JAWS** (Freedom Scientific, paid) | The other major one; has a free demo mode |
| **Narrator** (Win+Ctrl+Enter) | Built-in; scan mode for quick checks |
| **Accessibility Insights for Windows** (desktop app, FastPass) | Automated WCAG checks on any window, including browsers — Windows-only |
| **Inspect.exe** (Windows SDK) | UI Automation tree inspector |
| **axe DevTools** extension for Edge/Chrome | In-browser automated audits on Windows |
| **Edge DevTools** → Accessibility pane + Issues tool | A11y tree + issue reports in the browser most Windows users have |
| **PowerToys** (Color Picker, Text Extractor OCR, Keyboard Manager, Mouse Utilities, Always on Top) | Productivity + vision/motor accommodations |
| **Magnifier** (Win + "+") | Zoom to ≥ 200 % |
| **Color filters** (Settings → Accessibility → Color filters) | System-level color-vision-deficiency simulation (ties to WCAG 1.4.1) |
| **High Contrast themes** (left Alt + left Shift + PrtSc) | `forced-colors` — verify your custom controls stay visible |
| **Voice Access** (Windows 11 22H2+) | Voice control ("click …") driven by **accessible names** — same rule as Apple Voice Control: names must match visible labels (WCAG 2.5.3) |
| **Live Captions** | System captions for any audio |

### 3. Automated checks on Windows

- The same pipeline runs natively (Node.js, Java, Chrome/Edge headless): `pa11y-ci`, Lighthouse CI, `vnu.jar`, `lychee`, `markdownlint-cli2`, `veraPDF`.
- CI: `windows-latest` GitHub runner + Edge WebDriver (Selenium) or Playwright Chromium, injecting axe-core, asserting 0 WCAG2AA violations.
- Note for your content: **flag emoji render as plain letters ("GB"/"RO") on Windows** — another reason to drop them from the language switcher (already in your TODOs).

### 4. Manual checks on Windows

- **NVDA**: browse-mode quick keys — `H` headings, `K` links, `D` landmarks, `B` buttons, `Insert+F7` elements list; verify one h1 per page, descriptive link names, skip link, carousel buttons + pause, lightbox dialog focus handling, contact links. **Math note:** NVDA speaks MathML in Firefox natively, but in Chrome/Edge it needs the free **MathPlayer** add-on — install it before judging your KaTeX/MathML output on Windows.
- **Narrator** scan mode as a second opinion; **JAWS** if budget allows (it's the reference in many US schools).
- Magnifier ≥ 200 %; enable a **High Contrast** theme and re-check borders/focus rings on the sidebar toggles, carousel dots, and lightbox overlay; also emulate `forced-colors: active` in DevTools → Rendering.
- **Color filters** on (e.g. deuteranopia) → confirm no meaning is color-only (links underlined, figures have style+label encoding).
- **Voice Access**: try "click" commands against your buttons — fails loudly when accessible names are missing or emoji-only.

### 5. Windows matrix to keep green

Windows 10/11 · Edge + Chrome + Firefox · NVDA (free) + Narrator (+ JAWS if licensed) + MathPlayer for Chrome/Edge math · Magnifier · High Contrast / forced-colors · Color filters · Voice Access · CI: windows-latest runner + Edge WebDriver + axe.
