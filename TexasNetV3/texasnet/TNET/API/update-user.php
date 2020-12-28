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
    $type=$_POST["type"];
    if(in_array($login,$login_array)){
        if($type=="update"){ //update information utilisateur 
            $nom=$_POST["nom"];
            $prenom=$_POST["prenom"];
            $email=$_POST["email"];
            $ville=$_POST["ville"];
            $cp=$_POST["cp"];
            $adresse1=$_POST["adresse1"];
            $adresse2=$_POST["adresse2"];
            $telephone=$_POST["telephone"];
            $langue=$_POST["langue"];

            if((isset($nom))&&(!empty($nom))){
                $req=$bdd->prepare("UPDATE client SET raisonSocialeFact=:raisonSocialeFact WHERE login=:login");
                $req->execute(array(
                    "raisonSocialeFact"=>$nom,
                    "login"=>$login
                ));
            }
            if((isset($prenom))&&(!empty($prenom))){
                $req=$bdd->prepare("UPDATE client SET complementFacturation=:complementFacturation WHERE login=:login");
                $req->execute(array(
                    "complementFacturation"=>$prenom,
                    "login"=>$login
                ));
            }
            if((isset($email))&&(!empty($email))){
                $req=$bdd->prepare("UPDATE client SET email=:email WHERE login=:login");
                $req->execute(array(
                    "email"=>$email,
                    "login"=>$login
                ));
            }
            if((isset($ville))&&(!empty($ville))){
                $req=$bdd->prepare("UPDATE adresse SET ville=:ville WHERE login=:login");
                $req->execute(array(
                    "ville"=>$ville,
                    "login"=>$login
                ));
            }
            if((isset($cp))&&(!empty($cp))){
                $req=$bdd->prepare("UPDATE client SET codePostal=:codePostal WHERE login=:login");
                $req->execute(array(
                    "codePostal"=>$cp,
                    "login"=>$login
                ));
            }
            if((isset($adresse1))&&(!empty($adresse1))){
                $req=$bdd->prepare("UPDATE adresse SET adresse1=:adresse1 WHERE login=:login");
                $req->execute(array(
                    "adresse1"=>$adresse1,
                    "login"=>$login
                ));
            }
            if((isset($adresse2))&&($adresse2!="")){
                $req=$bdd->prepare("UPDATE adresse SET adresse2=:adresse2 WHERE login=:login");
                $req->execute(array(
                    "adresse2"=>$adresse2,
                    "login"=>$login
                ));
            }
            if((isset($telephone))&&(!empty($telephone))){
                $req=$bdd->prepare("UPDATE client SET telephone=:telephone WHERE login=:login");
                $req->execute(array(
                    "telephone"=>$telephone,
                    "login"=>$login
                ));
            }
            if((isset($langue))&&(!empty($langue))){
                $req=$bdd->prepare("UPDATE client SET codeLangue=:codeLangue WHERE login=:login");
                $req->execute(array(
                    "codeLangue"=>$langue,
                    "login"=>$login
                ));
            }
            ?>
            {
                "success":true,
                "type":"<?php echo $_POST["type"]; ?>",
                "message":"le login <?php echo $login; ?> est présent dans la bdd",
                "login":"<?php echo $login; ?>",
                "nom":"<?php echo $nom; ?>",
                "prenom":"<?php echo $prenom; ?>",
                "email":"<?php echo $email; ?>",
                "ville":"<?php echo $ville; ?>",
                "cp":"<?php echo $cp; ?>",
                "adresse1":"<?php echo $adresse1; ?>",
                "adresse2":"<?php echo $adresse2; ?>",
                "telephone":"<?php echo $telephone; ?>",
                "langue":"<?php echo $langue; ?>",
                "EmptyOrNot":"<?php if(empty($email)){ echo "email vide"; }else{ echo "email rempli"; } ?>"
            }
            <?php
        }else if($type=="verify"){ //Vérifie le mot de passe du client
            $password="TexasNet." .$_POST["passwordUser"];
            $reponse=$bdd->prepare("SELECT password FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$login
            ));
            while($donnees=$reponse->fetch()){
                if($donnees["password"]==md5($password)){
                    ?>
                    {
                        "success":true,
                        "message":"bon mot de passe"
                    }
                    <?php
                }else{
                    ?>
                    {
                        "success":false,
                        "message":"mauvais mot de passe"
                    }
                    <?php
                }
            }
            $reponse->closeCursor();
        }else if($type=="change"){ // modification du mot de passe de l'utilisateur
            $password="TexasNet." .$_POST["newPassword"];
            $req=$bdd->prepare("UPDATE client SET password=:password WHERE login=:login");
            $req->execute(array(
                "password"=>md5($password),
                "login"=>$login
            ));
            ?>
            {
                "success":true,
                "message":"Modification du mot de passe réussie"
            }
            <?php
        }else{
            ?>
            {
                "success":false,
                "message":"erreur update"
            }
            <?php

        }
    }else{
        ?>
        {
            "success":false,
            "message":"Aucun login <?php echo $login; ?> présent dans la bdd"
        }
        <?php
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