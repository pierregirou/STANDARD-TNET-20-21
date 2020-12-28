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
        $reponse=$bdd->prepare("SELECT * FROM client WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        while($donnees=$reponse->fetch()){  
        ?>          
            {
                "success":true,
                "nom":"<?php echo $donnees["raisonSocialeFact"]; ?>",
                "prenom":"<?php echo $donnees["complementFacturation"]; ?>",
                "telephone":"<?php echo $donnees["telephone"] ?>",
                "email":"<?php echo $donnees["email"]; ?>",
                "ville":"<?php echo $donnees["villeFact"]; ?>",
                "cp":"<?php echo $donnees["codePostalFact"] ?>",
                "adresse1":"<?php echo $donnees["raisonSociale"]; ?>",
                "adresse2":"<?php echo $donnees["complementLivraison"]; ?>",
                "langue":"<?php echo $donnees["codeLangue"]; ?>"
            }
        <?php
        }
        $reponse->closeCursor();
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