<?php

namespace PHPDFS\Util;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    public static function parseFile($filePath){
        try{
            $content = file_get_contents($filePath);
            $data = Yaml::parse($content);
            return $data;
        }catch (\Exception $e){
            return [];
        }
    }
}