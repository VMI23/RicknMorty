<?php

declare(strict_types=1);

use RickAndMorty\Core\Renderer;
use RickAndMorty\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

$routes = require_once __DIR__ . '/../app/routes.php';
$response = Router::response($routes);
$renderer = new Renderer(__DIR__ . '/../app/Views');

echo $renderer->render($response);