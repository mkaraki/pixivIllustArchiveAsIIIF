#!/bin/sh
SCRIPT_DIR=$(cd $(dirname $0); pwd)
docker run -it --rm -e MONGO_INITDB_ROOT_USERNAME=root -e MONGO_INITDB_ROOT_PASSWORD=toor -p 27017:27017 -v $SCRIPT_DIR/_dbg/db:/data/db mongo
