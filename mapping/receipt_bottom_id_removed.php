<?php
class Receipt_bottom_id_removed
{
    public $chrShop_ID;
    public $intLine_Count;
    public $chrComment;
    function Receipt_bottom_id_removed($shopid = "", $count = "", $comment = "")
    {
        $this->chrShop_ID = $shopid;
        $this->intLine_Count = $count;
        $this->chrComment = $comment;
    }

}