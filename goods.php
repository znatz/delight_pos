<?php
require_once 'helper.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetGoods = Goods::get_one_empty_goods();;

extract($_POST);

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全商品データを一回取り出す
$contents = Goods::get_all_goods();

// 商品区分のIDを取り出す
$classids = Goods::get_distinct_class_chrID();

// 新規ボタンの処理
if (isset($_POST['newID'])) {
    $targetGoods = Goods::get_new_goods();
    unset($_POST['newID']);
}

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {

    $targetGoods = Goods::get_one_goods($_POST["targetID"]);

    // 選択を解除
    unset($_POST["target"]);
    $contents = Goods::get_all_goods();
}

// 削除ボタン処理
if (isset($_POST['delete'])) {
        if (Goods::delete_one_goods($_POST['delete'])) {
            $contents = Goods::get_all_goods();
            $successMessage = "ユーザが削除しました。";
        } else {
            $errorMessage = "削除失敗しました。";
        }
}

// 　登録処理
if (isset($_POST["submit"])) {
    if (Goods::insert_one_goods($_POST['chrID'],
        $_POST['chrClass_ID'],
        $_POST['chrCode'],
        $_POST['chrName'],
        $_POST['chrKana'],
        $_POST['chrSeller_ID'],
        $_POST['chrMaker_ID'],
        $_POST['chrGroup_ID'],
        $_POST['chrUnit_ID'],
        $_POST['chrColor'],
        $_POST['chrSize'],
        $_POST['chrComment1'],
        $_POST['chrComment2'],
        $_POST['intCost'],
        $_POST['intPrice'])
    ) {
        $successMessage = "追加しました。";
    } else {
        // 更新処理開始
            if (Goods::update_one_goods($_POST['chrID'],
 	        $_POST['chrClass_ID'],
        	$_POST['chrCode'],
        	$_POST['chrName'],
        	$_POST['chrKana'],
        	$_POST['chrSeller_ID'],
        	$_POST['chrMaker_ID'],
        	$_POST['chrGroup_ID'],
        	$_POST['chrUnit_ID'],
        	$_POST['chrColor'],
        	$_POST['chrSize'],
        	$_POST['chrComment1'],
        	$_POST['chrComment2'],
        	$_POST['intCost'],
        	$_POST['intPrice'])
            ) {
                $successMessage = "更新しました。";
            };
    }

    // 再度リストを更新
    $contents = Goods::get_all_goods();
    $_POST["targetID"] = $chrID;
}

$groups = Group::get_distinct_group_chrID();
$sellers = Seller::get_all();
$makers = Maker::get_all();
$units = Unit::get_all();

$contents = Goods::get_all_goods();
?>

