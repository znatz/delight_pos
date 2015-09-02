<?php
require_once 'helper.php';

session_start();
session_check();

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

extract($_POST);

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全商品データを一回取り出す
$contents   = Member::get_all();
$groups     = Group::get_distinct_group_chrID();
$shops      = Shop::get_distinct_shop_chrID();;

// 検索押された
if(isset($Search)){
//    $contents = Member::search_goods($chrSeller_ID, $chrMaker_ID, $chrGroup_ID, $chrClass_ID);
}

?>

<!DOCTYPE html>
<head>
    <? include('./html_parts/css_and_js.html'); ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            $('#myTable').DataTable({
                "language": {
                    "sProcessing": "処理中...",
                    "sLengthMenu": "_MENU_ 件表示",
                    "sZeroRecords": "データはありません。",
                    "sInfo": " _TOTAL_ 件中 _START_ から _END_ まで表示",
                    "sInfoEmpty": " 0 件中 0 から 0 まで表示",
                    "sInfoFiltered": "（全 _MAX_ 件より抽出）",
                    "sInfoPostFix": "",
                    "sSearch": "キーワード検索:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "先頭",
                        "sPrevious": "前",
                        "sNext": "次",
                        "sLast": "最終"
                    }
                },
                "aoColumnDefs": [
                    {"bSortable": false, "aTargets": [13, 14]}
                ]
            });


            $('#main-menu').smartmenus();
            jQuery("#user_add_form").validationEngine();

            $('#right-menu').sidr({
                name: 'sidr-right',
                side: 'right'
            });
            $('#right-menu-seller').sidr({
                name: 'sidr-right-seller',
                side: 'right'
            });

            var dateFormat = 'yy/mm/dd';
            $("#from").datepicker({dateFormat:dateFormat});
            $("#to").datepicker({dateFormat:dateFormat});


        });
    </script>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
    <style type="text/css">
        p.list {
            width: 800px;
        }
        select {
            float:left;
            width:289px;
            margin-bottom: 0;
        }
        .yen-mark {
            background-color: gray;
            background-image: linear-gradient(transparent 70%, rgba(100,100,100,.5) 50%);
            background-size: 2px 2px;
            margin:0 !important; padding-top:10px !important;
            display:block;
            height:27px !important;
            color:white;
            text-align:center !important;
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

            <!-- ********************* マスタ一覧の作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form" style="margin: 0 auto; width: 800px; height: 200px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>顧客問合わせ</legend>
                    </fieldset>

                    <p class="list">
                        <label class="list">キーワード検索</label>
                        <input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <input tabindex="5" style="margin-top:0; margin-bottom:0; width:80px; height:34px;" class="center_button hvr-fade" type="submit" name="Search" size="10" value="検索"/>
                        <a tabindex="6" style="margin-top:0; margin-bottom:0;width:50px;" class="center_button hvr-fade" href="./memberList.php" style="display: block; float:left;text-decoration: none; ; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                    </p>

                    <p class="list">
                        <label class="list">来店期間</label>
                        <input
                            id="from"
                            style="width: 100px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label class="list yen-mark" style="width:27px;">-</label>
                        <input
                            id="to"
                            style="width: 100px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label class="list" style="width:80px;">来店店舗</label>
                        <select
                            style="width:241px;height:37px;"
                            tabindex="3"
                            name="chrShop_ID" >
                            <option/>
                            <? foreach ($shops as $s) : ?>
                                <option
                                    value="<? echo $s[0] ?>"
                                    <? if ($s[0] == $chrShop_ID) echo "selected"; ?>
                                    ><? echo $s[0] . " " . $s[1]; ?></option>
                            <? endforeach; ?>
                        </select>
                    </p>

                    <p class="list">
                        <label class="list">部門</label>
                        <select
                            style="width:241px;height:37px;"
                            tabindex="3"
                            name="chrGroup_ID" >
                            <option/>
                            <? foreach ($groups as $g) : ?>
                                <option
                                    value="<? echo $g[0] ?>"
                                    <? if ($g[0] == $chrGroup_ID) echo "selected"; ?>
                                    ><? echo $g[0] . " " . $g[1]; ?></option>
                            <? endforeach; ?>
                        </select>

                        <label class="list" style="width:80px;">来店回数</label>

                        <input
                            style="width: 90px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label
                            class="list yen-mark"
                            style="width:30px">回～</label>
                        <input
                            style="width: 90px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label class="list yen-mark" style="width:18px">回</label>
                    </p>
                    <p class="list">
                        <label class="list">買上げ金額</label>
                        <input
                            style="width: 89px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label
                            class="list yen-mark"
                            style="width:30px">円～</label>
                        <input
                            style="width: 90px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label class="list yen-mark" style="width:18px">円</label>
                        <label class="list" style="width:80px;">粗利金額</label>
                        <input
                            style="width: 90px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label class="list yen-mark" style="width:30px;">円～</label>
                        <input
                            style="width: 90px; margin: 0 0 0 0px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                        <label class="list yen-mark" style="width:18px;">円</label>
                    </p>
<!--
                    <p style="padding-left:400px;"
                       id="buttonlist">
                    </p>
-->
                    <div
                        style="float: right; width: -100%; height: 100px; margin: 0px 0 0 0; text-align: center; vertical-align: middle;">

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
        <div id="user_list" style="overflow:auto !important;width:1000px;">
            <form method="post" id="list" action="" style="overflow: auto;">
                <?
                 $header = [
                     "顧客連番"    => 50,
                     "登録日"      => 50,
                     "顧客名"      => 80,
                     "カナ"        => 50,
                     "郵便番号"    => 50,
                     "住所"        => 50,
                     "番地"        => 50,
                     "地区"        => 50,
                     "電話番号"    => 50,
                     "携帯番号"    => 50,
                     "誕生日"      => 50,
                     "年齢"        => 50,
                     "性別"        => 50,
                     "分類"        => 50,
                     "DM区分"      => 50,
                     "備考１"      => 50,
                     "備考２"      => 50,
                     "備考３"      => 50,
                     "選択"        => 52,
                     "削除"        => 50
                ];

                $prop = [
                    'chrCode'           =>'center',
                    'chrRegisterDate'   =>'center',
                    'chrName'           =>'left',
                    'chrKana'           =>'left',
                    'chrPostNo'         =>'left',
                    'chrAddress'        =>'center',
                    'chrAddress2'       =>'center',
                    'chrArea_ID'        =>'center',
                    'chrTel'            =>'center',
                    'chrMobile'         =>'center',
                    'chrBirthday'       =>'center',
                    'intAge'            =>'center',
                    'chrSex_ID'         =>'left',
                    'chrMemberranking_ID'       =>'left',
                    'chrDmFlg'          =>'left',
                    'chrTemporary1'     =>'left',
                    'chrTemporary2'     =>'left',
                    'chrTemporary3'     =>'left',
                ];
                get_list($header, $contents, "chrCode", $prop, "2000px") ;

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
