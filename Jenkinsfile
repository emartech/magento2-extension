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
    CODESHIP_USER       = credentials('integrations_team_codeship_user')
    CODESHIP_PASSWORD   = credentials('integrations_team_codeship_password')
    GCP_SERVICE_ACCOUNT = credentials('plugins-gcloud-ci-service-account-keyfile')
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
        sh 'docker build -f ./dev/Docker/Dockerfile-node-CI --build-arg NPM_TOKEN=$NPM_TOKEN --build-arg http_proxy=http://webproxy.emarsys.at:3128 --build-arg https_proxy=http://webproxy.emarsys.at:3128 -t mage_node ./dev'
      }
    }
    stage('Run code style check') {
      steps {
        sh 'VERSION=2.3.3ee sh dev/jenkins/run-code-style.sh'
      }
    }
    stage('Run tests on current versions') {
      parallel {
        stage('Magento 2.3.3CE: build and run tests') {
          steps {
            sh 'VERSION=2.3.3ce sh dev/jenkins/run.sh'
          }
        }
        stage('Magento 2.3.3EE: build and run tests') {
          steps {
            sh 'VERSION=2.3.3ee sh dev/jenkins/run.sh'
          }
        }
      }
    }
    stage('Deploy to staging') {
      steps {
        sh 'echo "$GCP_SERVICE_ACCOUNT" > ci-account.json'
        sh 'docker run --name gcloud-auth -e HTTP_PROXY="http://webproxy.emarsys.at:3128" -e HTTPS_PROXY="http://webproxy.emarsys.at:3128" -v "$(pwd)/ci-account.json:/auth/ci-account.json" emarsys/ems-plugins-gke-service /bin/bash -c "gcloud auth activate-service-account ci-service@ems-plugins.iam.gserviceaccount.com --key-file=/auth/ci-account.json && gcloud container clusters get-credentials cluster-1 --region europe-west2 --project ems-plugins"'
        sh 'docker run --rm -e HTTP_PROXY="http://webproxy.emarsys.at:3128" -e HTTPS_PROXY="http://webproxy.emarsys.at:3128" --volumes-from gcloud-auth emarsys/ems-plugins-gke-service kubectl set env deployment web-staging EXTENSION_SHA=$GIT_COMMIT'
        sh 'docker rm gcloud-auth'
        sh 'rm ci-account.json'
      }
    }
    stage('Run tests on recent versions') {
      parallel {
        stage('Magento 2.3.1EE: build and run tests') {
          steps {
            sh 'VERSION=2.3.1ee sh dev/jenkins/run.sh'
          }
        }
        stage('Magento 2.3.1CE with table prefix: build and run tests') {
          steps {
            sh 'VERSION=2.3.1ce-prefixed TABLE_PREFIX=ems_ sh dev/jenkins/run.sh'
          }
        }
      }
    }
    stage('Run tests on legacy versions') {
      parallel {
        stage('Magento 2.2.6CE: build and run tests') {
          steps {
            sh 'VERSION=2.2.6ce sh dev/jenkins/run.sh'
          }
        }
        stage('Magento 2.1.8CE: build and run tests') {
          steps {
            sh 'VERSION=2.1.8ce sh dev/jenkins/run.sh'
          }
        }
      }
    }
  }
  post {
    always {
      sh 'docker container rm -f $(docker container ls -aq) || echo \'No leftover containers...\''
      sh 'docker rmi mage_node || echo \'Mage Node could not be removed...\''
      sh 'VERSION=2.3.3ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v --rmi all || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.2ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v --rmi all || echo \'Could not stop Docker...\''
      sh 'VERSION=2.2.6ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v --rmi all || echo \'Could not stop Docker...\''
      sh 'VERSION=2.1.8ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v --rmi all || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.1ce-prefixed docker-compose -f ./dev/jenkins/docker-compose.yml down -v --rmi all || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.2ee docker-compose -f ./dev/jenkins/docker-compose.yml down -v --rmi all || echo \'Could not stop Docker...\''
      sh 'docker rmi emarsys/ems-integration-cypress:latest'
    }
    success {
      slackSend(tokenCredentialId: '	slack-jenkins-shopify', color: 'good', message: "${env.JOB_NAME} - #${env.BUILD_NUMBER} SUCCESS after ${currentBuild.durationString} (<${env.BUILD_URL}|Open>)")
    }
    failure {
      slackSend(tokenCredentialId: '	slack-jenkins-shopify', color: 'danger', message: "${env.JOB_NAME} - #${env.BUILD_NUMBER} FAILED after ${currentBuild.durationString} (<${env.BUILD_URL}|Open>)")
    }
  }
}
