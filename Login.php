<?php
require_once 'password.php';
require_once 'connect.php';
require_once 'staff_class.php';

session_start();
$_SESSION = array();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";

if (isset($_POST["submit"])) {

    if (empty($_POST["loginid"])) {
        $errorMessage = "ログインIDが未入力です。";
    } else if (empty($_POST["password"])) {
        $errorMessage = "パスワードが未入力です。";
    }

    if (!empty($_POST["loginid"]) && !empty($_POST["password"])) {

        $staff = Staff::findBy('chrLogin_ID', $_POST['loginid']);
        if (!$staff) {
            print('クエリーが失敗しました。' . mysql_error());
            $connection->close();
            exit();
        }
        if (password_verify($_POST['password'], $staff->chrPasswordHash)) {
            if (is_null($staff->chrSession)) {
                $staff = Staff::update_staff_session($staff);
                $_SESSION["loginid"] = $_POST["loginid"];
                $_SESSION["staff"] = serialize($staff);
                header("Location: index.php");
            } else {
                $errorMessage = "重複ログイン。";
                $_SESSION["staff"] = serialize($staff);
                header("Location: force_logout.php");
            }
        } else {
            $errorMessage = "ログインIDあるいはパスワードに誤りがあります。";
        }
    }

}
?>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>POSCO</title>
    <link rel="stylesheet" href="../css/validationEngine.jquery.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="./css/blended_layout.css">
    <link rel="stylesheet" href="../css/form.css" type="text/css"/>
    <link rel="stylesheet" href="../css/button.css" type="text/css"/>
    <link rel="stylesheet" href="../css/table.css" type="text/css"/>
    <script src="../js/jquery-1.8.2.min.js" type="text/javascript"></script>
    <script src="../js/languages/jquery.validationEngine-ja.js" type="text/javascript" charset="utf-8"></script>
    <script src="../js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("#login_form").validationEngine();
            $("#login_form").bind("jqv.field.result", function (event, field, errorFound, prompText) {
                console.log(errorFound)
            })
        });
    </script>
    <style>
       label.list {
            padding-right:10px;
        }
        .blended_grid, .pageContent {
            background: white;
            margin: 0 auto;
            width: 900px;
            padding: 0;
            border: none;
        }

        .pageContent {
            padding: 0 100px;
            margin-top: 0;
            width: 700px;
            height: 400px;
        }
    </style>
</head>
<body>
<div class="blended_grid">

    <div style="float:right; margin:200px 200px 0 0;">
        <h3 style="line-height: 10px;">クラウドPOSシステム</h3>
        <img src="./img/delight.png" alt="Delight" title="Delight"/>
    </div>
    <div style="float:left; margin:200px 0 0 200px;">
        <img src="./img/posco.png" alt="POSCO" title="POSCO"/>
    </div>
    </h1>
    <div class="pageContent">
        <div style="margin:5px auto ; padding:0 100px;width:700px;text-align: left;font-size:15px;font-weight:bold;">
            <div style="margin:0 0 8px 0;width:500px;"> <?php include('./html_parts/warning.html'); ?></div>
            <h3>ログインIDとパスワードを入力してください</h3>
        </div>
        <div id="form" style="margin:0 100px auto; width:700px;">
            <form method="POST" action="Login.php" id="login_form" style="width:500px;height:200px;text-align: center;"
                  class="search_form">
                <p class="list">
                    <br>
                    <br>
                    <label for="loginid" class="list">ログインID</label>
                    <input type="text" id="loginid" name="loginid"
                           class="loginid validate[required,custom[onlyLetterNumber],maxSize[6]] text-input"
                           placeholder="ログインID" data-prompt-position="topLeft:5">
                </p>
                <br/>

                <p style="text-align: center;" class="list">
                    <br>
                    <br>
                    <label for="password" class="list">パスワード</label>
                    <input type="password" id="password" name="password"
                           class="password validate[required,onlyNumberSp,maxSize[6]]" placeholder="パスワード"
                           data-prompt-position="topLeft:5">
                </p>
                <br/>

                <p class="list" style="text-align: center;">
                    <br>
                    <br>
                    <input type="submit" name="submit" value="ログインする" style="width:150px;margin:0 150px auto;"
                           class="center_button hvr-fade"/>

                </p>
            </form>
        </div>
        <br/>
    </div>
    <? include('./html_parts/footer.html'); ?>
</div>
</body>
</html>