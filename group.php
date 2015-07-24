<?php
require_once 'helper.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetGroup = new Group;

if (!isset($_SESSION['staff']))
    header("Location: Login.php");

$staff = unserialize($_SESSION["staff"]);

$contents = Group::get_all();

$cats = Category::get_distinct_category_chrID();

// 新規ボタンの処理
if (isset($_POST['newID'])) {
    $targetGroup = Group::get_new_group();
    unset($_POST['newID']);
}

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {

    $targetGroup = Group::find($_POST["targetID"]);
    unset($_POST["target"]);
    $contents = Group::get_all();
}

// 削除ボタン処理
if (isset($_POST['delete'])) {
    if (Group::delete($_POST['delete'])) {
        $contents = Group::get_all();
        $successMessage = "削除しました。";
    } else {
        $errorMessage = "削除失敗しました。";
    }
}

// 　登録処理
if (isset($_POST["submit"])) {
    if (Group::insert_values(
        [
            $_POST['chrID'],
            $_POST['chrName'],
            $_POST['intCost_Rate'],
            $_POST['chrCategory_ID'],
            $_POST['intTax_Rate']
        ])
    ) {
        $successMessage = "追加しました。";
    } else {
        // 更新処理開始
        if (Group::update_to_columns(
            [
                $_POST['chrID'],
                $_POST['chrName'],
                $_POST['intCost_Rate'],
                $_POST['chrCategory_ID'],
                $_POST['intTax_Rate']
            ])
        ) {
            $successMessage = "更新しました。";
        };
    }

    // 再度リストを更新
    $contents = Group::get_all();
    $_POST["targetID"] = $chrID;
}

$contents = Group::get_all();
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
                    "sLengthMenu": "_MENU_  件表示",
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
    <style type="text/css">

        select {
            border: 1px solid #555555;
            margin: 0 0 0px 18px;
            width: 199px;
            font-size: 14px;
            background: #faffbd;
        }

    </style>
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

                <form class="search_form" style="margin: 0 auto; width: 700px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>部門マスタ</legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">コード</label>
                        <input
                            tabindex="1"
                            class="chrID validate[custom[integer],custom[required_2_digits] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 290px; margin: 0 0 0 18px;" name="chrID"
                            type="text" size="10" placeholder="00"
                            value='<?php
                            echo $targetGroup->chrID;
                            ?>'/>
                        <input class="newID hvr-fade"
                               tabindex="2"
                               style="width: 100px; height: 37px; margin: 0;" type="submit"
                               name="newID" id="newID" size="10" value="新規"/>
                        <a
                            tabindex="12"
                            class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu"
                            href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    </p>

                    <p class="list">
                        <label class="list">部門名</label>
                        <input
                            tabindex="3"
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?php
                            echo $targetGroup->chrName;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">原価率</label>
                        <input
                            tabindex="4"
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="intCost_Rate validate[required,onlyNumberSp,custom[required_1_to_3_digits]] text-input"
                            data-prompt-position="topLeft:140" name="intCost_Rate"
                            value="<?php
                            echo $targetGroup->intCost_Rate;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">大分類</label>
                        <select name="chrCategory_ID"
                                class="validate[required]"
                                tabindex="5"
                                style="float:left;height:37px;width:207px;">
                            <option/>
                            <?php foreach ($cats as $c) : ?>
                                <option <? if ($c->chrID == $targetGroup->chrCategory_ID) echo "selected"; ?>
                                    value="<? echo $c->chrID ?>"><?php echo $c->chrID . "  " . $c->chrName; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p class="list">
                        <label class="list">消費税率</label>
                        <input
                            tabindex="6"
                            style="width: 200px; margin: 0 0 0 18px;" type="text"
                            size="10"
                            class="chrCategory_ID validate[required,onlyNumberSp,maxSize[2],custom[required_1_to_2_digits]]"
                            data-prompt-position="topLeft:140" name="intTax_Rate"
                            value="<?
                            echo $targetGroup->intTax_Rate;
                            ?>"/>
                    </p>

                    <p style="float: left; text-align: center; width: 300px;"
                       id="buttonlist">
                        <input
                            tabindex="7"
                            class="center_button hvr-fade" type="submit" name="submit"
                            size="10" value="登録"/>
                        <a
                            tabindex="8"
                            class="center_button hvr-fade" href="./group.php"
                            style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                        <a
                            tabindex="9"
                            class="center_button hvr-fade" href="./index.php"
                            style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
                    </p>

                    <div
                        style="float: right; width: -100%; height: 100px; margin: 10px 0; text-align: center; vertical-align: middle;">
                        <a
                            tabindex="10"
                            class="center_button hvr-fade" href="../utils/excel_export.php"
                            style="display: block; text-decoration: none; width: 150px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">EXCELへ出力&nbsp;<i
                                class="fa fa-file-text-o"></i>&nbsp;</a>
                        <a
                            tabindex="11"
                            class="center_button hvr-fade" href="../utils/csv_export.php"
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
                    "部門名" => 300,
                    "原価率" => 80,
                    "大分類" => 80,
                    "消費税率" => 80,
                    "選択" => 50,
                    "削除" => 70
                ];

                $prop = [
                    'chrID' => 'center',
                    'chrName' => 'left',
                    'intCost_Rate' => 'left',
                    'chrCategory_ID' => 'left',
                    'intTax_Rate' => 'left',
                ];

                get_list($header, $contents, "chrID", $prop, "800px");

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
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="group";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>