var pages = [
    "../pages-random-en/adam-elohima.html",
    "../pages-random-en/adam-piano.html",
    "../pages-random-en/coke.html",
    "../pages-random-en/currey.html",
    "../pages-random-en/eddington.html",
    "../pages-random-en/exurb1a.html",
    "../pages-random-en/fermat-lib.html",
    "../pages-random-en/fever.html",
    "../pages-random-en/fey-prime.html",
    "../pages-random-en/gnossienne.html",
    "../pages-random-en/iron-trapeze.html",
    "../pages-random-en/keating-en.html",
    "../pages-random-en/kn-life.html",
    "../pages-random-en/kn-wherever.html",
    "../pages-random-en/letters.html",
    "../pages-random-en/pelageya.html",
    "../pages-random-en/rpf.html"
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
