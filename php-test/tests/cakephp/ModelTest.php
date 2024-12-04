<?php
namespace Tests\CakePHP;

use PHPUnit\Framework\TestCase;
use PDO;

class ModelTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO(
            sprintf(
                "pgsql:host=%s;port=%s;dbname=%s",
                'postgres',
                '5432',
                getenv('CAKE_PGSQL_TEST_DB')
            ),
            getenv('TEST_USER'),
            getenv('TEST_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Create test table
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS products (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    public function testCanCreateProduct()
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO products (name, price) VALUES (?, ?)
        ');
        $result = $stmt->execute(['Test Product', 99.99]);
        $this->assertTrue($result);

        $lastId = $this->pdo->lastInsertId('products_id_seq');
        $this->assertNotEmpty($lastId);
    }

    public function testCanRetrieveProduct()
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM products WHERE name = ?
        ');
        $stmt->execute(['Test Product']);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(99.99, floatval($product['price']));
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS products');
        $this->pdo = null;
    }
}
