<?php
include_once('vendor/autoload.php');
use PHPDFS\Routing\Router;
use PHPDFS\Util\DatabaseManager;
use PHPDFS\Util\TokenManager;
use PHPDFS\Util\ExceptionHandler;
use PHPDFS\Util\RequestUtil;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);

DatabaseManager::setupDatabase($dotenv);

$router = new Router(__DIR__ . '/routes.yaml');
$token = RequestUtil::getToken();
$path = RequestUtil::getCurrentPath();

$router->route($path, $token);