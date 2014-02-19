#!/bin/bash

echo "Installing composer packages"
composer install

echo "Installing npm packages"
npm install


echo "Installing package requirements..."
php artisan config:publish loic-sharma/profiler

echo "Installation complete"
