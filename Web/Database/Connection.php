<?php

namespace Web\Database;

use Web\Interfaces\ConnectionInterface;
use Web\Core\Logger;

class Connection implements ConnectionInterface
{
    private $pdo;
    private $connected = false;

    public function __construct($typ, $host, $username, $password, $databaseName)
    {
        $this->setConnection($typ, $host, $username, $password, $databaseName);
    }

    /**
     * @param $typ
     * @param $host
     * @param $username
     * @param $password
     * @param $databaseName
     * @return mixed
     */
    public function setConnection($typ, $host, $username, $password, $databaseName)
    {
        try {
            $dsn = $typ . ':dbname=' . $databaseName . ';host=' . $host;
            $this->pdo = New \PDO($dsn, $username, $password);
            $this->connected = true;
        } catch (\Exception $e) {
            Logger::setLoggerMessage(__FILE__, __METHOD__, __LINE__, $e->getMessage());
        }
    }

    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param $query
     * @param $array
     * @return mixed
     */
    public function getOne($query, $array)
    {

    }

    /**
     * @param $query
     * @param $array
     * @return mixed
     */
    public function getAll($query, $array)
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @param $query
     * @param $array
     * @return mixed
     */
    public function execute($query, $array)
    {
        // TODO: Implement execute() method.
    }

    /**
     * @return mixed
     */
    public function error()
    {
        // TODO: Implement error() method.
    }


}