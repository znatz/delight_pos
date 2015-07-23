<?php
session_start();
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/menu_class.php';
require_once './mapping/staff_class.php';

session_start();
if (!isset($_SESSION['staff']))
    header("Location: Login.php");
$staff = unserialize($_SESSION["staff"]);

global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
if (isset($_POST["submit"])) {
    if ($_POST["newpass"] != $_POST["repeatnew"]) {
        $errorMessage = "再入力と新パスワード一致ではありません。";
    } else {

        $connection = new Connection();
        $query = "SELECT * FROM staff WHERE chrLogin_ID = '" . $staff->chrLoginID . "'";
        $result = $connection->result($query);
        if (!$result) {
            print('クエリーが失敗しました。' . mysql_error());
            $connection->close();
            exit();
        }

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $db_hashed_pwd = $row['chrPasswordHash'];
            $new_staff = new Staff($row['chrID'], $row['chrName'], $row['chrLogin_ID'], $row['intAuthority_ID'], $row['chrPasswordHash'], $row['chrSession']);
        }


        if (password_verify($_POST["oldpass"], $db_hashed_pwd)) {
            $query = "UPDATE staff SET chrPasswordHash='" . password_hash($_POST["newpass"], PASSWORD_DEFAULT) . "' WHERE chrLogin_ID= '" . $staff->chrLoginID . "'";
            $connection->result($query);
            $successMessage = "パスワードを更新しました。";
            $connection->close();
        } else {
            $errorMessage = "旧パスワードが間違います。";
        }
    }

}
?>

<!DOCTYPE html>
<head>
    <?php include('./html_parts/css_and_js.html'); ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            $('#main-menu').smartmenus();
            jQuery("#user_add_form").validationEngine();
            jQuery("#password_change_form").validationEngine();
            $("#password_change_form").bind("jqv.field.result", function (event, field, errorFound, prompText) {
                console.log(errorFound)
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

        .pageContent {
            text-align: center;
            width:1100px;
            height:800px;
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
            margin: 10px 5px 5px 0;
            height: 20px;
            width: 200px;
            text-align: right;
            font-size: 14px;
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
            <div style="clear: both; float: top;text-align:center;">
                <form style="width:700px;height:300px;margin:0 auto;" method="post" id="password_change_form" class="search_form">
                    <fieldset>
                        <legend style="margin:10px;font-size:20px;">
                            ユーザーパスワード変更
                        </legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">ユーザーID</label>
                        <input style="width:300px" type="text" size="10" disabled="disabled"
                               value="<?php echo $staff->chrLoginID ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">旧パスワード</label>
                        <input style="width:300px" type="password" size="10" name="oldpass"
                               class="password validate[required,onlyNumberSp,maxSize[6]]"/>
                    </p>

                    <p class="list">
                        <label  class="list">新パスワード</label>
                        <input style="width:300px" type="password" size="10" name="newpass"
                               class="password validate[required,onlyNumberSp,maxSize[6]]"/>
                    </p>

                    <p class="list">
                        <label  class="list">新パスワード（再入力）</label>
                        <input style="width:300px" type="password" size="10" name="repeatnew"
                               class="password validate[required,equals[password],onlyNumberSp,maxSize[6]]"/>
                    </p>

                    <p class="list">
                        <input class="center_button hvr-fade" style="width:150px;margin-left:200px;" type="submit"
                               value="パースワード変更" name="submit"/>
                        <input class="center_button hvr-fade"  type="reset" value="クリア"/>
                        <input class="center_button hvr-fade" type="button" value="戻る"
                               onClick="history.go(-1);return true;">
                    </p>
                </form>
            </div>


       </div>
    </div>
             <div class="pageFooter">
                <h4 style="color: #ffffff; text-align: center; padding: 4px 0 0 0;">CopyRight
                    2015 POSCO Co.Ltd All Rights Reserved</h4>
            </div>
    <!-- ********************  入力規則　開始      *********************** -->
    <div id="sidr-right">
        <?php
        $connection = new Connection();
        $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="password_change";';
        $result = $connection->result($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo $row['txtInstruction'];
        ?>
    </div>
    <!-- ********************  入力規則　終了      *********************** -->
</body>
</html>

