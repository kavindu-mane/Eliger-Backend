<?php

namespace EligerBackend\model\classes;

use PDO;
use PDOException;

class DBConnector
{
    public static function getConnection()
    {
        $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV["DB_NAME"];
        try {
            return new PDO($dsn, $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        } catch (PDOException $ex) {
            die("Connection failed : " . $ex->getMessage());
        }
    }
}
