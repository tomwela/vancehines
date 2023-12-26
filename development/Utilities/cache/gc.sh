#!/bin/bash
find  /home/www/sites/spggoods.com/html/development/Temporary/_cache -type f -mmin +0 -delete
echo "Done"