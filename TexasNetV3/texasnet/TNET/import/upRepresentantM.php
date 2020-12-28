<?php

include ("connectM.php");

	$fields=array("idRepresentant","codeRep","nom","login","passwords", "langue");	
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
				if(strtolower($deb)=="repre"){
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
				
				$Ligne = fgets($fp,400);// recup les lignes du fichier (approuveur.txt)
				$chaine=utf8_encode($Ligne);// on encode en UTF8 les ligne récupérer dans $Ligne
				$separateur=["separateur"];
				$separateur=';';
				// $sp contient un tableau  associatif de chaque $ligne ou $chaine avec index séparer par ";"
				$sp=explode($separateur, $chaine);
				
				if(isset($sp[1]) || isset($sp[2]) || isset($sp[3]) || isset($sp[4]) || isset($sp[5])){
					array_push($codeRep, $sp[0]);
					array_push($nom, $sp[1]);
					array_push($login, $sp[2]);
				    array_push($passwords, $sp[3]);
					array_push($langue, $sp[4]);
				};
				
				$line+=1;
			};
			for ($i=1; $i <= $nbrLignes; $i++) { 
				echo  $idRepresentant[$i]." - ".$codeRep[$i]." - ".$nom[$i]." - ".$login[$i]." - ".$passwords[$i]." - ".$langue[$i];
				
				$data = $bdd->prepare("SELECT * FROM `representant` 
										WHERE representant.login = :login");
				$data->execute(array(
					'login' => $login[$i],
				 ));
				$dataRequest = $data->fetch();
				
				if($login[$i] !== $dataRequest['login']){
					echo " --- insert";
					$req = $bdd->prepare("INSERT INTO `representant` 
											VALUES('', :codeRep, :nom, :login, :passwords, :langue)");
					$req->execute(array(
						'codeRep' => $codeRep[$i],
						'nom' => $nom[$i],
						'login' => $login[$i],
						'passwords' => md5("TexasNet.".$passwords[$i]),
						'langue' => trim($langue[$i])
					));
					echo "<br>";
				}else{
					echo " --- update";
					$reqUpdate= $bdd->prepare("UPDATE `representant` 
												SET coderep = :codeRep, nom = :nom, representant.password = :passwords, langue = :langue 
												WHERE representant.login = :login");
					$reqUpdate->execute(array(
						'codeRep' => $codeRep[$i],
						'nom' => $nom[$i],
						'login' => $login[$i],
						'passwords' => md5("TexasNet.".$passwords[$i]),
						'langue' => trim($langue[$i])
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