<?php

include('connect.php');

$login_array=[];

$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
    $login_array[]= $donnees["login"];
}
$reponse->closeCursor();



$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $reponse=$bdd->prepare("SELECT numCommande FROM commande WHERE login=:login AND valid=0");
    $reponse->execute(array(
        "login"=>$_POST["login"]
    ));
    $retour=$reponse->fetch();
    $numCommande=$retour["numCommande"];
    $reponse->closeCursor();

    $montantEscompte = $_POST['montantEscompte'];    
    $montantTPH = $_POST['montantTPH'];   
    $montantPort = $_POST['montantPort'];   
    $montantTVA = $_POST['montantTVA'];   

    
    $req=$bdd->prepare("UPDATE commande SET escompte=:escompte,mtTPH=:TPH,mttva=:TVA,fraisport=:PORT WHERE numCommande=:numCommande");
    $req->execute(array(
        "escompte"=> $montantEscompte,
        "TPH"=> $montantTPH,
        "TVA"=> $montantTVA,
        "PORT"=> $montantPort,
        "numCommande"=> $numCommande
    ));

}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}

?>