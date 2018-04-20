# Emarsys Magento 2 Extension Developer Guide

## Installation
To start development first copy `dev/.env.example` to `dev/.env` then use
```
$ make up
```
This will set up the containers and run Magento installation.

The web container will expose its port `80` to port `8888` on the host machine.

By default the Magento store will be available at http://magento.local:8888, but you have to add this to your `/etc/hosts` file first:
```
127.0.0.1 magento.local
```

### Sample data
If you want sample data use:
```
$ make install-sampledata
```
---
## Usage
### Working with containers
Start all
```
$ make start
```
Stop all
```
$ make stop
```
Display status
```
$ make ps
```
Destroy all
```
$ make down
```
**Note:** MYSQL data is persisted in local filesystem for faster rebuild. If you want to start from a clean state, you can delete `dev/db-data` folder after `make down`.

Access the web container CLI as `root`
```
$ make ssh
```
**Warning:** Do not run Magento CLI commands as root!

Execute single command in web container as `root`
```
$ make exec <command>
```

### Magento
If you want to run Magento CLI commands, you should use
```
$ make magento command=setup:upgrade
```
This will run the command as the `www-data` user in the container. If you run this without command parameter, you will get a list of available commands.