<?php

include('connect.php');

$login_array=[];

$reponse=$bdd->query("SELECT login FROM client");
while($donnees=$reponse->fetch()){
    $login_array[]=$donnees["login"];
}
$reponse->closeCursor();

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $login=$_POST["login"];

    if(in_array($login,$login_array)){
        $reponse=$bdd->prepare("SELECT point_initial FROM points WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        while($donnees=$reponse->fetch()){
            ?>
            {
                "success":true,
                "points":<?php echo $donnees["point_initial"]; ?>,
                "message":"bonne connexion <?php echo $login .$donnees["point_initial"]; ?>"
            }
            <?php
        }
        $reponse->closeCursor();
    }else{
        ?>
        {
            "success":false,
            "message":"mauvais login <?php echo $login; ?>"
        }
        <?php
    }
}else{
?>
{
    "success":false,
    "message":"Only post request allowed <?php echo $_POST["login"]; ?>"
}
<?php
}

?>