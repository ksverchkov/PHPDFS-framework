<?php

namespace PHPDFS\Controller;

use PHPDFS\Util\TokenManager;

class FileController
{
    public function show($params)
    {
        $uuid = $params['uuid'];
    }

    public function search($params){
        $uuid = $params['uuid'];
        echo $uuid;
    }
}