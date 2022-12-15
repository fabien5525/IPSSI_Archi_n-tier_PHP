<?php

class Database {

    private static $connection;

    static function connection() {
        if (Database::$connection == null) {
            Database::$connection = new PDO('mysql:host=localhost;dbname=catalogue;charset=utf8', 'root', '');
        }
        return Database::$connection;
    }
}

