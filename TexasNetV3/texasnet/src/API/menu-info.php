<?php
include('connect.php');
/* met à jour la table modules à partir du pannel d'administration */
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    if($_POST["choix"]=="info"){
        $reponse=$bdd->query("SELECT * FROM menu");
        $i=0;
        while($donnees=$reponse->fetch()){
            $arrMenu[$i]["nom"]=$donnees["nom"];
            $arrMenu[$i]["ordreMenu"]=$donnees["ordre_menu"];
            $arrMenu[$i]["actif"]=$donnees["actif"];
            $arrMenu[$i]["id"]=$donnees["id"];
            $i++;
        }
        echo json_encode($arrMenu);
    }
    if($_POST["choix"]=="update"){

        $reponse=$bdd->prepare("SELECT actif FROM menu WHERE id=:id");
        $reponse->execute(array(
            "id"=>$_POST["idToUpdate"]
        ));
        $retour=$reponse->fetch();
        $actif=$retour["actif"];
        $reponse->closeCursor();
        if($actif==="1"){
            $actifToChange="0";
            $reponse=$bdd->query("SELECT * FROM menu");
            $i=0;
            $j=0;
            $k=0;
            while($donnees=$reponse->fetch()){
                
                if((int)$donnees["id"]!=(int)$_POST["idToUpdate"]){
                    if( ((int)($donnees["ordre_menu"]) > (int)($_POST["ordreMenu"]))=="1" ){
                        $req=$bdd->prepare("UPDATE menu SET ordre_menu=ordre_menu-1 WHERE id=:id");
                        $req->execute(array(
                            "id"=>$donnees["id"]
                        ));
                        $k++;
                    }
                    $i++;
                }else{
                    $req=$bdd->prepare("UPDATE menu SET ordre_menu=0 WHERE id=:id");
                    $req->execute(array(
                        "id"=>$donnees["id"]
                    ));
                    $j++;
                }
            }
            $reponse->closeCursor();

        }
        if($actif==="0"){
            $actifToChange="1";
            $i=0;
            $j=0;
            $k=0;
            $position=1;
            $reponse=$bdd->query("SELECT * FROM menu");
            while($donnees=$reponse->fetch()){
                if((int)$donnees["id"]==(int)$_POST["idToUpdate"]){
                    $req=$bdd->prepare("UPDATE menu SET ordre_menu=:position WHERE id=:id");
                    $req->execute(array(
                        "position"=>$position,
                        "id"=>$donnees["id"]
                    ));
                }else{
                    $req=$bdd->prepare("UPDATE menu SET ordre_menu=:position WHERE id=:id AND ordre_menu!=0");
                    $req->execute(array(
                        "position"=>$position,
                        "id"=>$donnees["id"]
                    ));
                }
                $position++;
            }
            $reponse->closeCursor();
        }

        $req=$bdd->prepare("UPDATE menu SET actif=:actif WHERE id=:id");
        $req->execute(array(
            "actif"=>$actifToChange,
            "id"=>$_POST["idToUpdate"]
        ))
        ?>
        {
            "success":true,
            "message":"update du menu",
            "id":"<?php echo $_POST["idToUpdate"]; ?>",
            "actif":"<?php echo $actif ?>",
            "actifToChange":"<?php echo $actifToChange; ?>"
        }
        <?php
    }
    if($_POST["choix"]=="activeSubMenu"){
        $reponse=$bdd->query("SELECT COUNT(actif) as nbActif FROM menu WHERE actif=1");
        $retour=$reponse->fetch();
        $nbActif=$retour["nbActif"];
        $reponse->closeCursor();
        ?>
        {
            "success":true,
            "nbActif":<?php echo $nbActif; ?>
        }
        <?php
    }
}else{
    ?>
    {
        "success":false,
        "message":"Only POST request allowedsssss"
    }
    <?php
}
?>