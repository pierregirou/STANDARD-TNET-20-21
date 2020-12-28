<?php

	class Produit 
	{
		public function addDetailProduit($ref,$libelle,$codecolori,$codegamme,$tailledeb,$taillefin,$saison,$marque,$theme,$famille,$sousfamille,$modele,$codeligne,$nomcmd,$description,$poids,$champsstat,$codeean13,$codetaille,$prix,$codetarif,$stockdisponible,$stockencmd,$stockaterme,$libcolori,$libmarque,$com1,$com2,$com3,$com4,$com5){
			$com .= $com2;
			$com .= $com3;
			$com .= $com4;
			$com .= $com5;
			$req="insert into detailproduit values('','$ref','$codecolori','$codegamme','$tailledeb','$taillefin','$nomcmd','$codetaille','$prix','$codetarif','$codeean13','$stockdisponible','$stockencmd','$stockaterme','')" ;
			echo $req;
			$exec=mysql_query($req);
		}
		
		public function addProduit($ref,$libelle,$codecolori,$codegamme,$tailledeb,$taillefin,$saison,$marque,$theme,$famille,$sousfamille,$modele,$codeligne,$nomcmd,$description,$poids,$champsstat,$codetarif,$stockdisponible,$stockencmd,$stockaterme,$prix,$libcolori,$libmarque,$com1,$com2,$com3,$com4,$com5,$libelle2,$texteLibre){	
			$com .= $com2;
			$com .= $com3;
			$com .= $com4;
			$com .= $com5;
			//echo $com;
			$req="insert into produit values('','$ref','$libelle','$codecolori','$codegamme','$tailledeb','$taillefin','$saison','$marque','$theme','$famille','$sousfamille','$modele','$codeligne','$nomcmd','$poids','$codetarif','$prix','$libcolori','$libmarque','$com1','$com2','$com3','$com4','$com5','0','0','0','0','0','0','$libelle2','$texteLibre')";
			echo $req."<br>";
			// echo "<br>";
			$exec=mysql_query($req);
		}
		
		
		public function updateProduit($ref,$codecolori,$saison,$codetarif,$stockdisponible,$stockencmd,$stockaterme){
			$req="update produit set stockdisponible=stockdisponible+'$stockdisponible',stockencmd=stockencmd+'$stockencmd',stockaterme=stockaterme+'$stockaterme' where refproduit='$ref' and codeSaison='$saison' and codecolori='$codecolori' and codetarif='$codetarif'  " ;
			// echo $req;
			$exec=mysql_query($req);
		}		
		
		public function updateDProduit($ref,$codecolori,$saison,$codetarif,$stockdisponible,$stockencmd,$stockaterme,$codetaille){
			$req="update detailproduit set stockdisponible='$stockdisponible',stockencmd='$stockencmd',stockaterme='$stockaterme' where refproduit='$ref' and codeSaison='$saison' and codecolori='$codecolori' and codetarif='$codetarif' and codetaille='$codetaille' " ;
			// echo $req;
			//$exec=mysql_query($req);
		}
		
		public function updateDProduit2($ean13,$codetarif,$stockdisponible,$stockencmd,$stockaterme){
			$req="update detailproduit set stockdisponible='$stockdisponible',stockencmd='$stockencmd',stockaterme='$stockaterme' where codeean13='$ean13' and codetarif='$codetarif'" ;
			// echo $req;
			$exec=mysql_query($req);
		}
				
		public function addTaille($codetaille,$libelle,$codegamme){
			$req="insert into taille values('','$codetaille','$libelle','$codegamme')";
			$ex=mysql_query($req);
			return $req;
		}
		
		public function addColori($id,$libelle){
			$req="insert into colori values('$id','FRA','$libelle')";
			//echo $req."<br>";
			$ex=mysql_query($req);
			return $req;
		}
		
		public function addSaison($id,$libelle,$langue){
			$req="insert into saison values('$id','$libelle','$langue')";
			//echo $req."<br>";
			$ex=mysql_query($req);
			return $req;
		}
		
		public function getProduitByRef($ref,$sais){
			$req="select * from produit where refproduit='$ref' and codeSaison='$sais'" ;
			// echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
		
		public function getDescriptionPro($ref,$sais){
			$req="select description from produit where refproduit='$ref' and codeSaison='$sais'" ;
			//echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
		
		public function getDProduitByRef($ref,$sais,$taille){
			$req="select * from detailproduit where refproduit='$ref' and codetaille='$taille' and codeSaison='$sais'" ;
			//echo $req."<br> ";
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
		
		public function getListProduitByRef($ref,$sais){
			// $req="select * from produit where refproduit='$ref' and codeSaison='$sais' and stockDisponible>0" ;
			$req="select * from produit where refproduit='$ref' and codeSaison='$sais'" ;
			//echo $req;
			$exec=mysql_query($req);
			return $exec;
		}
		
		public function getListProduit(){
			$req="select * from produit ";
			$exec=mysql_query($req);
			return $exec;
		}
			
		public function getProduitByRefColSais($ref,$sais,$col,$partarif){

			$codetarif=$_SESSION['codetarif'];
			$count="select count(*) from detailproduit  where refproduit='$ref' and codeSaison='$sais' and codecolori='$col'  and codetarif='$_SESSION[codetarif]' ";
			// echo $count;
			$nbr=mysql_fetch_array(mysql_query($count));
			
			if($nbr[0]==0){
				$codetarif=$partarif;
			}
			
			$req="select sum(stockdisponible)-sum(stockencmd) as test, prix from detailproduit where refproduit='$ref' and codeSaison='$sais' and codecolori='$col' and codetarif='$codetarif' GROUP BY prix" ;
			//echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
			
		public function getProduitById($id){
			$req="select * from produit where idproduit='$id'" ;
			// echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}

		
		public function getProduitByEan($ean13){
			$req="select * from detailproduit where codeean13='$ean13'" ;
			//echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
		
		public function getProduitByRefCol($ref,$col,$saison,$tarif){
			$req="select * from produit where refproduit='$ref' and codeColori='$col' and codesaison='$saison' and codetarif='$tarif'" ;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
		
		public function getLibelle($table,$code,$codelangue){
			$req="select libelle from $table where id='$code' and codelangue='$codelangue'" ;
			// echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_row($exec);
			return $red[0];
		}
		
		public function getID($table,$code,$codelangue){
			$req="select id from $table where id='$code' and codelangue='$codelangue'" ;
			// echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_row($exec);
			return $red[0];
		}
		
		public function getCGT($ref){
			$req="select codeGammeTaille from produit where refproduit='$ref'" ;
			$exec=mysql_query($req);
			$red=mysql_fetch_row($exec);
			return $red[0];
		}
		
		
		public function getTailleToCGT($taille,$cgt){
			$req="select * from taille where codegamme='$cgt' and libelle='$taille'" ;
			$exec=mysql_query($req);
			$red=mysql_fetch_row($exec);
			return $red;
		}
		
		public function getDebFin($ref){
			$req="select codetailledebut, codetaillefin from produit where refproduit='$ref'" ;
			$exec=mysql_query($req);
			$red=mysql_fetch_row($exec);
			return $red;
		}
		
		public function getLibSaison($code,$codelangue){
			$an=substr($code,0,2);
			$sais=substr($code,2,1);
			$req="select  libelle from libelle where code='$sais' and codelangue='$codelangue'";
			$red=mysql_fetch_array(mysql_query($req));
			$ch=$red[0]." 20".$an;
			return $ch;
		}
		
		public function getCode($table,$lib,$codelangue){
			$req="select id from $table where libelle like '%$lib%' and codelangue='$codelangue'" ;
			$exec=mysql_query($req);
			$red=mysql_fetch_row($exec);
			return $red[0];
		}

		public function getTailles($codeGamme,$deb,$fin){
			$req="select libelle,codetaille from taille where codegamme='$codeGamme' and codetaille between $deb and $fin order by codetaille asc " ;
			// echo $req;
			$exec=mysql_query($req);
			return $exec;
		}
	
		public function getNbrTaille($codeGamme,$deb,$fin){
			$req="select count(*),sum(length(libelle)) from taille where codegamme='$codeGamme' and codetaille between $deb and $fin " ;
			// echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			$tab=array($red[0],$red[1]);
			return $tab;
		}
	
		public function getColoris($ref,$sais){
			$req="select codeColori,libcolori from produit where refproduit='$ref' and codeSaison='$sais' order by libcolori asc LIMIT 1" ;
			//echo $req;
			$exec=mysql_query($req);
			return $exec;
		}
		
		public function getColorisName($codeColori){
			$req="select codeColori from produit where codeColori='$codeColori' LIMIT 1" ;
			//echo $req;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red[0];
		}
		
		public function getNbrColori($ref,$sais){
			$req="select count(*) from produit where refproduit='$ref' and codeSaison='$sais' " ;
			//echo $req;
			$exec=mysql_fetch_array(mysql_query($req));
			return $exec[0];
		}

		public function getStock($ref,$col,$sais,$taille,$codegamme){
			include_once("admin.php5");
			$ad=new Admin();
			$par=$ad->getParametre();
			$codetarif=$_SESSION['codetarif'];
			$count="select count(*) from detailproduit  where refproduit='$ref' and codeSaison='$sais' and codecolori='$col' and codetaille='$taille' and codetarif='$_SESSION[codetarif]' ";
			$nbr=mysql_fetch_array(mysql_query($count));
			
			if($nbr[0]==0){
				$codetarif=$par['codetarif'];
			}

			$req="select stockdisponible-stockencmd as stockdisponible ,prix from detailproduit where refproduit='$ref' and codeSaison='$sais' and codecolori='$col' and codetaille='$taille' and codetarif='$codetarif'  " ;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
		
		public function getStockByLib($ref,$col,$sais,$taille,$codegamme,$histqte){
			$req="select stockdisponible-stockencmd+$histqte from detailproduit where refproduit='$ref' and codeSaison='$sais' and codecolori='$col' and codetaille=(select codetaille from taille where libelle='$taille' and codeGamme='$codegamme' ) " ;
			//echo $req."<br>";
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red[0];
		}

		public function getQte($ref,$col,$sais,$taille,$gamme){
			$req="select quantite from lignecommande where refproduit='$ref' and codesaison='$sais' and codecolori='$col' and taille=(select libelle from taille where codetaille='$taille' and codegamme='$gamme') and numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0) " ;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red[0];
		}
		
		public function getSelectProd(){
			$req="select * from produit where selection=1 ";
			//echo $req."<br>";
			$red=mysql_query($req);			
			return $red;
		}
		
		public function getProdBytaille($ref,$col,$sais,$taille,$codegamme){
			include_once("admin.php5");
			$ad=new Admin();
			$par=$ad->getParametre();

			$codetarif=$_SESSION['codetarif'];
			$count="select count(*) from detailproduit  where refproduit='$ref' and codeSaison='$sais' and codecolori='$col' and codetaille='$taille'   and codetarif='$_SESSION[codetarif]' ";
			$nbr=mysql_fetch_array(mysql_query($count));
			
			if($nbr[0]==0){
			$codetarif=$par['codetarif'];
			}

			$req="select detailproduit.*,stockdisponible-stockencmd  as stockdisponible   from detailproduit where refproduit='$ref' and codeSaison='$sais' and codecolori='$col' and codetaille='$taille'  and codetarif='$codetarif' ";
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red;
		}
		
		public function getProdByRefSaisTaille($ref,$sais,$taille,$partarif){
			$codetarif=$_SESSION['codetarif'];
			$count="select count(*) from detailproduit  where refproduit='$ref' and codeSaison='$sais' and codetaille='$taille'  and codetarif='$_SESSION[codetarif]' ";
			$nbr=mysql_fetch_array(mysql_query($count));
			
			if($nbr[0]==0){
			$codetarif=$partarif;
			}
			
			$req="SELECT stockdisponible-stockencmd-IFNULL(t.quantite,0)as stock,prix,trim(d.codecolori) FROM detailproduit d
			left join (select refproduit,codecolori,codesaison,taille,quantite from tempsaisie where login<>'$_SESSION[login]') t on d.refproduit=t.refproduit and d.codecolori=t.codecolori and d.codetaille=t.taille where d.refproduit ='$ref' AND d.codetaille ='$taille' and d.codesaison='$sais' and d.codeTarif='$codetarif' ORDER BY libcolori ASC";
			$ex=mysql_query($req);
			$ch="";
			while ($red=mysql_fetch_array($ex)){ 
				$ch.=$red[0]."/".$red[1]."/".$red[2]."|";
			}
			
			return $ch;
		}
	
		public function getNbrProdByCol($sais,$ref,$col,$codetarif){
			$req="select count(*) from produit where refproduit='$ref' and codeSaison='$sais' and codeColori='$col' and codetarif='$codetarif'";
			//echo $req."<br>";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getNbrProdByColTaille($sais,$ref,$col,$taille,$codetarif){
			$req="select count(*) from detailproduit where refproduit='$ref' and codeSaison='$sais' and codeColori='$col' and codetaille='$taille' and codetarif='$codetarif'";
			echo $req."<br>";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getNbrProdByColTaille2($ean13,$codetarif){
			$req="select count(*) from detailproduit where codeean13='$ean13' and codetarif='$codetarif'";
			// echo $req."<br>";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getNbrTailleByCol($gamme,$codetaille){
			$req="select count(*) from taille where codegamme='$gamme' and codetaille='$codetaille'  ";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getNbrCol($code){
			$req="select count(*) from colori where id='$code'  ";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getNbrSaison($code){
			$req="select count(*) from saison where id='$code'  ";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getNbr($table,$code){
			$req="select count(*) from $table where libelle='$code'  ";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function addChamp($table,$libelle){
			$req="insert into $table values('','$libelle','FRA')  ";
			$red=mysql_query($req);
			return $req;
		}
		
		public function getNbrByTarif($codetarif){
			$req="select count(*) from detailproduit where codetarif='$codetarif'  ";
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getFirstProduit($ref,$sais,$col,$gamme){
			$req="select d.prix,t.libelle from  produit p,detailproduit d,taille t  WHERE p.codeSaison=d.codesaison and p.refproduit=d.refproduit and p.codeColori=d.codecolori and d.codeTaille=t.codeTaille and d.codetarif='$_SESSION[codetarif]' and d.codecolori='$col' and d.codesaison='$sais' and d.refproduit='$ref' and t.codeGamme='$gamme' limit 1";
			//echo $req;
			$red=mysql_fetch_array(mysql_query($req));
			$tab=array($red[0],$red[1]);
			return $tab;
		}
		
		public function savePosProduit($refproduit,$libelle,$codeColori,$codeGammeTaille,$codetailledebut,$codetaillefin,$codeSaison,$codeMarque,$codeTheme,$codeFamille,$codeSousFamille,$codeModele,$codeLigne,$nonComandable,$description,$codeEan,$poids,$champsstat,$libtaille,$libcol,$prix,$codetarif,$stockdisponible,$stockencmd,$stockaterme,$libmarque,$separateur){
			$vider=mysql_query("truncate table posproduit"); 
			$req="insert into posproduit values('$refproduit','$libelle','$codeColori','$codeGammeTaille','$codetailledebut','$codetaillefin','$codeSaison','$codeMarque','$codeTheme','$codeFamille','$codeSousFamille','$codeModele','$codeLigne','$nonComandable','$description','$codeEan','$poids','$champsstat','$libtaille','$libcol','$prix','$codetarif','$stockdisponible','$stockencmd','$stockaterme','$libmarque','$separateur')";
			//echo $req;
			$ex=mysql_query($req);
		}
		
		public function getPosProduit(){
			$req="select * from posproduit";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getPosSuivi(){
			$req="select * from possuivi";
			//echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getPosDescription()	{
			$req="select * from posdescription";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function savePosDetProduit($codesaison,$refproduit,$codecolori,$codetaille,$prix,$codetarif,$codeean,$stockdispo,$stockencmd,$stockterme,$separateur){
			$vider=mysql_query("truncate table posdetailproduit"); 
			$req="insert into posdetailproduit values('$codesaison','$refproduit','$codecolori','$codetaille','$prix','$codetarif','$codeean','$stockdispo','$stockencmd','$stockterme','$separateur')" ;
			//echo $req;
			$exec=mysql_query($req);	
		}
		
		public function getPosDetProduit(){
			$req="select * from posdetailproduit";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function viderPtoduit(){
			$vider="truncate table produit";
			$vider2="truncate table detailproduit";
			$ex=mysql_query($vider);
			$ex=mysql_query($vider2);
		}
		
		public function getStockProduit($refproduit,$coloris,$codesaison,$codetarif){
			$codetarif=$_SESSION['codetarif'];
			$req="select sum(stockdisponible)-sum(stockencmd) from detailproduit where refproduit='$refproduit' and codecolori='$coloris' and codeTarif='$codetarif' and codesaison='$codesaison'";
			$ex=mysql_query($req); 
			//echo $req;
			$red=mysql_fetch_array($ex);	
			return $red[0];
		}
		
		public function getStockProduitPositiv($refproduit,$coloris,$codesaison,$codetarif){
			$codetarif=$_SESSION['codetarif'];
			$req="select sum(stockdisponible)-sum(stockencmd) from detailproduit where refproduit='$refproduit' and codecolori='$coloris' and codeTarif='$codetarif' and codesaison='$codesaison' AND stockdisponible - stockencmd >=0";
			$ex=mysql_query($req); 
			//echo $req."<br>";
			$red=mysql_fetch_array($ex);	
			return $red[0];
		}
	
		public function getNbrDescription($ref,$sais,$codelangue){
			$req="select count(*) from description where codeproduit='$ref' and codeSaison='$sais' and codelangue='$codelangue' " ;
			$exec=mysql_fetch_array(mysql_query($req));
			return $exec[0];
		}
		
		public function addDescription($codesaison,$codeproduit,$codelangue,$libellelangue,$description){
			$req="insert into description values('$codesaison','$codeproduit','$codelangue','$libellelangue','$description')";
			//echo $req."<br>";
			$exec=mysql_query($req);
		}
		
		public function updateDescription($codesaison,$codeproduit,$codelangue,$libellelangue,$description){
			$req="update description set description='$description',libellelangue='$libellelangue' where codesaison='$codesaison' and codeproduit='$codeproduit' and codelangue='$codelangue'";
			$exec=mysql_query($req);
		}
		
		public function savePosDescription($codesaison,$codeproduit,$codelangue,$libellelangue,$description,$separateur){
			$vider=mysql_query("truncate table posdescription"); 
			$req="insert into posdescription values('$codesaison','$codeproduit','$codelangue','$libellelangue','$description','$separateur')";
			//echo $req;
			$ex=mysql_query($req);
		}
		
		public function getDescription($refprod,$saison,$codelangue){
			$codetarif=$_SESSION['codetarif'];
			$req="select description from description where codeproduit='$refprod' and codesaison='$saison' and codelangue='$_SESSION[codelangue]'";
			//echo $req;
			$ex=mysql_query($req); 
			$red=mysql_fetch_array($ex);	
			return $red[0];
		}
		
		public function getColorisStock($qte){
			$param=$_SESSION['parametre'];
			$stockIndisponible=$param['stockIndisponible'];
			$minStockLimite=$param['minStockLimite'];
			$maxStockLimite=$param['maxStockLimite'];
			$stockDisponible=$param['stockDisponible'];
			//echo $qte." ".$stockIndisponible." ".$minStockLimite." ".$maxStockLimite." ".$stockDisponible;die();
			
			if($qte<=$stockIndisponible) { 
				return 'red';
			}else if($qte>=$minStockLimite && $qte <=$maxStockLimite) {
				return 'orange';
			}else { 
				return 'green'; 
			}
		}
		
		public function getColorisStockQte($qte){
			$param=$_SESSION['parametre'];
			$stockIndisponible=$param['stockIndisponible'];
			$minStockLimite=$param['minStockLimite'];
			$maxStockLimite=$param['maxStockLimite'];
			$stockDisponible=$param['stockDisponible'];
			 //echo $qte." ".$stockIndisponible." ".$minStockLimite." ".$maxStockLimite." ".$stockDisponible;die();
			
			if($qte<=$stockIndisponible) { 
				return 'red';
			}else if($qte>=$minStockLimite && $qte <=$maxStockLimite) {
				return 'orange';
			}else { 
				return 'green'; 
			}
		}
		
		public function getStockTaille($ref,$sais,$coloris,$taille,$codetarif){
			$req="SELECT stockdisponible-stockencmd-IFNULL(t.quantite,0)as stock FROM detailproduit d
			left join (select refproduit,codecolori,codesaison,taille,quantite from tempsaisie where login<>'$_SESSION[login]') t on d.refproduit=t.refproduit and d.codecolori=t.codecolori and d.codetaille=t.taille where d.refproduit ='$ref' AND d.codetaille ='$taille' and d.codesaison='$sais' and d.codeColori='$coloris' and d.codeTarif='$codetarif' ORDER BY libcolori ASC";
			//echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
		
		public function qteAutoriseCmd($refprod,$login,$numcde,$qte){
			$req="UPDATE `autorisation_prodcli` SET qte_commande=$qte, numcde='$numcde' WHERE codePF='$refprod' AND login='$login'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
		}
		
		public function qteAutorise($refprod,$login){
			$req="SELECT qte_autorise-qte_commande AS qteRestant FROM `autorisation_prodcli` WHERE login='$login'";
			//echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}	

		public function verifStock($refprod,$taille,$qte){
			$req="SELECT (stockdisponible-stockencmd)-$qte as stocks FROM detailproduit WHERE refproduit= '$refprod' AND codetaille= '$taille' AND stockdisponible > 0";
			//echo $req . "<br>";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}	
		
		public function getPosStock()	{
			$req="select * from posstock";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		
		public function updateStock($codeEan,$stockDisponible,$stockencmd,$stockaterme){
			echo "update detailproduit set stockdisponible='$stockDisponible',stockencmd='$stockencmd',stockaterme='$stockaterme' where codeean13='$codeEan'";
			$req="update detailproduit set stockdisponible='$stockDisponible',stockencmd='$stockencmd',stockaterme='$stockaterme' where codeean13='$codeEan'";
			$exec=mysql_query($req);			
		}
		
		public function majFullStock(){
			$req="UPDATE produit p SET stockdisponible = (SELECT SUM(stockdisponible) FROM detailproduit d WHERE d.refproduit = p.refproduit AND d.codeColori = p.codeColori)";
			$exec=mysql_query($req);
			$req2="UPDATE produit p SET nonCommandable = 1 WHERE stockdisponible > 0";
			$exec2=mysql_query($req2);
			
		}
		
		public function stockNegatif(){
			$req="UPDATE produit p SET stockdisponible = 0 ";
			$req2="UPDATE detailproduit p SET stockdisponible = 0";
			//$exec=mysql_query($req);
			//$exec2=mysql_query($req2);
			
		}
		
		
		
	}
?>