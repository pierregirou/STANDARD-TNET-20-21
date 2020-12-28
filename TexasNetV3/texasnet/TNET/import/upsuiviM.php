<?php
session_start();
include("../../includes/config.php5");
include_once("../../classes/commander.php5");
include_once("../../classes/produit.php5");
$fields=array("refCommande","nomFacturation","societeFacturation","adresseFacturation","villeFacturation","codePostalFacturation","paysFacturation","nomLivraison","societeLivraison","addresseLivraison","villeLivraison","codePostalLivraison","paysLivraison","fraisDePort","tva","totaleHtCommande","nbrProduitCommande","detailCommande","statut","saison","codeClient","noColis","noSuivi","site","noCommandeTw");

//valeur
mysql_query("SET NAMES UTF8"); 
$cmd=new Commander();
$nbrLignes=0;
$prod=new Produit();
$pos=$prod->getPosSuivi();
$separateur=$pos["separateur"];
foreach($fields as $field)
{
	${$field.'pos'}=$pos[$field];	
}
//parcourir le dossier a_importer
$nomFichier="";
if ($handle = opendir('../../commandes/maj')) {
    /* recuperer le nom du fichier a importer */
    while (false !== ($trFile = readdir($handle))) {
		$deb=substr($trFile,0,8);
		if($deb=="commande")
		{
			$nomFichier=$trFile;
	    }
    }
    closedir($handle);
}
if( $nomFichier<>"")
{
	if (!$fp = fopen("../../commandes/maj/$nomFichier","r")) {
	echo "Echec de l'ouverture du fichier";
	exit;

	}else {
		
		$line=0;
		$noni=0;
		while(!feof($fp)) {
			$nbrLignes+=1;
			$line+=1;;
			// On récupère une ligne
			$Ligne = fgets($fp);
			$chaine=utf8_encode($Ligne);
			// echo $separateur;
			
			$sp=split("$separateur", $chaine);
				echo "<br><br>";
			foreach($fields as $field){
				if(${$field."pos"}<>"" && !empty($sp[${$field."pos"}-1]))	{ ${$field}=addslashes(trim($sp[${$field."pos"}-1]));}
				else{
					${$field}="";
				}
				echo $field . " : " .${$field} . "<br>";
			}
			if ($statut == 1) {
				$etat = "Intégrer";
			} elseif ($statut == 2) {
				$etat = "Livraison en cours";
			} elseif ($statut == 3) {
				$etat = "Livré";
			} elseif ($statut == 4) {
				$etat = "Facturé";
			} else {
				$etat = "Rayé";
			}
			
			$refCommande = substr($refCommande, 19,25);
			$noSuivi = substr($noSuivi, 11);		
			// $noSuivi = str_replace("N° Suivi: ", "", $noSuivi);		
			$noSuivi = str_replace("Mode sans Colisage - Voir bon transporteur,", "", $noSuivi);
			$noSuivi = str_replace("N° Suivi: ", "", $noSuivi);				
			$noSuivi = str_replace(",", "|", $noSuivi);				

			
			if($cmd->exists($refCommande)==1){
				if($statut!=9){
						$cmd->updatestatut($noSuivi,$refCommande);
					}
			}
		}
	}
}
	
	// fclose($fp); // On ferme le fichier
	$cmd->addLog('IMPORT AUTOMATIQUE','SUIVI COMMANDE',0,$line-1);
	// unlink("../a_importer/$nomFichier");

?>