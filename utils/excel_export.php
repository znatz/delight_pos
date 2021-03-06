<?php
session_start();


// unserialized　ため必要
require_once dirname(__FILE__). '/../Classes/PHPExcel.php';

function my_autoload($class_name) {
    if(strpos($class_name, "Excel") == false ) require_once strtolower($class_name).'_class.php';
}
spl_autoload_register("my_autoload");
// シートのデーターを取り出す
$instances = unserialize($_SESSION["sheet"]);
$header = $_SESSION["sheet_header"];

$filename = "";
$instances = unserialize($_SESSION["sheet"]);
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
    case "Area":
        $filename = "地区マスタ";
        break;
    case "Memberranking":
        $filename = "分類マスタ";
        break;
}

/* ログ */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');


// PHPExcel object生成
$objPHPExcel = new PHPExcel();

// Excelファイルの属性設定
$objPHPExcel -> getProperties() -> setCreator("POSCO") -> setLastModifiedBy("POSCO") -> setTitle("タイトル") -> setSubject("サブジェクト") -> setDescription("Delight POSより出力") -> setKeywords("office 2007 openxml php") -> setCategory("カタログ");

// 行位置
$i = 1;
// 列位置
$alphabet = range('A', 'T');

$header_count = count($header);

foreach($header as $h) {

		//　値を挿入
		$objPHPExcel -> setActiveSheetIndex(0)
					-> setCellValue(array_shift($alphabet).$i, $h);
}


// 行位置
$i = 2;

foreach($instances as $in_row) {

    $c = 0;

	foreach(get_object_vars($in_row) as $prop) {

        if($c >= $header_count) break;
		//　値を挿入
		$objPHPExcel -> setActiveSheetIndex(0)
                    -> setCellValueExplicitByColumnAndRow($c, $i, $prop, PHPExcel_Cell_DataType::TYPE_STRING);
        $c++;
	}

	$i += 1;
}





// worksheet設定
$objPHPExcel -> getActiveSheet() -> setTitle('シート名前');

// 最初開くシート
$objPHPExcel -> setActiveSheetIndex(0);

// Redirect
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='.$filename.'.xls');
header('Cache-Control: max-age=0');

// IE9の交換性
header('Cache-Control: max-age=1');

// IEとSSL交換性
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');


header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: cache, must-revalidate');
// HTTP/1.1
header('Pragma: public');
// HTTP/1.0

// 書きだす
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter -> save('php://output');
exit ;
