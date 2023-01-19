<?php

require_once(dirname(dirname(__FILE__)) . "/Database.php");

class BaseModel
{
    protected $table_name;
    protected $db;

    function __construct($table_name)
    {
        $this->table_name = $table_name;
        $this->db = new Database();
    }
}
