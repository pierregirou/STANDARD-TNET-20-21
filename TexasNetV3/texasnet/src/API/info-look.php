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
            $reponse=$bdd->prepare("SELECT codeTarif FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$login
            ));
            while($donnees=$reponse->fetch()){
                $codeTarif=$donnees["codeTarif"];
            }
            $reponse->closeCursor();
                    $reponse=$bdd->query("SELECT * FROM looks");                    
                    $i=1;                    
                    while($donnees=$reponse->fetch()){
                        $nomLook=$donnees['nomLook'];  
                        $reponseLook=$bdd->prepare("SELECT produit.*,detaillook.*,looks.nomLook FROM produit INNER JOIN detaillook ON detaillook.idproduit = produit.idproduit INNER JOIN looks ON looks.idLook = detaillook.idLook WHERE codeTarif=:codeTarif AND looks.idLook=:idLook");
                        $reponseLook->execute(array(
                            "codeTarif"=>$codeTarif,
                            "idLook"=>$donnees['idLook']
                        ));
                        while($donneesLook=$reponseLook->fetch()){
                            $arr2[$nomLook][$i]["idproduit"]=$donneesLook["idproduit"];
                            $arr2[$nomLook][$i]["refproduit"]=$donneesLook["refproduit"];     
                            $arr2[$nomLook][$i]["libelle"]=$donneesLook["libelle"];
                            $arr2[$nomLook][$i]["codeColori"]=$donneesLook["codeColori"];
                            $arr2[$nomLook][$i]["nbColori"]=$nbColori;
                            $arr2[$nomLook][$i]["codeGammeTaille"]=$donneesLook["codeGammeTaille"];
                            $arr2[$nomLook][$i]["codetailledebut"]=$donneesLook["codetailledebut"];
                            $arr2[$nomLook][$i]["codetaillefin"]=$donneesLook["codetaillefin"];
                            $arr2[$nomLook][$i]["codeSaison"]=$donneesLook["codeSaison"];
                            $arr2[$nomLook][$i]["codeMarque"]=$donneesLook["codeMarque"];
                            $arr2[$nomLook][$i]["codeTheme"]=$donneesLook["codeTheme"];
                            $arr2[$nomLook][$i]["codeFamille"]=$donneesLook["codeFamille"];
                            $arr2[$nomLook][$i]["codeSousFamille"]=$donneesLook["codeSousFamille"];
                            $arr2[$nomLook][$i]["codeModele"]=$donneesLook["codeModele"];
                            $arr2[$nomLook][$i]["refproduit"]=$donneesLook["refproduit"];
                            $arr2[$nomLook][$i]["codeLigne"]=$donneesLook["codeLigne"];
                            $arr2[$nomLook][$i]["nonCommendable"]=$donneesLook["nonCommendable"];
                            $arr2[$nomLook][$i]["refproduit"]=$donneesLook["refproduit"];
                            $arr2[$nomLook][$i]["poids"]=$donneesLook["poids"];
                            $arr2[$nomLook][$i]["codetarif"]=$donneesLook["codetarif"];
                            $arr2[$nomLook][$i]["prix"]=$donneesLook["prix"];
                            $arr2[$nomLook][$i]["libcolori"]=$donneesLook["libcolori"];
                            $arr2[$nomLook][$i]["libMarque"]=$donneesLook["libMarque"];
                            $arr2[$nomLook][$i]["commentaire1"]=$donneesLook["commentaire1"];
                            $arr2[$nomLook][$i]["commentaire2"]=$donneesLook["commentaire2"];
                            $arr2[$nomLook][$i]["commentaire3"]=$donneesLook["commentaire3"];
                            $arr2[$nomLook][$i]["commentaire4"]=$donneesLook["commentaire4"];
                            $arr2[$nomLook][$i]["commentaire5"]=$donneesLook["commentaire5"];
                            $arr2[$nomLook][$i]["selection"]=$donneesLook["selection"];
                            $arr2[$nomLook][$i]["promo"]=$donneesLook["promo"];
                            $arr2[$nomLook][$i]["tarif_promo"]=$donneesLook["tarif_promo"];
                            $arr2[$nomLook][$i]["codeTarifClient"]=$codeTarif;                    
                            $i++;
                        }
                    }
                    echo json_encode($arr2);
             }
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