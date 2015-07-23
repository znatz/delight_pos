<?php

require_once dirname(__FILE__).'/../utils/helper.php';
require_once dirname(__FILE__).'/../utils/connect.php';

class Unit {
    public $chrID;
    public $chrGroup_ID;
    public $chrName;
    public $chrShort_Name;
    public $intDiscount;
    public $intTax_Type;
    public $intPoint_Flag;
    function Unit($id="", $groupid="", $name="", $shortname="", $discount="", $tax="", $point="") {
        $this->chrID = $id;
        $this->chrGroup_ID = $groupid;
        $this->chrName = $name;
        $this->chrShort_Name = $shortname;
        $this->intDiscount = $discount;
        $this->intTax_Type = $tax;
        $this->intPoint_Flag = $point;
    }

    public static function get_one_empty_unit() {
        return new Unit("", "", "", "","","","");
    }

    public static function get_all_unit() {
        $connection = new Connection();
        $query = "SELECT * FROM `unit`;";
        $result = $connection->result($query);
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = new Unit($row['chrID'],
                                    $row['chrGroup_ID'],
                                    $row['chrName'],
                                    $row['chrShort_Name'],
                                    $row['intDiscount'],
                                    $row['intTax_Type'],
                                    $row['intPoint_Flag']);
        }
        return $contents;
    }

    public static function get_all_unit_chrID() {
        $connection = new Connection();
        $query = "SELECT `chrID` FROM `unit` ORDER BY `chrID`;";
        $result = $connection->result($query);
        echo mysql_error();
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = $row['chrID'];
        }
        $connection->close();
        return $contents;
    }

    public static function get_one_unit($chrID) {
        $connection = new Connection();
        $query = "SELECT * FROM `unit` WHERE `chrID`=".$chrID.";";
        $result = $connection->result($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $contents = new Unit($row['chrID'],
                                    $row['chrGroup_ID'],
                                    $row['chrName'],
                                    $row['chrShort_Name'],
                                    $row['intDiscount'],
                                    $row['intTax_Type'],
                                    $row['intPoint_Flag']);
        $connection->close();
        return $contents;

    }

    public static function insert_one_unit($id, $groupid, $name, $shortname, $discount, $tax, $point) {
        $connection = new Connection();
        $query = <<<SQL
INSERT INTO `unit` (`chrID`,
                    `chrGroup_ID`,
                    `chrName`,
                    `chrShort_Name`,
                    `intDiscount`,
                    `intTax_Type`,
                    `intPoint_Flag`)
VALUES ('$id', '$groupid', '$name', '$shortname', '$discount','$tax', '$point');
SQL;
        $result = $connection->result($query);
        return $result;

    }

    public static function update_one_unit($id, $groupid, $name, $shortname, $discount, $tax, $point) {
        $connection = new Connection();
        $query = <<<EOF
UPDATE `unit` SET `chrGroup_ID`='$groupid', `chrName`='$name', `chrShort_Name`='$shortname', `intDiscount`='$discount', `intTax_Type`='$tax', `intPoint_Flag`='$point'
WHERE `chrID`='$id';
EOF;
        $result = $connection->result($query);
        $connection->close();
        return $result;

    }

    public static function delete_one_unit($chrID) {
        $connection = new Connection();
        $query = "DELETE FROM `unit` WHERE `chrID`=".$chrID.";";
        $result = $connection->result($query);
        echo mysql_error();
        $connection->close();
        return $result;

    }

    public static function get_new_unit() {
        $connection = new Connection();
        $id = get_lastet_number(self::get_all_unit_chrID());
        $result = new Unit($id,"","","","","","");
        $connection->close();
        return $result;
    }
}
