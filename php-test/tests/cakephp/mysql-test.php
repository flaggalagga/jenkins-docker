<?php
declare(strict_types=1);

namespace App\Test\TestCase;

use Cake\TestSuite\TestCase;
use Cake\Datasource\ConnectionManager;

class MySQLTest extends TestCase
{
    public function test_mysql_connection()
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
                id INT AUTO_INCREMENT PRIMARY KEY,
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
