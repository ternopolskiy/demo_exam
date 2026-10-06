<?php

class Application
{
    public const STATUS_NEW = 'Новая';
    public const STATUS_CONFIRMED = 'Подтверждена';
    public const STATUS_COMPLETED = 'Приём завершён';
    public const STATUS_CANCELLED = 'Отменена';

    public const TRANSITIONS = [
        self::STATUS_NEW => [self::STATUS_CONFIRMED],
        self::STATUS_CONFIRMED => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_COMPLETED => [],
        self::STATUS_CANCELLED => [],
    ];

    public const SERVICES = ['первичный осмотр', 'вакцинация', 'стрижка когтей', 'УЗИ'];
    public static function create(array $data): int
    {
        db()->query(
            'INSERT INTO applications (user_id, pet_id, pet_name, species, service, date, time, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'],
                $data['pet_id'],
                $data['pet_name'],
                $data['species'],
                $data['service'],
                $data['date'],
                $data['time'],
                $data['payment_method'],
                self::STATUS_NEW,
            ]
        );
        return db()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        return db()->fetchOne('SELECT * FROM applications WHERE id = ?', [$id]);
    }

    public static function forUser(int $userId): array
    {
        return db()->fetchAll(
            'SELECT * FROM applications WHERE user_id = ? ORDER BY id DESC',
            [$userId]
        );
    }

    private static function filtersWhere(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'a.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['service'])) {
            $where[] = 'a.service = ?';
            $params[] = $filters['service'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(a.pet_name LIKE ? OR u.full_name LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        return [$where, $params];
    }

    public static function all(array $filters = [], int $page = 1, int $perPage = 5): array
    {
        [$where, $params] = self::filtersWhere($filters);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT a.*, u.full_name, u.login
                FROM applications a
                JOIN users u ON u.id = a.user_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY a.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset;

        return db()->fetchAll($sql, $params);
    }

    public static function countAll(array $filters = []): int
    {
        [$where, $params] = self::filtersWhere($filters);

        $sql = 'SELECT COUNT(*) AS c
                FROM applications a
                JOIN users u ON u.id = a.user_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $row = db()->fetchOne($sql, $params);
        return (int)($row['c'] ?? 0);
    }

    public static function services(): array
    {
        $rows = db()->fetchAll('SELECT DISTINCT service FROM applications ORDER BY service');
        return array_column($rows, 'service');
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $application = self::find($id);
        if ($application === null) {
            return false;
        }
        if (!self::canTransition($application['status'], $status)) {
            return false;
        }
        db()->query('UPDATE applications SET status = ? WHERE id = ?', [$status, $id]);
        return true;
    }

    public static function cancelByUser(int $id, int $userId): bool
    {
        $application = self::find($id);
        if ($application === null) {
            return false;
        }
        if ((int)$application['user_id'] !== $userId || $application['status'] !== self::STATUS_NEW) {
            return false;
        }
        db()->query('UPDATE applications SET status = ? WHERE id = ?', [self::STATUS_CANCELLED, $id]);
        return true;
    }

    public static function statusSlug(string $status): string
    {
        $map = [
            self::STATUS_NEW => 'new',
            self::STATUS_CONFIRMED => 'confirmed',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_CANCELLED => 'cancelled',
        ];
        return $map[$status] ?? 'unknown';
    }
}
