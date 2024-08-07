<?php
class Database{
    private $host;
    private $dbname;
    private $username;
    private $pass;
    private static $pdo;

    public function __construct($host, $dbname, $username, $pass)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->pass = $pass;
    }

    public function connect(){
        try{
            self::$pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname}",
                $this->username, $this->pass);
            
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            self::$pdo->exec("SET NAMES utf8");

            return true;
        }catch(PDOException $e){
            echo "Conenction failed.";
            return false;
        }
    }

    public static function getConnection(){
        return self::$pdo;
    }

    public static function query($query_string, $params = [], $exec = true){
        $stmt = self::$pdo->prepare($query_string);
        foreach($params as $placeholder => $value){
            $stmt->bindValue($placeholder, $value);
        }
        if($exec){
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else{
            return $stmt;
        }
    }
    public function close(){
        self::$pdo = null;
    }

}