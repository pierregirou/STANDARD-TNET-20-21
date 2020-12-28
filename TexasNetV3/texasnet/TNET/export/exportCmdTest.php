<?php
include ('./connect.php');

$csvCommande ="Num commande;Code client;Numero adresse livraison;Adresse livraison;Frais de port;total HT;Nbr produits;Detail commande;Statut;Saison;Commentaire1;Commentaire2;Commentaire3;Commentaire4;Commentaire5;Numero;Date livraison souhaite;Points;Point Relais;NO adresse;Raison Social livraison; Complement RS livraison; adresse 1; adresse2; CP livraison; ville livraison; pays livraison;Adresse email; Telephone\r\n";

$dataTableCommandeNBR = $bdd->prepare("SELECT count(*) as nbr FROM commande WHERE valid=1  AND etat = 'A valider' ");
$dataTableCommandeNBR->execute();
$detailsCommandesNBR = $dataTableCommandeNBR->fetch();

$nbrCmd = $detailsCommandesNBR['nbr'];

	// -----------------------------------récupération du codeSaison --> saisonCommande de la table parametrage------------------------------------------------ 
    $dataSaisonCommande = $bdd->query("SELECT saisonCommande FROM parametrage");
    $SaisonCommande = $dataSaisonCommande->fetch();
	
	$codeSaison = $SaisonCommande["saisonCommande"];

