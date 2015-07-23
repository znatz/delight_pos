<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/staff_class.php';

session_start();
if(!isset($_SESSION['staff'])) header("Location: Login.php");
$staff = unserialize($_SESSION["staff"]);

    $connection = new Connection();
    $query = "UPDATE `staff` SET `chrSession`= null WHERE `chrLogin_ID`= '".$staff->chrLoginID. "'";
    $connection-> result($query) or die("SQL Error 1: " . mysql_error());
    $connection-> close();
    session_start();
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);
?>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>POSCO</title>
     <link rel="stylesheet" type="text/css" href="./css/blended_layout.css">
    <link rel="stylesheet" href="../css/template.css" type="text/css"/>
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

        .pageFooter {
            width: 900px;
        }

        h3 {
            text-align: center;
            font-weight: bold;;
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
            <div style="margin:0;width:500px;"> <?php include('./html_parts/warning.html'); ?></div>
        </div>
        <div id="form" style="margin:0 100px auto; width:700px;">
            <form method="POST" action="Login.php" id="login_form" style="width:500px;height:200px;text-align: center;"
                  class="search_form">

                <p class="list" style="text-align: center;">
                    <br>
                    <br>
            <h3><?php echo $_GET['LOGOUT_MSG']; ?>ログアウトしました。</h3>
                <br>
                <br>
                <br>
                <br>
              <a class="center_button hvr-fade" style="width:460px; margin:0 auto;" href="./Login.php">ログインページへ</a>

                </p>
            </form>
        </div>
        <br/>
    </div>





           <div class="pageFooter">
                <h4 style="color:#ffffff;text-align:center;padding:2px 0 0 0;">CopyRight 2015 POSCO Co.Ltd  All Rights Reserved</h4>
            </div>
        </div>
    </body>
</html>