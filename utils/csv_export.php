<?php
session_start();
ob_start();

header('Content-Type: text/plain;charset=UTF-8');
// unserialized　ため必要
require_once dirname(__FILE__). '/../Classes/PHPExcel.php';
require_once "menu_class.php";
require_once "staff_class.php";
require_once "group_class.php";
require_once "unit_class.php";
require_once "receipt_top_class.php";
require_once "receipt_bottom_class.php";

require_once "category_class.php";
require_once "goods_class.php";
require_once "invoice_class.php";
require_once "maker_class.php";
require_once "priceband_class.php";
require_once "salesgoal_class.php";
require_once "seller_class.php";
require_once "shop_class.php";
require_once "telop_class.php";
require_once 'receipt_top_id_removed.php';
require_once 'receipt_bottom_id_removed.php';
require_once 'invoice_id_removed.php';


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