$i = 0;
echo $i . " --> " . $nbrCmd . "</br>";
while($i < $nbrCmd){
    // -----------------------------------récupération du detail de la commande------------------------------------------------ 
    
    $dataTableCommande = $bdd->prepare("SELECT * FROM commande,adresse,client WHERE commande.adresselivraison = adresse.id AND commande.login=client.login AND  etat IN ('A valider') AND valid = 1 ");
    $dataTableCommande->execute();
    $detailsCommandes = $dataTableCommande->fetchAll();

    // DATA DE LA TABLE COMMANDE ET LIGNECOMMANDE
    $numCommande = $detailsCommandes[$i]["numCommande"];
	$raisonSociale = utf8_encode($detailsCommandes[$i]["raisonSociale"]);
	$complementRaisonSociale = utf8_encode($detailsCommandes[$i]["complementLivraison"]);
    $numeroAdresseLivraison = $detailsCommandes[$i]["numero"];
    $idAdresseLivraison = $detailsCommandes[$i]["adresselivraison"];
    $adresseLivraison = $detailsCommandes[$i]["adresse1"]." - " .$detailsCommandes[$i]["adresse2"];
    $fraisPort = $detailsCommandes[$i]["fraisport"];
    $totalHt = $detailsCommandes[$i]["montant"];
    $nbrProduits = $detailsCommandes[$i]["nbrpiece"];
    $statut = $detailsCommandes[$i]["etat"];
	
    // gestion des articles commandés qui corresponde à leurs commandes. Je fais une requête qui va chercher le numCommande
    $dataNumCommandeTableLigneCommande = $bdd->prepare("SELECT * FROM lignecommande lc
                                                        INNER JOIN detailproduit dp ON lc.idDetailProduit = dp.idproduit
                                                        WHERE numCommande=:numCommande"
                                                        );
    $dataNumCommandeTableLigneCommande->execute(array(
        "numCommande"=>$numCommande
    ));
    $numsCommandesTableLigneCommande = $dataNumCommandeTableLigneCommande->fetchAll();
    //déclaration de notre variable contenant les informations des articles commandés
    $detailCommande = "";

    // je concatène à  cette variable tous les articles (avec les infos souhaitées séparer par des pipes) tant que celle-ci correspondes au numéro de commande
    foreach ($numsCommandesTableLigneCommande as $numCom) {
        if($numCom['numCommande'] === $detailsCommandes[$i]["numCommande"]){
            $detailCommande .= $numCom["codeean13"]."|"
                              .$numCom["quantite"]."|"
                              .$numCom["prix"].""
                              ."$";
        // il manque la quantitée préparée, quantitée livée, reste à livrer
        }
    }
	$commentaire = utf8_encode($detailsCommandes[$i]["commentaire1"]);
	$commentaire1 = substr($commentaire, 0, 39);
	// $commentaire2 = substr($commentaire, 40, 79);
	// $commentaire3 = substr($commentaire, 80, 119);
    $commentaire4 = $detailsCommandes[$i]["commentaire4"];
    $commentaire5 = $detailsCommandes[$i]["commentaire5"];
    $dateLivraisonSouhaite = $detailsCommandes[$i]["datelivraison"];
	$points = "0";
    $pointRelais = $detailsCommandes[$i]["numPointRetrait"];
    $adresseEmail = $detailsCommandes[$i]["login"];
    $telephone = $detailsCommandes[$i]["telephone"];
    // il n'apparaisse pas dans le fichier csv d'avant
    $montantTva = $detailsCommandes[$i]["mttva"];
    $montantTtc = $detailsCommandes[$i]["mtttc"];
    $montantTph = $detailsCommandes[$i]["mtTPH"];
    $escompte = $detailsCommandes[$i]["escompte"];
    $codeRepresentant = $detailsCommandes[$i]["codeRep"];
    $dateCommande = $detailsCommandes[$i]["datecommande"]; 
    $typeCommande = $detailsCommandes[$i]["typecommande"];
    $idAdresseFacturation = $detailsCommandes[$i]["adressefacturation"];
    $numsuivi = $detailsCommandes[$i]["numsuivi"];
    $valid = $detailsCommandes[$i]["valid"];
    $transporteur = $detailsCommandes[$i]["transporteur"];
    // $login = $detailsCommandes[$i]["login"];
    $login = $detailsCommandes[$i]["codeClient"];
    // $login = "CWMED04201";
    $nom = $detailsCommandes[$i]["nom"];
    $prenom = $detailsCommandes[$i]["prenom"];
    $service = $detailsCommandes[$i]["service"];
    $dateValidation = $detailsCommandes[$i]["dateValidation"];
    $dateExport = $detailsCommandes[$i]["dateExport"];

	$adresse1 = $detailsCommandes[$i]["adresse1"];
	$adresse2 = $detailsCommandes[$i]["adresse2"];
	$cpLivraison = $detailsCommandes[$i]["codePostal"];
	$villeLivraison = $detailsCommandes[$i]["ville"];
	$codePays = $detailsCommandes[$i]["pays"];
	
    // j'écris les infos provennat des table commande et lignecommande
    $csvCommande .= 'V/CDE TexasNET N. '.$numCommande.";".$login.";".$numeroAdresseLivraison.";".$adresseLivraison.";".$fraisPort.";".$totalHt.";".$nbrProduits.";".$detailCommande.";0;".$codeSaison.";".$commentaire1.";".$commentaire2.";".$commentaire3.";".$commentaire4.";".$commentaire5.";".$numCommande.";".$dateLivraisonSouhaite.";".$points.";".$pointRelais.";";

    // -----------------------------------récupération du detail de la livraison------------------------------------------------ 
    /*$dataLivraison = $bdd->prepare("SELECT * FROM adresse
                                    INNER JOIN commande ON adresse.id= commande.adresselivraison
                                    WHERE id = :idAdresseLivraison"
                                    );
    $dataLivraison->execute(array(
        "idAdresseLivraison"=> $idAdresseLivraison
    ));
    $detailLivraisonCommande = $dataLivraison->fetchAll();
    // DATA DE LA TABLE ADRESSE
    $adresse1 = $detailLivraisonCommande[$i]["adresse1"];
    $adresse2 = $detailLivraisonCommande[$i]["adresse2"];
    $cpLivraison = $detailLivraisonCommande[$i]["codePostal"];
    $villeLivraison = $detailLivraisonCommande[$i]["ville"];
    $paysLivraison = $detailLivraisonCommande[$i]["pays"];
    $codePays =  $detailLivraisonCommande[$i]["codePays"];
    $numero = $detailLivraisonCommande[$i]["numero"]; */
	
    // je concatène à la suite les infos récupérer de la table adresse
    $csvCommande .= strtoupper($numero.";".$raisonSociale.";".$complementRaisonSociale.";".$adresse1.";".$adresse2.";".$cpLivraison.";".$villeLivraison.";".$codePays.";");
	$csvCommande .= $adresseEmail.";".$telephone.";\r\n";

	$updateCmd = $bdd->prepare("UPDATE commande SET etat='Exporte' WHERE numCommande=:numCmd");
    /* CHANGE L'ETAT DES COMMANDES EN BDD
    $updateCmd->execute(array(
        "numCmd"=> $numCommande
    ));*/
	
    $i++;
	
	
}

$csvCommande = utf8_decode($csvCommande);

echo $csvCommande;

$nom="CTNET-".date("dmYHi").".csv"; // je donne un nom au fichier qui va etre creer

if($nbrCmd>0){
	// toutes les donnees sont en chaine, on va envoyer :
	$fp = fopen("./commandes/".$nom,"a"); // ouverture du fichier en ecriture
	fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF))); // on ecrit le nom et email dans le fichier
	fputs($fp, $csvCommande); // on ecrit le nom et email dans le fichier
	fputs($fp, "\n"); // on va a la ligne
	fclose($fp); 

	//Ecrire une copie du fichier

	$file = "./commandes/$nom";
    $newfile = "./commandes/sauvegardes/$nom";
    
		if (!copy($file, $newfile)) {
		echo "La copie $file du fichier a echoue...\n";
	}
}

?>
