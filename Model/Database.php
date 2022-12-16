<?php

require_once('../debug.php');

class Database
{
    private static $connection;

    static function connection()
    {
        if (Database::$connection == null) {
            Database::$connection = new PDO('mysql:host=localhost;dbname=catalogue;charset=utf8', 'root', '');
        }
        return Database::$connection;
    }

    static function initDatabase(): int
    {
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
                `validated` BOOLEAN NOT NULL DEFAULT FALSE,
                PRIMARY KEY (`id`)
            );

            INSERT INTO `subscription` (`name`, `price`, `duration`) VALUES
            ("3 ticket par jour", 5, 30);

            INSERT INTO `subscription` (`name`, `price`, `duration`) VALUES
            ("8 ticket par jour", 15, 30);

            INSERT INTO `subscription` (`name`, `price`, `duration`) VALUES
            ("15 ticket par jour", 30, 30);


            CREATE TABLE IF NOT EXISTS `user` (
                `id` INT NOT NULL,
                `ticket` INT NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            );

            INSERT INTO `user` (`id`, `ticket`) VALUES
            (1,5);

            INSERT INTO `user` (`id`, `ticket`) VALUES
            (2,0);

            INSERT INTO `user` (`id`, `ticket`) VALUES
            (3,13);

            INSERT INTO `user` (`id`, `ticket`) VALUES
            (4,115);

            INSERT INTO `user` (`id`, `ticket`) VALUES
            (5,15);

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
                `validated` BOOLEAN NOT NULL DEFAULT FALSE,
                PRIMARY KEY (`id`)
            );


            INSERT INTO `skin` (`name`, `price`) VALUES
            ("pepo rouge ", 5);

            INSERT INTO `skin` (`name`, `price`) VALUES
            ("pepo infernal galactique ", 20.5);

            INSERT INTO `skin` (`name`, `price`) VALUES
            ("susan",13 );

            INSERT INTO `skin` (`name`, `price`) VALUES
            ("lamantin urf", 115);

            INSERT INTO `skin` (`name`, `price`) VALUES
            ("huit neuf", 15);

            CREATE TABLE IF NOT EXISTS `user_skin` (
                `user_id` INT NOT NULL,
                `skin_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`user_id`, `skin_id`),
                FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
                FOREIGN KEY (`skin_id`) REFERENCES `skin`(`id`)
            );

            INSERT INTO `user_skin` (`user_id`, `skin_id`,`created_at`) VALUES
            (1,1 ,now());

            INSERT INTO `user_subscription` (`user_id`, `subscription_id`,`created_at`) VALUES
            (1,1 ,now());
        ';
        $query = Database::connection()->prepare($sql);
        return $query->execute();
    }

    /**
     * Get all skins
     * @return array
     */
    static function querySkins(): array
    {
        $sql = '
            SELECT * FROM `skin`;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all subscriptions
     * @return array
     */
    static function querySubscriptions(): array
    {
        $sql = '
            SELECT * FROM `subscription`;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users
     * @return array
     */
    static function getTickets(int $userId): int
    {
        $sql = '
            SELECT `ticket` FROM `user` WHERE `id` = :userId;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute(['userId' => $userId]);
        return $query->fetch(PDO::FETCH_ASSOC)['ticket'] ?? 0;
    }

    /**
     * Get all subscriptions of a user
     * @param int $userId
     * @return array
     */
    static function getSubscriptions(int $userId): array
    {
        $sql = '
            SELECT `subscription`.`id`, `subscription`.`name`, `subscription`.`price`, `subscription`.`duration`, `user_subscription`.`created_at` FROM `user_subscription`
            INNER JOIN `subscription` ON `user_subscription`.`subscription_id` = `subscription`.`id`
            WHERE `user_subscription`.`user_id` = :userId
            AND `subscription`.`validated` = TRUE;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute(['userId' => $userId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all skins of a user
     * @param int $userId
     * @return array
     */
    static function getSkins(int $userId): array
    {
        $sql = '
            SELECT `skin`.`id`, `skin`.`name`, `skin`.`price`, `user_skin`.`created_at` FROM `user_skin`
            INNER JOIN `skin` ON `user_skin`.`skin_id` = `skin`.`id`
            WHERE `user_skin`.`user_id` = :userId
            AND `skin`.`validated` = TRUE;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute(['userId' => $userId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    static function setSkinsToUser(int $userId, int $skinId)
    {
        $date = date('d-m-y');
        $sql = '
        INSERT INTO `user_skin` (`user_id`, `skin_id`,`created_at`) VALUES
        (:userId,:skinId ,:date);

        ';
        $query = Database::connection()->prepare($sql);
        $query->execute([":userId" => $userId, ":skinId" => $skinId, ":date" => $date]);
    }

    static function setSubscriptionToUser(int $userId, int $subscriptionId)
    {
        $date = date('d-m-y');
        $sql = '
        INSERT INTO `user_subscription` (`user_id`, `user_subscription`,`created_at`) VALUES
        (:userId,:subscriptionId ,:date);

        ';
        $query = Database::connection()->prepare($sql);
        $query->execute([":userId" => $userId, ":skinId" => $subscriptionId, ":date" => $date]);
    }

    static function setTicketToUser(int $userId, $nbTicket)
    {
        $sql =
            '
        UPDATE user SET ticket = ? WHERE id = ?;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute([$userId, $nbTicket]);
    }
    static function buyTicket(int $userId)
    {
        $sql =
            '
        UPDATE user SET ticket = ticket + 1 WHERE id = ?;
        ';
        $query = Database::connection()->prepare($sql);
        $query->execute([$userId]);
    }

    static function consumeTicket(int $userId)
    {
        $sql =
            '
        UPDATE user SET ticket = ticket - 1 WHERE id = ?;
        ';

        $query = Database::connection()->prepare($sql);
        $query->execute([$userId]);
    }

    static function existUser(int $userId)
    {
        $sql =
            '
        SELECT * FROM user WHERE id = ?;
        ';

        $query = Database::connection()->prepare($sql);
        $query->execute([$userId]);
        $taille = $query->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($taille) == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    static function setUser(int $userId)
    {
        if (!existUser($userId)) {
            $sql = '
        INSERT INTO `user` (`id`, `ticket`) VALUES
        (:userId,0);
        ';

            $query = Database::connection()->prepare($sql);
            $query->execute([":userId" => $userId]);
        }
    }
}
