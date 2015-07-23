<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/staff_class.php';
require_once './utils/html_parts_generator.php';
require_once './utils/helper.php';
require_once './mapping/menu_class.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetStaff = new Staff("", "", "", "");

extract($_POST);

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全担当データを一回取り出す
$contents = Staff::get_all_staff();

// 新規ボタンの処理
if (isset($newID)) {
    $chrID = get_lastet_number(Staff::get_all_staff_chrID());
    $targetStaff = new Staff($chrID, "", "", "", "", "");
    unset($_POST['newID']);
}

// リスト内ラジオボタンの処理
if (isset($targetID)) {
    $targetStaff = Staff::get_one_staff($targetID);
    // 選択を解除
    unset($_POST["target"]);
}

// 削除ボタン処理
if (isset($delete)) {
    if (Staff::deletete_one_staff($delete)) {
        $contents = Staff::get_all_staff();
        $successMessage = "削除しました。";
    } else {
        $errorMessage = "削除失敗しました。";
    }
}

// 　登録処理
if (isset($_POST["submit"])) {
    // マスタからデータを取り出す
    $pass = password_hash($password, PASSWORD_DEFAULT);

    // データベースに挿入
    if (Staff::insert_one_staff($chrID, $chrName, $chrLogin_ID, $auth, $pass)) {
        $successMessage = "追加しました。";
    } else {
        if (mysql_errno() == 1062) {
            if (Staff::update_one_staff($chrID, $chrName, $chrLogin_ID, $auth, $pass)) {
                $successMessage = "更新しました。";
            };
        }
    }

    // 再度リストを更新
    $contents = Staff::get_all_staff();
    $_POST["targetID"] = $chrID;

}

?>

<!DOCTYPE html>
<head>
    <? include('./html_parts/css_and_js.html'); ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {

            $("tr").dblclick(function () {
                var chrID = $(this).attr("id");
                console.log(chrID);
                $("input[name=targetID][value=" + chrID + "]").attr('checked', 'checked');
                $("#list").submit();
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

            $("#user_password").focus(function () {
                this.type = "text";
            }).blur(function () {
                this.type = "password";
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
                    {"bSortable": false, "aTargets": [4, 5]}
                ]
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
                        <legend>担当マスタ</legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">担当コード</label>
                        <input
                            tabindex="1"
                            class="chrID validate[custom[integer],custom[required_2_digits] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 290px; margin: 0 0 0 18px;" name="chrID"
                            type="text" size="10" placeholder="00"
                            value='<?
                            echo $targetStaff->chrID;
                            ?>'/>
                        <input
                            tabindex="2"
                            class="newID hvr-fade" style="width: 100px; height: 37px; margin: 0;" type="submit"
                            name="newID" id="newID" size="10" value="新規"/>
                        <a
                            tabindex="7"
                            class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu"
                           href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    </p>

                    <p class="list">
                        <label class="list">担当者名</label>
                        <input
                            tabindex="3"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?
                            echo $targetStaff->chrName;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">ログインID</label>
                        <input
                            tabindex="4"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="loginid validate[required,custom[onlyLetterNumber],maxSize[6]] text-input"
                            data-prompt-position="topLeft:140" name="chrLogin_ID"
                            value="<?
                            echo $targetStaff->chrLogin_ID;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">パスワード</label>
                        <input
                            tabindex="5"
                            style="width: 390px; margin: 0 0 0 18px;" type="password"
                            size="10" id="user_password"
                            class="password validate[required,onlyNumberSp,maxSize[6]]"
                            data-prompt-position="topLeft:140" name="password"/>
                    </p>

                    <p class="list">
                        <label class="list">権限設定</label>
                        <select
                            tabindex="6 " name="auth"
                            class="validate[required]">
                            <option/>
                            <option value="1"
                                <? if ($targetStaff->intAuthority_ID == 1) echo "selected"; ?>>1.一般
                            </option>
                            <option value="2"
                                <? if ($targetStaff->intAuthority_ID == 2) echo "selected"; ?>>2.マネジャー
                            </option>
                            <option value="9"
                                <? if ($targetStaff->intAuthority_ID == 9) echo "selected"; ?>>9.管理者
                            </option>
                        </select>
                        <label
                            style="display: block; float: left;  height: 40px; width: 300px; text-align: center; font-size: 14px; background-color: #eeeeee; line-height: 40px; letter-spacing:5px;">1:一般
                            2:マネジャー 9：管理者</label>
                    </p>
                    <? include('./html_parts/form_button.html'); ?>
              </form>
            </div>
        </div>
        <!-- ********************* マスタの作成 終了　**********************	-->


        <!-- ********************* リストの作成 開始　**********************	-->
        <div id="user_list" style="width:700px;margin:0 auto; text-align:center;">
            <?
                $header = [
                    "コード" => 80,
                    "担当" => 200,
                    "ログインID" => 200,
                    "権限" => 50,
                    "選択" => 50,
                    "削除" => 70
                ];
                $prop = [
                    'chrID'=>'center',
                    'chrName'=>'left',
                    'chrLogin_ID'=>"center",
                    'intAuthority_ID'=>'center'
                ];
                get_list($header, $contents, "chrID", $prop, "700px") ;
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
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="staff_add";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>