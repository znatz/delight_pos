<?php
require_once 'helper.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetShop = new Shop;

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

$staff = unserialize($_SESSION["staff"]);

$contents = Shop::get_all();

if (isset($_POST['newID'])) {
    $targetShop = Shop::get_new_shop();
    unset($_POST['newID']);
}

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {
    $targetShop = Shop::find($_POST["targetID"]);
    $post = $targetShop->chrPost;
    $address = $targetShop->chrAddress;

    unset($_POST["target"]);
    $contents = Shop::get_all();
}

// 削除ボタン処理
if (isset($_POST['delete'])) {
    if (Shop::delete($_POST['delete'])) {
        $contents = Shop::get_all();
        $successMessage = "削除しました。";
    } else {
        $errorMessage = "削除失敗しました。";
    }
}

// 　登録処理
if (isset($_POST["submit"])) {
    if (Shop::insert_values(
        [
            $_POST['chrID'],
            $_POST['chrName'],
            $_POST['chrPost'],
            $_POST['chrAddress'],
            $_POST['chrAddressNo'],
            $_POST['chrTel'],
            $_POST['chrFax'],
            $_POST['intDisplayOrder']
        ])
    ) {
        $successMessage = "追加しました。";
    } else {
        // 更新処理開始
        if (Shop::update_to_columns(
            [
                $_POST['chrID'],
                $_POST['chrName'],
                $_POST['chrPost'],
                $_POST['chrAddress'],
                $_POST['chrAddressNo'],
                $_POST['chrTel'],
                $_POST['chrFax'],
                $_POST['intDisplayOrder']
            ])
        ) {
            $successMessage = "更新しました。";
        };
    }

    // 再度リストを更新
    $contents = Shop::get_all();
    $_POST["targetID"] = $chrID;
}

$contents = Shop::get_all();

// 検索ボタンの処理
if (isset($_POST['SearchPost'])) {
    $targetShop = new Shop($_POST['chrID'],
        $_POST['chrName'],
        $_POST['chrPost'],
        $_POST['chrAddress'],
        $_POST['chrAddressNo'],
        $_POST['chrTel'],
        $_POST['chrFax'],
        $_POST['intDisplayOrder']);
    $post = $_POST["chrPost"];
    $connection = new Connection();
    $query = "select * from post Where chrID='" . $post . "';";
    $result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
    $rowCount = mysql_num_rows($result);
    if ($rowCount > 0) {
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $address = $row['chrPrefecture'] . $row['chrAddress'];
    } else {
    }
    $connection->close();
}

?>

<!DOCTYPE html>
<head>
    <?php include('./html_parts/css_and_js.html'); ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            $("tr").dblclick(function () {
                var chrID = $(this).attr("id");
                console.log(chrID);
                $("input[name=targetID][value=" + chrID + "]").attr('checked', 'checked');
                $("#list").submit();
            });


            $('#myTable').DataTable({
                "language": {
                    "sProcessing": "処理中...",
                    "sLengthMenu": "_MENU_ 件表示",
                    "sZeroRecords": "データはありません。",
                    "sInfo": " _TOTAL_ 件中 _START_ から _END_ まで表示",
                    "sInfoEmpty": " 0 件中 0 から 0 まで表示",
                    "sInfoFiltered": "（全 _MAX_ 件より抽出）",
                    "sInfoPostFix": "",
                    "sSearch": "検索:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "先頭",
                        "sPrevious": "前",
                        "sNext": "次",
                        "sLast": "最終"
                    }
                },
                "aoColumnDefs": [
                    {"bSortable": false, "aTargets": [5, 6]}
                ]
            });


            $('#main-menu').smartmenus();
            jQuery("#user_add_form").validationEngine();
            $("#user_add_form").bind("jqv.field.result", function (event, field, errorFound, prompText) {
                console.log(errorFound)
            });

            $("#newID").click(function () {
                $('#user_add_form').validationEngine('hideAll');
                $('#user_add_form').validationEngine('detach');
                return true;
            });
            $('#right-menu').sidr({
                name: 'sidr-right',
                side: 'right'
            });


        });
    </script>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
