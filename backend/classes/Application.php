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

    public static function create(array $data): int
    {
        db()->query(
            'INSERT INTO applications (user_id, pet_name, species, service, date, time, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'],
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

    public static function all(): array
    {
        return db()->fetchAll(
            'SELECT a.*, u.full_name, u.login
             FROM applications a
             JOIN users u ON u.id = a.user_id
             ORDER BY a.id DESC'
        );
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
