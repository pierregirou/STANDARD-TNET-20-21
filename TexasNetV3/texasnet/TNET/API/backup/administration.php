<?php
include('connect.php');
/* met à jour la table modules à partir du pannel d'administration */
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    if($_POST["module"]=="visGalerie"){
        $req=$bdd->prepare("UPDATE modules SET visGalerie=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="maintenance"){
        $req=$bdd->prepare("UPDATE modules SET maintenance=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="langueAng"){
        $req=$bdd->prepare("UPDATE modules SET langueAng=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="updateAdresse"){
        $req=$bdd->prepare("UPDATE modules SET updateAdresse=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="selectionMoment"){
        $req=$bdd->prepare("UPDATE modules SET selectionMoment=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="promotion"){
        $req=$bdd->prepare("UPDATE modules SET promotion=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="modeSaisie"){
        $req=$bdd->prepare("UPDATE modules SET modeSaisie=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="soColissimo"){
        $req=$bdd->prepare("UPDATE modules SET soColissimo=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="timerCommande"){
        $req=$bdd->prepare("UPDATE modules SET timerCommande=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="points"){
        $req=$bdd->prepare("UPDATE modules SET points=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="stockCouleur"){
        $req=$bdd->prepare("UPDATE modules SET stockCouleur=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="fraisDePort"){
        $req=$bdd->prepare("UPDATE modules SET fraisDePort=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="montantPort"){
        $req=$bdd->prepare("UPDATE modules SET montantPort=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="portGratuit"){
        $req=$bdd->prepare("UPDATE modules SET portGratuit=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="stockDisponible"){
        $req=$bdd->prepare("UPDATE modules SET stockDisponible=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="stockIndisponible"){
        $req=$bdd->prepare("UPDATE modules SET stockIndisponible=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="minStockLimite"){
        $req=$bdd->prepare("UPDATE modules SET minStockLimite=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="maxStockLimite"){
        $req=$bdd->prepare("UPDATE modules SET maxStockLimite=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="controleStock"){
        $req=$bdd->prepare("UPDATE modules SET controleStock=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="quantiteMax"){
        $req=$bdd->prepare("UPDATE modules SET quantiteMax=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="valQteMax"){
        $req=$bdd->prepare("UPDATE modules SET valQteMax=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    if($_POST["module"]=="cdeMarque"){
        $req=$bdd->prepare("UPDATE modules SET cdeMarque=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }

    if($_POST["module"]=="prixVenteConseille"){
        $req=$bdd->prepare("UPDATE modules SET prixVenteConseille=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    
    if($_POST["module"]=="promoCodeTarif"){
        $req=$bdd->prepare("UPDATE modules SET promoCodeTarif=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }

    if($_POST["module"]=="promoPourcentageCodeTarif"){
        $req=$bdd->prepare("UPDATE modules SET promoPourcentageCodeTarif=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }

    if($_POST["module"]=="promoMontant"){
        $req=$bdd->prepare("UPDATE modules SET promoMontant=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }

    if($_POST["module"]=="promoPourcentage"){
        $req=$bdd->prepare("UPDATE modules SET promoPourcentage=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }

    if($_POST["module"]=="visInf"){
        $req=$bdd->prepare("UPDATE modules SET visInformationAff=:nouvelleValeur");
        $req->execute(array(
            "nouvelleValeur"=>$_POST["valeur"]
        ));
    }
    


    ?>
    {
        "success":true,
        "message":"ok",
        "moduleToUpdate":"<?php echo $_POST["module"]; ?>",
        "nouvelleValeur":"<?php echo $_POST["valeur"]; ?>"
    }
    <?php
}else{
    ?>
    {
        "success":false,
        "message":"Only POST request allowed"
    }
    <?php
}
?>