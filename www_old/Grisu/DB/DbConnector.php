<?php
namespace Grisu\DB;

use Grisu\Interfaces\DbConnectionInterface;
use Grisu\Interfaces\DbConnectorInterface;

class DbConnector implements DbConnectorInterface
{
    /**
     * @var DbConnectionInterface
     */
    private $connection;
    public function __construct($connection){
        if($connection InstanceOf DbConnectionInterface ) {
            $this->setConnection($connection);
        }
    }
    public function exec($sql,array $array = array()){
        return $this->connection->exec($sql, $array);
    }
    public function selectall($sql,array $array = array()){
        return $this->connection->selectall($sql, $array );
    }
    public function selectone($sql,array $array = array()){
        return $this->connection->selectone($sql, $array);
    }
    public function error(){
        return $this->connection->error();
    }
    public function affected(){
        return $this->connection->affected();
    }
    public function last_insert_id(){

    }
    public function starttransaction(){

    }
    public function rollback(){

    }
    public function commit(){

    }
    public function getfieldfromtable($table){

    }
    public function getallfieldinfosfromtable($table){

    }
    public function getalltables(){

    }

    private function setConnection($connection){
        $this->connection = $connection;
    }
}