<?php

class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    public static function instance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function pdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->connect();
        }
        return $this->pdo;
    }

    private function connect(): PDO
    {
        $driver = DB_DRIVER;

        if ($driver === 'sqlite') {
            $pdo = new PDO('sqlite:' . DB_SQLITE_PATH);
        } elseif ($driver === 'mysql') {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
        } elseif ($driver === 'pgsql') {
            $dsn = 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
        } else {
            throw new RuntimeException('Неизвестный драйвер базы данных: ' . $driver);
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if ($driver === 'sqlite') {
            $pdo->exec('PRAGMA foreign_keys = ON');
            $this->initSqlite($pdo);
        }

        return $pdo;
    }

    private function initSqlite(PDO $pdo): void
    {
        $dump = file_get_contents(__DIR__ . '/../database/dump.sql');
        if ($dump !== false && trim($dump) !== '') {
            $pdo->exec($dump);
        }

        $columns = $pdo->query('PRAGMA table_info(applications)')->fetchAll(PDO::FETCH_ASSOC);
        $hasPetId = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'pet_id') {
                $hasPetId = true;
                break;
            }
        }
        if (!$hasPetId) {
            $pdo->exec('ALTER TABLE applications ADD COLUMN pet_id INTEGER REFERENCES pets(id)');
        }
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId(): int
    {
        return (int)$this->pdo()->lastInsertId();
    }
}
