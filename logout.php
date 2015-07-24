<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/staff_class.php';

session_start();
if (!isset($_SESSION['staff'])) header("Location: Login.php");
$staff = unserialize($_SESSION["staff"]);

Staff::update_to_column('chrSession', 'null', 'chrLogin_ID', $staff->chrLogin_ID);
session_start();
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(), '', 0, '/');
session_regenerate_id(true);
?>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>POSCO</title>
    <link rel="stylesheet" type="text/css" href="./css/blended_layout.css">
    <link rel="stylesheet" href="../css/form.css" type="text/css"/>
    <link rel="stylesheet" href="../css/button.css" type="text/css"/>
    <style>
        * {
            font-family: Verdana;
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

        h3 {
            position: relative;
            display: block;
            width: 500px;
            text-align: center;
            font-weight: bold;;
        }
    </style>
</head>
<body>
<div class="blended_grid">
    <div style="float:right; margin:200px 50px 100px 150px;width:300px;">
        <h3 style="line-height: 10px;text-align:left;font-weight:normal;">クラウドPOSシステム</h3>
        <img src="./img/delight.png" alt="Delight" title="Delight"/>
    </div>
    <div style="float:left; margin:200px 0 100px 200px;">
        <img src="./img/posco.png" alt="POSCO" title="POSCO"/>
    </div>
    <div class="pageContent">
        <div style="clear:both;margin:5px auto ; padding:0 100px;width:700px;text-align: left;font-size:15px;font-weight:bold;">

            <h3>ログアウトしました。</h3>
            <br>
            <br>
            <br>
            <br>
            <a class="center_button hvr-fade" style="width:460px; margin:0 auto;" href="./Login.php">ログインページへ</a>

            <br/>
        </div>
    </div>


    <? include('./html_parts/footer.html'); ?>
</div>
</body>
</html>