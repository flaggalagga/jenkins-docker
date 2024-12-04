pipeline {
    agent any

    environment {
        TEST_USER = 'testuser'
        TEST_PASSWORD = credentials('test-db-password')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Environment Setup') {
            steps {
                sh '''
                    docker-compose down
                    docker volume rm jenkins-docker_mysql-data jenkins-docker_postgres-data || true
                    docker-compose up -d
                    sleep 30  # Wait for databases to initialize
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh 'docker-compose run --rm php-test vendor/bin/phpunit -c tests/phpunit.xml'
            }
        }
    }

    post {
        always {
            sh 'docker-compose down'
        }
    }
}
