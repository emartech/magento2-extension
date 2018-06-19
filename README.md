# Emarsys Magento 2 Extension

For development environment information refer to [dev README](dev/README.md).

## Release
To create a new release follow these steps:
* Bump the plugin version in `composer.json`, commit, tag with version number and push. (Optionally tagging can be made on the UI later.)
* Go the repo on GitHub, click **releases** tab and click **Draft new release** button.
* If you have tagged the version earlier choose it from the dropdown, otherwise enter the new version number in the field.
* Add release title and optionally description.
* Click **Publish release** button.

## Codeship env
* [Install](https://documentation.codeship.com/pro/jet-cli/installation/) `jet`
* Download the `aes` key from [Codeship](https://app.codeship.com/projects/290273/configure) into the project directory.
* Run `$ jet encrypt codeship.env codeship.env.encrypted`
* Commit `codeship.env.encrypted` into the repo.

## Deployment
Both staging and production environments have a `deploy.sh` script in the bitnami user's home folder. This should run all required commands to update the extension to the latest commit. So the workflow is the following:
* `$ ssh bitnami@environment-ip-address` *
* `$ sh deploy.sh`

\* If you do not have SSH access: 
* go to [GCP console / Compute Engine / Metadata / SSH keys](https://console.cloud.google.com/compute/metadata/sshKeys?project=ems-plugins)
* Click **Edit**
* Add your SSH public key, but change the username to `bitnami` at the end.
Ex.:
<br>From `...oGQAWT8/B6gEKaHbJoUX1zTxJUWpgeQ== your.name@emarsys.com`
<br>To `...oGQAWT8/B6gEKaHbJoUX1zTxJUWpgeQ== bitnami`
* Click **Save**