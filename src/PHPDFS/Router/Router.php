<?php

declare(strict_types = 1);

namespace PHPDFS\Router;

use PHPDFS\Router\RouterInterface;

class Router implements RouterInterface
{

    /**
     * return an array of route from our routing table
     * @var array
     */
    protected array $routes = [];

    /**
     * return an array of route parameters
     * @var array
     */
    protected array $params = [];

    /**
     * Adds a suffix onto the controller name
     * @var string
     */
    protected string $controllerSuffix = 'controller';

    /**
     * @inheritDoc
     */
    public function add(string $route, array $params= []): void
    {
        $this->routes[$route] = $params;
    }

    public function dispatch(string $url) : void
    {

    }

    /**
     * m
     */
    private function match(string $url): bool
    {
        
    }
}