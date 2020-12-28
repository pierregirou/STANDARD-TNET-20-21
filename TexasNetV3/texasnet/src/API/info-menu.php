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
    if($_POST["cryptKey"]=="eJhG487711G56D14532Ddgj"){
        $login = $_POST['login'];
        //$login = "ASTI";
        $tbs = array();
        $tab = array();
        $fields = array() ;
        $param = $bdd->query('SELECT * FROM modules');
        $paramInfo = $param->fetch();

        $rattachMarque  = $paramInfo['rattachMarque'];
        $catMarque      = $paramInfo['catMarque'];


        $req_ordre = $bdd->query('SELECT * FROM menu ORDER BY ordre_menu');
        $i = 0;

        while( $numero = $req_ordre->fetch()){
            $nom = ucfirst(str_replace('-', '', $numero['nom']));
            $num = $numero['ordre_menu'];
            $noms = "code".$nom;
            if ($i<6) { $concaten  .= "code".$nom.",";} else { $concaten  .= "code".$nom;}
            $i++;

            $actif = $numero['actif'];
            if ($actif == 1){
                $fields [] = $nom;
                $tab[] = $noms;
                $tbs[] = $nom;
            }
        }
        $tb = array();
        if($rattachMarque == 1) {
            // $reqSQL = "SELECT `idproduit`, `refproduit`, p.`libelle`, `codeColori`, `codeGammeTaille`, `codetailledebut`, `codetaillefin`, `codeSaison`, mq.libelle as codeMarque, t.libelle as codeTheme,f.libelle as codeFamille, sf.libelle as codeSousFamille, md.libelle as codeModele, l.libelle as codeLigne, `nonCommandable`, `poids`, `codetarif`, `prix`, `libcolori`, `libMarque`, `commentaire1`, `commentaire2`, `commentaire3`, `commentaire4`, `commentaire5`, `selection`, `promo`, `tarif_promo`, `positionGalerie`, `tarif_pvc`, `stockdisponible`, `libelle2`, `texteLibre` FROM `produit` p LEFT JOIN marque mq ON p.codeMarque = mq.idMarque LEFT JOIN theme t ON p.codeTheme = t.idTheme LEFT JOIN famille f ON p.codeFamille = f.idFamille LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille LEFT JOIN modele md ON p.codeModele = md.idModele LEFT JOIN ligne l ON p.codeLigne = l.idLigne WHERE stockdisponible > 0";
            
            $reqSQL = "SELECT `idproduit`, `refproduit`, p.`libelle`, `codeColori`, `codeGammeTaille`, `codetailledebut`, `codetaillefin`, `codeSaison`,
            substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) as codeMarque,
            substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme,
            substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille,
            substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3,locate('/',substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3))-1) as codeSousFamille,
            substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3,locate('/',substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3))-1) as codeModele,
            substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne,

             `nonCommandable`, `poids`, `codetarif`, `prix`, `libcolori`, `libMarque`, `commentaire1`, `commentaire2`, `commentaire3`, `commentaire4`, `commentaire5`, `selection`, `promo`, `tarif_promo`, `positionGalerie`, `tarif_pvc`, `stockdisponible`, `libelle2`, `texteLibre`
            FROM `produit` p
            LEFT JOIN marque mq ON p.codeMarque = mq.idMarque
            LEFT JOIN theme t ON p.codeTheme = t.idTheme
            LEFT JOIN famille f ON p.codeFamille = f.idFamille
            LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille
            LEFT JOIN modele md ON p.codeModele = md.idModele
            LEFT JOIN ligne l ON p.codeLigne = l.idLigne
            WHERE stockdisponible > 0 ";
            $reqSQL .= " AND substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) IN (SELECT DISTINCT libelleMarque FROM marqueclient WHERE codeclient='$login')";
        } else {
            $reqSQL = "SELECT `idproduit`, `refproduit`, p.`libelle`, `codeColori`, `codeGammeTaille`, `codetailledebut`, `codetaillefin`, `codeSaison`,
substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3,locate('/',substring(mq.libelle,locate('".$_POST['langue']."',mq.libelle)+3))-1) as codeMarque,
substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme,
substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille,
substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3,locate('/',substring(sf.libelle,locate('".$_POST['langue']."',sf.libelle)+3))-1) as codeSousFamille,
substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3,locate('/',substring(md.libelle,locate('".$_POST['langue']."',md.libelle)+3))-1) as codeModele,
substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne,

 `nonCommandable`, `poids`, `codetarif`, `prix`, `libcolori`, `libMarque`, `commentaire1`, `commentaire2`, `commentaire3`, `commentaire4`, `commentaire5`, `selection`, `promo`, `tarif_promo`, `positionGalerie`, `tarif_pvc`, `stockdisponible`, `libelle2`, `texteLibre`
