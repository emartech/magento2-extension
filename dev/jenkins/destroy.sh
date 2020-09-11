#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

if [[ $version =~ ^2\.4 ]]
then
  composefile="docker-compose-elastic.yml"
else
  composefile="docker-compose.yml"
fi

alias compose="docker-compose -f $composefile"

compose -p mage_unit_$VERSION down || echo "Could not stop Docker..."
compose -p mage_e2e_$VERSION down || echo "Could not stop Docker..."