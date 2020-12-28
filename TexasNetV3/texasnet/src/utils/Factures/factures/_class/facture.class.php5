<?php
class docHTMLPDF{
	
	public function getParametre(){
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
	
	public function getAdresse($num){
		global $bdd;			
		$reponse = $bdd->query("select * from adresse where id='$num'");
		$donnees = $reponse->fetch();
		return $donnees;
	} 
	
	public function getProduit($numcmd){
		global $bdd;			
		$reponse = $bdd->query("select distinct * from lignecommande where numCommande='$numcmd'");
		echo '<table class="sample">
				<tr>
					<th width="80" height="20%">Produit fini / Coloris </th>
				</tr>';
		while($donnees = $reponse->fetch()){	
			$infoDProd = $this->getDProduitInfo($donnees['idDetailProduit']);
			$infoProd  = $this->getProduitInfo($infoDProd['refproduit'],$infoDProd['codeColori']);
			
			echo "<tr>";
				echo "<td>".$infoProd['refproduit'].' '.$infoProd['libelle']."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo '<table class="sample">';
						echo '<tr><th width="120"></th>';
									$tailledeb=$infoDProd['codetailledebut'];
									$taillefin=$infoDProd['codetaillefin'];
									$nbrtaille=$taillefin-$tailledeb+1;
						
							$redgamme=$this->getTailles($infoDProd["codeGammeTaille"],$infoDProd["codetailledebut"],$infoDProd["codetaillefin"]);
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
							echo '<th>Quantit&eacute;</th><th width="40">Montant</th>';
						
						echo "</tr>";
								$qteCol=0;
								$mtCol=0;
								//tester la longeur du libellé coloris
								if(strlen($infoProd['libcolori'])<=25){ 
									$libcolori=$infoProd['libcolori'];
								}else {
									$libcolori=substr($infoProd['libcolori'],0,25)."...";
								}
								echo '<tr>
									<td class="sansBorder">'.utf8_encode($libcolori).'</td>'; 
									
									for($i=0;$i<$nbrtaille;$i++){
										$idDP = $donnees['idDetailProduit'];
										$hist=$this->getHistLigne($donnees['idDetailProduit'],$numcmd);
										if (($infoDProd['codetaille']-$infoDProd["codetailledebut"]) == $i) {
											echo "<td class=val>".$hist[0]."</td>"; 
											$qteCol+=$hist[0]; 
											$mtCol+=$hist[0]*$hist[1];
										} else {
											echo "<td class=val></td>"; 
										}
									} 
									
									for($i=0;$i<(16-$nbrtaille);$i++){
										echo "<td class=val></td>";
									} 
									
									echo '
									<td class="val">'.$qteCol.'</td>
									<td class="val">'.number_format($mtCol,2).' € </td>
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