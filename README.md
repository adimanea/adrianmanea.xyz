# My Personal Website

Here are the files for my [personal website](adrianmanea.xyz).

Shortcuts:
- [All PDFs](https://gitlab.com/adimanea/adrianmanea.xyz/tree/master/docs);
- [All LaTeX sources](https://gitlab.com/adimanea/adrianmanea.xyz/tree/master/latex);
- [My landscape photos (full res)](https://gitlab.com/adimanea/adrianmanea.xyz/tree/master/images/gallery/hi-res/la) (easier to browse them [here](https://adrianmanea.xyz/pages/6-photo.php));
- [My portrait photos (full res)](https://gitlab.com/adimanea/adrianmanea.xyz/tree/master/images/gallery/hi-res/po) (easier to browse them [here](https://adrianmanea.xyz/pages/6-foto.php)).

All files are my own, except where references are cited or credits are given.

Content is free for personal use, but credit is expected.

All contributions and comments are welcome, via email or PR/issue.

## Știri
I added a news section ([Știri](https://adrianmanea.xyz/pages/6-stiri.php)) which aggregates the RSS feeds from some sources that I follow. In the terminal, on my computer, I use [newsboat](https://newsboat.org/), but for mobile, I needed to put the feeds somewhere. So I collect them, put them in that section that automatically updates every 3 hours.

Inspired by [Kai Hendry's solution](https://www.youtube.com/watch?v=Sspm9K6g2BE) with [picofeed](https://github.com/seenaburns/picofeed).


## TODOs
### Știri
- prettify **Știri**:
    + move navigation summary from bottom to top (`sed`?);
    + disable horizontal scroll & enable wrap (another `style.css`?);
    + make sections into `button/accordion` as in **Didactica** (another `style.css` + `sed`?)
- get short descriptions of article content ("Abstract/Excerpt"):
    + switch parser (with [mozilla/readablility](https://github.com/mozilla/readability) ?) or edit source (see `curl` part + parse).
- archive when it gets messy (see `sfeed` README, the `sfeed_archive.c` example);
- does it mark as read?
