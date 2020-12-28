<?php

	session_start();
	set_time_limit(300);
	ini_set("maximum_execution_time", 300);
	include("./config.php5");
	include("./classes/client.php5");
	include("./classes/admin.php5");
	include_once("./classes/commander.php5");
	
	$fields=array("codeClient","nomSociete","codepostal","raisonSociale","telephone","fax","email","login","password","codeLangue","codeTarif","ville","pays","nadresselivraison","adr1adrliv2","raisfactcomp","codePostalFact","villeFact","paysFact","nadresseFacturation","adr1adr2fact","tauxRemise","codeSurveillance","categorie","geo","souscategorie","centrale","souscentrale","point","civilite","sommeil","codePaysL","codePaysF");	
	//valeur
		$cli=new Client();
		$admin=new Admin();
		$par=$admin->getParametre();
		$cmd=new Commander();
		$nbrLignes=0;
		$nbrAjout=0;
		$pos=$cli->getPosClient();
		
		foreach($fields as $field){
			${$field.'pos'}=$pos[$field];	
		}
		$nomFichier = "";
	//parcourir le dossier import_files
		if ($handle = opendir('./import_files')) {
			while (false !== ($trFile = readdir($handle))) {
				$deb=substr($trFile,0,3);

				if(strtoupper($deb)=="CLI"){
					$nomFichier=$trFile;
				}
			}
			closedir($handle);
		} 
		
	if( $nomFichier<>""){
		if (!$fp = fopen("./import_files/$nomFichier","r")) {
			echo "Echec de l'ouverture du fichier";
			exit;
		} else {
			$line=0;
			$i = 1;
			while(!feof($fp)) {
				$nbrLignes+=1;
				$Ligne = fgets($fp,400);
				$chaine=utf8_encode($Ligne);
				//$separateur=$pos["separateur"];
				$separateur=';';
				$sp=explode($separateur, $chaine);
				$tr=false;
				foreach($fields as $field){
					if(${$field."pos"}<>"" && !empty($sp[${$field."pos"}-1])){ 
						${$field}=addslashes($sp[${$field."pos"}-1]);
						$tr=true;
					}
					else{
						${$field}="";
					}
				}
				
				//separer les 2 adresse de facturation
				if($raisonSociale<>""){
					$raiscomp=split("\|",$raisonSociale);
					$raisliv=$raiscomp[0];
					$compliv=$raiscomp[1];
				} else {
					$raisliv="";
					$compliv="";	
				}
			
				if($adr1adrliv2<>""){
					$adrliv=split("\|",$adr1adrliv2);
					if($adrliv[0] != ""){
						$adrliv1= $nomSociete."%".$adrliv[0]."|".$adrliv[1]."%".$codepostal."%".$ville;
					}
					// if($adrliv[1] != ""){	
						// $adrliv2= $raisonSociale."%".."%".$codepostal."%".$ville;
					// }
				} else	{
					$adrliv1="";
					$adrliv2="";	
				}

				//nombre de fois
				$nbradresse1=$cli->getNbrAdresse($login,$adrliv1,"livraison");
				$nbradresse2=$cli->getNbrAdresse($login,$adrliv2,"livraison");
				$nbclient=$cli->getNbrClient($login);
				//separer les 2 adresse de facturation
				//separer entre la raison sociale et le complement
				if($raisfactcomp==""){$raisfactcomp=" ";}
				$raiscomp=split("\|",$raisfactcomp);
				
				if($adr1adr2fact<>""){
					$adrfact=split("\|",$adr1adr2fact);
					if($adrfact[0] != ""){
						// $adrfact1= $adrfact[0]."%".$codePostalFact."%".$villeFact;
						$adrfact1= $raisfactcomp."%".$adrfact[0]."%".$codePostalFact."%".$villeFact;
					}
					// if($adrfact[1] != ""){
					// $adrfact2= $adrfact[1]."%".$codePostalFact."%".$villeFact;
					// $adrfact2= $raisfactcomp."%".$adrfact[1]."%".$codePostalFact."%".$villeFact;
					// }
				} else {
					$adrfact1="";
					$adrfact2="";	
				}

				//tester si le client existe deja
				if($par['login_ski'] == 1) {
					$login = $cli->convert_login($login);
					$personne = $cli->getProfil($login);
					$emails = $cli->getEmail($login);
					if ($email != $emails){
						if ($personne['login'] == $login){
							$login .= $i;
							$i++;
						} else {
							$i = 1;
						}
					}
				}
				
					// Temporaire, pour tester les ajours en BDD, il faut décider quel login choisir
				$login = $email;
				
				$login_exist = $cli->getLoginExist($login);	
				if($login_exist == false){
					if(strtoupper($codeLangue)!="FRA" && strtoupper($codeLangue)!="ANG"){
						$codeLangue="FRA";
					}
					$password = md5("TexasNet.".$password);
				
					//ajouter le client
					if($email <> ""){
						$nbrAjout+=1;
						echo "Client : ";
						$ins=$cli->addClient($codeClient,$nomSociete,$codepostal,$raisliv,$compliv,$telephone,$fax,$email,$login,$password,strtoupper($codeLangue),$codeTarif,$ville,$pays,$nadresselivraison,$raiscomp[0],$raiscomp[1],$codePostalFact,$villeFact,$paysFact,$nadresseFacturation,$tauxRemise,$codeSurveillance,$categorie,$geo,$souscategorie,$centrale,$souscentrale,$civilite,$sommeil,$codePaysL,$codePaysF);
					
					
						///////////////////////////
						// TOUTES LES ADRESSES ont été commenté car elles ne correspondent plus à la base de donnée.
						// Elles ont été gardé temporairement, le temps de réécrire le fichier au propre quand Cihat sera revenu ou qu'une organisation précise des fichiers aura été décidé. 
						//
					
					
						//if($adrliv1<>""){$newArd=$cli->addAdresse($login,$adrliv1,1,"livraison");}
						//if($adrliv2<>""){$newArd=$cli->addAdresse($login,$adrliv2,2,"livraison");}
					
						//ajouer les 2 adresse de facturation
						//if($adrfact1<>""){$newArd=$cli->addAdresse($login,$adrfact1,1,"facturation");}
						//if($adrfact2<>""){$newArd=$cli->addAdresse($login,$adrfact2,2,"facturation");}
			
					}
				}	else	{			
					/*
					//adresse de livraison				
				
					$nbradresse1=$cli->getNbrAdresse($login,$adrliv1,"livraison");
					$nbradresse2=$cli->getNbrAdresse($login,$adrliv2,"livraison");
					if($nbradresse1==0){
						$num=$cli->getNumAdresse($login,"livraison");
						if($adrliv1<>""){
							//$newArd=$cli->addAdresse($login,$adrliv1,$num,"livraison");
						}
					}
					if($nbradresse2==0){
						$num=$cli->getNumAdresse($login,"livraison");
						if($adrliv2<>""){
							//$newArd=$cli->addAdresse($login,$adrliv2,$num,"livraison");
						}
					}

					//si le client exist tester les 2 adresse de facturation
				
				
					$nbradrfact1=$cli->getNbrAdresse($login,$adrfact1,"facturation");
					$nbradrfact2=$cli->getNbrAdresse($login,$adrfact2,"facturation");
					
					//adresse1 de facturation				
					if($nbradrfact1==0){
						$num=$cli->getNumAdresse($login,"facturation");
						if($adrfact1<>""){
							//$newArd=$cli->addAdresse($login,$adrfact1,$num,"facturation");
						}
					}
					if($nbradrfact2==0){
						$num=$cli->getNumAdresse($login,"facturation");
						//if($adrfact2<>""){$newArd=$cli->addAdresse($login,$adrfact2,$num,"facturation");}
					}
					$updInfo=$cli->upInfos($codeClient,$pays,$codePaysL,$codePaysF);
					*/
				}
				$line+=1;
			}
			fclose($fp); // On ferme le fichier
			echo '</br>Total lignes : '.$nbrLignes; 
			echo '</br>Total ajouts : '.$nbrAjout; 
			$cmd->addLog('IMPORT MANUEL','CLIENT',0,$nbrLignes);
		}
	}

	// if( $nomFichier<>"")
	// {
	// unlink("../import_files/$nomFichier");
	// }
?>
<script type="text/javascript">
// history.go(-1);
</script>