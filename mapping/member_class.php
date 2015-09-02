<?php

require_once 'helper.php';
require_once 'connect.php';
require_once 'ZModel.php';

class Member extends ZModel
{
    protected static $table = "member";
    protected $props = ["chrID",
        "chrCode",
        "chrName",
        "chrKana",
        "chrRegisterDate",
        "chrUpdateDate",
        "chrPostNo",
        "chrAddress",
        "chrAddress2",
        "chrArea_ID",
        "chrMemberranking_ID",
        "chrTel",
        "chrMobile",
        "chrBirthday",
        "intAge",
        "chrSex_ID",
        "chrDmFlg",
        "chrTemporary1",
        "chrTemporary2",
        "chrTemporary3",
    ];
     protected static $columns = ["chrID",
        "chrCode",
        "chrName",
        "chrKana",
        "chrRegisterDate",
        "chrUpdateDate",
        "chrPostNo",
        "chrAddress",
        "chrAddress2",
        "chrArea_ID",
        "chrMemberranking_ID",
        "chrTel",
        "chrMobile",
         "chrBirthday",
        "intAge",
        "chrSex_ID",
        "chrDmFlg",
        "chrTemporary1",
        "chrTemporary2",
        "chrTemporary3",
    ];

    function Member()
    {
        $parameters = func_get_args();
        foreach ($parameters as $p) {
            $key = array_shift($this->props);
            $this->$key = $p;
        }
    }
}
