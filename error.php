<?php
$status = $_SERVER['REDIRECT_STATUS'];
$codes = array(403 => array('403 Forbidden', 'サーバー拒否 z.nat.cn'), 
	           404 => array('404 Not Found', 'ページ存在しない z.nat.cn'), 
	           405 => array('405 Method Not Allowed', '間違い送信メソッド z.nat.cn'), 
	           408 => array('408 Request Timeout', 'タイムアウト z.nat.cn'), 
	           500 => array('500 Internal Server Error', 'コードに問題がある z.nat.cn'), 
	           502 => array('502 Bad Gateway', 'Bad Gateway z.nat.cn'), 
	           504 => array('504 Gateway Timeout', 'Gatewayタイムアウト z.nat.cn'),);

$title = $codes[$status][0];
$message = $codes[$status][1];
if ($title == false || strlen($status) != 3) {
    $message = 'ほかのエラーステータスの処理はerror.phpで。　z.nat.cn';
}

// Insert headers here
echo '<h1>' . $title . '</h1>
<p>' . $message . '</p>';

// Insert footer here
