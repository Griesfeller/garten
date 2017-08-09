<?php

namespace test\Web;

use PHPUnit\Framework\TestCase;
use Web\Database\Connection;


class ConnectionTest extends TestCase
{
    public function testSetConnection()
    {
        $connection = new Connection('sqlite', '', '', '', '');
        $this->assertEquals(get_class($connection), 'Web\Database\Connection');
        $this->assertTrue($connection->isConnected());

        $connection = new Connection('/tmp/wasauchimmer', '', '', '', '');
        $this->assertEquals(get_class($connection), 'Web\Database\Connection');
        $this->assertFalse($connection->isConnected());
    }

}
