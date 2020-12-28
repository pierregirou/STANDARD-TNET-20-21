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
            // Login
        $login=$_POST['login'];
        if(in_array($login,$login_array)){

            /////////////////
            //    Params
            //

            $req = "SELECT *
            FROM produit P
            INNER JOIN detailproduit DP
            ON DP.refproduit = P.refproduit
            AND DP.codeColori= P.codeColori
            AND DP.codeGammeTaille = P.codeGammeTaille
            WHERE P.codetarif = :codetarif
            AND P.nonCommandable > 0
            AND P.stockdisponible > 0 ";

            $reponse=$bdd->query("SELECT ordreAffichage,souscenTheme FROM modules");
            $retour=$reponse->fetch();
            $souscenTheme=$retour["souscenTheme"];
            $ordreAffichage=$retour["ordreAffichage"];
            $reponse->closeCursor();

                // Codetarif
            $reponse=$bdd->prepare("SELECT codetarif, souscentrale FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$login
            ));
            $retour=$reponse->fetch();
            $sousCentralClient=substr($retour["souscentrale"], 0, -1);
            $codetarif=$retour["codetarif"];
            $reponse->closeCursor();

                // Url
            $tabMenu=explode("&&",$_POST["url"]);
            $menu_actif=[];
            $reponse=$bdd->query("SELECT nom FROM menu WHERE actif=1 ORDER BY ordre_menu");
            while($donnees=$reponse->fetch()){
                $menu_actif[]=$donnees["nom"];
            }
            $reponse->closeCursor();

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

            if($ligne!=""){
                $req.=" AND codeLigne='$ligne'";
            }
            if($modele!=""){
                $req.=" AND codeModele='$modele'";
            }
            if($souscenTheme == 1) {
                $req .= " AND codeMarque='$sousCentralClient' ";
            }
            if($sousFamille!=""){
                $req.=" AND codeSousFamille='$sousFamille'";
            }
            if($theme!=""){
                $req.=" AND codeTheme='$theme'";
            }
            if($famille!=""){
                $req.=" AND codeFamille='$famille'";
            }

                // Coloris
            $listeColoris = "";
            foreach($_POST["coloris"] as $colori){
                if ($listeColoris === "") {
                    $listeColoris = "\"".$colori."\"";
                } else {
                    $listeColoris .= ",\"".$colori."\"";
                }
            }
            if ($listeColoris !== "") {
                $req .= " AND P.libcolori IN ($listeColoris) ";
            }
                // Taille
            if (count($_POST["taille"]) > 0) {
              $req .= "AND (";
            }
            $tempCheckFirst = true;
            foreach($_POST["taille"] as $taille){
                $reponse2=$bdd->prepare("SELECT codetaille FROM taille WHERE codegamme=:codegamme AND libelle=:libelle");
                $reponse2->execute(array(
                    "codegamme"=>$taille[0],
                    "libelle"=>$taille[1]
                ));
                $retour=$reponse2->fetch();
                $tempCodeTaille = $retour["codetaille"];
                if ($tempCheckFirst) {
                  $req .= "(DP.codeGammeTaille='$taille[0]' AND DP.codetaille='$tempCodeTaille')";
                  $tempCheckFirst = false;
                } else {
                  $req .= " OR (DP.codeGammeTaille='$taille[0]' AND DP.codetaille='$tempCodeTaille')";
                }
                $reponse2->closeCursor();
            }
            if (count($_POST["taille"]) > 0) {
              $req .= ")";
            }

                // Order by
            switch($ordreAffichage){
                case "1":
                    $orderBy = "prix asc";
                    break;
                case "2":
                    $orderBy = "prix desc";
                    break;
                case "3":
                    $orderBy = "libelle asc";
                    break;
                case "4":
                    $orderBy = "libelle desc";
                    break;
                case "5":
                    $orderBy = "positionGalerie";
                    break;
            }

            $req .=" GROUP BY P.refproduit, P.prix, P.tarif_promo ORDER BY $orderBy";

            $reponse=$bdd->prepare($req);
            $reponse->setFetchMode(PDO::FETCH_NAMED);
            $reponse->execute(array(
                ":codetarif"=>$codetarif
            ));
            $arrProduit[0] = false;
            $i = 1;

            while($donnees=$reponse->fetch()){

                $refProduit=str_replace('/','_',$donnees['refproduit'][0]);
                $refProduit=str_replace(' ','_',$refProduit);

                $fichier= "../Photos/".$donnees['codeSaison'].$refProduit.".jpg";
                $fichier1= "../Photos/".$donnees['codeSaison'].$refProduit."-1.jpg";;
                $fichier2= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori'][0]."-1.jpg";
                $fichier3= "../Photos/".$donnees['codeSaison'].$refProduit."--0.jpg";
                $fichier4= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori'][0]."-0.jpg";
                $fichier5= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori'][0]."-".$donnees['libelle']."-1.jpg";
                $fichier6= "../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori'][0].".jpg";

                for($p=1;$p<6;$p++){
                    $fichierMin="../Photos/".$donnees['codeSaison'].$refProduit."-".$donnees['codeColori'][0]."-".$p.".jpg";
                    $arrProduit[$i]["imageMiniature"][$p] = $fichierMin;
                }

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
                $refprod = $donnees["refproduit"][0];
                $arrProduit[$i]["idproduit"]=$donnees["idproduit"][0];
                $arrProduit[$i]["refproduit"]=$donnees["refproduit"][0];
                $arrProduit[$i]["libelle"]=$donnees["libelle"];
                $arrProduit[$i]["codeColori"]=$donnees["codeColori"][0];
                //$arrProduit[$i]["nbColori"]=$nbColori;
                $arrProduit[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"][0];
                $arrProduit[$i]["codetailledebut"]=$donnees["codetailledebut"][0];
                $arrProduit[$i]["codetaillefin"]=$donnees["codetaillefin"][0];
                $arrProduit[$i]["codeSaison"]=$donnees["codeSaison"];
                $arrProduit[$i]["codeMarque"]=$donnees["codeMarque"];
                $arrProduit[$i]["codeTheme"]=$donnees["codeTheme"];
                $arrProduit[$i]["codeFamille"]=$donnees["codeFamille"];
                $arrProduit[$i]["codeSousFamille"]=$donnees["codeSousFamille"];
                $arrProduit[$i]["codeModele"]=$donnees["codeModele"];
                $arrProduit[$i]["codeLigne"]=$donnees["codeLigne"];
                $arrProduit[$i]["nonCommandable"]=$donnees["nonCommandable"][1];
                $arrProduit[$i]["poids"]=$donnees["poids"];
                $arrProduit[$i]["codetarif"]=$donnees["codetarif"][0];
                $arrProduit[$i]["prix"]=$donnees["prix"][1];
                $arrProduit[$i]["libcolori"]=$donnees["libcolori"];
                $arrProduit[$i]["libMarque"]=$donnees["libMarque"];
                $arrProduit[$i]["commentaire1"]=$donnees["commentaire1"];
                $arrProduit[$i]["commentaire2"]=$donnees["commentaire2"];
                $arrProduit[$i]["commentaire3"]=$donnees["commentaire3"];
                $arrProduit[$i]["commentaire4"]=$donnees["commentaire4"];
                $arrProduit[$i]["commentaire5"]=$donnees["commentaire5"];
                $arrProduit[$i]["selection"]=$donnees["selection"];
                $arrProduit[$i]["promo"]=$donnees["promo"];
                $arrProduit[$i]["tarif_promo"]=$donnees["tarif_promo"];
                $arrProduit[$i]["tarif_pvc"]=$donnees["tarif_pvc"];
                $arrProduit[$i]["stockdisponible"]=$donnees["stockdisponible"][1];
                $arrProduit[$i]["libelle2"]=$donnees["libelle2"];
                $arrProduit[$i]["texteLibre"]=$donnees["texteLibre"];
                $arrProduit[$i]["codetaille"]=$donnees["codetaille"];
                $arrProduit[$i]["positionGalerie"]=(string)$i;
                $arrProduit[$i]["codeTarifClient"]=$codetarif;
                $arrProduit[$i]["ordreAffichage"]=$ordreAffichage;
                $arrProduit[$i]["imageArt"]=$fichier;
                $reponse2=$bdd->prepare("SELECT COUNT(refproduit) as nbRef FROM produit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND nonCommandable > 0 AND stockdisponible > 0 AND tarif_promo=:TarifPromo");
                        $reponse2->execute(array(
                            "refproduit"=>$donnees["refproduit"][0],
                            "TarifPromo"=>$donnees["tarif_promo"],
                            "codeTarif"=>$codetarif
                        ));
                $retour=$reponse2->fetch();
                $arrProduit[$i]["nbRef"]=$retour["nbRef"];
                $reponse2->closeCursor();
                $reponse3=$bdd->prepare("SELECT libcolori,codeColori,tarif_pvc FROM produit WHERE codetarif=:codeTarif AND refproduit=:refproduit AND nonCommandable > 0 AND stockdisponible > 0  AND tarif_promo=:TarifPromo");
                  $reponse3->execute(array(
                    "refproduit"=>$refprod,
                    "TarifPromo"=>$donnees["tarif_promo"],
                    "codeTarif"=>$codetarif
                ));
                $j=0;
                 while($donnees3=$reponse3->fetch()){
                    $arrProduit[$i]["arrayColori"][$j]["libcolori"]=$donnees3["libcolori"];
                    $arrProduit[$i]["arrayColori"][$j]["tarif_pvc"]=$donnees3["tarif_pvc"];

                    $picto = "../../Photos/Coloris/-".$donnees3["codeColori"]."-".strtoupper($donnees3["libcolori"])."-1.jpg";
                    $arrProduit[$i]["arrayColori"][$j]["codeColoris"]=$picto;
                    $j++;
                }
                $reponse3->closeCursor();
                $i++;
            }
            $reponse->closeCursor();
                // Renvoi un booléen si il y a des produits
            if (count($arrProduit) > 1) {
                $arrProduit[0] = true;
            }

            echo json_encode($arrProduit);
        }
    }else{
        ?>
        {
            "success":false,
            "message":"mauvais login"
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
