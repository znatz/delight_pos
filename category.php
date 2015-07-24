<?php
require_once 'helper.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetCategory = new Category();

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全担当データを一回取り出す
$contents = Category::get_all();

// 新規ボタンの処理
if (isset($_POST['newID'])) {
    $targetCategory = Category::get_new_category();
    unset($_POST['newID']);
}

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {
    $targetCategory = Category::find($_POST["targetID"]);
    unset($_POST["target"]);
    $contents = Category::get_all();
}

// 削除ボタン処理
    if (isset($_POST['delete'])) {
        if (Category::delete($_POST['delete'])) {
            $contents = Category::get_all_category();
            $successMessage = "削除しました。";
        } else {
            $errorMessage = "削除失敗しました。";
        }
    }

// 　登録処理
if (isset($_POST["submit"])) {
    if (Category::insert_values([$_POST['chrID'],
        $_POST['chrName']])
    ) {
        $successMessage = "追加しました。";
    } else {
            if (Category::update_to_column('chrName',$_POST['chrName'],'chrID',$_POST['chrID']))
            {
                $successMessage = "更新しました。";
            };
    }

    // 再度リストを更新
    $contents = Category::get_all();
    $_POST["targetID"] = $chrID;
}

$contents = Category::get_all();
?>

<!DOCTYPE html>
<head>
    <?php include('./html_parts/css_and_js.html'); ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            $("tr").dblclick(function(){
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
                    {"bSortable": false, "aTargets": [2, 3]}
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

            $("#jMenu").jMenu({
                ulWidth: 'auto',
                effects: {
                    effectSpeedOpen: 300,
                    effectTypeClose: 'slide'
                },
                animatedText: false,
                openClick: true
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

            <!-- ********************* マスタの作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form" style="margin: 0 auto; width: 700px; height: 220px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>大分類マスタ</legend>
                    </fieldset>
                    <a class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu" href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    <p class="list" style="margin:50px 0 0 0;">
                        <label class="list">コード</label>
                        <input
                            class="chrID validate[custom[integer],custom[required_2_digits] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 290px; margin: 0 0 0 18px;" name="chrID"
                            type="text" size="10" placeholder="00"
                            value='<?php
                            echo $targetCategory->chrID;
                            ?>'/>
                        <input class="newID hvr-fade"
                                         style="width: 100px; height: 37px; margin: 0;" type="submit"
                                         name="newID" id="newID" size="10" value="新規"/>
                    </p>

                    <p class="list">
                        <label class="list">大分類名</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?php
                            echo $targetCategory->chrName;
                            ?>"/>
                    </p>

                    <p style="float: left; text-align: center; width: 300px;margin-top: 80px;"
                       id="buttonlist">
                        <input class="center_button hvr-fade" type="submit" name="submit"
                               size="10" value="登録"/>
                        <a class="center_button hvr-fade" href="./category.php" style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                        <a class="center_button hvr-fade" href="./index.php" style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
                    </p>

                    <div
                        style="float: right; width: -100%; height: 100px; margin: 80px 0 0 0; text-align: center; vertical-align: middle;">

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
        <div id="user_list">
            <form method="post" id="list" action="">
                <?php
                $header = [
                    "コード" => 80,
                    "大分類名" => 500,
                    "選択" => 50,
                    "削除" => 70
                ];

                $prop = [
                    'chrID'         =>'center',
                    'chrName'   =>'center',
                ];

                get_list($header, $contents, "chrID", $prop, "800px") ;

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
    <?php
    $connection = new Connection();
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="category";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>
