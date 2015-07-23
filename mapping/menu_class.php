<?php

require_once dirname(__FILE__).'/../utils/connect.php';
class Menu {
	public $menuno;
	public $menuname;
	public $listno;
	public $listname;
	public $link;
	public $authority;

	function Menu($menu, $list, $link) {
		$this -> menuname = $menu;
		$this -> listname = $list;
		$this -> link = $link;
	}

    public static function get_menu_from_authority($auth){

        $connection = new Connection();

        $query = "SELECT * FROM menu WHERE intAuthority<='".$auth."' order by intMenu_No, intList_No;";
        $result = $connection->result($query) or die("SQL Error 1: " . mysql_error());

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pairs[] = new Menu($row['chrMenu_Name'], $row['chrList_Name'], $row['chrLink']);
        }

        // 問合せ結果よりリスト充填
        $titles = ["売上管理", "売上明細", "仕入・移動管理", "商品管理", "在庫管理", "顧客管理", "マスタ管理", "そのほか管理"];
        foreach ($titles as $title) {
            $inner = "";
            $href = "";
            foreach ($pairs as $menu) {
                if ($menu -> menuname == $title) {
                    $inner[] = $menu -> listname;
                    $href[]  = $menu -> link;
                }
            }
            $top_titles[$title] = $inner;
            $all_hrefs[$title] = $href;
        }

        // DB接続切断
        $connection->close();
        $tmp = array($top_titles, $all_hrefs);

        return $tmp;
    }

}
