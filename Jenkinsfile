pipeline {
  agent {
    label 'jenkins-master01'
  }

  environment {
    http_proxy   = 'http://webproxy.emarsys.at:3128'
    https_proxy  = 'http://webproxy.emarsys.at:3128'
    NPM_TOKEN = credentials('npm_token')
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
        withCredentials([file(credentialsId: 'magento2_env', variable: 'MAGENTO2_ENV')]) {
          sh 'echo $MAGENTO2_ENV'
          sh 'ls -la && ls -la dev/'
          sh 'echo pwd'
          sh 'cp $MAGENTO2_ENV dev/.env'
          sh 'docker-compose -f dev/docker-compose.yaml build --build-arg http_proxy=$http_proxy --build-arg https_proxy=$https_proxy'
          sh 'docker-compose -f dev/docker-compose.yaml up -d'
          sh 'make create-test-db'
          sh 'make mocha'
          sh 'make run-e2e'
        }
      }
    }
  }

  post {
    always {
        sh 'make down'
    }
  }
}
