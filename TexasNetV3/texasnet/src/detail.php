<?php		
	require('../config-soap.php');		
	$productArray	= [];
	
	$JSON->Entete->Ressource = "Client";
	
	$JSON->Contenu->Filtre = "PFCLEUNIK=683;CodeDepot=000;CodeGriffe=000;codeTarif=ZPA"; // ne JAMAIS mettre d'espace
	$JSON->Contenu->NbMaxLigne = 0;
	$JSON->Contenu->CommencerA = 0;	
	
	
	$paramJSON	= json_encode($JSON);	
	$getValue 	= json_decode($client->Test_NET_PF_DETAIL($paramJSON));
	
	$productArray 	= $getValue->Resultats;
	
	echo json_encode($productArray);
?>

