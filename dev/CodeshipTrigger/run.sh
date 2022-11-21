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
    -e "http_proxy=$http_proxy" \
    -e "https_proxy=$https_proxy" \
    node bash -c "npm config set proxy $http_proxy && npm config set https-proxy $https_proxy && npm i && node index.js" \
    --http-proxy=$http_proxy \
    --https-proxy=$https_proxy