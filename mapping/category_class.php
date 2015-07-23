<?php

require_once 'helper.php';
require_once 'connect.php';

class Category {
    public $chrID;
    public $chrName;
    function Category($id="", $name="") {
        $this->chrID = $id;
        $this->chrName = $name;
    }

    public static function get_one_empty_category() {
        return new Category("", "");
    }

    public static function get_all_category() {
        $connection = new Connection();
        $query = "SELECT * FROM `category`;";
        $result = $connection->result($query);
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = new Category($row['chrID'], $row['chrName']);
        }
        return $contents;
    }

    public static function get_all_category_chrID() {
        $connection = new Connection();
        $query = "SELECT `chrID` FROM `category` ORDER BY `chrID`;";
        $result = $connection->result($query);
        echo mysql_error();
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = $row['chrID'];
        }
        $connection->close();
        return $contents;
    }

    public static function get_distinct_category_chrID() {
        $connection = new Connection();
        $query = "SELECT DISTINCT * FROM `category` ORDER BY `chrID`;";
        $result = $connection->result($query);
        echo mysql_error();
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $contents[] = new Category($row['chrID'], $row['chrName']);
        }
        $connection->close();
        return $contents;
    }

    public static function get_one_category($chrID) {
        $connection = new Connection();
        $query = "SELECT * FROM `category` WHERE `chrID`=".$chrID.";";
        $result = $connection->result($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $contents = new Category($row['chrID'],
                                    $row['chrName']);

        $connection->close();
        return $contents;

    }

    public static function insert_one_category($id, $name) {
        $connection = new Connection();
        $query = <<<SQL
INSERT INTO `category` (`chrID`,
                        `chrName`)
VALUES ('$id', '$name');
SQL;
        $result = $connection->result($query);
        return $result;

    }

    public static function update_one_category($id, $name) {
        $connection = new Connection();
        $query = <<<EOF
UPDATE `category` SET `chrName`='$name'
WHERE `chrID`='$id';
EOF;
        $result = $connection->result($query);
        $connection->close();
        return $result;

    }

    public static function delete_one_category($chrID) {
        $connection = new Connection();
        $query = "DELETE FROM `category` WHERE `chrID`=".$chrID.";";
        $result = $connection->result($query);
        echo mysql_error();
        $connection->close();
        return $result;

    }

    public static function get_new_category() {
        $id = get_lastet_number(self::get_all_category_chrID());
        $result = new Category($id,"");
        return $result;
    }
}
