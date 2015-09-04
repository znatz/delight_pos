<?php
require_once 'helper.php';

session_start();
session_check();

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

extract($_POST);
extract($_GET);

// Debug
$successMessage = "サンプルデータベースに全条件テストため＝＞　日付:2015/08/21 部門:01 仕入先:東京サンエス メーカー:deuter 店舗:01";
$errorMessage = "サンプルデーターベースに部門が足りませんので集計はあってません。";
// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全商品データを一回取り出す
//$contents = Data_sale::get_all_with_date_range("2015/08/01", "2015/08/31", "");
$groups = Group::get_distinct_group_chrID();
$shops = Shop::get_distinct_shop_chrID();;
array_shift($shops); // remove shop 00
$sellers = Seller::get_distinct_column('chrID');
$makers = Maker::get_distinct_column('chrID');

$contents = Stocklist::buildWholeStockList_by_Conditions("2015/08/21", DB_SELECT_ALL_IN_COLUMN, DB_SELECT_ALL_IN_COLUMN, DB_SELECT_ALL_IN_COLUMN, DB_SELECT_ALL_IN_COLUMN, 10 * ($page_counter), 10);

// 検索押された
if (isset($search) || isset($change_page) && !isset($byCondition)) {
    $contents = Stocklist::buildWholeStockList_by_Conditions($dateFrom, $searchInShop, $searchInGroup, $searchInSeller, $searchInMaker, 10 * ($page_counter), 10);
} elseif (isset($byCondition)) {
    $contents = [];
    $contents = Stocksum::sumWholeStockList_by_Conditions($dateFrom, $searchInShop, $searchInGroup, $searchInSeller, $searchInMaker, 0, 0, $byCondition);
}

?>

<!DOCTYPE html>
<head>
    <? include('./html_parts/css_and_js.html'); ?>

    <script src="./js/autoNumeric.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {

            $('.date_cell').each(function () {
                var $this = $(this);
                if ($.trim($this.closest('tr').prev('tr').find('.date_cell').text()) == $this.text()) {
                    $this.hide();
                }
            });

            var decimalLength = function () {
                /* your code here */
                var value = 0;
                /* example only */
                return value;
            };

            $('.currency_cell').autoNumeric("init", {
                aSep: ',',
                mDec: decimalLength,
            });


            $('#main-menu').smartmenus();
            jQuery("#user_add_form").validationEngine();

            $('#right-menu').sidr({
                name: 'sidr-right',
                side: 'right'
            });
            $('#right-menu-seller').sidr({
                name: 'sidr-right-seller',
                side: 'right'
            });

            // Datepicker Setup
            var dateFormat = 'yy/mm/dd';
            $("#from").datepicker({dateFormat: dateFormat});

            // Page counter
            $('.page_up').click(function () {
                var counter = $('.page_counter').val();
                counter--;
                $('.page_counter').val(counter);
                $('.searchForm').submit();
            });
            $('.page_down').click(function () {
                var counter = $('.page_counter').val();
                counter++;
                $('.page_counter').val(counter);
                $('.searchForm').submit();
            });
            $('.page_reset').click(function () {
                var counter = 0;
                $('.page_counter').val(counter);
                $('.searchForm').submit();
            })

        });
    </script>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
    <style type="text/css">
        p.list {
            width: 900px;
        }

        select {
            float: left;
            width: 289px;
            margin-bottom: 0;
        }

        label.list {
            width: 100px;
        }

        .yen-mark {
            background-color: gray;
            background-image: linear-gradient(transparent 70%, rgba(100, 100, 100, .5) 50%);
            background-size: 2px 2px;
            margin: 0 !important;
            padding-top: 10px !important;
            display: block;
            height: 27px !important;
            color: white;
            text-align: center !important;
        }

        tbody tr:last-child {
            background-color: #9acfea !important;
        }

        <?
//        Hide cell in Summing Line
        if(!isset($byCondition)) {
        for($i=0;$i<9;$i++) {
        $css = <<<CSS
        tbody tr:last-child td:nth-child($i) {
            border-right:0;
        }
CSS;


        echo $css;
        }
        };

        ?>
    </style>
