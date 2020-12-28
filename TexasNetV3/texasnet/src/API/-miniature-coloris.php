<?php

    $_POST = json_decode(file_get_contents("php://input"),true);
    if(isset($_POST) && !empty($_POST)){
        $refProduit = $_POST["refproduit"];
        $codeColoris = $_POST["codeColoris"];
				$tableauPhotoMini=[];
				$tableauPhotoMiniZ=[];

            // On recherche aussi la photo sans coloris car elle doit etre selectionnable dans les miniatures
        $fichierMin="../Photos/000".$refProduit."-".$codeColoris.".jpg";
        if(file_exists($fichierMin)){
            $tableauPhotoMini[] = $fichierMin;
        }
            //Afficher les photos du produits
        for($p=1;$p<6;$p++){
        $fichierMin="../Photos/000".$refProduit."-".$codeColoris."-".$p.".jpg";
            if(file_exists($fichierMin)){
                $tableauPhotoMini[] = $fichierMin;
            }
        }
				$res['fichierMin'] = $tableauPhotoMini;

						//Afficher les photos du produits zoom
				for($p=1;$p<6;$p++){
				$fichierMin="../Photos/000".$refProduit."-".$codeColoris."-".$p."-Z.jpg";
						if(file_exists($fichierMin)){
								$tableauPhotoMiniZ[] = $fichierMin;
						}
				}
				$res['fichierZoom'] = $tableauPhotoMiniZ;

        echo json_encode($res);

    }
?>
