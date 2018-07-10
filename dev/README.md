# Emarsys Magento 2 Extension Developer Guide

## Prerequisites
To be able to pull image from Google Container Registry (GCR), you will have to authenticate to Google Cloud and
 configure docker to use those credentials.
 
First install Google Cloud SDK:
```
$ brew cask install google-cloud-sdk
```
Then login to your account:
```
$ gcloud auth login
```
This will bring up the browser: choose your account that is associated with the `ems-plugin` GCP project and grant the permissions.

After authentication set the default project:
```
$ gcloud config set project ems-plugins
```
The last step is to configure docker to use the credentials:
```
$ gcloud auth configure-docker
```

## Installation
To start development first copy `dev/.env.example` to `dev/.env` then use
```
$ make up
```
This will set up the containers, run Magento installation and installs the extension.

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

Removing sample data:
```
  make down
  docker volume rm mage_magento-db
  make up
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
**Note:** MYSQL data is persisted in a Docker volume for faster rebuild. If you want to start from a clean state, you can delete `mage_magento-db` volume folder after `make down` by
```
$ docker volume rm mage_magento-db
```

Access the web container CLI as `www-data` user
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

There are some frequently used Magento commands predefined:

Run `cache:flush` & `setup:upgrade`:
```
$ make upgrade
```

Clean the generated code folder and run `cache:flush`:
```
$ make flush
```

For debugging, use (This will `tail -f` Magento's `expception.log` file.):
```
$ make exception
```

**Uninstall** the extension in local instance (will remove connect token from `core_config_data`, drop extension migrations, delete module from `setup_module`, delete generated code and flush cache):
```
$ make uninstall
```
then you can **reinstall** it by calling `upgrade`:
```
$ make upgrade
```

### MYSQL
The MYSQL container exposes its connection port `3306` to port `13306` on the host machine. Credentials are defined in your `.env` file.

You can also use
```
$ make mysql
```
to enter the MYSQL CLI directly.

### Testing
Tests are run in NodeJS environment in a separate container. The node container does not run constantly, it boots up for one-off test runs.

Before the first run `npm` packages must be installed by
```
$ make npm-install
```
To run the tests use
```
$ make test
```