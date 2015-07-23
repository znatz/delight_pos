<?php

require_once dirname(__FILE__).'/../utils/helper.php';
require_once dirname(__FILE__).'/../utils/connect.php';

class Shop {
    public $chrID;
    public $chrName;
    public $chrPost;
    public $chrAddress;
    public $chrAddress_No;
    public $chrTel;
    public $chrFax;
    public $intDisplay_Order;
    function Shop($id="", $name="", $post="", $address="", $addressno="", $tel="", $fax="", $display="") {
        $this->chrID = $id;
        $this->chrName = $name;
        $this->chrPost = $post;
        $this->chrAddress = $address;
        $this->chrAddress_No = $addressno;
        $this->chrTel = $tel;
        $this->chrFax = $fax;
        $this->intDisplay_Order = $display;
    }

    public static function get_one_empty_shop() {
        return new Shop("", "", "", "", "", "", "", "");
    }

    public static function get_all_shop() {
        $connection = new Connection();
        $query = "SELECT * FROM `shop`;";
        $result = $connection->result($query);
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = new Shop($row['chrID'],
                                    $row['chrName'],
                                    $row['chrPost'],
                                    $row['chrAddress'],
                                    $row['chrAddressNo'],
                                    $row['chrTel'],
                                    $row['chrFax'],
                                    $row['intDisplayOrder']);
        }
        return $contents;
    }

    public static function get_all_shop_chrID() {
        $connection = new Connection();
        $query = "SELECT `chrID` FROM `shop` ORDER BY `chrID`;";
        $result = $connection->result($query);
        echo mysql_error();
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = $row['chrID'];
        }
        $connection->close();
        return $contents;
    }

    public static function get_one_shop($chrID) {
        $connection = new Connection();
        $query = "SELECT * FROM `shop` WHERE `chrID`=".$chrID.";";
        $result = $connection->result($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $contents = new Shop($row['chrID'],
                                    $row['chrName'],
                                    $row['chrPost'],
                                    $row['chrAddress'],
                                    $row['chrAddressNo'],
                                    $row['chrTel'],
                                    $row['chrFax'],
                                    $row['intDisplayOrder']);
        $connection->close();
        return $contents;

    }

    public static function insert_one_shop($id, $name, $post, $address, $addressno, $tel, $fax, $display) {
        $connection = new Connection();
        $query = <<<SQL
INSERT INTO `shop` (`chrID`,
                    `chrName`,
                    `chrPost`,
                    `chrAddress`,
                    `chrAddressNo`,
                    `chrTel`,
                    `chrFax`,
                    `intDisplayOrder`)
VALUES ('$id', '$name', '$post', '$address', '$addressno', '$tel', '$fax', '$display');
SQL;
        $result = $connection->result($query);
        return $result;

    }

    public static function update_one_shop($id, $name, $post, $address, $addressno, $tel, $fax, $display) {
        $connection = new Connection();
        $query = <<<EOF
UPDATE `shop` SET `chrName`='$name', `chrPost`='$post', `chrAddress`='$address', `chrAddressNo`='$addressno'
, `chrTel`='$tel', `chrFax`='$fax', `intDisplayOrder`='$display' WHERE `chrID`='$id';
EOF;
        $result = $connection->result($query);
        $connection->close();
        return $result;

    }

    public static function delete_one_shop($chrID) {
        $connection = new Connection();
        $query = "DELETE FROM `shop` WHERE `chrID`=".$chrID.";";
        $result = $connection->result($query);
        echo mysql_error();
        $connection->close();
        return $result;

    }

    public static function get_new_shop() {
        $id = get_lastet_number(self::get_all_shop_chrID());
        $result = new Shop($id,"","","","","","");
        return $result;
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
