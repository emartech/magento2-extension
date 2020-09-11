#!/usr/bin/env sh

set -e

docker ps --filter name=mage_* -aq | xargs docker rm -f