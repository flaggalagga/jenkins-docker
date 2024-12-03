#!/bin/bash
set -e

echo "Creating PostgreSQL test databases..."
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
    CREATE DATABASE cake_test_pgsql;
    CREATE DATABASE laravel_test_pgsql;

    CREATE USER $DB_TEST_USER WITH PASSWORD '$DB_TEST_PASSWORD';
    GRANT ALL PRIVILEGES ON DATABASE cake_test_pgsql TO $DB_TEST_USER;
    GRANT ALL PRIVILEGES ON DATABASE laravel_test_pgsql TO $DB_TEST_USER;
EOSQL
