<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/staff_class.php';
require_once './mapping/menu_class.php';
require_once './utils/helper.php';

session_start();
if (! isset($_SESSION['staff']))
    header("Location: Login.php");
$staff = unserialize($_SESSION["staff"]);
session_check();
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="./css/blended_layout.css">
<link rel="stylesheet" type="text/css" href="./css/search.css">
<title>POSCO</title>
<meta name="description" content="POSCO">
    <script src="./js/jquery.1.9.0.js"></script>
    <script src="../js/jquery-1.8.2.min.js" type="text/javascript"></script>
	<script src="../js/languages/jquery.validationEngine-ja.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <script src="../js/jquery.sidr.min.js"></script>

    <script src="../js/jquery.smartmenus.js" type="text/javascript"></script>
    <link href='../css/sm-core-css.css' rel='stylesheet' type='text/css' />
    <link href='../css/sm-blue/sm-blue.css' rel='stylesheet' type='text/css' />
<!--<link rel="stylesheet" type="text/css" href="./css/blended_layout.css">-->

<script type="text/javascript">
    		jQuery(document).ready(function() {

                $('#main-menu').smartmenus();

    		});
</script>
</head>
<body>
	<div class="blended_grid">
		<div class="pageHeader">
<?php include('./html_parts/header.html');?>
        </div>
		<div class="pageContent">        	
<?php include('./html_parts/top_menu.html');?>
        </div>
		<div class="pageFooter">
			<h4 style="color: #ffffff; text-align: center; padding: 4px 0 0 0;">CopyRight
				201 POSCO Co.Ltd All Rights Reserved</h4>
		</div>
	</div>
</body>
</html>