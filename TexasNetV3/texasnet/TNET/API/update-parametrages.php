<?php
include('connect.php');
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $req2="UPDATE parametrage SET " .$_POST["choix"] ."='" .$_POST["value"] ."'";
    $req=$bdd->query($req2);
    ?>
    {
        "success":true,
        "message":"update parametrages",
        "choix":"<?php echo $_POST["choix"]; ?>",
        "value":"<?php echo $_POST["value"]; ?>",
        "req2":"<?php echo $req2; ?>"
    }
    <?php
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>