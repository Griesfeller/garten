<?php
namespace Grisu\Interfaces;


interface SensorInterface
{
    public function __construct($dbConnector);

    public function getOne($id);
    public function getAll($id);
    public function getByQuery($id);
    public function set();
    public function updateByQuery(array $values, $query);
}