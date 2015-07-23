<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Goods extends ZModel
{
    protected static $table = "goods";
    protected $props = ["chrID",
        "chrClass_ID",
        "chrCode",
        "chrName",
        "chrKana",
        "chrSeller_ID",
        "chrMaker_ID",
        "chrGroup_ID",
        "chrUnit_ID",
        "chrColor",
        "chrSize",
        "chrComment1",
        "chrComment2",
        "intCost",
        "intPrice"];
    protected static $columns = ["chrID",
        "chrClass_ID",
        "chrCode",
        "chrName",
        "chrKana",
        "chrSeller_ID",
        "chrMaker_ID",
        "chrGroup_ID",
        "chrUnit_ID",
        "chrColor",
        "chrSize",
        "chrComment1",
        "chrComment2",
        "intCost",
        "intRetailPrice",
        "intPrice",
        "chrRegisterDate",
        "chrUpdateDate"];

//    $intTax_Type;

    function Goods()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_one_empty_goods()
    {
        return new Goods();
    }

    public static function get_all_goods()
    {
        return parent::get_all();
    }

    public static function search_goods($seller, $maker, $group, $classID)
    {
        $query = "SELECT * FROM `goods` ";
        $query .= prefix_ifNotEmpty_else(" WHERE `chrSeller_ID` = ", $seller, "WHERE `chrSeller_ID` LIKE '%'");
        $query .= prefix_ifNotEmpty(" AND `chrMaker_ID` = ", $maker);
        $query .= prefix_ifNotEmpty(" AND `chrGroup_ID` = ", $group);
        $query .= prefix_ifNotEmpty(" AND `chrClass_ID` = ", $classID);
        return get_all_from_table("goods", $query);
    }

    public static function get_all_goods_chrID()
    {
        $query = "SELECT `chrID` FROM `goods` ORDER BY `chrID`;";
        return get_all_from_table("goods", $query);
    }

    public static function get_one_goods($chrID)
    {
        return parent::find($chrID);
    }


    public static function insert_one_goods()
    {

        $today = date("Y/m/d");
        $array = func_get_args();
        /* initial input + $price + insertedDate + updatedDate */
        array_push($array,end(func_get_args()), $today,$today);
        return parent::insert_to_columns($array);
    }

    public static function update_one_goods($id, $class, $code, $name, $kana, $seller, $maker, $group, $unit, $color, $size, $comme1, $comme2, $cost, $price)
    {
        $today = date("Y/m/d");
        $array = func_get_args();
        /* initial input + $price + insertedDate + updatedDate */
        array_push($array,end(func_get_args()), $today,$today);
        return parent::update_to_columns($array);
    }

    public static function delete_one_goods($chrID)
    {
        return parent::delete($chrID);
    }

    public static function get_new_goods()
    {
        $connection = new Connection();
        $query = "SELECT `chrID` FROM `goods` Where Left(`chrID`,2) ='20' ORDER BY `chrID` DESC LIMIT 0, 1;";
        $result = $connection->result($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        if (is_null($row['chrID'])) {
            $row['chrID'] = "200000000001" . self::calcJanCodeDigit("200000000001");
        } else {
            $newbar = str_pad(intval(mb_substr($row['chrID'], 2, 10, "UTF-8")) + 1, 10, "0", STR_PAD_LEFT);
            $row['chrID'] = "20" . $newbar . self::calcJanCodeDigit("20" . $newbar);
        }
        $contents = new Goods($row['chrID']);
        $connection->close();
        return $contents;
    }

    public static function get_distinct_goods_chrID()
    {
        return parent::get_distinct_column('chrID');
    }

    public static function get_distinct_class_chrID()
    {
        $connection = new Connection();
        $query = "SELECT DISTINCT `chrID`,`chrName` FROM `class` ORDER BY `chrID`;";
        $result = $connection->result($query);
        echo mysql_error();
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = $row['chrID'] . ":" . $row['chrName'];
        }
        $connection->close();
        return $contents;
    }

    private function calcJanCodeDigit($num)
    {
        $arr = str_split($num);
        $odd = 0;
        $mod = 0;
        for ($i = 0; $i < count($arr); $i++) {
            if (($i + 1) % 2 == 0) {
                //偶数の総和
                $mod += intval($arr[$i]);
            } else {
                //奇数の総和
                $odd += intval($arr[$i]);
            }
        }
        //偶数の和を3倍+奇数の総和を加算して、下1桁の数字を10から引く
        $cd = 10 - intval(substr((string)($mod * 3) + $odd, -1));
        //10なら1の位は0なので、0を返す。
        return $cd === 10 ? 0 : $cd;
    }
}
