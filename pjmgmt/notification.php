<?php
    require_once("functions.php");
    if(!isset($base)){
        render_404page();
    }
    Class Notification{
        function getNotification($uid, $hash){
            if(!($uid > 0)){return -1;}
            if(!checkCookies($uid, $hash)){return false;}

            $colsArray = array("borrow"=>array(1, 2,3,4,5,6,7), "item"=>array(1, 3, 4));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `item`, `borrow` WHERE `borrow`.`iid`=`item`.`iid` AND `item`.`uid`=$uid AND `item`.`status`=1;";
            $data = array();
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $data[$res["bid"]] = getSqlData($colsArray, $res);
                }
            }
            return $data;
        }
    }
?>