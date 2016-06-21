<?php
    require_once("functions.php");
    if(!isset($base)){
        render_404page();
    }
    Class Message{
        function addMessage($uid, $hash, $ruid, $message){
            if(!checkCookies($uid, $hash)){return 2;}
            if(!($ruid > 0)){return 3;}
            if(!$this->hasUser($ruid)){return 4;}
            $message = $this->checkMessage($message);
            $query = "INSERT INTO `message` (`suid`, `ruid`, `message`) VALUES ($uid, $ruid, '$message');";
            if(mysql_query($query)){return 1;}
            return 5;

        }
        function hasUser($uid){
            $query = "SELECT * FROM `users` WHERE `uid`=$uid;";
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    return true;
                }
            }
            return false;
        }
        function checkMessage($message){
            return mysql_real_escape_string($message);
        }
        function getMessagesFrom($uid, $hash, $ruid, $limit, $from){
            if(!checkCookies($uid, $hash)){return 3;}
            if(!($ruid > 0)){return 4;}
            if(!$this->hasUser($ruid)){return 5;}
            if(!($from > 0)){$from = 0;}
            if(!($limit > 0)){return 6;}
            $query = "SELECT `users`.`fname` as `rfname`, `users`.`pic` as `rpic`, `last`.`sfname`, `last`.`spic`,`last`.`message`, `last`.`messageCreated`, `last`.`suid`, `last`.`mid`, `last`.`ruid` FROM (SELECT `users`.`fname` as `sfname`, `users`.`pic` as `spic`, `message`.`message`, `message`.`mid`, `message`.`messageCreated`, `message`.`suid`, `message`.`ruid` FROM `message`, `users` WHERE `users`.`uid` = `message`.`suid` AND `message`.`mid` > $from) AS `last`, `users` WHERE `users`.`uid`=`last`.`ruid` ORDER BY `last`.`messageCreated` LIMIT $limit;";
            //echo $query."<br>";
            $messages = array();
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $messages[$res["mid"]] = array(
                        "mid" => $res["mid"],
                        "suid" => $res["suid"],
                        "ruid" => $res["ruid"],
                        "message" => $res["message"],
                        "messageCreated" => $res["messageCreated"],
                        "rfname" => $res["rfname"],
                        "sfname" => $res["sfname"],
                        "rpic" => getUserPic($res["rpic"]),
                        "spic" => getUserPic($res["spic"])
                    );
                }
                return $messages;
            }
            return 2;
        }
        function getMessagesBefore($uid, $hash, $ruid, $limit, $before){
            if(!checkCookies($uid, $hash)){return 3;}
            if(!($ruid > 0)){return 4;}
            if(!$this->hasUser($ruid)){return 5;}
            $beforeData = "AND `message`.`mid` < $before ";
            if(!($before > 0)){$beforeData = "";}
            if(!($limit > 0)){return 6;}
            $query = "SELECT `users`.`fname` as `rfname`, `users`.`pic` as `rpic`, `last`.`sfname`, `last`.`spic`,`last`.`message`, `last`.`messageCreated`, `last`.`suid`, `last`.`mid`, `last`.`ruid` FROM (SELECT `users`.`fname` as `sfname`, `users`.`pic` as `spic`, `message`.`message`, `message`.`mid`, `message`.`messageCreated`, `message`.`suid`, `message`.`ruid` FROM `message`, `users` WHERE `users`.`uid` = `message`.`suid` $beforeData) AS `last`, `users` WHERE `users`.`uid`=`last`.`ruid` ORDER BY `last`.`messageCreated` ASC LIMIT $limit;";
            $messages = array();
            if($result = mysql_query($query)){
                while($res = mysql_fetch_array($result)){
                    $messages[$res["mid"]] = array(
                        "mid" => $res["mid"],
                        "suid" => $res["suid"],
                        "ruid" => $res["ruid"],
                        "message" => $res["message"],
                        "messageCreated" => $res["messageCreated"],
                        "rfname" => $res["rfname"],
                        "sfname" => $res["sfname"],
                        "rpic" => getUserPic($res["rpic"]),
                        "spic" => getUserPic($res["spic"])
                    );
                }
                return $messages;
            }
            return 2;
        }
    }
?>