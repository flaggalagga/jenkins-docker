<?php
class TestDatabaseConfig
{
    public static function getMySQLConfig()
    {
        return [
            'host' => getenv('MYSQL_HOST'),
            'port' => getenv('MYSQL_PORT'),
            'database' => getenv('MYSQL_DATABASE'),
            'username' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
        ];
    }

    public static function getPgSQLConfig()
    {
        return [
            'host' => getenv('PGSQL_HOST'),
            'port' => getenv('PGSQL_PORT'),
            'database' => getenv('PGSQL_DATABASE'),
            'username' => getenv('PGSQL_USER'),
            'password' => getenv('PGSQL_PASSWORD'),
        ];
    }
}
