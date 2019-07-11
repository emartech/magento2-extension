#$/usr/bin/env bash

wait_it()
{
  while :
  do
    result=$(docker-compose -p "mage_$VERSION" ps | grep magento)

    if [[ ! -z "$result" ]]; then
      healthy=$(echo $result | grep "Up (healthy)")
      if [[ ! -z "$healthy" ]]; then
        echo "ready"
        break
      fi
      exited=$(echo $result | grep "Exit")
      if [[ ! -z "$exited" ]]; then
        echo "exited"
        exit 1
      fi
    fi
    sleep 1
  done
}

wait_it