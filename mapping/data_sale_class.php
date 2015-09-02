<?php

require_once "helper.php";
require_once "connect.php";
require_once "ZModel.php";
/**
 * Created by PhpStorm.
 * User: POSCO
 * Date: 2015/08/31
 * Time: 15:38
 */
class Data_sale extends ZModel
{
    protected static $table = "data_sale";
    protected $props = ["chrDate",
                        "chrTime",
                        "chrShop_ID",
                        "chrTerminalNo",
                        "chrReceiptNo",
                        "chrStaff",
                        "chrMember_Code",
                        "chrMember_ID",
                        "chrSeller_ID",
                        "chrGroup_ID",
                        "chrUnit_ID",
                        "chrSize_ID",
                        "chrVar1",
                        "chrVar2",
                        "chrVar3",
                        "chrSalesFlg",
                        "intCost" ,
                        "intRetailPrice" ,
                        "intPrice" ,
                        "intCount" ,
                        "intAmount" ,
                        "intProfit" ,
                        "intTotal" ,
                        "intDiscount" ,
                        "intTax" ,
                        "intRevenue" ,
                        "intRemain" ,
                        "intReceivable" ,
                        "intPartialPayment" ,
                        "intCredit" ,
                        "intExchangeCheck" ,
                        "intServiceTicket" ,
                        "intPaymentCount" ,
                        "chrBarCode1" ,
                        "chrBarCode2" ,
                        "chrBarCode3" ,
                        "intOutput" ,
                        "intInput" ,
                        "intLastPoint" ,
                        "intTodayPoint" ,
                        "intSpendPont",
                        "intFlg" ,
                        "intTemporary" ,
                        "chrTemporary" ,
                        "intCost2" ,
                        "intRetailPrice2" ,
                        "intPrice2" ,
                        "chrMinute",
                        "chrLineNo",
                        "intTemporary1",
                        "intTemporary2" ,
                        "intTemporary3" ,
                        "intTemporary4" ,
                        "intTemporary5" ,
                        "chrTemporary1" ,
                        "chrTemporary2" ,
                        "chrTemporary3" ,
                        "chrTemporary4" ,
                        "chrTemporary5" ,
                        "chrKeyCode"];

    protected static $columns = ["chrDate",
                        "chrTime",
                        "chrShop_ID",
                        "chrTerminalNo",
                        "chrReceiptNo",
                        "chrStaff",
                        "chrMember_Code",
                        "chrMember_ID",
                        "chrSeller_ID",
                        "chrGroup_ID",
                        "chrUnit_ID",
                        "chrSize_ID",
                        "chrVar1",
                        "chrVar2",
                        "chrVar3",
                        "chrSalesFlg",
                        "intCost" ,
                        "intRetailPrice" ,
                        "intPrice" ,
                        "intCount" ,
                        "intAmount" ,
                        "intProfit" ,
                        "intTotal" ,
                        "intDiscount" ,
                        "intTax" ,
                        "intRevenue" ,
                        "intRemain" ,
                        "intReceivable" ,
                        "intPartialPayment" ,
                        "intCredit" ,
                        "intExchangeCheck" ,
                        "intServiceTicket" ,
                        "intPaymentCount" ,
                        "chrBarCode1" ,
                        "chrBarCode2" ,
                        "chrBarCode3" ,
                        "intOutput" ,
                        "intInput" ,
                        "intLastPoint" ,
                        "intTodayPoint" ,
                        "intSpendPont",
                        "intFlg" ,
                        "intTemporary" ,
                        "chrTemporary" ,
                        "intCost2" ,
                        "intRetailPrice2" ,
                        "intPrice2" ,
                        "chrMinute",
                        "chrLineNo",
                        "intTemporary1",
                        "intTemporary2" ,
                        "intTemporary3" ,
                        "intTemporary4" ,
                        "intTemporary5" ,
                        "chrTemporary1" ,
                        "chrTemporary2" ,
                        "chrTemporary3" ,
                        "chrTemporary4" ,
                        "chrTemporary5" ,
                        "chrKeyCode"];

    function Data_sale()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function get_all_with_limits($l)
    {
        return get_all_from_table_with_limits(static::$table, '', $l);
    }

    public static function get_all_with_date_range($from, $to, $shop_id)
    {
        if(isset($shop_id)) {
            $query = "SELECT * FROM ".static::$table." WHERE chrDate BETWEEN '".$from."' AND '".$to."' AND chrShop_ID=".$shop_id;
            $query = $query." ORDER BY chrDate, chrShop_ID";
        } else {
            $query = "SELECT * FROM ".static::$table." WHERE chrDate BETWEEN '".$from."' AND '".$to."'";
            $query = $query." ORDER BY chrDate, chrShop_ID";
        }
        return get_all_from_table(static::$table, $query);
    }

    public static function sum_all_with_date_range_in_shop($sumColumns, $from, $to, $shop_id)
    {
        $query = "SELECT chrDate, chrShop_ID, ";

        foreach($sumColumns as $column_name) {
            $query = $query." Sum(".$column_name.") AS ".$column_name. ",";
        }
        $query = rtrim($query, ",");

        $query = $query. " FROM ".static::$table." WHERE chrDate BETWEEN '".$from."' AND '".$to."'";
        if($shop_id != '00') $query = $query." AND chrShop_ID IN (".$shop_id.')';
        $query = $query. " Group By chrDate, chrShop_ID";

        $query = $query. " ORDER BY chrDate, chrShop_ID";

        $result =  get_all_from_table(static::$table, $query);
        return $result;
    }

}