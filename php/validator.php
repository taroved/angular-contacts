<?php

class Validator
{
    private static $schema = [
        'user' => [
            'name' => ['required' => null, 'minlength' => 3, 'maxlength' => 60, 'pattern' => '/^[\\da-zA-Z_]*$/'],
            'password' => ['required' => null, 'minlength' => 3]
        ],
        'contact' => [
            'name' => ['required' => null, 'minlength' => 3, 'maxlength' => 60],
            'email' => ['required' => null, 'minlength' => 3, 'maxlength' => 60, 'email' => null]
        ]
    ];

    public static function is_valid($obj, $model_name, &$validation_messages)
    {
        $result = true;
        foreach (self::$schema[$model_name] as $field => $restrictions) {
            if (isset($restrictions['required']) && !isset($obj->$field)) {
                array_push($validation_messages, "$field is required");
                return false;
            }
            else if (isset($obj->$field)) {
                foreach ($restrictions as $restriction_name => $restriction_value) {
                    $msg = "";
                    if (!self::is_field_valid($restriction_name, $restriction_value, $field, $obj->$field, $msg)) {
                        array_push($validation_messages, $msg);
                        $result = false;
                    }
                }
            }
        }
        return $result;
    }

    private static function is_field_valid($restriction_name, $restriction_value, $field, $value, &$msg){
        $result = true;

        switch ($restriction_name) {
        case 'minlength': $result = strlen($value) >= $restriction_value; $msg = "$field should be at least $restriction_value characters"; break;
        case 'maxlength': $result = strlen($value) <= $restriction_value; $msg = "$field should be should be no more then $restriction_value characters"; break;
        case 'pattern':
            $result = preg_match($restriction_value, $value);
            if ($result === 0) {
                $msg = "$field should match $restriction_value regexp"; break;
                $result = false;
            }
            elseif ($result === false)
                throw Exception("Regex is invalid");
            break;
        case 'email': $result = filter_var($value, FILTER_VALIDATE_EMAIL); $msg = "$field should be email"; break;
        case 'required': // already checked
            break;
        default: throw Exception('Invalid restriction name');
        }

        return $result;
    }

    public static function is_username_available($username, &$validation_messages=null)
    {
        $do = DB::get_pdo();
        $statement = $do->prepare('SELECT id FROM users WHERE name = ?');
        $statement->execute([$username]);
        $result = $statement->fetchObject();

        if ($result && $validation_messages !== null)
            array_push($validation_messages, 'User name already exists');
        
        return !$result;
    }
}
