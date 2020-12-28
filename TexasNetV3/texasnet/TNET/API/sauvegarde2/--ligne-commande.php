<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){ //vérifie l'état de la requête POST
    $testMarque=0;
    if ($_POST["quantite"] >= 0) {
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

        /* Récupère le numéro de commande non valide crée lorsque l'utilisateur est arrivé sur le site ==> il ne peut y en avoir qu'un seul */
        $reponse=$bdd->prepare("SELECT numCommande FROM commande WHERE login=:login AND valid=0");
        $reponse->execute(array(
            "login"=>$_POST["login"]
        ));
        $retour=$reponse->fetch();
        $numCommande=$retour["numCommande"];
        $reponse->closeCursor();

        /* Détermine si le produit ajouté au panier est sujet à une promotion */
        $reponse=$bdd->prepare("SELECT promo FROM produit WHERE codetarif=:codetarif AND refproduit = (
                                    SELECT refproduit FROM detailproduit WHERE idproduit=:idproduit) AND codeColori=:codeColori");
        $reponse->execute(array(
            "idproduit"=>$_POST["idproduit"],
            "codeColori"=>$_POST["codeColoris"],
            "codetarif"=>$codeTarifClient
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

            $reponse=$bdd->prepare("SELECT tarif_promoL from detailproduit WHERE idproduit=:idproduit");
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
                $reponse=$bdd->prepare("SELECT tarif_promoL FROM detailproduit WHERE codetarif=:codetarif AND idproduit=:idproduit");
                $reponse->execute(array(
                    "idproduit"=>$_POST["idproduit"],
					"codetarif"=>$codeTarifClient
                ));
                $retour=$reponse->fetch();
                $prix=$retour["tarif_promoL"];
                $reponse->closeCursor();
            }else{
                $prixTaillePromo=0;
                $reponse=$bdd->prepare("SELECT tarif_promo FROM produit WHERE codetarif=:codetarif AND refproduit=(
                                            SELECT refproduit FROM detailproduit WHERE idproduit=:idproduit) AND codeColori=:codeColori ");
                $reponse->execute(array(
                    "idproduit"=>$_POST["idproduit"],
					"codeColori"=>$_POST["codeColoris"],
					"codetarif"=>$codeTarifClient
                ));
                $retour=$reponse->fetch();
                $prix=$retour["tarif_promo"];
                $reponse->closeCursor();
            }
        }else{
            $prixTaillePromo=0;
            $reponse=$bdd->prepare("SELECT prix FROM detailproduit WHERE codetarif=:codetarif AND idproduit=:idproduit");
            $reponse->execute(array(
                "idproduit"=>$_POST["idproduit"],
				"codetarif"=>$codeTarifClient
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



        $verifQuantite=0;

        //Modification de lignecommande ==> permet de connaitre la quantité le PU du produit ajouté au panier associé à la commande UNIQUE de l'utilisateur
        $reponse=$bdd->prepare("SELECT COUNT(*) as nbLigne FROM lignecommande WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
        //détermine si le produit est déjà associée à une commande
        $reponse->execute(array(
            "idDetailProduit"=>$_POST["idproduit"],
            "numCommande"=>$numCommande
        ));
        $retour=$reponse->fetch();
        $nbLigne=$retour["nbLigne"]; //si = 0 création d'une nouvelle ligne , si > 0 update la ligne existante

        $reponse->closeCursor();

		$reqEAN13=$bdd->prepare("SELECT codeean13 FROM detailproduit WHERE idproduit=:idproduit");
		$reqEAN13->execute(array(
			"idproduit"=>$_POST["idproduit"]
			));
			
		while($reponseEAN13 = $reqEAN13->fetch()){						
			$codeEan13=$reponseEAN13['codeean13'];
		}
					
        if($nbLigne==0){ //Dans le cas où l'utilisateur ajoute un nouveau produit au panier
            /* Crée dans ligne commande une nouvelle ligne en renseignant le numéro de la commande en cours, le produit commandé, la quantité et son prix */


            $reqModule=$bdd->query("SELECT cdeMarque FROM modules");
            $reponseModule=$reqModule->fetch();
            $cdeMarque=$reponseModule['cdeMarque'];
            $reqModulePanier=$bdd->prepare("SELECT DISTINCT codeMarque,codeSaison FROM produit p INNER JOIN detailproduit d ON d.refproduit = p.refproduit INNER JOIN lignecommande l ON l.idDetailProduit = d.idproduit WHERE l.numCommande = :numCommande");
            $reqModulePanier->execute(array(
                "numCommande"=>$numCommande
            ));
            //$tab=[];
            while($donnees = $reqModulePanier->fetch()){
                $tab[]= $donnees['codeMarque'];
                $tabS[]= "000;";
                $tabS[]= $donnees['codeSaison'];
            }

            if ($cdeMarque == '1') {
                if(!(in_array($_POST["codeMarque"],$tab)) && count($tab)>0){
                    $testMarque="0";
                    $testSaison="1";
                } else {
                    $req=$bdd->prepare("INSERT INTO lignecommande (numCommande,idDetailProduit,quantite,commentaire,prix) VALUES (:numCommande,:idDetailProduit,:quantite,:commentaire,:prix)");
                    $req->execute(array(
                        "numCommande"=>$numCommande,
                        "idDetailProduit"=>$_POST["idproduit"],
                        "quantite"=>$_POST["quantite"],
                        "commentaire"=>"a",
                        "prix"=>$prix
                    ));
                    /* Mise à jour du stocke enlève la quantité sélectionnée et rajoute la quantité à stockencmd */
				
					
                    $req=$bdd->prepare("UPDATE detailproduit set stockdisponible=stockdisponible-:depstock , stockencmd=stockencmd+:depstock  WHERE codeean13=:codeean13");
                    $req->execute(array(
                        "depstock"=>$_POST["quantite"],						
                        "codeean13"=>$codeEan13
                    ));
                    $testMarque="1";
                    $testSaison="1";
                }
            } else {
				if($_POST["quantite"] < 1) {
					$qte = 1;
				} else {
					$qte = $_POST["quantite"];
				}
                    $req=$bdd->prepare("INSERT INTO lignecommande (numCommande,idDetailProduit,quantite,commentaire,prix) VALUES (:numCommande,:idDetailProduit,:quantite,:commentaire,:prix)");
                    $req->execute(array(
                        "numCommande"=>$numCommande,
                        "idDetailProduit"=>$_POST["idproduit"],
                        "quantite"=>$qte,
                        "commentaire"=>"b",
                        "prix"=>$prix
                    ));

                        // "quantite"=>$_POST["quantite"],
                    /* Mise à jour du stocke enlève la quantité sélectionnée et rajoute la quantité à stockencmd */
					
                    $req=$bdd->prepare("UPDATE detailproduit set stockdisponible=stockdisponible-:depstock , stockencmd=stockencmd+:depstock  WHERE codeean13=:codeean13");
                    $req->execute(array(
                        "depstock"=>$_POST["quantite"],						
                        "codeean13"=>$codeEan13
                    ));
                    $testSaison="1";
                    $testMarque="1";

            }

        }

        // Debut bug

        if($nbLigne>0){
            $reqModule=$bdd->query("SELECT cdeMarque FROM modules");
            $reponseModule=$reqModule->fetch();
            $cdeMarque=$reponseModule['cdeMarque'];
            $reqModulePanier=$bdd->prepare("SELECT DISTINCT codeMarque,codeSaison FROM produit p LEFT JOIN detailproduit dp ON dp.refproduit = p.refproduit LEFT JOIN lignecommande lc ON lc.idDetailProduit = dp.idproduit WHERE numCommande = :numCommande");
            $reqModulePanier->execute(array(
                "numCommande"=>$numCommande
            ));
            //$tab=[];
            while($donnees = $reqModulePanier->fetch()){
                $tab[]= $donnees['codeMarque'];
            }


            if ($cdeMarque == '1') {
                if(!(in_array($_POST["codeMarque"],$tab)) && count($tab)>0 ){
                    $testMarque="0";
                    $testSaison="1";
                }else{
                    $req=$bdd->prepare("INSERT INTO lignecommande (numCommande,idDetailProduit,quantite,commentaire,prix) VALUES (:numCommande,:idDetailProduit,:quantite,:commentaire,:prix)");
                    $req->execute(array(
                        "numCommande"=>$numCommande,
                        "idDetailProduit"=>$_POST["idproduit"],
                        "quantite"=>$_POST["quantite"],
                        "commentaire"=>"c",
                        "prix"=>$prix
                    ));
                    $testMarque="1";
                    $testSaison="1";
                }
            } else {
                    //dans le cas où un produit est ajouté au panier -->upload
                    $reponse=$bdd->prepare("SELECT quantite FROM lignecommande WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
                    $reponse->execute(array(
                        "idDetailProduit"=>$_POST["idproduit"],
                        "numCommande"=>$numCommande
                    ));
                    $retour=$reponse->fetch();
                    $verifQuantite=$retour["quantite"];
                    $reponse->closeCursor();

                    $req=$bdd->prepare("UPDATE lignecommande set quantite=:quantite WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
                    $req->execute(array(
                        "quantite"=>$_POST["quantite"],
                        "idDetailProduit"=>$_POST["idproduit"],
						"numCommande"=>$numCommande								 
                    ));


					if($_POST["quantite"] == 0) { // Supprime la ligne si elle est EGALE à 0
						$reqD=$bdd->prepare("DELETE FROM lignecommande WHERE numCommande=:numCommande AND idDetailProduit=:idDetailProduit");
						$reqD->execute(array(
							"numCommande"=>$numCommande,
							"idDetailProduit"=>$_POST["idproduit"]
						));
                    }

                    if($verifQuantite>$_POST["quantite"]){ //si la quantité sélectionnée est INFERIEURE à la quantité déjà dans le panier
                        $req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible+:quantite , stockencmd=stockencmd-:quantite WHERE codeean13=:codeean13");
                        $req->execute(array(
                            "quantite"=>($verifQuantite-$_POST["quantite"]),
                            "codeean13"=>$codeEan13
                        ));
                    }
                    if($verifQuantite<$_POST["quantite"]){ //si la quantité sélectionnée est SUPERIEURE à la quantité déjà dans le panier
                        $req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible-:quantite , stockencmd=stockencmd+:quantite WHERE codeean13=:codeean13");
                        $req->execute(array(
                            "quantite"=>($_POST["quantite"]-$verifQuantite),
                            "codeean13"=>$codeEan13
                        ));
                    }
                    $testSaison="1";
                    $testMarque="1";

            }

        }

        // Fin bug

        $quantiteCommande=0;
        $montant=0;
        $reponse=$bdd->prepare("SELECT quantite,prix FROM lignecommande WHERE numCommande=:numCommande");
        $reponse->execute(array(
            "numCommande"=>$numCommande
        ));
        while($donnees=$reponse->fetch()){
            $quantiteCommande=$quantiteCommande+$donnees["quantite"];
            $montant=$montant+($donnees["quantite"]*$donnees["prix"]);
        }
        $reponse->closeCursor();


        $reponse=$bdd->query("SELECT fraisDePort,montantPort,portGratuit,tauxEscompteGlobal FROM modules");
        $retour=$reponse->fetch();
        $fraisDePort=$retour["fraisDePort"];
        $montantPort=$retour["montantPort"];
        $portGratuit=$retour["portGratuit"];
        $tauxEscompteGlobal=$retour["tauxEscompteGlobal"];
        $reponse->closeCursor();

        /*if($fraisDePort==1){
            if( ($montant+$fraisDePort)<$portGratuit){
                $montant+=$montantPort;
            }
        }*/
        $reponse=$bdd->query("SELECT promoPourcentage,promoMontant FROM modules");
        $retour=$reponse->fetch();
        $promoPourcentage=$retour["promoPourcentage"];
        $promoMontant=$retour["promoMontant"];
        $reponse->closeCursor();
        if($promoMontant>0){
            $montant=$montant-$promoMontant;
            $montant=round($montant,2);
        }
        if($promoPourcentage>0){
            $montant=(float)$montant * (1-(((float)$promoPourcentage)/100));
            $montant=round($montant,2);
        }

        $req=$bdd->prepare("UPDATE commande set montant=:montant , nbrpiece=:nbrpiece, escompte=:escompte WHERE numCommande=:numCommande");
        $req->execute(array(
            "montant"=>$montant,
            "nbrpiece"=>$quantiteCommande,
            "escompte"=>($montant*$tauxEscompteGlobal)/100,
            "numCommande"=>$numCommande
        ));
        ?>
        {
            "success":true,
            "promoMontant":<?php echo $promoMontant; ?>,
            "promoPourcentage":<?php echo $promoPourcentage; ?>,
            "isPromo":"<?php echo $isPromo; ?>",
            "fraisDePort":"<?php echo $fraisDePort; ?>",
            "montantPort":"<?php echo $montantPort; ?>",
            "portGratuit":"<?php echo $portGratuit; ?>",
            "prixTaillePromo":"<?php echo $prixTaillePromo; ?>",
            "message":"Ajout au panier <?php echo $nbLigne; ?>",
            "verif quantite":<?php echo $verifQuantite; ?>,
            "testMarque":"<?php echo $testMarque; ?>",
            "testSaison":"<?php echo $testSaison; ?>",
            "montant":<?php echo $montant; ?>,
            "codeTarifClient":"<?php echo $codeTarifClient; ?>",
            "promoCodeTarif":"<?php echo $promoCodeTarif; ?>",
            "promoPourcentageCodeTarif":"<?php echo $promoPourcentageCodeTarif ." " .$prix; ?>",
            "pourcentage":<?php echo ((float)($promoPourcentageCodeTarif))/100; ?>
        }
        <?php
    }
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>
