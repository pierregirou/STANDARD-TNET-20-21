<?php
	session_start();
	include("/home/oakwoodb2b/public_html/import/config.php");
	include("/home/oakwoodb2b/public_html/import/classes/produit.php");
	include_once("/home/oakwoodb2b/public_html/import/classes/commander.php");
	$fields=array("codeEan","stockdisponible","stockencmd","stockaterme");
	//valeur
	mysql_query("SET NAMES UTF8"); 
	$cmd=new Commander();
	$nbrLignes=0;
	$prod=new Produit();
	$pos=$prod->getPosStock();
	$separateur=$pos["separateur"];
	foreach($fields as $field){
		${$field.'pos'}=$pos[$field];	
	}

	$videQte = $prod->stockNegatif();
	$nomFichier="";
	if ($handle = opendir('/home/oakwoodb2b/public_html/import/import_files/')) {
		/* recuperer le nom du fichier a importer */
		while (false !== ($trFile = readdir($handle))) {			
			$deb=substr($trFile,0,3);
			if(strtoupper($deb)== "STK"){
				$nomFichier=$trFile;
			}
		}
		closedir($handle);
	}
	if( $nomFichier<>""){
		if (!$fp = fopen("/home/oakwoodb2b/public_html/import/import_files/$nomFichier","r")) {
			echo "Echec de l'ouverture du fichier";

			exit;

		} else {
			while(!feof($fp)) {
				$nbrLignes+=1;
			// On récupère une ligne
				$Ligne = fgets($fp,400);
				$chaine=utf8_encode($Ligne);
				$sp = split("$separateur", $chaine);
					foreach($fields as $field){
						if(${$field."pos"}<>"" && !empty($sp[${$field."pos"}-1]))	{ 
							${$field}=addslashes(trim($sp[${$field."pos"}-1]));
							
						} else {
							${$field}="";
						}
					}

				$upDP=$prod->updateStock($codeEan,$stockdisponible,$stockencmd,$stockaterme);
			}
		}
		fclose($fp); // On ferme le fichier
		
		$upDPFULL=$prod->majFullStock();
		$cmd->addLog('IMPORT AUTOMATIQUE','STOCK',0,$nbrLignes);
		
	}

?>



    
