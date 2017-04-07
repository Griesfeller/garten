<?php
/**
 * Created by PhpStorm.
 * User: sven
 * Date: 07.04.2017
 * Time: 17:44
 */

namespace Web\Interfaces;


interface ConnectionInterface
{
    public function setConnection($typ, $host, $username, $password, $databaseName);

    public function getOne($query, $array);

    public function getAll($query, $array);

    public function execute($query, $array);

    public function error();

}