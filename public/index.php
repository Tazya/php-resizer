<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    $payload = json_encode(['status' => 'ok', 'message' => 'Kolesa Academy!']);
    $response->getBody()->write($payload);

    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8');
});

$app->run();
