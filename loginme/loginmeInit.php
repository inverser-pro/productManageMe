<?php

if(count(get_included_files()) ==1) {
    header("HTTP/1.1 403 Forbidden");
    header("Location: /");
    exit;
}

define("L_DEBUG",1);
define("L_ROOT",dirname(__DIR__));
define("L_APP",L_ROOT.'/loginme');
define("L_DB_USER",'test_user');
define("L_DB_PASS",'GGwYILMN456tydrghrthdrhdg');
define("L_DB_NAME",'test_name');


if(L_DEBUG===1){
    ini_set('display_errors',1);
}

require_once L_APP.'/loginme.php';
