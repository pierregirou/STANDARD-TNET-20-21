<?php

include('connect.php');
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $reponse=$bdd->query("SELECT * FROM pays");                    
    $i=1;                    
    while($donnees=$reponse->fetch()){
        $arr2[$i]["idPays"]     = $donnees["id"];
        $arr2[$i]["codeISO2"]   = $donnees["alpha2"];     
        $arr2[$i]["codeISO3"]   = $donnees["alpha3"];
        $arr2[$i]["nomPays"]    = utf8_encode($donnees["nom_fr_fr"]); 
        $i++;
    }
    echo json_encode($arr2);
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>