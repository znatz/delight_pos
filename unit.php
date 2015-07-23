<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/unit_class.php';
require_once './mapping/staff_class.php';
require_once './mapping/group_class.php';
require_once './utils/html_parts_generator.php';
require_once './utils/helper.php';
require_once './mapping/menu_class.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetUnit = Unit::get_one_empty_unit();;

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全担当データを一回取り出す
$contents = Unit::get_all_unit();

// 部門IDリストを取得
$groups = Group::get_distinct_group_chrID();

// 新規ボタンの処理
/*if (isset($_POST['newID'])) {
    $targetUnit = Unit::get_new_unit();
    unset($_POST['newID']);
}*/

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {

    $targetUnit = Unit::get_one_unit($_POST["targetID"]);

    // 選択を解除
    unset($_POST["target"]);
    $contents = Unit::get_all_unit();
}

// 削除ボタン処理
if (isset($_POST['delete'])) {
    if (Unit::delete_one_unit($_POST['delete'])) {
        $contents = Unit::get_all_unit();
        $successMessage = "削除しました。";
    } else {
        $errorMessage = "削除失敗しました。";
    }
}

// 　登録処理
if (isset($_POST["submit"])) {
    if (Unit::insert_one_unit($_POST['chrID'],
        $_POST['chrGroup_ID'],
        $_POST['chrName'],
        $_POST['chrShort_Name'],
        $_POST['intDiscount'],
        $_POST['intTax_Type'],
        $_POST['intPoint_Flag'])
    ) {
        $successMessage = "追加しました。";
    } else {
        // 更新処理開始
        if (mysql_errno() == 1062) {
            if (Unit::update_one_unit($_POST['chrID'],
                $_POST['chrGroup_ID'],
                $_POST['chrName'],
                $_POST['chrShort_Name'],
                $_POST['intDiscount'],
                $_POST['intTax_Type'],
                $_POST['intPoint_Flag'])
            ) {
                $successMessage = "更新しました。";
            };
        }
    }

    // 再度リストを更新
    $contents = Unit::get_all_unit();
    $_POST["targetID"] = $chrID;
}

