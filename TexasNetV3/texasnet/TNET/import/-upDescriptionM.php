<?php

include ("./connect.php");


$fields=array("codeSaison","codeProduit","codeLangue","libelleLangue","description");
$nbrLignes = 0;
$nbrAjout = 0;
foreach ($fields as $field) {
	// creation d'une variable dynamique des diff field qui est egal a un tableau  
	${$field} = [$field];
}
$nomFichier = "";
//parcourir le dossier  ./import_files
if ($handle = opendir('./import_files/')) {
	while (false !== ($trFile = readdir($handle))) {
		$deb = substr($trFile, 0, 3);
		/*si il trouve un ficher nommer "des" pour les 3 premier caractères
				alors le fichier (contact.txt) est enregistrer dans $nomFichier */
		if (strtolower($deb) == "des") {
			$nomFichier = $trFile;
		}
	}
	closedir($handle);
}

if ($nomFichier <> "") {
	//le fichier a importer est dans ./import_files si il ne le toruve pas il y aune erreur
	if (!$fp = fopen("./import_files/$nomFichier", "r")) {
		echo "Echec de l'ouverture du fichier";
		exit;
	} else {
		$line = 0;
		while (!feof($fp)) {
			$nbrLignes += 1;

			$Ligne = fgets($fp); // recup les lignes du fichier (approuveur.txt)

			$chaine = $Ligne; // on encode en UTF8 les ligne récupérer dans $Ligne
			$separateur = ["separateur"];
			$separateur = ';';
			
			echo $Ligne." -/-<br><br><br>";
			// $sp contient un tableau  associatif de chaque $ligne ou $chaine avec index séparer par ";"
			$sp = explode($separateur, $chaine);
			
			array_push($codeSaison, $sp[0]);
	
			if (isset($sp[1]) || isset($sp[2]) || isset($sp[3]) || isset($sp[4])) {
				array_push($codeProduit, $sp[1]);
				array_push($codeLangue, $sp[2]);
				array_push($libelleLangue, $sp[3]);
				array_push($description, str_replace("\\","",nl2br($sp[4])));
			};
			$line += 1;
		};

		for($i = 1; $i <= $nbrLignes; $i++) {

			echo  $codeSaison[$i] ." - ". $codeProduit[$i] ." - ". $codeLangue[$i] . " - " . $libelleLangue[$i] . " - " . $description[$i]."<br>";

			$data = $bdd->prepare("SELECT * FROM `description` WHERE codeProduit=:codeProduit AND codeSaison=:codeSaison AND codeLangue=:codeLangue");
			$data->execute(array(
				"codeSaison" => $codeSaison[$i],
			 	"codeProduit" => $codeProduit[$i],
			 	"codeLangue" => $codeLangue[$i]
			));
			$dataDescription = $data->fetch();
			
			 if ($codeProduit[$i] !== $dataDescription["codeProduit"] && $codeSaison[$i] !== $dataDescription["codeSaison"]  && $codeLangue[$i] !== $dataDescription["codeLangue"] ) {
			 	echo " ---- insert";
			 	$req = $bdd->prepare("INSERT INTO `description` VALUES('', :codeSaison, :codeProduit, :codeLangue, :description )");
			 	$req->execute(array(
			 		"codeSaison" => $codeSaison[$i],
			 		"codeProduit" => $codeProduit[$i],
			 		"codeLangue" => $codeLangue[$i],
			 		"description" =>$description[$i]
			 	));
			 	echo "<br>";
			  }else {
			  	echo " ---- update";
			  	$reqUpdate = $bdd->prepare("UPDATE `description` SET description=:description WHERE codeSaison=:codeSaison AND codeProduit=:codeProduit AND codeLangue=:codeLangue");
			  	$reqUpdate->execute(array(
			  		"codeSaison" => $codeSaison[$i],
			  		"codeProduit" => $codeProduit[$i],
			  		"codeLangue" => $codeLangue[$i],
			  		"description" => $description[$i]
			  	));
			  	echo "<br>";
			  }
			 $nbrAjout++;
		}
		fclose($fp); // On ferme le fichier

		echo '</br>Total lignes : ' . $nbrLignes;
		echo '</br>Total ajouts : ' . $nbrAjout;
	}
}
?>