<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    if($_POST["ordreAffichage"]){
        $requete=$bdd->prepare("UPDATE modules SET ordreAffichage=:ordreAffichage");
        $requete->execute(array(
            "ordreAffichage"=>$_POST['ordreAffichage']
        ));
    } else {
        $ordreAffichage="";
    }
    $req=$bdd->query("SELECT ordreAffichage FROM modules");
    $retour=$req->fetch();
    $ordreAffichage=$retour['ordreAffichage'];
    ?>
    {
        "message":"Modification prise en compte",
        "idOrder":"<?php echo $ordreAffichage; ?>"
    }
    <?php
    
}else{
    ?>
    {
        "success":false,
        "message":"Only POST request allowed"
    }
    <?php
}
?>