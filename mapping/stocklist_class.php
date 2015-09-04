<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';
require_once './utils/ConstantDb.php';

class Stocklist extends ZModel
{
    protected $props = [
        "chrID",
        "chrCode",
        "chrName",
        "chrGroup",
        "chrSeller",
        "chrMaker",
        "chrColor",
        "chrSize",
        "intCost",
        "intPrice",
        "intStockCount",
        "intTotalCost",
        "intTotalPrice"
    ];

    function Stocklist()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }
    public static function Stocklist_Construtor2()
    {
        $parameters = func_get_args();
        return new Stocklist($parameters[0]);
    }

    public static function buildOneStockList($barcode) {
        $data_stock = Data_stock::findBy('chrStockCode', $barcode);
        $goods      = Goods::find($barcode);
        $group      = Group::find($goods->chrGroup_ID);
        $seller     = Seller::find($goods->chrSeller_ID);
        $maker      = Maker::find($goods->chrMaker_ID);
        $shop       = Shop::find($data_stock->chrShop_ID);
        return new Stocklist(
            $goods->chrID,
            $goods->chrCode,
            $goods->chrName,
            $group->chrName,
            $seller->chrName,
            $maker->chrName,
            $goods->chrColor,
            $goods->chrSize,
            $goods->intCost,
            $goods->intPrice,
            $data_stock->intStockCount,
            $goods->intCost * $data_stock->intStockCount,
            $goods->intPrice * $data_stock->intStockCount
        );
    }

    public static function buildWholeStockList($barcodeList) {
        foreach($barcodeList as $barcode) {
            $result[] = self::buildOneStockList($barcode);
        }
        return $result;
    }

    public static function buildWholeStockList_by_Conditions($date, $shop, $group, $seller, $maker, $offset, $length) {
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

        $query =<<<SQL
        SELECT
            data_stock.chrStockCode as chrID,
            goods.chrCode as chrCode,
            goods.chrName as chrName,
            `group`.chrName as chrGroup,
            seller.chrName as chrSeller,
            maker.chrName as chrMaker,
            goods.chrColor as chrColor,
            goods.chrSize as chrSize,
            goods.intCost as intCost,
            goods.intPrice as intPrice,
            Sum(data_stock.intStockCount) as intStockCount,
            Sum(goods.intCost * data_stock.intStockCount) as intTotalCost,
            Sum(goods.intPrice * data_stock.intStockCount) as intTotalPrice
        From
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
            GROUP By chrID WITH ROLLUP
            LIMIT $offset, $length
            ;
SQL;
        $result = mysqli_query($connection, $query);

        $contents = array();

        if (is_bool($result)) return $result;

        $sumStockList = new Stocklist();
        $sumStockList->intPrice = "合計";
        while ($row = mysqli_fetch_array($result)) {
            $stocklist = new Stocklist($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12] );
            $sumStockList->intStockCount += $stocklist->intStockCount;
            $sumStockList->intTotalCost += $stocklist->intTotalCost;
            $sumStockList->intTotalPrice += $stocklist->intTotalPrice;
            $contents[] = $stocklist;
        }

        $contents[] = $sumStockList;
        $connection->close();
        return $contents;
    }

}



