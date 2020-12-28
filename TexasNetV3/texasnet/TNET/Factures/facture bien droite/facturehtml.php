<style type="text/Css">

.titrecmd
{
	font-size:20px;
	font-style:inherit;
	font-weight:bold;
	}
.titreAdresse
{
	font-size:16px;
	font-style:inherit;
	text-align:center;
	background-color:#C7C7C7;
	width:350;
	
}
.partieReglementFacturation
{
	font-size:16px;
	font-style:inherit;
	text-align:center;
	background-color:#C7C7C7;
	width:325;
	
}
.partieProduitsColoris th
{
	width:36px;
}
.textAdresse
{
	font-size:14px;
	font-style:inherit;
}
.textentete
{
	font-size:12px;
	font-style:inherit;
	text-align:center;
}

#espace{
	margin-left:20px;
}
#titrecentre
{
		font-size:13px;
   margin-left: 50px;
   padding: 195px;
   position:fixed;
}
#titredroite
{
		font-size:13px;
  margin-left: 450px;

   position:fixed;
}
table.sample 
{
  vertical-align: top;
  text-align: left;

	
}
table.sample th {
	border: solid 1px #cccccc;
  vertical-align: top;
  text-align: center;
background-color:#C7C7C7;
  font-size:10px;
  padding:5px;

}
table.sample td {
  border: solid 0.5px #cccccc;
  vertical-align: top;
}
.sansBorder
{
	border:none;
font-size:12px;
}
.val
{
	border:none;
	text-align:right;
	font-size:13px;
}

</style>

<?php	
	include("factures/_class/facture.class.php5");
	include("../API/connect.php");	
	$cmd=new docHTMLPDF();


	if(isset($_GET['numcmd'])){
		$num=$_GET['numcmd'];
	}else{
		$num="";
	}

	$param		= $cmd->getParametres();
	$par		= $cmd->getModules();
	$redcmd		= $cmd->getCmdBynum($num);	
	$rep		= $cmd->getRepresentant($redcmd['commentaire4']);	
	$login 		= $redcmd['login'];
	$profil		= $cmd->getProfil($login);
	$adrliv		= $cmd->getAdresse($redcmd['adresselivraison']);	
	$adrfac		= $cmd->getAdresse($redcmd['adressefacturation']);	
		
	
	if($par['gestionGroupe']==0){ 
		$libAdresseFac="Adresse de facturation"; 
	} else { 
		$libAdresseFac="Adresse de magasin";   
	}

	
?>
<table>
		<tr style="text-align:center">
			<td colspan="3"><img src="../Images/logo-net.png" width="450" height="100" /></td>			
			<td colspan="1"></td>
		</tr>
		
		<tr>
			<td colspan="1"></td>
			<td colspan="3"><span width="318" height="118"> </span></td>
		</tr>
		
		<tr>
			<td><span class="titrecmd">COMMANDE N&deg; <?php echo $num;?></span></td>
			<td> <span style="color:#FFF">___________</span><span class="textentete">Date commande <?php echo date("d/m/Y",strtotime($redcmd['datecommande']));?></span></td><td><span style="color:#FFF">________________</span><span>Page 1</span></td>
		</tr>
		
		<tr>
			<td><span class="textentete">Client : <?php echo $profil['codeClient'].'<br>'?></span><span style="color:#FFF">___ __</span><span class="textentete"><?php echo $profil['raisonSociale'];?> </span></td>
			<td> <span style="color:#FFF">___________</span><span class="textentete">Date livraison souhait&eacute;e &nbsp;<?php  echo "  ".date("d/m/Y",strtotime($redcmd['datecommande']));?></span></td>
			<td></td>
		</tr>
		<tr>
			<td><span class="textentete">Représentant : <?php echo $rep['login']. " - " .$rep['nom'];?> </span></td>
			<td> <span style="color:#FFF">___________</span><span class="textentete">Saison : <?php  echo $param['saisonCommande'];?></span></td>

		</tr>
		<tr>
			<td></td>
			<td><span style="color:#FFF">___________</span><span class="textentete">Type de commande : RIN</span></td>
			

		</tr>
	</table>
	
	<br /><br />
	
	<table>
		<tr>
			<td>
				<table class="sample" style="font-size:16px;">
					<tr>
						<td class="titreAdresse">Adresse de livraison</td>
					</tr>
	
					<tr>
						<td class="textAdresse">
							<?php
								if($par['gestionGroupe']==0){
									echo $profil['raisonSociale']." ";
									echo $profil['complementLivraison']."<br />";
								} else {
									echo $redcmd['adresselivraison']."<br />";
									if($redcmd['adresselivraison']=="..."){
										echo $redcmd['prenom']." ".$redcmd['nom']."<br />";
									}
								}								
								echo $adrliv["adresse1"]."<br>";
								if ($adrliv["adresse2"] != "null") { echo $adrliv["adresse2"]."<br />"; }
								echo $adrliv["codePostal"]. " - " . $adrliv["ville"]. "<br>";
								echo $adrliv["pays"];
							?>
						</td>
					</tr>
				</table> 
			</td>
			<td><div id="espace"> </div></td>
			<td>
				<table class="sample" style="font-size:16px"><tr><td  class="titreAdresse"><?php echo ($libAdresseFac);?></td></tr>
					<tr>
						<td class="textAdresse">
							<?php
								if($par['gestionGroupe']==0){
									echo $profil['raisonSocialeFact']." ";
									echo $profil['complementFacturation']."<br />";
								} else {
									echo $redcmd['adressefacturation']."<br />";
									echo $profil['raisonSocialeFact']."<br />";
									echo $profil['complementFacturation'];

								} 
								
								echo $adrfac["adresse1"]."<br>";
								if ($adrfac["adresse2"] != "null") { echo $adrfac["adresse2"]."<br />"; }
								echo $adrfac["codePostal"]. " - " . $adrfac["ville"]. "<br>";
								echo $adrfac['pays'];
							?>
						</td>
					</tr>
				</table> 
			</td>
		</tr>
	</table>
	<br />
	
	<?php 
		$redprod	= $cmd->getProduit($num); 
	?>
