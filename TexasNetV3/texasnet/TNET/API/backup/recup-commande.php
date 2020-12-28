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
    $pieces=$retour["nbrpiece"];
    $numCommande=$retour["numCommande"];
    $fraisport=$retour["fraisport"];
    $escompte=$retour["escompte"];
    $mttva=$retour["mttva"];
    $mtTPH=$retour["mtTPH"];
    $reponse->closeCursor();

    $reponseLogin=$bdd->prepare("SELECT * FROM client WHERE login=:login");
    $reponseLogin->execute(array(
        "login"=>$_POST["login"]
    ));
    $retourLogin=$reponseLogin->fetch();
    $codeTarifClient = $retourLogin['codeTarif'];

    /* Récupère le mode de saisie 1-->ligne 2-->tableau */
    $reponse=$bdd->query("SELECT modeSaisie FROM modules");
    $retour=$reponse->fetch();
    $modeSaisie=$retour["modeSaisie"];
    $reponse->closeCursor();

    $arrcommande[1]["montant"]=$montant;
    $arrcommande[1]["pieces"]=$pieces;
    $arrcommande[1]["numCommande"]=$numCommande;
    $arrcommande[1]["fraisport"]=$fraisport;
    $arrcommande[1]["escompte"]=$escompte;
    $arrcommande[1]["mttva"]=$mttva;
    $arrcommande[1]["mtTPH"]=$mtTPH;

    $refArray=[];

    $reponse=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCommande");
    $reponse->execute(array(
        "numCommande"=>$numCommande
    ));
    $i=0;
    while($donnees=$reponse->fetch()){
        $reponse2=$bdd->prepare("SELECT refproduit, codetaille, codeGammeTaille , stockdisponible, codeColori FROM detailproduit WHERE idproduit=:idproduit");
        $reponse2->execute(array(
            "idproduit"=>$donnees["idDetailProduit"]
        ));
        $retour2=$reponse2->fetch();
        $refproduit=$retour2["refproduit"];
        $codeColory=$retour2["codeColori"];
        $blocageDoublon=$refproduit.$donnees["codeColori"];
        if(!in_array($blocageDoublon,$refArray) || $modeSaisie==="1"){
            $refArray[$i]=$blocageDoublon;
            $codetaille=$retour2["codetaille"];
            $codeGammeTaille=$retour2["codeGammeTaille"];
            $stockdisponible=$retour2["stockdisponible"];
            $codeColoris=$retour2["codeColori"];
            $reponse2->closeCursor();

            $reponse2=$bdd->prepare("SELECT libelle FROM taille WHERE codetaille=:codetaille AND codegamme=:codegamme");
            $reponse2->execute(array(
                "codetaille"=>$codetaille,
                "codegamme"=>$codeGammeTaille
            ));
            $retour2=$reponse2->fetch();
            $taille=$retour2["libelle"];
            $reponse2->closeCursor();

            $reponse2=$bdd->prepare("SELECT * FROM produit WHERE refproduit=:refproduit AND stockdisponible > 0 AND nonCommandable > 0 AND codeColori=:codeColori");
            $reponse2->execute(array(
                "refproduit"=>$refproduit,
                "codeColori"=>$codeColoris
            ));
            $retour2=$reponse2->fetch();
            $libelle=$retour2["libelle"];
            $idProduitGlobal=$retour2["idproduit"];
            $saison=$retour2["codeSaison"];
            $tailleMin=$retour2["codetailledebut"];
            $tailleMax=$retour2["codetaillefin"];
            $reponse2->closeCursor();

            $reponse2=$bdd->prepare("SELECT DISTINCT T.codetaille, T.libelle
              FROM taille T
              INNER JOIN produit P
              ON refproduit=:refproduit
              AND stockdisponible > 0
              AND nonCommandable > 0
              WHERE codetaille
              IN (:taillemin, :taillemax)
              AND codegamme=:codegamme
              ORDER BY codetaille ASC");
            $reponse2->execute(array(
                "refproduit"=>$refproduit,
                "codegamme"=>$codeGammeTaille,
                "taillemin"=>$tailleMin,
                "taillemax"=>$tailleMax
            ));
            $retour2=$reponse2->fetch();
            $tailleMin=$retour2["libelle"];
            $retour2=$reponse2->fetch();
            $tailleMax=$retour2["libelle"];
            $reponse2->closeCursor();

            /* Si le mode de saisie est à 2 -->tableau indique le nombre de produits ajouté au panier en fct de la ref */
            $reponse2=$bdd->prepare("SELECT DISTINCT lc.prix, SUM(lc.quantite) as quantTab, dp.idproduit as idDpPanier, dp.refproduit,dp.codeColori FROM lignecommande lc, detailproduit dp WHERE dp.idproduit = lc.idDetailProduit AND dp.refproduit = :refproduit AND lc.numCommande=:numCommande GROUP BY lc.prix");

            $reponse2->execute(array(
                "numCommande"=>$numCommande,
                "refproduit"=>$refproduit
            ));
            while($retour=$reponse2->fetch()) {
              $quantiteTabprod=$retour["quantTab"];
              $total=$retour["prix"]*$quantiteTabprod;
              $arraySelect=array(); //array pour connaitre le stock disponible sur mobile
              $o=0;
              $l=1;
              /* Pour les mobiles permet de choisir la quantité de produits à commander */
              while($o<$stockdisponible){
                  $arraySelect[$o]=(string)$l; /* L'array prend autant de valeurs en fonction du stock disponible s'incrémente de 1 à chaque fois */
                  $o++;
                  $l++;
              }

                  $refProduit=str_replace('/','_',$refproduit);
                  $refProduit=str_replace(' ','_',$refProduit);

                  $fichier= "../Photos/".$saison.$refProduit.".jpg";
                  $fichier1= "../Photos/".$saison.$refProduit."-1.jpg";;
                  $fichier2= "../Photos/".$saison.$refProduit."-".$codeColoris."-1.jpg";
                  $fichier3= "../Photos/".$saison.$refProduit."--0.jpg";
                  $fichier4= "../Photos/".$saison.$refProduit."-".$codeColoris."-0.jpg";
                  $fichier5= "../Photos/".$saison.$refProduit."-".$codeColoris."-".$libelle."-1.jpg";
                  $fichier6= "../Photos/".$saison.$refProduit."-".$codeColoris.".jpg";

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

                  $reponse3=$bdd->prepare("SELECT DISTINCT libelle, codeColori, libcolori,tarif_promo,prix FROM produit WHERE refproduit=:refproduit AND stockdisponible > 0 AND codetarif=:codeTarif");
                  $reponse3->execute(array(
                      "refproduit"=>$refproduit,
                      "codeTarif"=>$codeTarifClient
                  ));

                  $arrImgColori = [];
                  while($retour3=$reponse3->fetch()) {
                    $oneElemColor = [];
                    $fichierColori= "../Photos/".$saison.$refProduit.".jpg";
                    $fichierColori1= "../Photos/".$saison.$refProduit."-1.jpg";;
                    $fichierColori2= "../Photos/".$saison.$refProduit."-".$retour3["codeColori"]."-1.jpg";
                    $fichierColori3= "../Photos/".$saison.$refProduit."--0.jpg";
                    $fichierColori4= "../Photos/".$saison.$refProduit."-".$retour3["codeColori"]."-0.jpg";
                    $fichierColori5= "../Photos/".$saison.$refProduit."-".$retour3["codeColori"]."-".$retour3["libelle"]."-1.jpg";
                    $fichierColori6= "../Photos/".$saison.$refProduit."-".$retour3["codeColori"].".jpg";

                    if (file_exists($fichierColori6)) {
                        $fichierColori=$fichierColori6;
                    } elseif(file_exists($fichierColori5)) {
                        $fichierColori=$fichierColori5;
                    } elseif(file_exists($fichierColori4)) {
                        $fichierColori=$fichierColori4;
                    } elseif(file_exists($fichierColori3)) {
                        $fichierColori=$fichierColori3;
                    } elseif(file_exists($fichierColori2)) {
                        $fichierColori=$fichierColori2;
                    } elseif(file_exists($fichierColori1)) {
                        $fichierColori=$fichierColori1;
                    } elseif(file_exists($fichierColori)) {
                        $fichierColori=$fichierColori;
                    } else {
                        if (!file_exists($fichierColori)) {
                            $fichierColori="../../Images/no_image.png";
                        }
                    }
                    $oneElemColor[] = $retour3["codeColori"];
                    $oneElemColor[] = $retour3["libcolori"];
                    $oneElemColor[] = $retour3["prix"];
                    $oneElemColor[] = $retour3["tarif_promo"];
                    $oneElemColor[] = $fichierColori;
                    $arrImgColori[] = $oneElemColor;
                  }

              $arrcommande[2][$i]["idDetailProduit"]=$retour["idDpPanier"];
              $arrcommande[2][$i]["quantite"]=(int)$donnees["quantite"];
              $arrcommande[2][$i]["prix"]=$retour["prix"];
              $arrcommande[2][$i]["refproduit"]=$refproduit;
              $arrcommande[2][$i]["taille"]=$taille;
              $arrcommande[2][$i]["libelle"]=$libelle;
              $arrcommande[2][$i]["total"]=$total;
              $arrcommande[2][$i]["saison"]=$saison;
              $arrcommande[2][$i]["stockdisponible"]=(int)$stockdisponible;
              $arrcommande[2][$i]["arraySelect"]=$arraySelect; //pour la quantite sur mobile
              $arrcommande[2][$i]["refArray"]=$refArray;
              $arrcommande[2][$i]["modeSaisie"]=$modeSaisie;
              $arrcommande[2][$i]["quantTab"]=$quantiteTabprod;
              $arrcommande[2][$i]["imageArt"]=$fichier;
              $arrcommande[2][$i]["tailleMin"]=$tailleMin;
              $arrcommande[2][$i]["tailleMax"]=$tailleMax;
              $arrcommande[2][$i]["idProduitGlobal"]=$idProduitGlobal;
              $arrcommande[2][$i]["imageColori"]=$arrImgColori;
              $arrcommande[2][$i]["codeColori"]=$retour["codeColori"];

              $i++;
            }
            $reponse2->closeCursor();
        }
    }
    $reponse->closeCursor();

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
