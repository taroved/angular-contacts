<?php

class DB
{
    private static $pdo = null;

    public static function get_pdo()
    {
        if (!self::$pdo) {
            global $config;
            $dbconf = $config['mysql'];
            self::$pdo = new PDO('mysql:host='. $dbconf['host'] .';dbname='. $dbconf['database'],
                $dbconf['username'], $dbconf['password'], array(PDO::ATTR_PERSISTENT => true));
        }
        return self::$pdo;
    }
}
