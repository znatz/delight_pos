<?php

require_once 'connect.php';

class Systemsetting
{
    public $chrID;
    public $intRate;
    public $intRoundType;
    public $chrCompanyName;
    public $chrPost;
    public $chrAddress;
    public $chrAddressNo;
    public $chrTel;
    public $chrFax;
    public $chrInvoiceComment1;
    public $chrInvoiceComment2;
    public $chrInvoiceComment3;

    function Systemsetting($id = "", $rate = "", $round = "", $company = "", $post = "", $address = "", $addressno = "", $tel = "", $fax = "", $comment1 = "", $comment2 = "", $comment3 = "")
    {
        $this->chrID = $id;
        $this->intRate = $rate;
        $this->intRoundType = $round;
        $this->chrCompanyName = $company;
        $this->chrPost = $post;
        $this->chrAddress = $address;
        $this->chrAddressNo = $addressno;
        $this->chrTel = $tel;
        $this->chrFax = $fax;
        $this->chrInvoiceComment1 = $comment1;
        $this->chrInvoiceComment2 = $comment2;
        $this->chrInvoiceComment3 = $comment3;
    }

    public static function get_all_systemsetting()
    {
       return Connection::get_all_from_table('systemsetting');
    }

    public static function get_companyname_from_chrID($id)
    {
        $connection = new Connection();
        $query = "SELECT chrCompanyName FROM systemsetting WHERE chrID=" . $id;

        $result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $connection->close();
        return $row['chrCompanyName'];
    }

    public static function get_companyid_and_name()
    {

        $connection = new Connection();
        $query = "SELECT chrID, chrCompanyName FROM systemsetting;";

        $result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $connection->close();
        return array($row['chrID'], $row['chrCompanyName']);
    }

}