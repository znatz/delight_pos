<?php

require_once dirname(__FILE__).'/../utils/helper.php';
require_once dirname(__FILE__).'/../utils/connect.php';

class Salesgoal
{
    public $chrID;
    public $chrShop_ID;
    public $chrDate;
    public $intGoal;

    function Salesgoal($id = "", $shopid = "", $date = "", $goal="")
    {
        $this->chrID = $id;
        $this->chrShop_ID = $shopid;
        $this->chrDate = $date;
        $this->intGoal = $goal;
    }

    public static function get_one_empty_salesgoal()
    {
        return new Salesgoal("", "", "");
    }

    public static function get_all_salesgoal()
    {
        $connection = new Connection();
        $query = "SELECT * FROM `salesgoal`;";
        $result = $connection->result($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = new Salesgoal(
                $row['chrID'],
                $row['chrShop_ID'],
                $row['chrDate'],
                $row['intGoal']
            );
        }
        return $contents;
    }

    public static function get_all_salesgoal_chrID()
    {
        $connection = new Connection();
        $query = "SELECT `chrID` FROM `salesgoal` ORDER BY `chrID`;";
        $result = $connection->result($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = $row['chrID'];
        }
        $connection->close();
        return $contents;
    }

    public static function get_one_salesgoal($chrID)
    {
        $connection = new Connection();
        $query = "SELECT * FROM `salesgoal` WHERE `chrID`=" . $chrID . ";";
        $result = $connection->result($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $contents = new Salesgoal($row['chrID'],
            $row['chrShop_ID'],
            $row['chrDate'],
            $row['intGoal']);
        $connection->close();
        return $contents;

    }

    public static function insert_one_salesgoal($shopid, $date, $goal)
    {
        $connection = new Connection();
        $query = <<<EOF
INSERT INTO `salesgoal` (
                    `chrShop_ID`,
                    `chrDate`,
                    `intGoal`)
VALUES ('$shopid', '$date', $goal);
EOF;
        $result = $connection->result($query);
        return $result;

    }

    public static function update_one_salesgoal($shopid, $date, $goal)
    {
        $connection = new Connection();
        $query = <<<EOF
UPDATE `salesgoal` SET `chrShop_ID`='$shopid', `chrDate`='$date',`intGoal`='$goal'
WHERE `chrID`='$id';
EOF;
        $result = $connection->result($query);
        $connection->close();
        return $result;

    }

    public static function delete_one_salesgoal($chrID)
    {
        $connection = new Connection();
        $query = "DELETE FROM `salesgoal` WHERE `chrID`=" . $chrID . ";";
        $result = $connection->result($query);
        $connection->close();
        return $result;

    }

    public static function get_new_salesgoal()
    {
        $id = get_lastet_3_number(self::get_all_salesgoal_chrID());
        $result = new Salesgoal($id, "", "");
        return $result;
    }

    public static function get_distinct_salesgoal_chrID()
    {
        $connection = new Connection();
        $query = "SELECT DISTINCT `chrID` FROM `salesgoal` ORDER BY `chrID`;";
        $result = $connection->result($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = $row['chrID'];
        }
        $connection->close();
        return $contents;
    }
}
