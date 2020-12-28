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
    $positiongalerieinfo = intval($_POST["nummenu"]);

    if ($positiongalerieinfo === 1) {
      $modifTabProd = true;
    } else {
      $modifTabProd = false;
    }

    if ($positiongalerieinfo < 0) {
      $ajoutPromo = "promo";
      $positiongalerieinfo = $positiongalerieinfo*-1;
    } else {
      $ajoutPromo = "";
    }


    if ($_POST["type"] === "set") {
      $refprod_array=[];
      $reponse=$bdd->query("SELECT * FROM positiongalerie$ajoutPromo");
      while($donnees=$reponse->fetch()){
          $refprod_array[]= $donnees["refproduit"];
      }
      $reponse->closeCursor();
      if ($modifTabProd) {
        for ($i = 0; $i < count($_POST["refprodarray"]); $i++) {
          if (in_array($_POST["refprodarray"][$i],$refprod_array)) {
            $req=$bdd->prepare("UPDATE produit SET positionGalerie=:positionGalerie WHERE refproduit=:refproduit");
            $req->execute(array(
                "positionGalerie"=>$i+1,
                "refproduit"=>$_POST["refprodarray"][$i]
            ));
          } else {
            // On n'ajoute pas le produit
          }
        }
      }
      for ($i = 0; $i < count($_POST["refprodarray"]); $i++) {
          if (!in_array($_POST["refprodarray"][$i],$refprod_array)) {
          $req=$bdd->prepare("INSERT INTO `positiongalerie$ajoutPromo`(`idPos`, `refproduit`, `position1`, `position2`, `position3`, `position4`, `position5`, `position6`)
                              VALUES ('',:refproduit,'','','','','','')");
          $req->execute(array(
            "refproduit"=>$_POST["refprodarray"][$i]
          ));
          $refprod_array[]=$_POST["refprodarray"][$i];
        }
        $req=$bdd->prepare("UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=:positionGalerie WHERE refproduit=:refproduit");
        $req->execute(array(
            "positionGalerie"=>$i+1,
            "refproduit"=>$_POST["refprodarray"][$i]
        ));
      }

      if ($modifTabProd) {
        for ($j = 0; $j < count($refprod_array); $j++) {
          if (!in_array($refprod_array[$j],$_POST["refprodarray"])) {
            $req=$bdd->prepare("DELETE FROM `positiongalerie$ajoutPromo` WHERE refproduit=:refproduit");
            $req->execute(array(
                "refproduit"=>$refprod_array[$j]
            ));
          }
        }
      }

        // Echange
    } else if ($_POST["type"] === "exchange") {
        $reqFrom=$bdd->prepare("SELECT * FROM positiongalerie$ajoutPromo WHERE refproduit=:refproduit");
        $reqFrom->execute(array(
            "refproduit"=>$_POST["refproduit1"]
        ));
        $reqFromRes=$reqFrom->fetch();
        $bornemin = $reqFromRes["position".$positiongalerieinfo];
        $reqTo=$bdd->prepare("SELECT * FROM positiongalerie$ajoutPromo WHERE refproduit=:refproduit");
        $reqTo->execute(array(
            "refproduit"=>$_POST["refproduit2"]
        ));
        $reqToRes=$reqTo->fetch();
        $bornemax = $reqToRes["position".$positiongalerieinfo];

        $diffTaille = $_POST["tailleP1"]-$_POST["tailleP2"];

            // la position 1 correspond au rangement général
        if ($modifTabProd) {
            $req=$bdd->prepare("UPDATE produit SET positionGalerie=:positionGalerie WHERE refproduit=:refproduit");
            $req->execute(array(
                "positionGalerie"=>$_POST["indexTo"],
                "refproduit"=>$_POST["refproduit1"]
            ));
            $req->execute(array(
                "positionGalerie"=>$_POST["indexFrom"],
                "refproduit"=>$_POST["refproduit2"]
            ));
        }

        $req=$bdd->prepare("UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=-1 WHERE refproduit=:refproduit");
        $req->execute(array(
            "refproduit"=>$_POST["refproduit1"]
        ));
        $req=$bdd->prepare("UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=-2 WHERE refproduit=:refproduit");
        $req->execute(array(
            "refproduit"=>$_POST["refproduit2"]
        ));

        $req=$bdd->prepare("UPDATE produit SET positionGalerie=positionGalerie+:diffTaille WHERE positionGalerie BETWEEN :bornemin AND :bornemax");
        $req->execute(array(
          "diffTaille"=>$diffTaille,
          "bornemin"=>$bornemin,
          "bornemax"=>$bornemax
        ));

        $tmp3 = $bornemax+$diffTaille;

        $req3=$bdd->prepare("UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=:positionGalerie WHERE position$positiongalerieinfo=-1");
        $req3->execute(array(
            "positionGalerie"=>$tmp3
        ));

        $req3=$bdd->prepare("UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=:positionGalerie WHERE position$positiongalerieinfo=-2");
        $req3->execute(array(
            "positionGalerie"=>$bornemin
        ));

        // Decalage
    } else if ($_POST["type"] === "drop") {
        $reqFrom=$bdd->prepare("SELECT * FROM positiongalerie$ajoutPromo WHERE refproduit=:refproduit");
        $reqFrom->execute(array(
            "refproduit"=>$_POST["refproduitC1"]
        ));
        $reqFromRes=$reqFrom->fetch();
        $bornemin = $reqFromRes["position".$positiongalerieinfo];

        $reqTo=$bdd->prepare("SELECT * FROM positiongalerie$ajoutPromo WHERE refproduit=:refproduit");
        $reqTo->execute(array(
            "refproduit"=>$_POST["refproduitC2"]
        ));
        $reqToRes=$reqTo->fetch();
        $bornemax = $reqToRes["position".$positiongalerieinfo];

            // la position 1 correspond au aussi rangement général des produits dans produits
        if ($modifTabProd) {
            $req=$bdd->prepare("UPDATE produit SET positionGalerie=-1 WHERE refproduit=:refproduit");
            //echo "UPDATE produit SET positionGalerie=-1 WHERE refproduit='".$_POST["refproduitC1"]."'";
            $req->execute(array(
                "refproduit"=>$_POST["refproduitC1"]
            ));

            if ($_POST["indexFrom"] > $_POST["indexTo"]) {
                $req=$bdd->prepare("UPDATE produit SET positionGalerie=positionGalerie+:amplitude WHERE positionGalerie BETWEEN :bornemax AND :bornemin");
                //echo "UPDATE produit SET positionGalerie=positionGalerie+".$_POST["amplitude"]." WHERE positionGalerie BETWEEN $bornemax AND $bornemin";
                $req->execute(array(
                    "amplitude"=>$_POST["amplitude"],
                    "bornemin"=>$bornemin,
                    "bornemax"=>$bornemax
            ));
            } else {
                $req=$bdd->prepare("UPDATE produit SET positionGalerie=positionGalerie-:amplitude WHERE positionGalerie BETWEEN :bornemin AND :bornemax");

                //echo "UPDATE produit SET positionGalerie=positionGalerie-".$_POST["amplitude"]." WHERE positionGalerie BETWEEN $bornemin AND $bornemax";
                $req->execute(array(
                    "amplitude"=>$_POST["amplitude"],
                    "bornemin"=>$bornemin,
                    "bornemax"=>$bornemax
                ));
            }
            $req=$bdd->prepare("UPDATE produit SET positionGalerie=:positionGalerie WHERE positionGalerie=-1");
            //echo "UPDATE produit SET positionGalerie=$$bornemax WHERE positionGalerie=-1";
            $req->execute(array(
                "positionGalerie"=>$bornemax
            ));
        }

          // Traitement pour position galerie
        $req=$bdd->prepare("UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=-1 WHERE refproduit=:refproduit");
        //echo "UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=-1 WHERE refproduit='".$_POST["refproduitC1"]."'";
        $req->execute(array(
            "refproduit"=>$_POST["refproduitC1"]
        ));

            if ($_POST["indexFrom"] > $_POST["indexTo"]) {
                $myReq = "UPDATE positiongalerie$ajoutPromo SET position".$positiongalerieinfo."=(position".$positiongalerieinfo.")+".$_POST["amplitude"]." WHERE position".$positiongalerieinfo." BETWEEN ".$bornemax." AND ".$bornemin;
                $req2=$bdd->prepare($myReq);
                $req2->execute();
            } else {
                $myReq = "UPDATE positiongalerie$ajoutPromo SET position".$positiongalerieinfo."=(position".$positiongalerieinfo.")-".$_POST["amplitude"]." WHERE position".$positiongalerieinfo." BETWEEN ".$bornemin." AND ".$bornemax;
                $req2=$bdd->prepare($myReq);
                $req2->execute();
            }
            $req3=$bdd->prepare("UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=:positionGalerie WHERE position$positiongalerieinfo=-1");
            //echo "UPDATE positiongalerie$ajoutPromo SET position$positiongalerieinfo=$bornemax WHERE position$positiongalerieinfo=-1";
            $req3->execute(array(
                "positionGalerie"=>$bornemax
            ));
    }
    $returnedArray = [];
    $JSONreqstr="SELECT refproduit,position".$positiongalerieinfo." FROM positiongalerie$ajoutPromo";
    $JSONreq=$bdd->prepare($JSONreqstr);
    $JSONreq->execute();
    while($donneesJSON=$JSONreq->fetch()) {
      $returnedArray[] = $donneesJSON;
    }
    echo json_encode($returnedArray);
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>
