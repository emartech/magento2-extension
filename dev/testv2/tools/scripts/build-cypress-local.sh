#!/usr/bin/env bash

set +e

cd ../docker/cypress/ || exit
docker build -t "cypress:3.6.1" .