<!DOCTYPE html>
<head>
    <? include('./html_parts/css_and_js.html'); ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            $('#ref_seller').focusout(function () {
                queryParam = $(this).val();
                phpCode = 'require_once dirname(__FILE__)."/../mapping/seller_class.php";echo Seller::search_chrID_chrName_by_word('+
                    queryParam+
                ');';
                $.post('utils/show_list.php', {"phpCode":phpCode},function (data) {
                    console.log(data);
                    $("#sidr-right-seller").text(data);
                })
            });

            $("tr").dblclick(function(){
                var chrID = $(this).attr("id");
                console.log(chrID);
                $("input[name=targetID][value=" + chrID + "]").attr('checked', 'checked');
                $("#list").submit();
            });


            $('#myTable').DataTable({
                "language": {
                    "sProcessing": "処理中...",
                    "sLengthMenu": "_MENU_ 件表示",
                    "sZeroRecords": "データはありません。",
                    "sInfo": " _TOTAL_ 件中 _START_ から _END_ まで表示",
                    "sInfoEmpty": " 0 件中 0 から 0 まで表示",
                    "sInfoFiltered": "（全 _MAX_ 件より抽出）",
                    "sInfoPostFix": "",
                    "sSearch": "検索:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "先頭",
                        "sPrevious": "前",
                        "sNext": "次",
                        "sLast": "最終"
                    }
                },
                "aoColumnDefs": [
                    {"bSortable": false, "aTargets": [5, 6]}
                ]
            });


            $('#main-menu').smartmenus();
            jQuery("#user_add_form").validationEngine();
            $("#user_add_form").bind("jqv.field.result", function (event, field, errorFound, prompText) {
                console.log(errorFound)
            });

            $("#newID").click(function () {
                $('#user_add_form').validationEngine('hideAll');
                $('#user_add_form').validationEngine('detach');
                return true;
            });
            $('#right-menu').sidr({
                name: 'sidr-right',
                side: 'right'
            });
            $('#right-menu-seller').sidr({
                name: 'sidr-right-seller',
                side: 'right'
            });


        });
    </script>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
    <style type="text/css">


        input[type="text"], input[type="password"], select {
            padding: 0 5px 0 5px;
            font-size: 14px;
        }

        input[disable="disable"] {
            font-size: 14px;
            padding: 0 0 0 5px;
        }

        select {
            float: left;
            border: 1px solid #555555;
            margin: 0 0 0px 18px;
            width: 199px;
            font-size: 14px;
            background: #faffbd;
        }

        #user_list {
            clear: both;
            overflow: auto !important;
        }

        #buttonlist {
            margin: 10px 0;
        }

        p.list {
            width: 700px;
            height: 37px;
            color: #000000;
        }

        p.list input[type="text"], input[type="password"], select {
            float: left;
            height: 35px;
            border: 1px solid #555555;
            background: #faffbd;
            transition: border 0.3s;
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

    </style>
</head>
<body>
<div class="blended_grid">
    <div class="pageHeader">
        <? include('./html_parts/header.html'); ?>
    </div>
    <div class="pageContent">
        <? include('./html_parts/top_menu.html'); ?>
        <div class="main">
            <? include('./html_parts/warning.html'); ?>

            <!-- ********************* マスタの作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form" style="margin: 0 auto; width: 700px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>商品マスタ</legend>
                    </fieldset>
                    <p class="list">
                        <label class="list">コード</label>
                        <input
                            class="validate[required,onlyNumberSp,maxSize[13]] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 290px; margin: 0 0 0 18px;" name="chrID"
                            type="text" size="10" placeholder="0000000000000"
                            value='<?
                            echo $targetGoods->chrID;
                            ?>'/>
                        <input class="newID hvr-fade"
                                         style="width: 100px; height: 37px; margin: 0;" type="submit"
                                         name="newID" id="newID" size="10" value="新規"/>
                        <a class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu" href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    </p>
                    <p class="list">
                        <label class="list">商品区分</label>
                        <select name="chrClass_ID" style="float:left;height:37px;width:150px;">
                            <option/>
                            <? foreach ($classids as $c) : ?>
                                <option 
				<? if (mb_substr($c,0,1,"UTF-8") == $targetGoods->chrClass_ID) { ?>
					selected 
				<? } ?>
				value="<? echo $c ?>"><? echo $c ?></option>
                            <? endforeach; ?>
                        </select>
                    </p>

                    <p class="list">
                        <label class="list">品番</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[30]] text-input"
                            data-prompt-position="topLeft:140" name="chrCode"
                            value="<?
                            echo $targetGoods->chrCode;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">商品名</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?
                            echo $targetGoods->chrName;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">商品名カナ</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrKana"
                            value="<?
                            echo $targetGoods->chrKana;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">仕入先</label>


                        <select
                            style="width:402px;"
                            tabindex="1"
                            name="chrSeller_ID" >
                            <option/>
                            <? foreach ($sellers as $s) : ?>
                                <option
                                    value="<? echo $s->chrID; ?>"
                                    <? if ($s->chrID == $targetGoods->chrSeller_ID) echo "selected"; ?>
                                    ><? echo $s->chrID.'_'.$s->chrName; ?>
                                </option>
                            <? endforeach; ?>
                        </select>


                        <a class="center_button hvr-fade" style="margin:0 auto;float:right;width:80px;" id="right-menu-seller" href="#sidr">仕入れリスト</a>
                    </p>
                    <p class="list">
                        <label class="list">メーカー</label>
                        <select
                            style="width:402px;"
                            tabindex="2"
                            name="chrMaker_ID" >
                            <option/>
                            <? foreach ($makers as $m) : ?>
                                <option
                                    value="<? echo $m->chrID; ?>"
                                    <? if ($m->chrID == $targetGoods->chrMaker_ID) echo "selected"; ?>
                                    ><? echo $m->chrID.' '.$m->chrName; ?></option>
                            <? endforeach; ?>
                        </select>

                    </p>
                    <p class="list">
                        <label class="list">部門</label>

                        <select
                            tabindex="3"
                            name="chrGroup_ID" style="width:402px;">
                            <option/>
                            <? foreach ($groups as $g) : ?>
                                <option
                                    value="<? echo $g[0] ?>"
                                    <? if ($g[0] == $targetGoods->chrGroup_ID) echo "selected"; ?>
                                    ><? echo $g[0] . " " . $g[1]; ?></option>
                            <? endforeach; ?>
                        </select>


                    </p>
                    <p class="list">
                        <label class="list">品種</label>
                        <select
                            style="width:402px;"
                            tabindex="2"
                            name="chrUnit_ID" >
                            <option/>
                            <? foreach ($units as $u) : ?>
                                <option
                                    value="<? echo $u->chrID; ?>"
                                    <? if ($u->chrID == $targetGoods->chrUnit_ID) echo "selected"; ?>
                                    ><? echo $u->chrID.' '.$u->chrName; ?></option>
                            <? endforeach; ?>
                        </select>
                   </p>
                    <p class="list">
                        <label class="list">カラー</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrColor"
                            value="<?
                            echo $targetGoods->chrColor;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">サイズ・規格</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrSize"
                            value="<?
                            echo $targetGoods->chrSize;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">備考1</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrComment1"
                            value="<?
                            echo $targetGoods->chrComment1;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">備考2</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[50]] text-input"
                            data-prompt-position="topLeft:140" name="chrComment2"
                            value="<?
                            echo $targetGoods->chrComment2;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">原価</label> <input
                            style="width: 150px; margin: 0 0 0 18px;text-align:right;" type="text" size="10"
                            class="validate[required,onlyNumberSp,maxSize[7]] text-input"
                            data-prompt-position="topLeft:140" name="intCost"
                            value="<?
                            echo $targetGoods->intCost;
                            ?>"/>
                    </p>
                    <p class="list">
                        <label class="list">売価</label> <input
                            style="width: 150px; margin: 0 0 0 18px;text-align:right;" type="text" size="10"
                            class="validate[required,onlyNumberSp,maxSize[7]] text-input"
                            data-prompt-position="topLeft:140" name="intPrice"
                            value="<?
                            echo $targetGoods->intPrice;
                            ?>"/>
                    </p>
                    <p style="float: left; text-align: center; width: 300px;"
                       id="buttonlist">
                        <input class="center_button hvr-fade" type="submit" name="submit"
                               size="10" value="登録"/>
                        <a
                            tabindex="6"
                            class="center_button hvr-fade" href="./goods.php"
                            style="display: block; float:left;text-decoration: none; ; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                        <a class="center_button hvr-fade" href="./index.php"
                                                                      style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
                    </p>

                    <div
                        style="float: right; width: -100%; height: 100px; margin: 10px 0; text-align: center; vertical-align: middle;">

                        <a class="center_button hvr-fade" href="../utils/excel_export.php"
                           style="display: block; text-decoration: none; width: 150px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">EXCELへ出力&nbsp;<i
                                class="fa fa-file-text-o"></i>&nbsp;</a>
                        <a class="center_button hvr-fade" href="../utils/csv_export.php"
                           style="display: block; text-decoration: none; width: 130px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">CSVへ出力&nbsp;<i
                                class="fa fa-file-text-o"></i>&nbsp;</a>
                    </div>
                </form>
            </div>
            <form action=""></form>
        </div>
        <!-- ********************* マスタの作成 終了　**********************	-->


        <!-- ********************* リストの作成 開始　**********************	-->
        <div id="user_list" style="overflow:auto !important;margin:0 0 0 70px !important;width:1000px !important;">
            <form method="post" id="list" action="">
                <?
                $header = [
                    "コード" => 50,
                    "商品区分" => 100,
                    "品番" => 80,
                    "商品名" => 550,
                    "商品名カナ" => 350,
                    "仕入先" => 50,
                    "メーカー" => 50,
                    "部門" => 50,
                    "品種" => 50,
                    "カラー" => 50,
                    "サイズ" => 50,
                    "備考1" => 50,
                    "備考2" => 50,
                    "原価" => 50,
                    "売価" => 50,
                    "選択" => 52,
                    "削除" => 70
                ];

                $prop = [
                    'chrID'         =>'center',
                    'chrClass_ID'   =>'center',
                    'chrCode'       =>'left',
                    'chrName'       =>'left',
                    'chrKana'       =>'left',
                    'chrSeller_ID'  =>'center',
                    'chrMaker_ID'   =>'center',
                    'chrGroup_ID'   =>'center',
                    'chrUnit_ID'    =>'center',
                    'chrColor'      =>'left',
                    'chrSize'       =>'left',
                    'chrComment1'   =>'left',
                    'chrComment2'   =>'left',
                    'intCost'       =>'right',
                    'intPrice'      =>'right',
                ];
                get_list($header, $contents, "chrID", $prop, "1500px") ;

                ?>
                <input type="submit" name="target" style="display: none"/>
            </form>
        </div>
    </div>
    <!-- ********************* リストの作成  終了　********************** -->
    <? include('./html_parts/footer.html'); ?>
</div>
</div>
<!-- ********************  入力規則　開始      *********************** -->
<div id="sidr-right">
    <?
    $connection = new Connection();
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="goods";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<div id="sidr-right-seller">
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>