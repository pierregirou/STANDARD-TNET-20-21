<?php

    include('connect.php');
    $_POST = json_decode(file_get_contents("php://input"),true);

    if(isset($_POST) && !empty($_POST)){
        $tabApprouveCom = $_POST["tabApprouveCom"];

        foreach($tabApprouveCom as $val) {
            $req2="UPDATE commande SET etat=\"".$val[1]."\", motifRefus=\"".$val[2]."\" WHERE numCommande=".$val[0];
            $req=$bdd->query($req2);
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
