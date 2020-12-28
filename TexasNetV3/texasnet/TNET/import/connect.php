<?php
    include('/home/amateispro/public_html/import/config.php');
    try{
       $db = new PDO ("mysql:host=".$C_HOSTDB.";dbname=".$C_DBNAME.";charsetutf8", $C_USERDB, $C_PWDDB);
    }catch(Exception $e){
        die("Error : " .$e->getMessage());
    }
?>


