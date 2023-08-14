<?php

namespace PHPDFS\Controller;

use PHPDFS\Util\TokenManager;
use PHPDFS\Filesystem\FileHandler;
use PHPDFS\Util\RequestUtil;
use PHPDFS\Database\DatabaseManager;

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

    public function upload($params){
        if (!empty($_FILES)){
            $fileName = $_FILES["file"]["name"];
            $response = FileHandler::uploadFile($_FILES["file"], $fileName);
            $uris = DatabaseManager::generateDownloadUri($response->uuid);
            $response["download"] = $uris;
            RequestUtil::response("Success upload file " . $fileName, $response, true);
        }else{
            if (isset($_GET["url"])){
                $url = explode('/', $_GET["url"]);
                $fileName = $url[count($url)-1];
                $response = FileHandler::uploadFileFromUrl($_GET["url"], $fileName);
                $uris = DatabaseManager::generateDownloadUri($response->uuid);
                $response["download"] = $uris;
                RequestUtil::response("Success upload file " . $fileName, $response, true);
            }else{
                RequestUtil::response("No files presented. Error uploading.", [], false);
            }
        }
    }

    public function get($params){
        $uuid = $params["uuid"];
        $isRight = DatabaseManager::checkSecretToken($uuid, $_GET["secret"]);
        if ($isRight){
            FileHandler::downloadFile($uuid);
        }
    }
}