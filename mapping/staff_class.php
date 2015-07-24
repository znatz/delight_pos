<?php

//require_once dirname(__FILE__) . '/../utils/connect.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Staff extends ZModel
{
    protected static $table = 'staff';
    protected $props = [ "chrID", "chrName", "chrLogin_ID", "intAuthority_ID", "chrPasswordHash", "chrSession"];
    protected static $columns = [ "chrID", "chrName", "chrLogin_ID", "intAuthority_ID", "chrPasswordHash"];

    function Staff()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_all_staff()
    {
        $all_staffs = parent::get_all();
        foreach ($all_staffs as $staff) {
            $staff->chrPasswordHash = "";
            $staff->chrSession = "";
        }
        return $all_staffs;
    }

    public static function get_all_staff_chrID()
    {
        return parent::get_column('chrID');
    }

    public static function update_staff_session($staff)
    {
        session_regenerate_id(true);
        $query = "UPDATE staff SET chrSession='" . session_id() . "' WHERE chrLogin_ID= '" . $staff->chrLogin_ID . "'";

        Connection::go_query($query);
        $staff->chrSession = session_id();
        return $staff;
    }

    public static function get_one_staff($chrID)
    {
        $staff = parent::find($chrID);
        $staff->chrPasswordHash = "";
        $staff->chrSession = "";
        return $staff;
    }

    public static function deletete_one_staff($chrID)
    {
        return parent::delete($chrID);
    }

//    public static function insert_one_staff($chrID, $name, $id, $auth, $pass)
    public static function insert_one_staff()
    {
        return parent::insert_to_columns(func_get_args());
    }

    public static function update_one_staff($chrID, $name, $id, $auth, $pass)
    {
        $query = <<<EOF
UPDATE staff SET chrID='$chrID', chrName='$name', chrLogin_ID='$id', intAuthority_ID='$auth', chrPasswordHash='$pass'
WHERE chrID='$chrID' OR chrName='$name' OR chrLogin_ID='$id';
EOF;
        $result = Connection::go_query($query);
        return $result;
    }

}