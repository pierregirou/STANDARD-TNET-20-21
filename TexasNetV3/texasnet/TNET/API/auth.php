<?php

include('connect.php');

$loginC_array=[]; //logins client
$loginA_array=[]; //logins administrateur
$loginR_array=[]; //logins representant
$loginAP_array=[]; //logins approuveur

/* Récupère tous les logins de la table client */
$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
    $loginC_array[]= $donnees["login"];
}
$reponse->closeCursor();

/* Récupère tous les logins de la table admin */
$reponse=$bdd->query("SELECT * FROM admin");
while($donnees=$reponse->fetch()){
    $loginA_array[]= $donnees["login"];
}
$reponse->closeCursor();

/* Récupère tous les logins de la table représentant */
$reponse=$bdd->query("SELECT * FROM representant");
while($donnees=$reponse->fetch()){
    $loginR_array[]= $donnees["login"];
}
$reponse->closeCursor();

$reponse=$bdd->query("SELECT * FROM approuveur");
while($donnees=$reponse->fetch()){
    $loginAP_array[]= $donnees["login"];
}
$reponse->closeCursor();

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $login=$_POST["login"];
    $password="TexasNet." .$_POST["password"];
    /* Si le login appartient au client */
    if(in_array($login,$loginC_array)){
        $reponse=$bdd->prepare("SELECT * FROM client WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        while($donnees=$reponse->fetch()){
            if($donnees["password"]==md5($password)){

                $infoClient[0]=true;
                $infoClient[1]="client";
                /* Info sur le client */
                $infoClient[2]["infoClient"]["codeClient"]=$donnees["codeClient"];
                $infoClient[2]["infoClient"]["nomSociete"]=$donnees["nomSociete"];
                $infoClient[2]["infoClient"]["raisonSociale"]=$donnees["raisonSociale"];
                $infoClient[2]["infoClient"]["complementLivraison"]=$donnees["complementLivraison"];
                $infoClient[2]["infoClient"]["telephone"]=$donnees["telephone"];
                $infoClient[2]["infoClient"]["fax"]=$donnees["fax"];
                $infoClient[2]["infoClient"]["email"]=$donnees["email"];
                $infoClient[2]["infoClient"]["login"]=$donnees["login"];
                $infoClient[2]["infoClient"]["codeLangue"]=$donnees["codeLangue"];
                $infoClient[2]["infoClient"]["dateDerniereVisite"]=$donnees["dateDerniereVisite"];
                $infoClient[2]["infoClient"]["nombreVisite"]=$donnees["nombreVisit"];
                $infoClient[2]["infoClient"]["codeTarif"]=$donnees["codeTarif"];
                $infoClient[2]["infoClient"]["raisonSocialeFact"]=$donnees["raisonSocialeFact"];
                $infoClient[2]["infoClient"]["complementFacturation"]=$donnees["complementFacturation"];
                $infoClient[2]["infoClient"]["codePostalFact"]=$donnees["codePostalFact"];
                $infoClient[2]["infoClient"]["villeFact"]=$donnees["villeFact"];
                $infoClient[2]["infoClient"]["paysFact"]=$donnees["paysFact"];
                $infoClient[2]["infoClient"]["tauxRemise"]=$donnees["tauxRemise"];
                $infoClient[2]["infoClient"]["codeSurveillance"]=$donnees["codeSurveillance"];
                $infoClient[2]["infoClient"]["categorie"]=$donnees["categorie"];
                $infoClient[2]["infoClient"]["geo"]=$donnees["geo"];
                $infoClient[2]["infoClient"]["souscategorie"]=$donnees["souscategorie"];
                $infoClient[2]["infoClient"]["centrale"]=$donnees["centrale"];
                $infoClient[2]["infoClient"]["souscentrale"]=$donnees["souscentrale"];
                $infoClient[2]["infoClient"]["civilite"]=$donnees["civilite"];
                $infoClient[2]["infoClient"]["sommeil"]=$donnees["sommeil"];
                $infoClient[2]["infoClient"]["codeFiscal"]=$donnees["codeFiscal"];
                echo json_encode($infoClient);                
            }else{
                $infoClient[0]=false;
                echo json_encode($infoClient);
            }
        }
        $reponse->closeCursor();
     /* Si le login appartient aux admins */   
    }else if(in_array($login,$loginA_array)){
        $reponse=$bdd->prepare("SELECT password FROM admin WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        while($donnees=$reponse->fetch()){
            if($donnees["password"]==md5($password)){
                $infoAdmin[0]=true;
                $infoAdmin[1]="admin";
                echo json_encode($infoAdmin);                
            }else{
                $infoAdmin[0]=false;
                echo json_encode($infoAdmin);
            }
        }
        $reponse->closeCursor();
     /* Si le client appartient aux representants */
    }else if(in_array($login,$loginR_array)){
        $reponse=$bdd->prepare("SELECT password FROM representant WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        while($donnees=$reponse->fetch()){
            if($donnees["password"]==md5($password)){
                $infoRepresentant[0]=true;
                $infoRepresentant[1]="representant";
                echo json_encode($infoRepresentant);                
            }else{
                $infoRepresentant[0]=false;
                echo json_encode($infoRepresentant);
            }
        }
        $reponse->closeCursor();
    /* Si le client appartient aux approuveurs */
    }else if(in_array($login,$loginAP_array)){
        $reponse=$bdd->prepare("SELECT password FROM approuveur WHERE login=:login");
        $reponse->execute(array(
            "login"=>$login
        ));
        while($donnees=$reponse->fetch()){
            if($donnees["password"]==md5($password)){
                $infoApprouveur[0]=true;
                $infoApprouveur[1]="approuveur";
                echo json_encode($infoApprouveur);                
            }else{
                $infoApprouveur[0]=false;
                echo json_encode($infoApprouveur);
            }
        }
        $reponse->closeCursor();
    }else{
        $arrErreur[0]=false;
        echo json_encode($arrErreur);
    }
}else{
    $arrErreur[0]=false;
    echo json_encode($arrErreur);
}

?>