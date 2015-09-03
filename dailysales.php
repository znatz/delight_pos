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

$contents = Data_sale::sum_all_with_range_of_date_in_shop($sumColumns, "2015/08/01", "2015/08/31", "00");
// 検索押された
if (isset($search)) {
//    $contents = Data_sale::sum_all_with_date_range_in_shop($sumColumns, $dateFrom, $dateTo, $searchIn);
    if (count($_POST["searchIn"]) < 1) {
        $errorMessage = "店舗を選択してください。";
    } else {
        $searchInShops = implode(",", $_POST["searchIn"]);
        $contents = Data_sale::sum_all_with_range_of_date_in_shop($sumColumns, $dateFrom, $dateTo, $searchInShops);
    }
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
                      style="margin: 0 auto; margin-bottom:50px; width: 950px; height: <? echo (130 + 10 * count($shops)) . 'px'; ?>;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>日別売上</legend>
                    </fieldset>

                    <p class="list">
                        <label class="list">集計期間</label>
                        <input
                            id="from"
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="dateFrom"
                            value="<?php
                            echo ifNotEmpty($dateFrom, "");
                            ?>"/>
                        <label class="list yen-mark" style="width:27px;">-</label>
                        <input
                            id="to"
                            style="width: 200px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="dateTo"
                            value="<?php
                            echo ifNotEmpty($dateTo, "");
                            ?>"/>
                        <label class="list" style="width:80px;">来店店舗</label>
                        <select
                            multiple="multiple"
                            style="width:241px;height:<? echo (10 * count($shops)) . 'px'; ?>"
                            tabindex="3"
                            name="searchIn[]">
                            <? foreach ($shops as $s) : ?>
                                <option
                                    value="<? echo $s[0] ?>"
                                    <? if ($searchIn == $s[0]) echo "selected"; ?>
                                    ><? echo $s[0] . " " . $s[1]; ?></option>
                            <? endforeach; ?>
                        </select>
                    </p>

                    <p style="float: left; text-align: center; width: 300px;"
                       id="buttonlist">

                    </p>

                    <div
                        style="float: right; width: -100%; height: 100px; margin: 0px 0 0 0; text-align: center; vertical-align: middle;">
                        <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="search"
                            size="10" value="集計"/>
                        <a
                            tabindex="8"
                            class="center_button hvr-fade" href="./dailysales.php"
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
        <div id="user_list" style="overflow:auto !important;width:1000px;">
            <?
            echo '<table style="border:0;padding:0;border-radius:5px;width:1500px" class="search_table"><thead>';
            $table_header = ['日付' => 'chrDate',
                '店舗' => '_chrShop_ID',
                '売上数' => 'intCount',
                '売上金額' => 'intAmount',
                '粗利金額' => 'intProfit',
                '消費税' => 'intTax',
                '税込金額' => '_PaymentWithTax',
                '客数' => 'intPaymentCount',
                '現金' => '_Cash',
                'クレジット' => 'intCredit',
                '商品券' => 'intExchangeCheck',
                'サービス券' => 'intServiceTicket',
                '売掛金' => 'intReceivable',
                '掛入金' => 'intPartialPayment'];
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


                $isTotal    = (is_null($element->chrDate) && is_null($element->chrShopID));
                $isSubTotal = (!is_null($element->chrDate) && is_null($element->chrShop_ID));
                $missingShop_ID = is_null($element->chrShop_ID);
                if(!$missingShop_ID) {
                    $shop  = Shop::find($element->chrShop_ID);
                }

                // Normal Line
                echo '<tr>';
                foreach ($table_header as $dummy => $val) {
                    if (isset($element->$val)) {
                        if ($val != "chrDate") {
                            if ($isSubTotal && !$isTotal) {
                                echo '<td class="summing_cell currency_cell">' . $element->$val . '</td>';
                            } elseif ($isTotal) {
                                echo '<td class="summing_cell total_summing currency_cell">' . $element->$val . '</td>';
                            } else {
                                echo '<td class="currency_cell">' . $element->$val . '</td>';
                            }
                        } elseif ($val == "chrDate") {
                            if ($isSubTotal && !$isTotal) {
                                echo '<td colspan="2" class="summing_cell_head">小計</td>';
                            } elseif ($isTotal) {
                                echo '<td colspan="2" class="summing_cell_head total_summing">総計</td>';
                            } else {
                                echo '<td><span class="date_cell">' . $element->$val . '</span></td>';
                            }

                        }
                    } else {
                        if ($val=="chrDate" && $isTotal) echo '<td colspan="2" class="summing_cell_head total_summing">総計</td>';

                        if ($val=="_chrShop_ID" && !$isSubTotal & !$isTotal) {
                            if(is_null($shop) && !$missingShop_ID)
                                echo '<td>店舗' . $element->chrShop_ID . '存在しない</td>';
                            else
                                echo '<td>' . $shop->chrID . ' . ' . $shop->chrName. '</td>';
                        }

                        if ($val == "_PaymentWithTax") {
                            if($isTotal)
                                echo '<td class="summing_cell currency_cell total_summing">' . ($element->intAmount + $element->intTax) . '</td>';
                            if($isSubTotal)
                                echo '<td class="summing_cell currency_cell">' . ($element->intAmount + $element->intTax) . '</td>';
                            if(!$isSubTotal && !$isTotal)
                                echo '<td class="currency_cell">' . ($element->intAmount + $element->intTax) . '</td>';
                        }

                        if ($val == "_Cash") {
                            if($isTotal)
                                echo '<td class="summing_cell currency_cell total_summing">' . ($element->intRevenue + $element->intRemain) . '</td>';
                            if($isSubTotal)
                                echo '<td class="summing_cell currency_cell">' . ($element->intRevenue + $element->intRemain) . '</td>';
                            if(!$isSubTotal && !$isTotal)
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
