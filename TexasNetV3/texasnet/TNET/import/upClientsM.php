<?php
	require_once('./connectM.php');
	session_start();
	set_time_limit(0);
	error_reporting(E_ALL); // display errors
	ini_set("display_errors", 1);

	$import_file = [];
	$current_day = date('d_m_Y');
	$log_file = $C_LOG_DIR.$C_LOG_FILE.$current_day.'.txt';

	file_put_contents($log_file, "[".date('h:m:s')."] "."----- Debut de l'import des clients -----\n\n", FILE_APPEND);
	file_put_contents($log_file, "[".date('h:m:s')."] "."Récupération des positions\n", FILE_APPEND);
	$positions = $db->query("SELECT * FROM posclient")->fetch();
	$pos_code_client = $positions["codeClient"];
	$pos_nom_societe = $positions["nomSociete"];
	$pos_code_postal = $positions["codepostal"];
	$pos_raison_social = $positions["raisonSociale"];
	$pos_telephone = $positions["telephone"];
	$pos_fax = $positions["fax"];
	$pos_email = $positions["email"];
	$pos_login = $positions["login"];
	$pos_password = $positions["password"];
	$pos_code_langue = $positions["codeLangue"];
	$pos_code_tarif = $positions["codeTarif"];
	$pos_ville = $positions["ville"];
	$pos_pays = $positions["pays"];
	$pos_n_adresse_livraison = $positions["nadresselivraison"];
	$pos_adr1_adrliv2 = $positions["adr1adrliv2"];
	$pos_rais_fact_comp = $positions["raisfactcomp"];
	$pos_code_postal_fact = $positions["codePostalFact"];
	$pos_ville_fact = $positions["villeFact"];
	$pos_pays_fact = $positions["paysFact"];
	$pos_n_adresse_facturation = $positions["nadresseFacturation"];
	$pos_adr1_adr2fact = $positions["adr1adr2fact"];
	$pos_taux_remise = $positions["tauxRemise"];
	$pos_code_surveillance = $positions["codeSurveillance"];
	$pos_categorie = $positions["categorie"];
	$pos_geo = $positions["geo"];
	$pos_sous_categorie = $positions["souscategorie"];
	$pos_centrale = $positions["centrale"];
	$pos_sous_centrale = $positions["souscentrale"];
	$pos_point = $positions["point"];
	$pos_civilite = $positions["civilite"];
	$pos_sommeil = $positions["sommeil"];
	$pos_code_pays_l = $positions["codePaysL"];
	$pos_code_pays_f = $positions["codePaysF"];
	$pos_separateur = $positions["separateur"];
	$pos_code_fiscal = $positions["fiscal"];
	file_put_contents($log_file, "[".date('h:m:s')."] "."Lecture du fichier\n\n", FILE_APPEND);
	$import_file = file($C_IMPORT_DIR.$C_CLI_FILE);
	foreach($import_file as $i => $import_line) {
		$import_line = iconv('Windows-1253','UTF-8', $import_line);
		$import_line = str_replace("’", "'", $import_line);
		$line = explode($C_SEPARATOR, $import_line);
		if(trim($import_line) !== '' && trim($line[$pos_code_client-1]) !== '') {
				// Transform data to adapt db's scheme
			$code_client = $line[$pos_code_client-1];
			$nom_societe = $line[$pos_nom_societe-1]?$line[$pos_nom_societe-1]:"";
			$pwd = md5('TexasNet.'.$line[$pos_password-1]);
			$raison_sociale = explode($C_SEPARATOR_SEC, $line[$pos_raison_social-1])[0];
			$complement_livraison = explode($C_SEPARATOR_SEC, $line[$pos_raison_social-1])[1];
			$raison_sociale_facturation = explode($C_SEPARATOR_SEC, $line[$pos_rais_fact_comp-1])[0];
			$complement_facturation = explode($C_SEPARATOR_SEC, $line[$pos_rais_fact_comp-1])[1];
			$telephone = $line[$pos_telephone-1]?$line[$pos_telephone-1]:"";
			$fax = $line[$pos_fax-1]?$line[$pos_fax-1]:"";
			$email = $line[$pos_email-1]?$line[$pos_email-1]:"";
			$login = $line[$pos_login-1]?$line[$pos_login-1]:"";
			$code_langue = $line[$pos_code_langue-1]?$line[$pos_code_langue-1]:"";
			$code_tarif = $line[$pos_code_tarif-1]?$line[$pos_code_tarif-1]:"";
			$code_postal_fact = $line[$pos_code_postal_fact-1]?$line[$pos_code_postal_fact-1]:"";
			$ville_fact = $line[$pos_ville_fact-1]?$line[$pos_ville_fact-1]:"";
			$pays_fact = $line[$pos_pays_fact-1]?$line[$pos_pays_fact-1]:"";
			$taux_remise = $line[$pos_taux_remise-1]?$line[$pos_taux_remise-1]:"0";
			$code_surveillance = $line[$pos_code_surveillance-1]?$line[$pos_code_surveillance-1]:"0";
			$categorie = $line[$pos_categorie-1]?$line[$pos_categorie-1]:"";
			$geo = $line[$pos_geo-1]?$line[$pos_geo-1]:"";
			$sous_categorie = $line[$pos_sous_categorie-1]?$line[$pos_sous_categorie-1]:"";
			$centrale = $line[$pos_centrale-1]?$line[$pos_centrale-1]:"";
			$sous_centrale = $line[$pos_sous_centrale-1]?$line[$pos_sous_centrale-1]:"";
			$civilite = $line[$pos_civilite-1]?$line[$pos_civilite-1]:"";
			$sommeil = $line[$pos_sommeil-1]?$line[$pos_sommeil-1]:"0";
			$code_fiscal = $line[$pos_code_fiscal-1]?$line[$pos_code_fiscal-1]:"";
				// Address
			$adresse_liv_1 = explode($C_SEPARATOR_SEC, $line[$pos_adr1_adrliv2-1])[0];
			$adresse_liv_2 = explode($C_SEPARATOR_SEC, $line[$pos_adr1_adrliv2-1])[1];
			$code_postal = $line[$pos_code_postal-1];
			$ville = $line[$pos_ville-1];
			$pays = $line[$pos_pays-1];
			$code_pays_liv = $line[$pos_code_pays_l-1];
			$num_adresse_liv = $line[$pos_n_adresse_livraison-1];

			$adresse_fac_1 = explode($C_SEPARATOR_SEC, $line[$pos_adr1_adr2fact-1])[0];
			$adresse_fac_2 = explode($C_SEPARATOR_SEC, $line[$pos_adr1_adr2fact-1])[1];
			$code_pays_fact = $line[$pos_code_pays_f-1];
			$num_adresse_fact = $line[$pos_n_adresse_facturation-1];

			file_put_contents($log_file, "[".date('h:m:s')."] "."Recherche du codeClient '".$code_client."' dans la table client.\n", FILE_APPEND);
			file_put_contents($log_file, "[".date('h:m:s')."] "."Pos login : ".$pos_login."\n", FILE_APPEND);
			file_put_contents($log_file, "[".date('h:m:s')."] "."Line login : ".$line[$pos_login-1]."\n", FILE_APPEND);
			file_put_contents($log_file, "[".date('h:m:s')."] "."Login : ".$login."\n", FILE_APPEND);
			if (trim($login) !== "") {
					// Looking for unique codeClient in DB
				$stmt = $db->prepare("SELECT * FROM client WHERE codeClient=:codeClient");
				$stmt->execute(['codeClient' => $line[$pos_code_client-1]]);
				$user = $stmt->fetch();
				if(!$user) {
						// Unindentified client, adding in db
					file_put_contents($log_file, "[".date('h:m:s')."] "."Pas de résultat, ajout dans la base de données.\n", FILE_APPEND);
					$stmt = $db->prepare("INSERT INTO client (codeClient, nomSociete, raisonSociale, complementLivraison, telephone, fax, email, login, password,
					codeLangue, dateDerniereVisite, nombreVisit, codeTarif, raisonSocialeFact, complementFacturation, codePostalFact, villeFact, paysFact,
					tauxRemise, codeSurveillance, categorie, geo, souscategorie, centrale, souscentrale, civilite, sommeil, codeFiscal) VALUES (:codeClient,
					:nomSociete, :raisonSociale, :complementLivraison, :telephone, :fax, :email, :login, :password, :codeLangue, :dateDerniereVisite, :nombreVisit,
					:codeTarif, :raisonSocialeFact, :complementFacturation, :codePostalFact, :villeFact, :paysFact, :tauxRemise, :codeSurveillance, :categorie, :geo,
					:souscategorie, :centrale, :souscentrale, :civilite, :sommeil, :codeFiscal)");
					$insertClient = $stmt->execute([
						'codeClient' => $code_client,
						'nomSociete' => $nom_societe,
						'raisonSociale' => $raison_sociale,
						'complementLivraison' => $complement_livraison,
						'telephone' => $telephone,
						'fax' => $fax,
						'email' => $email,
						'login' => $login,
						'password' => $pwd,
						'codeLangue' => $code_langue,
						'dateDerniereVisite' => "2017-01-01",
						'nombreVisit' => "0",
						'codeTarif' => $code_tarif,
						'raisonSocialeFact' => $raison_sociale_facturation,
						'complementFacturation' => $complement_facturation,
						'codePostalFact' => $code_postal_fact,
						'villeFact' => $ville_fact,
						'paysFact' => $pays_fact,
						'tauxRemise' => $taux_remise,
						'codeSurveillance' => $code_surveillance,
						'categorie' => $categorie,
						'geo' => $geo,
						'souscategorie' => $sous_categorie,
						'centrale' => $centrale,
						'souscentrale' => $sous_centrale,
						'civilite' => $civilite,
						'sommeil' => $sommeil,
						'codeFiscal' => $code_fiscal
					]);
					if ($insertClient) {
						file_put_contents($log_file, "[".date('h:m:s')."] "."Ajout effectué. Ajout des adresses correspondantes.\n", FILE_APPEND);
						$stmt = $db->prepare("INSERT INTO adresse (login, adresse1, adresse2, codePostal, ville, pays, codePays, numero, type)
						VALUES (:login, :adresse1, :adresse2, :codePostal, :ville, :pays, :codePays, :numero, :type)");
						$insertAdresseLivraison = $stmt->execute([
							'login' => $login,
							'adresse1' => $adresse_liv_1,
							'adresse2' => $adresse_liv_2,
							'codePostal' => $code_postal,
							'ville' => $ville,
							'pays' => $pays,
							'codePays' => $code_pays_liv,
							'numero' => $num_adresse_liv,
							'type' => 'livraison'
						]);
						if ($insertAdresseLivraison) {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Ajout effectué pour l'adresse de livraison ".$num_adresse_liv.".\n", FILE_APPEND);
						} else {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de l'ajout en base de données.\n", FILE_APPEND);
						}
						$stmt = $db->prepare("INSERT INTO adresse (login, adresse1, adresse2, codePostal, ville, pays, codePays, numero, type)
						VALUES (:login, :adresse1, :adresse2, :codePostal, :ville, :pays, :codePays, :numero, :type)");
						$insertAdresseFacturation = $stmt->execute([
							'login' => $login,
							'adresse1' => $adresse_fac_1,
							'adresse2' => $adresse_fac_2,
							'codePostal' => $code_postal_fact,
							'ville' => $ville_fact,
							'pays' => $pays_fact,
							'codePays' => $code_pays_fact,
							'numero' => $num_adresse_fact,
							'type' => 'facturation'
						]);
						if ($insertAdresseFacturation) {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Ajout effectué pour l'adresse de facturation ".$num_adresse_fact.".\n", FILE_APPEND);
						} else {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de l'ajout en base de données.\n", FILE_APPEND);
						}
					} else {
						file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de l'ajout en base de données.\n", FILE_APPEND);
					}
				} else {
						// Identified client, checking for modifications in client table
					file_put_contents($log_file, "[".date('h:m:s')."] "."Client trouvé, tests des differences entre les champs du fichier et de la base de données.\n", FILE_APPEND);
					if (
						$raison_sociale !== $user["raisonSociale"] ||
						$complement_livraison !== $user["complementLivraison"] ||
						$raison_sociale_facturation !== $user["raisonSocialeFact"] ||
						$complement_facturation !== $user["complementFacturation"] ||
						$telephone !== $user["telephone"] ||
						$fax !== $user["fax"] ||
						$email !== $user["email"] ||
						$login !== $user["login"] ||
						$code_langue !== $user["codeLangue"] ||
						$code_tarif !== $user["codeTarif"] ||
						$code_postal_fact !== $user["codePostalFact"] ||
						$ville_fact !== $user["villeFact"] ||
						$pays_fact !== $user["paysFact"] ||
						$taux_remise !== $user["tauxRemise"] ||
						$code_surveillance !== $user["codeSurveillance"] ||
						$categorie !== $user["categorie"] ||
						$geo !== $user["geo"] ||
						$sous_categorie !== $user["souscategorie"] ||
						$centrale !== $user["centrale"] ||
						$sous_centrale !== $user["souscentrale"] ||
						$civilite !== $user["civilite"] ||
						$sommeil !== $user["sommeil"] ||
						$code_fiscal !== $user["codeFiscal"]
					) {
							// Data change detected, updating db with new data
						file_put_contents($log_file, "[".date('h:m:s')."] "."Données différentes, modification de la base de données.\n", FILE_APPEND);
						$stmt = $db->prepare("UPDATE client SET codeClient=:codeClient, nomSociete=:nomSociete, raisonSociale=:raisonSociale, complementLivraison=:complementLivraison,
						telephone=:telephone, fax=:fax, email=:email, login=:login, password=:password, codeLangue=:codeLangue,
						codeTarif=:codeTarif, raisonSocialeFact=:raisonSocialeFact, complementFacturation=:complementFacturation, codePostalFact=:codePostalFact,
						villeFact=:villeFact, paysFact=:paysFact, tauxRemise=:tauxRemise, codeSurveillance=:codeSurveillance, categorie=:categorie, geo=:geo, souscategorie=:souscategorie,
						centrale=:centrale, souscentrale=:souscentrale, civilite=:civilite, sommeil=:sommeil, codeFiscal=:codeFiscal WHERE codeClient=:codeClient");
						$stmt->execute([
							'codeClient' => $code_client,
							'nomSociete' => $nom_societe,
							'raisonSociale' => $raison_sociale,
							'complementLivraison' => $complement_livraison,
							'telephone' => $telephone,
							'fax' => $fax,
							'email' => $email,
							'login' => $login,
							'password' => $pwd,
							'codeLangue' => $code_langue,
							'codeTarif' => $code_tarif,
							'raisonSocialeFact' => $raison_sociale_facturation,
							'complementFacturation' => $complement_facturation,
							'codePostalFact' => $code_postal_fact,
							'villeFact' => $ville_fact,
							'paysFact' => $pays_fact,
							'tauxRemise' => $taux_remise,
							'codeSurveillance' => $code_surveillance,
							'categorie' => $categorie,
							'geo' => $geo,
							'souscategorie' => $sous_categorie,
							'centrale' => $centrale,
							'souscentrale' => $sous_centrale,
							'civilite' => $civilite,
							'sommeil' => $sommeil,
							'codeFiscal' => $code_fiscal
							]);
					} else {
							// Data equal, no update
						file_put_contents($log_file, "[".date('h:m:s')."] "."Données identiques pour le client, pas de modification dans la base de données.\n", FILE_APPEND);
					}

						// Identified client, checking for modifications in client adresse
						// Delivery
					$stmt = $db->prepare("SELECT * FROM adresse WHERE login=:login AND numero=:numero AND type='livraison'");
					$stmt->execute(['login' => $login, 'numero' => $num_adresse_liv]);
					$address_liv = $stmt->fetch();
					if ($address_liv) {
						if(
							$adresse_liv_1 !== $address_liv["adresse1"] ||
							$adresse_liv_2 !== $address_liv["adresse2"] ||
							$code_postal !== $address_liv["codePostal"] ||
							$ville !== $address_liv["ville"] ||
							$pays !== $address_liv["pays"] ||
							$code_pays_liv !== $address_liv["codePays"]
						) {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Données différentes pour l'adresse de livraison ".$num_adresse_liv.", modification de la base de données.\n", FILE_APPEND);
							$stmt = $db->prepare("UPDATE adresse SET adresse1=:adresse1, adresse2=:adresse2, codePostal=:codePostal, ville=:ville, pays=:pays, codePays=:codePays WHERE login=:login AND numero=:numero AND type='livraison'");
							$stmt->execute([
								'adresse1' => $adresse_liv_1,
								'adresse2' => $adresse_liv_2,
								'codePostal' => $code_postal,
								'ville' => $ville,
								'pays' => $pays,
								'codePays' => $code_pays_liv,
								'login' => $login,
								'numero' => $num_adresse_liv
							]);
						} else {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Données identiques pour l'adresse de livraison ".$num_adresse_liv.", pas de modification dans la base de données.\n", FILE_APPEND);
						}
					} else {
						file_put_contents($log_file, "[".date('h:m:s')."] "."Aucun résultat pour l'adresse de livraison ".$num_adresse_liv.", ajout en base de données.\n", FILE_APPEND);
						$stmt = $db->prepare("INSERT INTO adresse (login, adresse1, adresse2, codePostal, ville, pays, codePays, numero, type)
						VALUES (:login, :adresse1, :adresse2, :codePostal, :ville, :pays, :codePays, :numero, :type)");
						$insertAdresseLivraison = $stmt->execute([
							'login' => $login,
							'adresse1' => $adresse_liv_1,
							'adresse2' => $adresse_liv_2,
							'codePostal' => $code_postal,
							'ville' => $ville,
							'pays' => $pays,
							'codePays' => $code_pays_liv,
							'numero' => $num_adresse_liv,
							'type' => 'livraison'
						]);
						if ($insertAdresseLivraison) {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Ajout effectué pour l'adresse de livraison ".$num_adresse_liv.".\n", FILE_APPEND);
						} else {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de l'ajout en base de données de l'adresse de livraison ".$num_adresse_liv.".\n", FILE_APPEND);
						}
					}

						// Bill
					$stmt = $db->prepare("SELECT * FROM adresse WHERE login=:login AND numero=:numero AND type='facturation'");
					$stmt->execute(['login' => $login, 'numero' => $num_adresse_fact]);
					$address_fact = $stmt->fetch();
					if ($address_fact) {
						if(
							$adresse_fac_1 !== $address_fact["adresse1"] ||
							$adresse_fac_2 !== $address_fact["adresse2"] ||
							$code_postal_fact !== $address_fact["codePostal"] ||
							$ville_fact !== $address_fact["ville"] ||
							$pays_fact !== $address_fact["pays"] ||
							$code_pays_fact !== $address_fact["codePays"]
						) {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Données différentes pour l'adresse de facturation ".$num_adresse_fact.", modification de la base de données.\n", FILE_APPEND);
							$stmt = $db->prepare("UPDATE adresse SET adresse1=:adresse1, adresse2=:adresse2, codePostal=:codePostal, ville=:ville, pays=:pays, codePays=:codePays WHERE login=:login AND numero=:numero AND type='facturation'");
							$stmt->execute([
								'adresse1' => $adresse_fac_1,
								'adresse2' => $adresse_fac_2,
								'codePostal' => $code_postal_fact,
								'ville' => $ville_fact,
								'pays' => $pays_fact,
								'codePays' => $code_pays_fact,
								'login' => $login,
								'numero' => $num_adresse_fact
							]);
						} else {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Données identiques pour l'adresse de facturation ".$num_adresse_fact.", pas de modification dans la base de données.\n", FILE_APPEND);
						}
					} else {
						file_put_contents($log_file, "[".date('h:m:s')."] "."Aucun résultat pour l'adresse de facturation ".$num_adresse_fact.", ajout en base de données.\n", FILE_APPEND);
						$stmt = $db->prepare("INSERT INTO adresse (login, adresse1, adresse2, codePostal, ville, pays, codePays, numero, type)
						VALUES (:login, :adresse1, :adresse2, :codePostal, :ville, :pays, :codePays, :numero, :type)");
						$insertAdresseFacturation = $stmt->execute([
							'login' => $login,
							'adresse1' => $adresse_fac_1,
							'adresse2' => $adresse_fac_2,
							'codePostal' => $code_postal_fact,
							'ville' => $ville_fact,
							'pays' => $pays_fact,
							'codePays' => $code_pays_fact,
							'numero' => $num_adresse_fact,
							'type' => 'facturation'
						]);
						if ($insertAdresseFacturation) {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Ajout effectué pour l'adresse de facturation ".$num_adresse_fact.".\n", FILE_APPEND);
						} else {
							file_put_contents($log_file, "[".date('h:m:s')."] "."Problème lors de l'ajout en base de données.\n", FILE_APPEND);
						}
					}
				}
			} else {
				file_put_contents($log_file, "[".date('h:m:s')."] "."Le login est vide, annulation de l'insertion en base de données.\n", FILE_APPEND);
			}
			file_put_contents($log_file, "[".date('h:m:s')."] "."Fin de l'intégration de '".$code_client."'.\n\n", FILE_APPEND);
		}
	}
	$execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	file_put_contents($log_file, "[".date('h:m:s')."] "."Temps d'execution du traitement : ".$execution_time." secondes.\n", FILE_APPEND);
	file_put_contents($log_file, "[".date('h:m:s')."] "."----- Fin de l'import des clients -----\n\n\n", FILE_APPEND);
?>
