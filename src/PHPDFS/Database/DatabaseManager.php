<?php

namespace PHPDFS\Database;

use RedBeanPHP\R as R;
use PHPDFS\Util\TokenManager;
use Dotenv\Dotenv;

class DatabaseManager
{
    public static function setupDatabase($dotenv)
    {
        $dotenv->load();
        $dbConnection = $_ENV['DB_CONNECTION'];
        $dbName = $_ENV['DB_NAME'];
        R::setup("{$dbConnection}:{$dbName}");
        self::createTables();
        self::addInitialPermissions();
        self::createRandomToken();
    }

    public static function createTables()
    {
        if (!R::testConnection())
        {
            return;
        }

        // Create 'permissions' table if not exists
        if (!R::testConnection() || !R::inspect('permissions')){
            $permissionsTable = R::dispense('permissions');
            $permissionsTable->name = '';
            R::store($permissionsTable);
            $nullRow = R::findOne('permissions', 'id = 1');
            R::trash($nullRow);
        }

        // Create 'tokens' table if not exists
        if (!R::testConnection() || !R::inspect('tokens')){
            $tokensTable = R::dispense('tokens');
            $tokensTable->token = '';
            R::store($tokensTable);
            $nullRow = R::findOne('tokens', 'id = 1');
            R::trash($nullRow);
        }

        if (!R::testConnection() || !R::inspect('tokenpermissions')){
            $tokenPermissionsTable = R::dispense('tokenpermissions');
            $tokenPermissionsTable->token_id = 0;
            $tokenPermissionsTable->permission_id = 0;
            R::store($tokenPermissionsTable);
            $nullRow = R::findOne('tokenpermissions', 'id = 1');
            R::trash($nullRow);
        }

        if (!R::testConnection() || !R::inspect('servers')){
            $serversTable = R::dispense('servers');
            $serversTable->server_url = '';
            R::store($serversTable);
            $nullRow = R::findOne('servers', 'id = 1');
            R::trash($nullRow);
        }

        if (!R::testConnection() || !R::inspect('files')){
            $filesTable = R::dispense('files');
            $filesTable->uuid = '';
            $filesTable->filename = '';
            $filesTable->secret_key = '';
            $filesTable->mime_type= '';
            $filesTable->file_size = 0;
            R::store($filesTable);
            $nullRow = R::findOne('files', 'id = 1');
            R::trash($nullRow);
        }

        if (!R::testConnection() || !R::inspect('fileservers')){
            $fileServersTable = R::dispense('fileservers');
            $fileServersTable->server_id = 0;
            $fileServersTable->file_id = 0;
            R::store($fileServersTable);
            $nullRow = R::findOne('fileservers', 'id = 1');
            R::trash($nullRow);
        }
    }

    protected static function addInitialPermissions(){
        $permissions = [
            'create_auth',
            'upload_file',
            'cron_update',
            'view_file',
            'administrator',
        ];

        foreach ($permissions as $permission){
            $existingPermission = R::findOne('permissions', 'name = ?',  [$permission]);
            if (!$existingPermission){
                $newPermission = R::dispense('permissions');
                $newPermission->name = $permission;
                R::store($newPermission);
            }
        }
    }

    protected static function createToken($permissions=[]){
        $newToken = R::dispense('tokens');
        $newToken->token = TokenManager::generateToken();
        R::store($newToken);

        foreach ($permissions as $permission){
            $tokenPermission = R::dispense('tokenpermissions');
            $tokenPermission->token = $newToken;
            $tokenPermission->permission_id = $permission->id;
            R::store($tokenPermission);
        }
        return $newToken;
    }

    protected static function createRandomToken(){
        $tokenExists = R::count('tokens') > 0;
        if (!$tokenExists) {
            $permissions = R::findAll('permissions');
            $token = self::createToken($permissions);
            echo '{"status": "ok", "message": "Token got successfully", "token": "' . $token->token . '"}';
            file_put_contents('token.txt', json_encode($token));
        }
    }

    public static function getUserPermissions($token)
    {
        $tokenRow = R::findOne('tokens', 'token = ?', [$token]);

        if (!$tokenRow) {
            return [];
        }
        
        $permissions = R::getAll("
            SELECT p.name
            FROM permissions p
            JOIN tokenpermissions tp ON p.id = tp.permission_id
            WHERE tp.token_id = ?
        ", [$tokenRow->id]);
        
        $userPermissions = [];
        foreach ($permissions as $permission) {
            $userPermissions[] = $permission['name'];
        }
        
        return $userPermissions;
    }

    public static function storeFile($uuid, $fileName, $secretKey, $mimeType, $fileSize, $server_url = '')
    {
        if ($server_url == ''){
            $server_url = $_ENV['CURRENT_SERVER'];
        }
        $file = R::dispense('files');
        $file->uuid = $uuid;
        $file->secret_key = $secretKey;
        $file->mime_type = $mimeType;
        $file->file_size = $fileSize;
        $file->filename = $fileName;
        R::store($file);

        $server = R::findOne('servers', 'server_url = ?', [$server_url]);
        if (!$server){
            $server = R::dispense('servers');
            $server->server_url = $server_url;
            R::store($server);
        }

        $serverToFile = R::dispense('fileservers');
        $serverToFile->server = $server;
        $serverToFile->file = $file;
        R::store($serverToFile);

        return $file;
    }

    public static function generateDownloadUri($uuid){
        $urlsForDownloading = [];
        $startPoint='/file/get/';
        $file = R::findOne('files', 'uuid = ?', [$uuid]);
        if ($file){
            $servers = R::getAll("
                SELECT server_url from servers
                INNER JOIN fileservers on fileservers.server_id = servers.id
                WHERE fileservers.file_id = ?
            ", [$file->id]);
            foreach ($servers as $server){
                if (substr($server["server_url"], -1) == '/'){
                    $startPoint='file/get/';
                }
                $endPoint = $server["server_url"] . $startPoint . $uuid . '?secret=' . $file->secret_key;
                $urlsForDownloading[count($urlsForDownloading)] = $endPoint;
            }
        }
        return $urlsForDownloading;
    }

    public static function checkSecretToken($uuid, $token){
        $file = R::findOne('files', 'uuid = ?', [$uuid]);
        if ($file->secret_key == $token){
            return true;
        }
        return false;
    }

    public static function getMimeType($uuid){
        $file = R::findOne('files', 'uuid = ?', [$uuid]);
        return $file->mime_type;
    }

    public static function getFileName($uuid){
        $file = R::findOne('files', 'uuid = ?', [$uuid]);
        return $file->filename;
    }

}