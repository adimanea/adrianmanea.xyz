#!/bin/sh

SITEDIR='/var/www/adrianmanea.xyz/html'

# get new news
sfeed_update > /dev/null
# make them into HTML
sfeed_html $HOME/.sfeed/feeds/* > $HOME/.sfeed/feeds.html
# put the HTML in the website directory
cp -f $HOME/.sfeed/feeds.html $SITEDIR/pages/
echo "NEWS UPDATED\n"
