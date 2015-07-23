<?php
require_once './utils/password.php';
require_once './utils/connect.php';
require_once './mapping/category_class.php';
require_once './utils/html_parts_generator.php';
require_once './utils/helper.php';

session_start();
$errorMessage = "";
// 大分類を取り出す関数
function get_all_category() {
	$connection = new Connection();
	$query = "select * from category";
	
	
	$result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    $contents[] = new Category($row['chrID'], $row['chrName']);
	}
	return $contents;	
}

// 全ての大分類chrIDを取り出す関数
function get_all_category_chrID() {
	$connection = new Connection();
	$query = "select chrID from category order by chrID";
	
	
	$result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    $all_chrID[] = $row['chrID'];
	}
	return $all_chrID;	
}

// ログイン状態チェック
if (!isset($_SESSION['staff'])) header("Location: Login.php");
// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

$contents = get_all_category();

// 新規ボタンの処理
if(isset($_POST['newID'])) {
		$chrID = get_lastet_number(get_all_category_chrID());
		$targetCategory = new Category($chrID, "");
		unset($_POST['newID']);
}

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {
	
	// 選択されたのリストID
	$chrID = $_POST["targetID"] ;

	// そのIDでデータを取り出す
    $connection = new Connection();
    $query = "select * from category where chrID=" . $chrID . ";";
    $result = $connection->result($query) or die("SQL Error 1: " . mysql_error());
    $row = mysql_fetch_array($result, MYSQL_ASSOC);

	// 結果を保存、フォームに表示用
    $targetCategory = new Category($row['chrID'], $row['chrName']);

	// 選択を解除
    unset($_POST["target"]);
}

//　登録処理
if (isset($_POST["submit"])) {
	// フォームからデータを取り出す
	$chrID = $_POST['chrID'];
	$name = $_POST["chrName"];
	
	// データベースに挿入
	$query = <<<EOF
	INSERT INTO category(chrID, chrName) 
	VALUES ('$chrID', '$name');
EOF;
	
	$connection = new Connection();
	$connection->result($query);
	$connection->close();
	
	// 再度リストを更新
	$contents = get_all_category();
	$_POST["targetID"] = $chrID;
	$errorMessage = "登録しました。";
}

?>

