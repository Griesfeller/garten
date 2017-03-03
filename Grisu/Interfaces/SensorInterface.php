<?php
/**
 * Created by PhpStorm.
 * User: sven
 * Date: 03.03.2017
 * Time: 13:25
 */

namespace Grisu\Interfaces;


interface SensorInterface
{
    public function __construct(DbConnectionInterface $dbConnector);

    public function getOne($id);
    public function getAll($id);
    public function getByQuery($id);
    public function set();
    public function updateByQuery(array $values, $query);
}