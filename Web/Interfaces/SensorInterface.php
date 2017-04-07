<?php
/**
 * Created by PhpStorm.
 * User: sven
 * Date: 07.04.2017
 * Time: 17:42
 */

namespace Web\Interfaces;


interface SensorInterface
{
    public function getName();

    public function getValue();

    public function getLastChange();
}