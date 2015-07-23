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

    public static function get_one_empty_priceband() {
        return new Priceband();
    }

    public static function get_all_priceband() {
        return parent::get_all();
    }

    public static function get_all_priceband_chrID() {
        return parent::get_column('chrID');
    }

    public static function get_one_priceband($chrID) {
        return parent::find($chrID);

    }

    public static function insert_one_priceband() {
        return parent::insert_values(func_get_args());
    }

    public static function update_one_priceband() {
        return parent::update_to_columns(func_get_args());
    }

    public static function delete_one_priceband($chrID) {
        return parent::delete($chrID);
    }

    public static function get_new_priceband() {
        return new Priceband(get_lastet_number(parent::get_column('chrID')));
    }

    public static function get_distinct_priceband_chrID() {
        return parent::get_distinct_column('chrID');
    }
}
