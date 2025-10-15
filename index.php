<?php
//  REST API
// Usage: POST /order/calc with JSON body
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

// --------------------------------------------------
//  router
// --------------------------------------------------
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/api/users' && $method === 'GET') {
    (new \App\Controllers\UserController())->index();
    exit;
}
if ($uri === '/api/user/' && $method === 'GET') {
    (new \App\Controllers\UserController())->one();
    exit;
}
if ($uri === '/api/user' && $method === 'POST') {
    (new \App\Controllers\UserController())->create();
    exit;
}
if ($uri === '/api/user' && $method === 'PATH') {
    (new \App\Controllers\UserController())->update();
    exit;
}
if ($uri === '/api/user' && $method === 'DELETE') {
    (new \App\Controllers\UserController())->delete();
    exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Not Found'], JSON_THROW_ON_ERROR);
exit;