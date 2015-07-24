<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Priceband extends ZModel{

    protected static $table = 'priceband';
    protected $props = [ 'chrID', 'chrName', 'intUnder_Bound', 'intUpper_Bound'];
    protected static $columns = [ 'chrID', 'chrName', 'intUnder_Bound', 'intUpper_Bound'];

    function Priceband() {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }


    public static function get_new_priceband() {
        return new Priceband(get_lastet_number(parent::get_column('chrID')));
    }

}
