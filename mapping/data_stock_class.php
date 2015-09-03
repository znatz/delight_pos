<?php

require_once "helper.php";
require_once "connect.php";
require_once "ZModel.php";
/**
 * Created by PhpStorm.
 * User: POSCO
 * Date: 2015/08/31
 * Time: 15:38
 */
class Data_stock extends ZModel
{
    protected static $table = "data_stock";
    protected $props = [
        "chrDate",
        "chrShop_ID",
        "chrStockCode",
        "intStockCount",
        "strKeyCode"
    ];

    protected static $columns = [
        "chrDate",
        "chrShop_ID",
        "chrStockCode",
        "intStockCount",
        "strKeyCode"
    ];

    function Data_stock()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_all_with_limits($l)
    {
        return get_all_from_table_with_limits(static::$table, '', $l);
    }
}
