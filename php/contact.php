<?php

class Contact
{
    public static function get_user_contacts($user_id) {
        $do = DB::get_pdo();
        $statement = $do->prepare('SELECT id, name, email FROM contacts WHERE user_id = ?');
        $statement->execute([$user_id]);
        return $records = $statement->fetchAll(PDO::FETCH_CLASS, 'contact');
    }
}
