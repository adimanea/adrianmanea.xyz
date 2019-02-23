#!/bin/sh

# this script uses sed to prettify the news summary
# that sfeed_html outputs

SITEDIR='var/www/adrianmanea.xyz/html/pages'

####################################
# FOLDABLE FEEDS
###################################

# change h2 to accordion button (i = inplace)
sed -i s_h2_"button class=\"accordion\"><span class=\"accordion1\""_ ${SITEDIR}/feeds.html
# change closing h2 to closing span & button
sed -i s_"</h2>"_"</span></button>"_ ${SITEDIR}/feeds.html
# make new div and list for the items after button
sed -i s_"</button>"_"</button>\n<div class=\"panel\">\n<ul class=\"big-list\">"_ ${SITEDIR}/feeds.html
# make entries starting with 201* become list items
sed -i 's_\b201.*_<li>&</li>_' ${SITEDIR}/feeds.html
# close ul and div before next button
sed -i 's_^<button class_</ul>\n</div>\n<button class_' ${SITEDIR}/feeds.html


#######################
# MOVE THE SIDEBAR UP
#######################
# select the sidebar and store it
SIDEBAR=$(echo `awk '/\"sidebar\"/{a=1}/\/div>/{a=0}a' ${SITEDIR}/feeds.html`)
# delete the sidebar
sed -i "/\"sidebar\"/,/\/div>/d" ${SITEDIR}/feeds.html
# write the sidebar where it belongs
sed -i s_"<body class=\"noframe\">"_"<body class=\"noframe\">${SIDEBAR}\n"_ ${SITEDIR}/feeds.html

##################################
## PRETTIFY "SIDEBAR" => FOLDABLE
##################################
sed -i "s/div id=\"sidebar\"/button class=\"accordion\"\>\<span class=\"accordion1\"\>Cuprins\<\/span\>\<\/button/" ${SITEDIR}/feeds.html
# make all ul into ul class="big-list"
#sed -i "s/\<ul\>/\<ul class=\"big-list\"\>/" ${SITEDIR}/feeds.html

##################################
# OTHER OPERATIONS
#################################
# delete the <pre> and </pre>
sed -i s_"<pre>"__ ${SITEDIR}/feeds.html
sed -i s_"</pre>"__ ${SITEDIR}/feeds.html

# call the js script at the bottom
sed -i s_"</body>"_"<script src=\"../scripts/bookmarks-script.js\"></script>\n</body>"_ ${SITEDIR}/feeds.html

