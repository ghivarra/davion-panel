#!/bin/bash

# remove composer and npm installer folders
rm -rf vendor
rm -rf node_modules

# echo
echo -e "Composer and NPM packages has been removed. Run install.sh script to install it again."