FROM `produit` p
LEFT JOIN marque mq ON p.codeMarque = mq.idMarque
LEFT JOIN theme t ON p.codeTheme = t.idTheme
LEFT JOIN famille f ON p.codeFamille = f.idFamille
LEFT JOIN sousfamille sf ON p.codeSousFamille = sf.idSousfamille
LEFT JOIN modele md ON p.codeModele = md.idModele
LEFT JOIN ligne l ON p.codeLigne = l.idLigne
WHERE stockdisponible > 0 ";
        }

            $reqSQL .= " ORDER BY $concaten";

        $sql_menu = $bdd->query("SELECT COUNT(*) as nbr FROM menu  WHERE actif='1'");
        $nb_actif = $sql_menu->fetch();
        $actif_menu = $nb_actif['nbr'];
        $tab_marque = "";
		    $reqSQL = substr($reqSQL,0,-1);
        $sql = $bdd->query($reqSQL);
        // echo $reqSQL;
        while($data = $sql->fetch()){
            $var1 = utf8_encode($data[$tab[0]]);
            $var2 = utf8_encode($data[$tab[1]]);
            $var3 = utf8_encode($data[$tab[2]]);
            $var4 = utf8_encode($data[$tab[3]]);
            $var5 = utf8_encode($data[$tab[4]]);
            $var6 = utf8_encode($data[$tab[5]]);
            $var7 = utf8_encode($data[$tab[6]]);

            if(strpos($tab_marque, $var1) === false){
                $tab_marque.= $var1.";";
            }
            switch ($actif_menu){
                case 1 :
                    $tb[$var1] = $var1;
                break;

                case 2 :
                if (!isset($tb[$var1])){
                    $tb[$var1] = array();
                }
                    $tb[$var1][$var2] = $var2;

                break;

                case 3 :
                if (!isset($tb[$var1])){
                    $tb[$var1] = array();
                }
                if (!isset($tb[$var1][$var2])){
                    $tb[$var1][$var2] = array();
                }
                    $tb[$var1][$var2][$var3] = $var3;
                break;

                case 4 :
                if (!isset($tb[$var1])){
                    $tb[$var1] = array();
                }
                if (!isset($tb[$var1][$var2])){
                    $tb[$var1][$var2] = array();
                }
                if (!isset($tb[$var1][$var2][$var3])) {
                    $tb[$var1][$var2][$var3] = array();
                }
                    $tb[$var1][$var2][$var3][$var4] = $var4;
                break;

                case 5 :
                if (!isset($tb[$var1])){
                    $tb[$var1] = array();
                }
                if (!isset($tb[$var1][$var2])){
                    $tb[$var1][$var2] = array();
                }
                if (!isset($tb[$var1][$var2][$var3])) {
                    $tb[$var1][$var2][$var3] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4])) {
                    $tb[$var1][$var2][$var3][$var4] = array();
                }
                $tb[$var1][$var2][$var3][$var4][$var5] = $var5;
                break;

                case 6 :
                if (!isset($tb[$var1])){
                    $tb[$var1] = array();
                }
                if (!isset($tb[$var1][$var2])){
                    $tb[$var1][$var2] = array();
                }
                if (!isset($tb[$var1][$var2][$var3])) {
                    $tb[$var1][$var2][$var3] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4])) {
                    $tb[$var1][$var2][$var3][$var4] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4][$var5])) {
                        $tb[$var1][$var2][$var3][$var4][$var5] = array();
                }
                $tb[$var1][$var2][$var3][$var4][$var5][$var6] = $var6;
                break;

                case 7 :
                if (!isset($tb[$var1])){
                    $tb[$var1] = array();
                }
                if (!isset($tb[$var1][$var2])){
                    $tb[$var1][$var2] = array();
                }
                if (!isset($tb[$var1][$var2][$var3])) {
                    $tb[$var1][$var2][$var3] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4])) {
                    $tb[$var1][$var2][$var3][$var4] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4][$var5])) {
                        $tb[$var1][$var2][$var3][$var4][$var5] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4][$var5][$var6])) {
                        $tb[$var1][$var2][$var3][$var4][$var5][$var6] = array();
                }
                $tb[$var1][$var2][$var3][$var4][$var5][$var6] = $var6;
                break;

                case 8 :
                if (!isset($tb[$var1])){
                    $tb[$var1] = array();
                }
                if (!isset($tb[$var1][$var2])){
                    $tb[$var1][$var2] = array();
                }
                if (!isset($tb[$var1][$var2][$var3])) {
                    $tb[$var1][$var2][$var3] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4])) {
                    $tb[$var1][$var2][$var3][$var4] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4][$var5])) {
                        $tb[$var1][$var2][$var3][$var4][$var5] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4][$var5][$var6])) {
                        $tb[$var1][$var2][$var3][$var4][$var5][$var6] = array();
                }
                if (!isset($tb[$var1][$var2][$var3][$var4][$var5][$var6][$var7])) {
                    $tb[$var1][$var2][$var3][$var4][$var5][$var6][$var7] = array();
                }
                $tb[$var1][$var2][$var3][$var4][$var5][$var6][$var7] = $var7;
                break;

                default: $tb[1] = "Tenue à commander";
            }

            switch ($actif_menu){
                case 1 :
                  if($data["tarif_promo"] > 0) {
                    $tb2[$var1] = $var1;
                  }
                break;

                case 2 :
                  if($data["tarif_promo"] > 0) {
                    if (!isset($tb2[$var1])){
                        $tb2[$var1] = array();
                    }
                    $tb2[$var1][$var2] = $var2;
                  }
                break;

                case 3 :
                  if($data["tarif_promo"] > 0) {
                    if (!isset($tb2[$var1])){
                        $tb2[$var1] = array();
                    }
                    if (!isset($tb2[$var1][$var2])){
                        $tb2[$var1][$var2] = array();
                    }
                    $tb2[$var1][$var2][$var3] = $var3;
                  }
                break;

                case 4 :
                  if($data["tarif_promo"] > 0) {
                    if (!isset($tb2[$var1])){
                        $tb2[$var1] = array();
                    }
                    if (!isset($tb2[$var1][$var2])){
                        $tb2[$var1][$var2] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3])) {
                        $tb2[$var1][$var2][$var3] = array();
                    }
                    $tb2[$var1][$var2][$var3][$var4] = $var4;
                  }
                break;

                case 5 :
                  if($data["tarif_promo"] > 0) {
                    if (!isset($tb2[$var1])){
                        $tb2[$var1] = array();
                    }
                    if (!isset($tb2[$var1][$var2])){
                        $tb2[$var1][$var2] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3])) {
                        $tb2[$var1][$var2][$var3] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4])) {
                        $tb2[$var1][$var2][$var3][$var4] = array();
                    }
                    $tb2[$var1][$var2][$var3][$var4][$var5] = $var5;
                  }
                break;

                case 6 :
                  if($data["tarif_promo"] > 0) {
                    if (!isset($tb2[$var1])){
                        $tb2[$var1] = array();
                    }
                    if (!isset($tb2[$var1][$var2])){
                        $tb2[$var1][$var2] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3])) {
                        $tb2[$var1][$var2][$var3] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4])) {
                        $tb2[$var1][$var2][$var3][$var4] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4][$var5])) {
                            $tb2[$var1][$var2][$var3][$var4][$var5] = array();
                    }
                    $tb2[$var1][$var2][$var3][$var4][$var5][$var6] = $var6;
                  }
                break;

                case 7 :
                  if($data["tarif_promo"] > 0) {
                    if (!isset($tb2[$var1])){
                        $tb2[$var1] = array();
                    }
                    if (!isset($tb2[$var1][$var2])){
                        $tb2[$var1][$var2] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3])) {
                        $tb2[$var1][$var2][$var3] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4])) {
                        $tb2[$var1][$var2][$var3][$var4] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4][$var5])) {
                            $tb2[$var1][$var2][$var3][$var4][$var5] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4][$var5][$var6])) {
                            $tb2[$var1][$var2][$var3][$var4][$var5][$var6] = array();
                    }
                    $tb2[$var1][$var2][$var3][$var4][$var5][$var6] = $var6;
                  }
                break;

                case 8 :
                  if($data["tarif_promo"] > 0) {
                    if (!isset($tb2[$var1])){
                        $tb2[$var1] = array();
                    }
                    if (!isset($tb2[$var1][$var2])){
                        $tb2[$var1][$var2] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3])) {
                        $tb2[$var1][$var2][$var3] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4])) {
                        $tb2[$var1][$var2][$var3][$var4] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4][$var5])) {
                            $tb2[$var1][$var2][$var3][$var4][$var5] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4][$var5][$var6])) {
                            $tb2[$var1][$var2][$var3][$var4][$var5][$var6] = array();
                    }
                    if (!isset($tb2[$var1][$var2][$var3][$var4][$var5][$var6][$var7])) {
                        $tb2[$var1][$var2][$var3][$var4][$var5][$var6][$var7] = array();
                    }
                    $tb2[$var1][$var2][$var3][$var4][$var5][$var6][$var7] = $var7;
                  }
                break;

                default: $tb2[1] = "Tenue à commander";
            }
        }
        $tbPromo["Promo"] = $tb2;
        echo json_encode([$tb, $tbPromo]);
    }
}else{
    ?>
    {
        "success":false,
        "message":"Only POST request allowed"
    }
    <?php
}
?>
