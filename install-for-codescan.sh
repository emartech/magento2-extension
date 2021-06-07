composer config -g github-oauth.github.com "${GITHUB_TOKEN}"
composer install --no-autoloader --no-scripts --no-progress --no-suggest --no-interaction --ignore-platform-reqs --no-dev