#!/usr/bin/env bash

TS=$(date +%s)

kubectl get deploy $1 -o yaml |
  sed -E "s/value: RESTART_[0-9]+/value: RESTART_${TS}/g" |
  kubectl replace deploy $1 -f -