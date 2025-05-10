#!/bin/bash

# run composer and npm installer
composer install
npm install

# copy default file
cp public/default-favicon.ico public/favicon.ico
cp env .env

# echo
echo -e "\n\nComposer and NPM packages has been installed.\nModify the .env file especially the database configurations before migrating the database. \nThen run 'php spark migrate:refresh' command afterwards."