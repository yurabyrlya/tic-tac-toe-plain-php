<?php


namespace App\Model;


class Migration
{
    /**
     * @return DB
     */
    public static function db():DB {
        $config = include __DIR__. '/../config.php';
        return new DB(
            $config['db_name'],
            $config['username'],
            $config['password'],
            $config['host'],
            $config['port']
        );
    }

    public static function execute(){
        $config = include __DIR__. '/../config.php';
        self::playerSchema(self::db(),  $config['db_name']);
        self::gameSchema(self::db(),  $config['db_name']);
        //self::resultSchema(self::db(),  $config['db_name']);

    }

    /**
     * @param DB $db
     * @param $dbName
     */
    private static function playerSchema(DB $db, $dbName){
        $playerSchema = "
        CREATE TABLE IF NOT EXISTS  `$dbName`.`player` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(45) NOT NULL,
                    PRIMARY KEY (`id`));
        ";

        $db->run($playerSchema);
    }

    /**
     * future version 1.2
     * @param DB $db
     * @param $dbName
     */
    private static function resultSchema(DB $db, $dbName){

        $resultSchema = "
              CREATE TABLE IF NOT EXISTS  `$dbName`.`results` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `player_id` INT NOT NULL,
                  `is_won` TINYINT NOT NULL DEFAULT 0,
                  PRIMARY KEY (`id`),
                  INDEX `player_idx` (`player_id` ASC),
                  CONSTRAINT `player_result`
                    FOREIGN KEY (`player_id`)
                    REFERENCES `$dbName`.`player` (`id`)
                    ON DELETE CASCADE
                  );
        ";

        $db->run($resultSchema);
    }


    /**
     * @param DB $db
     * @param $dbName
     */
    private static function gameSchema(DB $db, $dbName){

        $gameSchema = "
              CREATE TABLE IF NOT EXISTS  `$dbName`.`game` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `player_id` INT NOT NULL,
                  `pl_x` INT DEFAULT NULL ,
                  `pl_y` INT DEFAULT NULL,
                  `cp_x` INT DEFAULT NULL,
                  `cp_y` INT DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  INDEX `player_idx` (`player_id` ASC),
                  CONSTRAINT `player`
                    FOREIGN KEY (`player_id`)
                    REFERENCES `$dbName`.`player` (`id`)
                    ON DELETE CASCADE
                  );
        ";

        $db->run($gameSchema);
    }
}