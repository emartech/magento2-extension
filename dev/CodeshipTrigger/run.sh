#!/bin/bash

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

echo "\n|--- Triggering deploy on Codeship"

docker run --rm \
    -v "$(pwd)/index.js:/index.js" \
    -v "$(pwd)/package.json:/package.json" \
    -v "$(pwd)/package-lock.json:/package-lock.json" \
    -e "CODESHIP_USER=$CODESHIP_USER" \
    -e "CODESHIP_PASSWORD=$CODESHIP_PASSWORD" \
    -e "REVISION=$GIT_COMMIT" \
    node bash -c "npm i && node index.js" \
    --http-proxy=$http_proxy \
    --https-proxy=https_proxy