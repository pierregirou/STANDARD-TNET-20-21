<?php
	class Commander 
	{
		public function ecrireLog($txt, $nom){
			//if (!file_exists("./log/$nom.txt")) file_put_contents("./log/$nom.txt", "");
			//file_put_contents("./log/$nom.txt",date("[j/m/y H:i:s]")." - $txt \n".file_get_contents("./log/$nom.txt"));
		}

		static function strtoupperFr($string) {
			$string = strtoupper($string);
			$string = str_replace(array('é', 'è', 'ê', 'ë', 'à', 'â', 'î', 'ï', 'ô', 'ù', 'û'), array('E', 'E', 'E', 'E', 'E', 'A', 'I', 'I', 'O', 'U', 'U'), $string);
			$string = str_replace(array('É', 'È', 'Ê', 'Ë', 'À', 'Â', 'Î', 'Ï', 'Ô', 'Ù', 'Û'), array('E', 'E', 'E', 'E', 'E', 'A', 'I', 'I', 'O', 'U', 'U'), $string	);
			return $string;
		}
		
		public function addCmd($login){
			$prenom= $this->strtoupperFr($prenom);
			$nom =$this->strtoupperFr($nom);
			
			//tester s'il y'a un panier ouvert
			 $reqcmd="select count(*) from commande where valid=0 and login='$login' ";
			 // echo $reqcmd;
			 
			 $excmd=mysql_fetch_row(mysql_query($reqcmd));
			 $nbr=$excmd[0];
			//echo $nbr;
			
			 //si existe panier ouvert
			 if($nbr==0){
				$date= date("Y-m-d H:i:s");
				 
				 //inserer commande
					$req="insert into commande values(NULL,'$date','','c',0,0,0,0,0,0,0,'En attente','','$adrliv','$adrfact','$com1','$com2','$com3','$com4','$com5','123',0,'sa55','transporteur','$login','$nom','$prenom','$service',NULL,NULL,'$saison','','','','')";
					// echo $req;
					
					$ex=mysql_query($req);
					$this->ecrireLog("Commande ajouté - $login - $ncmd", "cmd");
				return $req;
			}
		}
		
		

		public function updateCmd2($login,$datliv,$adrliv,$adrfact,$com1,$com2,$com3,$com4,$com5,$nom,$prenom,$service,$saison,$numcmd,$numpr,$tel,$mail){
			$prenom= $this->strtoupperFr($prenom);
			$nom =$this->strtoupperFr($nom);
			$date= date("y-m-d");		
			$this->ecrireLog("$login,$datliv,$adrliv,$adrfact,$com1,$com2,$com3,$com4,$com5,$nom,$prenom,$service,$saison,$numcmd,$numpr,$tel,$mail", "composition");
			//update
			$req="UPDATE commande SET datecommande='$date', datelivraison='$datliv',adresselivraison='$adrliv', adressefacturation='$adrfact',commentaire1='$com1',commentaire2='$com2',commentaire3 = '$com3', commentaire4 = '$com4', commentaire5 = '$com5',nom = '$nom', prenom = '$prenom', service= '$service', saison ='$saison',numPointRetrait='$numpr',tel = '$tel', mail = '$mail' WHERE numCommande='$numcmd'";
			$this->ecrireLog("$req", "requete commande");
			$ex=mysql_query($req);
			return $req;
		}
		
		public function updatestatut($noSuivi,$numcmd){
			//update
			$req="UPDATE commande SET numsuivi='|$noSuivi' WHERE numCommande='$numcmd'";
			echo $req;
			$ex=mysql_query($req);
			return $req;
		}
		
		public function exists($numcmd){
			$req="SELECT * from commande WHERE numCommande='$numcmd'";
			$ex1 = mysql_query($req);
			$ex=mysql_fetch_row($ex1);
			if ($ex > 1 ){
				return 1;
			} else {
				return 0;
			}
		}

		public function controle(){
			$req="delete from lignecommande where numCommande=0" ;
			$exec=mysql_query($req);
			return $req;
		}
		
		public function getNbrColori($ref,$sais,$numcmd){
			$req="select count(distinct codecolori) from lignecommande where  refProduit='$ref' and codesaison='$sais' and numCommande='$numcmd' " ;
			$exec=mysql_fetch_array(mysql_query($req));
			return $exec[0];
		}
		
		public function addProd($login,$refprod,$colori,$sais,$taille,$qte,$commentaire,$prix,$codegamme,$marque,$theme,$famille,$sousfamille,$modele,$ligne,$libcolori,$libelle){
			$ancqte=0;
			//recuperer le numéro de la commande
			$var=mysql_query("select numcommande from commande where login='$login' and valid=0 order by numcommande");
			$var2="select numcommande from commande where login='$login' and valid=0 order by numcommande";
			$numrow = mysql_fetch_row($var);
			$numcmd = $numrow[0]; 
			//echo $numcmd;
			//echo $var2."<br>";
			$red=mysql_fetch_array(mysql_query("select count(*) from lignecommande where codecolori='$colori' and taille='$taille' and codesaison='$sais' and refproduit='$refprod' and numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0)"));

			if($red[0]==0){
				$ins="insert into lignecommande(numCommande,refProduit,codecolori,codesaison,taille,quantite,commentaire,prix,login,codeMarque,codeTheme,codeFamille,codeSousFamille,codeModele,libcolori,libelle) values ('$numcmd','$refprod','$colori','$sais','$taille','$qte','$commentaire','$prix','$_SESSION[login]','$marque','$theme','$famille','$sousfamille','$modele','$libcolori','$libelle') ";
				echo $ins."<br>";
			}else{
				//recupere l'ancien quantité
				$var=mysql_query("select quantite from lignecommande where codecolori='$colori' and taille='$taille' and refProduit='$refprod' and codesaison='$sais' and numcommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0)");
				$numrow = mysql_fetch_row($var);
				$ancqte = $numrow[0];
				//modifier la ligne de commande
				$ins="update lignecommande set quantite=$qte where codecolori='$colori' and taille='$taille' and numCommande= (select numCommande from commande where login='$_SESSION[login]' and valid=0) and codesaison='$sais' and refproduit='$refprod'";
			}

			if(mysql_query($ins)){
				$updetail="update detailproduit set stockdisponible=stockdisponible-$qte+$ancqte where refproduit='$refprod' and codecolori='$colori' and codesaison='$sais' and codetaille=(select codetaille from taille where libelle='$taille' and codegamme='$codegamme')";
				echo $updetail."<br>";
				$exup=mysql_query($updetail);
				$updproduit="update produit set stockdisponible=stockdisponible-$qte+$ancqte,qtecmd=qtecmd+$qte-$ancqte where refproduit='$refprod' and codecolori='$colori' and codesaison='$sais'";
				echo $updproduit;
				$exup=mysql_query($updproduit);
			}
		}
		
		public function addProds($login,$refprod,$colori,$sais,$taille,$qte,$commentaire,$prix,$codegamme,$marque,$theme,$famille,$sousfamille,$modele,$ligne,$libcolori,$libelle){
				$ins="insert into lignecommande(numCommande,refProduit,codecolori,codesaison,taille,quantite,commentaire,prix,login,codeMarque,codeTheme,codeFamille,codeSousFamille,codeModele,libcolori,libelle) values ('$numcmd','$refprod','$colori','$sais','$taille','$qte','$commentaire','$prix','$_SESSION[login]','$marque','$theme','$famille','$sousfamille','$modele','$libcolori','$libelle') ";
				// echo $ins."<br>";

		}
		
		public function updatecmd($login,$refprod,$colori,$saison,$taille,$qte,$codegamme){
			$cmd=new Commander();
			$redcmd=$cmd->getCmd($login);
			$numcmd=$redcmd['numCommande'];
			
			//recuperer lancien quantité de la commande
			$var=mysql_query("select quantite from lignecommande where codecolori='$colori' and taille='$taille' and refProduit='$refprod' and codesaison='$saison' and numcommande='$numcmd'");
			$numrow = mysql_fetch_row($var);
			$ancqte = $numrow[0];

			//modifier la quantit" de la ligne de commande
			$up="update lignecommande set quantite=$qte where codecolori='$colori' and taille='$taille' and refProduit='$refprod' and codesaison='$saison' and numcommande='$numcmd'";
			
			if(mysql_query($up)){
				$updetail="update detailproduit set stockdisponible=stockdisponible+$ancqte-$qte where refproduit='$refprod' and codecolori='$colori' and codesaison='$saison' and codetaille=(select codetaille from taille where libelle='$taille' and codegamme='$codegamme')";
				//echo $updetail;
				//echo $upprod;
				$exup=mysql_query($updetail);
			}
		}
		
		public function setQte($id,$qte){
			//recuperer la ligne de commande
			$var=mysql_query("select * from lignecommande where id=$id ");
			$numrow = mysql_fetch_array($var);
			$ancqte = $numrow['quantite'];
			$refprod = $numrow['refProduit'];
			$saison = $numrow['saison'];
			$taille= $numrow['taille'];
			$colori= $numrow['colori'];

			//modifier la quantit" de la ligne de commande
			$up="update lignecommande set quantite=$qte where codecolori='$colori' and taille='$taille' and refProduit='$refprod' and codesaison='$saison'";
			if(mysql_query($up)){
				$updetail="update detailproduit set stockdisponible=stockdisponible+$ancqte-$qte where refproduit='$refprod' and codecolori='$colori' and codesaison='$saison' and codetaille=(select codetaille from taille where libelle='$taille')";
				//echo $upprod;
				$exup=mysql_query($updetail);
			}
		}
		
		public function getOpen($login){
			$req="select count(*) from commande where login='$login' and valid='0' order by numcommande";
			$var=mysql_query($req);
			$numrow = mysql_fetch_row($var);
			$nbr = $numrow[0];
			return $nbr;
		}
		
		public function nbrCommande($login){
			$req="select count(*) from commande where valid=1 and etat='exporté'";
			$var=mysql_query($req);
			$numrow = mysql_fetch_row($var);
			$nbr = $numrow[0];
			return $nbr;
		}

		public function validCmd($login){
			$cmd=$this->getNumCmdInvalid();		
			$date= date("y-m-d H:i:s");
			$req="update commande set valid=1,dateValidation='$date' where login='$login' and numCommande='$cmd'  order by numCommande";
			$var=mysql_query($req);
			return $req;
		}
		
		public function deleteCmd($login){
			// $req2 = "SELECT prix from commande where login='$login' and valid=0 ";
			// $var2=mysql_query($req2);
			// $prix = $var2['prix'];
			// $req3 = "SELECT point_temporaire-$prix from points where login='$login'";
			// $var3=mysql_query($req3);
			$req="delete from commande where login='$login' and valid=0 ";
			$var=mysql_query($req);
			return $req;
		}
		
		public function rayeCmd($login){
			$req="delete from commande where login='$login' and valid=0 ";
			$var=mysql_query($req);
			return $req;
		}
		
		public function getNbrLine($login){
			$var=mysql_query("select count(id) from lignecommande l,commande c where l.numCommande=c.numCommande and c.login='$login' and valid='0' order by c.numCommande");
			$numrow = mysql_fetch_row($var);
			$nbr = $numrow[0];
			return $nbr;	
		}
	
		public function updatMtcmd($mt,$np)	{
			$req="update commande set montant=montant+$mt,nbrpiece=nbrpiece+$np where login ='$_SESSION[login]' and valid=0 ";
			$up=mysql_query($req);
		}
		
		public function updatMtQte(){
			$redcmd=mysql_fetch_array(mysql_query("select numCommande from commande where login='$_SESSION[login]' and valid=0"));
			$numcmd=$redcmd[0];
			$req="update commande set montant=(select sum(quantite*prix) from lignecommande where numCommande=($numcmd)),nbrpiece=(select sum(quantite) from lignecommande where numCommande=($numcmd)) where login='$_SESSION[login]' and valid=0 ";
			//echo $req;
			$up=mysql_query($req);
		}

		public function getCmd($login){
			$req="select * from commande where  login ='$login' and valid=0";
			$ex=mysql_query($req);
			$numrow = mysql_fetch_array($ex);
			return $numrow;
		}
		
		public function getCmdInfo($num){
			$req="select * from commande where numCommande ='$num'";
			// echo $req."<br>";
			$ex=mysql_query($req);
			$numrow = mysql_fetch_array($ex);
			return $numrow;
		}
		
		public function getCmdBynum($num){
			$req="select * from commande where  numCommande ='$num' ";
			$ex=mysql_query($req);
			$numrow = mysql_fetch_array($ex);
			return $numrow;
		}
		
		public function deleLinecmd($mt,$np){
			$req="update commande set montant=montant+$mt,nbrpiece=nbrpiece+$np where login ='$_SESSION[login]' and valid=0 ";
			//echo $req;
			$up=mysql_query($req);
		}
		
		public function getPrixByColTaille($col,$taille,$ref,$sais){
			//$req="SELECT prix FROM detailproduit WHERE refProduit='$ref' AND codesaison='$sais' AND codeColori = '$col' AND codetaille = '$taille';";
			$req = "SELECT dp.prix FROM detailproduit dp, taille t WHERE dp.refProduit='$ref' AND dp.codesaison='$sais' AND dp.codeColori = '$col' AND t.libelle = '$taille' AND t.codegamme = dp.codeGammeTaille AND dp.codetaille = t.codetaille";
			//echo $req.'</br>'; //die();
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getQteByColTaille($col,$taille,$ref,$sais){
			$req="select quantite,prix from lignecommande where codecolori='$col' and taille='$taille' and refProduit='$ref' and codesaison='$sais' and numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0)";
			// echo $req.";\n";
			$ex=mysql_query($req);
			$red=mysql_fetch_row($ex);
			return $red;
		}
		
		public function addTempSaisie($ref,$col,$saison,$taille,$qte){
			$nbr=mysql_fetch_array(mysql_query("select count(*) from tempsaisie where refproduit='$ref' and codecolori='$col' and codesaison='$saison' and taille='$taille' and login='$_SESSION[login]'"));
			$date= date("y-m-d");
			$date.=" ".date("H:i:s");
			if($nbr[0]==0 && $qte>0){
				$req="insert into tempsaisie values('$ref','$col','$saison','$taille',$qte,'$_SESSION[login]','$date')";
			}else{
				if($qte==0){
					$req="delete from tempsaisie where refproduit='$ref' and codecolori='$col' and codesaison='$saison' and taille='$taille' and login='$_SESSION[login]' ";
					$del=mysql_query($req);
				}else{
					$req="update tempsaisie set quantite=$qte,date='$date' where refproduit='$ref' and codecolori='$col' and codesaison='$saison' and taille='$taille' and login='$_SESSION[login]'";
				}
			}
			
			$ex=mysql_query($req);		
			return $req;
		}
		
		public function getTemQte($ref,$col,$sais,$taille){
			$date= date("y-m-d");
			$date.=" ".date("H:i:s");
			$req="select sum(quantite) from tempsaisie where refproduit='$ref' and codecolori='$col' and codesaison='$sais' and taille='$taille' and login<>'$_SESSION[login]'  " ;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red[0];
		}
		
		public function getTemQteBycode($ref,$col,$sais,$taille,$gamme){
			$date= date("y-m-d");
			$date.=" ".date("H:i:s");
			$req="select sum(quantite) from tempsaisie where refproduit='$ref' and codecolori='$col' and codesaison='$sais' and taille=(select libelle from taille where codetaille='$taille' and codegamme='$gamme') and login<>'$_SESSION[login]'  " ;
			$exec=mysql_query($req);
			$red=mysql_fetch_array($exec);
			return $red[0];
		}
		
		public function getEtat($etat){
			$req="select * from etat where libelle<>'$etat' ";
			//echo $req;
			$var=mysql_query($req);
			return $var;
		}
		
		public function setEtat($num,$etat){
			$req="update commande set etat='$etat' where numCommande='$num'";
			$var=mysql_query($req);
			return $req;
		}

		public function getProfil($login){
			$req="select * from client where login='$login'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getProduit($numcmd){
			$req="select distinct refproduit,codesaison,libcolori,libelle,codeMarque from lignecommande where numCommande='$numcmd' group by refproduit";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function getColByRefSais($numcmd,$ref,$sais){
			$req="select distinct codecolori,libcolori from lignecommande where numCommande='$numcmd' and refProduit='$ref' and codesaison='$sais'";
			//echo $req."<br>";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function getProduitByRefSais($ref,$sais){
			$req="select *  from produit where refproduit='$ref' and codeSaison='$sais'";
			//echo $req."<br>";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getTailles($gamme,$deb,$fin){
			$req="select *  from taille where codegamme='$gamme' and codetaille between '$deb' and '$fin' order by codetaille asc  ";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function getMaxLenTaille($gamme){
			$req="select max(length(libelle))  from taille where codegamme='$gamme' ";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
		
		public function getQteByLine($col,$taille,$ref,$sais,$numcmd){
			$tab= array();
			$req="select quantite,prix from lignecommande where codecolori='$col' and taille='$taille' and refProduit='$ref' and codesaison='$sais' and numCommande='$numcmd'";
			$req2="select round(max(prix),2) as max from detailproduit where refProduit='$ref' and codecolori='$col' and codeSaison='$sais' ";
			//echo $req2;
			$ex=mysql_query($req);
			$ex2=mysql_query($req2);
			$red=mysql_fetch_array($ex);
			$red2=mysql_fetch_array($ex2);
			$tab[0]=$red;
			$tab[1]=$red2;
			return $tab;
		}
		
		public function getLibelle($code,$langue){
			$req="select libelle from colori where codeLangue='$langue' and id='$code'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
	
		public function getCmds(){
			//$req="SELECT *,codeClient,a.numero,e.id as numstat FROM commande c,client cli,adresse a,etat e where c.login=cli.login and a.login=cli.login and c.adresselivraison=a.adresse and e.libelle=c.etat and valid=1 and etat<>'Exporté' and a.type='livraison'";
			$req="SELECT * FROM commande where valid=1 and etat not like 'Expo%' and etat not like 'Ray%' ";
			$ex=mysql_query($req);
			//echo $req;
			return $ex;	
		}

		public function getCmdss(){
			//$req="SELECT *,codeClient,a.numero,e.id as numstat FROM commande2 c,client cli,adresse a,etat e where c.login=cli.login and a.login=cli.login and c.adresselivraison=a.adresse and e.libelle=c.etat and valid=1 and etat<>'Exporté' and a.type='livraison'";
			$req="SELECT * FROM commande2 where valid=1 and etat not like 'Expo%' and etat not like 'Ray%' ";
			$ex=mysql_query($req);
			//echo $req;
			return $ex;	
		}
		
		public function setEtatCmds(){
			$date= date("y-m-d H:i:s");
			mysql_query("SET NAMES UTF8"); 
			$req="update commande set etat='Exporté',dateExport='$date' where valid='1'  and etat<>'Exporté'";
			//echo $req;
			return $ex;
		}
		
		public function setEtatCmd($numcmd,$action,$type,$id,$nbrLignes){
			mysql_query("SET AUTOCOMMIT=0");
			mysql_query("START TRANSACTION");
			mysql_query("BEGIN");

			$date= date("y-m-d H:i:s");
			mysql_query("SET NAMES UTF8"); 
			$req1="update commande set etat='Exporté',dateExport='$date' where valid='1'  and numCommande='$numcmd'";
			$ex1=mysql_query($req1);

			//Ajouter le log
			$date= date("y-m-d H:i:s");
			$req2="insert into log values (0,'$action','$type','$id','$nbrLignes','$date')";
			$ex2=mysql_query($req2);

			if ($ex1 and $ex2 ) {
				mysql_query("COMMIT");
			} else {
				mysql_query("ROLLBACK");
			}
			return $ex2;
		}
		
		public function getLignCmd($numcmd){
			$req="select * from lignecommande where numcommande='$numcmd'";
			// echo $req;
			$exa=mysql_query($req);
			return $exa;
		}
		
		public function getLignSaison($numcmd,$saison){
			$req="select * from lignecommande where numcommande='$numcmd' AND codesaison='".$saison."' ORDER BY codeSaison ASC ";
			//echo $req;
			$exa=mysql_query($req);
			return $exa;
		}
		
		public function getParametre(){
			$req="select * from parametrage";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getNumCmd(){
			$req="select * from commande where numCommande=(select max(numCommande) from commande where login='$_SESSION[login]' and valid=1) ";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getNumCmdInvalid(){
			$req="select numCommande from commande where login='$_SESSION[login]' and valid=0";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
	
		public function getHistLigne($col,$taille,$ref,$sais,$numcmd){
			$req="select quantite,prix from lignecommande where codecolori='$col' and taille='$taille' and refProduit='$ref' and codesaison='$sais' and numcommande='$numcmd' ";
			$ex=mysql_query($req);
			$red=mysql_fetch_row($ex);
			return $red;
		}

		public function getProdBySaisRefCol($sais,$ref,$col){
			$req="select * from produit where codesaison='$sais' and refproduit='$ref' and codecolori='$col'  ";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getEan13($sais,$ref,$col,$gamme,$taille){
			$req="select codeean13 from detailproduit where codesaison='$sais' and refproduit='$ref' and codecolori='$col' and codeGammeTaille='$gamme' and codetaille=(select codetaille from taille where codegamme='$gamme' and libelle='$taille') GROUP BY codeSaison";
			// echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
		
		public function getEan13s($sais,$ref,$gamme,$taille){
			$req="select codeean13 from detailproduit where codesaison='$sais' and refproduit='$ref' and codeGammeTaille='$gamme' and codetaille=(select codetaille from taille where codegamme='$gamme' and libelle='$taille') GROUP BY codeSaison";
			// echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
		
		public function getResultat($col,$table,$id,$word){
			if($col==""){$col="*";}
			$req="select $col from $table where $id='$word'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			if($col<>"*"){$red=$red[0];}
			return $red;
		}
	
		public function getNumAdresse($login,$libelle){
			$adresse = substr($libelle, 4,100);
			//echo $libelle."</br>";
			$req="select * from adresse where login='$login' and adresse like '%$adresse'  and type='livraison'";
			// $req="select * from adresse where login='$login' and adresse = '$libelle'  and type='livraison'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			// echo $req;
			return $red[3];
		}
		
		public function MaxNumCmd(){
			$req="select max(numCommande) from commande where  login='$_SESSION[login]' ";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}

		public function getStock(){
			$req="select d.`refproduit`,d.`libelle`,d.`codeColori`,d.`libcolori`,codegammetaille,d.codetaille,tl.libelle as libtaille,d.`stockdisponible`-(d.`stockencmd`+coalesce(t.quantite,0)) as qte from taille tl,detailproduit d  left join tempsaisie t on d.refproduit=t.refproduit and d.codecolori=t.codecolori and d.codesaison=t.codesaison and codetaille=t.taille where tl.codetaille=d.codetaille and d.codeGammeTaille=tl.codeGamme and `stockdisponible`-(d.`stockencmd`+coalesce(t.quantite,0))>0";
			$ex=mysql_query($req);
			return $ex;
		}
	
		public function addLog($action,$type,$id,$nbrLignes){
			$date= date("y-m-d H:i:s");
			$req="insert into log values (0,'$action','$type','$id','$nbrLignes','$date')";
			$ex=mysql_query($req);
		}

		public function updatSaisoncmd($saison){
			$req="update commande set codesaison = '$saison' where login ='$_SESSION[login]' and valid= 0 ";
			$up=mysql_query($req);
		}

		public function delTimer($login, $temps){
			// $req ='SELECT numCommande, datecommande from commande where login="'.$login.'" and valid="0"';
			// $result = mysql_query($req);
			// $data = mysql_fetch_assoc($result);
			// $ncmd = $data['numCommande'];
			// $datev = strtotime($data['datecommande']);
			// // echo time() - $datev .'<br>';
			// // echo $temps;
			// if (time() - $datev > $temps){
				// $up ='UPDATE points SET point_temporaire = 0 where login="'.$login.'"';
				// $results = mysql_query($up);
				// $del = 'delete from commande where login="'.$login.'" and valid=0';
				// $del_lc = 'delete from lignecommande where numCommande='.$ncmd.'';
				// $ex_del = mysql_query($del);
				// $ex_dels = mysql_query($del_lc);
				// if (time() - $datev = $temps){
					// $this->ecrireLog("Commande supprimé - $login - $ncmd", "timer");
				// }
			// return $ex_del;
			// return $ex_dels;
			// }

		}
		
		public function getRetour($login, $numCommande){
			$date= date("Y-m-d H:i:s");				 
			$req="SELECT * FROM retour WHERE login = '$login' AND numCommande='$numCommande' LIMIT 1";
			// echo $req."<br>";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function addRetour($login,$numCommande,$nbr){
			$date= date("Y-m-d H:i:s");	
			$nbr += 1; 
			$req="insert into retour values('', '$numCommande','$login', '$nbr', '$date', '...')";
			// echo $req."<br>";
			$ex=mysql_query($req);
		}
		
		public function upRetour($id,$refColis,$nbr){
			$date= date("Y-m-d H:i:s");				 
			$req="UPDATE retour SET refColis='$refColis', nbr_prod='$nbr' WHERE numCommande = '$id'";
			echo $req;
			$ex=mysql_query($req);
		}
		
		public function getLigneRetour($login, $numCommande,$idRetour){
			$date= date("Y-m-d H:i:s");				 
			$req="SELECT * FROM ligneretour WHERE login = '$login' AND numCommande='$numCommande' and idRetour = '$idRetour'";
			// echo $req."<br>";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function getNbrLigneRetour($numCommande){			 
			$req="SELECT SUM(qte) as qte, login FROM ligneretour WHERE numCommande='$numCommande'";
			// echo $req."<br>";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function addLigneRetour($idRetour,$numCommande,$qte,$refproduit,$login,$taille){
			$date= date("Y-m-d H:i:s");	
			$req="insert into ligneretour values('', '$idRetour','$numCommande', '$refproduit','$taille','$qte', '$login', '$date')";
			echo $req."<br>";
			$ex=mysql_query($req);
		}
				
		public function getMarqueCde($mrq){					
			$red=mysql_fetch_array(mysql_query("select count(*) from lignecommande where numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0)"));
			// echo "select count(*) from lignecommande where numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0)<br><br>";
			
			if($red[0] > 0){
				$req2="SELECT codeMarque FROM lignecommande where numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0)";
				// echo $req2."<br>";
				$ex=mysql_fetch_array(mysql_query($req2));
				$codemarque = $ex['codeMarque'];
			} else {
				$codemarque = $mrq;
			}			
			
			return $codemarque;
		}
		
	}
?>
