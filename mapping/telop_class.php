<?php

require_once dirname(__FILE__) . '/../utils/helper.php';
require_once dirname(__FILE__) . '/../utils/connect.php';

class Telop
{
    public $chrShop_ID;
    public $chrTelop;

    function Telop($shopid = "", $comment = "")
    {
        $this->chrShop_ID = $shopid;
        $this->chrTelop = $comment;
    }

    public static function get_one_empty_telop()
    {
        return new Telop("", "");
    }

    public static function get_all_telop()
    {
        $connection = new Connection();
        $query = <<<EOF
SELECT * FROM `telop` ORDER BY `chrShop_ID`;
EOF;
        $result = $connection->result($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = new Telop($row['chrShop_ID'], $row['chrComment']);
        }
        return $contents;
    }


    public static function get_one_telop($shopid)
    {
        $connection = new Connection();
        $query = <<<EVB
SELECT * FROM `telop` WHERE `chrShop_ID`='$shopid';
EVB;
        $result = $connection->result($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $contents = new Telop($row['chrShop_ID'], $row['chrComment']);
        $connection->close();
        return $contents;

    }

    public static function insert_one_telop($comment)
    {
        $connection = new Connection();
        $query = <<<EOF
INSERT INTO `telop` (`chrComment`)
VALUES ('$comment');
EOF;
        $result = $connection->result($query);
        return $result;

    }

    /*
    @param: Shop ID, Line Count, Comment
    */
    public static function update_one_telop($comment)
    {
        $connection = new Connection();
        $query = <<<EOF
UPDATE `telop` SET `chrShop_ID`='$shopid',`chrComment`='$comment'
WHERE `chrShop_ID`='$shopid';
EOF;
        $result = $connection->result($query);
        $connection->close();
        return $result;

    }

    public static function delete_one_telop($shopid)
    {
        $connection = new Connection();
        $query = <<<EOF
DELETE FROM `telop` WHERE `chrID`='$shopid'
EOF;
        $result = $connection->result($query);
        echo mysql_error();
        $connection->close();
        return $result;

    }
}
