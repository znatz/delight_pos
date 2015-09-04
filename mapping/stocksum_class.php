<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';
require_once './utils/ConstantDb.php';

class Stocksum extends ZModel
{
    protected $props = [
        "chrID",
        "chrGroup",
        "chrSeller",
        "chrMaker",
        "intStockCount",
        "intTotalCost",
        "intTotalPrice",
        "intCostToTotalCost"
    ];

    function Stocksum()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }


    public static function sumWholeStockList_by_Conditions($date, $shop, $group, $seller, $maker, $offset, $length, $byCondition) {
        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        mysqli_set_charset($connection, "utf8");

        if($maker != DB_SELECT_ALL_IN_COLUMN) $searchInMaker = <<<SQL
                  AND goods.chrMaker_ID  = '$maker'
SQL;

        if($seller != DB_SELECT_ALL_IN_COLUMN) $searchInSeller = <<<SQL
                  AND goods.chrSeller_ID = '$seller'
SQL;

        if($group != DB_SELECT_ALL_IN_COLUMN) $searchInGroup = <<<SQL
                  AND goods.chrGroup_ID  = '$group'
SQL;

        if($shop != DB_SELECT_ALL_IN_COLUMN) $searchInShop = <<<SQL
                  AND data_stock.chrShop_ID   = '$shop'
SQL;

        switch((string) $byCondition) {
            case "byGroup" : $condition = "`group`.`chrID`"; break;
            case "bySeller" : $condition = "`seller`.`chrID`"; break;
            case "byMaker" : $condition = "`maker`.`chrID`"; break;
        }

        $query =<<<SQL
        SELECT
            data_stock.chrStockCode as chrID,
            `group`.chrName as chrGroup,
            seller.chrName as chrSeller,
            maker.chrName as chrMaker,
            Sum(data_stock.intStockCount) as intStockCount,
            Sum(goods.intCost * data_stock.intStockCount) as intTotalCost,
            Sum(goods.intPrice * data_stock.intStockCount) as intTotalPrice,
            Sum(goods.intCost) / Sum(goods.intCost * data_stock.intStockCount) as intCostToTotalCost
        FROM
            data_stock data_stock
            LEFT JOIN goods
                  ON data_stock.chrStockCode = goods.chrID
                  AND data_stock.chrDate = '$date'
            INNER JOIN seller seller ON goods.chrSeller_ID   = seller.chrID
                  $searchInSeller
            INNER JOIN maker maker ON goods.chrMaker_ID    = maker.chrID
                  $searchInMaker
            INNER JOIN `group` `group` ON goods.chrGroup_ID    = `group`.chrID
                  $searchInGroup
            INNER JOIN shop shop  ON data_stock.chrShop_ID     = shop.chrID
                  $searchInShop
        GROUP BY
            $condition
            WITH ROLLUP
SQL;
        $result = mysqli_query($connection, $query);

/*  -------------------- SLOW -------------------------------------
        $query =<<<SQL
        SELECT
            data_stock.chrStockCode as chrID
        From
            data_stock data_stock
            LEFT JOIN goods
                  ON data_stock.chrStockCode = goods.chrID
                  AND data_stock.chrDate = '$date'
                  AND goods.chrSeller_ID = '$seller'
                  AND goods.chrMaker_ID  = '$maker'
                  AND goods.chrGroup_ID  = '$group'
                  AND data_stock.chrShop_ID   = '$shop'
            LIMIT $offset, $length;
SQL;
        $result = mysqli_query($connection, $query);

 -------------------- SLOW ------------------------------------- */

        $contents = array();

        if (is_bool($result)) return $result;

        while ($row = mysqli_fetch_array($result)) {
            $stocksum = new Stocksum($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
            $contents[] = $stocksum;
        }


        $lastitem = array_pop($contents);
        $sumofall = new Stocksum(" ","合計","合計","合計", $lastitem->intStockCount, $lastitem->intTotalCost, $lastitem->intTotalPrice, $lastitem->intCostToTotalCost);
        array_push($contents, $sumofall);

        $connection->close();

        return $contents;
    }

}



