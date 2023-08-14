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

    public static function getCurrentServer(){
        $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? 'https' : 'http';
        $host = $_SERVER["HTTP_HOST"];
        return "$protocol://$host$uri";
    }

    public static function getCurrentPath(){
        return $_SERVER["REQUEST_URI"];
    }

    public static function response($message, $data=[], $success=true){
        if ($success){
            $status = 'success';
        }else{
            $status = 'failed';
        }
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        echo json_encode($response);
        http_response_code(200);
        exit;
    }
}