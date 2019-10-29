#!/bin/bash

if [ -z "$1" ]; then
    docker run --rm -ti -v $(pwd):/tmp/www -e "JEKYLL_ENV=docker" -p 8080:4000 jcubic.pl
elif [ "$1" = "build" ]; then
    docker build -t jcubic.pl .
elif [ "$1" = "bash" ]; then
    docker run --rm -ti -v $(pwd):/tmp/www jcubic.pl bash
elif [ "$1" = "make" ]; then
    docker run --rm -ti -v $(pwd):/tmp/www jcubic.pl make $2
fi
