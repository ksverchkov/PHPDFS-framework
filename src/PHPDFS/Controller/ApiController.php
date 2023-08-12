<?php

namespace PHPDFS\Controller;

use PHPDFS\Util\TokenManager;

class FileController
{
    public function createToken($params)
    {
        $token = TokenManager::generateToken();
    }
}