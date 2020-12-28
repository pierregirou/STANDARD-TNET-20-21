<?php
/* Récupère les informations relatives aux produits à partir d'une commande */

include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){

    $numCommande = $_POST["numCommande"];
    $detailsCommandes[0]=false;

    $reponse=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCommande");
    $reponse->execute(array(
        "numCommande"=>$numCommande
    ));

    $i = 0;
    while($donnees=$reponse->fetch()) {

        $reponse2=$bdd->prepare("SELECT * FROM detailproduit WHERE idproduit=:idDetailProduit");
        $reponse2->execute(array(
            "idDetailProduit"=>$donnees["idDetailProduit"]
        ));
            // On évite les lignes vides
        if($donnees2=$reponse2->fetch()) {
            $reponse3=$bdd->prepare("SELECT * FROM produit WHERE refproduit=:refproduit AND codeColori=:codeColori");
            $reponse3->execute(array(
                "refproduit"=>$donnees2["refproduit"],
                "codeColori"=>$donnees2["codeColori"]
            ));

            $reponse4=$bdd->prepare("SELECT libelle FROM taille WHERE codegamme=:codegamme AND codetaille=:codeTaille");
            $reponse4->execute(array(
                "codegamme"=>$donnees2["codeGammeTaille"],
                "codeTaille"=>$donnees2["codetaille"]
            ));

            if($donnees3=$reponse3->fetch()) {
                if($donnees4=$reponse4->fetch()) {
                        // On vérifie qu'il n'y a pas de ligne vide
                    if ($donnees["quantite"] != 0) {
                        
                        $detailsCommandes[0]=true;
            
                        $detailsCommandes[1][$i]["numCommande"] = $donnees["numCommande"];
                        $detailsCommandes[1][$i]["idDetailProduit"] = $donnees["idDetailProduit"];
                        $detailsCommandes[1][$i]["reference"] = $donnees2["refproduit"];
                        $detailsCommandes[1][$i]["libelleProduit"] = $donnees3["libelle"];
                        $detailsCommandes[1][$i]["prixUnitaire"] = $donnees["prix"];
                        $detailsCommandes[1][$i]["libelleTaille"] = $donnees4["libelle"];
                        $detailsCommandes[1][$i]["libelleColoris"] = $donnees3["libcolori"];
                        $detailsCommandes[1][$i]["quantite"] = $donnees["quantite"];
                        $detailsCommandes[1][$i]["total"] = $donnees["prix"] * $donnees["quantite"]; 
                        $i++;
                    }
                }
            }
            $reponse4->closeCursor();
            $reponse3->closeCursor();
        }
        $reponse2->closeCursor();
    }
    $reponse->closeCursor();
    echo json_encode($detailsCommandes);
} else {
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>