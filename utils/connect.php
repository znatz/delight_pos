<?php

require_once 'ConstantDb.php';
require_once 'helper.php';

class Connection {
	public $link; 
	function Connection() {
		$this->link = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die('Could not connect: ' . mysql_error());

		mysql_query('SET NAMES SJIS');
		mysql_query('SET LC_MESSAGES =  "ja_JP"');
		mysql_set_charset('utf8');
		mysql_select_db(DB_NAME, $this->link);

		$bool = mysql_select_db(DB_NAME, $this->link);
		if ($bool === False) {
			print "DB_NAME存在しません。";
		}

	}

	public function result($query) {
		$result = mysql_query($query);
		if(mysql_error()) return null;
		return $result;
	}


	public function close() {
		mysql_close($this->link);
	}

    public static function go_query($query) {
        $conn = new Connection();
        return $conn->result($query);
    }
    public static function get_all_from_table($table_name) {
        require_once $table_name.'_class.php';
        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS,DB_NAME);
        mysqli_set_charset($connection, "utf8");

        $query = "SELECT * FROM ".strtolower($table_name);
        $result = mysqli_query($connection, $query);
        $contents = array();

        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
             $keys = array_keys($row);
            $obj = new $table_name;
            foreach ($keys as $k) {
                    $obj->$k = $row[$k];
            }
            return $obj;
        };

        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {

            $keys = array_keys($row);
            $obj = new $table_name;
            foreach ($keys as $k) {
                    $obj->$k = $row[$k];
            }
            $contents[] = $obj;
        }
        $connection->close();
        return $contents;
    }
}
