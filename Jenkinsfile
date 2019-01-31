pipeline {
  agent {
    label 'jenkins-master01'
  }

  environment {
    http_proxy   = 'http://webproxy.emarsys.at:3128'
    https_proxy  = 'http://webproxy.emarsys.at:3128'
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
        sh 'docker-compose -f dev/docker-compose.yml --build --build-arg http_proxy=$http_proxy --build-arg https_proxy=$https_proxy'
        sh 'docker-compose -f dev/docker-compose.yml up -d'
        sh 'make create-test-db'
        sh 'make mocha'
        sh 'make run-e2e'
      }
    }
  }

  post {
    always {
        sh 'make down'
    }
  }
}
