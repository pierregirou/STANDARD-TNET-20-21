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
    $login=$_POST["login"];
    if(in_array($login,$login_array)){
        $tab[0] = $_POST["keys"];
        for ($i=0;$i<(count($tab[0])+1);$i++){
            $numCommande = $tab[0][$i][0]["numCommande"];
            $refProduit = $tab[0][$i][0]["refProduit"];
            $quantiteR = $tab[0][$i][0]["quantiteR"];
            $idProduit = $tab[0][$i][0]["idDetailProduit"];

            $reponse=$bdd->prepare("SELECT * FROM detailproduit WHERE idproduit=:idproduit");
            $reponse->execute(array(
                "idproduit"=>$idProduit
            ));
            $donnees=$reponse->fetch();
            $codeGammeTaille=$donnees["codeGammeTaille"];     
            $codetaille=$donnees["codetaille"];     

            $reponseTaille=$bdd->prepare("SELECT * FROM taille WHERE codegamme=:codeGammeTaille AND codetaille=:codetaille");
            $reponseTaille->execute(array(
                "codetaille"=>$codetaille,
                "codeGammeTaille"=>$codeGammeTaille
            ));
            $donneesTaille=$reponseTaille->fetch();
            $libelleTaille=$donneesTaille["libelle"];    
            
            $reponseRet=$bdd->prepare("SELECT * FROM retour WHERE login =:login AND numCommande=:numCommande LIMIT 1");
            $reponseRet->execute(array(
                "login"=>$login,
                "numCommande"=>$numCommande
            ));
            $donneesRet=$reponseRet->fetch();
            $idRetour=$donneesRet["IDRetour"];   




            $req=$bdd->prepare("INSERT INTO `ligneretour`(`idRetour`, `numCommande`, `refProduit`, `taille`, `qte`, `login`, `date`) VALUES (:idRetour,:numCommande,:refProduit,:taille,:qte,:login,NOW())");
            $req->execute(array(
                "idRetour"=>$idRetour,
                "numCommande"=>$numCommande,
                "refProduit"=>$refProduit,
                "taille"=> $libelleTaille,
                'qte'=>$quantiteR,
                "login"=>$_POST["login"]
            ));
        }
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