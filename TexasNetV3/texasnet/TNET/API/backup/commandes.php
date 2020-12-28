<?php
/* Création de la commande pour les clients ->
    -Une commande doit être créée lorsque l'utilisateur s'authentifie ou accède à la page produit==>valid=0
    -Tant que la commande n'est pas valide (valid=1) On récupère la commande existante associée au client
    -Si le client est associé à une commande valide en crée une nouvelle non valide
*/
include('connect.php');
$login_array=[];

$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
    $login_array[]= $donnees["login"];
    $email=$donnees["email"];
    $tel=$donnees["telephone"];
}
$reponse->closeCursor();

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){ //Vérifie l'état de la requête 
    $login=$_POST["login"];
    $type=$_POST["type"];
    if(in_array($login,$login_array)){
        
        $reponse=$bdd->query("SELECT timerCommande FROM modules"); //récupère le timmerCommande de la table modules
        $retour=$reponse->fetch();
        $timerCommande=$retour["timerCommande"];
        $reponse->closeCursor();
        $k=0;
        if(((int)($timerCommande))>0){ //si le timer est strictement supérieur à 0 sec
            $reponse=$bdd->prepare("SELECT login,datecommande,DAY(datecommande) AS jourcommande, MONTH(datecommande) AS moiscommande, YEAR(datecommande) AS anneecommande, HOUR(datecommande) AS heurecommande,  MINUTE(datecommande) AS minutecommande , SECOND(datecommande) AS secondecommande FROM commande WHERE valid=:valid");
            $reponse->execute(array(
                "valid"=>0
            ));
            while($donnees=$reponse->fetch()){
                $k++;
                //récupère les infos de temps
                $datecommande=$donnees["datecommande"]; //date de création de la commande
                $jourcommande=$donnees["jourcommande"]; //isole le jour de la commande
                $moiscommande=$donnees["moiscommande"]; //isole le mois de la commande
                $anneecommande=$donnees["anneecommande"]; //isole l'année de la commande
                $heurecommande=$donnees["heurecommande"]; //isole l'heure de la commande
                $minutecommande=$donnees["minutecommande"]; //isole la minute de la commande
                //si x sec converti en nb de jours,heures,minutes,sec:
                $day=floor($timerCommande/86400); //convertir les secondes en jour
                $hour=(int)(($timerCommande/3600)%24); //convertir les secondes en heure
                $min=(int)(($timerCommande%3600)/60); //convertir les secondes en minute
                $sec=(int)(($timerCommande%3600)%60); //convertir les secondes restantes en secondes
                $datetime1 = new DateTime($datecommande); //création d'un datetime de la date de création de la commande
                $datetime2 = new DateTime('now'); //création d'un datetime de la date actuelle
                $interval = $datetime1->diff($datetime2);//détermine l'interval entre datetime1 et datetime2
                $commandeValide="false";
                //détermine si l'écart est supérieur à la durée de commande autorisée
                if((int)($interval->format("%Y"))<1){
                    if((int)($interval->format("%M"))<1){
                        if( (int)($interval->format("%d"))<=(int)($day) ){
                            if( ((int)($interval->format("%H"))<(int)($hour)) || ((int)($hour))==0 ){
                                $commandeValide="true";
                                if((int)($min)>0){
                                    if((int)($interval->format("%i"))<=(int)$min ){
                                        $commandeValide="true";
                                        
                                    }else{
                                        $commandeValide="false";
                                    }
                                }
                            }else{
                                $commandeValide="false";
                            }
                        }else{
                            $commandeValide="false";
                        }
                    }else{
                        $commandeValide="false";
                    }
                }else{
                    $commandeValide="false";
                }
                if($commandeValide=="false"){ //si la durée a expirée
                        
                    $reponse2=$bdd->prepare("SELECT numCommande FROM commande WHERE login=:login AND valid=0");
                    $reponse2->execute(array(
                        "login"=>$donnees["login"]
                    ));
                    $retour=$reponse2->fetch();
                    $numCommande=$retour["numCommande"];
                    $reponse2->closeCursor();

                    //récupère le numéro de la commande
                    $reponse2=$bdd->prepare("SELECT * FROM lignecommande WHERE numCommande=:numCommande");
                    $reponse2->execute(array(
                        "numCommande"=>$numCommande
                    ));
                    while($donnees2=$reponse2->fetch()){
                        $produit=$donnees2["idDetailProduit"];
                        $quant=$donnees2["quantite"];
                        //remet à jour les stocks
                        $req=$bdd->prepare("UPDATE detailproduit SET stockdisponible=stockdisponible+:stockp , stockencmd=stockencmd-:stockp WHERE idproduit=:idproduit");
                        $req->execute(array(
                            "stockp"=>$donnees2["quantite"],
                            "idproduit"=>$donnees2["idDetailProduit"]
                        ));
                    }
                    $reponse->closeCursor();

                    //supprime les lignescommandes en fonction de la numCommande
                    $req=$bdd->prepare("DELETE FROM lignecommande WHERE numCommande=:numCommande");
                    $req->execute(array(
                        "numCommande"=>$numCommande
                    ));
                    $req=$bdd->prepare("UPDATE commande SET montant=0 ,nbrpiece=0 WHERE numCommande=:numCommande");
                    $req->execute(array(
                        "numCommande"=>$numCommande
                    ));

                    //supprime la commande
                    $req=$bdd->prepare("DELETE FROM commande WHERE login=:login AND valid=0");
                    $req->execute(array(
                        "login"=>$donnees["login"]
                    ));

                    //création d'une nouivelle commande
                    $req=$bdd->prepare("INSERT INTO `commande`(`datecommande`, `datelivraison`, `typecommande`, `montant`, `mttva`, `mtttc`, `nbrpiece`, `fraisport`, `etat`, `adresselivraison`, `adressefacturation`, `commentaire1`, `commentaire2`, `commentaire3`, `commentaire4`, `commentaire5`, `numsuivi`, `valid`, `codesaison`, `transporteur`, `login`, `nom`, `prenom`, `service`, `dateValidation`, `dateExport`, `codeRep`, `numPointRetrait`, `tel`, `mail`) VALUES (NOW(),NOW(),:typecommande,:montant,:mttva,:mtttc,:nbrpiece,:fraisport,:etat,:adresselivraison,:adressefacturation,:commentaire1,:commentaire2,:commentaire3,:commentaire4,:commentaire5,:numsuivi,:valid,:codesaison,:transporteur,:login,:nom,:prenom,:service,NOW(),NOW(),:codeRep,:numPointRetrait,:tel,:mail)");
                    $req->execute(array(
                        "typecommande"=>"commande",
                        "montant"=>0,
                        "mttva"=>0,
                        "mtttc"=>0,
                        'nbrpiece'=>0,
                        "fraisport"=>0,
                        "etat"=>"",
                        "adresselivraison"=>0,
                        "adressefacturation"=>0,
                        "commentaire1"=>"",
                        "commentaire2"=>"",
                        "commentaire3"=>"",
                        "commentaire4"=>"",
                        "commentaire5"=>"",
                        "numsuivi"=>"",
                        "valid"=>0,
                        "codesaison"=>"",
                        "transporteur"=>"",
                        "login"=>$donnees["login"],
                        "nom"=>"",
                        "prenom"=>"",
                        "service"=>"",
                        "codeRep"=>"",
                        "numPointRetrait"=>"",
                        "tel"=>$tel,
                        "mail"=>$email
                    ));
                }
            }
            $reponse->closeCursor();
        }

        $reponse=$bdd->prepare("SELECT COUNT(*) as nbCommande FROM commande WHERE login=:login"); //détermine le nombre de commandes de l'utilisateur
        $reponse->execute(array(
            "login"=>$login
        ));
        $donnee=$reponse->fetch();
        $reponse->closeCursor();
        if($donnee["nbCommande"]==0){ //si l'utilisateur n'a aucune commande (valide ou invalide) création d'une nouvelle commande avec valide=0
            $req=$bdd->prepare("INSERT INTO `commande`(`datecommande`, `datelivraison`, `typecommande`, `montant`, `mttva`, `mtttc`, `nbrpiece`, `fraisport`, `etat`, `adresselivraison`, `adressefacturation`, `commentaire1`, `commentaire2`, `commentaire3`, `commentaire4`, `commentaire5`, `numsuivi`, `valid`, `codesaison`, `transporteur`, `login`, `nom`, `prenom`, `service`, `dateValidation`, `dateExport`, `codeRep`, `numPointRetrait`, `tel`, `mail`) VALUES (NOW(),NOW(),:typecommande,:montant,:mttva,:mtttc,:nbrpiece,:fraisport,:etat,:adresselivraison,:adressefacturation,:commentaire1,:commentaire2,:commentaire3,:commentaire4,:commentaire5,:numsuivi,:valid,:codesaison,:transporteur,:login,:nom,:prenom,:service,NOW(),NOW(),:codeRep,:numPointRetrait,:tel,:mail)");
            $req->execute(array(
                "typecommande"=>"commande",
                "montant"=>0,
                "mttva"=>0,
                "mtttc"=>0,
                'nbrpiece'=>0,
                "fraisport"=>0,
                "etat"=>"",
                "adresselivraison"=>0,
                "adressefacturation"=>0,
                "commentaire1"=>"",
                "commentaire2"=>"",
                "commentaire3"=>"",
                "commentaire4"=>"",
                "commentaire5"=>"",
                "numsuivi"=>"",
                "valid"=>0,
                "codesaison"=>"",
                "transporteur"=>"",
                "login"=>$login,
                "nom"=>"",
                "prenom"=>"",
                "service"=>"",
                "codeRep"=>"",
                "numPointRetrait"=>"",
                "tel"=>$tel,
                "mail"=>$email
            ));
            ?>
            {
                "success":true,
                "message":"Initialisation de la commande réussie",
                "nbInvalid":<?php echo $k; ?>
            }
            <?php
        }else if($donnee["nbCommande"]>0){ //Si l'utilisateur possède des commandes valide ou invalide 

            //Récupère le nombre de commande invalide du client (valid=0)
            $reponse=$bdd->prepare("SELECT COUNT(DISTINCT valid) as nbValid FROM commande WHERE login=:login AND valid=:valid");
            $reponse->execute(array(
                "login"=>$login,
                "valid"=>0
            ));
            $donnee2=$reponse->fetch();
            $reponse->closeCursor();
            if($donnee2["nbValid"]==0){ //si toutes les commandes de l'utilisateur sont valides création d'une nouvelle commande avec valid=0
                $req=$bdd->prepare("INSERT INTO `commande`(`datecommande`, `datelivraison`, `typecommande`, `montant`, `mttva`, `mtttc`, `nbrpiece`, `fraisport`, `etat`, `adresselivraison`, `adressefacturation`, `commentaire1`, `commentaire2`, `commentaire3`, `commentaire4`, `commentaire5`, `numsuivi`, `valid`, `codesaison`, `transporteur`, `login`, `nom`, `prenom`, `service`, `dateValidation`, `dateExport`, `codeRep`, `numPointRetrait`, `tel`, `mail`) VALUES (NOW(),NOW(),:typecommande,:montant,:mttva,:mtttc,:nbrpiece,:fraisport,:etat,:adresselivraison,:adressefacturation,:commentaire1,:commentaire2,:commentaire3,:commentaire4,:commentaire5,:numsuivi,:valid,:codesaison,:transporteur,:login,:nom,:prenom,:service,NOW(),NOW(),:codeRep,:numPointRetrait,:tel,:mail)");
                $req->execute(array(
                    "typecommande"=>"commande",
                    "montant"=>0,
                    "mttva"=>0,
                    "mtttc"=>0,
                    'nbrpiece'=>0,
                    "fraisport"=>0,
                    "etat"=>"",
                    "adresselivraison"=>0,
                    "adressefacturation"=>0,
                    "commentaire1"=>"",
                    "commentaire2"=>"",
                    "commentaire3"=>"",
                    "commentaire4"=>"",
                    "commentaire5"=>"",
                    "numsuivi"=>"",
                    "valid"=>0,
                    "codesaison"=>"",
                    "transporteur"=>"",
                    "login"=>$login,
                    "nom"=>"",
                    "prenom"=>"",
                    "service"=>"",
                    "codeRep"=>"",
                    "numPointRetrait"=>"",
                    "tel"=>$tel,
                    "mail"=>$email
                ));
                
                ?>
                {
                    "success":true,
                    "message":"Création d'une nouvelle commande",
                    "nbInvalid":<?php echo $k; ?>
                }
                <?php
            }else if($donnee2["nbValid"]==1){ //si une commande est en cours (valid=0) on continue avec cette dernière
                $testC=0;
                if(0<0){
                    $testC=1;
                }else{
                    $testC=0;
                }
                ?>
                {
                    "success":true,
                    "message":"ok",
                    "nbCommande":<?php echo $testC; ?>,
                    "min":"<?php echo (int)($min); ?>"
                }
                <?php
            }
        }
    }else{
        ?>
        {
            "success":false,
            "message":"bad connexion"
        }
        <?php
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