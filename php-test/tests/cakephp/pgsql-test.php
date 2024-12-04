<?php
declare(strict_types=1);

namespace App\Test\TestCase;

use Cake\TestSuite\TestCase;
use Cake\Datasource\ConnectionManager;

class PostgreSQLTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ConnectionManager::setConfig('default', [
            'url' => sprintf(
                'postgres://%s:%s@%s:%s/%s',
                getenv('TEST_PGSQL_USER'),
                getenv('TEST_PGSQL_PASSWORD'),
                getenv('TEST_PGSQL_HOST'),
                getenv('TEST_PGSQL_PORT'),
                getenv('CAKE_TEST_PGSQL_DB')
            ),
        ]);
    }

    public function test_pgsql_connection()
    {
        $connection = ConnectionManager::get('default');
        $result = $connection->execute('SELECT 1')->fetch();
        $this->assertEquals(1, $result[0]);
    }

    public function test_can_create_and_query_table()
    {
        $connection = ConnectionManager::get('default');
        
        // Create test table
        $connection->execute('
            CREATE TABLE IF NOT EXISTS test_users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255)
            )
        ');

        // Insert test data
        $connection->execute('INSERT INTO test_users (name) VALUES (?)', ['Test User']);

        // Query the data
        $result = $connection->execute('SELECT name FROM test_users WHERE name = ?', ['Test User'])->fetch();
        $this->assertEquals('Test User', $result['name']);
    }
}
