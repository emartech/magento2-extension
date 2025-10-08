#!/usr/bin/env bash

set +e

# cd ../../ || exit
docker build -f testv2/tools/docker/Dockerfile-node-local -t "mage_node" .
