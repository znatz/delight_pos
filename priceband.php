<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/priceband_class.php';
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
$targetGroup = Priceband::get_one_empty_priceband();;

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全マスタを一回取り出す
$contents = Priceband::get_all_priceband();

//// 大分類のIDを取り出す
//$catids = Category::get_distinct_category_chrID();

// 新規ボタンの処理
if (isset($_POST['newID'])) {
    $targetGroup = Priceband::get_new_priceband();
    unset($_POST['newID']);
}

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {

    $targetGroup = Priceband::get_one_priceband($_POST["targetID"]);

    // 選択を解除
    unset($_POST["target"]);
    $contents = Priceband::get_all_priceband();
}

// 削除ボタン処理
for ($i = 0; $i <= 99; $i++) {
    $two_digits = str_pad($i, 2, '0', STR_PAD_LEFT);
    if (isset($_POST[$two_digits])) {
        if (Priceband::delete_one_priceband($two_digits)) {
            $contents = Priceband::get_all_priceband();
            $successMessage = "削除しました。";
        } else {
            $errorMessage = "削除失敗しました。";
        }
        break;
    }
}

// 　登録処理
if (isset($_POST["submit"])) {
    if (Priceband::insert_one_priceband($_POST['chrID'],
        $_POST['chrName'],
        $_POST['intUnder_Bound'],
        $_POST['intUpper_Bound'])
    ) {
        $successMessage = "追加しました。";
    } else {
        // 更新処理開始
            if (Priceband::update_one_priceband($_POST['chrID'],
                $_POST['chrName'],
                $_POST['intUnder_Bound'],
                $_POST['intUpper_Bound'])
            ) {
                $successMessage = "更新しました。";
            };
    }

    // 再度リストを更新
    $contents = Priceband::get_all_priceband();
    $_POST["targetID"] = $chrID;
}

$contents = Priceband::get_all_priceband();
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
                    {"bSortable": false, "aTargets": [4, 5]}
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
        * {
            font-family: Verdana;
        }

        input {
            border: 1px solid #000000;
        }

        input[type="text"], input[type="password"], select {
            padding: 0 0 0 5px;
            font-size: 14px;
        }

        input[disable="disable"] {
            font-size: 14px;
            padding: 0 0 0 5px;
        }

        select {
            float: left;
            border: 1px solid #555555;
            margin: 0 0 0px 18px;
            width: 199px;
            font-size: 14px;
            background: #faffbd;
        }

        #user_list {
            width: 800px;
            margin: 0 auto;
            clear: both;
        }

        #buttonlist {
            margin: 10px 0;
        }

        p.list {
            width: 700px;
            height: 37px;
            color: #000000;
        }

        p.list input[type="text"], input[type="password"], select {
            float: left;
            height: 35px;
            border: 1px solid #555555;
            background: #faffbd;
            transition: border 0.3s;
        }

        p.list input[type="text"]:focus, input[type="password"]:focus, select:focus {
            background: #ffffff;
            border-bottom: solid 1px #FDAB07;
        }

        label.list {
            display: block;
            float: left;
            margin: 10px 0 5px 0;
            height: 20px;
            width: 150px;
            text-align: right;
            font-size: 14px;
        }

        input.delete_button {
            background-image: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#c2c2c2));
            background-image: -webkit-linear-gradient(top, #FFFFFF, #c2c2c2);
            background-image: -moz-linear-gradient(top, #FFFFFF, #c2c2c2);
            background-image: -ms-linear-gradient(top, #FFFFFF, #c2c2c2);
            background-image: -o-linear-gradient(top, #FFFFFF, #c2c2c2);
            background-image: linear-gradient(to bottom, #FFFFFF, #c2c2c2);
            filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0, startColorstr=#FFFFFF, endColorstr=#c2c2c2);
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

                <form class="search_form" style="margin: 0 auto; width: 700px; height: 220px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>価格帯マスタ</legend>
                    </fieldset>
                    <a class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu"
                    		href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    <p class="list">
                        <label class="list">コード</label>
                        <input
                            class="chrID validate[custom[integer],custom[required_2_digits] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 290px; margin: 0 0 0 18px;" name="chrID"
                            type="text" size="10" placeholder="00"
                            value='<?php
                            echo $targetGroup->chrID;
                            ?>'/>
                        <input class="newID hvr-fade"
                                         style="width: 100px; height: 37px; margin: 0;" type="submit"
                                         name="newID" id="newID" size="10" value="新規"/>
                    </p>

                    <p class="list">
                        <label class="list">価格帯名</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?php
                            echo $targetGroup->chrName;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">価格帯下限</label> <input
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="intUnder_Bound validate[required,onlyNumberSp,custom[required_3_digits]] text-input"
                            data-prompt-position="topLeft:140" name="intUnder_Bound"
                            value="<?php
                            echo $targetGroup->intUnder_Bound;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">価格帯上限</label> <input
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="intUpper_Bound validate[required,onlyNumberSp,custom[required_3_digits]] text-input"
                            data-prompt-position="topLeft:140" name="intUpper_Bound"
                            value="<?php
                            echo $targetGroup->intUpper_Bound;
                            ?>"/>
                    </p>

                    <p style="float: left; text-align: center; width: 300px; margin: 40px;"
                       id="buttonlist">
                        <input class="center_button hvr-fade" type="submit" name="submit"
                               size="10" value="登録"/>
                        <a class="center_button hvr-fade" href="./priceband.php" style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                        <a class="center_button hvr-fade" href="./index.php" style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
                    </p>

                    <div
                        style="float: right; width: -100%; height: 100px; margin: 15px 0; text-align: center; vertical-align: middle;">

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
                    "コード" => 100,
                    "価格帯名" => 150,
                    "価格帯下限" => 120,
                    "価格帯上限" => 120,
                    "選択" => 52,
                    "削除" => 70
                ];

                echo '<table id="myTable" style="table-layout:fixed;border:0;padding:0;border-radius:5px;" class="search_table tablesorter">';
                echo '<thead><tr>';
                foreach ($header as $name => $width)
                    echo '<th width="' . $width . '">' . $name . '</th>';
                echo '</tr></thead><tbody>';


                foreach ((array)$contents as $row) {
                    echo '<tr class="not_header" id="' . $row->chrID . '">';
                    echo '<td width="100px;">' . $row->chrID . '</td>';
                    echo '<td width="152px;">' . $row->chrName . '</td>';
                    echo '<td width="120px;" style="text-align:right;">' . $row->intUnder_Bound . '</td>';
                    echo '<td width="120px;" style="text-align:right;">' . $row->intUpper_Bound . '</td>';
                    echo '<td style="width:52px;text-align:center;"><input type="radio" onclick="javascript: submit()" name="targetID" id="targetID" value="' . $row->chrID . '"/></td>';
                    echo '<td style="width:70px;padding:2px;"><input class="center_button hvr-fade delete_button" style="width:65px; height:25px; margin:0;padding:0;font-weight:normal;" type="submit" name="' . $row->chrID . '" value="削除"/></td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';

                $_SESSION["sheet"] = serialize($contents);
                array_pop($header);
                array_pop($header);
                $_SESSION["sheet_header"] = array_keys($header);
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
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="priceband";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>