<?php
/* Récupère  du moment */


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
            $arr2[0]["success"]=true;
            $reponse=$bdd->query("SELECT * FROM telechargement");
            $i=1;
            while($donnees=$reponse->fetch()){
                $arr2[$i]["idTelechargement"]=$donnees["id"];
                $arr2[$i]["intitule"]=$donnees["intitule"];     
                $arr2[$i]["type"]=$donnees["type"];
                $arr2[$i]["lien"]=$donnees["lien"];
                $i++;
            }
            echo json_encode($arr2);
            $reponse->closeCursor();
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
