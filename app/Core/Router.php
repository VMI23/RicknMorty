<?php

declare(strict_types=1);

namespace RickAndMorty\Core;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

class Router
{
    public static function response(array $routes): ?TwigView
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                [$httpMethod, $uri, $handler] = $route;
                $r->addRoute($httpMethod, $uri, $handler);
            }
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return null;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                return null;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2]; // Parameters from query

                [$controllerName, $methodName] = $handler;
                $controller = new $controllerName;

                // Can pass the array of query parameters to the controller method using the splat operator
                return $controller->{$methodName}($vars);
        }

        return null;
    }
}