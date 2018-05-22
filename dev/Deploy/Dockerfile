FROM ubuntu:16.04

RUN apt-get update \
  && apt-get install -y --no-install-recommends\
    ca-certificates  \
    ssh

RUN mkdir -p /root/.ssh \
  && ssh-keyscan -H 35.204.248.33 >> /root/.ssh/known_hosts
