<?php
include('connect.php');
/* met à jour la table modules à partir du pannel d'administration */
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){

    /* Info et mise à jour du texte en français */
    if($_POST["choix"]=="infoTexte"){
        $reponse=$bdd->query("SELECT texte FROM template");
        $retour=$reponse->fetch();
        $texte=$retour["texte"];
        $reponse->closeCursor();
        $arrTemplate[0]=utf8_encode($texte);
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateTexte"){
        $arrTemplateUpdate[0]=true;
        $arrTemplateUpdate[1]=$_POST["texteToUpdate"];
        $req=$bdd->prepare("UPDATE template SET texte=:texte");
        $req->execute(array(
            "texte"=>$_POST["texteToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }
    /* Info et mise à jour du texte en anglais */
    if($_POST["choix"]=="infoTexteAng"){
        $reponse=$bdd->query("SELECT texte_ang FROM template");
        $retour=$reponse->fetch();
        $texte=$retour["texte_ang"];
        $reponse->closeCursor();
        $arrTemplate[0]=utf8_encode($texte);
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateTexteAng"){
        $arrTemplateUpdate[0]=true;
        $arrTemplateUpdate[1]=$_POST["texteAngToUpdate"];
        $req=$bdd->prepare("UPDATE template SET texte_ang=:texte_ang");
        $req->execute(array(
            "texte_ang"=>$_POST["texteAngToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }

    /* Info et mise à jour du background-color 1 */
    if($_POST["choix"]=="infoBG"){
        $reponse=$bdd->query("SELECT bg FROM template");
        $retour=$reponse->fetch();
        $bg=$retour["bg"];
        $reponse->closeCursor();
        $arrTemplate[0]=$bg;
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateBG"){
        $arrTemplateUpdate[0]=true;
        $req=$bdd->prepare("UPDATE template set bg=:bg");
        $req->execute(array(
            "bg"=>$_POST["bgToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }
    /* Info et mise à jour du background-color 2 */
    if($_POST["choix"]=="infoBG2"){
        $reponse=$bdd->query("SELECT bg2 FROM template");
        $retour=$reponse->fetch();
        $bg2=$retour["bg2"];
        $reponse->closeCursor();
        $arrTemplate[0]=$bg2;
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateBG2"){
        $arrTemplateUpdate[0]=true;
        $req=$bdd->prepare("UPDATE template set bg2=:bg2");
        $req->execute(array(
            "bg2"=>$_POST["bg2ToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }

    /* Info et mise à jour de la couleur du menu */
    if($_POST["choix"]=="infoMenuColor"){
        $reponse=$bdd->query("SELECT navbar FROM template");
        $retour=$reponse->fetch();
        $menuColor=$retour["navbar"];
        $reponse->closeCursor();
        $arrTemplate[0]=$menuColor;
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateMenuColor"){
        $arrTemplateUpdate[0]=true;
        $req=$bdd->prepare("UPDATE template set navbar=:navbar");
        $req->execute(array(
            "navbar"=>$_POST["menuColorToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }

    /* Info et mise à jour de la couleur du footer */
    if($_POST["choix"]=="infoFooterColor"){
        $reponse=$bdd->query("SELECT footer FROM template");
        $retour=$reponse->fetch();
        $footerColor=$retour["footer"];
        $reponse->closeCursor();
        $arrTemplate[0]=$footerColor;
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateFooterColor"){
        $arrTemplateUpdate[0]=true;
        $req=$bdd->prepare("UPDATE template set footer=:footer");
        $req->execute(array(
            "footer"=>$_POST["footerColorToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }

    /* Info et mise à jour de la couleur du contenu */
    if($_POST["choix"]=="infoContenuColor"){
        $reponse=$bdd->query("SELECT wellcentre FROM template");
        $retour=$reponse->fetch();
        $contenuColor=$retour["wellcentre"];
        $reponse->closeCursor();
        $arrTemplate[0]=$contenuColor;
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateContenuColor"){
        $arrTemplateUpdate[0]=true;
        $req=$bdd->prepare("UPDATE template set wellcentre=:wellcentre");
        $req->execute(array(
            "wellcentre"=>$_POST["contenuColorToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }

    /* Info et mise à jour de la couleur de totalcentre */
    if($_POST["choix"]=="infoTotalColor"){
        $reponse=$bdd->query("SELECT totalcentre FROM template");
        $retour=$reponse->fetch();
        $totalColor=$retour["totalcentre"];
        $reponse->closeCursor();
        $arrTemplate[0]=$totalColor;
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateTotalColor"){
        $arrTemplateUpdate[0]=true;
        $req=$bdd->prepare("UPDATE template set totalcentre=:totalcentre");
        $req->execute(array(
            "totalcentre"=>$_POST["totalColorToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
    }

    /* Info et mise à jour de la couleur de infocentre */
    if($_POST["choix"]=="infoInfoColor"){
        $reponse=$bdd->query("SELECT infocentre FROM template");
        $retour=$reponse->fetch();
        $infoColor=$retour["infocentre"];
        $reponse->closeCursor();
        $arrTemplate[0]=$infoColor;
        echo json_encode($arrTemplate);
    }
    if($_POST["choix"]=="updateInfoColor"){
        $arrTemplateUpdate[0]=true;
        $req=$bdd->prepare("UPDATE template set infocentre=:infocentre");
        $req->execute(array(
            "infocentre"=>$_POST["infoColorToUpdate"]
        ));
        echo json_encode($arrTemplateUpdate);
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