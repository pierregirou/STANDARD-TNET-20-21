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
        $numCommande = $tab[0][1][0]["numCommande"];
        
        for ($i=0;$i<(count($tab[0])+1);$i++){
            $nbr_prod += $tab[0][$i][0]["quantiteR"];
        }


        $req=$bdd->prepare("INSERT INTO `retour`( `numCommande`, `login`, `nbr_prod`, `date`, `refColis`) VALUES (:numCommande,:login,:nbr_prod,NOW(),:refColis)");
        $req->execute(array(
            "numCommande"=>$numCommande,
            "login"=>$login,
            "nbr_prod"=> $nbr_prod,
            "refColis"=>"..."
        ));
        ?>
            {
                "success":true,
                "message":"C'est validé!"
            }
        <?php
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