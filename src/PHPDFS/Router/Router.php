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

    /**
     * @inheritDoc
     */
    public function dispatch(string $url) : void
    {
        if ($this->match($url)){
            $controllerString = $this->params["controller"];
            $controllerString = $this->transformUpperCamelCase($controllerString);
            $controllerString = $this->getNamespace($controllerString);
        }
    }

    public function transformUpperCamelCase(string $string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Match the route to the routes in the routing table, setting the $this->params property
     * if a route is found 
     * 
     * @param string $url
     * @return bool
     */
    private function match(string $url): bool
    {
        foreach ($this->routes as $route => $params){
            if (preg_match($route, $url, $matches)){
                foreach ($matches as $key => $param){
                    if (is_string($key)){
                        $params[$key] = $param;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Get the namespace for the controller class, the name space defined within the route parameters
     * @param string $string
     * @return string
     */
    public function getNamespace(string $string) : string
    {
        $namespace = 'App\Controller\\';
        if (array_key_exists('namespace', $this->params)){
            $namespace .= $this->params["namespace"] . '\\';
        }
        return $namespace;
    }
}