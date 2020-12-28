<?php

include_once("connectM.php");

	$fields=array("codeClient","marque","libelleMarque");
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
				alors le fichier (MarqueCl.txt) est enregistrer dans $nomFichier */
				if(strtolower($deb)=="marqu"){
					$nomFichier=$trFile;
				}
			}
			closedir($handle);
		}
	if( $nomFichier<>""){
		//le fichier a importer est dans ./import_files si il ne le toruve pas il y aune erreur
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
				$sp = explode($separateur, $chaine);
				$codeClient[]=$sp[0];
        $marqueCode[]=$sp[1];
        $libMarque[]=TRIM($sp[2]);
				$line+=1;
			};
			for($i = 1; $i <= $nbrLignes; $i++) {
				// echo "------codeClient-->".$codeClient[$i]."-------codeM->".$marqueCode[$i]."<br>";
			 	$req = "SELECT * FROM marqueclient WHERE codeClient=".$codeClient[$i]." AND marque=".$marqueCode[$i] . " AND libelleMarque = $libMarque[$i] ";
				//echo $req . "<br>";
				$data = $db->prepare($req);
				$data->execute();
				$dataRe = $data->fetch();

				if ($codeClient[$i] !== $dataRe["codeClient"] && $marqueCode[$i] !== $dataRe["marque"] && $libMarque[$i] !== $dataRe["libelleMarque"]) {
					echo " --- insert------codeClient-->".$codeClient[$i]."-------codeM->".$marqueCode[$i]."<br>";
					$req = $db->prepare("INSERT INTO marqueclient VALUES ('".$codeClient[$i]."','". $marqueCode[$i]."','". $libMarque[$i]."')");
					$req->execute();
				}
				$nbrAjout++;
			}


				fclose($fp); // On ferme le fichier

				echo '</br>Total lignes : '.$nbrLignes;
				echo '</br>Total ajouts : '.$nbrAjout;
		}
	}
?>
