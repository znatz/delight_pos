<?php
require_once 'helper.php';

session_start();
session_check();

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

extract($_POST);

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全商品データを一回取り出す
//$contents = Data_sale::get_all_with_date_range("2015/08/01", "2015/08/31", "");
$groups = Group::get_distinct_group_chrID();
$shops = Shop::get_distinct_shop_chrID();;
$sellers = Seller::get_distinct_column('chrID');
$makers  = Maker::get_distinct_column('chrID');

$data_stock = Data_stock::get_all_with_limits(10);
foreach($data_stock as $d) {
    $barcodes[] = $d->chrStockCode;
}
$contents = Stocklist::buildWholeStockList($barcodes);
// 検索押された
if (isset($search) || isset($change_page)) {
//    $contents = Data_sale::sum_all_with_date_range_in_shop($sumColumns, $dateFrom, $dateTo, $searchIn);
        $contents = Stocklist::buildWholeStockList_by_Conditions($dateFrom, $searchInShop, $searchInGroup, $searchInSeller, $searchInMaker, 10*($page_down-$page_up), 10);
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

        .summing_cell_head {
            text-align: center
        }

        .summing_cell_head, .summing_cell {
            background-color: #9acfea;
            display: <? if($_POST["searchIn"] && count($_POST["searchIn"])==1 && ($searchInShops!= "00")) echo 'none'; ?>
        }

        .total_summing {
            display:table-cell !important;
        }

        .currency_cell {
            text-align: right
        }

        .date_cell {
            text-align: center
        }
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
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="dateFrom"
                            value="<?php
                            echo ifNotEmpty($dateFrom, "");
                            ?>"/>
                        <label class="list" style="width:80px;">店舗</label>
                        <select
                            style="width:300px;"
                            tabindex="3"
                            name="searchInShop">
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
                            <option value="byGroup">部門別</option>
                            <option value="bySeller">仕入先別</option>
                            <option value="byMaker">メーカー別</option>
                        </select>
                    </p>


                    <div
                        style="float: right; width: -100%; height: 100px; margin: 0px 0 0 0; text-align: center; vertical-align: middle;">
                        <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="search"
                            size="10" value="検索"/>
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
            <form action=""></form>
        </div>
        <!-- ********************* マスタの作成 終了　**********************	-->

        <!-- ********************* リストの作成 開始　**********************	-->
        <p style="float: left; text-align: center; width: 300px;" id="buttonlist">
            <div style=" height: 100px; margin: 0px 0 0 0; text-align: center; vertical-align: middle;">
            <form method="post">
                <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="change_page"
                            style="width:200px;"
                            size="10" value="前のページ"/>
                        <input type="text" name="page_up" hidden="true" value="<? echo $_POST["page_up"] + 1; ?>"/>
                        <input type="text" name="page_down" hidden="true" value="<? echo $_POST["page_down"] ; ?>"/>
             </form>
            <form method="post">
                        <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="change_page"
                            style="width:200px;"
                            size="10" value="次のページ"/>
                        <input type="text" name="page_up" hidden="true" value="<? echo $_POST["page_up"] ; ?>"/>
                        <input type="text" name="page_down" hidden="true" value="<? echo $_POST["page_down"] + 1; ?>"/>
             </form>
            </div>
        </p>

        <div id="user_list" style="overflow:auto !important;width:1000px; height:600px;">


            <form method="post" id="list" action="" style="overflow: auto;">

                <?
                 $header = [
                     "コード"          => 50,
                     "品番"            => 50,
                     "品名"            => 280,
                     "部門"            => 150,
                     "仕入先"          => 150,
                     "メーカー"        => 150,
                     "カラー"          => 150,
                     "サイズ"          => 150,
                     "原価"            => 150,
                     "売価"            => 150,
                     "在庫数"          => 150,
                     "原価金額"        => 150,
                     "売価金額"        => 150,
                ];

                $prop = [
                    "chrID"         =>'center'            ,
                    "chrCode"       =>'center'            ,
                    "chrName"       =>'center'            ,
                    "chrGroup"      =>'center'            ,
                    "chrSeller"     =>'center'            ,
                    "chrMaker"      =>'center'            ,
                    "chrColor"      =>'center'            ,
                    "chrSize"       =>'center'            ,
                    "intCost"       =>'center'            ,
                    "intPrice"      =>'center'            ,
                    "intStockCount" =>'center'            ,
                    "intTotalCost"  =>'center'            ,
                    "intTotalPrice" =>'center'

                ];
                get_list_without_buttons($header, $contents, "chrCode", $prop, "1500px") ;

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
