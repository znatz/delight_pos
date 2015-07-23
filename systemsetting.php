<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/staff_class.php';
require_once './mapping/systemsetting_class.php';
require_once './utils/html_parts_generator.php';
require_once './utils/helper.php';
require_once './mapping/menu_class.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetSystemsetting = new Systemsetting("", "", "", "", "", "", "", "", "", "", "", "");

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// chrID取得の為データを一回取り出す
$targetSystemsetting = Systemsetting::get_all_systemsetting();
$chrID = $targetSystemsetting ->chrID ;

//　登録処理
if (isset($_POST["submit"])) {
	// フォームからデータを取り出す
//	$chrID = "999999";
	$rate = $_POST["intRate"];
	$intRoundType= $_POST["intRoundType"];
	$company = $_POST["chrCompanyName"];
	$post = $_POST["chrPost"];
	$address = $_POST["chrAddress"];
	$addressno = $_POST["chrAddressNo"];
	$tel = $_POST["chrTel"];
	$fax = $_POST["chrFax"];
	$comment1 = $_POST["chrInvoiceComment1"];
	$comment2 = $_POST["chrInvoiceComment2"];
	$comment3 = $_POST["chrInvoiceComment3"];

	// データベースを削除
	$query = <<<EOF
	DELETE FROM systemsetting;
EOF;
	$connection = new Connection();
	$connection->result($query);
	$connection->close();

	// データベースに挿入
	$query = <<<EOF
	INSERT INTO systemsetting(chrID, intRate, intRoundType, chrCompanyName, chrPost, chrAddress, chrAddressNo, chrTel, chrFax, chrInvoiceComment1, chrInvoiceComment2, chrInvoiceComment3) 
	VALUES ('$chrID', '$rate', '$intRoundType', '$company', '$post', '$address', '$addressno', '$tel', '$fax', '$comment1', '$comment2', '$comment3');
EOF;
	$connection = new Connection();
	$connection->result($query);
	$connection->close();
        $successMessage = "更新しました。";
	
}

// フォーム表示の為データを一回取り出す
$targetSystemsetting = Systemsetting::get_all_systemsetting();

$post = $targetSystemsetting ->chrPost;
$address = $targetSystemsetting ->chrAddress;
// 検索ボタンの処理
if (isset($_POST['SearchPost'])) {
    $post = $_POST["chrPost"];
    $connection = new Connection();
    $query = "select * from post Where chrID='" . $post ."';";
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
                        "sProcessing":   "処理中...",
                        "sLengthMenu":   "_MENU_ 件表示",
                        "sZeroRecords":  "データはありません。",
                        "sInfo":         " _TOTAL_ 件中 _START_ から _END_ まで表示",
                        "sInfoEmpty":    " 0 件中 0 から 0 まで表示",
                        "sInfoFiltered": "（全 _MAX_ 件より抽出）",
                        "sInfoPostFix":  "",
                        "sSearch":       "検索:",
                        "sUrl":          "",
                        "oPaginate": {
                        "sFirst":    "先頭",
                            "sPrevious": "前",
                            "sNext":     "次",
                            "sLast":     "最終"
                        }
                    },
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": [ 4, 5 ] }
                    ]
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
            margin: 0 0 5px 18px;
            height: 35px;
            width: 99px;
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
	label.list2 {
		display: block;
		float: left;
		margin: 10px 0 5px 5px;
		height: 20px;
		width: 150px;
		text-align: left;
		font-weight: bold;
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

            <!-- ********************* フォームの作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form" style="margin: 0 auto; width: 700px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>システム設定</legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">消費税率</label> <input
                            class="validate[required,onlyNumberSp,maxSize[2]] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 103px; margin: 0 0 0 18px;" name="intRate"
                            type="text" size="10" placeholder="00"
                            value='<?php
                            echo $targetSystemsetting->intRate;
                            ?>'/> 
			<label class="list2">％</label>
                        <a class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu"
                           href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    </p>
                    <p class="list">
                        <label class="list">小数点計算</label> <select name="intRoundType" style="width:110px">
                            <option value="1"
                                <?php if ($targetSystemsetting->intRoundType == 1) echo "selected"; ?>>1.切捨て
                            </option>
                            <option value="2"
                                <?php if ($targetSystemsetting->intRoundType == 2) echo "selected"; ?>>2.四捨五入
                            </option>
                        </select> <label
                            style="display: block; float: left;  height: 40px; width: 288px; text-align: center; font-size: 14px; background-color: #eeeeee; line-height: 40px; letter-spacing:5px;">1:切捨て
                            2:四捨五入</label>
                    </p>
                    <p class="list">
                        <label class="list">会社名</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[25]] text-input"
                            data-prompt-position="topLeft:140" name="chrCompanyName"
                            value="<?php
                            echo $targetSystemsetting->chrCompanyName;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用郵便番号</label><input
                            style="width: 290px; margin: 0 0 0 18px;" type="text" size="10" placeholder="000-0000"
                            class="validate[maxSize[8]] text-input"
                            data-prompt-position="topLeft:140" name="chrPost"
                            value="<?php
                            echo $post;
                            ?>"/>
			<input class="newID hvr-fade"
                                         style="width: 100px; height: 37px; margin: 0;" type="submit"
                                         name="SearchPost" id="SearchPost" size="10" value="検索"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用住所</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrAddress"
                            value="<?php
                            echo $address;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用番地</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrAddressNo"
                            value="<?php
                            echo $targetSystemsetting->chrAddressNo;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用電話番号</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[13]] text-input"
                            data-prompt-position="topLeft:140" name="chrTel"
                            value="<?php
                            echo $targetSystemsetting->chrTel;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用FAX番号</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[13]] text-input"
                            data-prompt-position="topLeft:140" name="chrFax"
                            value="<?php
                            echo $targetSystemsetting->chrFax;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用備考①</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrInvoiceComment1"
                            value="<?php
                            echo $targetSystemsetting->chrInvoiceComment1;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用備考②</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrInvoiceComment2"
                            value="<?php
                            echo $targetSystemsetting->chrInvoiceComment2;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">請求書用備考③</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrInvoiceComment3"
                            value="<?php
                            echo $targetSystemsetting->chrInvoiceComment3;
                            ?>"/>
                    </p>
		    <p style="float: left; text-align: center; width: 290px;margin: 10px 0 0 240px;"
			id="buttonlist">
			<input class="center_button hvr-fade" type="submit" name="submit"
				size="10" value="登録"/>
			<a class="center_button hvr-fade" href="./index.php"
				style="display: block; text-decoration: none; width: 75px; height: 14px; margin: 30px 5px 0 40px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
		    </p>
                </form>
            </div>
            <form action=""></form>
        </div>
        <!-- ********************* フォームの作成 終了　**********************	-->


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
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="systemsetting";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>