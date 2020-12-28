<?php
	require_once('./connectM.php');
	session_start();
	set_time_limit(0);
	error_reporting(E_ALL); // display errors
	ini_set("display_errors", 1);

	$import_file = [];
	$current_day = date('d_m_Y');
	$log_file = $C_LOG_DIR.$C_LOG_FILE.$current_day.'.txt';

	file_put_contents($log_file, "[".date('h:m:s')."] "."----- Debut de l'import des produits -----\n\n", FILE_APPEND);
	file_put_contents($log_file, "[".date('h:m:s')."] "."Récupération des positions\n", FILE_APPEND);
	$positions = $db->query("SELECT * FROM posproduit")->fetch();
	$pos_ref_produit = $positions["refproduit"];
	$pos_libelle = $positions["libelle"];
	$pos_code_colori = $positions["codeColori"];
	$pos_code_gamme_taille = $positions["codeGammeTaille"];
	$pos_code_taille_debut = $positions["codetailledebut"];
	$pos_code_taille_fin = $positions["codetaillefin"];
	$pos_code_saison = $positions["codeSaison"];
	$pos_marque = $positions["marque"];
	$pos_theme = $positions["theme"];
	$pos_famille = $positions["famille"];
	$pos_sous_famille = $positions["sousfamille"];
	$pos_modele = $positions["modele"];
	$pos_ligne = $positions["ligne"];
	$pos_non_commandable = $positions["nonComandable"];
	$pos_description = $positions["description"];
	$pos_code_ean = $positions["codeEan"];
	$pos_poids = $positions["poids"];
	$pos_champ_stat = $positions["champsstat"];
	$pos_libelle_taille = $positions["libtaille"];
	$pos_libelle_colori = $positions["libcolori"];
	$pos_prix = $positions["prix"];
	$pos_code_tarif = $positions["codetarif"];
	$pos_stock_disponible = $positions["stockdisponible"];
	$pos_stock_commande = $positions["stockencmd"];
	$pos_stock_terme = $positions["stockaterme"];
	$pos_libelle_marque = $positions["libMarque"];
	$pos_com1 = $positions["com1"];
	$pos_com2 = $positions["com2"];
	$pos_com3 = $positions["com3"];
	$pos_com4 = $positions["com4"];
	$pos_com5 = $positions["com5"];
 	$pos_libelle_2= $positions["libelle2"];
	$pos_texte_libre = $positions["texteLibre"];
	$pos_separateur = $positions["separateur"];
	$pos_champsstat = $positions["champsstat"];
	$currentProductStk = 0;
	$atleastOneDPOrder = false;

	$import_file = array_filter(file($C_IMPORT_DIR.$C_ART_FILE), function($v) {
		return trim($v) !== '';
	});
	$import_file = array_values($import_file);
	foreach ($import_file as $i => $import_line) {
		$import_line = iconv('Windows-1253','UTF-8', $import_line);
		$import_line = str_replace("’", "'", $import_line);
		$line = explode($C_SEPARATOR, $import_line);
		if(trim($import_line) !== '' && trim($line[$pos_ref_produit-1]) !== '' && trim($line[$pos_code_colori-1]) !== '') {
				// Transform data to adapt db's scheme
			$ref_produit = $line[$pos_ref_produit-1]?$line[$pos_ref_produit-1]:"";
			$libelle = $line[$pos_libelle-1]?$line[$pos_libelle-1]:"";
			$code_colori = $line[$pos_code_colori-1]?$line[$pos_code_colori-1]:"";
			$code_gamme_taille = $line[$pos_code_gamme_taille-1]?$line[$pos_code_gamme_taille-1]:"";
			$code_taille_debut = $line[$pos_code_taille_debut-1]?$line[$pos_code_taille_debut-1]:"";
			$code_taille_fin = $line[$pos_code_taille_fin-1]?$line[$pos_code_taille_fin-1]:"";
			$code_saison = $line[$pos_code_saison-1]?$line[$pos_code_saison-1]:"";
			$marque = $line[$pos_marque-1]?$line[$pos_marque-1]:"";
			$theme = $line[$pos_theme-1]?$line[$pos_theme-1]:"";
			$famille = $line[$pos_famille-1]?$line[$pos_famille-1]:"";
			$sous_famille = $line[$pos_sous_famille-1]?$line[$pos_sous_famille-1]:"";
			$modele = $line[$pos_modele-1]?$line[$pos_modele-1]:"";
			$ligne = $line[$pos_ligne-1]?$line[$pos_ligne-1]:"";
			$non_commandable = (strtolower($line[$pos_non_commandable-1])==="vrai"?true:false);
			$description = $line[$pos_description-1]?$line[$pos_description-1]:"";
			$code_ean = $line[$pos_code_ean-1]?$line[$pos_code_ean-1]:"";
			$poids = $line[$pos_poids-1]?$line[$pos_poids-1]:"";
			$champ_stat = $line[$pos_champ_stat-1]?$line[$pos_champ_stat-1]:"";
			$libelle_taille = $line[$pos_libelle_taille-1]?$line[$pos_libelle_taille-1]:"";
			$libelle_colori = $line[$pos_libelle_colori-1]?$line[$pos_libelle_colori-1]:"";
			$prix = $line[$pos_prix-1]?$line[$pos_prix-1]:"";
			$code_tarif = $line[$pos_code_tarif-1]?$line[$pos_code_tarif-1]:"";
			$stock_disponible = $line[$pos_stock_disponible-1]?$line[$pos_stock_disponible-1]:"0";
			$stock_commande = $line[$pos_stock_commande-1]?$line[$pos_stock_commande-1]:"0";
			$stock_terme = $line[$pos_stock_terme-1]?$line[$pos_stock_terme-1]:0;
			$libelle_marque = $line[$pos_libelle_marque-1]?$line[$pos_libelle_marque-1]:"";
			$com1 = $line[$pos_com1-1]?$line[$pos_com1-1]:"";
			$com2 = $line[$pos_com2-1]?$line[$pos_com2-1]:"";
			$com3 = $line[$pos_com3-1]?$line[$pos_com3-1]:"";
			$com4 = $line[$pos_com4-1]?$line[$pos_com4-1]:"";
			$com5 = $line[$pos_com5-1]?$line[$pos_com5-1]:"";
			$libelle_2 = $line[$pos_libelle_2-1]?$line[$pos_libelle_2-1]:"";
			$texte_libre = $line[$pos_texte_libre-1]?$line[$pos_texte_libre-1]:"";
			$separateur = $line[$pos_separateur-1]?$line[$pos_separateur-1]:"";
			$champsstat = $line[$pos_champsstat-1]?$line[$pos_champsstat-1]:"";
			if ($non_commandable) $atleastOneDPOrder = true;

			$previousProduct = "";
			$code_gamme = substr($code_gamme_taille, 0, 3);
			$code_taille = substr($code_gamme_taille, 3);
			$stmt = $db->prepare("SELECT * FROM taille WHERE codetaille=:codetaille AND codegamme=:codegamme");
			$stmt->execute(["codetaille" => $code_taille, "codegamme" => $code_gamme]);
			$tmp = $stmt->fetch();
			if (!$tmp) {
				$stmt = $db->prepare("INSERT INTO taille (codetaille, libelle, codegamme) VALUES (:codetaille, :libelle, :codegamme)");
				$stmt->execute(["codetaille" => $code_taille, "libelle" => $libelle_taille, "codegamme" => $code_gamme]);
			}
			$res_marque = $db->prepare("SELECT idMarque FROM marque WHERE SUBSTRING(libelle, 4, LOCATE('/', libelle)-4)=:marque");
			$res_marque->execute(['marque' => $marque]);
			$num_marque = $res_marque->fetch()[0];
			if(!$num_marque) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Marque '".$marque."' non trouvé. Ajout dans la base de donnée.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO marque (idMarque, libelle) SELECT IFNULL(MAX(idMarque)+1,1), :libelle FROM marque");
				$stmt->execute(['libelle' => 'FRA'.$marque.'/']);
				$num_marque = $db->lastInsertId();
			}
			$res_theme = $db->prepare("SELECT idTheme FROM theme WHERE SUBSTRING(libelle, 4, LOCATE('/', libelle)-4)=:theme");
			$res_theme->execute(['theme' => $theme]);
			$num_theme = $res_theme->fetch()[0];
			if(!$num_theme) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Theme '".$theme."' non trouvé. Ajout dans la base de donnée.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO theme (idTheme, libelle) SELECT IFNULL(MAX(idTheme)+1,1), :libelle FROM theme");
				$stmt->execute(['libelle' => 'FRA'.$theme.'/']);
				$num_theme = $db->lastInsertId();
			}
			$res_famille = $db->prepare("SELECT idFamille FROM famille WHERE SUBSTRING(libelle, 4, LOCATE('/', libelle)-4)=:famille");
			$res_famille->execute(['famille' => $famille]);
			$num_famille = $res_famille->fetch()[0];
			if(!$num_famille) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Famille '".$famille."' non trouvé. Ajout dans la base de donnée.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO famille (idFamille, libelle) SELECT IFNULL(MAX(idFamille)+1,1), :libelle FROM famille");
				$stmt->execute(['libelle' => 'FRA'.$famille.'/']);
				$num_famille = $db->lastInsertId();
			}
			$res_sous_famille = $db->prepare("SELECT idSousfamille FROM sousfamille WHERE SUBSTRING(libelle, 4, LOCATE('/', libelle)-4)=:sousfamille");
			$res_sous_famille->execute(['sousfamille' => $sous_famille]);
			$num_sous_famille = $res_sous_famille->fetch()[0];
			if(!$num_sous_famille) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Sous-samille '".$num_sous_famille."' non trouvé. Ajout dans la base de donnée.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO sousfamille (idSousFamille, libelle) SELECT IFNULL(MAX(idSousFamille)+1,1), :libelle FROM sousfamille");
				$stmt->execute(['libelle' => 'FRA'.$sous_famille.'/']);
				$num_sous_famille = $db->lastInsertId();
			}
			$res_modele = $db->prepare("SELECT idModele FROM modele WHERE SUBSTRING(libelle, 4, LOCATE('/', libelle)-4)=:modele");
			$res_modele->execute(['modele' => $modele]);
			$num_modele = $res_modele->fetch()[0];
			if(!$num_modele) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Modele '".$modele."' non trouvé. Ajout dans la base de donnée.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO modele (idModele, libelle) SELECT IFNULL(MAX(idModele)+1,1), :libelle FROM modele");
				$stmt->execute(['libelle' => 'FRA'.$modele.'/']);
				$num_modele = $db->lastInsertId();
			}
			$res_ligne = $db->prepare("SELECT idLigne FROM ligne WHERE SUBSTRING(libelle, 4, LOCATE('/', libelle)-4)=:ligne");
			$res_ligne->execute(['ligne' => $ligne]);
			$num_ligne = $res_ligne->fetch()[0];
			if(!$num_ligne) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Ligne '".$ligne."' non trouvé. Ajout dans la base de donnée.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO ligne (idLigne, libelle) SELECT IFNULL(MAX(idLigne)+1,1), :libelle FROM ligne");
				$stmt->execute(['libelle' => 'FRA'.$ligne.'/']);
				$num_ligne = $db->lastInsertId();
			}
			file_put_contents($log_file, "[".date('h:m:s')."] "."Recherche du produit '".$ref_produit."' au code colori '".$code_colori."'.\n", FILE_APPEND);
			$stmt = $db->prepare("SELECT * FROM produit WHERE refproduit=:refproduit AND codeColori=:codeColori");
			$stmt->execute(['refproduit' => $ref_produit, 'codeColori' => $code_colori]);
			$product = $stmt->fetch();
			if(!$product) {
				
					// Unindentified product, adding in db
				file_put_contents($log_file, "[".date('h:m:s')."] "."Pas de résultat, ajout dans la base de données.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO produit (refproduit, libelle, codeColori, codeGammeTaille, codetailledebut, codetaillefin, codeSaison, codeMarque, codeTheme,
				codeFamille, codeSousFamille, codeModele, codeLigne, nonCommandable, poids, codetarif, prix, libcolori, libMarque, commentaire1, commentaire2, commentaire3,
				commentaire4, commentaire5, selection, promo, tarif_promo, positionGalerie, tarif_pvc, stockdisponible, libelle2, texteLibre, champsstat) VALUES (:refproduit, :libelle, :codeColori,
				:codeGammeTaille, :codetailledebut, :codetaillefin, :codeSaison, :codeMarque, :codeTheme, :codeFamille, :codeSousFamille, :codeModele, :codeLigne, :nonCommandable, :poids,
				:codetarif, :prix, :libcolori, :libMarque, :commentaire1, :commentaire2, :commentaire3, :commentaire4, :commentaire5, :selection, :promo, :tarif_promo, :positionGalerie,
				:tarif_pvc, :stockdisponible, :libelle2, :texteLibre, :champsstat)");
				$insertProduct = $stmt->execute([
					'refproduit' => $ref_produit,
					'libelle' => $libelle,
					'codeColori' => $code_colori,
					'codeGammeTaille' => $code_gamme,
					'codetailledebut' => $code_taille_debut,
					'codetaillefin' => $code_taille_fin,
					'codeSaison' => $code_saison,
					'codeMarque' => $num_marque,
					'codeTheme' => $num_theme,
					'codeFamille' => $num_famille,
					'codeSousFamille' => $num_sous_famille,
					'codeModele' => $num_modele,
					'codeLigne' => $num_ligne,
					'nonCommandable' => $non_commandable,
					'poids' => $poids,
					'codetarif' => $code_tarif,
					'prix' => $prix,
					'libcolori' => $libelle_colori,
					'libMarque' => $libelle_marque,
					'commentaire1' => $com1,
					'commentaire2' => $com2,
					'commentaire3' => $com3,
					'commentaire4' => $com4,
					'commentaire5' => $com5,
					'selection' => "0",
					'promo' => "0",
					'tarif_promo' => "0",
					'positionGalerie' => "0",
					'tarif_pvc' => "0",
					'stockdisponible' => $stock_disponible,
					'libelle2' => $libelle_2,
					'texteLibre' => $texte_libre,
					'champsstat' => $champsstat
				]);
				if ($insertProduct) {
					file_put_contents($log_file, "[".date('h:m:s')."] "."Ajout effectué.\n", FILE_APPEND);
				} else {
					file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de l'ajout en base de données.\n", FILE_APPEND);
				}
			}else{
				if($code_tarif === 'PVP'){
					echo " je suis la";
				 $stmt = $db->prepare("UPDATE produit SET tarif_pvc=:prix  WHERE refproduit=:refproduit");
				 $stmt->execute(array(
				 "prix"=>$prix,
				 "refproduit"=>$ref_produit
				 ));
				}
			}

			file_put_contents($log_file, "[".date('h:m:s')."] "."Recherche du detail produit '".$ref_produit."' au code colori '".$code_colori."' au code gamme '".$code_gamme."' pour la taille ".$code_taille.".\n", FILE_APPEND);
			$stmt = $db->prepare("SELECT * FROM detailproduit WHERE refproduit=:refproduit AND codeColori=:codeColori AND codeGammeTaille=:codeGammeTaille AND codetaille=:codetaille");
			$stmt->execute(['refproduit' => $ref_produit, 'codeColori' => $code_colori, 'codeGammeTaille' => $code_gamme, 'codetaille' => $code_taille]);
			$detail_product = $stmt->fetch();
			if($detail_product) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Detail produit présent dans la base de données, on check pour update.\n", FILE_APPEND);
				$detail_product_non_commandable = $detail_product["nonCommandable"]==="1"?true:false;
				if( $code_tarif == '000' && (
					$stock_disponible !== $detail_product["stockdisponible"] ||
					$stock_commande !== $detail_product["stockencmd"] ||
					$stock_terme !== $detail_product["stockaterme"] ||
					$prix !== $detail_product["prix"] ||
					$non_commandable !== $detail_product_non_commandable ||
					$code_ean !== $detail_product["codeean13"] ||
					$code_taille_debut !== $detail_product["codetailledebut"] ||
					$code_taille_fin !== $detail_product["codetaillefin"] 
					)
				) {
					file_put_contents($log_file, "[".date('h:m:s')."] "."Données différentes pour le détail produit, modification de la base de données.\n", FILE_APPEND);
					$stmt = $db->prepare("UPDATE detailproduit SET 
						stockdisponible=:stockdisponible, 
						stockencmd=:stockencmd, 
						stockaterme=:stockaterme, 
						prix=:prix, 
						nonCommandable=:nonCommandable, 
						codeean13=:codeean13,
						codetailledebut=:codetailledebut,
						codetaillefin=:codetaillefin,
						codetarif=:codetarif
						WHERE refproduit=:refproduit 
						AND codeColori=:codeColori
						AND codeGammeTaille=:codeGammeTaille
						AND codetaille=:codetaille");
					$stmt->execute([
						'stockdisponible' => $stock_disponible,
						'stockencmd' => $stock_commande,
						'stockaterme' => $stock_terme,
						'prix' => $prix,
						'nonCommandable' => $non_commandable,
						'codeean13' => $code_ean,
						'codetailledebut' => $code_taille_debut,
						'codetaillefin' => $code_taille_fin,
						'codetarif' => $code_tarif,
						'refproduit' => $ref_produit,
						'codeColori' => $code_colori,
						'codeGammeTaille' => $code_gamme,
						'codetaille' => $code_taille
					]);
				} else {
					file_put_contents($log_file, "[".date('h:m:s')."] "."Données identiques pour le détail, pas de modification dans la base de données.\n", FILE_APPEND);
				}
			} else if( $code_tarif == '000'){
				file_put_contents($log_file, "[".date('h:m:s')."] "."Pas de résultat, ajout dans la base de données.\n", FILE_APPEND);
				$stmt = $db->prepare("INSERT INTO detailproduit (refproduit, codeColori, codeGammeTaille, codetailledebut, codetaillefin, nonCommandable, codetaille,
				prix, codetarif, codeean13, stockdisponible, stockencmd, stockaterme, tarif_promoL) VALUES (:refproduit, :codeColori, :codeGammeTaille, :codetailledebut, 
				:codetaillefin, :nonCommandable, :codetaille, :prix, :codetarif, :codeean13, :stockdisponible, :stockencmd, :stockaterme, :tarif_promoL)");
				$insertDetailProduct = $stmt->execute([
					'refproduit' => $ref_produit,
					'codeColori' => $code_colori,
					'codeGammeTaille' => $code_gamme,
					'codetailledebut' => $code_taille_debut,
					'codetaillefin' => $code_taille_fin,
					'nonCommandable' => $non_commandable,
					'codetaille' => $code_taille,
					'prix' => $prix,
					'codetarif' => $code_tarif,
					'codeean13' => $code_ean,
					'stockdisponible' => $stock_disponible,
					'stockencmd' => $stock_commande,
					'stockaterme' => $stock_terme,
					'tarif_promoL' => "0"
				]);
				if ($insertDetailProduct) {
					file_put_contents($log_file, "[".date('h:m:s')."] "."Ajout effectué.\n", FILE_APPEND);
				} else {
					file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de l'ajout en base de données.\n", FILE_APPEND);
				}
			}
				// If the next product has not the same ref than the current one, we check for updates on produit
			$next_line = $import_file[$i+1];
			$next_line = iconv('Windows-1253','UTF-8', $next_line);
			$next_line = str_replace("’", "'", $next_line);
			$n_line = explode($C_SEPARATOR, $next_line);
			$currentProductStk += intval($stock_disponible);
			if ($n_line[$pos_ref_produit-1] !== $ref_produit || $n_line[$pos_code_colori-1] !== $code_colori) {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Ref produit en cours '".$ref_produit."', prochaine ref produit '".$n_line[$pos_ref_produit-1]."', on check pour update.\n", FILE_APPEND);
				$product_non_commandable = $product["nonCommandable"]==="1"?true:false;
				if( $code_tarif == '000' && (
					strval($currentProductStk) !== $product["stockdisponible"] ||
					$libelle !== $product["libelle"] ||
					$code_gamme !== $product["codeGammeTaille"] ||
					$code_taille_debut !== $product["codetailledebut"] ||
					$code_taille_fin !== $product["codetaillefin"] ||
					$atleastOneDPOrder !== $product_non_commandable ||
					$code_saison !== $product["codeSaison"] ||
					$num_marque !== $product["codeMarque"] ||
					$num_theme !== $product["codeTheme"] ||
					$num_famille !== $product["codeFamille"] ||
					$num_sous_famille !== $product["codeSousFamille"] ||
					$num_modele !== $product["codeModele"] ||
					$num_ligne !== $product["codeLigne"] ||
					$poids !== $product["poids"] ||
					$prix !== $product["prix"] ||
					$libelle_colori !== $product["libcolori"] ||
					$libelle_marque !== $product["libMarque"] ||
					$com1 !== $product["commentaire1"] ||
					$com2 !== $product["commentaire2"] ||
					$com3 !== $product["commentaire3"] ||
					$com4 !== $product["commentaire4"] ||
					$com5 !== $product["commentaire5"] ||
					$libelle_2 !== $product["libelle2"] ||
					$texte_libre !== $product["texteLibre"]||
					$champsstat !== $product["champsstat"]
					)
				) {	
					file_put_contents($log_file, "[".date('h:m:s')."] "."Données différentes pour le produit, modification de la base de données.\n", FILE_APPEND);
					$stmt = $db->prepare("UPDATE produit SET 
						stockdisponible=:stockdisponible, 
						libelle=:libelle, 
						codeGammeTaille=:codeGammeTaille, 
						codetailledebut=:codetailledebut, 
						codetaillefin=:codetaillefin, 
						codeSaison=:codeSaison,
						codeMarque=:codeMarque,
						codeTheme=:codeTheme,
						codeFamille=:codeFamille,
						codeSousFamille=:codeSousFamille,
						codeModele=:codeModele,
						codeLigne=:codeLigne,
						nonCommandable=:nonCommandable,
						poids=:poids,
						codetarif=:codetarif,
						prix=:prix,
						libcolori=:libcolori,
						libMarque=:libMarque,
						commentaire1=:commentaire1,
						commentaire2=:commentaire2,
						commentaire3=:commentaire3,
						commentaire4=:commentaire4,
						commentaire5=:commentaire5,
						libelle2=:libelle2,
						texteLibre=:texteLibre,
						champsstat=:champsstat
						WHERE refproduit=:refproduit 
						AND codeColori=:codeColori");
					$updateProduct = $stmt->execute([
						'stockdisponible' => $currentProductStk,
						'libelle' => $libelle,
						'codeGammeTaille' => $code_gamme,
						'codetailledebut' => $code_taille_debut,
						'codetaillefin' => $code_taille_fin,
						'codeSaison' => $code_saison,
						'codeMarque' => $num_marque,
						'codeTheme' => $num_theme,
						'codeFamille' => $num_famille,
						'codeSousFamille' => $num_sous_famille,
						'codeModele' => $num_modele,
						'codeLigne' => $num_ligne,
						'nonCommandable' => $non_commandable,
						'poids' => $poids,
						'codetarif' => $code_tarif,
						'prix' => $prix,
						'libcolori' => $libelle_colori,
						'libMarque' => $libelle_marque,
						'commentaire1' => $com1,
						'commentaire2' => $com2,
						'commentaire3' => $com3,
						'commentaire4' => $com4,
						'commentaire5' => $com5,
						'libelle2' => $libelle_2,
						'texteLibre' => $texte_libre,
						'champsstat' => $champsstat,
						'refproduit' => $ref_produit,
						'codeColori' => $code_colori
					]);
					if ($updateProduct) {
						file_put_contents($log_file, "[".date('h:m:s')."] "."Modification du produit effectué.\n", FILE_APPEND);
					} else {
						file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de la modification du produit en base de données.\n", FILE_APPEND);
					}
				}
				$currentProductStk = 0;
				$atleastOneDPOrder = false;
			}
			file_put_contents($log_file, "\n", FILE_APPEND);
		}
	}

	$execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	file_put_contents($log_file, "[".date('h:m:s')."] "."Temps d'execution du traitement : ".$execution_time." secondes.\n", FILE_APPEND);
	file_put_contents($log_file, "[".date('h:m:s')."] "."----- Fin de l'import des produits -----\n\n\n", FILE_APPEND);
?>
