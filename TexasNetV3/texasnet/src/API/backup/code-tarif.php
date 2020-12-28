<?php

include('connect.php');
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $reponse=$bdd->query("SELECT promoCodeTarif,promoPourcentageCodeTarif FROM modules");
    $retour=$reponse->fetch();
    $promoCodeTarif=$retour["promoCodeTarif"];
    $promoPourcentageCodeTarif=$retour["promoPourcentageCodeTarif"];

    $reponse->closeCursor();
    $reponse=$bdd->prepare("SELECT codeTarif FROM client WHERE login=:login");
    $reponse->execute(array(
        "login"=>$_POST["login"]
    ));
    $retour=$reponse->fetch();
    $codeTarifClient=$retour["codeTarif"];
    
    $reponse->closeCursor();

    $arrayCodeTarifClient=(explode(';',$promoCodeTarif));
    $arrayPromoPourcentage=(explode(';',$promoPourcentageCodeTarif));

    if(in_array($codeTarifClient,$arrayCodeTarifClient)){
        $isCodeTarif=true;
        $indicePromoPourcentage=array_search($codeTarifClient,$arrayCodeTarifClient);
    }else{
        $isCodeTarif=false;
    }

    $arrCodeTarif[0]=$isCodeTarif;
    $arrCodeTarif[1]=$arrayPromoPourcentage[$indicePromoPourcentage];
    echo json_encode($arrCodeTarif);

}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>