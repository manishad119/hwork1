<?php
    Class Useres{
        function getUserById($uid, $hash, $userId){
            if(!($uid > 0)){return -1;}
            if(!($userId > 0)){return -1;}
            if(!checkCookies($uid, $hash)){return -1;}

            $colsArray = array("users"=>array(1,2,4,5,6,7,8));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `users` WHERE `uid`=$userId;";
            $data = array();
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $data["user"] = getSqlData($colsArray, $res);
                }
            }

            $colsArray = array("item"=>array(1, 3, 4, 11));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `item` WHERE `item`.`uid`=$userId;";
            if($result = mysql_query($query)){
                $inndata = array();
                while($res = mysql_fetch_array($result)){
                    array_push($inndata, getSqlData($colsArray, $res));
                }
                $data["items"] = $inndata;
            }
            return $data;

        }
    }

?>