<?php
    require_once("functions.php");
    if(!isset($base)){
        render_404page();
    }
    Class Items{
        var $items = array();
        function getByUserId($uid){
            $items = array();
            $colsArray = array("item"=>array(1, 3, 4, 5), "address"=>array(3, 4, 5));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `item`, `address` WHERE `item`.`uid` = $uid AND `item`.`aid` = `address`.`aid`;";
            //echo $query;
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $item = getSqlData($colsArray, $res);
                    $items[] = $item;
                }
            }
            return $items;
        }
        function getByItemsLocation($longitude, $latitude, $diff=2){
            $items = array();
            $lowLon = $longitude - $diff;
            $higLon = $longitude + $diff;
            $lowLat = $latitude - $diff;
            $higLat = $latitude + $diff;
            if($lowLat < -90){$temp = $lowLat;$lowLat = $higLat;$higLat = $temp+180;}
            if($lowLon < -180){$temp = $lowLon;$lowLon = $higLon;$higLon = $temp+360;}
            if($higLat > 90){$temp = $higLat;$higLat = $lowLat;$lowLat = $temp-180;}
            if($higLon > 180){$temp = $higLon;$higLon = $lowLon;$lowLon = $temp-360;}

            $colsArray = array("item"=>array(1, 2, 3, 4, 5, 11), "address"=>array(3, 4, 5));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `item`, `address` WHERE (`address`.`latitude` > $lowLat AND `address`.`latitude` < $higLat) AND (`address`.`longitude` > $lowLon AND `address`.`longitude` < $higLon) AND `item`.`aid` = `address`.`aid`;";
            //echo $query;
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $item = getSqlData($colsArray, $res);
                    $items[] = $item;
                }
            }
            return $items;
        }
        function getItemsBySearch($q){
            $q = mysql_real_escape_string($q);
            $colsArray = array("item"=>array(1, 2, 3, 4, 5, 11), "address"=>array(3, 4, 5));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `item`, `address` WHERE `item`.`aid`=`address`.`aid` AND ((`item`.`itemName` LIKE '%$q%') OR (`item`.`itemIntroduction` LIKE '%$q%') OR (`address`.`address` LIKE '%$q%'));";
            mysql_query("SET NAMES 'utf8'");
            $items = array();
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $item = getSqlData($colsArray, $res);
                    $items[] = $item;
                }
            }
            return $items;
        }
        function getItemById($itemId){
            $item = array();
            $iid = $itemId;
            $colsArray = array("item"=>array(1, 3, 4, 5, 6, 7, 9, 10, 11), "address"=>array(3, 4, 5));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `item`, `address` WHERE `item`.`iid`=$iid AND `item`.`aid`=`address`.`aid`;";
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $item = getSqlData($colsArray, $res);
                }
            }
            $rate = new Rating();
            $item["rating"] = $rate->getRating($itemId);
            $comments = $this->getComments($itemId);
            $replies = $this->getReply($itemId);
            foreach ($comments as $cid=>$allData){
                if(!isset($comments[$cid]["replies"])){$comments[$cid]["replies"] = array();}
                if(isset($replies[$cid])){
                    $comments[$cid]["replies"] = $replies[$cid];
                }
            }
            $item["comments"] = $comments;
            return $item;
        }
        function getComments($iid){
            //
            $colsArray = array("comment"=>array(1, 3, 4, 5), "users"=>array(1, 4, 6));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `comment`, `users` WHERE `comment`.`iid`=$iid AND `comment`.`uid`=`users`.`uid`;";
            if($result = mysql_query($query)){
                $comments = array();
                while($res = mysql_fetch_array($result)){
                    if($res["cid"]){
                        $comments[$res["cid"]] = getSqlData($colsArray, $res);
                    }
                }
                return $comments;
            }
            return false;
        }
        function getReply($iid){
            //
            $colsArray = array("replycomment"=>array(1, 3, 4, 5, 6));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `replycomment` WHERE `iid`=$iid;";
            if($result = mysql_query($query)){
                $comments = array();
                while($res = mysql_fetch_array($result)){
                    if(!isset($comments[$res["cid"]])){
                        $comments[$res["cid"]] = array();
                    }
                    $comments[$res["cid"]][$res["rcid"]] = getSqlData($colsArray, $res);
                }
                return $comments;
            }
        }
        function getUserId($itemId){
            //
            if(!($itemId > 0)){return -1;}
            $query = "SELECT `item`.`uid` FROM `item` WHERE `iid`=$itemId;";
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    return $res["uid"];
                }
            }
            return -1;
        }
        function getUserInformation($itemId){
            if(!($itemId > 0)){return -1;}
            $data = array();
            $query = "SELECT `users`.`uid`, `users`.`fname`, `users`.`pic` FROM `item`, `users` WHERE `item`.`iid`=$itemId AND `item`.`uid` = `users`.`uid`;";
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $data["pic"] = getUserPic($res["pic"]);
                    $data["userId"] = $res["uid"];
                    $data["fname"] = $res["fname"];
                    return $data;
                }
            }
            return $data;
        }
    }
?>