<?php
    require_once($base."functions.php");
    if(!isset($base)){render_404page();}

    function getUid(){
        if(!isset($_POST["uid"]) || !$_POST["uid"]){add_return_data(0, 2, "Uid is required.");}
        return $_POST["uid"];
    }
    function getCookieHash(){
        if(!isset($_POST["hash"]) || !$_POST["hash"]){add_return_data(0, 3, "Hash is required.");}
        return $_POST["hash"];
    }
    function checkCookieHash($uid, $hash){
        if(!checkCookies($uid, $hash)){add_return_data(0, 4, "Uid, Hash Combination didn't match.");}
    }
?>