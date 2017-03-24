<?php
/**
 * Created by PhpStorm.
 * User: sven
 * Date: 07.03.2017
 * Time: 16:30
 */

namespace Grisu\DB;


class dbConnection
{
    protected $user;
    protected $password;
    protected $host;
    protected $dsn;
    protected $isMaster = true;
    private $connection;
    private $istransaction;
    private $hasErrors;
    private $isRollback;

    public function __construct($host, $user, $password, $isMaster = true)
    {

    }

    public function selectOne($sqlQuery, $array = array())
    {
        return array();
    }

    public function selectAll($sqlQuery, $array = array())
    {
        return array();
    }

    public function execute($sqlQuery, $array = array())
    {
        return true;
    }

    private function checkConnection($reConnect = true)
    {
        return true;
    }

}