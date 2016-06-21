<?php
    require_once("functions.php");
    if(! isset($base)){
        render_404page();
    }

    $servername = "127.0.0.1";
    $port = "3306";
    $username = "root";
    $password = "";
    $database = "pjmgmt";
    mysql_connect($servername.":".$port, $username, $password) or die("Connection failed: ".mysql_error());

    //echo "Connected successfully";
    mysql_select_db($database) or die("Unable to connect to database: ".mysql_error());
    //mysql_query ("set character_set_client='utf8'");
    //mysql_query ("set character_set_results='utf8'");
    mysql_query("SET NAMES 'utf8';");
    mysql_query ("set collation_connection='utf8_general_ci';");
?>