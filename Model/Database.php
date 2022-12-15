<?php

class Database {
    private static $connection;

    static function connection() {
        if (Database::$connection == null) {
            Database::$connection = new PDO('mysql:host=localhost;dbname=catalogue;charset=utf8', 'root', '');
        }
        return Database::$connection;
    }

    static function initDatabase() : int {
        $sql = '
            DROP TABLE IF EXISTS `user_skin`;
            DROP TABLE IF EXISTS `skin`;
            DROP TABLE IF EXISTS `user_subscription`;
            DROP TABLE IF EXISTS `user`;
            DROP TABLE IF EXISTS `subscription`;

            CREATE TABLE IF NOT EXISTS `subscription` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `price` FLOAT NOT NULL,
                `duration` INT NOT NULL,
                PRIMARY KEY (`id`)
            );

            INSERT INTO `subscription` (`name`, `price`, `duration`) VALUES
            ("3 ticket par jour", 5, 30);

            CREATE TABLE IF NOT EXISTS `user` (
                `id` INT NOT NULL,
                `ticket` INT NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            );

            CREATE TABLE IF NOT EXISTS `user_subscription` (
                `user_id` INT NOT NULL,
                `subscription_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`user_id`, `subscription_id`),
                FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
                FOREIGN KEY (`subscription_id`) REFERENCES `subscription`(`id`)
            );

            CREATE TABLE IF NOT EXISTS `skin` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `price` FLOAT NOT NULL,
                PRIMARY KEY (`id`)
            );

            CREATE TABLE IF NOT EXISTS `user_skin` (
                `user_id` INT NOT NULL,
                `skin_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`user_id`, `skin_id`),
                FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
                FOREIGN KEY (`skin_id`) REFERENCES `skin`(`id`)
            );
        ';
        $query = Database::connection()->prepare($sql);
        return $query->execute();
    }

    static function querySkins() : array {
        $sql = '
            SELECT * FROM `skin`;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

