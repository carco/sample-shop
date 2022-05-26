<?php
namespace Shop;
use Shop\Model\AbstractModel;

final class App {
    
    /** @var \mysqli $db */
    private static $db;
    
    private $conn;
    
    private function __construct()
    {
        $this->conn = mysqli_connect(SHOP_DB_HOST, SHOP_DB_USER, SHOP_DB_PASS);
        if (!$this->conn) {
            throw new \Exception("MySQL connection failed " . mysqli_connect_error());
        }
        if (!mysqli_select_db($this->conn, SHOP_DB)) {
            throw new \Exception("Invalid DB");
        }
    }

    /**
     * @return \mysqli
     * @throws \Exception
     */
    public static function getConnection() : \mysqli
    {
        if (self::$db == null)
        {
            self::$db = mysqli_connect(SHOP_DB_HOST, SHOP_DB_USER, SHOP_DB_PASS);
            if (!self::$db) {
                throw new \Exception("MySQL connection failed " . mysqli_connect_error());
            }
            if (!mysqli_select_db(self::$db, SHOP_DB)) {
                throw new \Exception("Invalid DB");
            }
        }

        return self::$db;
    }
    
    
    public static function getModel(string $modelName) : AbstractModel
    {
        $modelClass = 'Shop\\Model\\'.ucfirst($modelName).'Model';
        if (!class_exists($modelClass)) {
           throw new \Exception("Invalid model name ($modelName)");
        }
        $model = new $modelClass;
        if (!($model instanceof AbstractModel)) {
            throw new \Exception("Invalid model class (".get_class($model).")");
        }
        return $model;
    }
    
 
}