</head>
<body>
<div class="blended_grid">
    <div class="pageHeader">
        <?php include('./html_parts/header.html'); ?>
    </div>
    <div class="pageContent">
        <?php include('./html_parts/top_menu.html'); ?>
        <div class="main">
            <?php include('./html_parts/warning.html'); ?>

            <!-- ********************* フォームの作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form" style="margin: 0 auto; width: 700px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>店舗マスタ</legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">コード</label>
                        <input
                            tabindex="1"
                            class="validate[custom[integer],custom[required_2_digits],custom[onlyLetterNumber]] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 290px; margin: 0 0 0 18px;" name="chrID"
                            type="text" size="10" placeholder="00"
                            value='<?php
                            echo $targetShop->chrID;
                            ?>'/>
                        <input
                            tabindex="2"
                            class="newID hvr-fade"
                            style="width: 100px; height: 37px; margin: 0;" type="submit"
                            name="newID" id="newID" size="10" value="新規"/>
                        <a
                            tabindex="11"
                            class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu"
                            href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    </p>

                    <p class="list">
                        <label class="list">店舗名</label>
                        <input
                            tabindex="3"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?php
                            echo $targetShop->chrName;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">郵便番号</label>
                        <input
                            tabindex="4"
                            style="width: 290px; margin: 0 0 0 18px;" type="text" size="10" placeholder="000-0000"
                            class="validate[maxSize[8]] text-input"
                            data-prompt-position="topLeft:140" name="chrPost"
                            value="<?php
                            echo $post;
                            ?>"/>
                        <input
                            tabindex="5"
                            class="newID hvr-fade"
                            style="width: 100px; height: 37px; margin: 0;" type="submit"
                            name="SearchPost" id="SearchPost" size="10" value="検索"/>
                    </p>

                    <p class="list">
                        <label class="list">住所</label>
                        <input
                            tabindex="6"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrAddress"
                            value="<?php
                            echo $address;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">番地</label>
                        <input
                            tabindex="7"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrAddressNo"
                            value="<?php
                            echo $targetShop->chrAddressNo;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">電話番号</label>
                        <input
                            tabindex="8"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[13]] text-input"
                            data-prompt-position="topLeft:140" name="chrTel"
                            value="<?php
                            echo $targetShop->chrTel;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">FAX番号</label>
                        <input
                            tabindex="9"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[13]] text-input"
                            data-prompt-position="topLeft:140" name="chrFax"
                            value="<?php
                            echo $targetShop->chrFax;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">表示順</label>
                        <input
                            tabindex="10"
                            style="width: 100px; margin: 0 0 0 18px;" type="text"
                            size="10"
                            class="chrCategory_ID validate[onlyNumberSp,maxSize[2]]"
                            data-prompt-position="topLeft:140" name="intDisplayOrder"
                            value="<?php
                            echo $targetShop->intDisplayOrder;
                            ?>"/>
                    </p>

                    <p style="float: left; text-align: center; width: 300px;"
                       id="buttonlist">
                        <input class="center_button hvr-fade" type="submit" name="submit"
                               size="10" value="登録"/>
                        <a class="center_button hvr-fade" href="./shop.php"
                           style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                        <a class="center_button hvr-fade" href="./index.php"
                           style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
                    </p>

                    <div
                        style="float: right; width: -100%; height: 100px; margin: 10px 0; text-align: center; vertical-align: middle;">

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
        <!-- ********************* フォームの作成 終了　**********************	-->


        <!-- ********************* リストの作成 開始　**********************	-->
        <div id="user_list" style="overflow:auto !important; width:1000px !important;">
            <form method="post" id="list" action="">
                <?php
                $header = [
                    "コード" => 80,
                    "店舗名" => 300,
                    "郵便番号" => 80,
                    "住所" => 150,
                    "番地" => 150,
                    "電話番号" => 150,
                    "FAX番号" => 120,
                    "表示順" => 80,
                    "選択" => 50,
                    "削除" => 70
                ];
                $prop = [
                    'chrID' => 'center',
                    'chrName' => 'left',
                    'chrPost' => 'right',
                    'chrAddress' => 'right',
                    'chrAddressNo' => 'right',
                    'chrTel' => 'right',
                    'chrFax' => 'right',
                    'intDisplayOrder' => 'right',
                ];
                get_list($header, $contents, "chrID", $prop, "1200px");

                ?>
                <input type="submit" name="target" style="display: none"/>
            </form>
        </div>
    </div>
    <!-- ********************* リストの作成  終了　********************** -->
    <div class="pageFooter">
        <h4 style="color: #ffffff; text-align: center; padding: 4px 0 0 0;">CopyRight
            2015 POSCO Co.Ltd All Rights Reserved</h4>
    </div>
</div>
</div>
<!-- ********************  入力規則　開始      *********************** -->
<div id="sidr-right">
    <?php
    $connection = new Connection();
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="Shop";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>