#!/bin/bash

cd /var/www/vhosts/sounds
pwd

echo "copying database file"
cp local.ini sounds.vanceandhines.com/php/.local.ini

echo "setting owner & group"
chown -R vhfp3:apache  sounds.vanceandhines.com/

echo "adding links"
cd /var/www/vhosts/sounds/sounds.vanceandhines.com/
rm -f audio
ln -s /var/www/vhosts/sounds/audio audio

echo "Done!"
