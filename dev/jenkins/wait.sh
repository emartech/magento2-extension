#$/usr/bin/env bash

RUNNER=$1

project_version=$(echo "$VERSION" | tr '.' '_')
echo -e "\n|--- Using docker compose project $project_version"

wait_it()
{
  while :
  do
    result=$(docker compose -p "mage_${RUNNER}_${project_version}" ps | grep magento)
    echo -e $result
    if [ ! -z "$result" ]; then
      healthy=$(echo $result | grep "(healthy)")
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
