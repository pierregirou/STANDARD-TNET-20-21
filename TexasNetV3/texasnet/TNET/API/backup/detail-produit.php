<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $arrdetail[0]["success"]=true; // success de la requête post

    /* Récupère le numéro de commande non valide de l'utilisateur connecté */
    $reponse=$bdd->prepare("SELECT numCommande FROM commande WHERE login=:login AND valid=0");
    $reponse->execute(array(
        "login"=>$_POST["login"]
    ));
    $retour=$reponse->fetch();
    $numCommande=$retour["numCommande"];
    $reponse->closeCursor();

			$reponse=$bdd->prepare("SELECT codeTarif FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$_POST["login"]
            ));
            while($donnees=$reponse->fetch()){
                $codeTarif=$donnees["codeTarif"];

            }
            $reponse->closeCursor();

    /* Récupère le nombre de coloris par produit */

    $reponse=$bdd->prepare("SELECT libcolori,codeColori FROM produit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND stockdisponible > 0");
    $reponse->execute(array(
        "refproduit"=>$_POST["refproduit"],
		"codeTarif"=>$codeTarif
    ));
    while($donnees=$reponse->fetch()){
        $reponse5=$bdd->prepare("SELECT libelleColoris FROM colorisTraduction WHERE codeLangue ='ANG' AND codeColoris = :codeColoris");
        $reponse5->execute(array(
            "codeColoris"=> $donnees["codeColori"]

        ));
        $donnees5=$reponse5->fetch();
        $libColorisArray[]=$donnees["libcolori"];
        $libColorisArrayANG[]=$donnees5["libelleColoris"];
        $libCodeColorisArray[]=$donnees["codeColori"];
        $libPictoArray[]="../../Photos/Coloris/-".$donnees["codeColori"]."-".strtoupper($donnees["libcolori"])."-1.jpg";
    }


    $arrdetail[1]["libcoloris"]=$libColorisArray;
    $arrdetail[1]["libcolorisANG"]=$libColorisArrayANG;
    $arrdetail[1]["codeColoris"]=$libCodeColorisArray;
    $arrdetail[1]["pictogramme"]=$libPictoArray;
    $reponse->closeCursor();

    //Déterminer la taille début et la taille de fin du produit


    $reponse=$bdd->prepare("SELECT codetailledebut,codetaillefin,codeGammeTaille,codeSaison,codeMarque FROM produit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND stockdisponible > 0 AND nonCommandable > 0");
    $reponse->execute(array(
        "refproduit"=>$_POST["refproduit"],
		"codeTarif"=>$codeTarif
    ));
    $l=0;
    while($donnees=$reponse->fetch()){

        $codeSaison=$donnees['codeSaison'];
        $codeMarque=$donnees['codeMarque'];
        $arrdetail[2]["codetailledebut"]=$donnees["codetailledebut"];
        $arrdetail[2]["codetaillefin"]=$donnees["codetaillefin"];
        $arrdetail[2]["scale"]=$donnees["codetaillefin"]-$donnees["codetailledebut"];
        $arrdetail[2]["codeGammeTaille"]=$donnees["codeGammeTaille"];
        (int)$dep=$donnees["codetailledebut"];
        $m=0;
        while($dep<=$donnees["codetaillefin"]){
            $reponse2=$bdd->prepare("SELECT libelle FROM taille WHERE codetaille=:codetaille AND codegamme=:codegamme");
            $reponse2->execute(array(
                "codetaille"=>(int)$dep,
                "codegamme"=>$donnees["codeGammeTaille"]
            ));
            while($donnees2=$reponse2->fetch()){
                $arrdetail[2]["tailleDebFin"][$m]=$donnees2["libelle"];
                $m++;
            }
            $dep++;

        }
    }
    $reponse->closeCursor();

    //récupère la gamme de taille du produit --> PAN, XXS...
    $reponse=$bdd->prepare("SELECT codeGammeTaille FROM detailproduit WHERE codetarif=:codeTarif AND refproduit=:refproduit");
    $reponse->execute(array(
        "refproduit"=>$_POST["refproduit"],
		"codeTarif"=>$codeTarif
    ));
    while($donnees=$reponse->fetch()){
        $arrdetail[3]["codegamme"]=$donnees["codeGammeTaille"];
    }

    $reponse->closeCursor();

    /*récupère toutes les tailles du produit*/
    $reponse=$bdd->prepare("SELECT DISTINCT codeColori,tarif_promo FROM produit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND stockdisponible > 0 AND nonCommandable > 0");
    $reponse->execute(array(
        "refproduit"=>$_POST["refproduit"],
		"codeTarif"=>$codeTarif
    ));
    $k=0;
    while($donnees=$reponse->fetch()){
        $reponse2=$bdd->prepare("SELECT * FROM detailproduit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND codeColori=:codeColori");
        $reponse2->execute(array(
            "refproduit"=>$_POST["refproduit"],
            "codeColori"=>$donnees["codeColori"],
			"codeTarif"=>$codeTarif
        ));
        $j=0;
        $montantLigne = 0;
        $nbrLigne = 0;
        while($donnees2=$reponse2->fetch()){
            $reponse3=$bdd->prepare("SELECT * FROM taille WHERE codetaille=:codetaille AND codegamme=:codegamme");
            $reponse3->execute(array(
                "codetaille"=>$donnees2["codetaille"],
                "codegamme"=>$donnees2["codeGammeTaille"]
            ));
            $quantiteLigne = 0;
            while($donnees3=$reponse3->fetch()){
                $reponse4=$bdd->prepare("SELECT p.libcolori,p.codeColori,p.selection, p.texteLibre,
                  substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) as codeMarque,
                  substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme,
                  substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille,
                  substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3,locate('/',substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3))-1) as codeSousFamille,
                  substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3,locate('/',substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3))-1) as codeModele,
                  substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne
              FROM produit p
              LEFT JOIN marque mq ON p.codeMarque = mq.idMarque
              LEFT JOIN theme t ON p.codeTheme = t.idTheme
              LEFT JOIN famille f ON p.codeFamille = f.idFamille
              LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille
              LEFT JOIN modele md ON p.codeModele = md.idModele
              LEFT JOIN ligne l ON p.codeLigne = l.idLigne
              WHERE codetarif=:codeTarif AND refproduit=:refproduit AND codeColori=:codeColori AND stockdisponible > 0 AND nonCommandable > 0");
                $reponse4->execute(array(
                    "refproduit"=>$_POST["refproduit"],
                    "codeColori"=>$donnees["codeColori"],
					          "codeTarif"=>$codeTarif
                ));
                $donnees4=$reponse4->fetch();

                $refProduit=str_replace('/','_',$_POST["refproduit"]);
                $refProduit=str_replace(' ','_',$refProduit);

                $fichier= "../Photos/".$codeSaison.$refProduit.".jpg";
                $fichier1= "../Photos/".$codeSaison.$refProduit."-1.jpg";;
                $fichier2= "../Photos/".$codeSaison.$refProduit."-".$donnees['codeColori']."-1.jpg";
                $fichier3= "../Photos/".$codeSaison.$refProduit."--0.jpg";
                $fichier4= "../Photos/".$codeSaison.$refProduit."-".$donnees['codeColori']."-0.jpg";
                $fichier5= "../Photos/".$codeSaison.$refProduit."-".$donnees['codeColori']."-".$donnees3["libelle"]."-1.jpg";
                $fichier6= "../Photos/".$codeSaison.$refProduit."-".$donnees['codeColori'].".jpg";

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
                        $fichier="../../Images/no_image.png";
                    }
                }

                $arrdetail[4][$k][$j]["langueSelect"]=$_POST['langue'];
                $arrdetail[4][$k][$j]["taille"]=$donnees3["libelle"];
                $arrdetail[4][$k][$j]["prix"]=$donnees2["prix"];
                $arrdetail[4][$k][$j]["stockdisponible"]=$donnees2["stockdisponible"];
                $arrdetail[4][$k][$j]["stockencmd"]=$donnees2["stockencmd"];
                $arrdetail[4][$k][$j]["stockaterme"]=$donnees2["stockaterme"];
                $arrdetail[4][$k][$j]["idproduit"]=$donnees2["idproduit"];
                $arrdetail[4][$k][$j]["codeMarque"]=$donnees4["codeMarque"];
                $arrdetail[4][$k][$j]["codeModele"]=$donnees4["codeModele"];
                $arrdetail[4][$k][$j]["codeTheme"]=$donnees4["texteLibre"];
                $arrdetail[4][$k][$j]["codeLigne"]=$donnees4["codeLigne"];
                $arrdetail[4][$k][$j]["codeFamille"]=$donnees4["codeFamille"];
                $arrdetail[4][$k][$j]["codeSFamille"]=$donnees4["codeSousFamille"];
                $arrdetail[4][$k][$j]["codeSaison"]=$codeSaison;
                $arrdetail[4][$k][$j]["libelleColoris"]=$donnees4["libcolori"];
                $arrdetail[4][$k][$j]["codeColoris"]=$donnees4["codeColori"];
                $arrdetail[4][$k][$j]["tarif_promo"]=$donnees["tarif_promo"];
                $arrdetail[4][$k][$j]["tarif_promoL"]=$donnees2["tarif_promoL"];
                $arrdetail[4][$k][$j]["selection"]=$donnees4["selection"];
                $arrdetail[4][$k][$j]["imageArt"]=$fichier;

                $reponse4=$bdd->prepare("SELECT * FROM lignecommande WHERE idDetailProduit=:idDetailProduit AND numCommande=:numCommande");
                $reponse4->execute(array(
                    "idDetailProduit"=>$donnees2["idproduit"],
                    "numCommande"=>$numCommande
                ));
                $retour=$reponse4->fetch();
                $quantite=$retour["quantite"];
                $reponse4->closeCursor();
                $arrdetail[4][$k][$j]["quantite"]=$quantite;
                $quantiteLigne += $quantite;
                $nbrLigne += $quantite;
                $j++;

            }
            if ($donnees["tarif_promo"] > 0){
                $montantProduit = $donnees["tarif_promo"];
            } elseif ($donnees2["tarif_promoL"] > 0){
                $montantProduit = $donnees2["tarif_promoL"];
            } else {
                $montantProduit = $donnees2["prix"];
            }

            $montantLigne+=$quantiteLigne*$montantProduit;
            $reponse3->closeCursor();
        }
        if ($montantLigne > 0 ){
            $arrdetail[4][$k][$j]["montantLigne"]=$montantLigne;
            $arrdetail[4][$k][$j]["quantiteLigne"]= $nbrLigne;
        } else {
            $arrdetail[4][$k][$j]["montantLigne"]=0;
            $arrdetail[4][$k][$j]["quantiteLigne"]=0;
        }
        $k++;
        $reponse2->closeCursor();
    }
    $reponse->closeCursor();

    /* Description d'une fiche article (Saison RefProduit) */


    $reponse=$bdd->prepare("SELECT * FROM `description` WHERE codeProduit=:codeproduit AND codeSaison=:codesaison");
    $reponse->execute(array(
        "codeproduit"=>$_POST["refproduit"],
        "codesaison"=>$codeSaison
    ));
    while($donnees=$reponse->fetch()){
		$codeLangue = $donnees['codeLangue'];
        if($donnees['codeLangue'] === "FRA"){
			$arrdetail[5][$codeLangue]=((stripslashes($donnees['description'])));
		} elseif($donnees['codeLangue'] === "ANG"){
			$arrdetail[5][$codeLangue]=((stripslashes($donnees['description'])));
		}
	}
    $reponse->closeCursor();

    /* Récupère les détails des produits */
    $reponse=$bdd->prepare("SELECT * FROM detailproduit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND stockdisponible > 0");
    $reponse->execute(array(
        "refproduit"=>$_POST["refproduit"],
		"codeTarif"=>$codeTarif
    ));
    $i=6;
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
                $fichier="../../Images/no_image.png";
            }
        }

            // On recherche aussi la photo sans coloris car elle doit etre selectionnable dans les miniatures
        $fichierMin="../Photos/000".$refProduit."-".$donnees['codeColori'].".jpg";
        if(file_exists($fichierMin)){
            $tableauPhotoMini[] = $fichierMin;
        }

        //Afficher les photos du produits
        for($p=1;$p<6;$p++){
            $fichierMin="../Photos/000".$refProduit."-".$donnees['codeColori']."-".$p.".jpg";
            if(file_exists($fichierMin)){
                $tableauPhotoMini[] = $fichierMin;
            }
        }

        $arrdetail[$i]["idproduit"]=$donnees["idproduit"];
        $arrdetail[$i]["codeColori"]=$donnees["codeColori"];
        $arrdetail[$i]["codetailledebut"]=$donnees["codetailledebut"];
        $arrdetail[$i]["codetaillefin"]=$donnees["codetaillefin"];
        $arrdetail[$i]["nonCommandable"]=$donnees["nonCommandable"];
        $arrdetail[$i]["codetaille"]=$donnees["codetaille"];
        $arrdetail[$i]["prix"]=$donnees["prix"];
        $arrdetail[$i]["codetarif"]=$donnees["codetarif"];
        $arrdetail[$i]["codeean13"]=$donnees["codeean13"];
        $arrdetail[$i]["stockdisponible"]=$donnees["stockdisponible"];
        $arrdetail[$i]["stockencmd"]=$donnees["stockencmd"];
        $arrdetail[$i]["stockaterme"]=$donnees["stockaterme"];
        $arrdetail[$i]["imageMiniature"]=$tableauPhotoMini;
        $i++;
    }
    $reponse->closeCursor();
    echo json_encode($arrdetail);
}else{
    ?>
    {
        "success":false,
        "message":"Only POST request allowed"
    }
    <?php
}

?>
