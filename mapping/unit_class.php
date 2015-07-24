<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Unit extends ZModel {
    protected static $table = 'unit';
    protected $props =[ 'chrID', 'chrGroup_ID', 'chrName', 'chrShort_Name', 'intDiscount', 'intTax_Type', 'intPoint_Flag'];
    protected static $columns =[ 'chrID', 'chrGroup_ID', 'chrName', 'chrShort_Name', 'intDiscount', 'intTax_Type', 'intPoint_Flag'];
    function Unit() {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

}
