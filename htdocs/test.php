<?php
include __DIR__ . '/../Grisu/Core/Autoloader.php';

use Grisu\DB\DbMysql;

$db = new DbMysql();

$temperatur = new \Grisu\Core\Temperatur($db);



$test = \Grisu\Core\Temperatur::object($db);
echo "<pre>".print_r($test, true)."</pre>";