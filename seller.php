<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/seller_class.php';
require_once './mapping/staff_class.php';
require_once './mapping/category_class.php';
require_once './utils/html_parts_generator.php';
require_once './utils/helper.php';
require_once './mapping/menu_class.php';


session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetSeller = Seller::get_one_empty_seller();;

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// $_POSTデーターを変数化
extract($_POST);


// 　登録処理
if (isset($submit)) {
    if (Seller::insert_one_seller($chrID, $chrName, $chrShort_Name, $chrPos, $chrAddress, $chrAddress_No, $chrTel, $chrFax, $chrStaff) )
    {
        $successMessage = "追加しました。";
    } else {
        if (Seller::update_one_seller($chrID, $chrName, $chrShort_Name, $chrPos, $chrAddress, $chrAddress_No, $chrTel, $chrFax, $chrStaff))
        {
            $successMessage = "更新しました。";
        };
    }

    // 再度リストを更新
    $contents = Seller::get_all_seller();
    $_POST["targetID"] = $chrID;
}

// chrID取得の為データを一回取り出す
$contents = Seller::get_all_seller();
$targetSeller = Seller::get_all_seller();
$chrID = $targetSeller->chrID;

//検索ボタンの処理
$Address = issetThen($_POST['SearchPost'], showCode($_POST['chrPos']));

// リスト内ラジオボタンの処理
if (isset($targetID)) {

    $targetSeller = Seller::get_one_seller($_POST["targetID"]);

    // 選択を解除
    unset($_POST["target"]);
    $contents = Seller::get_all_seller();

}

// 削除ボタン処理
if (isset($delete)) {
    if (Seller::delete_one_seller($delete)) {
        $contents = Seller::get_all_seller();
        $successMessage = "削除しました。";
    } else {
        $errorMessage = "削除失敗しました。";
    }
}


// 新規ボタンの処理
if (isset($newID)) {
    $targetSeller = Seller::get_new_seller();
    $_POST['chrID'] = $targetSeller->chrID;
    unset($_POST['newID']);
}
?>

<!DOCTYPE html>
<head>
    <? include('./html_parts/css_and_js.html'); ?>
    <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
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
            })

            $("#newID").click(function () {
                $('#user_add_form').validationEngine('hideAll');
                $('#user_add_form').validationEngine('detach');
                return true;
            });
            $('#right-menu').sidr({
                name: 'sidr-right',
                side: 'right'
            });

            /* Search Post Code */
            $("#SearchPost").click(function () {
                $('#user_add_form').validationEngine('hideAll');
                $('#user_add_form').validationEngine('detach');
                return true;
            });


        });
    </script>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
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

            <!-- ********************* マスタの作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form" style="margin: 0 auto; width: 700px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>仕入先マスタ</legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">コード</label>
                        <input
                            tabindex="1"
                            class="chrID validate[custom[integer],custom[required_3_digits]] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 290px; margin: 0 0 0 18px;" name="chrID"
                            type="text" size="10" placeholder="000"
                            value='<? echo ifNotEmpty($_POST['chrID'], $targetSeller->chrID); ?>'/>
                        <input
                            tabindex="2"
                            class="newID hvr-fade" style="width: 100px; height: 37px; margin: 0;" type="submit"
                            name="newID" id="newID" size="10" value="新規"/>
                        <a
                            tabindex="12"
                            class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu"
                            href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    </p>

                    <p class="list">
                        <label class="list">仕入先名</label>
                        <input
                            tabindex="3"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[30]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<? echo ifNotEmpty($_POST['chrName'], $targetSeller->chrName); ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">カナ</label>
                        <input
                            tabindex="4"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[katagana,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrShort_Name"
                            value="<? echo ifNotEmpty($_POST['chrShort_Name'], $targetSeller->chrShort_Name); ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">郵便番号</label>
                        <input
                            tabindex="5"
                            style="width: 100px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[onlyLetterNumber,maxSize[8]] text-input"
                            data-prompt-position="topLeft:140" name="chrPos"
                            value="<? echo ifNotEmpty($_POST['chrPos'], $targetSeller->chrPos); ?>"
                            >
                        <input class="newID hvr-fade"
                               style="width: 100px; height: 37px; margin: 0;" type="submit"
                               name="SearchPost" id="SearchPost" size="10" value="検索"/>
                    </p>

                    <p class="list">
                        <label class="list">住所</label>
                        <input
                            tabindex="6"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[50]] text-input Address1"
                            data-prompt-position="topLeft:140" name="chrAddress"
                            value="<? echo ifNotEmpty($Address, $targetSeller->chrAddress); ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">番地</label>
                        <input
                            tabindex="7"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrAddress_No"
                            value="<? echo ifNotEmpty($_POST['chrAddress_No'], $targetSeller->chrAddress_No); ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">電話番号</label>
                        <input
                            tabindex="8"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[phone,maxSize[13]] text-input"
                            data-prompt-position="topLeft:140" name="chrTel"
                            value="<? echo ifNotEmpty($_POST['chrTel'], $targetSeller->chrTel); ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">FAX番号</label>
                        <input
                            tabindex="10"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[phone,maxSize[13]] text-input"
                            data-prompt-position="topLeft:140" name="chrFax"
                            value="<? echo ifNotEmpty($_POST['chrFax'], $targetSeller->chrFax); ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">担当者名</label>
                        <input
                            tabindex="11"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[10]] text-input"
                            data-prompt-position="topLeft:140" name="chrStaff"
                            value="<? echo ifNotEmpty($_POST['chrStaff'], $targetSeller->chrStaff); ?>"/>
                    </p>

                    <p style="float: left; text-align: center; width: 300px;"
                       id="buttonlist">
                        <input class="center_button hvr-fade" type="submit" name="submit"
                               size="10" value="登録"/>
                        <a class="center_button hvr-fade" href="./seller.php"
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
                    <pre id="output"></pre>
                </form>
            </div>
            <form action=""></form>
        </div>
        <!-- ********************* マスタの作成 終了　**********************	-->


        <!-- ********************* リストの作成 開始　**********************	-->
        <div id="user_list" style="overflow:auto !important;margin:0 0 0 70px !important;width:1000px !important;">
            <form method="post" id="list" action="">
                <?
                $header = [
                    "コード" => 80,
                    "仕入先名" => 300,
                    "カナ" => 150,
                    "郵便番号" => 80,
                    "住所" => 150,
                    "番地" => 150,
                    "電話番号" => 120,
                    "FAX番号" => 120,
                    "担当者名" => 120,
                    "選択" => 50,
                    "削除" => 70
                ];

                $prop = [
                    'chrID' => 'center',
                    'chrName' => 'left',
                    'chrShort_Name' => "center",
                    'chrPos' => 'center',
                    'chrAddress' => 'center',
                    'chrAddress_No' => 'center',
                    'chrTel' => 'center',
                    'chrFax' => 'center',
                    'chrStaff' => 'center',
                ];
                get_list($header, $contents, "chrID", $prop, "1500px");

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
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="seller";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>
