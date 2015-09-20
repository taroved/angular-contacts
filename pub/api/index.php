<?php
require '../../php/config.php';
require '../../php/db.php';
require '../../php/auth.php';
require '../../php/contact.php';
require '../../php/validator.php';


function request_object() {
    $json = file_get_contents('php://input');
    return json_decode($json);
}

function response_object($object) {
    return json_encode($object);
}

function header_failed_validation() {
    http_response_code(400); //  Bad Request
    header('X-Status-Reason: Validation failed');
}


$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


if ($method == 'POST' && $uri == '/api/login') {
    if (!Auth::get_current_user()) {
        http_response_code(401); //  Unauthorized
    }
}
elseif ($method == 'POST' && $uri == '/api/register') {
    $user = request_object();
    if (!Auth::register_user($user)) {
        header_failed_validation();
    }
}
elseif ($method == 'GET' && $uri == '/api/contacts') {
    $user = Auth::get_current_user();
    if ($user) {
        echo response_object(Contact::get_user_contacts($user->id));
    }
    else {
        http_response_code(401); //  Unauthorized
    }
}
else
{
    http_response_code(404); //  Not found`
}
