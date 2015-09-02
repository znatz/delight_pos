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
$groups     = Group::get_distinct_group_chrID();
$shops      = Shop::get_distinct_shop_chrID();;

$sumColumns = [ "intCount",
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

$contents_array = Data_sale::sum_all_with_month_range_in_shop($sumColumns,"2015","08", "2015", "09", "00");
// 検索押された
if(isset($search)){
    $contents_array = [];
    if(count($_POST["searchIn"]) < 1) {
        $errorMessage = "店舗を選択してください。";
    } else {
        $searchInShops = implode(",", $_POST["searchIn"]);
        $contents_array = Data_sale::sum_all_with_month_range_in_shop($sumColumns, $dateFromYear, $dateFromMonth, $dateToYear, $dateToMonth, $searchInShops);
    }
}

?>

<!DOCTYPE html>
<head>
    <? include('./html_parts/css_and_js.html'); ?>

    <script src="./js/autoNumeric.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {

            $('.date_cell').each(function() {
                var $this = $(this);
                if ($.trim($this.closest('tr').prev('tr').find('.date_cell').text()) == $this.text()) {
                    $this.hide();
                }
            });

            var decimalLength = function(){
                /* your code here */
                var value = 0; /* example only */
                return value;
            };

            $('.currency_cell').autoNumeric("init",{
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
            $("#year").datepicker({
                changeYear:true,
                changeMonth:true,
                dateFormat:'yy',
                onClose: function(dateText, inst) {
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            });

            $("#year2").datepicker({
                changeYear:true,
                changeMonth:true,
                dateFormat:'yy',
                onClose: function(dateText, inst) {
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            });

            $("#month").datepicker({
                changeYear:true,
                changeMonth:true,
                dateFormat:'mm',
                onClose: function(dateText, inst) {
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            });

            $("#month2").datepicker({
                changeYear:true,
                changeMonth:true,
                dateFormat:'mm',
                onClose: function(dateText, inst) {
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            });

        });
    </script>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
    <style type="text/css">
        p.list {
            width: 900px;
        }
        select {
            float:left;
            width:289px;
            margin-bottom: 0;
        }
        label.list {
            width: 100px;
        }
        .yen-mark {
            background-color: gray;
            background-image: linear-gradient(transparent 70%, rgba(100,100,100,.5) 50%);
            background-size: 2px 2px;
            margin:0 !important; padding-top:10px !important;
            display:block;
            height:27px !important;
            color:white;
            text-align:center !important;
        }
        .summing_cell_head {
            text-align:center
        }
        .summing_cell_head, .summing_cell {
            background-color: #9acfea;
            display:<? if($_POST["searchIn"] && count($_POST["searchIn"])==1 && ($searchInShops!= "00")) echo 'none'; ?>
        }
        .currency_cell {
            text-align:right
        }
        .date_cell {
            text-align:center
        }

        .ui-datepicker-calendar {
            display:none;
        }
        .date_input {
            text-align:right;
            padding-right:5px !important;
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

                <form class="search_form" style="margin: 0 auto; margin-bottom:50px; width: 950px; height: <? echo (130+10*count($shops)).'px'; ?>;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>月別売上</legend>
                    </fieldset>

                    <p class="list">
                        <label class="list">集計期間</label>
                        <input
                            id="year"
                            style="width: 80px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input date_input"
                            data-prompt-position="topLeft:140" name="dateFromYear"
                            value="<?php
                            echo ifNotEmpty($dateFromYear,"");
                            ?>"/>
                        <label class="list yen-mark" style="width:27px;">年</label>
                        <input
                            id="month"
                            style="width: 50px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input date_input"
                            data-prompt-position="topLeft:140" name="dateFromMonth"
                            value="<?php
                            echo ifNotEmpty($dateFromMonth,"");
                            ?>"/>
                        <label class="list yen-mark" style="width:27px;">月-</label>
                        <input
                            id="year2"
                            style="width: 80px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input date_input"
                            data-prompt-position="topLeft:140" name="dateToYear"
                            value="<?php
                            echo ifNotEmpty($dateToYear,"");
                            ?>"/>
                        <label class="list yen-mark" style="width:27px;">年</label>
                        <input
                            id="month2"
                            style="width: 50px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input date_input"
                            data-prompt-position="topLeft:140" name="dateToMonth"
                            value="<?php
                            echo ifNotEmpty($dateToMonth,"");
                            ?>"/>
                        <label class="list yen-mark" style="width:27px;">月</label>
                        <label class="list" style="width:80px;">来店店舗</label>
                        <select
                            multiple="multiple"
                            style="width:241px;height:<? echo (10* count($shops)).'px'; ?>"
                            tabindex="3"
                            name="searchIn[]" >
                            <? foreach ($shops as $s) : ?>
                                <option
                                    value="<? echo $s[0] ?>"
                                    <? if($searchIn == $s[0]) echo "selected"; ?>
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
            $table_header = [   '日付'          => 'chrDate',
                '店舗'          => '_chrShop_ID',
                '売上数'        => 'intCount',
                '売上金額'      => 'intAmount',
                '粗利金額'      => 'intProfit',
                '消費税'        => 'intTax',
                '税込金額'      => '_PaymentWithTax',
                '客数'          => 'intPaymentCount',
                '現金'          => '_Cash',
                'クレジット'    => 'intCredit',
                '商品券'        => 'intExchangeCheck',
                'サービス券'    => 'intServiceTicket',
                '売掛金'        => 'intReceivable',
                '掛入金'        => 'intPartialPayment'];
            foreach($table_header as $td => $dummy) {
                echo '<th>'.$td.'</th>';
            }
            echo '</thead>';

            foreach ($contents_array as $contents) {

            // Show Warning and Hide Result If No Records Exists
            if(count($contents) < 1) {
                echo '<tbody><tr><td colspan="10"><div class="isa_error"><i class="fa fa-times-circle">この期間内データーありません。</i></div></td></tr></tbody>';
                echo '<tbody hidden="true">';
            } else {
                echo '<tbody>';
            }

            // Scan Vertically to Calculate Data
            $runningLine = new Data_sale();
            $runningLine->chrDate = $contents[0]->chrDate;

            foreach((array)$contents as $element) {

                // Summing Line
                if($element->chrDate != $runningLine->chrDate) {
                    foreach ($table_header as $dummy => $val) {
                        if(isset($runningLine->$val)) {
                            // Do Not Show Date in Summing Line
                            if($val != "chrDate") {
                                echo '<td class="summing_cell currency_cell">' . $runningLine->$val . '</td>';
                            } else {
                                echo "<td colspan='2' class='summing_cell_head'>小計</td>";
                            }
                        } else {
                            // Calculate Total Price With Tax
                            if($val == "_PaymentWithTax") echo '<td class="summing_cell currency_cell">'.($runningLine->intAmount + $runningLine->intTax).'</td>';
                            // Calculate Cash
                            if($val == "_Cash") echo '<td class="summing_cell currency_cell">'.($runningLine->intRevenue + $runningLine->intRemain).'</td>';
                        }
                    }
                    // Reset Summing Line Once Summing Finished.
                    $runningLine = new Data_sale();
                    $runningLine->chrDate = $element->chrDate;
                }

                // Normal Line
                echo '<tr>';
                foreach ($table_header as $dummy => $val) {
                    // Sum Revenue and Remain Which are Not Shown
                    $runningLine->intRevenue += $element->intRevenue;
                    $runningLine->intRemain  += $element->intRemain;
                    if(isset($element->$val)) {
                        // 1 : Sum Columns Which are Not Date
                        // 2 : Show Date and Currency Cells in Separate Format
                        if($val != "chrDate") {
                            $runningLine->$val += $element->$val;
                            echo '<td class="currency_cell">'.$element->$val . '</td>';
                        } elseif ($val == "chrDate") {
                            echo '<td><span class="date_cell">'.$element->$val.'</span></td>';
                        }
                    } else {
                        // Show Shop Name, Not Summed
                        if($val == "_chrShop_ID" && !is_null(Shop::find($element->chrShop_ID)))  {
                            echo '<td>'.$element->chrShop_ID.' . '.Shop::find($element->chrShop_ID)->chrName.'</td>';
                        } elseif($val == "_chrShop_ID" && is_null(Shop::find($element->chrShop_ID))) {
                            echo '<td>店舗'.$element->chrShop_ID.'存在しない</td>';
                        }
                        // Calculate Total Price With Tax, Not Summed
                        if($val == "_PaymentWithTax") echo '<td class="currency_cell">'.($element->intAmount + $element->intTax).'</td>';
                        // Calculate Cash, Not Summed
                        if($val == "_Cash") echo '<td class="currency_cell">'.($element->intRevenue + $element->intRemain).'</td>';
                    }
                }

                echo '</tr>';

            }

            { // Last Summing Line, Exactly the Same as Previous
                foreach ($table_header as $dummy => $val) {
                    if(isset($runningLine->$val)) {
                        if($val != "chrDate") {
                            echo '<td class="summing_cell currency_cell">' . $runningLine->$val . '</td>';
                        } else {
                            echo "<td colspan='2' class='summing_cell_head'>小計</td>";
                        }
                    } else {
                        // Calculate Total Price With Tax
                        if($val == "_PaymentWithTax") echo '<td class="summing_cell currency_cell">'.($runningLine->intAmount + $runningLine->intTax).'</td>';
                        // Calculate Cash
                        if($val == "_Cash") echo '<td class="summing_cell currency_cell">'.($runningLine->intRevenue + $runningLine->intRemain).'</td>';
                    }}
                $runningLine = new Data_sale();
                $runningLine->chrDate = $element->chrDate;

            } // End of Last Summing Line
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
