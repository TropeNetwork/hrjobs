#!/bin/sh
CHARSET="ISO-8859-1"
xgettext --keyword=func_gettext \
         --debug \
         --default-domain=messages \
         --indent \
         --no-location \
         --sort-output \
         --width=76 \
         --from-code=ISO-8859-1 \
         --output=messages.po \
	 ../public_html/*.php \
	 ../public_html/*.inc \
 	 ../public_html/skins/*/*.html \
 	 ../public_html/setup/*.php \
 	 ../lib/*.php \
 	 ../lib/*/*.php
	 
# loop over the locales 
for locale in de_DE; do
  if (! test -e "$locale/LC_MESSAGES/messages.po") then
    cp messages.po "$locale/LC_MESSAGES/"
  fi

  cp $locale/LC_MESSAGES/messages.po messages_old.po

  # merge gettexts, build binary
  msgmerge -o messages_new.po  -i messages_old.po  messages.po
  msgfmt -o messages.mo messages_new.po 
  
  mv messages_new.po $locale/LC_MESSAGES/messages.po
  mv messages.mo $locale/LC_MESSAGES/messages.mo
done



