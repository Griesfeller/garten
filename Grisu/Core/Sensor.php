<?php
namespace Grisu\Core;

use \Grisu\Interfaces\SensorInterface;
use \Grisu\Interfaces\DbConnectionInterface;

class Sensor implements SensorInterface
{
    public $id;
    protected $sensorTyp = '';
    /**
     * @var DbConnectionInterface
     */
    private $dbcon;
    private $tableName = '';

    /**
     * @param $dbconnection
     * @param int $id
     * @return Sensor
     */
    public static function object($dbconnection, $id = 0)
    {
        $return = new self($dbconnection);
        if ($id > 0) {
            $return->getOne($id);
        }
        return $return;
    }

    public function __construct($dbconnection)
    {
        if (!in_array('Grisu\Interfaces\DbConnectionInterface', class_implements($dbconnection))) {
            throw new \Exception('no $dbconnection ' . key(class_implements($dbconnection)));
        }
        $this->setDbCon($dbconnection);
    }

    private function setDbCon($dbConnector)
    {
        $this->dbcon = $dbConnector;
    }


    public function getOne($id){
        $result = $this->dbcon->selectone("select * from " . $this->tableName . " where id = ? and sensortype = ?", array($id, $this->sensorTyp));
        $this->loadArrayIntoThis($result);
        return clone $this;
    }

    private function loadArrayIntoThis($array)
    {
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function getAll($id){

    }

    public function getByQuery($id){

    }

    public function set(){

    }

    public function updateByQuery(array $values, $query){

    }
}