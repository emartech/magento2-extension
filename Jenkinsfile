pipeline {
  agent {
    label 'jenkins-master01'
  }

  environment {
    http_proxy          = 'http://webproxy.emarsys.at:3128'
    https_proxy         = 'http://webproxy.emarsys.at:3128'
    NPM_TOKEN           = credentials('npm_token')
    MAGENTO_REPO_KEY    = credentials('magento_repo_key')
    MAGENTO_REPO_SECRET = credentials('magento_repo_secret')
    MYSQL_HOST          = 'db'
    MYSQL_USER          = 'magento'
    MYSQL_PASSWORD      = 'magento'
    MAGENTO_URL         = 'http://magento-test.local'
  }

  triggers {
    pollSCM '* * * * *'
  }

  options {
    buildDiscarder(logRotator(numToKeepStr: '50'))
    disableConcurrentBuilds()
    timestamps()
  }

  stages {
    stage('Build and run tests') {
      steps {
        sh 'docker-compose -f dev/docker-compose.yaml build --build-arg http_proxy=$http_proxy --build-arg https_proxy=$https_proxy node magento-test magento-dev'
        sh 'docker-compose -f dev/docker-compose.yaml -p mage up -d'
        sh 'docker-compose -f dev/docker-compose.yaml -p mage exec -T --user root magento-test /bin/sh -c \'sh vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh\''
        sh 'docker-compose -f dev/docker-compose.yaml -p mage exec -T --user root magento-test /bin/sh -c \'sh vendor/emartech/emarsys-magento2-extension/dev/Magento/compile.sh\''
        sh 'docker-compose -f dev/docker-compose.yaml -p mage exec -T --user root node /bin/sh -c \'npm i && npm t\''
        sh 'docker-compose -f dev/docker-compose.yaml -p mage exec -T --user root node /bin/sh -c \'npm i && npm run e2e:ci\''

      }
    }
  }

  post {
    always {
        sh 'docker-compose -f dev/docker-compose.yaml -p mage down'
        sh 'docker volume rm mage_magento-db'
    }
  }
}
