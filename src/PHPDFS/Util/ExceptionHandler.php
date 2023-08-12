<?php

namespace PHPDFS\Utils;

class ExceptionHandler
{
    public static function handle($exception){
        $response = [
            'status' => 'failed',
            'message' => $exception->getMessage(),
        ];

        echo json_encode($response);
        http_response_code(200);
        exit;
    }
}