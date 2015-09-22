<?php

class Auth
{
    private static $current_user = null;

    public static function get_current_user()
    {
        if (!self::$current_user && isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            
            $do = DB::get_pdo();
            $statement = $do->prepare('SELECT id, name, password, salt FROM users WHERE name = ?');
            $statement->execute([$username]);
            $user = $statement->fetchObject();
            if ($user && sha1($password . $user->salt) == $user->password)
                self::$current_user = $user;
        }
        return self::$current_user;
    }

    public static function register_user($user, &$validation_msgs)
    {
        if (Validator::is_valid($user, 'user', $validation_msgs)) {
            if (Validator::is_username_available($user->name, $validation_msgs)) {
                $salt = self::generateRandomString(40);
                $password = sha1($user->password . $salt);

                $do = DB::get_pdo();
                $statement = $do->prepare('INSERT users (name, password, salt) VALUES (?, ?, ?)');
                $statement->execute([$user->name, $password, $salt]);
                return true;
            }
        }
        return false;
    }

    private static function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
