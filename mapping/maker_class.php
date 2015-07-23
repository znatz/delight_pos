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

    public static function get_one_empty_maker() {
        return new Maker();
    }

    public static function get_all_maker() {
        return parent::get_all();
   }

    public static function get_all_maker_chrID() {
        return parent::get_column('chrID');
    }

    public static function get_one_maker($chrID) {
        return parent::find($chrID);
    }

    public static function insert_one_maker() {
        return parent::insert_values(func_get_args());
    }

    public static function update_one_maker() {
        return parent::update_to_columns(func_get_args());
    }

    public static function delete_one_maker($chrID) {
        return parent::delete($chrID);
    }

    public static function get_new_maker() {
        return new Maker(get_lastet_3_number(parent::get_column('chrID')));
    }

    public static function get_distinct_maker_chrID() {
        return parent::get_distinct_column('chrID');
    }
}
