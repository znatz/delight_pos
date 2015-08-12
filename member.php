<?php
require_once 'helper.php';

session_start();
session_check();
global $errorMessage;
global $successMessage;
$errorMessage = "";
$successMessage = "";
$targetMember = new Category();

// ログイン状態チェック
if (!isset($_SESSION['staff']))
    header("Location: Login.php");

// 今ログインしている担当をセッションから取り出す、オブジェクトに一旦保存
$staff = unserialize($_SESSION["staff"]);

// リストを表示ため、全担当データを一回取り出す
$contents = Member::get_all();
$areas    = Area::get_all();
$memberrankings = Memberranking::get_all();

extract($_POST);


$targetMember = new Member;
if (isset($SearchPost)) {
    $chrBirthday     = year_month_day_mix($chrBirthday_year, $chrBirthday_month, $chrBirthday_day);
    $chrRegisterDate = year_month_day_mix($chrRegisterDate_year, $chrRegisterDate_month, $chrRegisterDate_day);
    $targetMember = new Member(
            $chrID,
            $chrCode,
            $chrName,
            $chrKana,
            $chrRegisterDate,
            $chrUpdateDate,
            $chrPostNo,
            $chrAddress,
            $chrAddress2,
            $chrArea_ID,
            $chrMemberranking_ID,
            $chrTel,
            $chrMobile,
            $chrBirthday,
            $intAge,
            $chrSex_ID,
            $chrDmFlg,
            $chrTemporary1,
            $chrTemporary2,
            $chrTemporary3
    );
    $targetMember->chrAddress = showCode($chrPostNo);
}

// リスト内ラジオボタンの処理
if (isset($_POST["targetID"])) {
    $targetMember = Member::findBy("chrCode",$targetID);
    unset($_POST["target"]);
    $contents = Member::get_all();
}

// 削除ボタン処理
if (isset($_POST['delete'])) {
    if (Member::delete($_POST['delete'])) {
        $contents = Member::get_all();
        $successMessage = "削除しました。";
    } else {
        $errorMessage = "削除失敗しました。";
    }
}

// 　登録処理
if (isset($_POST["submit"])) {
    $chrBirthday     = year_month_day_mix($chrBirthday_year, $chrBirthday_month, $chrBirthday_day);
    $chrRegisterDate = year_month_day_mix($chrRegisterDate_year, $chrRegisterDate_month, $chrRegisterDate_day);
    if (Member::insert_to_columns([
            $chrID,
            $chrCode,
            $chrName,
            $chrKana,
            $chrRegisterDate,
            $chrUpdateDate,
            $chrPostNo,
            $chrAddress,
            $chrAddress2,
            $chrArea_ID,
            $chrMemberranking_ID,
            $chrTel,
            $chrMobile,
            $chrBirthday,
            $intAge,
            $chrSex_ID,
            $chrDmFlg,
            $chrTemporary1,
            $chrTemporary2,
            $chrTemporary3]
        )
    ) {
        $successMessage = "追加しました。";
    } else {
        if (Member::update_to_columns_by_column(
            $chrID,
            $chrCode,
            $chrName,
            $chrKana,
            $chrRegisterDate,
            $chrUpdateDate,
            $chrPostNo,
            $chrAddress,
            $chrAddress2,
            $chrArea_ID,
            $chrMemberranking_ID,
            $chrTel,
            $chrMobile,
            $chrBirthday,
            $intAge,
            $chrSex_ID,
            $chrDmFlg,
            $chrTemporary1,
            $chrTemporary2,
            $chrTemporary3,
            "chrCode"
        ))
        {
            $successMessage = "更新しました。";
        };
    }

    // 再度リストを更新
    $contents = Member::get_all();
    $_POST["targetID"] = $chrID;
}

$contents = Member::get_all();
?>

