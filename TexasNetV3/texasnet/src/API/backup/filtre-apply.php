<?php

include('connect.php');

$login_array=[];
$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
    $login_array[]= $donnees["login"];
}
$reponse->closeCursor();
$reponse=$bdd->query("SELECT visGalerie FROM modules");
$retour=$reponse->fetch();
$visGalerie=$retour["visGalerie"];
$reponse->closeCursor();
$reponse=$bdd->query("SELECT souscenTheme FROM modules");
$retour=$reponse->fetch();
$souscenTheme=$retour["souscenTheme"];
$reponse->closeCursor();
$reponse=$bdd->query("SELECT ordreAffichage FROM modules");
$retour=$reponse->fetch();
$ordreAffichage=$retour["ordreAffichage"];
$reponse->closeCursor();

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    if(isset($_POST['login'])){
        $login=$_POST['login'];
        $reponse=$bdd->prepare("SELECT codetarif FROM client WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        $retour=$reponse->fetch();
        $codetarif=$retour["codetarif"];
        $reponse->closeCursor();
        if(in_array($login,$login_array)){
            if($_POST["type"]=="colori"){
                $arrColori[0]=true;
                $i=1;
                foreach($_POST["array"] as $colori){
                    if($_POST["mode"]=="ligne"){
                        $reponse=$bdd->prepare("SELECT * FROM produit WHERE codeTarif=:codeTarif AND libcolori=:libcolori ORDER BY positionGalerie");
                        $reponse->execute(array(
                            "codeTarif"=>$codetarif,
                            "libcolori"=>$colori
                        ));
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
                          //Afficher les photos du produits
                          for($p=1;$p<6;$p++){
                          $fichierMin="../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-".$p.".jpg";
                          $arrColori[$i]["imageMiniature"][$p] = $fichierMin;
                          }

                            $arrColori[$i]["idproduit"]=$donnees["idproduit"];
                            $arrColori[$i]["refproduit"]=$donnees["refproduit"];
                            $arrColori[$i]["libelle"]=$donnees["libelle"];
                            $arrColori[$i]["codeColori"]=$donnees["codeColori"];
                            $arrColori[$i]["nbColori"]=$nbColori;
                            $arrColori[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"];
                            $arrColori[$i]["codetailledebut"]=$donnees["codetailledebut"];
                            $arrColori[$i]["codetaillefin"]=$donnees["codetaillefin"];
                            $arrColori[$i]["codeSaison"]=$donnees["codeSaison"];
                            $arrColori[$i]["codeMarque"]=$donnees["codeMarque"];
                            $arrColori[$i]["codeTheme"]=$donnees["codeTheme"];
                            $arrColori[$i]["codeFamille"]=$donnees["codeFamille"];
                            $arrColori[$i]["codeSousFamille"]=$donnees["codeSousFamille"];
                            $arrColori[$i]["codeModele"]=$donnees["codeModele"];
                            $arrColori[$i]["refproduit"]=$donnees["refproduit"];
                            $arrColori[$i]["codeLigne"]=$donnees["codeLigne"];
                            $arrColori[$i]["nonCommendable"]=$donnees["nonCommendable"];
                            $arrColori[$i]["refproduit"]=$donnees["refproduit"];
                            $arrColori[$i]["poids"]=$donnees["poids"];
                            $arrColori[$i]["codetarif"]=$donnees["codetarif"];
                            $arrColori[$i]["prix"]=$donnees["prix"];
                            $arrColori[$i]["libcolori"]=$donnees["libcolori"];
                            $arrColori[$i]["libMarque"]=$donnees["libMarque"];
                            $arrColori[$i]["commentaire1"]=$donnees["commentaire1"];
                            $arrColori[$i]["commentaire2"]=$donnees["commentaire2"];
                            $arrColori[$i]["commentaire3"]=$donnees["commentaire3"];
                            $arrColori[$i]["commentaire4"]=$donnees["commentaire4"];
                            $arrColori[$i]["commentaire5"]=$donnees["commentaire5"];
                            $arrColori[$i]["selection"]=$donnees["selection"];
                            $arrColori[$i]["promo"]=$donnees["promo"];
                            $arrColori[$i]["tarif_promo"]=$donnees["tarif_promo"];
                            $arrColori[$i]["tarif_pvc"]=$donnees["tarif_pvc"];
                            $arrColori[$i]["libelle2"]=$donnees["libelle2"];
                            $arrColori[$i]["texteLibre"]=$donnees["texteLibre"];
                            $arrColori[$i]["positionGalerie"]=(string)$i;
                            $arrColori[$i]["codeTarifClient"]=$codeTarif;
                            $arrColori[$i]["ordreAffichage"]=$ordreAffichage;
                            $arrColori[$i]["imageArt"]=$fichier;
                            $reponse2=$bdd->prepare("SELECT libcolori FROM produit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND nonCommandable > 0");
                            $reponse2->execute(array(
                                "refproduit"=>$donnees["refproduit"],
                                "codeTarif"=>$codeTarif
                            ));
                            $j=0;
                            while($donnees2=$reponse2->fetch()){
                                $arrColori[$i]["arrayColori"][$j]=$donnees2["libcolori"];
                                $j++;
                            }
                            $reponse2->closeCursor();
                            $i++;
                        }
                    }
                    if($_POST["mode"]=="tableau"){
                        $reponse=$bdd->prepare("SELECT * FROM produit WHERE codetarif=:codeTarif AND libcolori=:libcolori GROUP BY refproduit ORDER BY positionGalerie");
                        $reponse->execute(array(
                            "codeTarif"=>$codetarif,
                            "libcolori"=>$colori
                        ));
                        while($donnees=$reponse->fetch()){
                            $reponse2=$bdd->prepare("SELECT COUNT(refproduit) as nbRef FROM produit WHERE refproduit=:refproduit");
                            $reponse2->execute(array(
                                "refproduit"=>$donnees["refproduit"]
                            ));
                            $retour=$reponse2->fetch();
                            $quant=$retour["nbRef"];
                            $reponse2->closeCursor();
                            $arrColori[$i]["idproduit"]=$donnees["idproduit"];
                            $arrColori[$i]["refproduit"]=$donnees["refproduit"];
                            $arrColori[$i]["libelle"]=$donnees["libelle"];
                            $arrColori[$i]["codeColori"]=$donnees["codeColori"];
                            $arrColori[$i]["nbColori"]=$nbColori;
                            $arrColori[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"];
                            $arrColori[$i]["codetailledebut"]=$donnees["codetailledebut"];
                            $arrColori[$i]["codetaillefin"]=$donnees["codetaillefin"];
                            $arrColori[$i]["codeSaison"]=$donnees["codeSaison"];
                            $arrColori[$i]["codeMarque"]=$donnees["codeMarque"];
                            $arrColori[$i]["codeTheme"]=$donnees["codeTheme"];
                            $arrColori[$i]["codeFamille"]=$donnees["codeFamille"];
                            $arrColori[$i]["codeSousFamille"]=$donnees["codeSousFamille"];
                            $arrColori[$i]["codeModele"]=$donnees["codeModele"];
                            $arrColori[$i]["refproduit"]=$donnees["refproduit"];
                            $arrColori[$i]["codeLigne"]=$donnees["codeLigne"];
                            $arrColori[$i]["nonCommendable"]=$donnees["nonCommendable"];
                            $arrColori[$i]["refproduit"]=$donnees["refproduit"];
                            $arrColori[$i]["poids"]=$donnees["poids"];
                            $arrColori[$i]["codetarif"]=$donnees["codetarif"];
                            $arrColori[$i]["prix"]=$donnees["prix"];
                            $arrColori[$i]["libcolori"]=$donnees["libcolori"];
                            $arrColori[$i]["libMarque"]=$donnees["libMarque"];
                            $arrColori[$i]["commentaire1"]=$donnees["commentaire1"];
                            $arrColori[$i]["commentaire2"]=$donnees["commentaire2"];
                            $arrColori[$i]["commentaire3"]=$donnees["commentaire3"];
                            $arrColori[$i]["commentaire4"]=$donnees["commentaire4"];
                            $arrColori[$i]["commentaire5"]=$donnees["commentaire5"];
                            $arrColori[$i]["selection"]=$donnees["selection"];
                            $arrColori[$i]["promo"]=$donnees["promo"];
                            $arrColori[$i]["tarif_promo"]=$donnees["tarif_promo"];
                            $arrColori[$i]["positionGalerie"]=(string)$i;
                            $arrColori[$i]["codeTarifClient"]=$codeTarif;
                            $arrColori[$i]["nbRef"]=$quant;
                            $reponse2=$bdd->prepare("SELECT libcolori FROM produit WHERE refproduit=:refproduit");
                            $reponse2->execute(array(
                                "refproduit"=>$donnees["refproduit"]
                            ));
                            $j=0;
                            while($donnees2=$reponse2->fetch()){
                                $arrColori[$i]["arrayColori"][$j]=$donnees2["libcolori"];
                                $j++;
                            }
                            $reponse2->closeCursor();


                            $i++;
                        }
                    }
                }

                echo json_encode($arrColori);
            }
            if($_POST["type"]=="taille"){
                $arrTaille[0]=$_POST["array"];
                $i=1;
                $j=0;
                if($_POST["mode"]=="ligne"){
                    foreach($arrTaille[0] as $tailles){
                        $reponse=$bdd->prepare("SELECT idproduit FROM produit WHERE codetarif=:codetarif AND refproduit IN (
                                                    SELECT refproduit FROM detailproduit WHERE codetaille IN (
                                                        SELECT codetaille FROM taille WHERE codegamme=:codegamme AND libelle=:libelle
                                                        ) AND codeGammeTaille=:codeGammeTaille
                                                    )");
                        $reponse->execute(array(
                            "codetarif"=>$codetarif,
                            "codegamme"=>$tailles[0],
                            "libelle"=>$tailles[1],
                            "codeGammeTaille"=>$tailles[0]
                        ));
                        while($donnees=$reponse->fetch()){
                            $idproduit[$j]=$donnees["idproduit"];
                            $j++;
                        }
                        $reponse->closeCursor();
                    }
                    foreach(array_unique($idproduit) as $id){
                        $reponse=$bdd->prepare("SELECT * FROM produit WHERE idproduit=:idproduit ORDER BY positionGalerie");
                        $reponse->execute(array(
                            "idproduit"=>$id
                        ));
                        while($donnees=$reponse->fetch()){
                            $arrTaille[$i]["idproduit"]=$donnees["idproduit"];
                            $arrTaille[$i]["refproduit"]=$donnees["refproduit"];
                            $arrTaille[$i]["libelle"]=$donnees["libelle"];
                            $arrTaille[$i]["codeColori"]=$donnees["codeColori"];
                            $arrTaille[$i]["nbColori"]=$nbColori;
                            $arrTaille[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"];
                            $arrTaille[$i]["codetailledebut"]=$donnees["codetailledebut"];
                            $arrTaille[$i]["codetaillefin"]=$donnees["codetaillefin"];
                            $arrTaille[$i]["codeSaison"]=$donnees["codeSaison"];
                            $arrTaille[$i]["codeMarque"]=$donnees["codeMarque"];
                            $arrTaille[$i]["codeTheme"]=$donnees["codeTheme"];
                            $arrTaille[$i]["codeFamille"]=$donnees["codeFamille"];
                            $arrTaille[$i]["codeSousFamille"]=$donnees["codeSousFamille"];
                            $arrTaille[$i]["codeModele"]=$donnees["codeModele"];
                            $arrTaille[$i]["refproduit"]=$donnees["refproduit"];
                            $arrTaille[$i]["codeLigne"]=$donnees["codeLigne"];
                            $arrTaille[$i]["nonCommendable"]=$donnees["nonCommendable"];
                            $arrTaille[$i]["refproduit"]=$donnees["refproduit"];
                            $arrTaille[$i]["poids"]=$donnees["poids"];
                            $arrTaille[$i]["codetarif"]=$donnees["codetarif"];
                            $arrTaille[$i]["prix"]=$donnees["prix"];
                            $arrTaille[$i]["libcolori"]=$donnees["libcolori"];
                            $arrTaille[$i]["libMarque"]=$donnees["libMarque"];
                            $arrTaille[$i]["commentaire1"]=$donnees["commentaire1"];
                            $arrTaille[$i]["commentaire2"]=$donnees["commentaire2"];
                            $arrTaille[$i]["commentaire3"]=$donnees["commentaire3"];
                            $arrTaille[$i]["commentaire4"]=$donnees["commentaire4"];
                            $arrTaille[$i]["commentaire5"]=$donnees["commentaire5"];
                            $arrTaille[$i]["selection"]=$donnees["selection"];
                            $arrTaille[$i]["promo"]=$donnees["promo"];
                            $arrTaille[$i]["tarif_promo"]=$donnees["tarif_promo"];
                            $arrTaille[$i]["positionGalerie"]=(string)$i;
                            $arrTaille[$i]["codeTarifClient"]=$codeTarif;
                            $i++;
                        }
                        $reponse->closeCursor();
                    }
                    echo json_encode($arrTaille);
                }
                if($_POST["mode"]=="tableau"){
                    foreach($arrTaille[0] as $tailles){
                        $reponse=$bdd->prepare("SELECT idproduit FROM produit WHERE codetarif=:codetarif AND refproduit IN (
                                                    SELECT refproduit FROM detailproduit WHERE codetaille IN (
                                                        SELECT codetaille FROM taille WHERE codegamme=:codegamme AND libelle=:libelle
                                                        ) AND codeGammeTaille=:codeGammeTaille
                                                    )");
                        $reponse->execute(array(
                            "codetarif"=>$codetarif,
                            "codegamme"=>$tailles[0],
                            "libelle"=>$tailles[1],
                            "codeGammeTaille"=>$tailles[0]
                        ));
                        while($donnees=$reponse->fetch()){
                            $idproduit[$j]=$donnees["idproduit"];
                            $j++;
                        }
                        $reponse->closeCursor();
                    }
                    foreach(array_unique($idproduit) as $id){
                        $reponse=$bdd->prepare("SELECT * FROM produit WHERE idproduit=:idproduit GROUP BY refproduit ORDER BY positionGalerie");
                        $reponse->execute(array(
                            "idproduit"=>$id
                        ));
                        while($donnees=$reponse->fetch()){
                            $reponse2=$bdd->prepare("SELECT COUNT(refproduit) as nbRef FROM produit WHERE refproduit=:refproduit");
                            $reponse2->execute(array(
                                "refproduit"=>$donnees["refproduit"]
                            ));
                            $retour=$reponse2->fetch();
                            $quant=$retour["nbRef"];
                            $reponse2->closeCursor();
                            $arrTaille[$i]["idproduit"]=$donnees["idproduit"];
                            $arrTaille[$i]["refproduit"]=$donnees["refproduit"];
                            $arrTaille[$i]["libelle"]=$donnees["libelle"];
                            $arrTaille[$i]["codeColori"]=$donnees["codeColori"];
                            $arrTaille[$i]["nbColori"]=$nbColori;
                            $arrTaille[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"];
                            $arrTaille[$i]["codetailledebut"]=$donnees["codetailledebut"];
                            $arrTaille[$i]["codetaillefin"]=$donnees["codetaillefin"];
                            $arrTaille[$i]["codeSaison"]=$donnees["codeSaison"];
                            $arrTaille[$i]["codeMarque"]=$donnees["codeMarque"];
                            $arrTaille[$i]["codeTheme"]=$donnees["codeTheme"];
                            $arrTaille[$i]["codeFamille"]=$donnees["codeFamille"];
                            $arrTaille[$i]["codeSousFamille"]=$donnees["codeSousFamille"];
                            $arrTaille[$i]["codeModele"]=$donnees["codeModele"];
                            $arrTaille[$i]["refproduit"]=$donnees["refproduit"];
                            $arrTaille[$i]["codeLigne"]=$donnees["codeLigne"];
                            $arrTaille[$i]["nonCommendable"]=$donnees["nonCommendable"];
                            $arrTaille[$i]["refproduit"]=$donnees["refproduit"];
                            $arrTaille[$i]["poids"]=$donnees["poids"];
                            $arrTaille[$i]["codetarif"]=$donnees["codetarif"];
                            $arrTaille[$i]["prix"]=$donnees["prix"];
                            $arrTaille[$i]["libcolori"]=$donnees["libcolori"];
                            $arrTaille[$i]["libMarque"]=$donnees["libMarque"];
                            $arrTaille[$i]["commentaire1"]=$donnees["commentaire1"];
                            $arrTaille[$i]["commentaire2"]=$donnees["commentaire2"];
                            $arrTaille[$i]["commentaire3"]=$donnees["commentaire3"];
                            $arrTaille[$i]["commentaire4"]=$donnees["commentaire4"];
                            $arrTaille[$i]["commentaire5"]=$donnees["commentaire5"];
                            $arrTaille[$i]["selection"]=$donnees["selection"];
                            $arrTaille[$i]["promo"]=$donnees["promo"];
                            $arrTaille[$i]["tarif_promo"]=$donnees["tarif_promo"];
                            $arrTaille[$i]["positionGalerie"]=(string)$i;
                            $arrTaille[$i]["codeTarifClient"]=$codeTarif;
                            $arrTaille[$i]["nbRef"]=$quant;
                            $reponse2=$bdd->prepare("SELECT libcolori FROM produit WHERE refproduit=:refproduit");
                            $reponse2->execute(array(
                                "refproduit"=>$donnees["refproduit"]
                            ));
                            $j=0;
                            while($donnees2=$reponse2->fetch()){
                                $arrTaille[$i]["arrayColori"][$j]=$donnees2["libcolori"];
                                $j++;
                            }
                            $reponse2->closeCursor();
                            $i++;
                        }
                        $reponse->closeCursor();
                    }
                    echo json_encode($arrTaille);
                }
            }
        }
    }else{
        ?>
        {
            "success":false,
            "message":"mauvais login"
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