<!DOCTYPE html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="../css/main.css" type="text/css"/>
	 <link rel="stylesheet" type="text/css" href="./css/blended_layout.css">
	<link rel="stylesheet" type="text/css" href="./css/search.css">
	<link rel="stylesheet" href="../css/validationEngine.jquery.css" type="text/css"/>
	<link rel="stylesheet" href="../css/template.css" type="text/css"/>
	<script src="../js/jquery-1.8.2.min.js" type="text/javascript"></script>
	<script src="../js/languages/jquery.validationEngine-ja.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script>
		jQuery(document).ready(function() {
			jQuery("#user_add_form").validationEngine();
			$("#user_add_form").bind("jqv.field.result", function(event, field, errorFound, prompText) {
				console.log(errorFound)
			})
			
	        $("#newID").click(function(){
	            $('#user_add_form').validationEngine('hideAll');
	            $('#user_add_form').validationEngine('detach');
	            return true;                
	        });
		});
	</script>
	<title>POSCO</title>
	<meta name="description" content="POSCO">
	<style>
		* {
			font-family:Verdana;
		}
		input {
			border:1px solid #000000;
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
			width:800px;
			margin:0 auto;
			clear:both;
		}
		#buttonlist {
			margin:10px 0;
		}
		p.list {
			width:700px;
			height:37px;
			color:#000000;
		}
		p.list input[type="text"], input[type="password"], select  {
			float:left;
			height:35px;
			border:1px solid #555555;
			background: #faffbd;
		}
		p.list input[type="text"]:focus, input[type="password"]:focus, select:focus  {
			background: #ffffff;
		}
		label.list {
			display:block;
			float:left; 
			margin:10px 0 5px 0; 
			height:20px; 
			width:150px;
			text-align: right;
			font-weight: bold;
			font-size: 14px;
		}
		
		input.center_button{
			float:center;
			width:90px;
			height:40px;
			margin:30px 5px 30px 5px;}
			
		a.center_button {
			float:right;
		}
			
		input.center_button, input.newID, a.center_button {			
			display: block;
			text-align: center;
			width: 80px;
			font-size: 14px;
			font-family: Verdana;
			font-weight: bold;
			-moz-border-radius: 8px;
			-webkit-border-radius: 8px;
			border-radius: 8px;
			border: 1px solid #ffaa22;
			padding: 9px 18px;
			text-decoration: none;
			background: -moz-linear-gradient( center top, #ffec64 5%, #ffab23 100%);
			background: -ms-linear-gradient( top, #ffec64 5%, #ffab23 100%);
			filter: progid : DXImageTransform.Microsoft.gradient(startColorstr='#ffec64', endColorstr='#ffab23');
			background: -webkit-gradient( linear, left top, left bottom, color-stop(5%, #ffec64), color-stop(100%, #ffab23));
			background-color: #ffec64;
			color: #333333;
			display: inline-block;
			text-shadow: 1px 1px 0px #ffee66;
			-webkit-box-shadow: inset 1px 1px 0px 0px #fff6af;
			-moz-box-shadow: inset 1px 1px 0px 0px #fff6af;
			box-shadow: inset 1px 1px 0px 0px #fff6af;
		}
		input.center_button:hover, input.newID:hover, a.center_button:hover {
			background: -moz-linear-gradient( center top, #ffab23 5%, #ffec64 100%);
			background: -ms-linear-gradient( top, #ffab23 5%, #ffec64 100%);
			filter: progid : DXImageTransform.Microsoft.gradient(startColorstr='#ffab23', endColorstr='#ffec64');
			background: -webkit-gradient( linear, left top, left bottom, color-stop(5%, #ffab23), color-stop(100%, #ffec64));
			background-color: #ffab23;
		}
		
		input.center_button:active {
			position: relative;
			top: 1px;
		}		
		.main {
			padding: 100px 0 0 0;
		}
		form.search_form {
			padding: 10px 0;
			border: 1px solid #555;
			-webkit-border-radius: 6px;
			-moz-border-radius: 6px;
			border-radius: 6px;
			-webkit-box-shadow: 0px 1px 10px #000000;
			-moz-box-shadow: 0px 1px 10px #000000;
			box-shadow: 0px 1px 10px #000000;
			border: solid #000000 1px;
			background: #fafafa;
		}
	</style>
</head>

<body>

	<div class="blended_grid">
        <div class="pageHeader">
<?php include('./html_parts/header.html');?>
        <div class="pageContent">
        	<?php include('./html_parts/top_menu.html');?>
<div class="main">
			<!-- ********************* フォームの作成 開始　**********************	-->
			<div style="clear:both;float:top;">
				<form class="search_form" style="margin:0 auto;width:700px; " method="post" id="user_add_form">
					<p class="list">
						<label class="list">大分類コード</label>
						<input class="validate[required, custom[required_2_digits],custom[onlyLetterNumber]] text-input" data-prompt-position="topLeft:140" style="width:290px;margin:0 0 0 18px;" name="chrID" type="text" size="10" value='<?php
echo $targetCategory->chrID; ?>'/>
						<input class="newID" style="width:100px;height:37px;margin:0;" type="submit" name="newID" id="newID" size="10" value="新規"/>
					</p>
					<p class="list">
						<label class="list">大分類</label><input style="width:390px;margin:0 0 0 18px;" type="text" size="10"
						class="validate[required,maxSize[20]] text-input" data-prompt-position="topLeft:140" name="chrName" value="<?php
echo $targetCategory->chrName; ?>" />
					</p>
					<p style="float:left;text-align:center;width:290px;" id="buttonlist">
						<input class="center_button" type="submit" name="submit" size="10" value="登録"/>
						<input class="center_button" type="reset" size="10" value="クリア"/>
						<input class="center_button" type="submit" name="delete" size="10" value="更新"/>
						<div style="float:left; width:400px;height:100px;margin:10px 0;text-align: center;vertical-align: middle;">
							<a class="center_button" href="./index.php" style="display:block;	text-decoration:none; 
									width:98px; height:20px;margin:30px 5px;
									background-color:#dddddd;font-size:14px; padding: 11px 0 9px 0;">戻る</a>
							<a class="center_button" href="../utils/excel_export.php" style="display:block;	text-decoration:none; 
									width:130px; height:20px;margin:30px 5px;
									background-color:#dddddd;font-size:14px; padding: 11px 0 9px 0;">EXCELへ出力</a>
							<a class="center_button" href="../utils/csv_export.php" style="display:block;	text-decoration:none; 
									width:130px; height:20px;margin:30px 5px;
									background-color:#dddddd;font-size:14px; padding: 11px 0 9px 0;">CSVへ出力</a>
						</div>
						</div>
					</p>
				</form>
			</div>
			<!-- ********************* フォームの作成 終了　**********************	-->
			
			
			<!-- ********************* リストの作成 開始　**********************	-->
			<div id="user_list"><form method="post" id="list">
<?php
$header = ["コード" => 100, "大分類" => 150, "選択" => 52, "削除"=>60];
echo '<table style="margin:0 auto; position:relative;padding:0;">';
echo '<thead><tr>';
foreach ($header as $name => $width) echo '<th width="' . $width . '">' . $name . '</th>';
echo '</tr></thead>';

echo '<tbody>';
foreach ($contents as $row) {
    echo '<tr>';
    echo '<td width="100px;">' . $row->chrID . '</td>';
    echo '<td width="150px;">' . $row->chrName . '</td>';
    echo '<td style="width:52px;text-align:center"><input type="radio" onclick="javascript: submit()" name="targetID" id="targetID" value="'. $row->chrID .'"/></td>';
    echo '<td style="width:60px;padding:0 5px;"><input class="center_button" style="width:60px; height:30px; margin:0;padding:0;" type="submit" name="'.$row->chrID.'" value="削除"/></td>';
    echo '</tr>';
}
echo '</tbody></table>';

$_SESSION["sheet"] = serialize($contents);
?>			
			<input type="submit" name="target" style="display:none"/>
			</form>		
		</div>
			<?php
echo $errorMessage ?>
				
		</div>
		<!-- ********************* リストの作成  終了　********************** -->
		<div class="pageFooter">
				<h4 style="color:#ffffff;text-align:center;padding:4px 0 0 0;">CopyRight 2015 POSCO Co.Ltd  All Rights Reserved</h4>
		</div>
	</div>
</body>
</html>