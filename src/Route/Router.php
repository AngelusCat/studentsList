<?php

namespace App\Route;

class Router
{

    /**
     *@var array<int, Route> массив экземпляров класса Route, каждый из которых представляет собой маршрут
     */

    private readonly array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function route(string $path): ?Route
    {
        $result = "";
        foreach ($this->routes as $route) {
            if ($route->getUrl() === $path) {
                $result = $route;
            }
        }

        if ($result) {
            return $result;
        } else {
            return null;
        }
    }
}