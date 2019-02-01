var pages = [
    "pages-random/adam-brown-piano.html",
    "pages-random/adam-elohima.html",
    "pages-random/coke-studio.html",
    "pages-random/daily-rituals.html",
    "pages-random/exurb1a.html",
    "pages-random/fermat-library.html",
    "pages-random/fever-ray.html",
    "pages-random/feynman.html",
    "pages-random/feynman-prim.html",
    "pages-random/foamea.html",
    "pages-random/gnossienne-guitar.html",
    "pages-random/introvertiti.html",
    "pages-random/iron-wine-trapeze.html",
    "pages-random/ghilgames.html",
    "pages-random/keating.html",
    "pages-random/knausgaard.html",
    "pages-random/kn-cadru.html",
    "pages-random/kn-experienta.html",
    "pages-random/kn-viata.html",
    "pages-random/knopfler-wherever.html",
    "pages-random/letters-live.html",
    "pages-random/mother.html",
    "pages-random/nu-mai-pilpiie.html",
    "pages-random/paler-cicatrice.html",
    "pages-random/paler-desertul.html",
    "pages-random/pelageya.html",
    "pages-random/problema-fringhii.html",
    "pages-random/riemann-ornea.html",
    "pages-random/sabato.html",
    "pages-random/tacere-4.html",
    "pages-random/tacere-24.html",
    "pages-random/tacere-40.html",
    "pages-random/tacere-50.html",
    "pages-random/tacere-94.html",
    "pages-random/tacere-117.html",
    "pages-random/turing.html",
    "pages-random/vers-univers.html",
    "pages-random/zen-alergi.html",
    "pages-random/zen-frumusete.html",
    "pages-random/zen-generatii.html"
]

// ======== WITH SCHEDULER ============
function randomContent() {
    var d = new Date();
    var hour = d.getHours();
    var day = d.getDay();
    var month = d.getMonth();
    var year = d.getFullYear();
    if (hour == 7) {
        var page = pages[(hour + day + month + year) % pages.length];
        return page;
    }
    else {
        var page = pages[(7 + day + month + year) % pages.length];
        return page;
    }
}
