<?php

require_once 'connect.php';
/**
 * Created by ZNATZ.
 * User: POSCO
 * Date: 2015/07/23
 * Time: 9:54
 */
class ZModel
{
    protected $props;
    protected static $table;
    protected static $columns;

    public function __construct()
    {
        foreach ($this->props as $key => $val) {
            $this->$val = nil;
        }
    }

    public static function get_all()
    {
        return get_all_from_table(static::$table, '');
    }

    public static function get_column($column)
    {
        return get_one_column(static::$table, $column);
    }
    public static function get_columns()
    {
        return get_columns_order_by(static::$table, func_get_args(),func_get_args()[0]);
    }
    public static function get_distinct_columns()
    {
        return get_distinct_columns_order_by(static::$table, func_get_args(),func_get_args()[0]);
    }

    public static function get_distinct_column($column)
    {
        return get_one_distinct_column(static::$table, $column);
    }

    public static function insert_values()
    {
        return insert_to_table(static::$table, func_get_args()[0]);
    }

    public static function insert_to_columns()
    {
        return insert_to_table_columns(static::$table, static::$columns, func_get_args()[0]);
    }

    public static function update_to_columns()
    {
        return update_to_table_columns(static::$table, static::$columns, func_get_args()[0], func_get_args()[0][0]);
    }
    public static function update_to_column($column, $value, $pk, $id)
    {
        return update_to_table_column(static::$table, $column, $value, $pk, $id);
    }

    public static function find($id)
    {
        $query = 'SELECT * FROM ' . static::$table . ' WHERE `chrID`=' . $id;
        return get_all_from_table(static::$table, $query)[0];
    }

    public static function findBy($column, $val)
    {
        $query = 'SELECT * FROM ' . static::$table . ' WHERE '.$column.'='. $val;
        return get_all_from_table(static::$table, $query)[0];
    }
    public static function delete($id)
    {
        $query = 'DELETE FROM ' . static::$table . ' WHERE `chrID`=' . $id;
        return get_all_from_table(static::$table, $query);
    }


}
