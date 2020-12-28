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
	
	$reqEAN13=$bdd->prepare("SELECT codeean13 FROM detailproduit WHERE idproduit=:idproduit");
	$reqEAN13->execute(array(
		"idproduit"=>$_POST["idproduit"]
		));
		
	while($reponseEAN13 = $reqEAN13->fetch()){						
		$codeEan13=$reponseEAN13['codeean13'];
	}

    $fraisDePortM = $_POST['fraisDePort'];
    /* Vérifie si le login possède un codetarif */
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

    $verifQuantite=0; //vérifier la quantité de produit déjà dans le panier (lignecommande)
    $reponse=$bdd->prepare("SELECT stockdisponible FROM detailproduit WHERE idproduit=:idproduit");
    $reponse->execute(array(
        "idproduit"=>$_POST["idproduit"]
    ));
    $retour=$reponse->fetch();
    $stockDispo=$retour["stockdisponible"];
    $reponse->closeCursor();

    /* Détermine si le produit ajouté au panier est sujet à une promotion */
    $reponse=$bdd->prepare("SELECT promo FROM produit WHERE refproduit = (
        SELECT refproduit FROM detailproduit WHERE idproduit=:idproduit)");
    $reponse->execute(array(
    "idproduit"=>$_POST["idproduit"]
    ));
    $retour=$reponse->fetch();
    $isPromo=$retour["promo"];
    $reponse->closeCursor();
    /* Détermine le prix du produit si il est sujet à une promotion */
    if($isPromo=="1"){
        /* Différents cas :
            -L'article est en promo mais pour certaines tailles
            -L'article est en promo mais le prix de la reduction s'applique sur toutes les tailles */

        //Test si le tarif de réduction s'applique sur toutes les tailles ou non
        $reponse=$bdd->prepare("SELECT tarif_promoL from detailproduit WHERE refproduit IN (
                                    SELECT refproduit FROM detailproduit WHERE idproduit=:idproduit)");
        $reponse->execute(array(
            "idproduit"=>$_POST["idproduit"]
        ));
        $testTaillePromo=0;
        while($donnees=$reponse->fetch()){
            if($donnees["tarif_promoL"]!="0.00"){
                $testTaillePromo++;
            }
        }
        $reponse->closeCursor();
        /* Cas où les promos sont assignées à une taille particulière */
        if($testTaillePromo!=0){
            $prixTaillePromo=1;
            $reponse=$bdd->prepare("SELECT tarif_promoL FROM detailproduit WHERE idproduit=:idproduit");
            $reponse->execute(array(
                "idproduit"=>$_POST["idproduit"]
            ));
            $retour=$reponse->fetch();
            $prix=$retour["tarif_promoL"];
            $reponse->closeCursor();
        }else{
            $prixTaillePromo=0;
            $reponse=$bdd->prepare("SELECT tarif_promo FROM produit WHERE refproduit=(
                                        SELECT refproduit FROM detailproduit WHERE idproduit=:idproduit)");
            $reponse->execute(array(
                "idproduit"=>$_POST["idproduit"]
            ));
            $retour=$reponse->fetch();
            $prix=$retour["tarif_promo"];
            $reponse->closeCursor();
        }
    }else{
        $prixTaillePromo=0;
        $reponse=$bdd->prepare("SELECT prix FROM detailproduit WHERE idproduit=:idproduit");
        $reponse->execute(array(
            "idproduit"=>$_POST["idproduit"]
        ));
        $retour=$reponse->fetch();
        $prix=$retour["prix"];
        $reponse->closeCursor();
    }


    $arrayCodeTarifClient=(explode(';',$promoCodeTarif));
    $arrayPromoPourcentage=(explode(';',$promoPourcentageCodeTarif));

    if(in_array($codeTarifClient,$arrayCodeTarifClient)){
        $isCodeTarif=true;
        $indicePromoPourcentage=array_search($codeTarifClient,$arrayCodeTarifClient);
        (int)$prix=((float)$prix * (1-((float)$arrayPromoPourcentage[$indicePromoPourcentage])/100));
        $prix=round($prix,2);
    }


    $reponse=$bdd->prepare("SELECT numCommande FROM commande WHERE login=:login AND valid=0");
    $reponse->execute(array(
        "login"=>$_POST["login"]
    ));
    $retour=$reponse->fetch();
    $numCommande=$retour["numCommande"];
    $reponse->closeCursor();
    $reponse=$bdd->query("SELECT promoPourcentage,promoMontant FROM modules");
    $retour=$reponse->fetch();
    $promoPourcentage=$retour["promoPourcentage"];
    $promoMontant=$retour["promoMontant"];
    $reponse->closeCursor();
    if($promoMontant>0){
        $prix=$prix-$promoMontant;
    }
    if($promoPourcentage>0){
        $prix=(float)$prix * (1-((float)$promoPourcentageCodeTarif)/100);
        $prix=round($prix,2);
    }

    $reponse=$bdd->query("SELECT fraisDePort,montantPort,portGratuit,tauxEscompteGlobal FROM modules");
    $retour=$reponse->fetch();
    $fraisDePort=$retour["fraisDePort"];
    $montantPort=$retour["montantPort"];
    $portGratuit=$retour["portGratuit"];
    $tauxEscompteGlobal=$retour["tauxEscompteGlobal"];
    $reponse->closeCursor();

    /* Détermine le montant du panier */
    $montantPanier=0;
    $reponse=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCommande");
    $reponse->execute(array(
        "numCommande"=>$numCommande
    ));
    while($donnees=$reponse->fetch()){
        $montantPanier=((float)($donnees["prix"]))*((float)($donnees["quantite"]));
    }
    $reponse->closeCursor();

    $reponse=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCommande AND idDetailProduit=:idDetailProduit");
    $reponse->execute(array(
        "idDetailProduit"=>$_POST["idproduit"],
        "numCommande"=>$numCommande
    ));
    $donnees=$reponse->fetch();
    $quantiteCommander=$donnees["quantite"];
    $reponse->closeCursor();



    if($_POST["action"]=="plus"){
        if($stockDispo>0){
            $req=$bdd->prepare("UPDATE lignecommande set quantite=:qte WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
            $req->execute(array(
                "idDetailProduit"=>$_POST["idproduit"],
                "qte"=>$_POST["quantite"],
                "numCommande"=>$numCommande
            ));
            $req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible-:qte , stockencmd=stockencmd+1 WHERE codeean13=:codeean13");
            $req->execute(array(
                "codeean13"=>$codeean13,
                "qte"=>$_POST["quantite"]
            ));

            $req=$bdd->prepare("UPDATE commande SET montant=montant+:prix , nbrpiece=nbrpiece+:qte, fraisport=:fraisport WHERE login=:login AND valid=0");
            $req->execute(array(
                "prix"=>$prix*($_POST["quantite"]-$quantiteCommander),
                "login"=>$_POST["login"],
                "fraisport"=>$fraisDePortM,
                "qte"=>($_POST["quantite"]-$quantiteCommander)
            ));
        }
    }
    if($_POST["action"]=="minus"){

        if(empty($numCommande)){
            $numCommande = $_POST['numCommande'];
        }

        if($_POST["quantite"]>=1){           
			
            if($_POST["app"]=="approuveur"){	
			
				/*echo "UPDATE lignecommande set quantite='".($quantiteCommander - $_POST["quantite"])."' WHERE idDetailProduit='".$_POST["idproduit"]."' AND numCommande=$numCommande";
				echo "UPDATE commande SET montant=montant-".$prix*($quantiteCommander - $_POST["quantite"])." , nbrpiece=nbrpiece-".$_POST["quantite"]." WHERE numCommande=$numCommande";*/
				
				$req=$bdd->prepare("UPDATE lignecommande set quantite=:qte WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
				$req->execute(array(
					"idDetailProduit"=>$_POST["idproduit"],
					"qte"=>($quantiteCommander - $_POST["quantite"]),
					"numCommande"=>$numCommande
				));

				$req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible+:qte , stockencmd=stockencmd-:qte WHERE codeean13=:codeean13");
				$req->execute(array(
					"codeean13"=>$codeean13,
					"qte"=>($quantiteCommander - $_POST["quantite"])
				));
				
				$req=$bdd->prepare("UPDATE commande SET montant=montant-:prix , nbrpiece=nbrpiece-:qte WHERE numCommande=:numCommande");
                $req->execute(array(
                    "prix"=>$prix*($quantiteCommander - $_POST["quantite"]),
                    "numCommande"=>$numCommande,
                    "qte"=>$_POST["quantite"]
                ));
            } else {				
				$req=$bdd->prepare("UPDATE lignecommande set quantite=:qte WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
				$req->execute(array(
					"idDetailProduit"=>$_POST["idproduit"],
					"qte"=>$_POST["quantite"],
					"numCommande"=>$numCommande
				));

				$req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible+:qte , stockencmd=stockencmd-1 WHERE codeean13=:codeean13");
				$req->execute(array(
					"codeean13"=>$codeean13,
					"qte"=>$_POST["quantite"]
				));

                //echo "UPDATE commande SET montant=montant-".$prix*($quantiteCommander - $_POST["quantite"]).", nbrpiece=nbrpiece-".($quantiteCommander - $_POST["quantite"]).", fraisport=".$fraisDePortM."  WHERE login=".$_POST["login"]." AND valid=0";
                $req=$bdd->prepare("UPDATE commande SET montant=montant-:prix , nbrpiece=nbrpiece-:qte, fraisport=:fraisport  WHERE login=:login AND valid=0");
                $req->execute(array(
                    "prix"=>$prix*($quantiteCommander - $_POST["quantite"]),
                    "login"=>$_POST["login"],
                    "fraisport"=>$fraisDePortM,
                    "qte"=>($quantiteCommander - $_POST["quantite"])
                ));
            }
        }
    }

    if($_POST["action"]=="updateMobile"){
        if($_POST["quantite"]>=1){

            $reponse=$bdd->prepare("SELECT quantite FROM lignecommande WHERE idDetailProduit=:idDetailProduit");
            $reponse->execute(array(
                "idDetailProduit"=>$_POST["idproduit"]
            ));
            $retour=$reponse->fetch();
            $quantiteCo=$retour["quantite"];
            $reponse->closeCursor();

            $req=$bdd->prepare("UPDATE lignecommande SET quantite=:quantite WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
            $req->execute(array(
                "quantite"=>$_POST["quantite"],
                "idDetailProduit"=>$_POST["idproduit"],
                "numCommande"=>$numCommande
            ));
            /* On sélectionne une quantité inférieure à celle sélectionnée */
            if($quantiteCo>$_POST["quantite"]){
                $req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible+:quantite, stockencmd=stockencmd-:quantite WHERE codeean13=:codeean13");
                $req->execute(array(
                    "quantite"=>($quantiteCo-$_POST["quantite"]),
                    "codeean13"=>$codeean13
                ));

                $req=$bdd->prepare("UPDATE commande SET montant=montant-:prixQuantite , nbrpiece=nbrpiece-:quantite, fraisport=:fraisport WHERE login=:login AND valid=0");
                $req->execute(array(
                    "prixQuantite"=>($prix*($quantiteCo-$_POST["quantite"])),
                    "quantite"=>$quantiteCo-$_POST["quantite"],
                    "login"=>$_POST["login"],
                    "fraisport"=>$fraisDePortM
                ));
            }

            if($quantiteCo<$_POST["quantite"]){
                $res=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible-:quantite, stockencmd=stockencmd+:quantite WHERE codeean13=:codeean13");
                $req->execute(array(
                    "quantite"=>($quantiteCo+$_POST["quantite"]),
                    "codeean13"=>$codeean13
                ));

                $req=$bdd->prepare("UPDATE commande SET montant=montant+:prixQuantite , nbrpiece=nbrpiece+:quantite, fraisport = :fraisport WHERE login=:login AND valid=0");
                $req->execute(array(
                    "prixQuantite"=>($prix*($_POST["quantite"]-$quantiteCo)),
                    "quantite"=>($_POST["quantite"]-$quantiteCo),
                    "login"=>$_POST["login"],
                    "fraisport"=>$fraisDePortM
                ));
            }
        }
    }
    //suppression d'un article de la commande
    if($_POST["action"]=="delete"){
        if($_POST["quantite"] === 0){
            $qteM = "1";
        } else {
            $qteM = $_POST["quantite"] ;
        }

        $idProdTab = $_POST["idproduitTab"];

        $request = "SELECT DISTINCT D.refproduit, L.prix, L.idDetailProduit FROM lignecommande L INNER JOIN detailproduit D ON L.idDetailProduit=D.idproduit WHERE D.idproduit IN (".$idProdTab.") AND L.numCommande=".$numCommande;

        $reponse2=$bdd->prepare($request);
        $reponse2->execute();
        while($donnees2=$reponse2->fetch()){
			
			$req3EAN13=$bdd->prepare("SELECT codeean13 FROM detailproduit WHERE idproduit=:idproduit");
			$req3EAN13->execute(array(
				"idproduit"=>$donnees2["idDetailProduit"]
				));
				
			while($reponse3EAN13 = $req3EAN13->fetch()){						
				$code3Ean13=$reponse3EAN13['codeean13'];
			}
	
	
          $reponse=$bdd->prepare("SELECT quantite, prix FROM lignecommande WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
          $reponse->execute(array(
              "idDetailProduit"=>$donnees2["idDetailProduit"],
              "numCommande"=>$numCommande
          ));
          $retour=$reponse->fetch();
          $prix=$retour["prix"];
          $verifQuantite=$retour["quantite"];
          $reponse->closeCursor();

          $req=$bdd->prepare("DELETE FROM lignecommande WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
          $req->execute(array(
              "idDetailProduit"=>$donnees2["idDetailProduit"],
              "numCommande"=>$numCommande
          ));

          $reponse=$bdd->prepare("SELECT montant FROM commande WHERE numCommande=:numCommande");
          $reponse->execute(array(
              "numCommande"=>$numCommande
          ));
          $retour=$reponse->fetch();
          $montant=$retour["montant"];
          $reponse->closeCursor();

          $montantAfter=$montant-($prix*$verifQuantite);

          $req=$bdd->prepare("UPDATE commande SET montant=:montantAfter , nbrpiece=nbrpiece-:quantite, fraisport = :fraisport WHERE numCommande=:numCommande");
          $req->execute(array(
              "montantAfter"=>$montantAfter,
              "quantite"=>$verifQuantite,
              "numCommande"=>$numCommande,
              "fraisport"=>$fraisDePortM
          ));

          $req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible+:quantite , stockencmd=stockencmd-:quantite WHERE codeean13=:codeean13");
          $req->execute(array(
              "quantite"=>$verifQuantite,
              "codeean13"=>$code3Ean13
          ));
        }
        $reponse2->closeCursor();
    }

    if($_POST["action"]=="deleteAll"){

        $reponse=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCommande");
        $tt=0;
        $reponse->execute(array(
            "numCommande"=>$numCommande
        ));
        $arrUpdate[0]=true;
        $i=1;
        while($donnees=$reponse->fetch()){
			
			$req2EAN13=$bdd->prepare("SELECT codeean13 FROM detailproduit WHERE idproduit=:idproduit");
			$req2EAN13->execute(array(
				"idproduit"=>$donnees["idDetailProduit"]
				));
				
			while($reponse2EAN13 = $req2EAN13->fetch()){						
				$code2Ean13=$reponse2EAN13['codeean13'];
			}
	
	
            $tt++;
            $produit=$donnees["idDetailProduit"];
            $quant=$donnees["quantite"];
            $arrUpdate[$i]["idproduit"]=$produit;
            $arrUpdate[$i]["quantite"]=$quant;
            $arrUpdate[$i]["tt"]=$tt;
            $i++;
            $req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible+:stockp , stockencmd=stockencmd-:stockp WHERE codeean13=:codeean13");
            $req->execute(array(
                "stockp"=>$donnees["quantite"],
                "codeean13"=>$code2Ean13
            ));
        }
        $reponse->closeCursor();

        $req=$bdd->prepare("DELETE FROM lignecommande WHERE numCommande=:numCommande");
        $req->execute(array(
            "numCommande"=>$numCommande
        ));
        $req=$bdd->prepare("UPDATE commande SET montant=0 ,nbrpiece=0, fraisport=0 WHERE numCommande=:numCommande");
        $req->execute(array(
            "numCommande"=>$numCommande
        ));

    }



    $reponse=$bdd->prepare("SELECT * FROM commande WHERE numCommande=:numCommande");
    $reponse->execute(array(
        "numCommande"=>$numCommande
    ));
    $donnees=$reponse->fetch();
    $montantCde = $donnees['montant'];
    $reponse->closeCursor();


    $req=$bdd->prepare("UPDATE commande SET escompte=:escompte WHERE numCommande=:numCommande");
    $req->execute(array(
        "escompte"=>($montantCde*$tauxEscompteGlobal)/100
    ));


    echo json_encode($arrUpdate);
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}

?>
