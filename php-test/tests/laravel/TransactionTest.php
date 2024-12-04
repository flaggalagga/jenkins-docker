<?php
namespace Tests\Laravel;

use PHPUnit\Framework\TestCase;
use PDO;

class TransactionTest extends TestCase
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

        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS accounts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                balance DECIMAL(10,2) NOT NULL
            )
        ');
    }

    public function testTransactionRollback()
    {
        // Initialize account
        $this->pdo->exec("INSERT INTO accounts (name, balance) VALUES ('Test Account', 100.00)");
        
        try {
            $this->pdo->beginTransaction();
            
            // Deduct money
            $stmt = $this->pdo->prepare('UPDATE accounts SET balance = balance - ? WHERE name = ?');
            $stmt->execute([50.00, 'Test Account']);
            
            // Simulate error
            throw new \Exception('Simulated error');
            
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
        }

        // Check balance hasn't changed
        $stmt = $this->pdo->query("SELECT balance FROM accounts WHERE name = 'Test Account'");
        $balance = $stmt->fetchColumn();
        $this->assertEquals(100.00, $balance);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS accounts');
        $this->pdo = null;
    }
}
