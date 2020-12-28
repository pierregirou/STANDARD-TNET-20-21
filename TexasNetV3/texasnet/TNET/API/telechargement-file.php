<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    if($_POST['type'] === "list") {
        $arrTelechargement['success'] = true;
        $reponse=$bdd->prepare("SELECT * FROM telechargement");
        $reponse->execute();
        $i = 0;
        while($donnees=$reponse->fetch()){
            $arrTelechargement[$i]['id'] = $donnees['id'];
            $arrTelechargement[$i]['intitule'] = $donnees['intitule'];
            $arrTelechargement[$i]['type'] = $donnees['type'];
            $arrTelechargement[$i]['lien'] = $donnees['lien'];
            $i++;
        }
        $reponse->closeCursor();
        echo json_encode($arrTelechargement);
    } else if ($_POST['type'] === "delete") {
        $reponse=$bdd->prepare("DELETE FROM telechargement WHERE id=:id");
        $reponse->execute(array(
            "id"=> $_POST['id']
        ));
        $arrTelechargement['success'] = true;
        echo json_encode($arrTelechargement);
    }

} else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>