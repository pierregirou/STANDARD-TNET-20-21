<?php
	session_start();
	set_time_limit(0);
	include("./config.php5");
	include_once("./produit.php5");
	
	
	$fields=array("refproduit","libelle","codeColori","codeGammeTaille","codetailledebut","codetaillefin","codeSaison","marque","theme","famille","sousfamille","modele","codeLigne","nonComandable","description","codeEan","poids","champsstat","libtaille","libcolori","prix","codetarif","stockdisponible","stockencmd","stockaterme","separateur","libMarque", "com1","com2","com3","com4","com5");
	
	
	//recuperer les positions
	$prod=new Produit();
	$nbrLignes=0;
	$pos=$prod->getPosProduit();
	foreach($fields as $field){
		${$field.'pos'}=$pos[$field];
	}	

	$etat="";
	$nomFichier = "";
	//parcourir le dossier a_importer
	if ($handle = opendir('./import_files')) {
		/* recuperer le nom du fichier a importer */
		while (false !== ($trFile = readdir($handle))) {
			$deb=substr($trFile,0,3);			
			if(strtoupper($deb)=="ART"){ $nomFichier=$trFile;  }
		}
		closedir($handle);
		
		if( $nomFichier<>""){
			if (!$fp = fopen("./import_files/$nomFichier","r")) {
				echo "Echec de l'ouverture du fichier";
				exit;
			} else {
				while(!feof($fp)) {
					$nbrLignes+=1;
					// On récupère une ligne
					$ligne  	= fgets($fp);
					$chaine 	= utf8_encode($ligne);
					$separateur	= $separateurpos;
					$sp = split("$separateur", $chaine);
					foreach($fields as $field){
						if(${$field."pos"}<>"" && !empty($sp[${$field."pos"}-1]))	{ 
							${$field}=addslashes(trim($sp[${$field."pos"}-1]));
							
						} else {
							${$field}="";
						}
					}
					$long=strlen($codeGammeTaille);
					$gamme=substr($codeGammeTaille,0,3);
					$codetaille=substr($codeGammeTaille,3,$long);
					$nbrProdCol=$prod->getNbrProdByCol($codeSaison,$refproduit,$codeColori,$codetarif);
					$nbrProdTaille=$prod->getNbrProdByColTaille($codeSaison,$refproduit,$codeColori,$codetaille,$codetarif);
					$nbrtaille=$prod->getNbrtailleByCol($gamme,$codetaille);
					$nbrsais=$prod->getNbrSaison($codeSaison);
					mysql_query("SET NAMES UTF8"); 
					$tables=array("marque","theme","famille","sousfamille","modele");
					foreach($tables as $table){
						$nbr=$prod->getNbr($table,${$table});
						if($nbr==0 && ${$table}<>"" ){
							$add=$prod->addChamp($table,${$table});
						}
					}
					

					if($nbrtaille==0){
						$instaille=$prod->addTaille($codetaille,$libtaille,$gamme);
					}
				
					if($nbrsais==0){
						if($codeSaison<>""){		
							if($codeSaison=="000"){
								$inssais=$prod->addSaison($codeSaison,$codeSaison,"FRA");
								$inssais=$prod->addSaison($codeSaison,$codeSaison,"ANG");	
							}else{
								$libsaison=$prod->getLibSaison($codeSaison,"FRA");
								$inssais=$prod->addSaison($codeSaison,$libsaison,"FRA");
								$libsaison=$prod->getLibSaison($codeSaison,"ANG");
								$inssais=$prod->addSaison($codeSaison,$libsaison,"ANG");
							}
						
						}
					}
					
					if($refproduit<>""){
						// ajouter detailproduit
						
						$codeLigne = str_replace("/","",$codeLigne);
						if($nbrProdTaille==0){ 
							$insdet=$prod->addDetailProduit($refproduit,$libelle,$codeColori,$gamme,$codetailledebut,$codetaillefin,$codeSaison,$marque,$theme,$famille,$sousfamille,$modele,$codeLigne,$nonComandable,$description,$poids,$champsstat,$codeEan,$codetaille,$prix,$codetarif,$stockdisponible,$stockencmd,$stockaterme,$libcolori,$libMarque,$com1,$com2,$com3,$com4,$com5);
						}
						if($nbrProdCol==0){ 
							$insprod=$prod->addProduit($refproduit,$libelle,$codeColori,$gamme,$codetailledebut,$codetaillefin,$codeSaison,$marque,$theme,$famille,$sousfamille,$modele,$codeLigne,$nonComandable,$description,$poids,$champsstat,$codetarif,$stockdisponible,$stockencmd,$stockaterme,$prix,$libcolori,$libMarque,$com1,$com2,$com3,$com4,$com5);
						} else {
							if ($stockdisponible === "") {
								$stockdisponible = "0";
							}
							if ($stockencmd === "") {
								$stockencmd = "0";
							}
							if ($stockaterme === "") {
								$stockaterme = "0";
							}
							
							//$insprod=$prod->updateProduit($refproduit,$codeColori,$codeSaison,$codetarif,$stockdisponible,$stockencmd,$stockaterme);
							$insDprod=$prod->updateDProduit($refproduit,$codeColori,$codeSaison,$codetarif,$stockdisponible,$stockencmd,$stockaterme,$codetaille);
						}
						
					}
					echo "<br><br>";
				}
			}
		}
		fclose($fp); // On ferme le fichier
	// unlink("../a_importer/$nomFichier");
	}
?>