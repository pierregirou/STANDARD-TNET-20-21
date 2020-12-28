<?php
try{
    $bdd = new PDO ("mysql:host=localhost;dbname=dbname;charsetutf8","user","mdp");
}catch(Exception $e){
    die("Error : " .$e->getMessage());
}

?>
