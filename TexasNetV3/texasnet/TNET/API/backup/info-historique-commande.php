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
        $reponse=$bdd->prepare("SELECT * FROM commande WHERE login=:login AND valid=1 AND nbrpiece > 0 ORDER BY numCommande desc");
        $reponse->execute(array(
            "login"=>$login
        ));
        $i=0;
        while($donnees=$reponse->fetch()){
            $arrCommandeVal[$i]["numCommande"]=$donnees["numCommande"];
            $arrCommandeVal[$i]["dateValidation"]=$donnees["dateValidation"];
            $arrCommandeVal[$i]["montant"]=$donnees["montant"]+$donnees["fraisport"];
            $arrCommandeVal[$i]["nbrpiece"]=$donnees["nbrpiece"];
            $i++;
        }
        $reponse->closeCursor();
        echo json_encode($arrCommandeVal);
    }else{
        ?>
        {
            "success":false,
            "message":"mauvaise authentification"
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