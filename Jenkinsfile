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
    stage('Build node image') {
      steps {
        sh 'docker build -f ./dev/Docker/Dockerfile-node-CI --build-arg NPM_TOKEN=$NPM_TOKEN -t mage_node  ./dev'
      }
    }
    stage('Run versions in parallel') {
      parallel {
        stage('Build and run tests on Magento 2.2.6') {
          steps {
            sh 'VERSION=2.2.6 sh dev/jenkins/run.sh'
          }
        }
        stage('Build and run tests on Magento 2.2.3') {
          steps {
            sh 'VERSION=2.2.3 sh dev/jenkins/run.sh'
          }
        }
      }
    }
  }
}
