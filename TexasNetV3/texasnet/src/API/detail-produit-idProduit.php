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
    if(isset($_POST['login'])){
        $login=$_POST['login'];
        if(in_array($login,$login_array)){
            $reponse=$bdd->prepare("SELECT codeTarif FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$login
            ));
            while($donnees=$reponse->fetch()){
                $codeTarif=$donnees["codeTarif"];
            }
            $reponse->closeCursor(); 
            $reponse2=$bdd->prepare("SELECT * FROM detailproduit WHERE idproduit=:idproduit AND codetarif=:codeTarif");
            $reponse2->execute(array(
                "codeTarif"=>$codeTarif,
                "idproduit"=>$_POST['idproduit']
            ));
            $donnees2=$reponse2->fetch();
            $refProduit=$donnees2["refproduit"];  
            $reponse2->closeCursor(); 
            $reponse3=$bdd->prepare("SELECT * FROM commande WHERE login=:login AND valid=0");
            $reponse3->execute(array(
                "login"=>$login
            ));
            $donnees3=$reponse3->fetch();
            $numCommande = $donnees3['numCommande'];
            $reponse3->closeCursor(); 
            $reponse4=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCde AND idDetailProduit=:idprod");
            $reponse4->execute(array(
                "idprod"=>$_POST['idproduit'],
                "numCde"=>$numCommande
            ));
            $dejaCde = $reponse4->fetch();
            echo $dejaCde;
                
            echo json_encode($arr);
        }
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