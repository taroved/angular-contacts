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

function response_failed_validation($messages) {
    http_response_code(400); //  Bad Request
    header('X-Status-Reason: Validation failed');
    echo response_object($messages);
}


$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

////////////
// ROUTING
////////////

if ($method == 'POST' && $path == '/api/login') {
    if (!Auth::get_current_user()) {
        http_response_code(401); //  Unauthorized
    }
}
elseif ($method == 'POST' && $path == '/api/register') {
    $user = request_object();
    $validation_msgs = array();
    if (!Auth::register_user($user, $validation_msgs)) {
        response_failed_validation($validation_msgs);
    }
}
elseif ($method == 'GET' && $path =='/api/check_username') {
    echo response_object((object)['inuse' => !Validator::is_username_available($_GET['name'])]);
}
elseif ($method == 'GET' && $path == '/api/contacts') {
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
