<?php

require_once 'connect.php';
require_once 'ConstantDb.php';

function __autoload($class_name) {
    require_once strtolower($class_name).'_class.php';
}
function prefix_ifNotEmpty($prefix, $a)
{
    if (!empty($a)) return $prefix . $a;
    return "";
}

function prefix_ifNotEmpty_else($prefix, $a, $b)
{
    if (!empty($a)) return $prefix . $a;
    return $b;
}

// if $a is set then do $b
function issetThen($a, $b)
{
    if (isset($a)) return $b;
}

// if $a is empty then $b
function ifEmpty($a, $b)
{
    if (empty($a)) return $b;
    return $a;
}

// if $a has value then $a
function ifNotEmpty($a, $b)
{
    if (!empty($a)) return $a;
    return $b;
}

function get_lastet_number($sorted_ary)
{
    $i = 1;
    foreach ((array)$sorted_ary as $chrID => $ID_value) {
        $two_digits = str_pad($i, 2, "0", STR_PAD_LEFT);
        if ($ID_value != $two_digits) return $two_digits;
        $i++;
    }
    return str_pad($i, 2, "0", STR_PAD_LEFT);
}

function get_lastet_3_number($sorted_ary)
{
    $i = 1;
    foreach ((array)$sorted_ary as $chrID => $ID_value) {
        $three_digits = str_pad($i, 3, "0", STR_PAD_LEFT);
        if ($ID_value != $three_digits) return $three_digits;
        $i++;
    }
    return str_pad($i, 3, "0", STR_PAD_LEFT);
}

function session_check()
{
    $connection = new Connection();
    $query = "SELECT * FROM staff WHERE chrSession = '" . session_id() . "'";
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    if (!$row) {
        $connection->close();
        header("Location: ./logout.php?LOGOUT_MSG=強制に");
    }
}

function plain_url_to_link($string)
{
    return preg_replace(
        '%(https?|ftp)://([-A-Z0-9./_*?&;=#]+)%i',
        '<a target="blank" rel="nofollow" href="$0" target="_blank">$0</a>', $string);
}

function get_post($post)
{
    $connection = new Connection();
    $query = "select * from post Where chrID='" . $post . "';";
    $result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
    $rowCount = mysql_num_rows($result);
    if ($rowCount > 0) {
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $address = $row['chrPrefecture'] . $row['chrAddress'];
    } else {
    }
    $connection->close();
    return $address;
}

function get_list($header, $contents, $id, $prop, $table_width)
{
    echo <<<EOF
    <form method="post" id="list" action="">
            <table id="myTable" style="border:0;padding:0;border-radius:5px;width:$table_width;" class="search_table">
                <thead>
                    <tr>
EOF;

    foreach ($header as $name => $width) {
        echo '<th width="' . $width . '">' . $name . '</th>';
    }

    echo '</tr></thead><tbody>';


    foreach ((array)$contents as $row) {
        $rowid = $row->$id;
        echo '<tr class="not_header" id="' . $rowid . '">';
        foreach ($prop as $p => $align) {
            echo '<td style="text-align:' . $align . ';">' . $row->$p . '</td>';
        }
        echo <<<EOF
           <td style="text-align:center;"><input type="radio" onclick="javascript: submit()" name="targetID" id="targetID" value="$rowid"/></td>
           <td style="padding:0 0 0 2px;"><button onClick="if(!confirm(\'削除しますか？\')){return false;}"  class="center_button hvr-fade delete_button" style="width:65px; height:30px; margin:0;padding:0;font-weight:normal;" type="submit" name="delete" value="$rowid">削除</button></td>
           </tr>
EOF;
    }

    echo <<<EOF
                </tbody>
            </table>
        <input type="submit" name="target" style="display: none"/>
    </form>
EOF;

    $_SESSION["sheet"] = serialize($contents);
    array_pop($header);
    array_pop($header);
    $_SESSION["sheet_header"] = array_keys($header);
}

function showCode($Pos)
{
    $connection = new Connection();
    $query = "select * from post Where chrID='" . $Pos . "';";
    $result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
    $rowCount = mysql_num_rows($result);
    if ($rowCount > 0) {
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $Address = $row['chrPrefecture'] . $row['chrAddress'];
        return $Address;
    } else {
    }
    $connection->close();
}

/* ------------------------------------------------------------------------   Database Helper ---------------------------------------------------- */
/* TODO : Add mysqli_real_excape_string to check every value being inserted */
/* ------------------------------------------------------------------------   Database Helper ---------------------------------------------------- */
function insert_to_table_columns($table_name, $columns, $values)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT ' . implode(',', $columns) . ' FROM ' . $table_name;
    $result = $connection->query($query);

    for ($i = 0; $i < count($values); $i++) {
        $types[] = $result->fetch_field_direct($i)->type;
    }

    foreach ($values as $key => $val) {
        if ($types[$key] == MYSQLI_TYPE_VAR_STRING) $quoted[] = '"' . $val . '"';
        else $quoted[] = $val;
    }

    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'INSERT INTO ' . $table_name . ' (' . implode(',', $columns) . ') VALUES (';

    $query .= implode(', ', $quoted);

    $query .= ')';
    $connection->query($query);

    $success = mysqli_errno($connection) > 0 ? false : true;
    return $success;
}

