pipeline {
    agent any

    environment {
        // Test database credentials
        TEST_USER = 'testuser'
        TEST_PASSWORD = credentials('test-db-password')
        
        // Test databases
        LARAVEL_MYSQL_TEST_DB = 'laravel_test_mysql'
        LARAVEL_PGSQL_TEST_DB = 'laravel_test_pgsql'
        CAKE_MYSQL_TEST_DB = 'cake_test_mysql'
        CAKE_PGSQL_TEST_DB = 'cake_test_pgsql'
        
        // Database root passwords
        MYSQL_ROOT_PASSWORD = credentials('mysql-root-password')
        POSTGRES_PASSWORD = credentials('postgres-password')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Prepare Test Environment') {
            steps {
                sh '''
                    # Start databases
                    docker-compose up -d mysql postgres
                    
                    # Wait for databases to be ready
                    sleep 30
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh '''
                    cd php-test
                    # Run all test suites
                    vendor/bin/phpunit -c tests/phpunit.xml
                '''
            }
        }
    }

    post {
        always {
            sh '''
                # Stop databases
                docker-compose down
            '''
        }
        success {
            echo 'All tests passed!'
        }
        failure {
            echo 'Tests failed! Check the logs for details.'
        }
    }
}
