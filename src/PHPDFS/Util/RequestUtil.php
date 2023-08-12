<?php

namespace PHPDFS\Util;

class RequestUtil{
    public static function getToken(){
        $token = null;

        $authHeader = $_SERVER["Authorization"] ?? null;
        if ($authHeader && preg_match('/Bearer\s+(.*)/', $authHeader, $matches)) {
            $token = $matches[1];
        }

        if (!$token && isset($_POST["token"])){
            $token = $_POST["token"];
        }

        if (!$token && isset($_GET["token"])){
            $token = $_GET["token"];
        }

        return $token;
    }

    public static function getCurrentUrl(){
        $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? 'https' : 'http';
        $host = $_SERVER["HTTP_HOST"];
        $uri = $_SERVER["REQUEST_URI"];

        return "$protocol://$host$uri";
    }

    public static function getCurrentPath(){
        return $_SERVER["REQUEST_URI"];
    }
}