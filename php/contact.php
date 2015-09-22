<?php

class Contact
{
    public static function get_user_contacts($user_id) {
        $do = DB::get_pdo();
        $statement = $do->prepare('SELECT id, name, email FROM contacts WHERE user_id = ?');
        $statement->execute([$user_id]);
        return $statement->fetchAll(PDO::FETCH_CLASS, 'contact');
    }

    public static function get_user_contact($user_id, $id) {
        $do = DB::get_pdo();
        $statement = $do->prepare('SELECT id, name, email FROM contacts WHERE user_id = ? && id = ?');
        $statement->execute([$user_id, $id]);
        return $statement->fetchObject();
    }

    public static function create_user_contact($user_id, $contact) {
        $do = DB::get_pdo();
        $statement = $do->prepare('INSERT INTO contacts (user_id, name, email) VALUES (?, ?, ?)');
        $statement->execute([$user_id, $contact->name, $contact->email]);
        return $do->lastInsertId();
    }

    public static function update_user_contact($user_id, $contact) {
        $do = DB::get_pdo();
        $statement = $do->prepare('UPDATE contacts SET name = ?, email = ? WHERE user_id = ? AND id = ?');
        return $statement->execute([$contact->name, $contact->email, $user_id, $contact->id]);
    }

    public static function delete_user_contact($user_id, $id) {
        $do = DB::get_pdo();
        $statement = $do->prepare('DELETE FROM contacts WHERE user_id = ? AND id = ?');
        return $statement->execute([$user_id, $id]);
    }
}