<br />

	<table>
		<tr>
			<td>
				<table class="sample" style="font-size:16px">
					<tr>
						<td class="partieReglementFacturation">R&eacute;glement / Payment</td>
					</tr>
					<!--<td><div id="espace"> </div></td>-->
					<tr>
						<td class="textAdresse"> Selon vos conditions actuelles</td>
					</tr>
				</table> 
			</td>
			
			<td>
				<table class="sample" style="font-size:16px" >
					<tr>
						<td class="partieReglementFacturation">Renseignements facturation</td>
					</tr>
					<tr>
						<td class="textAdresse">

						<table class="sample" border="0" cellspacing="0" cellpadding="0">
							<tr style="width:25%" >
								<td>Qt&eacute; totale</td>								
								<td><?php echo $redcmd['nbrpiece'];?></td>
							</tr>
							<tr>
								<td>Montant</td>
								<td><?php echo number_format($redcmd['montant'], 2, ',', '')?> €</td>
							</tr>
							<?php 
								if($redcmd['escompte'] > 0 ){
									$somme = $redcmd['montant'] - $redcmd['escompte'] + $redcmd['mtTPH'];
							?>
							<tr>
								<td>Escompte</td>
								<td><?php echo $redcmd['escompte'];?> €</td>
							</tr>
							<tr>
								<td>TPH</td>
								<td><?php echo $redcmd['mtTPH'];?> €</td>
							</tr>
							<?php } else {
								$somme = $redcmd['montant'];
							}
								if($redcmd['fraisport'] > 0 ){
									$somme = $somme + $redcmd['fraisport'];
							?>
							<tr>
								<td>Frais de port</td>
								<td><?php echo $redcmd['fraisport'];?> €</td>
							</tr>
							<?php } 
							if($redcmd['mttva'] > 0 ){									
									$somme = $somme + $redcmd['mttva'];
							?>
							<tr>
								<td>TVA</td>
								<td><?php echo number_format($redcmd['mttva'], 2, ',', '')?> €</td>
							</tr>
							<?php } ?>
							<tr>	
								<td>Montant total HT <span style="color:#FFF">________________</span></td>
								<td><?php echo number_format($somme, 2, ',', '')?> €<span style="color:#FFF">_______________</span></td>
							</tr>
						</table>
						
						</td>
					</tr>
				</table> 
			</td>
		</tr>
	</table>
	<br /><hr/>
	<table>
		<tr>
			<td><span class="titrecmd">Commentaires</span></td>
			<td></td>
		</tr>
		<?php
		//Affichage des commentaire		
		for($j=1;$j<4;$j++){				
			if($redcmd['commentaire'.$j]<>""){
				if($j < 3) {
					echo '<tr><td></td><td><span class=textAdresse>Commentaire '.$j.' : '.$redcmd['commentaire'.$j].'</span></td></tr>';						
				} 				
			}
		}
		if ($redcmd['commentaire4'] <> "") {
			echo '<tr><td></td><td><span class=textAdresse>Commentaire 4: Prise par '.$redcmd['commentaire4'].' - '.$rep['nom'].'</span></td></tr>';
		}
		if ($redcmd['commentaire5'] == 0) {
			echo '<tr><td></td><td><span class=textAdresse>Commentaire 5 : Sans cintre</span></td></tr>';
		} else {
			echo '<tr><td></td><td><span class=textAdresse>Commentaire 5 : Avec cintre</span></td></tr>';
		}
		?>
	</table>
	 
<?php echo $par['texteCommandeFra']; ?>