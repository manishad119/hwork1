<?php
    require_once("functions.php");
    if(!isset($base)){
        render_404page();
    }
    Class Item{
        var $item;
        var $hasItem = false;
        function __construct($data){
            if($this->checkAll($data)){
                $this->hasItem = true;
                $this->item = $data;
            }
        }
        function checkAll($data){
            if(!$data["uid"]){
                return false;
            }else if(!$data["itemName"]){return false;}
            else if(!$data["money"]){return false;}
            else if(!$data["period"]){return false;}
            else if(!$data["longitude"]){return false;}
            else if(!$data["latitude"]){return false;}
            return true;
        }
        function publish($uid, $ucookie){
            if(checkCookies($uid, $ucookie) && ($this->item["uid"] == $uid)){
                $longitude = $this->checkLongitude($this->item["longitude"]);
                $latitude = $this->checkLatitude($this->item["latitude"]);
                $address = $this->checkAddress($this->item["address"]);
                $query = "INSERT INTO `address` (`uid`, `longitude`, `latitude`, `address`) VALUES ($uid, '$longitude', '$latitude', '$address');";
                if(mysql_query($query)){
                    $aid = mysql_insert_id();
                    $itemName = $this->checkData($this->item["itemName"]);
                    $itemImageSrc = $this->checkData($this->item["itemImageSrc"]);
                    $money = $this->checkMoney($this->item["money"]);
                    $period = $this->checkData($this->item["period"]);
                    $expressType = $this->checkData($this->item["expressType"]);
                    $itemIntroduction = $this->checkData($this->item["itemIntroduction"]);

                    $nquery = "INSERT INTO `item` (`uid`, `itemName`, `itemImageSrc`, `money`, `period`,`expressType`, `aid`, `itemIntroduction`) VALUES ($uid, '$itemName', '$itemImageSrc',$money , '$period', '$expressType', $aid, '$itemIntroduction');";
					if(mysql_query($nquery)){
                        return mysql_insert_id();
                    }
                }
            }
            return false;
        }
        function checkLatitude($latitude){
            if($latitude >= -90 && $latitude <= 90){return $latitude;}
            return 0;
        }
        function checkLongitude($longitude){
            if($longitude >= -180 && $longitude <= 180){return $longitude;}
            return 0;
        }
        function checkAddress($address){
            return mysql_real_escape_string($address);
        }
        function checkPeriod($period){
            return mysql_real_escape_string($period);
        }
        function checkData($period){
            return mysql_real_escape_string($period);
        }
        function checkMoney($money){
            if($money >=0){
                return $money;
            }
            return 1.0;
        }
        function updateItemImage($uid, $hash, $iid, $imgId){
            if(!($iid > 0)){return false;}
            if(!checkCookies($uid, $hash)){return false;}
            $query = "UPDATE `item` SET `itemImageSrc`='$imgId' WHERE `iid`=$iid AND `uid` = $uid;";
            if(mysql_query($query)){return true;}
            return false;
        }
    }
?>