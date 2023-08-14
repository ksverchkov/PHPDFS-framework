<?php
include_once('vendor/autoload.php');

use PHPDFS\Routing\Router;
use PHPDFS\Database\DatabaseManager;
use PHPDFS\Util\TokenManager;
use PHPDFS\Util\ExceptionHandler;
use PHPDFS\Util\RequestUtil;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);

DatabaseManager::setupDatabase($dotenv);
if (!array_key_exists('CURRENT_SERVER', $_ENV)){
    $env_file = file_get_contents(__DIR__ . '/.env');
    $env_file = $env_file . "\nCURRENT_SERVER=" . RequestUtil::getCurrentServer();
    file_put_contents(__DIR__ . '/.env', $env_file);
}

$router = new Router(__DIR__ . '/routes.yaml');
$token = RequestUtil::getToken();
$path = RequestUtil::getCurrentPath();

$router->route($path, $token);