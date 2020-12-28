<?php
/* Récupère les informations des représentants */

include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $clientRep[0]=true;
    /* Récupère les clients associés au codeclient du représentant */
    
    // Pour inwitex
    //$reponse=$bdd->prepare("SELECT DISTINCT client.*,fonction FROM client INNER JOIN contact ON CONCAT(client.codeClient,client.raisonSociale,client.complementLivraison) = CONCAT(contact.codeClient,contact.nom,contact.prenom) WHERE client.codeClient IN (


    $reponse=$bdd->prepare("SELECT client.* FROM client
        INNER JOIN repclient ON repclient.codeclient = client.codeClient
        INNER JOIN representant ON representant.coderep = repclient.coderep
        WHERE representant.login = :login");
    $reponse->execute(array(
        "login"=>$_POST["loginRepresentant"]
    ));
    $i=0;
	
		
    while($donnees=$reponse->fetch()){
        $clientRep[1]["loginClient"][$i]=$donnees["login"];
        $clientRep[1]["nomSociete"][$i]=$donnees["nomSociete"];
        $clientRep[2][$i]["infoClient"]["codeClient"]=$donnees["codeClient"];
        $clientRep[2][$i]["infoClient"]["nomSociete"]=$donnees["nomSociete"];
        $clientRep[2][$i]["infoClient"]["raisonSociale"]=$donnees["raisonSociale"];
        $clientRep[2][$i]["infoClient"]["complementLivraison"]=$donnees["complementLivraison"];
        $clientRep[2][$i]["infoClient"]["telephone"]=$donnees["telephone"];
        $clientRep[2][$i]["infoClient"]["fax"]=$donnees["fax"];
        $clientRep[2][$i]["infoClient"]["email"]=$donnees["email"];
        $clientRep[2][$i]["infoClient"]["login"]=$donnees["login"];
        $clientRep[2][$i]["infoClient"]["codeLangue"]=$donnees["codeLangue"];
        $clientRep[2][$i]["infoClient"]["dateDerniereVisite"]=$donnees["dateDerniereVisite"];
        $clientRep[2][$i]["infoClient"]["nombreVisite"]=$donnees["nombreVisit"];
        $clientRep[2][$i]["infoClient"]["codeTarif"]=$donnees["codeTarif"];
        $clientRep[2][$i]["infoClient"]["raisonSocialeFact"]=$donnees["raisonSocialeFact"];
        $clientRep[2][$i]["infoClient"]["complementFacturation"]=$donnees["complementFacturation"];
        $clientRep[2][$i]["infoClient"]["codePostalFact"]=$donnees["codePostalFact"];
        $clientRep[2][$i]["infoClient"]["villeFact"]=$donnees["villeFact"];
        $clientRep[2][$i]["infoClient"]["paysFact"]=$donnees["paysFact"];
        $clientRep[2][$i]["infoClient"]["tauxRemise"]=$donnees["tauxRemise"];
        $clientRep[2][$i]["infoClient"]["codeSurveillance"]=$donnees["codeSurveillance"];
        $clientRep[2][$i]["infoClient"]["categorie"]=$donnees["categorie"];
        $clientRep[2][$i]["infoClient"]["geo"]=$donnees["geo"];
        $clientRep[2][$i]["infoClient"]["souscategorie"]=$donnees["souscategorie"];
        $clientRep[2][$i]["infoClient"]["centrale"]=$donnees["centrale"];
        $clientRep[2][$i]["infoClient"]["souscentrale"]=$donnees["souscentrale"];
        $clientRep[2][$i]["infoClient"]["civilite"]=$donnees["civilite"];
        $clientRep[2][$i]["infoClient"]["sommeil"]=$donnees["sommeil"];
        $clientRep[2][$i]["infoClient"]["fonction"]=utf8_encode($donnees['fonction']);
        $clientRep[2][$i]["infoClient"]["codeFiscal"]=$donnees['codeFiscal'];

        $i++;
    }
    echo json_encode($clientRep);
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>
