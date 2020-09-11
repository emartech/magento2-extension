#!/usr/bin/env sh

set -e

containers=$(docker ps --filter name=mage_* -aq)
docker rm -f $containers || echo 'No containers found'

networks=$(docker network ls --filter name=mage_* -q)
docker network rm $networks || echo 'No networks found'