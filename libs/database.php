<?php
require_once 'config.php';
class Database
{
    private static $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    private static $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );
    private static $db;
    public static function connect()
    {
        if (!isset(self::$db)) {
            try {
                self::$db = new PDO(self::$dsn, DB_USER, DB_PASS, self::$options);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$db;
    }
    public static function disconnect()
    {
        self::$db = null;
    }
}