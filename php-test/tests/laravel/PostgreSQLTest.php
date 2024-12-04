<?php
namespace Tests\Laravel;

use PHPUnit\Framework\TestCase;
use PDO;

class PostgreSQLTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s",
            'postgres',
            '5432',
            getenv('LARAVEL_PGSQL_TEST_DB')
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

    protected function tearDown(): void
    {
        $this->pdo = null;
    }
}
