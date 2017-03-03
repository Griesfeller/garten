<?php
namespace Grisu\Interfaces;

interface DbConnectionInterface
{
    public function __construct(array $dbconnectarray = array(),array $dbconnectarray_lesen = array());
    public function exec($sql,array $array = array());
    public function selectall($sql,array $array = array());
    public function selectone($sql,array $array = array());
    public function error();
    public function affected();
    public function last_insert_id();
    public function starttransaction();
    public function rollback();
    public function commit();
    public function getfieldfromtable($table);
    public function getallfieldinfosfromtable($table);
    public function getalltables();


}