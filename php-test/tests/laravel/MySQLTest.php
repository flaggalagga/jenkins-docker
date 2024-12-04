<?php
namespace Tests\Laravel;

use PHPUnit\Framework\TestCase;
use PDO;

class MySQLTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s",
            'mysql',
            '3306',
            getenv('LARAVEL_MYSQL_TEST_DB')
        );

        $this->pdo = new PDO(
            $dsn,
            getenv('TEST_USER'),
            getenv('TEST_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function testConnection()
    {
        $stmt = $this->pdo->query('SELECT 1');
        $result = $stmt->fetch(PDO::FETCH_NUM);
        $this->assertEquals(1, $result[0]);
    }

    public function testTableOperations()
    {
        // Create test table
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS test_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255)
            )
        ');

        // Insert data
        $stmt = $this->pdo->prepare('INSERT INTO test_table (name) VALUES (?)');
        $stmt->execute(['test item']);

        // Query data
        $stmt = $this->pdo->query('SELECT * FROM test_table');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals('test item', $result['name']);

        // Clean up
        $this->pdo->exec('DROP TABLE test_table');
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }
}
