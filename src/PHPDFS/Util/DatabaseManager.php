<?php

namespace PHPDFS\Util;

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
        }

        // Create 'tokens' table if not exists
        if (!R::testConnection() || !R::inspect('tokens')){
            $tokensTable = R::dispense('tokens');
            $tokensTable->token = '';
            R::store($tokensTable);
        }

        if (!R::testConnection() || !R::inspect('tokenpermissions')){
            $tokenPermissionsTable = R::dispense('tokenpermissions');
            $tokenPermissionsTable->token_id = 0;
            $tokenPermissionsTable->permission_id = 0;
            R::store($tokenPermissionsTable);
        }

        self::addInitialPermissions();
        self::createRandomToken();
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
            $tokenPermission->token_id = $newToken;
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
            JOIN token_permissions tp ON p.id = tp.permission_id
            WHERE tp.token_id = ?
        ", [$tokenRow->id]);
        
        $userPermissions = [];
        foreach ($permissions as $permission) {
            $userPermissions[] = $permission['name'];
        }
        
        return $userPermissions;
    }


}