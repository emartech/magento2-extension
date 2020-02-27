#$/usr/bin/env bash

BASEDIR=$(dirname "$0")
cd $BASEDIR

elapsed=0

wait_it()
{
  while :
  do
    result=$(docker-compose -p "mage" ps | grep magento-test)
    if [ ! -z "$result" ]; then
      healthy=$(echo $result | grep "Up (healthy)")
      if [ ! -z "$healthy" ]; then
        printf "\nContainers ready\n"
        break
      fi
      exited=$(echo $result | grep "Exit")
      if [ ! -z "$exited" ]; then
        printf "\nMagento container exited. Startup FAILED."
        exit 1
      fi
    fi
    printf "Waiting for container init... ($elapsed sec)\r"
    elapsed=$((elapsed + 5))
    sleep 5
  done
}

wait_it