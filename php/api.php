<?php
require 'config.php';
require 'db.php';
require 'auth.php';
require 'contact.php';
require 'validator.php';

class Api
{
    public static $validation_msgs = [];
    public static $authorized = false;

    public static function login() {
        return !!Auth::get_current_user();
    }

    public static function register_user($user) {
        return Auth::register_user($user, self::$validation_msgs);
    }

    public static function check_username($name) {
        return (object)['inuse' => !Validator::is_username_available($name)];
    }

    public static function contacts() {
        $user = Auth::get_current_user();
        self::$authorized = !!$user;
        if ($user)
            return Contact::get_user_contacts($user->id);
        else
            return [];
    }

    public static function get_contact($id) {
        $user = Auth::get_current_user();
        self::$authorized = !!$user;
        if ($user)
            return Contact::get_user_contact($user->id, $id);
        else
            return null;
    }

    public static function create_contact($contact) {
        $user = Auth::get_current_user();
        self::$authorized = !!$user;
        $result = null;
        if ($user)
            if (Validator::is_valid($contact, 'contact', self::$validation_msgs))
                $result = (object)['id' => Contact::create_user_contact($user->id, $contact)];
        return $result;
    }

    public static function update_contact($contact) {
        $user = Auth::get_current_user();
        self::$authorized = !!$user;
        if ($user && Validator::is_valid($contact, 'contact', self::$validation_msgs))
            return (object)['OK' => Contact::update_user_contact($user->id, $contact)];
        else
            return null;
    }

    public static function delete_contact($id) {
        $user = Auth::get_current_user();
        self::$authorized = !!$user;
        if ($user)
            return (object)['OK' => Contact::delete_user_contact($user->id, $id)];
        else
            return null;
    }
}
