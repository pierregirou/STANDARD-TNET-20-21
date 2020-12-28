<?php

	require('./config-soap.php');
	$_POST = json_decode(file_get_contents("php://input"),true);
	$user = [];
	
	
	// if($_POST["login"] && $_POST["pwd"]) {
		// if ($_POST["login"] === 'tristan') {
			// $user["id"] = 1;
			// $user["username"] = $_POST["login"];
			// $user["type"] = 'client';
			// $user["tarifCode"] = 'ZPA';
			// $user["addressDelivery"] = ['12 rue de adresse de livraison 42300 Roanne'];
			// $user["addressBill"] = ['28 rue de adresse de facturation 42300 Roanne'];	
		// } elseif ($_POST["login"] === 'admin') {
			// $user["id"] = 2;
			// $user["username"] = $_POST["login"];
			// $user["type"] = 'admin';
			// $user["tarifCode"] = 'ZPA';
			// $user["addressDelivery"] = ['12 rue de adresse de livraison 42300 Roanne'];
			// $user["addressBill"] = ['28 rue de adresse de facturation 42300 Roanne'];	
		// }
	// }		

	if($_POST["login"] && $_POST["pwd"]) {
		$JSON->Entete->Ressource = "Client";
		$JSON->Entete->Type = "Connexion";
		
		$login 		= "login=".$_POST["login"]."";
		$password 	= "password=".md5($_POST["pwd"])."";
		
		$filtreDetail = $login .";". $password;
		$JSON->Contenu->Filtre = $filtreDetail; // ne JAMAIS mettre d'espace	
		$JSON->Contenu->NbMaxLigne = 0;
		$JSON->Contenu->CommencerA = 0;	
		
		$paramJSON		= json_encode($JSON);
		$getValue 		= json_decode($client->Lister($paramJSON));
		$user			= $getValue->Resultats;

	}
	echo json_encode($user[0]);
?>