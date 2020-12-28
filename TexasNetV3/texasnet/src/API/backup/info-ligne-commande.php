<?php

include('connect.php');
//$login_array=[];

$login_array=[];

$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
   $login_array[]= $donnees["login"];
}
$reponse->closeCursor();

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $login=$_POST["login"];
    if(in_array($login,$login_array)){
        $reponse=$bdd->prepare("SELECT * FROM lignecommande INNER JOIN detailproduit ON detailproduit.idproduit = lignecommande.idDetailProduit WHERE numCommande=:numcommande");
        $reponse->execute(array(
            "numcommande"=>$_POST['numCommande']
        ));
        while($donnees=$reponse->fetch()){
            $numCommande=$donnees['numCommande'];
            $idDetailProd=$donnees['idDetailProduit'];
            $tab[$numCommande][$idDetailProd]["refproduit"] = $donnees["refproduit"];
            $tab[$numCommande][$idDetailProd]["idDetailProduit"] = $donnees["idDetailProduit"];
            $tab[$numCommande][$idDetailProd]["quantite"] = $donnees["quantite"];
            $tab[$numCommande][$idDetailProd]["prix"] = $donnees["prix"];
        }
        $reponse->closeCursor();
        echo json_encode($tab);
    }
} else {
    ?>
    {
        "success":false,
        "message":"Only POST request allowed"
    }
    <?php
}
?>