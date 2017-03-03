<?php
namespace Grisu\Core;

use \Grisu\Interfaces\SensorInterface;
use \Grisu\Interfaces\DbConnectionInterface;
use \Grisu\DB\DbConnector;

class Sensor implements SensorInterface
{
    /**
     * @var DbConnectionInterface
     */
    private $dbcon;

    public function __construct(DbConnectionInterface $dbConnector)
    {
        $this->setDbCon($dbConnector);
    }

    public function getOne($id){

    }
    public function getAll($id){

    }
    public function getByQuery($id){

    }
    public function set(){

    }
    public function updateByQuery(array $values, $query){

    }

    private function setDbCon($dbConnector){
        $this->dbcon = $dbConnector;
    }


    public static function object(DbConnector $dbconnection){
        $dbconnection = new DbConnector($dbconnection);

        return self::__construct($dbconnection);
    }
}