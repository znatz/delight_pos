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

$sumColumns = ["intCount",
    "intAmount",
    "intProfit",
    "intTax",
    "intPaymentCount",
    "intCredit",
    "intRevenue",
    "intRemain",
    "intExchangeCheck",
    "intServiceTicket",
    "intReceivable",
    "intPartialPayment"];

$today = new DateTime();
$today = $today->format("Y/m/d");
$tomorrow = new DateTime('tomorrow');
$tomorrow = $tomorrow->format("Y/m/d");
$thisYear = new DateTime('m');
$thisYear = $thisYear->format("Y");

$thisMonth = new DateTime('m');
$thisMonth = $thisMonth->format("m");

$nextMonth = new DateTime('next month');
$nextMonth = $nextMonth->format("m");
//$thisMonth = Data_sale::findMonth($today->format('Y'), $today->format('m'));
echo $today . "---" . $tomorrow . "----" . $thisMonth . "----" . $nextMonth;

$contentsThisMonth = Data_sale::sum_all_with_range_of_month_in_shop($thisYear, $thisMonth, $thisYear, $nextMonth, "00");

$contentsToday = Data_sale::sum_all_with_range_of_date_in_shop($sumColumns, $today, $today, "00");

$contents = Salesum::fillSalesum($contentsThisMonth, $contentsToday);
?>

<!DOCTYPE html>
<head>
    <? include('./html_parts/css_and_js.html'); ?>

    <script src="./js/autoNumeric.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {

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
            $("#to").datepicker({dateFormat: dateFormat});


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
            display: table-cell !important;
        }

        .currency_cell {
            text-align: right
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
                      style="margin: 0 auto; margin-bottom:50px; width: 950px; height: 80px;"
                      method="post" id="user_add_form" action="">
                    <label class="yen-mark">売上速報 <? $retrieveTime = new DateTime();
                        echo $retrieveTime->format("Y年m月d日 H:i:s"); ?></label>

                    <div
                        style="float:right; height: 100px; margin: 0 0 0 0; text-align: center; vertical-align: middle;">
                        <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="search"
                            style="margin-top:0;margin-bottom: 0;"
                            size="10" value="更新"/>
                    </div>
                </form>
            </div>
            <form action=""></form>
        </div>
        <!-- ********************* マスタの作成 終了　**********************	-->

        <!-- ********************* リストの作成 開始　**********************	-->
        <div id="user_list" style="overflow:auto !important;width:1000px;">
            <?
            echo '<table style="border:0;padding:0;border-radius:5px;width:800px" class="search_table"><thead>';
            $table_header = [
                '店舗' => 'chrShop_ID',
                '日計客数' => 'intPaymentCountDaily',
                '日計売上数' => 'intCountDaily',
                '日計売上金額' => 'intAmountDaily',
                '月計客数' => 'intPaymentCountMonthly',
                '月計売上数' => 'intCountMonthly',
                '月計売上金額' => 'intAmountMonthly'
            ];
            foreach ($table_header as $td => $dummy) {
                echo '<th>' . $td . '</th>';
            }
            echo '</thead>';

            // Show Warning and Hide Result If No Records Exists
            if (count($contents) < 1) {
                echo '<tbody><tr><td colspan="10"><div class="isa_error"><i class="fa fa-times-circle">この期間内データーありません。</i></div></td></tr></tbody>';
                echo '<tbody hidden="true">';
            } else {
                echo '<tbody>';
            }

            foreach ((array)$contents as $element) {

                echo $element->chrShop_ID;

                $isTotal = is_null($element->chrShopID);
                $isSubTotal = is_null($element->chrShop_ID);
                $missingShop_ID = is_null($element->chrShop_ID);
                if (!$missingShop_ID) {
                    $shop = Shop::find($element->chrShop_ID);
                }

                // Normal Line
                echo '<tr>';
                foreach ($table_header as $dummy => $val) {
                    if (isset($element->$val)) {
                        if ($val != "chrDate") {
                            if ($isSubTotal && !$isTotal) {
                                echo '<td class="summing_cell currency_cell">' . $element->$val . '</td>';
                            } elseif ($isTotal && $val != "chrShop_ID") {
                                echo '<td class="summing_cell total_summing currency_cell">' . $element->$val . '</td>';
                            } elseif ($val == "chrShop_ID" && !$isSubTotal & !$isTotal) {
                                if (is_null($shop) && !$missingShop_ID)
                                    echo '<td>店舗' . $element->chrShop_ID . '存在しない</td>';
                                else
                                    echo '<td>' . $shop->chrID . ' . ' . $shop->chrName . '</td>';
                            } else {
                                echo '<td class="currency_cell">' . $element->$val . '</td>';
                            }
                        }
                    } else {

                        if ($val == "_PaymentWithTax") {
                            if ($isTotal)
                                echo '<td class="summing_cell currency_cell total_summing">' . ($element->intAmount + $element->intTax) . '</td>';
                            if ($isSubTotal)
                                echo '<td class="summing_cell currency_cell">' . ($element->intAmount + $element->intTax) . '</td>';
                            if (!$isSubTotal && !$isTotal)
                                echo '<td class="currency_cell">' . ($element->intAmount + $element->intTax) . '</td>';
                        }

                        if ($val == "_Cash") {
                            if ($isTotal)
                                echo '<td class="summing_cell currency_cell total_summing">' . ($element->intRevenue + $element->intRemain) . '</td>';
                            if ($isSubTotal)
                                echo '<td class="summing_cell currency_cell">' . ($element->intRevenue + $element->intRemain) . '</td>';
                            if (!$isSubTotal && !$isTotal)
                                echo '<td class="currency_cell">' . ($element->intRevenue + $element->intRemain) . '</td>';
                        }

                    }
                }

                echo '</tr>';

            }

            echo '</tbody></table>'
            ?>
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
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="data_sale";';
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
