<?php


namespace app\database;
use PDO;

class Connection
{


    public function __construct()
    {
        
    }
    public static function Connection()
    {
        return new PDO('mysql:host=localhost;dbname=api_php', 'root', '123edu', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::MYSQL_ATTR_INIT_COMMAND
        ]);
    }
}
