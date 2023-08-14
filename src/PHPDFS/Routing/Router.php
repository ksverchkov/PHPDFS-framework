<?php

namespace PHPDFS\Routing;

use PHPDFS\Util\YamlParser;
use PHPDFS\Util\TokenManager;
use PHPDFS\Database\DatabaseManager;
use PHPDFS\Util\RequestUtil;

class Router
{
    protected $routes = [];

    public function __construct($configFile = 'examples/routes.yaml'){
        $config = YamlParser::parseFile($configFile);
        $this->routes = $config['routes'];
    }

    public function route($url, $token)
    {
        $url = explode('?', $url)[0];
        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['path']);
            if (preg_match($pattern, $url, $matches)) {
                if ($this->isAuthorized($route, $token)) {
                    list($controllerName, $action) = explode('@', $route['action']);
                    $controllerClass = 'PHPDFS\\Controller\\' . $controllerName;
                    $controller = new $controllerClass();
                    $params = $this->extractParams($matches);
                    try{
                        $controller->$action($params);
                    }catch (\Exception $e){
                        ExceptionHandler::handle($e);
                    }
                    return;
                } else {
                    header('HTTP/1.0 403 Forbidden');
                    RequestUtil::response("Unauthorized", [], $success=false);
                    return;
                }
            }
        }
        header('HTTP/1.0 404 Not found');
        echo '{"status":"failed", "message": "Not found"}';
        return;
    }

    protected function convertToRegex($path)
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    protected function extractParams($matches)
    {
        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    protected function isAuthorized($route, $token)
    {
        if (isset($route['auth']) && $route['auth'] === true) {
            $permissionsRequired = $route['permissions'] ?? [];
            $userPermissions = DatabaseManager::getUserPermissions($token);
            if (count($userPermissions) > 0){
                foreach ($permissionsRequired as $permission) {
                    if (!in_array($permission, $userPermissions)) {
                        return false;
                    }
                }
            }else{
                return false;
            }
            
            return true;
        }
        return true;
    }
}