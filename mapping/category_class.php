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

    public static function get_all_category_chrID() {
        return parent::get_column('chrID');
    }

    public static function get_distinct_category_chrID() {
        $query = "SELECT DISTINCT * FROM `category` ORDER BY `chrID`;";
        return get_all_from_table(self::$table, $query);
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
