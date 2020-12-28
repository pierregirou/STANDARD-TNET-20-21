<?php

class Admin
{
public function authentifier($login,$pwd)
{
	$req="select count(*),prenom,codeLangue from admin where login='$login' and password='$pwd'";
	$ex=mysql_query($req);
	$count=mysql_fetch_row($ex);
	$tab=array($count[0],$count[1],$count[2]);
	return $tab;
} 
public function setParametre($nomsociete,$adresse1,$adresse2,$telephone,$fax,$email,$siteweb,$langue,$devise,$codetarif,$visGalerie,$ordreAffichage,$dateminliv,$textcommandefra,$textcommandeang,$impmarque,$saisoncmd,$cumulsaison,$enstock,$ratachMarque,$stockCouleur,$stockIndisponible,$minStockLimite,$maxStockLimite,$StockDiponible,$controleStock,$stockApresSaisie,$photoLargeur,$photoHauteur,$gelerieAccueil,$gestionGroupe,$libelleService,$envoieMail,$AffichageQte,$timer,$stat,$points,$port,$montant_port,$port_gratuit,$so_col,$foid,$fraisexp,$clesha1,$version_col,$login_ski,$catMarque,$cenLigne,$souscenTheme,$retourcmd,$bloqmodif,$cde_marque,$promotion,$langue_ang,$selectMoment)
{
	$req="UPDATE `parametrage` SET `nomsociete`='$nomsociete',`adresse1`='$adresse1',`adresse2`='$adresse2',`telephone`='$telephone',`fax`='$fax',`email`='$email',`siteweb`='$siteweb',`codeLangue`='$langue',`devise`='$devise',`codetarif`='$codetarif',`visGalerie`='$visGalerie',`ordreAffichage`='$ordreAffichage',`dateMinLivraison`='$dateminliv',`texteCommandeFra`='$textcommandefra',`texteCommandeAng`='$textcommandeang',`impMarque`='$impmarque',`saisonCommande`='$saisoncmd',`cumulerSaisonPermanente`='$cumulsaison',`enStock`='$enstock',`rattachMarque`='$ratachMarque',`stockCouleur`='$stockCouleur',`stockIndisponible`='$stockIndisponible',`minStockLimite`='$minStockLimite',`maxStockLimite`='$maxStockLimite',`stockDisponible`='$StockDiponible',`controleStock`='$controleStock',`stockApresSaisie`='$stockApresSaisie',`photoLargeur`='$photoLargeur',`photoHauteur`='$photoHauteur',`galerieAccueil`='$gelerieAccueil',`gestionGroupe`='$gestionGroupe',`libelleService`='$libelleService',`envoieMail`='$envoieMail',`AffichageQte`='$AffichageQte',`timerCommande`='$timer',`stat_valid_panier`='$stat', `points`='$points',`port`='$port',`montant_port`='$montant_port',`port_gratuit`='$port_gratuit',`so_col`='$so_col',`foid`='$foid',`frais_dexpedition`='$fraisexp',`cle_sha1`='$clesha1',`version_col`='$version_col', `login_ski`='$login_ski', `catMarque`='$catMarque', `cenLigne`='$cenLigne', `souscenTheme`='$souscenTheme',`retour_act`='$retourcmd',`bloq_modifadr`='$bloqmodif', `cde_marque`='$cde_marque', `promotion`='$promotion', `langue_ang`='$langue_ang',`selectMoment`='$selectMoment' ";
	// echo $req;
	$ex=mysql_query($req);
} 

public function setExportTime($hour)
{
	$req="UPDATE `parametrage` SET `date_exppts` = '$hour';";
	// echo $req;
	$ex=mysql_query($req);
} 

public function setTemplate($bg,$baniere,$logo,$bg2,$contenu,$infoc,$labelbox,$totalc,$footer,$rechercher,$menu,$texte,$texte_ang,$image1,$image2,$image3,$video)
{
	$req="UPDATE template SET logo='$logo', banniere='$baniere', bg='$bg', bg2='$bg2', wellcentre='$contenu', labelbox='$labelbox', infocentre='$infoc', totalcentre='$totalc', footer='$footer', recherche='$rechercher', navbar='$menu', texte='$texte', texte_ang='$texte_ang', image1='$image1', image2='$image2', image3='$image3', video='$video'";
	// echo $req;
	$ex=mysql_query($req);
} 

public function setMenu($niv_ligne,$niv_modele,$niv_famille,$niv_sousf,$niv_marque,$niv_theme,$niv_saison,$actif_ligne,$actif_modele,$actif_famille,$actif_sousf,$actif_marque,$actif_theme,$actif_saison)
{
	$req="UPDATE menu SET actif='$actif_ligne', ordre_menu='$niv_ligne' WHERE nom='ligne';";
	$req1="UPDATE menu SET actif='$actif_modele', ordre_menu='$niv_modele' WHERE nom='modele';";
	$req2="UPDATE menu SET actif='$actif_famille', ordre_menu='$niv_famille' WHERE nom='famille';";
	$req3="UPDATE menu SET actif='$actif_sousf', ordre_menu='$niv_sousf' WHERE nom='sous-Famille';";
	$req4="UPDATE menu SET actif='$actif_marque', ordre_menu='$niv_marque' WHERE nom='marque';";
	$req5="UPDATE menu SET actif='$actif_theme', ordre_menu='$niv_theme' WHERE nom='theme';";
	$req6="UPDATE menu SET actif='$actif_saison', ordre_menu='$niv_saison' WHERE nom='saison';";
	// echo $req;
	$exec=mysql_query($req);
	$exec1=mysql_query($req1);
	$exec2=mysql_query($req2);
	$exec3=mysql_query($req3);
	$exec4=mysql_query($req4);
	$exec5=mysql_query($req5);
	$exec5=mysql_query($req6);
} 


public function getParametre()
{
	$req="select * from parametrage";
		mysql_query("SET NAMES UTF8"); 
	$ex=mysql_query($req);
	$red=mysql_fetch_array($ex);
	return $red;
}


public function getParametreMenu($nom)
{
	$req="select * from menu WHERE nom='$nom'";
	$ex=mysql_query($req);
	$red=mysql_fetch_array($ex);
	$actif = $red['actif'];
	// echo $red['ordre_menu'];
	return $actif;
}

public function getParametreMenu2($nom)
{
	$req="select * from menu WHERE nom='$nom'";
	$ex=mysql_query($req);
	$red=mysql_fetch_array($ex);
	echo $red['ordre_menu'];

}

public function getTemplate()
{
	$req="select * from template";
	mysql_query("SET NAMES UTF8"); 
	$ex=mysql_query($req);
	$red=mysql_fetch_array($ex);
	return $red;
}

public function getDevise()
{
	$req="select d.symbole from parametrage p,devise d where p.devise=d.codedevise";
		mysql_query("SET NAMES UTF8"); 
	$ex=mysql_query($req);
	$red=mysql_fetch_array($ex);
	return $red[0];
}
public function viderBd()
{
	$tables=array("adresse","client","ligne","commande","detailproduit","famille","lignecommande","marque","memoiresaisie","modele","produit","saison","sousfamille","taille","theme","tempsaisie","posClient","posproduit","posdetailproduit","posdescription");
	foreach($tables as $table)
{
$req="truncate  $table";
	$ex=mysql_query($req);	
}
return "";
}
public function getLiens()
{
	$req="select * from telechargement";
	mysql_query("SET NAMES UTF8"); 
	$ex=mysql_query($req);
	return $ex;
}
public function addLien($intitule,$type,$lien,$variable)
{
	mysql_query("SET NAMES UTF8"); 
	$req="insert into telechargement values(NULL,'$intitule','$type','$lien','$variable')";
	$ex=mysql_query($req);	
} 
public function getLog()
{
	mysql_query("SET NAMES UTF8"); 
	$req="select * from log";
	$ex=mysql_query($req);	
	return mysql_fetch_array($ex);
} 
}


?>