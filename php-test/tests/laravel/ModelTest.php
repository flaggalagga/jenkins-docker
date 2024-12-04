<?php
namespace Tests\Laravel;

use PHPUnit\Framework\TestCase;
use PDO;

class ModelTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO(
            sprintf(
                "mysql:host=%s;port=%s;dbname=%s",
                'mysql',
                '3306',
                getenv('LARAVEL_MYSQL_TEST_DB')
            ),
            getenv('TEST_USER'),
            getenv('TEST_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Create test table
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    public function testCanCreateUser()
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO users (name, email) VALUES (?, ?)
        ');
        $result = $stmt->execute(['Test User', 'test@example.com']);
        $this->assertTrue($result);

        $lastId = $this->pdo->lastInsertId();
        $this->assertNotEmpty($lastId);
    }

    public function testCanRetrieveUser()
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM users WHERE email = ?
        ');
        $stmt->execute(['test@example.com']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Test User', $user['name']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS users');
        $this->pdo = null;
    }
}
