<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Maker extends ZModel
{
    protected static $table = "maker";
    protected $props = [ 'chrID', 'chrName', 'chrShort_Name'];
    protected static $columns = [ 'chrID', 'chrName', 'chrShort_Name'];
	
    function Maker() {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }



    public static function get_new_maker() {
        return new Maker(get_lastet_3_number(parent::get_column('chrID')));
    }


}
