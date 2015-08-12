<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Area extends ZModel
{
    protected static $table = "area";
    protected $props = ["chrID",
        "chrName"];
    protected static $columns = ["chrID",
        "chrName"];

    function Area()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }
}