function update_to_table_columns($table_name, $columns, $values, $id)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT ' . implode(',', $columns) . ' FROM ' . $table_name;
    $result = $connection->query($query);

    for ($i = 0; $i < count($values); $i++) {
        $types[] = $result->fetch_field_direct($i)->type;
    }

    foreach ($values as $key => $val) {
        if ($types[$key] == MYSQLI_TYPE_VAR_STRING) $quoted[] = '"' . $val . '"';
        else $quoted[] = $val;
    }

    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'UPDATE ' . $table_name . ' SET ';
    for ($i = 0; $i < count($values); $i++) {
        $query .= ' `' . $columns[$i] . '`=' . $quoted[$i] . ',';
    }

    $query = rtrim($query, ',');

    if (!empty($id)) $query .= " WHERE `chrID` =" . $id;
    $connection->query($query);

    $success = mysqli_errno($connection) > 0 ? false : true;
    return $success;
}

/* WIP*/
function update_to_table_column($table_name, $column, $val, $pk, $id)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT ' . $column . ' FROM ' . $table_name;
    $result = $connection->query($query);

    $type = $result->fetch_field_direct(0)->type;

    if ($val == 'null') {
        $quoted = "NULL";
    } else {
        if ($type == MYSQLI_TYPE_VAR_STRING) $quoted = "'" . $val . "'"; else $quoted = $val;
    }
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'UPDATE ' . $table_name . ' SET ' . $column . '=' . $quoted . ' WHERE ' . $pk . '=' . $id;

    $connection->query($query);
    echo mysqli_error($connection);
    $success = mysqli_errno($connection) > 0 ? false : true;
    return $success;
}

function insert_to_table($table_name, $values)
{

    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT * FROM ' . $table_name;


    $types = array();
    $result = $connection->query($query);
    for ($i = 0; $i < count($values); $i++) {
        $types[] = $result->fetch_field_direct($i)->type;
    }

    foreach ($values as $key => $val) {
        if ($types[$key] == MYSQLI_TYPE_VAR_STRING) $quoted[] = '"' . $val . '"';
        else $quoted[] = $val;
    }

    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'INSERT INTO ' . $table_name . ' VALUES (';

    $query .= implode(', ', $quoted);

    $query .= ')';
    $connection->query($query);

    $success = mysqli_errno($connection) == 0 ? 1 : 0;
    return $success;
}

function get_one_column($table_name, $column)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT ' . $column . ' FROM ' . $table_name . ' ORDER BY ' . $column;
    $result = $connection->query($query);
    if ($result->num_rows == 1) {
        $row = mysqli_fetch_assoc($result);
        $contents[] = $row[$column];
    }

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        $contents[] = $row[$column];
    }

    return $contents;
}

function get_one_distinct_column($table_name, $column)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT DISTINCT ' . $column . ' FROM ' . $table_name;
    $result = $connection->query($query);
    if ($result->num_rows == 1) {
        $row = mysqli_fetch_assoc($result);
        $contents[] = $row[$column];
    }

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        $contents[] = $row[$column];
    }

    return $contents;
}

function get_columns_order_by($table_name, $columns, $id)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT ' . implode(',', $columns) . ' FROM ' . $table_name . ' ORDER BY ' . $id;
    $result = $connection->query($query);
    if ($result->num_rows == 1) {
        $row = mysqli_fetch_assoc($result);
        $e = array();
        foreach ($columns as $col) {
            array_push($e, $row[$col]);
        }
        $contents[] = $e;
    }

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $e = array();
        foreach ($columns as $col) {
            array_push($e, $row[$col]);
        }
        $contents[] = $e;
    }

    return $contents;
}

function get_distinct_columns_order_by($table_name, $columns, $id)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");
    $query = 'SELECT DISTINCT ' . implode(',', $columns) . ' FROM ' . $table_name . ' ORDER BY ' . $id;
    $result = $connection->query($query);
    echo mysqli_error($connection);
    if ($result->num_rows == 1) {
        $row = mysqli_fetch_assoc($result);
        $e = array();
        foreach ($columns as $col) {
            array_push($e, $row[$col]);
        }
        $contents[] = $e;
    }

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $e = array();
        foreach ($columns as $col) {
            array_push($e, $row[$col]);
        }
        $contents[] = $e;
    }

    return $contents;
}


function get_all_from_table($table_name, $query)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, "utf8");

    $query = ifNotEmpty($query, "SELECT * FROM " . strtolower($table_name));

    $table_name = trim($table_name, '`');
    $table_name = rtrim($table_name, '``');
    require_once $table_name . '_class.php';
    $result = mysqli_query($connection, $query);
    $contents = array();

    if (is_bool($result)) return $result;

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $keys = array_keys($row);
        $obj = new $table_name;
        foreach ($keys as $k) {
            $obj->$k = $row[$k];
        }
        $contents[] = $obj;
        return $contents;
    };

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

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
