<?php

class User
{
    public static function findByLogin(string $login): ?array
    {
        return db()->fetchOne('SELECT * FROM users WHERE login = ?', [$login]);
    }

    public static function findById(int $id): ?array
    {
        return db()->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function loginExists(string $login): bool
    {
        return db()->fetchOne('SELECT id FROM users WHERE login = ?', [$login]) !== null;
    }

    public static function create(array $data): int
    {
        db()->query(
            'INSERT INTO users (login, password_hash, full_name, phone, email) VALUES (?, ?, ?, ?, ?)',
            [
                $data['login'],
                $data['password_hash'],
                $data['full_name'],
                $data['phone'],
                $data['email'],
            ]
        );
        return db()->lastInsertId();
    }
}
