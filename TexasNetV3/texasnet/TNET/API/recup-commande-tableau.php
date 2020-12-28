<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $arrcommande[0]["success"]=true;
    $reponse=$bdd->prepare("SELECT * FROM commande WHERE login=:login AND valid=0");
    $reponse->execute(array(
        "login"=>$_POST["login"]
    ));
    $retour=$reponse->fetch();
    $montant=$retour["montant"];
    $fraisport=$retour["fraisport"];
    $pieces=$retour["nbrpiece"];
    $numCommande=$retour["numCommande"];
    $escompte=$retour["escompte"];
    $reponse->closeCursor();


    $arrcommande[1]["montant"]=$montant;
    $arrcommande[1]["fraisport"]=$fraisport;
    $arrcommande[1]["pieces"]=$pieces;
    $arrcommande[1]["numCommande"]=$numCommande;
    $arrcommande[1]["escompte"]=$escompte;

    /*$reponse=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCommande");
    $reponse->execute(array(
        "numCommande"=>$numCommande
    ));
    $i=0;
    $reponse->closeCursor();*/

    echo json_encode($arrcommande);
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>