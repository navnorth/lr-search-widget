#!/bin/bash

if [ "$(uname)" == "Darwin" ]; then

    PIDS=()

    # Start Redis
    pushd conf/redis > /dev/null 2>&1
    redis-server redis.homebrew.conf &
    PIDS+=("$!")
    popd

    sleep 2

    tail -f conf/redis/stdout

    kill ${PIDS[*]}

    wait



    echo "Services Stopped"

else
    echo "Sorry. For now this script is only configured for OSX with services installed via homebrew"
fi
