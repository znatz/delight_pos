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

    // Without Rollup
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

    // With Rollup
    public static function sum_all_with_range_of_date_in_shop($sumColumns, $from, $to, $shop_id)
    {

        $query = <<<SQL
        SELECT * FROM (
                          SELECT
                                  chrDate,
                                  chrShop_ID,
                                  Sum(intCount) AS intCount,
                                  Sum(intAmount) AS intAmount,
                                  Sum(intProfit) AS intProfit,
                                  Sum(intTax) AS intTax,
                                  Sum(intPaymentCount) AS intPaymentCount,
                                  Sum(intCredit) AS intCredit,
                                  Sum(intRevenue) AS intRevenue,
                                  Sum(intRemain) AS intRemain,
                                  Sum(intExchangeCheck) AS intExchangeCheck,
                                  Sum(intServiceTicket) AS intServiceTicket,
                                  Sum(intReceivable) AS intReceivable,
                                  Sum(intPartialPayment) AS intPartialPayment
                          FROM data_sale
                          WHERE chrDate BETWEEN \'2015/08/01\' AND \'2015/08/31\'
                          Group By chrDate, chrShop_ID With Rollup
                      ) as t ORDER BY isnull(`chrDate`),`chrDate`, ISNULL(chrShop_ID), `chrShop_ID`
SQL;

        $query = "SELECT * FROM (";
        $query = $query . "SELECT chrDate, chrShop_ID, ";

        foreach($sumColumns as $column_name) {
            $query = $query." Sum(".$column_name.") AS ".$column_name. ",";
        }
        $query = rtrim($query, ",");

        $query = $query. " FROM ".static::$table." WHERE chrDate BETWEEN '".$from."' AND '".$to."'";
        if($shop_id != '00') $query = $query." AND chrShop_ID IN (".$shop_id.')';
        $query = $query. " Group By chrDate, chrShop_ID With Rollup";

        $query = $query. ") as t ORDER BY isnull(`chrDate`),`chrDate`, ISNULL(chrShop_ID), `chrShop_ID`";

        $result =  get_all_from_table(static::$table, $query);
        return $result;
    }


    public static  function findMonth($year, $month) {
        $day = new DateTime($year.'-'.$month.'-01');
        return date_format($day, "Y/m");
    }

     public static function sum_all_with_range_of_month_in_shop($fromYear, $fromMonth, $toYear, $toMonth, $shop_id)
    {
        $queryPart1 = <<<SQL
                   SELECT chrDateRange as chrDate,
                   chrShop_ID,
                   intCount,
                   Sum(intAmount) AS intAmount,
                   Sum(intProfit) AS intProfit,
                   Sum(intTax) AS intTax,
                   Sum(intPaymentCount) AS intPaymentCount,
                   Sum(intCredit) AS intCredit,
                   Sum(intRevenue) AS intRevenue,
                   Sum(intRemain) AS intRemain,
                   Sum(intExchangeCheck) AS intExchangeCheck,
                   Sum(intServiceTicket) AS intServiceTicket,
                   Sum(intReceivable) AS intReceivable,
                   Sum(intPartialPayment) AS intPartialPayment
            FROM
              (SELECT CASE
SQL;

        $queryPart2 = "";
        $yearDiff = $toYear     - $fromYear;
        $monDiff  = $toMonth    - $fromMonth;
        if($toMonth == $fromMonth) $monDiff = 1;

        for( $y = 0; $y <= $yearDiff; $y++) {
            $fromYear = $fromYear + $y;
            for ( $m = 0; $m < $monDiff; $m++) {
                $from = self::findMonth($fromYear, ($fromMonth + $m));
                $to = self::findMonth($fromYear, ($fromMonth + $m + 1));
                $queryPart2_parts = <<<SQL
                          WHEN chrDate Between '$from/01' AND '$from/31' THEN '$from'
SQL;
                $queryPart2 .= $queryPart2_parts;
            }
        }

        $queryPart3 = <<<SQL
                      END AS chrDateRange,
                      chrShop_ID,
                      Sum(intCount) As intCount,
                      Sum(intAmount) As intAmount,
                      Sum(intProfit) As intProfit,
                      Sum(intTax) As intTax,
                      Sum(intPaymentCount) As intPaymentCount,
                      Sum(intCredit) As intCredit,
                      Sum(intRevenue) As intRevenue,
                      Sum(intRemain) As intRemain,
                      Sum(intExchangeCheck) As intExchangeCheck,
                      Sum(intServiceTicket) As intServiceTicket,
                      Sum(intReceivable) As intReceivable,
                      Sum(intPartialPayment) As intPartialPayment
               FROM data_sale
SQL;
        $queryPart4 = <<<SQL
               WHERE chrDate BETWEEN '$fromYear/$fromMonth/01' AND '$toYear/$toMonth/31'
SQL;
        if($shop_id != '00') $queryPart4 = $queryPart4." AND chrShop_ID IN (".$shop_id.')';

        $queryPart5 = <<<SQL
               GROUP BY chrDateRange, chrShop_ID with rollup) AS t
            GROUP BY chrDateRange,
                     chrShop_ID
            ORDER BY isnull(`chrDate`),
                     `chrDate`,
                     ISNULL(chrShop_ID),
                     `chrShop_ID`

SQL;


        $query = $queryPart1 . $queryPart2 . $queryPart3 . $queryPart4 . $queryPart5;
        $result =  get_all_from_table(static::$table, $query);
        return $result;
    }


}