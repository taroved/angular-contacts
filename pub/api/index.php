<?php
require '../../php/api.php';

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
    if (!Api::login()) {
        http_response_code(401); //  Unauthorized
    }
}
elseif ($method == 'POST' && $path == '/api/register') {
    $user = request_object();
    if (!Api::register_user($user)) {
        response_failed_validation(Api::$validation_msgs);
    }
}
elseif ($method == 'GET' && $path =='/api/check_username') {
    echo response_object(Api::check_username($_GET['name']));
}
elseif ($method == 'GET' && $path == '/api/contacts') {
    $contacts = Api::contacts();
    if (Api::$authorized) {
        echo response_object($contacts);
    }
    else {
        http_response_code(401); //  Unauthorized
    }
}
elseif ($method == 'GET' && preg_match('/^\/api\/contacts\/\d+$/', $path)) {
    $matches = null;
    preg_match('/^\/api\/contacts\/(\d+)$/', $path, $matches);
    $contact = Api::get_contact($matches[1]);
    if (Api::$authorized) {
        if ($contact)
            echo response_object($contact);
        else
            http_response_code(404); //  Not found
    }
    else {
        http_response_code(401); //  Unauthorized
    }
}
elseif ($method == 'POST' && $path == '/api/contacts') {
    $contact = Api::create_contact(request_object());
    if (Api::$authorized)
        if (!Api::$validation_msgs)
            echo response_object($contact);
        else
            response_failed_validation(Api::$validation_msgs);
    else
        http_response_code(401); //  Unauthorized
}
elseif ($method == 'PUT' && $path == '/api/contacts') {
    $contact = Api::update_contact(request_object());
    if (Api::$authorized)
        if (!Api::$validation_msgs)
            echo response_object($contact);
        else
            response_failed_validation(Api::$validation_msgs);
    else
        http_response_code(401); //  Unauthorized
}
elseif ($method == 'DELETE' && preg_match('/^\/api\/contacts\/\d+$/', $path)) {
    $matches = null;
    preg_match('/^\/api\/contacts\/(\d+)$/', $path, $matches);
    $result = Api::delete_contact($matches[1]);
    if (Api::$authorized)
        echo response_object($result);
    else
        http_response_code(401); //  Unauthorized
}
else
{
    http_response_code(404); //  Not found`
}
