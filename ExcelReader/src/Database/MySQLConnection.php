<?php

namespace ExcelReader\Database;

use PDO;
use PDOException;

class MySQLConnection
{
    private PDO $connection;
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private int $port;

    public function __construct(
        string $host = 'localhost',
        string $database = 'excel_data',
        string $username = 'root',
        string $password = '',
        int $port = 3306
    ) {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }

    public function connect(): bool
    {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return true;
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        if (!isset($this->connection)) {
            $this->connect();
        }
        return $this->connection;
    }

    public function createTable(string $tableName, array $columns): bool
    {
        try {
            $columnDefinitions = [];
            foreach ($columns as $columnName => $columnType) {
                $columnDefinitions[] = "`{$columnName}` {$columnType}";
            }
            
            $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (" . implode(', ', $columnDefinitions) . ")";
            $this->getConnection()->exec($sql);
            return true;
        } catch (PDOException $e) {
            throw new \Exception("Table creation failed: " . $e->getMessage());
        }
    }

    public function insertData(string $tableName, array $data): bool
    {
        try {
            if (empty($data)) {
                return false;
            }

            $columns = array_keys($data[0]);
            $placeholders = ':' . implode(', :', $columns);
            $columnNames = '`' . implode('`, `', $columns) . '`';
            
            $sql = "INSERT INTO `{$tableName}` ({$columnNames}) VALUES ({$placeholders})";
            $stmt = $this->getConnection()->prepare($sql);

            foreach ($data as $row) {
                $stmt->execute($row);
            }
            
            return true;
        } catch (PDOException $e) {
            throw new \Exception("Data insertion failed: " . $e->getMessage());
        }
    }

    public function close(): void
    {
        $this->connection = null;
    }
} 