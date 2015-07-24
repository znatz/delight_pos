<?php
require './utils/password.php';
require './utils/connect.php';
require_once './mapping/staff_class.php';

session_start();
$errorMessage = "";

if (isset($_POST["back"])) header("Location: Login.php");

// 強制ログアウトボタン処理
if (isset($_POST["submit"])) {
    $staff = unserialize($_SESSION["staff"]);
    session_regenerate_id(true);
    $connection = new Connection;
    Staff::update_to_column('chrSession', 'null', 'chrLogin_ID', $staff->chrLogin_ID);
    $staff->chrSession = session_id();
    $_SESSION["staff"] = serialize($staff);
    header("Location: Login.php");
    exit;

}
?>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>POSCO</title>
    <link rel="stylesheet" href="../css/validationEngine.jquery.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="./css/blended_layout.css">
    <link rel="stylesheet" href="../css/template.css" type="text/css"/>
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

        p.list {
            width: 500px;
            height: 37px;
            color: #000000;
        }

        p.list input[type="text"], input[type="password"], select {
            float: left;
            height: 35px;
            border: 1px solid #555555;
            background: #faffbd;
            transition: border 0.3s;
            margin-left: 10px;
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

        .pageFooter {
            width: 900px;
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
        <form method="POST" id="login_form" style="margin:10px 0; width:700px;height:300px;text-align: center;"
              class="search_form">
            <p class="list" style="text-align: center;">

            <p>「重複ログインエラー」が発生したために、ログインできませんでした。</p>
            <br>

            <p>入力されたユーザーIDの接続情報がシステムに残っています。</p><br/>
            <br>

            <p>IDを複数人で同時に使用されていない場合は、</p>
            <br>

            <p>次の画面に進み「強制ログアウト処理」を行ってください。</p>
            <br>

            <p>強制ログアウトを行うと、IDの接続情報を解除することができます。</p>
            <br>
            <input class="center_button hvr-fade" style="width:100px;margin:0 10px 0 240px;;" type="submit" name="back"
                   value="＜＜戻る"/>
            <input class="center_button hvr-fade" style="width:100px;margin:0 100px 0 0;" type="submit" name="submit"
                   value="＞＞続ける"/>
            </p>

        </form>
        <br/>
    </div>
    <div class="pageFooter">
        <h4 style="color:#ffffff;text-align:center;padding:2px 0 0 0; text-align: center; width:100%;">CopyRight 2015
            POSCO Co.Ltd All Rights
            Reserved</h4>
    </div>
</div>
</body>
</html>