<!DOCTYPE html>
<head>
    <?php include('./html_parts/css_and_js.html'); ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

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
                    {"bSortable": false, "aTargets": [2, 3]}
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

            /* Search Post Code */
            $("#SearchPost").click(function () {
                $('#user_add_form').validationEngine('hideAll');
                $('#user_add_form').validationEngine('detach');
                return true;
            });

        });
    </script>
    <style type="text/css">

        select {
            border: 1px solid #555555;
            margin: 0 0 0px 18px;
            width: 199px;
            font-size: 14px;
            background: #faffbd;
        }

    </style>
    <title>POSCO</title>
    <meta name="description" content="POSCO">
</head>
<body>
<div class="blended_grid">
    <div class="pageHeader">
        <?php include('./html_parts/header.html'); ?>
    </div>
    <div class="pageContent">
        <?php include('./html_parts/top_menu.html'); ?>
        <div class="main">
            <?php include('./html_parts/warning.html'); ?>

            <!-- ********************* マスタの作成 開始　**********************	-->
            <div style="clear: both; float: top;">

                <form class="search_form" style="margin: 0 auto; width: 700px; height: 820px;"
                      method="post" id="user_add_form" action="">
                    <fieldset>
                        <legend>顧客登録</legend>
                    </fieldset>
                    <a class="center_button hvr-fade" style="margin:0 auto;float:right;width:10px;" id="right-menu" href="#sidr">入力説明表示／非表示 <i class="fa fa-info-circle"></i></a>
                    <p class="list" style="margin:50px 0 0 0;">
                        <label class="list">顧客連番</label>
                        <input
                            class="chrCode validate[custom[integer],custom[required_2_digits] text-input"
                            data-prompt-position="topLeft:140"
                            style="width: 390px; margin: 0 0 0 18px;" name="chrCode"
                            type="text" size="10" placeholder="00"
                            value='<?php
                            echo $targetMember->chrCode;
                            ?>'/>
                    </p>

                    <p class="list">
                        <label class="list">登録日</label>
                        <input
                            style="width: 100px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrRegisterDate_year"
                            value="<?php
                            echo year_month_day_separate($targetMember->chrRegisterDate)[0];
                            ?>"/>
                        <label class="list" style="width:20px;">年</label>
                        <input
                            style="width: 55px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrRegisterDate_month"
                            value="<?php
                            echo year_month_day_separate($targetMember->chrRegisterDate)[1];
                            ?>"/>
                        <label class="list" style="width:20px;">月</label>
                        <input
                            style="width: 55px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrRegisterDate_day"
                            value="<?php
                            echo year_month_day_separate($targetMember->chrRegisterDate)[2];
                            ?>"/>
                        <label class="list" style="width:20px;">日</label>
                    </p>

                    <p class="list">
                        <label class="list">顧客名</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrName"
                            value="<?php
                            echo $targetMember->chrName;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">カナ</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrKana"
                            value="<?php
                            echo $targetMember->chrKana;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">郵便番号</label><input
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrPostNo"
                            value="<?php
                            echo $targetMember->chrPostNo;
                            ?>"/>
                        <input class="newID hvr-fade"
                               style="width: 100px; height: 37px; margin: 0;" type="submit"
                               name="SearchPost" id="SearchPost" size="10" value="検索"/>
                    </p>

                    <p class="list">
                        <label class="list">住所</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrAddress"
                            value="<?php
                            echo $targetMember->chrAddress;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">番地</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrAddress2"
                            value="<?php
                            echo $targetMember->chrAddress2;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">地区</label>
                        <select name="chrArea_ID"
                                class="validate[required]"
                                tabindex="5"
                                style="float:left;height:37px;width:207px;">
                            <option/>
                            <?php foreach ($areas as $a) : ?>
                                <option <? if ($a->chrID == $targetMember->chrArea_ID) echo "selected"; ?>
                                    value="<? echo $a->chrID ?>"><?php echo $a->chrID . "  " . $a->chrName; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p class="list">
                        <label class="list">電話番号</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrTel"
                            value="<?php
                            echo $targetMember->chrTel;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">携帯番号</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrMobile"
                            value="<?php
                            echo $targetMember->chrMobile;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">誕生日</label>
                        <input
                            style="width: 100px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrBirthday_year"
                            value="<?php
                            echo year_month_day_separate($targetMember->chrBirthday)[0];
                            ?>"/>
                        <label class="list" style="width:20px;">年</label>
                        <input
                            style="width: 55px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrBirthday_month"
                            value="<?php
                            echo year_month_day_separate($targetMember->chrBirthday)[1];
                            ?>"/>
                        <label class="list" style="width:20px;">月</label>
                        <input
                            style="width: 55px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrBirthday_day"
                            value="<?php
                            echo year_month_day_separate($targetMember->chrBirthday)[2];
                            ?>"/>
                        <label class="list" style="width:20px;">日</label>
                    </p>

                    <p class="list">
                        <label class="list">年齢</label>
                        <input
                            readonly
                            style="width: 200px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="intAge"
                            value="<?
                            $ymd = year_month_day_separate($targetMember->chrBirthday);
                            echo birthday_calculator($ymd[0], $ymd[1], $ymd[2]);
                            ?>"/>
                        <label class="list" style="width:20px;">歳</label>
                    </p>

                    <p class="list">
                        <label class="list">性別</label>
                        <select name="chrSex_ID"
                                class="validate[required]"
                                tabindex="5"
                                style="float:left;height:37px;width:207px;">
                            <option/>
                            <?php foreach (["男","女"] as $k=>$r) : ?>
                                <option <? if ($k == $targetMember->chrSex_ID) echo "selected"; ?>
                                    value="<? echo $k; ?>"><? echo $r; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p class="list">
                        <label class="list">分類</label>
                        <select name="chrMemberranking_ID"
                                class="validate[required]"
                                tabindex="5"
                                style="float:left;height:37px;width:207px;">
                            <option/>
                            <?php foreach ($memberrankings as $r) : ?>
                                <option <? if ($r->chrID == $targetMember->chrMemberranking_ID) echo "selected"; ?>
                                    value="<? echo $r->chrID ?>"><?php echo $r->chrID . "  " . $r->chrName; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p class="list">
                        <label class="list">DM区分</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrDmFlg"
                            value="<?php
                            echo $targetMember->chrDmFlg;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">備考１</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrTemporary1"
                            value="<?php
                            echo $targetMember->chrTemporary1;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">備考２</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrTemporary2"
                            value="<?php
                            echo $targetMember->chrTemporary2;
                            ?>"/>
                    </p>

                    <p class="list">
                        <label class="list">備考３</label><input
                            style="width: 390px; margin: 0 0 0 18px;" type="text" size="10"
                            class="validate[required,maxSize[20]] text-input"
                            data-prompt-position="topLeft:140" name="chrTemporary3"
                            value="<?php
                            echo $targetMember->chrTemporary3;
                            ?>"/>
                    </p>

                    <p style="float: left; text-align: center; width: 300px;margin-top: 80px;"
                       id="buttonlist">
                        <input class="center_button hvr-fade" type="submit" name="submit"
                               size="10" value="登録"/>
                        <a class="center_button hvr-fade" href="./member.php" style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">クリア</a>
                        <a class="center_button hvr-fade" href="./index.php" style="display: block; float:left;text-decoration: none; width: 88px; margin: 30px 5px; font-size: 14px; padding: 12px 0 12px 0;">戻る</a>
                    </p>

                    <div
                        style="float: right; width: -100%; height: 100px; margin: 80px 0 0 0; text-align: center; vertical-align: middle;">

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
    <?php
    $connection = new Connection();
    $query = 'SELECT txtInstruction FROM form_helper WHERE chrPageName="member";';
    $result = $connection->result($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo $row['txtInstruction'];
    ?>
</div>
<!-- ********************  入力規則　終了      *********************** -->
</body>
</html>
