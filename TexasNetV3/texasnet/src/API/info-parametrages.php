<?php
include('connect.php');
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $reponse=$bdd->query("SELECT * FROM parametrage");
    $retour=$reponse->fetch();
    $adresse1=$retour["adresse1"];
    $nomSociete=$retour["nomsociete"];
    $messageSoc=$retour["messageSoc"];
    $adresse2=$retour["adresse2"];
    $telephone=$retour["telephone"];
    $fax=$retour["fax"];
    $email=$retour["email"];
    $siteweb=$retour["siteweb"];
    $codeLangue=$retour["codeLangue"];
    $codeDevise=$retour["codeDevise"];
    $codeTarif=$retour["codeTarif"];
    $dateMinLivraison=$retour["dateMinLivraison"];
    $texteCommandeFra=$retour["texteCommandeFra"];
    $texteCommandeAng=$retour["texteCommandeAng"];
    $saisonCommande=$retour["saisonCommande"];
    $photoLargeur=$retour["photoLargeur"];
    $photoHauteur=$retour["photoHauteur"];
    /***************************************************** */
    $arrParametrage[0]=true;
    $arrParametrage[1]["nomSociete"]=$nomSociete;
    $arrParametrage[1]["messageSoc"]=$messageSoc;
    $arrParametrage[1]["adresse1"]=$adresse1;
    $arrParametrage[1]["adresse2"]=$adresse2;
    $arrParametrage[1]["telephone"]=$telephone;
    $arrParametrage[1]["fax"]=$fax;
    $arrParametrage[1]["email"]=$email;
    $arrParametrage[1]["siteweb"]=$siteweb;
    $arrParametrage[1]["codeLangue"]=$codeLangue;
    $arrParametrage[1]["codeDevise"]=$codeDevise;
    $arrParametrage[1]["codeTarif"]=$codeTarif;
    $arrParametrage[1]["dateMinLivraison"]=$dateMinLivraison;
    $arrParametrage[1]["texteCommandeFra"]=$texteCommandeFra;
    $arrParametrage[1]["texteCommandeAng"]=$texteCommandeAng;
    $arrParametrage[1]["saisonCommande"]=$saisonCommande;
    $arrParametrage[1]["photoLargeur"]=$photoLargeur;
    $arrParametrage[1]["photoHauteur"]=$photoHauteur;
    echo json_encode($arrParametrage);
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>