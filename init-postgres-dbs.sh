#!/bin/bash
set -e

echo "Initializing PostgreSQL databases..."

# Function to log messages
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Function to handle errors
handle_error() {
    log "ERROR: PostgreSQL initialization failed: $1"
    exit 1
}

trap 'handle_error "Script interrupted"' INT TERM

log "Creating PostgreSQL test user and databases..."

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL || handle_error "SQL execution failed"
    DO \$\$ 
    BEGIN
        -- Create test user if not exists
        IF NOT EXISTS (SELECT FROM pg_user WHERE usename = '${TEST_USER}') THEN
            CREATE USER ${TEST_USER} WITH PASSWORD '${TEST_PASSWORD}';
        END IF;
    EXCEPTION WHEN OTHERS THEN
        RAISE NOTICE 'Error creating user: %', SQLERRM;
    END
    \$\$;

    -- Create test databases
    SELECT 'CREATE DATABASE ${LARAVEL_PGSQL_TEST_DB}'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = '${LARAVEL_PGSQL_TEST_DB}');
    
    SELECT 'CREATE DATABASE ${CAKE_PGSQL_TEST_DB}'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = '${CAKE_PGSQL_TEST_DB}');

    -- Grant privileges
    GRANT ALL PRIVILEGES ON DATABASE ${LARAVEL_PGSQL_TEST_DB} TO ${TEST_USER};
    GRANT ALL PRIVILEGES ON DATABASE ${CAKE_PGSQL_TEST_DB} TO ${TEST_USER};
EOSQL

# Grant schema privileges
for DB in "${LARAVEL_PGSQL_TEST_DB}" "${CAKE_PGSQL_TEST_DB}"; do
    log "Configuring schema privileges for ${DB}"
    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "${DB}" <<-EOSQL || handle_error "Schema privileges failed for ${DB}"
        GRANT ALL ON SCHEMA public TO ${TEST_USER};
        ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO ${TEST_USER};
        ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO ${TEST_USER};
EOSQL
done

log "PostgreSQL initialization completed successfully"
