<?php

namespace PHPDFS\Util;

class TokenManager
{
    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    public static function generateSecretKey(){
        return bin2hex(random_bytes(43));
    }
    
}