$contents = Unit::get_all_unit();
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
                    {"bSortable": false, "aTargets": [7, 8]}
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
            $("#jMenu").jMenu({
                ulWidth: 'auto',
                effects: {
                    effectSpeedOpen: 300,
                    effectTypeClose: 'slide'
                },
                animatedText: false,
                openClick: true
            });
            $("#myTable").tablesorter({
                headers: {
                    5: {sorter: false},
                    6: {sorter: false}
                }
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
            width: 1000px;
            margin: 0 auto;
            clear: both;
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
            margin: 10px 0 5px 50px;
            height: 20px;
            width: 150px;
            text-align: right;
            font-size: 14px;
        }

        input.center_button {
            width: 90px;
            height: 40px;
            margin: 30px 5px 30px 5px;
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
                        <legend>品種マスタ</legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">部門コード</label>
                        <select
                            tabindex="1"
                            class="validate[required]"
                            name="chrGroup_ID" style="float:left;height:37px;width:207px;">
                            <option/>
                            <?php foreach ($groups as $g) : ?>
                                <option
                                    <? if ($targetUnit->chrGroup_ID == $g[0]) echo "selected"; ?>
                                    value="<?php echo $g[0] ?>"><?php echo $g[0] . " " . $g[1]; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a
                            tabindex="8"
                            class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu"
                            href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    </p>
                    <p class="list">
                        <label class="list">品種コード</label>
                        <input
                            tabindex="2"
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="chrID validate[custom[integer],custom[required_4_digits] text-input"
                            data-prompt-position="topLeft:140" name="chrID" placeholder="0000"
                            value="<?php
                            echo $targetUnit->chrID;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">品種名</label>
                        <input
                            tabindex="3"
                            style="width: 300px; margin: 0 0 0 18px;" type="text" size="10"
                            class="chrName validate[required] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?php
                            echo $targetUnit->chrName;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">略称</label>
                        <input
                            tabindex="4"
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="chrShort_Name validate[required,maxSize[20],custom[katagana]] text-input"
                            data-prompt-position="topLeft:140" name="chrShort_Name"
                            value="<?php
                            echo $targetUnit->chrShort_Name;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">割引率</label>
                        <input
                            tabindex="5"
                            style="width: 200px; margin: 0 0 0 18px;" type="text"
                            size="10"
                            class="intDiscount validate[required,onlyNumberSp,max[100]]"
                            data-prompt-position="topLeft:140" name="intDiscount"
                            value="<?php
                            echo $targetUnit->intDiscount;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">税区</label>
                        <select
                            tabindex="6"
                            class="validate[required]"
                            name="intTax_Type" style="float:left;height:37px;width:207px;">
                            <option/>
                            <option value="0"
                                <?php if ($targetUnit->intTax_Type == "0") echo "selected"; ?>>０：外税
                            </option>
                            <option value="1"
                                <?php if ($targetUnit->intTax_Type == "1") echo "selected"; ?>>１：内税
                            </option>
                            <option value="2"
                                <?php if ($targetUnit->intTax_Type == "2") echo "selected"; ?>>２：非課税
                            </option>
                        </select>
                    </p>
                    <p class="list">
                        <label class="list">ポイント対象</label>
                        <select
                            tabindex="7"
                            class="validate[required]"
                            name="intPoint_Flag" style="float:left;height:37px;width:207px;">
                            <option/>
                            <option value="0"
                                <?php if ($targetUnit->intPoint_Flag == "0") echo "selected"; ?>>０：対象
                            </option>
                            <option value="1"
                                <?php if ($targetUnit->intPoint_Flag == "1") echo "selected"; ?>>１：対象外
                            </option>
                        </select>
                    </p>

            <? include('./html_parts/form_button.html'); ?>
                </form>
            </div>
            <form action=""></form>
        </div>
        <!-- ********************* マスタの作成 終了　**********************	-->


        <!-- ********************* リストの作成 開始　**********************	-->
        <div id="user_list" style="overflow:auto !important;">
            <form method="post" id="list" action="">
                <?php
                $header = [
                    "品種コード" => 80,
                    "部門コード" => 120,
                    "品種名" => 300,
                    "略称" => 150,
                    "割引率" => 80,
                    "税区" => 50,
                    "ポイント対象" => 100,
                    "選択" => 50,
                    "削除" => 70
                ];
                echo '<table id="myTable" style="border:0;padding:0;border-radius:5px;" class="search_table tablesorter">';
                echo '<thead><tr>';
                foreach ($header as $name => $width)
                    echo '<th width="' . $width . 'px">' . $name . '</th>';
                echo '</thead></tr><tbody>';


                foreach ((array)$contents as $row) {
                    echo '<tr class="not_header" id="' . $row->chrID . '">';
                    echo '<td>' . $row->chrID . '</td>';
                    echo '<td>' . $row->chrGroup_ID . '</td>';
                    echo '<td>' . $row->chrName . '</td>';
                    echo '<td>' . $row->chrShort_Name . '</td>';
                    echo '<td style="text-align: right;">' . $row->intDiscount . '</td>';
                    echo '<td style="text-align: center;">' . $row->intTax_Type . '</td>';
                    echo '<td style="text-align:center;">' . $row->intPoint_Flag . '</td>';
                    echo '<td text-align:center;"><input type="radio" onclick="javascript: submit()" name="targetID" id="targetID" value="' . $row->chrID . '"/></td>';
                    echo '<td style="padding:0 0 0 2px;"><button onClick="if(!confirm(\'削除しますか？\')){return false;}"  class="center_button hvr-fade delete_button" style="width:65px; height:30px; margin:0;padding:0;font-weight:normal;" type="submit" name="delete" value="' . $row->chrID . '">削除</button></td>';
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
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="unit";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>