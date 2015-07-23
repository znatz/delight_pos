<?php

require_once dirname(__FILE__) . '/../utils/helper.php';
require_once dirname(__FILE__) . '/../utils/connect.php';

class Invoice_id_removed
{
    public $chrShop_ID;
    public $intLine_Count;
    public $chrComment;

    function Invoice_id_removed($shopid = "", $count = "", $comment = "")
    {
        $this->chrShop_ID = $shopid;
        $this->intLine_Count = $count;
        $this->chrComment = $comment;
    }
}
