pipeline {
  agent {
    label 'magento'
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
      parallel {
        stage('Magento') {
          steps {
            sh 'VERSION=2.3.3ee sh dev/jenkins/run-code-style.sh'
          }
        }
        stage('ESLint') {
          steps {
            sh 'docker run --rm mage_node sh -c "npm run code-style"'
          }
        }
      }
    }
    stage('Run tests on recent versions') {
      parallel {
        stage('Run unit tests on 2.4.0CE') {
          steps {
            sh 'VERSION=2.4.0ce sh dev/jenkins/run-unit.sh'
          }
        }
        stage('Run e2e tests on 2.4.0CE') {
          steps {
            sh 'VERSION=2.4.0ce sh dev/jenkins/run-e2e.sh'
          }
        }

        stage('Run unit tests on 2.3.3EE') {
          steps {
            sh 'VERSION=2.3.3ee sh dev/jenkins/run-unit.sh'
          }
        }
        stage('Run e2e tests on 2.3.3EE') {
          steps {
            sh 'VERSION=2.3.3ee sh dev/jenkins/run-e2e.sh'
          }
        }

        stage('Run unit tests on 2.3.5CE') {
          steps {
            sh 'VERSION=2.3.5ce sh dev/jenkins/run-unit.sh'
          }
        }
        stage('Run e2e tests on 2.3.5CE') {
          steps {
            sh 'VERSION=2.3.5ce sh dev/jenkins/run-e2e.sh'
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
    stage('Run tests on old version') {
      parallel {
        stage('Run unit tests on 2.3.1CE with table prefix') {
          steps {
            sh 'VERSION=2.3.1ce-prefixed sh dev/jenkins/run-unit.sh'
          }
        }
        stage('Run e2e tests on 2.3.1CE with table prefix') {
          steps {
            sh 'VERSION=2.3.1ce-prefixed sh dev/jenkins/run-e2e.sh'
          }
        }

        stage('Run unit tests on 2.2.6CE') {
          steps {
            sh 'VERSION=2.2.6ce sh dev/jenkins/run-unit.sh'
          }
        }
        stage('Run e2e tests on 2.2.6CE') {
          steps {
            sh 'VERSION=2.2.6ce sh dev/jenkins/run-e2e.sh'
          }
        }

        stage('Run unit tests on 2.3.2EE') {
          steps {
            sh 'VERSION=2.3.2ee sh dev/jenkins/run-unit.sh'
          }
        }
        stage('Run e2e tests on 2.3.2EE') {
          steps {
            sh 'VERSION=2.3.2ee sh dev/jenkins/run-e2e.sh'
          }
        }
      }
    }
  }
  post {
    always {
      sh 'docker rmi mage_node || echo \'Mage Node could not be removed...\''
      sh 'VERSION=2.3.3ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.3ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.2ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.2.6ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.1.8ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.1.9ee docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.1ce-prefixed docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.2ee docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.4.0ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
      sh 'VERSION=2.3.5ce docker-compose -f ./dev/jenkins/docker-compose.yml down -v || echo \'Could not stop Docker...\''
    }
    success {
      slackSend(tokenCredentialId: '	slack-jenkins-shopify', color: 'good', message: "${env.JOB_NAME} - #${env.BUILD_NUMBER} SUCCESS after ${currentBuild.durationString} (<${env.BUILD_URL}|Open>)")
    }
    failure {
      slackSend(tokenCredentialId: '	slack-jenkins-shopify', color: 'danger', message: "${env.JOB_NAME} - #${env.BUILD_NUMBER} FAILED after ${currentBuild.durationString} (<${env.BUILD_URL}|Open>)")
    }
  }
}
