<?php

/**
 * Автор: Диденко Алексей
 *
 * Дата реализации: 08.08.2022 14:00
 *
 * Дата изменения: 08.08.2022 16:00
 *
 * Утилита для работы с базой данных
*/

/**
 * Класс DB
 * 
 * Статический класс для подключения и работы с базой данных.
 * Методы:
 *  - connect(): подключение к базе данных если соединения ещё не существует
 *  - getConnection(): получение объекта соединения с базой данных и проверка существует ли соединение
 *  - disconnect(): закрытие соединения с базой данных если соединение существует
 */
class DB
{
    private static $host = 'localhost';
    private static $username = 'root';
    private static $password = '';
    private static $dbname = 'persons';
    private static $connection = NULL;

    public static function connect()
    {
        while (self::$connection == NULL || self::$connection->connect_errno)
        {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$connection = new mysqli(self::$host, self::$username, self::$password, self::$dbname);
            self::$connection->set_charset('utf8');
        }
    }

    public static function getConnection()
    {
        if (self::$connection == NULL || self::$connection->connect_errno)
        {
            throw new RuntimeException('Connection is not opened!');
        }
        return self::$connection;
    }

    public static function disconnect()
    {
        if (self::$connection == NULL || self::$connection->connect_errno)
        {
            throw new RuntimeException('Connection is not opened!');
        }
        self::$connection->close();
    }
}
