#!/usr/bin/env bash

set +e

cd ../../ || exit
docker build -f tools/docker/magenode/Dockerfile-node-local -t "mage_node" .
