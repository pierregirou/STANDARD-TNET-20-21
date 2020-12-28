<?php

include('connect.php');

$login_array=[];
$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
    $login_array[]= $donnees["login"];
}
$reponse->closeCursor();

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    if(isset($_POST['login'])){
        $login=$_POST['login'];
        if(in_array($login,$login_array)){
            $reponse=$bdd->prepare("SELECT codetarif,souscentrale,civilite FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$login
            ));
            $retour=$reponse->fetch();
            $codetarif=$retour["codetarif"];
			      $sousCentralClient=ucfirst($retour["souscentrale"]);
			      $civilite=$retour["civilite"];


            $tabMenu=explode("&&",$_POST["url"]);
            $reponse->closeCursor();
            /* Récupère l'url passé lors du menu selection et récupère chacun des champs séparés par '&&' */
            $tabMenu=explode("&&",$_POST["url"]);
            $numeroMenuGalerie = count($tabMenu)+1;
            $numeroMenuGaleriePromo = count($tabMenu);
            $menu_actif=[];
            $reponse=$bdd->query("SELECT nom FROM menu WHERE actif=1 ORDER BY ordre_menu");
            while($donnees=$reponse->fetch()){
                $menu_actif[]=$donnees["nom"];
            }
            $reponse->closeCursor();

            $reponse=$bdd->query("SELECT modeSaisie,souscenTheme FROM modules");
            $retour=$reponse->fetch();
            $modeSaisie=$retour["modeSaisie"];
            $souscenTheme=$retour["souscenTheme"];
            $reponse->closeCursor();

			/*if($civilite == "M"){
				$codeMod = "Homme";
			} else {
				$codeMod = "Femme";
			}*/
		if(substr($_POST["url"], 0,1) !== '$'){
            /* Les différents menus possibles */
            $ligne="";
            $modele="";
            $marque="";
            $sousFamille="";
            $theme="";
            $famille="";
            for($i=0;$i<count($tabMenu);$i++){
                if($menu_actif[$i]=="ligne"){
                    $ligne=$tabMenu[$i];
                }
                if($menu_actif[$i]=="modele"){
                    $modele=$tabMenu[$i];
                }
                if($menu_actif[$i]=="marque"){
                    $marque=$tabMenu[$i];
                }
                if($menu_actif[$i]=="sous-Famille"){
                    $sousFamille=$tabMenu[$i];
                }
                if($menu_actif[$i]=="theme"){
                    $theme=$tabMenu[$i];
                }
                if($menu_actif[$i]=="famille"){
                    $famille=$tabMenu[$i];
                }
            }

            if($_POST["typeSelect"]=="produits"){
                $req="SELECT idproduit, p.refproduit, p.libelle, codeColori, codeGammeTaille, codetailledebut, codetaillefin, codeSaison,substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) as codeMarque, substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme,substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille,substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3,locate('/',substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3))-1) as codeSousFamille,substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3,locate('/',substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3))-1) as codeModele,substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne, nonCommandable, poids, codetarif, prix, libcolori, libMarque, commentaire1, commentaire2, commentaire3, commentaire4, commentaire5, selection, promo, tarif_promo, positionGalerie, tarif_pvc, stockdisponible, libelle2 , pg.position1, pg.position2, pg.position3, pg.position4, pg.position5, pg.position6, CONCAT(substring(ma.libelle,locate('".$_POST['langue']."',ma.libelle)+3,locate('/',substring(ma.libelle,locate('".$_POST['langue']."',ma.libelle)+3))-1),' ', substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1)) as texteLibre FROM produit p LEFT JOIN positiongalerie pg ON p.refproduit=pg.refproduit LEFT JOIN marque mq ON p.codeMarque = mq.idMarque LEFT JOIN theme t ON p.codeTheme = t.idTheme LEFT JOIN famille f ON p.codeFamille = f.idFamille LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille LEFT JOIN modele md ON p.codeModele = md.idModele LEFT JOIN ligne l ON p.codeLigne = l.idLigne LEFT JOIN matiere ma ON p.codeMatiere = ma.idMatiere WHERE codetarif='$codetarif' AND p.stockdisponible > 0 ";
            }
            if($_POST["typeSelect"]=="promo"){
                $req="SELECT idproduit, p.refproduit, p.libelle, codeColori, codeGammeTaille, codetailledebut, codetaillefin, codeSaison,substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) as codeMarque, substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme,substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille,substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3,locate('/',substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3))-1) as codeSousFamille,substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3,locate('/',substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3))-1) as codeModele,substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne, nonCommandable, poids, codetarif, prix, libcolori, libMarque, commentaire1, commentaire2, commentaire3, commentaire4, commentaire5, selection, promo, tarif_promo, positionGalerie, tarif_pvc, stockdisponible, libelle2 , pg.position1, pg.position2, pg.position3, pg.position4, pg.position5, pg.position6, CONCAT(substring(ma.libelle,locate('".$_POST['langue']."',ma.libelle)+3,locate('/',substring(ma.libelle,locate('".$_POST['langue']."',ma.libelle)+3))-1),' ', substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1)) as texteLibre FROM produit p LEFT JOIN positiongaleriepromo pg ON p.refproduit=pg.refproduit LEFT JOIN marque mq ON p.codeMarque = mq.idMarque LEFT JOIN theme t ON p.codeTheme = t.idTheme LEFT JOIN famille f ON p.codeFamille = f.idFamille LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille LEFT JOIN modele md ON p.codeModele = md.idModele LEFT JOIN ligne l ON p.codeLigne = l.idLigne LEFT JOIN matiere ma ON p.codeMatiere = ma.idMatiere WHERE p.codetarif='$codetarif' AND p.promo=1 ";
            }
			
			$langueSelect 	= 	$_POST['langue'];
            $ligne			=	$langueSelect."".trim($ligne);
            $modele			=	$langueSelect."".trim($modele);
            $marque			=	$langueSelect."".trim($marque);
            $sousFamille	=	$langueSelect."".trim($sousFamille);
            $theme			=	$langueSelect."".trim($theme);
            $famille		=	$langueSelect."".trim($famille);
            $matiere		=	$langueSelect."".trim($matiere);
			

            if($ligne!=""){
                $req.=" AND l.libelle LIKE '%$ligne%'";
            }
            if($modele!=""){
                $req.=" AND md.libelle LIKE '%$modele%'";
            }
            if($marque!=""){
                $req.=" AND mq.libelle LIKE '%$marque%'";
            }
            if($sousFamille!=""){
                $req.=" AND sf.libelle LIKE '%$sousFamille%'";
            }
            if($theme!=""){
                $req.=" AND t.libelle LIKE '%$theme%'";
            }
            if($famille!=""){
                $req.=" AND f.libelle LIKE '%$famille%'";
            }
            if($matiere!=""){
                $req.=" AND ma.libelle LIKE '%$matiere%'";
            }


            if($modeSaisie == 2) {
                if($souscenTheme == 1) {
                    $req .= " AND p.codeMarque='$sousCentralClient' ";
                }
                if($_POST["typeSelect"]=="produits"){
                    $req.=" GROUP BY p.refproduit,p.prix,p.tarif_promo ORDER BY pg.position".$numeroMenuGalerie.",p.promo DESC";
                }
                if($_POST["typeSelect"]=="promo"){
                    $req.=" GROUP BY p.refproduit,p.prix,p.tarif_promo ORDER BY pg.position".$numeroMenuGaleriePromo.",p.promo DESC";
                }
            }
		} else {

			list($ap,$ligne,$famille,$theme,$coloris,$texteLibre,$tailleD)=explode("-",$_POST["url"]);
			$langueSelect 	= 	$_POST['langue'];
			

			$req = "SELECT idproduit, p.refproduit, p.libelle, codeColori, codeGammeTaille, codetailledebut, codetaillefin, codeSaison,substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) as codeMarque, substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme,substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille,substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3,locate('/',substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3))-1) as codeSousFamille,substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3,locate('/',substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3))-1) as codeModele,substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne, nonCommandable, poids, codetarif, prix, libcolori, libMarque, commentaire1, commentaire2, commentaire3, commentaire4, commentaire5, selection, promo, tarif_promo, positionGalerie, tarif_pvc, stockdisponible, libelle2 , pg.position1, pg.position2, pg.position3, pg.position4, pg.position5, pg.position6, CONCAT(substring(ma.libelle,locate('".$_POST['langue']."',ma.libelle)+3,locate('/',substring(ma.libelle,locate('".$_POST['langue']."',ma.libelle)+3))-1),' ', substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1)) as texteLibre  FROM produit p LEFT JOIN positiongalerie pg ON p.refproduit=pg.refproduit LEFT JOIN marque mq ON p.codeMarque = mq.idMarque LEFT JOIN theme t ON p.codeTheme = t.idTheme LEFT JOIN famille f ON p.codeFamille = f.idFamille LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille LEFT JOIN modele md ON p.codeModele = md.idModele LEFT JOIN ligne l ON p.codeLigne = l.idLigne LEFT JOIN matiere ma ON p.codeMatiere = ma.idMatiere  WHERE p.codetarif='$codetarif' AND p.stockdisponible > 0 AND (";
			
			if($ligne !== ""){
				
				$ligne			=	$langueSelect."".trim($ligne);
				$p = 1;
				$req .= "l.libelle LIKE '%$ligne%'";
			}

			if($famille !== ""){
				$famille		=	$langueSelect."".trim($famille);
				$f = 1;
				if ($p === 1) {
					$req .=  " AND f.libelle LIKE '%$famille%' ";
				}else {
					$req .=  "f.libelle LIKE '%$famille%' ";
				}
			}
			if($theme !== ""){
				$theme			=	$langueSelect."".trim($theme);
				$t = 1;
				if($p ===1 || $f ===1 ) {
					$req .=  " AND t.libelle LIKE '%$theme%'";
				} else {
					$req .=  " t.libelle LIKE '%$theme%'";
				}
			}
			if($coloris !== ""){
				$c = 1;
				if($p ===1 || $f ===1|| $t ===1) {
					$req .=  " AND p.libcolori LIKE '%$coloris%'";
				} else {
					$req .=  " p.libcolori LIKE '%$coloris%'";
				}
			}
			if($texteLibre !== ""){
				$tl =1;
				if($p ===1 || $f ===1|| $t ===1 || $c===1) {
					$req .=  " AND p.texteLibre = '$texteLibre'";
				} else {
					$req .=  "p.texteLibre = '$texteLibre'";
				}
			}
			/*if($tailleD !== ""){
				if($p ===1 || $f ===1|| $t ===1 || $c===1 | $tl ===1) {
					$req .=  " AND p.cod= '$texteLibre'";
				} else {
					$req .=  "p.texteLibre= '$texteLibre'";
				}
			}*/
			$req .= ") GROUP BY p.refproduit,p.prix,p.tarif_promo ORDER BY pg.position1";

		}
			// echo $req;
            $reponse=$bdd->query($req);
            $i=1;
            while($donnees=$reponse->fetch()){

                $refProduit=str_replace('/','_',$donnees['refproduit']);
                $refProduit=str_replace(' ','_',$refProduit);

                $fichier= "../Photos/".$donnees['codeSaison'].$refProduit.".jpg";
                $fichier1= "../Photos/".$donnees['codeSaison'].$refProduit."-1.jpg";;
                $fichier2= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-1.jpg";
                $fichier3= "../Photos/".$donnees['codeSaison'].$refProduit."--0.jpg";
                $fichier4= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-0.jpg";
                $fichier5= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori']."-".$donnees['libelle']."-1.jpg";
                $fichier6= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori'].".jpg";

                if (file_exists($fichier6)) {
                    $fichier=$fichier6;
                } elseif(file_exists($fichier5)) {
                    $fichier=$fichier5;
                } elseif(file_exists($fichier4)) {
                    $fichier=$fichier4;
                } elseif(file_exists($fichier3)) {
                    $fichier=$fichier3;
                } elseif(file_exists($fichier2)) {
                    $fichier=$fichier2;
                } elseif(file_exists($fichier1)) {
                    $fichier=$fichier1;
                } elseif(file_exists($fichier)) {
                    $fichier=$fichier;
                } else {
                    if (!file_exists($fichier)) {
                        $fichier="../../Images/no_image.png";
                    }
                }

                $arr2[$i]["idproduit"]=$donnees["idproduit"];
                $arr2[$i]["refproduit"]=$donnees["refproduit"];
                $arr2[$i]["libelle"]=$donnees["libelle"];
                $arr2[$i]["codeColori"]=$donnees["codeColori"];
                $arr2[$i]["nbColori"]=$nbColori;
                $arr2[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"];
                $arr2[$i]["codetailledebut"]=$donnees["codetailledebut"];
                $arr2[$i]["codetaillefin"]=$donnees["codetaillefin"];
                $arr2[$i]["codeSaison"]=$donnees["codeSaison"];
                $arr2[$i]["codeMarque"]=$donnees["codeMarque"];
                $arr2[$i]["codeTheme"]=$donnees["codeTheme"];
                $arr2[$i]["codeFamille"]=$donnees["codeFamille"];
                $arr2[$i]["codeSousFamille"]=$donnees["codeSousFamille"];
                $arr2[$i]["codeModele"]=$donnees["codeModele"];
                $arr2[$i]["refproduit"]=$donnees["refproduit"];
                $arr2[$i]["codeLigne"]=$donnees["codeLigne"];
                $arr2[$i]["nonCommendable"]=$donnees["nonCommendable"];
                $arr2[$i]["refproduit"]=$donnees["refproduit"];
                $arr2[$i]["poids"]=$donnees["poids"];
                $arr2[$i]["codetarif"]=$donnees["codetarif"];
                $arr2[$i]["prix"]=$donnees["prix"];
                $arr2[$i]["libcolori"]=$donnees["libcolori"];
                $arr2[$i]["libMarque"]=$donnees["libMarque"];
                $arr2[$i]["commentaire1"]=$donnees["commentaire1"];
                $arr2[$i]["commentaire2"]=$donnees["commentaire2"];
                $arr2[$i]["commentaire3"]=$donnees["commentaire3"];
                $arr2[$i]["commentaire4"]=$donnees["commentaire4"];
                $arr2[$i]["commentaire5"]=$donnees["commentaire5"];
                $arr2[$i]["selection"]=$donnees["selection"];
                $arr2[$i]["promo"]=$donnees["promo"];
                $arr2[$i]["tarif_promo"]=$donnees["tarif_promo"];
                $arr2[$i]["tarif_pvc"]=$donnees["tarif_pvc"];
                $arr2[$i]["positionGalerie"]=(string)$i;
                $arr2[$i]["codeTarifClient"]=$codetarif;
                $arr2[$i]["ordreAffichage"]=$ordreAffichage;
                $arr2[$i]["testOrdreAffichage"]=$testOrdreAffichage;
                $arr2[$i]["tarif_pvc"]=$donnees["tarif_pvc"];
                $arr2[$i]["libelle2"]=$donnees["libelle2"];
                $arr2[$i]["texteLibre"]=$donnees["texteLibre"];
                $arr2[$i]["imageArt"]=$fichier;
				
				$reponse2=$bdd->prepare("SELECT libcolori,codeColori,tarif_pvc,tarif_promo FROM produit WHERE codetarif=:codetarif AND refproduit=:refproduit AND nonCommandable > 0 AND stockdisponible > 0 AND tarif_promo=:tarifPromo");
				$reponse2->execute(array(
					"refproduit"=>$donnees["refproduit"],
					"tarifPromo"=>$donnees["tarif_promo"],
					"codetarif"=>$codetarif
				));
				$j=0;

                while($donnees2=$reponse2->fetch()){
                    $reponse4=$bdd->prepare("SELECT libelleColoris FROM colorisTraduction WHERE codeLangue ='ANG' AND codeColoris = :codeColoris");
                    $reponse4->execute(array(
                        "codeColoris"=> $donnees2["codeColori"]

                    ));
                    $donnees4=$reponse4->fetch();
                    $arr2[$i]["arrayColori"][$j]["libcoloriANG"]=$donnees4["libelleColoris"];
					$arr2[$i]["arrayColori"][$j]["libcolori"]=$donnees2["libcolori"];
					$arr2[$i]["arrayColori"][$j]["tarif_pvc"]=$donnees2["tarif_pvc"];
                    $arr2[$i]["arrayColori"][$j]["tarif_promo"]=$donnees2["tarif_promo"];
                    
                    $picto = "../../Photos/Coloris/-".$donnees2["codeColori"]."-".strtoupper($donnees2["libcolori"])."-1.jpg";
                    
                    $arr2[$i]["arrayColori"][$j]["codeColori"]=$donnees2["codeColori"];
                    $arr2[$i]["arrayColori"][$j]["imageMiniature"]=$picto;
					$arr2[$i]["arrayColori"][$j]["codeColoris"]=$picto;

					$j++;
				}
				$reponse2->closeCursor();
                $i++;
            }

            $reponseMenuSelection[0]=true;
            $reponseMenuSelection[1]=$tabMenu;
            $reponseMenuSelection[2]=$menu_actif;
            $reponseMenuSelection[3]=count($tabMenu);
            $reponseMenuSelection[4]["ligne"]=$ligne;
            $reponseMenuSelection[4]["modele"]=$modele;
            $reponseMenuSelection[4]["marque"]=$marque;
            $reponseMenuSelection[4]["sousFamille"]=$sousFamille;
            $reponseMenuSelection[4]["theme"]=$theme;
            $reponseMenuSelection[4]["famille"]=$famille;
            $reponseMenuSelection[5]=$req;
            $reponseMenuSelection[6]=$arr2;
            echo json_encode($reponseMenuSelection);
        }else{
            ?>
            {
                "success":false,
                "message":"erreur"
            }
            <?php
        }
    }else{
        ?>
        {
            "success":false,
            "message":"erreur"
        }
        <?php
    }
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>
