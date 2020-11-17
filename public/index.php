<?php

use Slim\Factory\AppFactory;
use App\Controllers\ResizeController;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', ResizeController::class);

$app->run();
