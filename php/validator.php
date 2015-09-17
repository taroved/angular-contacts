<?php

class Validator
{
    public static function is_username_available($username)
    {
        $do = DB::get_pdo();
        $statement = $do->prepare('SELECT id FROM users WHERE name = ?');
        $statement->execute([$username]);
        return !$statement->fetchObject();
    }
}
