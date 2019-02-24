#!/bin/sh

SITEDIR='/var/www/adrianmanea.xyz/html'
MYPATH='/usr/local/bin'

# remove all feeds (start fresh) -- in case of update
rm -rf $HOME/.sfeed/feeds/*
# get new news
$MYPATH/sfeed_update
# make them into HTML
$MYPATH/sfeed_html $HOME/.sfeed/feeds/* > $HOME/.sfeed/feeds.html
# put the HTML in the website directory
cp -f $HOME/.sfeed/feeds.html $SITEDIR/pages/
echo "NEWS UPDATED\n"
