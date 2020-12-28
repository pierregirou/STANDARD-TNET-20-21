<?php
    $_POST = json_decode(file_get_contents("php://input"),true);
    if(isset($_POST) && !empty($_POST)){
        $refProduit = $_POST["refproduit"];
        $codeColoris = $_POST["codeColoris"];
        $codeSaison = $_POST["codeSaison"];
        $tableauPhotoMini=[];
        $tableauPhotoMiniZ=[];

        //*********************************************************************Gestion photo ou coloris******************************************************************************************* */
        
        $refProduit=str_replace('/','_',$_POST["refproduit"]);
        //$refProduit=str_replace(' ','_',$refProduit);

        // On recherche aussi la photo sans coloris car elle doit etre selectionnable dans les miniatures
        $fichierMin="Photos/".$codeSaison.$refProduit."-".$codeColoris."-1.jpg";
        if(file_exists("../".$fichierMin)){
            $tableauPhotoMini[] = $fichierMin;
        }else{
            $fichierMin  ="Photos/".$codeSaison.$refProduit."-1.jpg";
            $tableauPhotoMini[] = $fichierMin;
            //$fichierMinSansCodeColoris ="Photos/".$codeSaison.$refProduit.".jpg";
        }
        //Afficher les photos du produits
        for($p=1;$p<6;$p++){
        $fichierMin="Photos/".$codeSaison.$refProduit."-".$codeColoris."-".$p.".jpg";
            if(file_exists("../".$fichierMin) && in_array($fichierMin,$tableauPhotoMini)== false){
                $tableauPhotoMini[] = $fichierMin;
            }
        }
				$res['fichierMin'] = $tableauPhotoMini;

				//Afficher les photos du produits zoom
				for($p=1;$p<6;$p++){
				$fichierMin="Photos/".$codeSaison.$refProduit."-".$codeColoris."-".$p."-Z.jpg";
						if(file_exists("../".$fichierMin)){
								$tableauPhotoMiniZ[] = $fichierMin;
						}
				}
				$res['fichierZoom'] = $tableauPhotoMiniZ;

        echo json_encode($res);

    }
?>
