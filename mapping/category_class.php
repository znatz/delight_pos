<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Category extends ZModel{
    protected static $table = 'category';
    protected $props = [ 'chrID', 'chrName'];
    protected static $columns = [ 'chrID', 'chrName'];
    function Category() {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_distinct_category_chrID() {
        $query = "SELECT DISTINCT * FROM `category` ORDER BY `chrID`;";
        return get_all_from_table(self::$table, $query);
    }


    public static function get_new_category() {
        return new Category(get_lastet_number(parent::get_column('chrID')));
    }
}
