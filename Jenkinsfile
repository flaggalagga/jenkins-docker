pipeline {
   agent any

   environment {
       TEST_USER = 'testuser'
       TEST_PASSWORD = credentials('test-db-password')
       LARAVEL_MYSQL_TEST_DB = 'laravel_test_mysql'
       LARAVEL_PGSQL_TEST_DB = 'laravel_test_pgsql'
       CAKE_MYSQL_TEST_DB = 'cake_test_mysql'
       CAKE_PGSQL_TEST_DB = 'cake_test_pgsql'
   }

   stages {
       stage('Checkout') {
           steps {
               checkout scm
           }
       }

       stage('Run Tests') {
           steps {
               sh '''
                   docker-compose run --rm php-test vendor/bin/phpunit -c tests/phpunit.xml
               '''
           }
       }
   }

   post {
       success {
           echo 'All tests passed!'
       }
       failure {
           echo 'Tests failed! Check the logs for details.'
       }
   }
}
