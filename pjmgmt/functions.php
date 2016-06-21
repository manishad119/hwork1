<?php
    function render_404page(){
        add_return_data(0, 1, "Page Not Found");
    }
    if(!isset($base)){render_404page();}
    require_once($base."connect.ini.php");
    require_once($base."item.php");
    require_once($base."items.php");
    require_once($base."notification.php");
    require_once($base."users.php");
    require_once($base."comment.php");
    require_once($base."borrow.php");
    require_once($base."loginCheck.php");
    require_once($base."rating.php");
    require_once($base."profileImage.php");
    require_once($base."message.php");
    require_once($base."user.php");

    function invalid_data($data){
        echo("Invalid ".$data);
        die("");
    }
    function getCookies($uname){
        $secret = "This is cookie secret. ";
        $secret = $secret.$uname;
        return hash("sha1", $secret);
    }
    function checkCookies($uname, $cookiehash){
        $ha = getCookies($uname);
        if($ha == $cookiehash){
            return true;
        }
        return false;
    }
    $returndata = array();
    function add_return_data($success, $msgid, $msg){
        global $returndata;
        $returndata["success"] = $success;
        $returndata["msgid"] = $msgid;
        $returndata["msg"] = $msg;
        echo json_encode($returndata, JSON_UNESCAPED_UNICODE);
        die("");
    }
    $colNames = array(
        "address" => array(1=>"aid", 2=>"uid", 3=>"longitude", 4=>"latitude", 5=>"address"),
        "borrow" => array(1=>"bid", 2=>"uid", 3=>"iid", 4=>"message", 5=>"isAccepted", 6=>"isReturned", 7=>"borrowedCreated"),
        "comment" => array(1=>"cid", 2=>"iid", 3=>"uid", 4=>"commentCreated", 5=>"comment"),
        "item" => array(1=>"iid", 2=>"uid", 3=>"itemName", 4=>"itemImageSrc", 5=>"money", 6=>"period", 7=>"expressType", 8=>"aid", 9=>"itemIntroduction", 10=>"itemCreated", 11=>"status"),
        "rating" => array(1=>"rid", 2=>"iid", 3=>"uid", 4=>"rating"),
        "replycomment" => array(1=>"rcid", 2=>"iid", 3=>"uid", 4=>"cid", 5=>"replyCommentCreated", 6=>"reply"),
        "users" => array(1=>"uid", 2=>"uname", 3=>"passhash", 4=>"fname", 5=>"gender", 6=>"pic", 7=>"email", 8=>"contact", 9=>"role", 10=>"userCreated"),
        "totalrating" => array(1=>"iid", 2=>"rating"),
    );
    function getSqlColumns($arr){
        global $colNames;
        $st = "";
        foreach ($arr as $table=>$col){
            foreach ($col as $id){
                $st1 = "`".$table."`.`".$colNames[$table][$id]."`";
                $st = insertComma($st, $st1);
            }
        }
        return $st;

    }
    function insertComma($st, $st1){
        if(!$st){return $st1;}
        if(!$st1){return $st;}
        return $st.", ".$st1;
    }
    function getSqlData($arr, $data){
        global $colNames;
        $newData = array();
        foreach ($arr as $table=>$col){
            foreach ($col as $id){
                $val = $colNames[$table][$id];
                if($val == "pic"){$newData[$val] = getUserPic($data[$val]);}
                else if($val == "itemImageSrc"){$newData[$val] = getItemPic($data[$val]);}
                else{$newData[$val] = $data[$val];}
            }
        }
        return $newData;
    }
    function getUserPic($picId){
        return "/pjmgmt/pics/profilePic/".$picId;
    }
    function getItemPic($itemId){
        return "/pjmgmt/pics/itemPic/".$itemId;
    }
?>