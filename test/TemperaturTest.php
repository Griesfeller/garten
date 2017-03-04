<?php
namespace test;

use Grisu\Core\Temperatur;
use Grisu\DB\DbMysql;
use PHPUnit\Framework\TestCase;


class TemperaturTest extends TestCase
{

    public function testTemperatur()
    {
        $dbcon = new DbMysql();
        $temperatur = new Temperatur($dbcon);
        $this->assertEquals(key(class_parents($temperatur)), 'Grisu\Core\Sensor', 'Error: ' . print_r(class_parents($temperatur), true));
    }

    public function testGetOne()
    {
        $dbcon = new DbMysql();
        $temperatur = new Temperatur($dbcon);
        $temperatur->getOne('0');
        $this->assertEquals($temperatur->id, 0, 'Object: ' . print_r($temperatur, true));
        $this->assertNotEquals($temperatur->id, 1, 'Object: ' . print_r($temperatur, true));
    }
}
