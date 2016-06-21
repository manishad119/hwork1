
<?php
    function uploadPic($type, $id, $postname){
        global $base;
        $target_dir = $base."pics/profilePic/";
        if($type == "itemPic"){$target_dir = $base."pics/itemPic/";}
        if(!isset($_FILES["$postname"]["name"]) || !$_FILES["$postname"]["name"]){return false;}
        $imageFileType = pathinfo($_FILES["$postname"]["name"],PATHINFO_EXTENSION);
        $target_file = $target_dir . "$id.".$imageFileType;//basename($_FILES["fileToUpload"]["name"]);
        //echo $target_file;
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["$postname"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            return false;
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["$postname"]["size"] > 50000000) {
            return false;
            $uploadOk = 0;
        }
        // Allow certain file formats
        if(strtolower($imageFileType) != "jpg" && $imageFileType != "png" && strtolower($imageFileType) != "jpeg"
        && $imageFileType != "gif" ) {
            return false;
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            return false;
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["$postname"]["tmp_name"], $target_file)) {
                return "$id.".$imageFileType;
            } else {
            return false;
            }
        }
    }
?>

