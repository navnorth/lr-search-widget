#!/bin/bash

usage="usage: $(basename "$0") [-h] [-w] -- compile coffeescript and stylus files\n
\n
Options:\n
    -h show this help text\n
    -w watch for changes\n"

WATCH=""

while getopts ":wh" opt; do
    case $opt in
        w)
            WATCH="-w"
            ;;
        \?)
            echo "Invalid option: -$OPTARG" >&2
            echo -e $usage >&2
            exit 1
            ;;
        :)
            echo "Option -$OPTARG requires an argument" >&2
            echo -e $usage >&2
            exit 1;;
        h)
            echo -e $usage;
            exit
    esac
done

PIDS=()

../node_modules/stylus/bin/stylus  $WATCH -u nib ../app/assets/stylus/*.styl -o ../public/css &
PIDS+=("$!")

../node_modules/coffee-script/bin/coffee $WATCH -c -o ../public/js ../app/assets/coffee/ &
PIDS+=("$!")

trap "kill {$PIDS[@]}" SIGTERM

wait
