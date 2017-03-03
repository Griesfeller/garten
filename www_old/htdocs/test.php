<?php
include __DIR__ . '/../Grisu/Core/Autoloader.php';

use Grisu\DB\DbMysql;

$db = new DbMysql();
$dbconnection = new \Grisu\DB\DbConnector($db);
$temperatur = new \Grisu\Core\Temperatur($dbconnection);



$test = \Grisu\Core\Temperatur::object($db);
echo "<pre>".print_r($test, true)."</pre>";