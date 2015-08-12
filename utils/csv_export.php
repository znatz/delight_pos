<?php
session_start();
ob_start();

header('Content-Type: text/plain;charset=UTF-8');
// unserialized　ため必要
require_once dirname(__FILE__). '/../Classes/PHPExcel.php';

function my_autoload($class_name) {
    if(strpos($class_name, "Excel") == false ) require_once strtolower($class_name).'_class.php';
}
spl_autoload_register("my_autoload");

$filename = "";
$instances = unserialize($_SESSION["sheet"]);
$header = $_SESSION["sheet_header"];

switch(get_class($instances[0])) {
    case "Unit":
        $filename = "品種マスタ";
        break;
    case "Staff":
        $filename = "担当マスタ";
        break;
    case "Group":
        $filename = "部門マスタ";
        break;
    case "Receipt_top_id_removed":
        $filename = "レシート上部マスタ";
        break;
    case "Receipt_bottom_id_removed":
        $filename = "レシート下部プマスタ";
        break;
    case "Category":
        $filename = "大分類マスタ";
        break;
    case "Goods":
        $filename = "商品マスタ";
        break;
    case "Invoice_id_removed":
        $filename = "レシートマスタ";
        break;
    case "Maker":
        $filename = "メーカーマスタ";
        break;
    case "Priceband":
        $filename = "価格帯マスタ";
        break;
    case "Salesgoal":
        $filename = "目標設定マスタ";
        break;
    case "Seller":
        $filename = "仕入先マスタ";
        break;
    case "Shop":
        $filename = "店舗マスタ";
        break;
    case "Telop":
        $filename = "テロップマスタ";
        break;
    case "Memberranking":
        $filename = "分類マスタ";
        break;
}

ob_start();
header('Content-Type: text/plain;charset=UTF-8');
$stream = fopen('php://output', 'w');


fputcsv($stream, $header);

$header_count = count($header);

foreach($instances as $instance) {

    $c = 0;
	foreach(get_object_vars($instance) as $prop) {
        if($c >= $header_count) break;
		$row[] = $prop;
        $c ++;
	}
	fputcsv($stream, $row);
	$row = "";
}

// more complex codes would be here. It may cause error.

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename.".csv");
echo mb_convert_encoding(ob_get_clean(), 'SJIS', 'UTF-8');

exit();
