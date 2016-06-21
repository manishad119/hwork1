<?php
    require_once("functions.php");
    if(!isset($base)){
        render_404page();
    }
    Class User{
            var $user;
            function __construct($username=null, $data = array()){
                $this->user = $data;
                if($username){
                    $this->user["uname"] = $username;
                    $this->getuser(); //Get user from mysql.
                }
            }
            function checklogin($username, $password){
                //check username and password
                if($this->user['passhash'] == $this->get_pass_hash($username, $password)){return true;}
                return false;
            }
            function getuser(){
                //get user from the database

                $colsArray = array("users"=>array(1,2,3,4,5,6,7,8));
                $colStr = getSqlColumns($colsArray);
                $query = "SELECT * FROM `users` WHERE `uname`='".$this->user["uname"]."';";
                $result = mysql_query($query);
                if(!$result){
                    return false;
                }
                while($row = mysql_fetch_array($result)){
                    //Get Data in here
                    $this->user = getSqlData($colsArray, $row);
                    return true;
                }
            }
            function insert($data){
                //
                $data = $this->get_sql_format_data($data);
                $uname = $data['uname'];
                $passhash = $data['passhash'];
                $fname = $data['fname'];
                $gender = $data['gender'];
                $pic = $data['pic'];
                $email = $data['email'];
                $contact = $data['contact'];
                $role = $data['role'];

                $query = "INSERT INTO `users` (`uname`, `passhash`, `fname`, `gender`, `pic`, `email`, `contact`, `role`) VALUES ('$uname', '$passhash', '$fname', $gender, '$pic', '$email', '$contact', $role);";
                $result = mysql_query($query);
                if(!$result){return false;}
                return mysql_insert_id();

            }
            function get_sql_format_data($data){
                //Check all the data if it is in sql format and return an object.
                $newdata = array();
                //gender
                if($data["gender"] == "male"){$newdata["gender"] = 1;}
                else if($data["gender"] == "female"){$newdata["gender"] = 2;}
                else{$newdata["gender"] = 3;}
                //passhash
                if($this->check_username($data["uname"])){
                    $newdata["uname"] = $data["uname"];
                }
                if($newdata["uname"] && $data["password"]){
                    $newdata["passhash"]=$this->get_pass_hash($newdata["uname"], $data["password"]);
                }
                //Fullname
                $newdata["fname"] = $this->get_sql_name($data["fname"]);
                //E-mail
                $newdata["email"] = $this->get_sql_email($data["email"]);
                //get contact data.
                $newdata["contact"] = $this->get_sql_contact($data["contact"]);
                //Get the right format of the role.
                $newdata["role"] = $this->get_sql_role($data["role"]);
                $newdata["pic"] = "0.jpg";
                return $newdata;

            }
            function get_pass_hash($username, $password){
                //return a password hash
                $secret = " This is PJMGMT site. ";
                $data = $username.$secret.$password;
                return hash('sha256', $data);
            }
            function check_username($username){
                //Check if username format is correct;
                if($username){return true;}
                return false;
            }
            function get_sql_email($name){
                //
                return mysql_real_escape_string($name);
            }
            function get_sql_name($name){
                return mysql_real_escape_string($name);
            }
            function get_sql_contact($contact){
                //Get Contact data format.
                return mysql_real_escape_string($contact);
            }
            function get_sql_role($role){
                //Get right format of the role
                if($role == "admin"){return 1;}
                return 2;
            }
            function get_user_data(){
                $newdata = array();
                foreach ($this->user as $key => $value){
                    if($key != "passhash"){
                        $newdata[$key] = $value;
                    }
                }
                return $newdata;
            }
            function get_userid(){
                return $this->user["uid"];
            }
            function updateProfileImage($uid, $imgId){
                $query = "UPDATE `users` SET `pic`='$imgId' WHERE `uid`=$uid;";
                if(mysql_query($query)){return true;}
                return false;
            }
    }
    Class Users{
        var $users = array();
        function getUsersByLocation($longitude, $latitude, $diff=2){
            $lowLon = $longitude - $diff;
            $higLon = $longitude + $diff;
            $lowLat = $latitude - $diff;
            $higLat = $latitude + $diff;
            if($lowLat < -90){$temp = $lowLat;$lowLat = $higLat;$higLat = $temp+180;}
            if($lowLon < -180){$temp = $lowLon;$lowLon = $higLon;$higLon = $temp+360;}
            if($higLat > 90){$temp = $higLat;$higLat = $lowLat;$lowLat = $temp-180;}
            if($higLon > 180){$temp = $higLon;$higLon = $lowLon;$lowLon = $temp-360;}

            $colsArray = array("item"=>array(1,4), "address"=>array(3,4,5), "users"=>array(1,2, 4, 5,6));
            $colStr = getSqlColumns($colsArray);
            //`users`.`uid`, `users`.`pic`, `users`.`uname`, `item`.`itemImageSrc`
            $query = "SELECT $colStr FROM `item`, `address`, `users` WHERE (`address`.`latitude` > $lowLat AND `address`.`latitude` < $higLat) AND (`address`.`longitude` > $lowLon AND `address`.`longitude` < $higLon) AND `item`.`aid` = `address`.`aid` AND `item`.`uid` = `users`.`uid`";// Long way to go over here...
            if($result = mysql_query($query)){
                while($ret = mysql_fetch_array($result)){
                    $uname = $ret["uname"];
                    if(!isset($this->users[$uname])){
                        $this->users[$uname] = array("pic"=> getUserPic($ret["pic"]), "fname"=>$ret["fname"], "gender"=>$ret["gender"], "uid"=> $ret["uid"], "uname"=>$ret["uname"], "owningItems"=> array());
                    }
                    $this->users[$uname]["owningItems"][] = array("itemImageSrc"=>getItemPic($ret["itemImageSrc"]), "itemId"=>$ret["iid"]);
                }
            }
            return $this->users;
        }
        function getUsersBySearch($q){
            $q = mysql_real_escape_string($q);
            $colsArray = array("users"=>array(1, 2, 4, 5, 6));
            $colStr = getSqlColumns($colsArray);
            $query = "SELECT $colStr FROM `users` WHERE (`users`.`email` LIKE '%$q%') OR (`users`.`uname` LIKE '%$q%') OR (`users`.`fname` LIKE '%$q%') OR (`users`.`contact` LIKE '%$q%');";
            $items = array();
            if($result = mysql_query($query)){
                while($res = mysql_fetch_assoc($result)){
                    $item = getSqlData($colsArray, $res);
                    $items[] = $item;
                }
            }
            return $items;
        }
    }
?>