<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Group extends ZModel
{
    /* group is keyword is mysql !*/
    protected static $table = "`group`";
    protected $props = ['chrID', 'chrName', 'intCost_Rate', 'chrCategory_ID', 'intTax_Rate'];
    protected static $columns = ['chrID', 'chrName', 'intCost_Rate', 'chrCategory_ID', 'intTax_Rate'];

    function Group()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_new_group()
    {
        return new Group(get_lastet_number(parent::get_column('chrID')));
    }

    public static function get_distinct_group_chrID()
    {
        return parent::get_distinct_columns('chrID', 'chrName');
    }
}
