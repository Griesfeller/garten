<?php
/**
 * Created by PhpStorm.
 * User: sven
 * Date: 03.03.2017
 * Time: 14:44
 */

namespace Grisu\Interfaces;


interface CacheInterface
{
    public function is_gecached($key);
    public function get_gecached($key);
    public function set_cache($key, $value);

}