</head>
<body>
<div class="blended_grid">
    <div class="pageHeader">
        <? include('./html_parts/header.html'); ?>
    </div>
    <div class="pageContent">
        <? include('./html_parts/top_menu.html'); ?>
        <div class="main">
            <? include('./html_parts/warning.html'); ?>

            <!-- ********************* マスタ一覧の作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form"
                      style="margin: 0 auto; margin-bottom:0px; width: 950px; height: 250px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>在庫問い合わせ</legend>
                    </fieldset>

                    <p class="list">
                        <label class="list">日付</label>
                        <input
                            id="from"
                            style="width: 293px; margin: 0 0 0 18px;" type="text" size="10"
                            data-prompt-position="topLeft:140" name="dateFrom"
                            placeholder="2015/08/21"
                            value="<?php
                            echo ifNotEmpty($dateFrom, "");
                            ?>"/>
                        <label class="list" style="width:80px;">店舗</label>
                        <select
                            style="width:300px;"
                            tabindex="3"
                            name="searchInShop">
                            <option value="<? echo DB_SELECT_ALL_IN_COLUMN; ?>">全店舗</option>
                            <? foreach ($shops as $s) : ?>
                                <option
                                    value="<? echo $s[0] ?>"
                                    <? if ($searchInShop == $s[0]) echo "selected"; ?>
                                    ><? echo $s[0] . " " . $s[1]; ?></option>
                            <? endforeach; ?>
                        </select>
                    </p>

                    <p class="list">
                        <label class="list">部門</label>
                        <select
                            style="width:300px;"
                            tabindex="3"
                            name="searchInGroup">
                            <option value="<? echo DB_SELECT_ALL_IN_COLUMN; ?>">全部門</option>
                            <? foreach ($groups as $g) : ?>
                                <option
                                    value="<? echo $g[0] ?>"
                                    <? if ($searchInGroup == $g[0]) echo "selected"; ?>
                                    ><? echo $g[0] . " " . Group::find($g[0])->chrName; ?></option>
                            <? endforeach; ?>
                        </select>
                        <label class="list" style="width:80px;">仕入先</label>
                        <select
                            style="width:300px;"
                            tabindex="3"
                            name="searchInSeller">
                            <option value="<? echo DB_SELECT_ALL_IN_COLUMN; ?>">全仕入先</option>
                            <? foreach ($sellers as $seller) : ?>
                                <option
                                    value="<? echo $seller ?>"
                                    <? if ($searchInSeller == $seller) echo "selected"; ?>
                                    ><? echo $seller . " " . Seller::find($seller)->chrName; ?></option>
                            <? endforeach; ?>
                        </select>
                    </p>

                    <p class="list">
                        <label class="list">メーカー</label>
                        <select
                            style="width:300px;"
                            tabindex="3"
                            name="searchInMaker">
                            <option value="<? echo DB_SELECT_ALL_IN_COLUMN; ?>">全メーカー</option>
                            <? foreach ($makers as $m) : ?>
                                <option
                                    value="<? echo $m ?>"
                                    <? if ($searchInMaker == $m) echo "selected"; ?>
                                    ><? echo $m . " " . Maker::find($m)->chrName; ?></option>
                            <? endforeach; ?>
                        </select>
                        <label class="list" style="width:80px;">集計方法</label>
                        <select
                            style="width:300px;"
                            tabindex="3"
                            name="byCondition">
                            <option value="byGroup" <? if ($byCondiontion == "byGroup") echo 'selected'; ?>>部門別</option>
                            <option value="bySeller" <? if ($byCondition == "bySeller") echo 'selected'; ?>>仕入先別
                            </option>
                            <option value="byMaker" <? if ($byCondition == "byMaker") echo 'selected'; ?>>メーカー別</option>
                        </select>
                    </p>


                    <div
                        style="float: right; width: -100%; height: 100px; margin: 0px 0 0 0; text-align: center; vertical-align: middle;">
                        <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="search"
                            size="10" value="検索"/>
                        <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="sum"
                            size="10" value="集計"/>
                        <a
                            tabindex="8"
                            class="center_button hvr-fade" href="./stock.php"
                            style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                        <a
                            tabindex="9"
                            class="center_button hvr-fade" href="./index.php"
                            style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
                        <a class="center_button hvr-fade" href="../utils/excel_export.php"
                           style="display: block; text-decoration: none; width: 150px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">EXCELへ出力&nbsp;<i
                                class="fa fa-file-text-o"></i>&nbsp;</a>
                        <a class="center_button hvr-fade" href="../utils/csv_export.php"
                           style="display: block; text-decoration: none; width: 130px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">CSVへ出力&nbsp;<i
                                class="fa fa-file-text-o"></i>&nbsp;</a>
                    </div>

                </form>
            </div>
        </div>
        <!-- ********************* マスタの作成 終了　**********************	-->

        <!-- ********************* リストの作成 開始　**********************	-->
        <!-- ********************* Page Counter : Start　**********************	-->
        <p style="float: left; text-align: center; width: 300px;" id="buttonlist">

        <div style=" height: 60px; margin: 0; text-align: center; vertical-align: middle;">
            <form
                <? if ($sum) echo 'hidden'; ?>
                style="margin: 0 auto; margin-bottom:0px; width: 950px; height: 250px;"
                method="post">
                <p class="list" style="height:50px; float:left;">
                    <label class="list" style="padding-top:3px">現在ページ <? echo $page_counter; ?></label>
                    <input
                        tabindex="7"
                        class="center_button hvr-fade page_reset" type="submit"
                        style="width:100px;margin-top:0;margin-bottom:0"
                        size="10" value="最初へ"/>
                    <input
                        tabindex="7"
                        class="center_button hvr-fade page_up" type="submit"
                        style="width:100px;margin-top:0;margin-bottom:0"
                        size="10" value="前のページ"/>
                    <input
                        tabindex="7"
                        class="center_button hvr-fade page_down" type="submit"
                        style="width:100px;margin-top:0;margin-bottom:0"
                        size="10" value="次のページ"/>
                    <input type="text" class="page_counter" name="page_counter" hidden="true"
                           value="<? echo $_POST["page_counter"]; ?>"/>
                </p>
            </form>
        </div>
        </p>
        <!-- ********************* Page Counter : End　**********************	-->

        <div id="user_list" style="overflow:auto !important; margin-top:5px;width:1000px; height:600px;">

            <form method="post" id="list" action="" style="overflow: auto;margin-top:0;">

                <?
                if (!$sum) {
                    $header = ["コード" => 50,
                        "品番" => 50,
                        "品名" => 280,
                        "部門" => 150,
                        "仕入先" => 150,
                        "メーカー" => 150,
                        "カラー" => 150,
                        "サイズ" => 150,
                        "原価" => 150,
                        "売価" => 150,
                        "在庫数" => 150,
                        "原価金額" => 150,
                        "売価金額" => 150,];

                    $prop = ["chrID" => 'center',
                        "chrCode" => 'center',
                        "chrName" => 'center',
                        "chrGroup" => 'center',
                        "chrSeller" => 'center',
                        "chrMaker" => 'center',
                        "chrColor" => 'center',
                        "chrSize" => 'center',
                        "intCost" => 'center',
                        "intPrice" => 'center',
                        "intStockCount" => 'center',
                        "intTotalCost" => 'center',
                        "intTotalPrice" => 'center'];
                    $table_length = "1500px";

                } else {
                    $header = ["コード" => 50];
                    $prop = ["chrID" => 'center',];
                    $table_length = "1000px";
                    switch ((string)$byCondition) {
                        case "byGroup" :
                            $header["部門"] = 450;
                            $prop["chrGroup"] = 'center';
                            break;
                        case "bySeller" :
                            $header["仕入先"] = 450;
                            $prop["chrSeller"] = 'center';
                            break;
                        case "byMaker" :
                            $header["メーカー"] = 450;
                            $prop["chrMaker"] = 'center';
                            break;
                    }
                    $header += ["在庫数" => 100,
                        "原価金額" => 150,
                        "売価金額" => 150,
                        "構成比" => 150];
                    $prop += ["intStockCount" => 'center',
                        "intTotalCost" => 'center',
                        "intTotalPrice" => 'center',
                        "intCostToTotalCost" => 'center'];
                }
                get_list_without_buttons($header, $contents, "chrCode", $prop, $table_length);

                ?>
                <input type="submit" name="target" style="display: none"/>
            </form>
        </div>
    </div>
    <!-- ********************* リストの作成  終了　********************** -->
    <? include('./html_parts/footer.html'); ?>
</div>
</div>
<!-- ********************  入力規則　開始      *********************** -->
<div id="sidr-right">
    <?
    $connection = new Connection();
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="goods";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<div id="sidr-right-seller">
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>
