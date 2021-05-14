<?php namespace Model;

use PDO;

class DbCmd
{

    private $dbconn;
    private $d;
    private $sql;

    public function __construct( DbConnect $dbconn, $sql ){
        $this->dbconn = $dbconn;
        $this->sql = $sql;
        $this->d = $dbconn->getPDO()->prepare( $sql );
    }

    public function __call($name, $arguments)
    {
        $resp = null;
        $retry = false;

        if( $name == 'bindParam' ){
            $name = 'bindValue';
        }

        try{
            $cmd = call_user_func_array(array($this->d, $name), $arguments);
            $resp = $cmd;
        }catch( \Exception $e ){
            if( strstr($e->getMessage(),'2006 MySQL server has gone away')  ){
                $retry = true;
            }else{
                //MESSAGE SOMEONE ABOUT DODGY SQL
            }
        }

        if( $retry ){
            try{
                $this->dbconn->reconnect();
                $this->d = $this->dbconn->getPDO()->prepare( $this->sql );
                call_user_func_array(array($this->d, $name), $arguments);
            }catch( \Exception $e ){}
        }


        return $resp;
    }

}

class DbConnect
{

    private $pdo;
    private $write;
    private $db;
    private $db_user;
    private $db_pass;


    public function reconnect(){
        $db = $this->db;
        $db_user = $this->db_user;
        $db_pass = $this->db_pass;
        $connection = false;
        $errors = array();
        $hosts = array();
        if( $this->write ){
            //This should be your master server
            $hosts[] = '127.0.0.1';
        }else{
            $hosts[] = '127.0.0.1';
            //Add Extra read nodes
            //$hosts[] = '';
        }
        shuffle($hosts );
        foreach( $hosts as $host ) {
            if( !$connection ) {
                try {
                    $conn = new \PDO('mysql:host=' . $host . ';port=3306;dbname=' . $db, $db_user, $db_pass, array(
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_TIMEOUT => 2,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ));
                    $connection = $conn;
                } catch (\Exception $e) {}
            }
        }
        if( !$connection ){
            \Output::error( 'Database Error',500);
        }
        $this->pdo = $connection;
    }

    public function __construct($write, $db, $db_user, $db_pass ){
        $this->write = $write;
        $this->db = $db;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->reconnect();
    }

    /**
     * @return PDO
     */
    public function getPDO(){
        return $this->pdo;
    }

    public function __call($name, $arguments)
    {
        $cmd = null;
        if( $name == 'prepare' ){
            $cmd = new DbCmd( $this , $arguments[0] );
        }else {
            $cmd = call_user_func_array(array($this->pdo, $name), $arguments);
        }
        return $cmd;
    }

}

class Db {

    static private $read = '';
    static private $write = '';

    /**
     * @return PDO
     */
    static public function read(){
        if( gettype(self::$read) == 'string' ) {
            self::$read = new DbConnect( false, '', '','' );
        }
        return self::$read;
    }

    public static function closeAll(){
        self::$read = null;
        self::$write = null;
    }

    /**
     * @return PDO
     */
    static public function write(){
        if( gettype(self::$write) == 'string' ) {
            self::$write = new DbConnect( true,  '', '','' );
        }
        return self::$write;
    }


}
