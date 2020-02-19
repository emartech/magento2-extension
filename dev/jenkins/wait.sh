#$/usr/bin/env bash

RUNNER=$1

wait_it()
{
  while :
  do
    result=$(docker-compose -p "mage_$RUNNER_$VERSION" ps | grep magento)
    echo "here"
    echo $result
    if [ ! -z "$result" ]; then
      healthy=$(echo $result | grep "Up (healthy)")
      if [ ! -z "$healthy" ]; then
        echo "ready"
        break
      fi
      exited=$(echo $result | grep "Exit")
      if [ ! -z "$exited" ]; then
        echo "exited"
        exit 1
      fi
    fi
    sleep 1
  done
}

wait_it