<?php
class docHTMLPDF{
	
	public function getParametres(){
		global $bdd;			
		$reponse = $bdd->query("select * from parametrage");
		$donnees = $reponse->fetch();
		return $donnees;
	}

		
	public function getModules(){
		global $bdd;			
		$reponse = $bdd->query("select * from modules");
		$donnees = $reponse->fetch();
		return $donnees;
	}
	
	public function getProfil($login){	
		global $bdd;		
		$reponse = $bdd->query("select * from client where login='$login'");
		$donnees = $reponse->fetch();
		return $donnees;
	}
	
	public function getContact($login){	
		global $bdd;			
		$nom = substr($login, 4);
		$reponse = $bdd->query("select * from contact where nom= '$nom'");
		$donnees = $reponse->fetch();
		return $donnees;
	}

		
	public function getRepresentant($login){	
		global $bdd;			
		$reponse = $bdd->query("select * from representant where login= '$login'");
		$donnees = $reponse->fetch();
		return $donnees;
	}
	
	public function getAdresse($num){
		global $bdd;			
		$reponse = $bdd->query("select * from adresse where id='$num'");
		$donnees = $reponse->fetch();
		return $donnees;
	} 
	
	public function getProduit($numcmd){
		global $bdd;			
		$reponse = $bdd->query("
		SELECT t.refproduit, t.libelle, t.codeColori, t.codeGammeTaille, t.codetailledebut, t.codetaillefin, t.quantite, t.prix,
		SUM(t.codetaille1) AS scodetaille1,
		SUM(t.codetaille2) AS scodetaille2,
		SUM(t.codetaille3) AS scodetaille3,
		SUM(t.codetaille4) AS scodetaille4,
		SUM(t.codetaille5) AS scodetaille5,
		SUM(t.codetaille6) AS scodetaille6,
		SUM(t.codetaille7) AS scodetaille7,
		SUM(t.codetaille8) AS scodetaille8,
		SUM(t.codetaille9) AS scodetaille9,
		SUM(t.codetaille10) AS scodetaille10,
		SUM(t.codetaille11) AS scodetaille11,
		SUM(t.codetaille12) AS scodetaille12,
		SUM(t.codetaille13) AS scodetaille13,
		SUM(t.codetaille14) AS scodetaille14,
		SUM(t.codetaille15) AS scodetaille15,
		SUM(t.codetaille16) AS scodetaille16
		FROM (
			SELECT DISTINCT d.refproduit, p.libelle, d.codeColori, d.codeGammeTaille, d.codetailledebut, d.codetaillefin, lc.quantite, lc.prix,
				(CASE WHEN d.codetaille=1 THEN quantite ELSE 0 END) AS codetaille1,
				(CASE WHEN d.codetaille=2 THEN quantite ELSE 0 END) AS codetaille2,
				(CASE WHEN d.codetaille=3 THEN quantite ELSE 0 END) AS codetaille3,
				(CASE WHEN d.codetaille=4 THEN quantite ELSE 0 END) AS codetaille4,
				(CASE WHEN d.codetaille=5 THEN quantite ELSE 0 END) AS codetaille5,
				(CASE WHEN d.codetaille=6 THEN quantite ELSE 0 END) AS codetaille6,
				(CASE WHEN d.codetaille=7 THEN quantite ELSE 0 END) AS codetaille7,
				(CASE WHEN d.codetaille=8 THEN quantite ELSE 0 END) AS codetaille8,
				(CASE WHEN d.codetaille=9 THEN quantite ELSE 0 END) AS codetaille9,
				(CASE WHEN d.codetaille=10 THEN quantite ELSE 0 END) AS codetaille10,
				(CASE WHEN d.codetaille=11 THEN quantite ELSE 0 END) AS codetaille11,
				(CASE WHEN d.codetaille=12 THEN quantite ELSE 0 END) AS codetaille12,
				(CASE WHEN d.codetaille=13 THEN quantite ELSE 0 END) AS codetaille13,
				(CASE WHEN d.codetaille=14 THEN quantite ELSE 0 END) AS codetaille14,
				(CASE WHEN d.codetaille=15 THEN quantite ELSE 0 END) AS codetaille15,
				(CASE WHEN d.codetaille=16 THEN quantite ELSE 0 END) AS codetaille16
			FROM lignecommande lc 
			INNER JOIN detailproduit d ON d.idproduit = lc.idDetailProduit
			INNER JOIN produit p ON p.refproduit = d.refproduit
			WHERE numCommande='$numcmd' 
		) t
		GROUP BY t.refproduit, t.libelle, t.codeColori
		ORDER BY t.refproduit, t.libelle, t.codeColori

		");
		echo '<table class="sample partieProduitsColoris">
				<tr>
					<th width="80" height="20%">Produit fini / Coloris </th>
				</tr>';
		while($donnees = $reponse->fetch()){	
			echo "<tr>";
			echo "<td>".$donnees['refproduit'].' '.$donnees['libelle']."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo '<table class="sample">';
					echo '<tr><th width="50"></th>'; //120
									$tailledeb=$donnees['codetailledebut'];
									$taillefin=$donnees['codetaillefin'];
									$nbrtaille=$taillefin-$tailledeb+1;
						
							$redgamme=$this->getTailles($donnees["codeGammeTaille"],$donnees["codetailledebut"],$donnees["codetaillefin"]);
							$tabTaille=array();
							$i=0;
							
							while($tail=$redgamme->fetch()){
								echo "<th width=5>".$tail['libelle']."</th>";
								$tabTaille[$i]=$tail['libelle'];
								$i++;
							}
							for($i=0;$i<(16-$nbrtaille);$i++){
								echo "<th width=5></th>";
							}
							echo '<th width="5">PU</th><th>Quantit&eacute;</th><th width="40">Montant</th>';
						
						echo "</tr>";
								$qteCol=0;
								$mtCol=0;
								//tester la longeur du libellé coloris
								
								$libcolori=$this->getlibColori($donnees['refproduit'],$donnees['codeColori']);
								if(strlen($libcolori)<=25){ 
									$libcolori=$donnees['codeColori'] . " - " .$libcolori;
								}else {
									$libcolori=$donnees['codeColori'] . " - " .substr($libcolori,0,25)."...";
								}
								echo '<tr>
									<td class="sansBorder" style="width:80px;font-size:10px; ">'.utf8_encode($libcolori).'</td>'; 
									for($i=$tailledeb;$i<17;$i++){
										if($donnees['scodetaille'.$i] > 0){
											echo "<td class=val>".$donnees['scodetaille'.$i]."</td>"; 
											$qtePcs = $donnees['scodetaille'.$i];
											$qteCol+=$qtePcs; 
										} else {
											echo "<td class=val></td>";
										}										
									} 
									
									if($tailledeb > 1) {
										$taillecomp = $tailledeb - 1;
										for($i=0;$i<$taillecomp;$i++){
											echo "<td class=val></td>";
										} 
									}
									
									$mtCol=$donnees['prix']*$qteCol;
									echo '
									<td class="val" style="font-size:10px;">'.$donnees['prix'].'€</td>
									<td class="val" style="font-size:10px;">'.$qteCol.'</td>
									<td class="val" style="font-size:10px;">'.number_format($mtCol,2).' € </td>
								</tr>'; 
					echo "</table>";
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	public function getDProduitInfo($idDetailProduit){
		global $bdd;			
		$reponse = $bdd->query("select * from detailproduit where idproduit='$idDetailProduit'");
		$donnees = $reponse->fetch();
		return $donnees;
	}
	
	public function getProduitInfo($refproduit,$codeColori){
		global $bdd;			
		$reponse = $bdd->query("select * from produit where refproduit='$refproduit' AND codeColori = '$codeColori' ");
		$donnees = $reponse->fetch();
		return $donnees;
	}
	
	public function getCmdBynum($num){
		global $bdd;			
		$reponse = $bdd->query("select * from commande where numCommande ='$num'");
		$donnees = $reponse->fetch();
		return $donnees;
	}
	
	public function getProduitByRefSais($ref,$sais){
		global $bdd;			
		$reponse = $bdd->query("select *  from produit where refproduit='$ref' and codeSaison='$sais'");
		$donnees = $reponse->fetch();
		return $donnees;
	}
	
	public function getlibColori($ref,$codeColori){
		global $bdd;		
		$reponse = $bdd->query("select libcolori from produit where refproduit='$ref' and codeColori='$codeColori'");
		$donnees = $reponse->fetch();
		return $donnees['libcolori'];
	}
	
	public function getTailles($gamme,$deb,$fin){
		global $bdd;			
		$reponse = $bdd->query("select *  from taille where codegamme='$gamme' and codetaille between '$deb' and '$fin' order by codetaille asc");
		return $reponse;
	}
		
	public function getColByRefSais($numcmd,$ref,$sais){
		global $bdd;			
		$reponse = $bdd->query("select distinct * from lignecommande where numCommande='$numcmd' and refProduit='$ref' and codesaison='$sais'");
		return $reponse;
	}
		
	public function getHistLigne($idDetailProduit,$numcmd){
		global $bdd;			
		$reponse = $bdd->query("select quantite,prix from lignecommande where numCommande='$numcmd' AND idDetailProduit='$idDetailProduit'");
		$donnees = $reponse->fetch();
		return $donnees;
	}
}
?>