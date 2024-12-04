#!/bin/bash
set -e

echo "Starting MySQL database initialization..."

# Wait for MySQL to be ready
until mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "SELECT 1" >/dev/null 2>&1; do
    echo "Waiting for MySQL to be ready..."
    sleep 1
done

mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<EOSQL
    -- Create test databases
    CREATE DATABASE IF NOT EXISTS laravel_test_mysql;
    CREATE DATABASE IF NOT EXISTS cake_test_mysql;

    -- Create test user
    CREATE USER IF NOT EXISTS '${TEST_USER}'@'%' IDENTIFIED BY '${TEST_PASSWORD}';
    
    -- Grant privileges
    GRANT ALL PRIVILEGES ON laravel_test_mysql.* TO '${TEST_USER}'@'%';
    GRANT ALL PRIVILEGES ON cake_test_mysql.* TO '${TEST_USER}'@'%';
    
    FLUSH PRIVILEGES;

    -- Show what was created
    SHOW DATABASES;
    SELECT User, Host FROM mysql.user;
EOSQL

echo "MySQL initialization completed"
