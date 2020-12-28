<?php
/* Récupère les informations des approuveurs */

include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){

    $loginRepresentant=$_POST["loginRepresentant"];
    $loginApprouveur=$_POST["loginApprouveur"];
    if($loginRepresentant !== null && $loginRepresentant !== '') {
      $codeClientApprouveur = substr($loginRepresentant,0,3);
      $codeClientApprouveur = strtoupper($codeClientApprouveur);
    } else {

        // On récupère le code client approuveur à partir du login approuveur
     $reponse=$bdd->prepare("SELECT codeClient FROM repclient WHERE login=:login");
     //$reponse=$bdd->prepare("SELECT codeClient FROM approuveur WHERE login=:login");
     $reponse->execute(array(
         "login"=>$loginApprouveur
     ));
     $donnees=$reponse->fetch();
     $codeClientApprouveur = $donnees["codeClient"];
     $reponse->closeCursor();
    }

        // On récupère les logins client à partir du code client approuveur
    //$reponse=$bdd->prepare("SELECT * FROM client WHERE codeClient=:codeClient ORDER BY raisonSociale");
    $reponse=$bdd->prepare("SELECT * FROM repclient WHERE coderep=:codeClient");
    $reponse->execute(array(
        "codeClient"=>$codeClientApprouveur
    ));

    $i=0;
    $listeCommandes[0]=false;
    while($donnees=$reponse->fetch()){
            // Enfin on récupère les commandes selon la liste des logins clients
            // Partie infos clients
        $reponse2=$bdd->prepare("SELECT * FROM client WHERE codeClient=:codeClient");
        $reponse2->execute(array(
            "codeClient"=>$donnees['codeclient']
        ));
        if($donnees2=$reponse2->fetch()) {
                // Partie infos commandes
            $reponse3=$bdd->prepare("SELECT * FROM commande WHERE login=:login AND valid=1 AND nbrpiece > 0 ORDER BY numCommande desc");
            $reponse3->execute(array(
                "login"=>$donnees2["login"]
            ));
            while($donnees3=$reponse3->fetch()){
                $listeCommandes[0]=true;
                $listeCommandes[1][$i]["numCommande"]=$donnees3["numCommande"];
                $listeCommandes[1][$i]["nom"]=$donnees2["nom"];
                $listeCommandes[1][$i]["prenom"]=$donnees2["prenom"];
                $listeCommandes[1][$i]["matricule"]=$donnees2["numeroMatricule"];
                $listeCommandes[1][$i]["fonction"]=$donnees2["fonction"];
                $listeCommandes[1][$i]["agence"]=$donnees2["codeClient"];
                $listeCommandes[1][$i]["montant"]=$donnees3["montant"]+$donnees3["fraisport"]+$donnees3["mttva"];
                $listeCommandes[1][$i]["nbrpiece"]=$donnees3["nbrpiece"];
                $listeCommandes[1][$i]["dateCommande"]=$donnees3["dateValidation"];
                $listeCommandes[1][$i]["motifRefus"]=utf8_encode($donnees3["motifRefus"]);
                if(trim($donnees3["etat"]) === "") {
                    $listeCommandes[1][$i]["etat"]=0;
                } else {
                    $listeCommandes[1][$i]["etat"]=$donnees3["etat"];
                }
                $i++;
            }
            $reponse3->closeCursor();
        }
        $reponse2->closeCursor();
    }
    $reponse->closeCursor();
    echo json_encode($listeCommandes);

}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>
