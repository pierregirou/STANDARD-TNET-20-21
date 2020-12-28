<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    if($_POST["choix"]=="select"){
        $req=$bdd->prepare("UPDATE produit SET selection=:selection WHERE refproduit=:refproduit AND codeColori=:codeColori");
        $req->execute(array(
            "selection"=>$_POST["selectToUpdate"],
            "refproduit"=>$_POST["refproduit"],
            "codeColori"=>$_POST["codeColori"]
        ));
        ?>
        {
            "success":true,
            "message":"SELECT"
        }
        <?php
    }
    if($_POST["choix"]=="select2"){
        //echo "UPDATE produit SET selection=".$_POST['selectToUpdate']." WHERE refproduit=".$_POST['refproduit']." AND tarif_promo=".$_POST['tarif_promo'];
        $req=$bdd->prepare("UPDATE produit SET selection=:selection WHERE refproduit=:refproduit AND tarif_promo=:tarif_promo");
        $req->execute(array(
            "selection"=>$_POST["selectToUpdate"],
            "refproduit"=>$_POST["refproduit"],
            "tarif_promo"=>$_POST["tarif_promo"]
        ));
        ?>
        {
            "success":true,
            "message":"SELECT2"
        }
        <?php
    }
    if($_POST["choix"]=="promo"){
        $req=$bdd->prepare("UPDATE produit SET promo=:promo WHERE idproduit=:idproduit");
        $req->execute(array(
            "promo"=>$_POST["promoToUpdate"],
            "idproduit"=>$_POST["idProduit"]
        ));
        if($_POST["promoToUpdate"] === 0) {
          $req=$bdd->prepare("UPDATE produit SET tarif_promo='0.00' WHERE idproduit=:idproduit");
          $req->execute(array(
              "idproduit"=>$_POST["idProduit"]
          ));
        }
        ?>
        {
            "success":true,
            "message":"promo"
        }
        <?php
    }
    if($_POST["choix"]=="tarifPromo"){
        $req=$bdd->prepare("UPDATE produit SET tarif_promo=:tarif_promo WHERE idproduit=:idproduit");
        $req->execute(array(
            "tarif_promo"=>$_POST["tarifToUpdate"],
            "idproduit"=>$_POST["idProduit"]
        ));
        ?>
        {
            "success":true,
            "message":"tarifPromo"
        }
        <?php
    }
    if($_POST["choix"]=="tarif_pvc"){
        $req=$bdd->prepare("UPDATE produit SET tarif_pvc=:tarif_pvc WHERE idproduit=:idproduit");
        $req->execute(array(
            "tarif_pvc"=>$_POST["tarifToUpdate"],
            "idproduit"=>$_POST["idProduit"]
        ));
        ?>
        {
            "success":true,
            "message":"tarif_pvc"
        }
        <?php
    }
    if($_POST["choix"]=="tarif_promoL"){
        $req=$bdd->prepare("UPDATE detailproduit SET tarif_promoL=:tarif_promoL WHERE idproduit=:idproduit");
        $req->execute(array(
            "tarif_promoL"=>$_POST["tarifToUpdate"],
            "idproduit"=>$_POST["idProduit"]
        ));
        ?>
        {
            "success":true,
            "message":"tarif_promoL"
        }
        <?php
    }
}else{
    ?>
    {
        "success":false,
        "message":"Only POST request allowed"
    }
    <?php
}
?>
