<?php

include('connect.php');

$login_array=[];
$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
    $login_array[]= $donnees["login"];
}
$reponse->closeCursor();

$reponse=$bdd->query("SELECT souscenTheme, visGalerie, ordreAffichage, imageMiniaturePicto, cenLigne FROM modules");
$retour=$reponse->fetch();
$souscenTheme=$retour["souscenTheme"];
$ordreAffichage=$retour["ordreAffichage"];
$visGalerie=$retour["visGalerie"];
$cenLigne=$retour["cenLigne"];
$imageMiniaturePicto=$retour["imageMiniaturePicto"];
$reponse->closeCursor();

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){

			$login=$_POST['login'];
	        $reponse=$bdd->prepare("SELECT codeTarif,souscentrale,civilite,centrale FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$login
            ));
            while($donnees=$reponse->fetch()){
                $civilite=$donnees["civilite"];
                $codeTarif=$donnees["codeTarif"];
				$centrale = $donnees['centrale'];
				$centrale = substr($centrale,0, -1);
				$reponse->closeCursor();

            }
            $reponse->closeCursor();


			if($_POST["type"]=="gestion"){
				$reponseTmp=$bdd->query("SELECT codeTarif FROM parametrage");
				$donneesTmp=$reponseTmp->fetch();
				$codeTarifGestion = $donneesTmp["codeTarif"];
				$reponseTmp->closeCursor();

				$codeTarif = $codeTarifGestion;
			}


            $arr2[0]["success"]=true;

			$req = "SELECT `idproduit`, p.`refproduit`, p.`libelle`, `codeColori`, `codeGammeTaille`, `codetailledebut`, `codetaillefin`, p.`codeSaison`,
			substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) as codeMarque,
			substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme,
			substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille,
			substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3,locate('/',substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3))-1) as codeSousFamille,
			substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3,locate('/',substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3))-1) as codeModele,
			substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne,
			 `nonCommandable`, `poids`, `codetarif`, `prix`, `libcolori`, `libMarque`, `commentaire1`, `commentaire2`, `commentaire3`, `commentaire4`, `commentaire5`, `selection`, `promo`, `tarif_promo`, `positionGalerie`, `tarif_pvc`, `stockdisponible`, `libelle2`, pg.position1, pg.position2, pg.position3, pg.position4, pg.position5, pg.position6, pgp.position1, pgp.position2, pgp.position3, pgp.position4, pgp.position5, pgp.position6
			 FROM produit p
			 LEFT JOIN positiongalerie pg ON p.refproduit=pg.refproduit
			 LEFT JOIN positiongaleriepromo pgp ON p.refproduit=pgp.refproduit
			 LEFT JOIN marque mq ON p.codeMarque = mq.idMarque
			 LEFT JOIN theme t ON p.codeTheme = t.idTheme
			 LEFT JOIN famille f ON p.codeFamille = f.idFamille
			 LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille
			 LEFT JOIN modele md ON p.codeModele = md.idModele
			 LEFT JOIN ligne l ON p.codeLigne = l.idLigne  			 
			 LEFT JOIN description d ON p.refproduit = d.codeProduit  
			 WHERE codetarif='$codeTarif' AND nonCommandable > 0 AND stockdisponible > 0 ";
			if($souscenTheme == 1) {
				$req .= "AND codeMarque='$sousCentralClient' ";
			}
			
			if($cenLigne == 1) {
				$centrale = strtolower($centrale);
				$centrale = ucfirst($centrale);	
				($centrale === "Cka") ? $centrale = "City" : $centrale = $centrale;
				
				$req .= " AND codeLigne = (SELECT idLigne FROM ligne WHERE libelle LIKE '%".$_POST['langue']."$centrale%') OR codeLigne = (SELECT idLigne FROM ligne WHERE libelle LIKE '%".$_POST['langue']."All%') OR (codeLigne = (SELECT idLigne FROM ligne WHERE libelle LIKE '%".$_POST['langue']."Bou%' AND stockdisponible > 0) OR codeLigne = (SELECT idLigne FROM ligne WHERE libelle LIKE '%".$_POST['langue']."Sto%' AND stockdisponible > 0)) OR codeLigne = (SELECT idLigne FROM ligne WHERE libelle LIKE '%".$_POST['langue']."Tech%' AND stockdisponible > 0) ";
			}


			if($_POST["type"]=="selection"){
				$req .= "AND selection = 1 GROUP BY p.refproduit,tarif_promo";
			}
			if($_POST["type"]=="promo"){
				$req .= "AND promo = 1 GROUP BY p.refproduit,prix,tarif_promo ORDER BY pgp.position1";
			}

    if(isset($_POST['login'])){
        /* Permet de définir l'ordre d'affichage sur la page des produits */

        if(in_array($login,$login_array)){

            if($_POST["type"]=="login"){
                if($_POST["mode"]=="ligne"){
                    switch($ordreAffichage){
                        case "1":
							$req .= "ORDER BY prix asc";
                            break;
                        case "2":
							$req .= "ORDER BY prix desc";
                            break;
                        case "3":
							$req .= "ORDER BY libelle asc";
                            break;
                        case "4":
							$req .= "ORDER BY libelle desc";
                            break;
                        case "5":
							$req .= "ORDER BY positionGalerie";
                            break;
                    }

                } else if($_POST["mode"]=="tableau"){
					if($civilite == "M"){
						$codeC = "Homme";
					} else {
						$codeC = "Femme";
					}

					switch($ordreAffichage){
                        case "1":
                            $req .= "GROUP BY p.refproduit ORDER BY prix asc, promo DESC";
                            break;
                        case "2":
                            $req .= "GROUP BY p.refproduit ORDER BY prix desc, promo DESC";
                            break;
                        case "3":
                            $req .= "GROUP BY p.refproduit,prix ORDER BY libelle asc, promo DESC";
                            break;
                        case "4":
                            $req .= "GROUP BY p.refproduit ORDER BY libelle desc, promo DESC";
                            break;
                        case "5":
                            $req .= "GROUP BY p.refproduit,prix,tarif_promo ORDER BY positionGalerie";
                            break;
                    }

                }
            }
		}
	} else if(isset($_POST["visGalerie"]) && ($_POST['visGalerie']=='true')){
			switch($ordreAffichage){
				case "1":
					$req .= "GROUP BY p.refproduit ORDER BY prix asc, promo DESC";
					break;
				case "2":
					$req .= "GROUP BY p.refproduit ORDER BY prix desc, promo DESC";
					break;
				case "3":
					$req .= "GROUP BY p.refproduit,prix ORDER BY libelle asc, promo DESC";
					break;
				case "4":
					$req .= "GROUP BY p.refproduit ORDER BY libelle desc, promo DESC";
					break;
				case "5":
					$req .= "GROUP BY p.refproduit,prix,tarif_promo ORDER BY positionGalerie";
					break;
			}

    } else if($_POST["type"]=="gestion"){ //gestion des produits pour le drag & drop
       $req .= "GROUP BY p.refproduit,tarif_promo ORDER BY positionGalerie, promo DESC";
    }
	
	// echo $req;
	$reponse=$bdd->query($req);


	$i=1;
	while($donnees=$reponse->fetch()){

		$refProduit=str_replace('/','_',$donnees['refproduit']);
		$refProduit=str_replace(' ','_',$refProduit);

		$fichier= "../Photos/".$donnees['codeSaison'].$refProduit.".jpg";
		$fichier1= "../Photos/".$donnees['codeSaison'].$refProduit."-1.jpg";;
		$fichier2= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-1.jpg";
		$fichier3= "../Photos/".$donnees['codeSaison'].$refProduit."--0.jpg";
		$fichier4= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-0.jpg";
		$fichier5= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-".$donnees['libelle']."-1.jpg";
		$fichier6= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori'].".jpg";

		if (file_exists($fichier6)) {
			$fichier=$fichier6;
		} elseif(file_exists($fichier5)) {
			$fichier=$fichier5;
		} elseif(file_exists($fichier4)) {
			$fichier=$fichier4;
		} elseif(file_exists($fichier3)) {
			$fichier=$fichier3;
		} elseif(file_exists($fichier2)) {
			$fichier=$fichier2;
		} elseif(file_exists($fichier1)) {
			$fichier=$fichier1;
		} elseif(file_exists($fichier)) {
			$fichier=$fichier;
		} else {
			if (!file_exists($fichier)) {
				$fichier="../Images/no_image.png";
			}
		}



		//ZOOM
		$fichierZ= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-1-Z.jpg";
		if (file_exists($fichierZ)) {
		$fichierZoom=$fichierZ;
		} else {
		$fichierZoom=$fichier2;
		}

		//Afficher les photos du produits
		for($p=1;$p<6;$p++){
		$fichierMin="../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-".$p.".jpg";
			$arr2[$i]["imageMiniature"][$p] = $fichierMin;
		}

		$reponse2=$bdd->prepare("SELECT COUNT(refproduit) as nbRef FROM produit WHERE codetarif=:codetarif AND refproduit=:refproduit AND nonCommandable > 0 ");
		$reponse2->execute(array(
			"refproduit"=>$donnees["refproduit"],
			"codetarif"=>$codeTarif
		));
		$retour=$reponse2->fetch();
		$quant=$retour["nbRef"];
		$reponse2->closeCursor();
		$arr2[$i]["idproduit"]=$donnees["idproduit"];
		$arr2[$i]["refproduit"]=$donnees["refproduit"];
		$arr2[$i]["libelle"]=$donnees["libelle"];
		$arr2[$i]["libelleANG"]=$donnees["libelleLang"];
		$arr2[$i]["codeColori"]=$donnees["codeColori"];
		$arr2[$i]["nbColori"]=$nbColori;
		$arr2[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"];
		$arr2[$i]["codetailledebut"]=$donnees["codetailledebut"];
		$arr2[$i]["codetaillefin"]=$donnees["codetaillefin"];
		$arr2[$i]["codeSaison"]=$donnees["codeSaison"];
		$arr2[$i]["codeMarque"]=$donnees["codeMarque"];
		$arr2[$i]["codeTheme"]=$donnees["codeTheme"];
		$arr2[$i]["codeFamille"]=$donnees["codeFamille"];
		$arr2[$i]["codeSousFamille"]=$donnees["codeSousFamille"];
		$arr2[$i]["codeModele"]=$donnees["codeModele"];
		$arr2[$i]["refproduit"]=$donnees["refproduit"];
		$arr2[$i]["codeLigne"]=$donnees["codeLigne"];
		$arr2[$i]["nonCommendable"]=$donnees["nonCommandable"];
		$arr2[$i]["refproduit"]=$donnees["refproduit"];
		$arr2[$i]["poids"]=$donnees["poids"];
		$arr2[$i]["codetarif"]=$donnees["codetarif"];
		$arr2[$i]["prix"]=$donnees["prix"];
		$arr2[$i]["libcolori"]=$donnees["libcolori"];
		$arr2[$i]["libMarque"]=$donnees["libMarque"];
		$arr2[$i]["commentaire1"]=$donnees["commentaire1"];
		$arr2[$i]["commentaire2"]=$donnees["commentaire2"];
		$arr2[$i]["commentaire3"]=$donnees["commentaire3"];
		$arr2[$i]["commentaire4"]=$donnees["commentaire4"];
		$arr2[$i]["commentaire5"]=$donnees["commentaire5"];
		$arr2[$i]["selection"]=$donnees["selection"];
		$arr2[$i]["promo"]=$donnees["promo"];
		$arr2[$i]["tarif_promo"]=$donnees["tarif_promo"];
		$arr2[$i]["tarif_pvc"]=$donnees["tarif_pvc"];
		$arr2[$i]["libelle2"]=$donnees["libelle2"];
		$arr2[$i]["texteLibre"]=$donnees["texteLibre"];
		$arr2[$i]["codeTarifClient"]=$codeTarif;
		$arr2[$i]["nbRef"]=$quant;
		$arr2[$i]["ordreAffichage"]=$ordreAffichage;
		$arr2[$i]["tarif_pvc"]=$donnees["tarif_pvc"];
		$arr2[$i]["imageArt"]=$fichier;
		$arr2[$i]["imageZoom"]=$fichierZoom;
		$arr2[$i]["positionGalerie"] = 	(string)$i;

		if($_POST["type"]=="gestion"){
			$reponse5=$bdd->prepare("SELECT * FROM positiongalerie WHERE refproduit=:refproduit");
			$reponse5->execute(array(
				"refproduit"=>$donnees["refproduit"]
			));
			if($donnees5=$reponse5->fetch()) {
        $arrayposition[1] = $donnees5["position1"];
        $arrayposition[2] = $donnees5["position2"];
        $arrayposition[3] = $donnees5["position3"];
        $arrayposition[4] = $donnees5["position4"];
        $arrayposition[5] = $donnees5["position5"];
        $arrayposition[6] = $donnees5["position6"];
			} else {
        $arrayposition[1] = "99999";
        $arrayposition[2] = "99999";
        $arrayposition[3] = "99999";
        $arrayposition[4] = "99999";
        $arrayposition[5] = "99999";
        $arrayposition[6] = "99999";
			}
			$reponse5->closeCursor();

      $reponse5=$bdd->prepare("SELECT * FROM positiongaleriepromo WHERE refproduit=:refproduit");
      $reponse5->execute(array(
        "refproduit"=>$donnees["refproduit"]
      ));
      if($donnees5=$reponse5->fetch()) {
        $arrayposition[-1] = $donnees5["position1"];
        $arrayposition[-2] = $donnees5["position2"];
        $arrayposition[-3] = $donnees5["position3"];
        $arrayposition[-4] = $donnees5["position4"];
        $arrayposition[-5] = $donnees5["position5"];
        $arrayposition[-6] = $donnees5["position6"];
      } else {
        $arrayposition[-1] = "99999";
        $arrayposition[-2] = "99999";
        $arrayposition[-3] = "99999";
        $arrayposition[-4] = "99999";
        $arrayposition[-5] = "99999";
        $arrayposition[-6] = "99999";
      }
      $reponse5->closeCursor();
      $arr2[$i]["positionGalerie"] = $arrayposition;
    }

		$contientSelection = false;

		$reponse3=$bdd->prepare("SELECT libcolori,codeColori,tarif_pvc,tarif_promo,promo,refproduit,codeSaison,selection FROM produit WHERE codetarif=:codetarif AND refproduit=:refproduit AND nonCommandable > 0 AND stockdisponible > 0");
		$reponse3->execute(array(
			"refproduit"=>$donnees["refproduit"],
      "codetarif"=>$codeTarif
		));
		$j=0;
		$y=0;
		while($donnees3=$reponse3->fetch()){
			//ZOOM
			$fichierZ= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees3['codeColori']."-1-Z.jpg";
			$fichierA= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees3['codeColori']."-1.jpg";

			if (file_exists($fichierZ)) {
				$fichierZoom=$fichierZ;
				$fichierAR=$fichierA;
			} else {
				$fichierZoom=$fichier2;
				$fichierAR=$fichierA;
			}

			for($p=1;$p<6;$p++){
				$fichierMin="../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees3['codeColori']."-".$p.".jpg";
				if (file_exists($fichierMin)) {
					$arr2[$i]["arrayColori"][$j]["imageMiniature"][$p] = $fichierMin;
				}
			}

			$reponse4=$bdd->prepare("SELECT libelleColoris FROM colorisTraduction WHERE codeLangue ='ANG' AND codeColoris = :codeColoris");
			$reponse4->execute(array(
				"codeColoris"=> $donnees3["codeColori"]

			));
			$donnees4=$reponse4->fetch();

			$picto1 = "../Photos/Coloris/-".$donnees3["codeColori"]."-".strtoupper($donnees3["libcolori"])."-1.jpg";
			$picto2="../Photos/".$donnees3['codeSaison'].$donnees3['refproduit']."-".$donnees3['codeColori']."-1.jpg"; 
			$picto2SansCodeColori = "../Photos/".$donnees3['codeSaison'].$donnees3['refproduit']."-1.jpg"; //gerer le cas ou il ni a pas de code coloris
			$picto2SansTiret = "../Photos/".$donnees3['codeSaison'].$donnees3['refproduit']."-".$donnees3['codeColori'].".jpg"; //gerer le cas ou il ni a pas de tiret "-1" a la fin 

			$arr2[$i]["arrayColori"][$j]["imageZoom"]=$fichierZoom;
			$arr2[$i]["arrayColori"][$j]["image"]=$fichierAR;
			$arr2[$i]["arrayColori"][$j]["libcolori"]=$donnees3["libcolori"];
			$arr2[$i]["arrayColori"][$j]["libcoloriANG"]=$donnees4["libelleColoris"];
			$arr2[$i]["arrayColori"][$j]["tarif_pvc"]=$donnees3["tarif_pvc"];
			$arr2[$i]["arrayColori"][$j]["tarif_promo"]=$donnees3["tarif_promo"];
			$arr2[$i]["arrayColori"][$j]["codeColori"]=$donnees3["codeColori"];
			$arr2[$i]["arrayColori"][$j]["promo"]=$donnees3["promo"];
			$arr2[$i]["arrayColori"][$j]["selection"]=$donnees3["selection"];
			if(file_exists($picto2)){
				$arr2[$i]["arrayColori"][$j]["imageMiniature2"]=$picto2;
			}else if(file_exists($picto2SansCodeColori)){
				$arr2[$i]["arrayColori"][$j]["imageMiniature2"]=$picto2SansCodeColori;
			}else{
				$arr2[$i]["arrayColori"][$j]["imageMiniature2"]=$picto2SansTiret;
			}

			$arr2[$i]["arrayColori"][$j]["imageMiniature"]=$picto1;
			if($donnees3["selection"] === '1' && $donnees3["tarif_promo"] === $donnees["tarif_promo"]) {
				$contientSelection = true;
			}

			$j++;
			$reponse6=$bdd->prepare("SELECT idproduit,codetarif,prix,codeColori,promo,tarif_promo,tarif_pvc,libcolori FROM produit WHERE refproduit=:refproduit AND codeColori=:codeColori AND nonCommandable > 0 ORDER BY codetarif");

			//echo "SELECT idproduit,codetarif,prix,codeColori,promo,tarif_promo,tarif_pvc,libcolori FROM produit WHERE refproduit='".$donnees["refproduit"]."' AND codeColori='".$donnees3["codeColori"]."' AND nonCommandable > 0 ORDER BY codetarif";

			$reponse6->execute(array(
			"refproduit"=>$donnees["refproduit"],
			"codeColori"=>$donnees3["codeColori"]
			));
			while($donnees6=$reponse6->fetch()){
			  $arr2[$i]["arrayTarif"][$y]["codeColori"]		= $donnees6["codeColori"];
			  $arr2[$i]["arrayTarif"][$y]["idproduit"]		= $donnees6["idproduit"];
			  $arr2[$i]["arrayTarif"][$y]["codeTarif"]		= $donnees6["codetarif"];
			  $arr2[$i]["arrayTarif"][$y]["libcolori"]		= $donnees6["libcolori"];
			  $arr2[$i]["arrayTarif"][$y]["prix"]			= $donnees6["prix"];
			  $arr2[$i]["arrayTarif"][$y]["promo"]			= $donnees6["promo"];
			  $arr2[$i]["arrayTarif"][$y]["tarif_promo"]	= $donnees6["tarif_promo"];
			  $arr2[$i]["arrayTarif"][$y]["tarif_pvc"]		= $donnees6["tarif_pvc"];
			  $y++;
			}
			$reponse6->closeCursor();
		}
		if($contientSelection === true) {
		  $arr2[$i]["selection"]="1";
		} else if ($contientSelection === false) {
		  $arr2[$i]["selection"]="0";
		}
		$reponse3->closeCursor();
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
