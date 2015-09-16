<?php
require '../../php/config.php';
require '../../php/db.php';
require '../../php/auth.php';
require '../../php/contact.php';

function request_object() {
    $json = file_get_contents('php://input');
    return json_decode($json);
}

function response_object($object) {
    return json_encode($object);
}


$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


if ($method == 'GET' && $uri == '/api/login') {
    if (!Auth::get_current_user()) {
        http_response_code(401); //  Unauthorized
    }
}
elseif ($method == 'POST' && $uri == '/api/register') {
    $user = request_object();
    if (!Auth::register_user($user)) {
        http_response_code(400); //  Bad Request
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

