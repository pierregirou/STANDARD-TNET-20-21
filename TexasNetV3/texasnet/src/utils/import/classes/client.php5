<?php
	class Client
	{
		public function ecrireLog($txt, $nom){
			//if (!file_exists("../../log/$nom.txt")) file_put_contents("../../log/$nom.txt", "");
			//file_put_contents("../../log/$nom.txt",date("[j/m/y H:i:s]")." - $txt \n".file_get_contents("../../log/$nom.txt"));
		}

		public function authentifier($login,$pwd){
			$req="select count(*),codeClient,codeLangue,codeTarif from client where login='$login' and password='$pwd' GROUP BY codeClient,codeLangue,codeTarif ";
			// echo $req;
			$ex=mysql_query($req);
			$count=mysql_fetch_array($ex);
			$tab=array($count[0],$count[1],$count[2],$count[3]);
			return $tab;
		} 
		
		public function authentifierRep($login,$pwd){
			$req="select count(*) from RepCl where login='$login' and password='$pwd'";
			// echo $req;
			$ex=mysql_query($req);
			$count=mysql_fetch_array($ex);
			$tab=array($count[0]);
			return $tab;
		} 
		
		public function addClient($codeclient,$nomsociete,$codepostal,$raisonsociale,$compliv,$telephone,$fax,$email,$login,$password,$codelangue,$codetarif,$ville,$pays,$nadrliv,$raisonsocialefact,$compfact,$codepostalfact,$villefact,$paysfact,$nadrfact,$tauxremise,$numsurv,$categorie,$geo,$souscategorie,$centrale,$souscentrale,$civilite,$sommeil){
			$numsurv = 0;
			$tauxremise = 0;
			$req="insert into client values('','$codeclient','$nomsociete','$raisonsociale','$compliv','$telephone','$fax','$email','$login','$password','$codelangue','2017-04-01','0','$codetarif','$raisonsocialefact','$compfact','$codepostalfact','$villefact','$paysfact','$tauxremise','$numsurv','$categorie','$geo','$souscategorie','$centrale','$souscentrale','$civilite','$sommeil')";
			// echo $req;
			$this->ecrireLog("Client : $login  créer", "addCli");
			echo $req."<br>";
			$ex=mysql_query($req);
		}
	
		public function incNbrvisit(){
			$date= date("y-m-d");
			$req="update client set nombreVisit=nombreVisit+1,dateDerniereVisite='$date' where login ='$_SESSION[login]'";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function upInfos($codeClient,$pays,$codepaysL,$codepaysF){
			$req="update client set pays = '$pays', codepaysL='$codepaysL', codepaysF='$codepaysF' where login ='$codeClient'";
			echo $req;
			$ex=mysql_query($req);
			return $ex;
		}

		public function getAdresse(){
			$req="select * from adresse where login='$_SESSION[login]' order by adresse asc";
			$ex=mysql_query($req);
			return $ex;
		} 
		
		public function getAdresseCli($login){
			$req="select * from adresse where login='$login' order by adresse asc";
			// echo $req;
			$ex=mysql_query($req);
			return $ex;
		} 
		
		public function getAdresseLivFact($login){
			$req="select * from adresse where login='$login'";
			// echo $req."<br>";
			$ex=mysql_query($req);
			return $ex;
		} 
	
		public function getService(){
			$req="select * from service";
			if($_SESSION['categorie']=="SAV"){
				$req.=" where libelle='SAV'";
			}
			$ex=mysql_query($req);
			return $ex;
		} 
	
		public function getLignecmd($numcmd){
			$req="select * from lignecommande where numCommande='$numcmd'";
			$ex=mysql_query($req);
			return $ex;
		} 
		
		public function getLignecmdBytaille($ref,$col){
			$req="select sum(quantite) from lignecommande where refProduit='$ref' and codecolori='$col' and numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0) group by refProduit,colori";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		} 
		
		public function getTotalByCol($sais,$ref,$col){
			$req="select round(sum(quantite*prix),2) from lignecommande where refProduit='$ref' and codecolori='$col' and codesaison='$sais' and numCommande=(select numCommande from commande where login='$_SESSION[login]' and valid=0) ";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		} 
		
		public function getcmd(){
		$req="select * from commande where login='$_SESSION[login]' and valid=1";
		$ex=mysql_query($req);
		return $ex;
		} 
		
		public function getPanier($login){
			$req="select * from lignecommande l,commande c where l.numCommande=c.numcommande and c.login='$login' and c.valid=0 " ;
			// echo $req;
			$exec=mysql_query($req);
			return $exec;
		}
		
		public function deleteLine($id){

			//recuperer la ligne
			$red=mysql_fetch_array(mysql_query("select distinct l.*,d.codeGammeTaille from lignecommande l,detailproduit d where d.refproduit=l.refproduit and d.codesaison=l.codesaison and l.codecolori=d.codecolori and l.id='$id'"));
			$qte=$red['quantite'];
			$ref=$red['refproduit'];;
			$col=$red['codeColori'];
			$saison=$red['codeSaison'];
			$prix=$red['prix'];
			$taille=$red['taille'];
			$numcmd=$red['numCommande'];
			$codegamme=$red['codeGammeTaille'];

			//remettre la quantité supprimer au stock du produit
			$updetail="update detailproduit set stockdisponible=stockdisponible+$qte where refproduit='$ref' and codesaison='$saison' and codecolori='$col' and codetaille=(select codetaille from taille where libelle='$taille' and codegamme='$codegamme')";
			$delprod = "DELETE FROM lignecommande WHERE id='$id'"; 
			$upprod="update produit set stockdisponible=stockdisponible+$qte,qtecmd=qtecmd-$qte where refproduit='$ref' and codeSaison='$saison' and codeColori='$col'";
			$exup=mysql_query($updetail);
			$exup=mysql_query($upprod);
			$exup=mysql_query($delprod);
			$requete="update commande set montant=(select sum(quantite*prix) from lignecommande where numCommande='$numcmd'),nbrpiece=(select sum(quantite) from lignecommande where numCommande='$numcmd') where numCommande='$numcmd'  ";
			
			//supprimer le produit
			return $requete;
		}
		
		public function changpass($ancpass,$nouvpass){
			$msg="s";
			$req="select count(*) from client where login='$_SESSION[login]' and password='$ancpass' ";
			// echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_row($ex);
			if($red[0]==1){
				$upreq="update client set password='$nouvpass' where login='$_SESSION[login]'";
				// echo $upreq;
				$up=mysql_query($upreq);
				$msg="Mot de passe modifié avec succés";
			}else{ 
				$msg="Mot de passe invalide"; 
			}

			return $msg;
		}
		
		public function changProfil($nom,$prenom,$rs,$rs2,$tel,$mail,$langue,$cp,$ville){
			$upreq="update client set raisonSocialeFact='$nom',complementFacturation='$prenom',raisonSociale='$rs',complementLivraison='$rs2',telephone='$tel',email='$mail',codeLangue='$langue',codepostal='$cp',ville='$ville' where login='$_SESSION[login]'";
			$_SESSION['codelangue']=$langue;
			// echo $upreq;
			$up=mysql_query($upreq);
			$msg="Profil modifié avec succés";
			return $msg;
		}
		
		public function getProfil($login){
			$req="select * from client where login='$login'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}

		public function getInformation(){
			$req="select * from client where login='$_SESSION[login]'";
			$ex=mysql_query($req);
			// echo $req;
			$red=mysql_fetch_array($ex);
			return $red;
		}

		public function getInfoClient($idClient){
			// echo "<h1>$idClient</h1>";
			$req="select * from client WHERE id = '$idClient'";
			// echo $req;
			$ex=mysql_query($req);
			$reqs = mysql_fetch_array($ex);
			return $reqs;
		}		
		
		public function getClients(){
			$req="select * from client";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function getAdresseClient($log){
			$req="select * from adresse where login='$log'";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function supClient($login){
			$req="delete from client where login='$login'";
			$ex=mysql_query($req);
		}
		
		public function ViderTempSaisie(){
			$req="delete from tempsaisie where login='$_SESSION[login]'";
			//echo $req;
			$ex=mysql_query($req);
		}
		
		public function getNbrClient($login){
			$req="select count(*) from client where login='$login'";
			//echo $req;
			$red=mysql_fetch_array(mysql_query($req));
			return $red[0];
		}
		
		public function getNumAdresse($login,$type){
			$req="select max(numero) from adresse where login='$login' and type='$type'";
			//echo $req;
			$red=mysql_fetch_array(mysql_query($req));
			$n=$red[0]+1;
			return $n;
		}
		
		public function getNbrAdresse($login,$adresse,$type){
			$req="select count(*) from adresse where login='$login' and adresse1='$adresse' and type='$type' ";
			// echo $req."<br>";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex) or die(mysql_error());;
			return $red[0];
		}
		
		public function addAdresse($login,$adresse,$num,$type){
			// Ajout de la nouvelle valeur			
			$req1="insert into adresse values(NULL,'$login','$adresse','$num','$type')";
			echo $login. ' - ' .$req1."<br><br>";
			$ex1=mysql_query($req1);
		}
		
		public function addAdresse2($login,$adresse1,$adresse2,$codepostal,$ville,$pays,$codepays,$num,$type){
			// Ajout de la nouvelle valeur			
			$req1="insert into adresse values('','$login','$adresse1','$adresse2','$codepostal','$ville','$pays','$codepays','$num','$type')";
			echo $login. ' - ' .$req1."<br><br>";
			$ex1=mysql_query($req1);
		}
		
		public function addApprouveur($login, $password, $codeClient){	
			$req="insert into approuveur values('','$login','$password','$codeClient')";
			echo $req."<br>";
			$exec=mysql_query($req);
		}
		
		public function addRepCli($codeRepresentant, $codeClient, $codeMarque){	
			$req="insert into repclient values('$codeRepresentant','$codeClient','$codeMarque')";
			echo $req."<br>";
			$exec=mysql_query($req);
		}
		
		public function addRep($coderep,$nom, $login, $password, $codeLangue){	
			$req="insert into representant values('','$coderep','$nom','$login','$password','$codeLangue')";
			echo $req."<br>";
			$exec=mysql_query($req);
		}
		
		public function getNbrLangue($codelangue){
			$req="select count(*) from  langue where id='$codelangue'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
		
		public function getLanguedDefaut(){
			$req="select codeLangue  from  parametrage";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red[0];
		}
		
		public function SetLangue($login,$langue){
			$req="update client set codeLangue='$langue' where login='$login'";
			$ex=mysql_query($req);
			return $ex;
		}
		
		public function addMessage($email,$nom,$sujet,$message,$priorite){
			$req="insert into message values('','$email','$nom','$sujet','$message','$priorite')";
			//echo $req;
			$ex=mysql_query($req);
			return $sujet;
		}
		
		public function getNomFichier($numcmd){
			$dirname = 'commandes/pdf/';
			$dir = opendir($dirname); 
			$bl="";
			$fact="";
			while($file = readdir($dir)) {
				if($file != '.' && $file != '..' && !is_dir($dirname.$file)){
					//echo $file;
					$ch=explode("_",$file);
					$a=strpos($file,"_");
					//tester si le nom fichier contien _
					if($a>0){
						if($ch[1]==$numcmd.".pdf"){
							if($ch[0]=="BL"){
								$bl=strval($file);
							}
							elseif($ch[0]=="FAC"){
								$fact=strval($file);
							}
						}
					}
				}
			}
			
			$tab = array($bl,$fact);
			closedir($dir);
			return $tab;
		}
		
		function formatLigne($text,$larg){
		
			$ch="";
			$i = 0;
			$words=explode(" ",$text);
			foreach ($words AS $word){
				$i += strlen($word) + 1;
				if ($i < $larg){
					$ch.=$word." ";
				}else{
					if(strlen($word)>$larg){$word=substr($word,0,$larg)."<br>".substr($word,$larg,$larg);}
					$ch.="<br>".$word."<br>";
					$i = 0;
				}
			} 
			return $ch;
		}
		
		function getEmail($login){
			$req="select email from client WHERE login = '$login'";
			// echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red['email'];
		}
		
		function getCategorie($login){
			$req="select categorie from client WHERE login = '$login'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red['categorie'];
		}
		
		function getCivilite($login){
			$req="select civilite from client WHERE login = '$login'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			$civ = $red['civilite'];
			if($civ == "Mme"){ 
				$civ = "F";
			} elseif($civ == "MM."){ 
				$civ = "H";
			} elseif($civ == "M."){ 
				$civ = "H";
			}
			
			return $civ;
		}
		function getCentral($login){
			$req="select centrale from client WHERE login = '$login'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red['centrale'];
		}
		
		function getCentralComplet($login){
			$req="select centrale from client WHERE login = '$login'";
			// echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			$ctrl = $red['centrale'];
			$req2 = "SELECT fct_complet FROM correspondance WHERE fonction='$ctrl'";
			// echo $req2;
			$ex2=mysql_query($req2);
			$red2=mysql_fetch_array($ex2);
			return $red2['fct_complet'];
		}
		
		function getSouscentral($login){
			$req="select souscentrale from client WHERE login = '$login'";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red['souscentrale'];
		}	
	
				
		function getLoginExist($login){
			$req="select login from client WHERE login = '$login'";
			// echo $req;
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			$exist=mysql_num_rows($ex);
			if($exist> 0){
				return true;
			} else {
				return false;
			}
		}
		
		public function savePosClient($codeclient,$nomsociete,$codepostal,$raisonsociale,$telephone,$fax,$email,$login,$password,$codelangue,$codetarif,$ville,$pays,$nadrliv,$adr1adr2liv,$raisfactcomp,$codepostalfact,$villefact,$paysfact,$nadrfact,$adre1adr2fact,$tauxremise,$numsurv,$categorie,$separateur){
			$vider="truncate table posclient";
			$req="insert into posclient values('$codeclient','$nomsociete','$codepostal','$raisonsociale','$telephone','$fax','$email','$login','$password','$codelangue','$codetarif','$ville','$pays','$nadrliv','$adr1adr2liv','$raisfactcomp','$codepostalfact','$villefact','$paysfact','$nadrfact','$adre1adr2fact','$tauxremise','$numsurv','$categorie','$separateur')";
			//  echo $req."<br>";
			mysql_query($vider);
			$ex=mysql_query($req);
		}
		
		public function getPosClient(){
			$req="select * from posclient";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;

		}

		public function getPosPoints(){
			$req="select * from pospoint";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;

		}
		
		public function getPosMarque(){
			$req="select * from posmarque";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function getPosService(){
			$req="select * from posservice";
			$ex=mysql_query($req);
			$red=mysql_fetch_array($ex);
			return $red;
		}
		
		public function savePosMarque($codeClient,$marque,$separateur){
			$vider=mysql_query("truncate table posmarque"); 
			$req="insert into posmarque values('$codeClient','$marque','$separateur')";
			//echo $req;
			$ex=mysql_query($req);
		}
		
		public function savePosService($libelle,$separateur){
			$vider=mysql_query("truncate table posservice"); 
			$req="insert into posservice values('$libelle','$separateur')";
			$ex=mysql_query($req);
		}
	
		public function getNbrMarqueClient($codeClient,$marque){
			$req="select count(*) from marqueclient where codeclient='$codeClient' and marque='$marque'" ;
			$exec=mysql_fetch_array(mysql_query($req));
			return $exec[0];
		}
		
		public function deleteService($libelle){
			$req="delete from service where libelle='$libelle'" ;
			$exec=mysql_query($req);
		}
		
		public function addMarqueClient($codeClient,$marque){
			$req="insert into marqueclient values('$codeClient','$marque')";
			$ex=mysql_query($req);
		}
		
		public function addService($libelle){
			$req="insert into service values('','$libelle')";
			$ex=mysql_query($req);
		}

		public function convert_login($data) {
			$nbr = str_word_count($data);	
			if ($nbr > 2){
				list($prenom1,$prenom2,$nom) = explode(" ", $data);
				
				if ($prenom1 === ucfirst($prenom1)){
					$login = $prenom1[0];
				}
				
				if ($prenom2 === ucfirst(strtolower(($prenom2)))){
					$login .= $prenom2[0].$nom;
				} else { 
					$login .= $prenom2;
				}
			}else {
				list($prenom,$nom) = explode(" ", $data);
				$login = $prenom[0].$nom;
			}

			return strtolower($login);
		}	
		
		public function addPoint($login,$point_initial){
			$date = date("Y-m-d H:i:s");
			// $req="insert into points values('$login','$point_initial','$point_consomme','$point_temporaire', '$date')";
			$req="insert into points (login, point_initial,date_modif)values('$login','$point_initial','$date')";
			$this->ecrireLog("Attribution des points à $login", "points");
			echo $req."<br>";
			$ex=mysql_query($req);
		}
		
		public function upPoint($login,$point_initial){
			$date = date("Y-m-d H:i:s");
			$req="UPDATE points SET point_initial = '$point_initial', point_consomme='0', date_modif='$date' WHERE login='$login'";
			$this->ecrireLog("Attribution des points à $login", "points");
			echo $req."<br>";
			$ex=mysql_query($req);
		}
		
		public function getPoints($login){
			$req=mysql_query("select ((point_initial-point_consomme)-point_temporaire) as vos_points from points WHERE login='$login'");
			$red=mysql_fetch_assoc($req);
			return $red['vos_points'];
		}			
		
		public function getCodeSurveillance($login){
			// $req="select codeSurveillance from client WHERE login='$login";
			// echo $req;
			$req=mysql_query("select codeSurveillance from client WHERE login='$login'");
			$red=mysql_fetch_assoc($req);
			return $red['codeSurveillance'];
		}	
		
		public function getPoint($login){
			$req=mysql_query("select (point_initial-point_consomme) as points from points WHERE login='$login'");
			$red=mysql_fetch_assoc($req);
			return $red['points'];
		}	
		
		public function setPointsTemporaire($login,$montant){
			if($montant < 0){
				$montant = 0;
			}
			if($montant > 0) {
				$req=mysql_query("UPDATE `points` SET `point_temporaire`= '$montant' WHERE `login`='$login'");
			}
		}

		public function setPointsConsomme($login){
			$date = date("Y-m-d H:i:s");
			$req=mysql_query("UPDATE `points` SET `point_consomme`=(`point_temporaire`+`point_consomme`),`point_temporaire` = 0, date_modif='$date' WHERE `login`='$login'");
		}
		
		public function ListeClientRepresentant($login){
			$req=mysql_query("select * from RepCl WHERE login='$login'");
			// echo $req;
			return $req;
		}
	
		
	}
?>
