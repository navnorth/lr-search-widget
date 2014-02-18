#!/bin/bash

echo "Installing package requirements..."
php artisan config:publish loic-sharma/profiler

echo "Installation complete"
