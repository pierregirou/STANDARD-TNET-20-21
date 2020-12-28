<?php
	header('Content-Type:application/json');
	include('connect.php');
	
	
	$tbs = array();
	$tab = array();
	$fields = array() ;	
	$req_ordre = $bdd->query('SELECT * FROM menu ORDER BY ordre_menu');	
	$i = 0;
	
	while( $numero = $req_ordre->fetch()){
		$nom = ucfirst(str_replace('-', '', $numero['nom']));
		$num = $numero['ordre_menu'];
		$noms = "code".$nom;
		if ($i<6) { $concaten  .= "code".$nom.",";} else { $concaten  .= "code".$nom;} 					
		$i++;
		
		$actif = $numero['actif'];
		if ($actif == 1){
			$fields [] = $nom; 
			$tab[] = $noms;
			$tbs[] = $nom;
		}
	}
	$tb = array();		
	
	
	$sql_menu = $bdd->query("SELECT COUNT(*) as nbr FROM menu  WHERE actif='1'");
	$nb_actif = $sql_menu->fetch();
	$actif_menu = $nb_actif['nbr'];
	$tab_marque = "";
	
	$sql = $bdd->query('SELECT DISTINCT * FROM produit p');
	while($data = $sql->fetch()){
		$var1 = utf8_encode($data[$tab[0]]);
		$var2 = utf8_encode($data[$tab[1]]);
		$var3 = utf8_encode($data[$tab[2]]);
		$var4 = utf8_encode($data[$tab[3]]);
		$var5 = utf8_encode($data[$tab[4]]);
		$var6 = utf8_encode($data[$tab[5]]);
		$var7 = utf8_encode($data[$tab[6]]);
		
		if(strpos($tab_marque, $var1) === false){	
			$tab_marque.= $var1.";";
		}
		switch ($actif_menu){
			case 1 : 
				$tb[$var1] = $var1;
			break;
			
			case 2 :
			if (!isset($tb[$var1])){
				$tb[$var1] = array();
			}
				$tb[$var1][$var2] = $var2;
			
			break;
			
			case 3 :
			if (!isset($tb[$var1])){
				$tb[$var1] = array();
			}
			if (!isset($tb[$var1][$var2])){
				$tb[$var1][$var2] = array();
			}
				$tb[$var1][$var2][$var3] = $var3;
			break;
			
			case 4 :
			if (!isset($tb[$var1])){
				$tb[$var1] = array();
			}
			if (!isset($tb[$var1][$var2])){
				$tb[$var1][$var2] = array();
			}
			if (!isset($tb[$var1][$var2][$var3])) {
				$tb[$var1][$var2][$var3] = array();
			}
				$tb[$var1][$var2][$var3][$var4] = $var4;
			break;
			
			case 5 :
			if (!isset($tb[$var1])){
				$tb[$var1] = array();
			}
			if (!isset($tb[$var1][$var2])){
				$tb[$var1][$var2] = array();
			}
			if (!isset($tb[$var1][$var2][$var3])) {
				$tb[$var1][$var2][$var3] = array();
			}
			if (!isset($tb[$var1][$var2][$var3][$var4])) {
				$tb[$var1][$var2][$var3][$var4] = array();
			}
			$tb[$var1][$var2][$var3][$var4][$var5] = $var5;
			break;
			
			case 6 :
			if (!isset($tb[$var1])){
				$tb[$var1] = array();
			}
			if (!isset($tb[$var1][$var2])){
				$tb[$var1][$var2] = array();
			}
			if (!isset($tb[$var1][$var2][$var3])) {
				$tb[$var1][$var2][$var3] = array();
			}
			if (!isset($tb[$var1][$var2][$var3][$var4])) {
				$tb[$var1][$var2][$var3][$var4] = array();
			}
			if (!isset($tb[$var1][$var2][$var3][$var4][$var5])) {
					$tb[$var1][$var2][$var3][$var4][$var5] = array();
			}
			$tb[$var1][$var2][$var3][$var4][$var5][$var6] = $var6;
			break;		
			
			case 7 :
			if (!isset($tb[$var1])){
				$tb[$var1] = array();
			}
			if (!isset($tb[$var1][$var2])){
				$tb[$var1][$var2] = array();
			}
			if (!isset($tb[$var1][$var2][$var3])) {
				$tb[$var1][$var2][$var3] = array();
			}
			if (!isset($tb[$var1][$var2][$var3][$var4])) {
				$tb[$var1][$var2][$var3][$var4] = array();
			}
			if (!isset($tb[$var1][$var2][$var3][$var4][$var5])) {
					$tb[$var1][$var2][$var3][$var4][$var5] = array();
			}
			if (!isset($tb[$var1][$var2][$var3][$var4][$var5][$var6])) {
					$tb[$var1][$var2][$var3][$var4][$var5][$var6] = array();
			}
			$tb[$var1][$var2][$var3][$var4][$var5][$var6] = $var6;
			break;
			
			default: $tb[1] = "Produits";
		}		
	}
	echo json_encode($tb);
?>
                		