<?php

class Validator
{
    public static function required(string $value, string $label): ?string
    {
        if (trim($value) === '') {
            return 'Поле «' . $label . '» обязательно для заполнения';
        }
        return null;
    }

    public static function login(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Логин обязателен для заполнения';
        }
        if (!preg_match('/^[A-Za-z0-9]{6,}$/', $value)) {
            return 'Логин: латиница и цифры, не менее 6 символов';
        }
        return null;
    }

    public static function password(string $value): ?string
    {
        if ($value === '') {
            return 'Пароль обязателен для заполнения';
        }
        if (mb_strlen($value) < 6) {
            return 'Пароль должен содержать не менее 6 символов';
        }
        return null;
    }

    public static function fullName(string $value): ?string
    {
        if (trim($value) === '') {
            return 'ФИО обязательно для заполнения';
        }
        if (!preg_match('/^[А-Яа-яЁё\s]+$/u', $value)) {
            return 'ФИО: только кириллица и пробелы';
        }
        return null;
    }

    public static function normalizePhone(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value);
        if (strlen($digits) === 11 && $digits[0] === '8') {
            $digits = '7' . substr($digits, 1);
        }
        if (strlen($digits) === 10 && $digits[0] === '9') {
            $digits = '7' . $digits;
        }
        if (strlen($digits) !== 11 || $digits[0] !== '7') {
            return null;
        }
        return '+7(' . substr($digits, 1, 3) . ')' . substr($digits, 4, 3) . '-' . substr($digits, 7, 2) . '-' . substr($digits, 9, 2);
    }

    public static function phone(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Телефон обязателен для заполнения';
        }
        if (self::normalizePhone($value) === null) {
            return 'Телефон в формате +7(XXX)XXX-XX-XX';
        }
        return null;
    }

    public static function date(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Дата обязательна для заполнения';
        }
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $value)) {
            return 'Дата в формате ДД.ММ.ГГГГ';
        }
        $date = DateTime::createFromFormat('d.m.Y', $value);
        if ($date === false || $date->format('d.m.Y') !== $value) {
            return 'Некорректная дата';
        }
        return null;
    }

    public static function time(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Время обязательно для заполнения';
        }
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value)) {
            return 'Время в формате ЧЧ:ММ';
        }
        return null;
    }

    public static function email(string $value): ?string
    {
        if (trim($value) === '') {
            return 'E-mail обязателен для заполнения';
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Некорректный адрес электронной почты';
        }
        return null;
    }
}
