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
class Salesum extends ZModel
{
    protected $props = [
        "chrShop_ID",
        "intPaymentCountDaily",
        "intCountDaily",
        "intAmountDaily",
        "intPaymentCountMonthly",
        "intCountMonthly",
        "intAmountMonthly",
    ];

    function Salesum()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }

    public static function fillSalesum($monthlySale, $dailySale)
    {
        if (count($monthlySale) >= count($dailySale)) {
            foreach ($monthlySale as $m) {
                foreach ($dailySale as $d) {
                    if ($d->chrShop_ID == $m->chrShop_ID)
                        $contents[] = new Salesum($m->chrShop_ID, $d->intPaymentCount, $d->intCount, $d->intAmount,
                            $m->intPaymentCount, $m->intCount, $m->intAmount);
                }
            }
        } else {
            foreach ($dailySale as $d) {
                foreach ($monthlySale as $m) {
                    if ($d->chrShop_ID == $m->chrShop_ID)
                        $contents[] = new Salesum($m->chrShop_ID, $d->intPaymentCount, $d->intCount, $d->intAmount,
                            $m->intPaymentCount, $m->intCount, $m->intAmount);
                }
            }
        }

        return $contents;
    }
}
