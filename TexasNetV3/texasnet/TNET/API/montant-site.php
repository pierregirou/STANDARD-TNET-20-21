<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){ //vérifie l'état de la requête POST
    $reponse=$bdd->query("SELECT promoPourcentage,promoMontant FROM modules");
    $retour=$reponse->fetch();
    $promoPourcentage=$retour["promoPourcentage"];
    $promoMontant=$retour["promoMontant"];
    $reponse->closeCursor();
    $arrMontantSite[0]=true;
    $arrMontantSite[1]=$promoPourcentage;
    $arrMontantSite[2]=$promoMontant;
    echo json_encode($arrMontantSite);
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>