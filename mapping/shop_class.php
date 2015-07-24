<?php

require_once dirname(__FILE__).'/../utils/helper.php';
require_once dirname(__FILE__).'/../utils/connect.php';
require_once 'ZModel.php';

class Shop extends ZModel {
    protected static $table = 'shop';
    protected $props =[ 'chrID', 'chrName', 'chrPost', 'chrAddress', 'chrAddressNo', 'chrTel', 'chrFax', 'intDisplayOrder'];
    protected static $columns =[ 'chrID', 'chrName', 'chrPost', 'chrAddress', 'chrAddressNo', 'chrTel', 'chrFax', 'intDisplayOrder'];

    function Shop() {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_new_shop() {
        return new Shop(get_lastet_number(parent::get_distinct_column('chrID')));

    }

    public static function get_distinct_shop_chrID() {
        $connection = new Connection();
        $query = "SELECT DISTINCT `chrID`,`chrName` FROM `shop` ORDER BY `chrID`;";
        $result = $connection->result($query);
        echo mysql_error();
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = array($row['chrID'], $row['chrName']);
        }
        $connection->close();
        return $contents;
    }
}
