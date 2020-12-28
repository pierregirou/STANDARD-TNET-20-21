<?php
include_once("connectM.php");

	$fields=array("codeRep","codeClient");
		$nbrLignes=0;
		$nbrAjout=0;
		foreach($fields as $field){
			// creation d'une variable dynamique des diff field qui est egal a un tableau  
			${$field}=[$field];
		} 
		$nomFichier = "";
	//parcourir le dossier  ./import_files
		if ($handle = opendir('./import_files')) {
			while (false !== ($trFile = readdir($handle))) {
				$deb=substr($trFile,0,5);
				/*si il trouve un ficher nommer "repcl" pour les 5 premier caractères
				alors le fichier (repclient.txt) est enregistrer dans $nomFichier */
				if(strtolower($deb)=="repcl"){
					$nomFichier=$trFile;				
				}
			}
			closedir($handle);
		} 
	if( $nomFichier<>""){
		//le fichier a importer est dans ./import_filessi il ne le toruve pas il y aune erreur
		if (!$fp = fopen("./import_files/$nomFichier","r")) {
			echo "Echec de l'ouverture du fichier";
			exit;
		} else {
			$line=0;
			while(!feof($fp)) {
				$nbrLignes+=1;
				
				$Ligne = fgets($fp,400); // recup les lignes du fichier (approuveur.txt)
				$chaine=utf8_encode($Ligne); // on encode en UTF8 les ligne récupérer dans $Ligne
				$separateur=["separateur"];
				$separateur=';';
				// $sp contient un tableau  associatif de chaque $ligne ou $chaine avec index séparer par ";"
				$sp=explode($separateur, $chaine);

				array_push($codeRep, $sp[0]);
				
				if(isset($sp[1])){
					array_push($codeClient, $sp[1]);
				};
				
				$line+=1;
		};
		for ($i = 1; $i <= $nbrLignes; $i++) {

			echo $i . " - " . $codeRep[$i] . " - " . $codeClient[$i];

			$data = $bdd->prepare("SELECT * FROM `repclient` WHERE coderep = :codeRep AND codeclient = :codeClient");
			$data->execute(array(
				"codeRep" => $codeRep[$i],
				"codeClient" => $codeClient[$i]
			));
			$dataRe = $data->fetch();
			

			if ($codeRep[$i] !== $dataRe['coderep'] && $codeClient[$i] !== $dataRe['codeclient']) {
				echo " --- insert";
				$req = $bdd->prepare("INSERT INTO `repclient` VALUES (:codeRep, :codeClient, :vide)");
				$req->execute(array(
					"codeRep" => $codeRep[$i],
					"codeClient" => $codeClient[$i],
					"vide" => ''
				));
				echo "<br>";
			} else {
				echo " --- update";
				$reqUpdate = $bdd->prepare("UPDATE `repclient` SET coderep = :codeRep WHERE codeclient = :codeClient");
				$reqUpdate->execute(array(
					"codeClient" => $codeClient[$i],
					"codeRep" => $codeRep[$i]
				));
				echo "<br>";
			}
			$nbrAjout++;
		}

			fclose($fp); // On ferme le fichier
			
			echo '</br>Total lignes : '.$nbrLignes; 
			echo '</br>Total ajouts : '.$nbrAjout; 
		}
	}
?>