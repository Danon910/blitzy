<?php

declare(strict_types=1);

namespace Danon910\blitzy;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class RouteFinder
{
    public function __construct(
        private readonly Router $route,
    )
    {
    }

    public function getRoute(string $class_name, string $method_name): ?Route
    {
        $routes = $this->route->getRoutes();

        foreach ($routes as $route) {
            $action = $route->getAction();

            if (str_contains($action['controller'] ?? '', sprintf("%s@%s", class_basename($class_name), $method_name))) {
                return $route;
            }
        }

        return null;
    }
}
