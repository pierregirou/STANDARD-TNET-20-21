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
        $reponse=$bdd->prepare("SELECT * FROM client WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        while($donnees=$reponse->fetch()){
            $reponse2=$bdd->prepare("SELECT * FROM adresse WHERE login=:login");
            $reponse2->execute(array(
                "login"=>$login
            ));
            while($donnees2=$reponse2->fetch()){
                $tab[$donnees2["type"]][$donnees2["id"]]["adresse1"] = $donnees2["adresse1"];
                $tab[$donnees2["type"]][$donnees2["id"]]["adresse2"] = $donnees2["adresse2"];
                $tab[$donnees2["type"]][$donnees2["id"]]["codePostal"] = $donnees2["codePostal"];
                $tab[$donnees2["type"]][$donnees2["id"]]["ville"] = $donnees2["ville"];
                $tab[$donnees2["type"]][$donnees2["id"]]["pays"] = $donnees2["pays"];
                $tab[$donnees2["type"]][$donnees2["id"]]["codePays"] = $donnees2["codePays"];
                $tab[$donnees2["type"]][$donnees2["id"]]["numero"] = $donnees2["numero"];
                $tab[$donnees2["type"]][$donnees2["id"]]["id"] = $donnees2["id"];
                $donnees2["id"]++;
            }
            $reponse2->closeCursor();
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