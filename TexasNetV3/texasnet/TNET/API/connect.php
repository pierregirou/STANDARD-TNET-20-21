<?php
try{
    $bdd = new PDO ("mysql:host=localhost;dbname=amateispro;charset=utf8","amateispro","hzu3z05Zahn");
}catch(Exception $e){
    die("Error : " .$e->getMessage());
}
?>