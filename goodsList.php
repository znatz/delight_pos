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
$contents = Goods::get_all_goods();

// 検索押された
if(isset($Search)){
    $contents = Goods::search_goods($chrSeller_ID, $chrMaker_ID, $chrGroup_ID, $chrClass_ID);
}

$classids = Goods::get_distinct_class_chrID();
$groups = Group::get_distinct_group_chrID();
$sellers = Seller::get_all();
$makers = Maker::get_all();
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


        });
    </script>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
    <style type="text/css">
        p.list {
            width: 1000px;
        }
        select {
            float:left;
            width:289px;
            margin-bottom: 0;
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

                <form class="search_form" style="margin: 0 auto; width: 1000px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>商品マスタ一覧</legend>
                    </fieldset>
					
                    <p class="list">
                        <label class="list">仕入先</label>
                        <select
                            tabindex="1"
                            name="chrSeller_ID" >
                            <option/>
                            <? foreach ($sellers as $s) : ?>
                                <option
                                    value="<? echo $s->chrID; ?>"
                                    <? if ($s->chrID == $chrSeller_ID) echo "selected"; ?>
                                    ><? echo $s->chrID.'_'.$s->chrName; ?>
                                </option>
                            <? endforeach; ?>
                        </select>

                        <label class="list">メーカー</label>
                        <select
                            tabindex="2"
                            name="chrMaker_ID" >
                            <option/>
                            <? foreach ($makers as $m) : ?>
                                <option
                                    value="<? echo $m->chrID; ?>"
                                    <? if ($m->chrID == $chrMaker_ID) echo "selected"; ?>
                                    ><? echo $m->chrID.' '.$m->chrName; ?></option>
                            <? endforeach; ?>
                        </select>
                   </p>

                    <p class="list">
                        <label class="list">部門</label>

                        <select
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

                        <label class="list">商品区分</label>
                        <select
                            tabindex="3"
                            name="chrClass_ID" >
                            <option/>
                            <? foreach ($classids as $c) : ?>
                                <option
                                    value="<? echo $c[0]; ?>"
                                    <? if ($c == $chrClass_ID) echo "selected"; ?>
                                    ><? echo $c; ?></option>
                            <? endforeach; ?>
                        </select>
                    </p>

                    <p style="padding-left:400px;"
                       id="buttonlist">
                        <input
                            tabindex="5"
                            class="center_button hvr-fade" type="submit" name="Search"
                            size="10" value="検索"/>
                        <a
                            tabindex="6"
                            class="center_button hvr-fade" href="./goodsList.php"
                            style="display: block; float:left;text-decoration: none; ; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                    </p>

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