<?php

require_once dirname(__FILE__) . '/../utils/helper.php';
require_once dirname(__FILE__) . '/../utils/connect.php';
require_once 'ZModel.php';

class Seller extends ZModel
{
    protected static $table = 'seller';
    protected $props = ["chrID",
        "chrName",
        "chrShort_Name",
        "chrPos",
        "chrAddress",
        "chrAddress_No",
        "chrTel",
        "chrFax",
        "chrStaff"];
    protected static $columns = ["chrID",
        "chrName",
        "chrShort_Name",
        "chrPos",
        "chrAddress",
        "chrAddress_No",
        "chrTel",
        "chrFax",
        "chrStaff"];

    function Seller()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_one_empty_seller()
    {
        return new Seller("", "", "", "", "", "", "", "", "");
    }

    public static function get_all_seller()
    {
        return parent::get_all();
    }

    public static function get_all_seller_chrID()
    {
        return parent::get_column('chrID');
    }


    public static function search_chrID_chrName_by_word($word)
    {
        $connection = new Connection();
        $query = <<<EOF
SELECT `chrID`,`chrName` FROM `seller` WHERE `chrID` LIKE '%$word%' ORDER BY `chrID`;
EOF;
        $result = $connection->result($query);
        echo mysql_error();
        $htmlString = "";
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//            $contents[] = array($row['chrID'],$row['chrName']);
            $htmlString .= sprintf("仕入先コード：%d 仕入先名：%s", $row['chrID'], $row['chrName']);
        }
        $connection->close();
//        return $contents;
        return $htmlString;
    }

    public static function get_one_seller($chrID)
    {
        return parent::find($chrID);

    }

    public static function insert_one_seller()
    {
        return parent::insert_values(func_get_args());
    }

    public static function update_one_seller($id, $name, $shortnm, $pos, $add, $addno, $tel, $fax, $staff)
    {
        return parent::update_to_columns(func_get_args());
    }

    public static function delete_one_seller($chrID)
    {
        return parent::delete($chrID);

    }

    public static function get_new_seller()
    {
        return new Seller(get_lastet_3_number(parent::get_distinct_column('chrID')));
    }
}
