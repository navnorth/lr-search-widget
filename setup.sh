#!/bin/bash

echo "Installing composer packages"
composer install

echo "Installing npm packages"
npm install

echo "Installing workbench packages"
pushd workbench/navnorth/lr-publisher > /dev/null
composer install
popd > /dev/null

echo "Installing package requirements..."
php artisan config:publish loic-sharma/profiler

echo "Installing Database"
php artisan migration

echo "Installation complete"
