<?php
/* Info filtre permet de récupèrer les différentes possibilités de trie: taille /couleur/theme... */
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
            $reponse=$bdd->prepare("SELECT codetarif FROM client WHERE login=:login");
            $reponse->execute(array(
                "login"=>$login
            ));
            $retour=$reponse->fetch();
            $codetarif=$retour["codetarif"];
            if($_POST["type"]=="colori"){ //pour récupérer les couleurs à filtrer 
                $arrColori[0]=true;
                $reponse->closeCursor();
                $reponse=$bdd->prepare("SELECT DISTINCT libcolori FROM produit WHERE codetarif=:codetarif");
                $reponse->execute(array(
                    "codetarif"=>$codetarif
                ));
                $i=1;
                while($donnees=$reponse->fetch()){
                    $arrColori[$i]=$donnees["libcolori"];
                    $i++;
                }
                $reponse->closeCursor();
                echo json_encode($arrColori);
            }
            if($_POST["type"]=="taille"){ //pour récupérer les tailles à filtre
                $arrTaille[0]=true;
                $reponse=$bdd->prepare("SELECT DISTINCT codeGammeTaille FROM produit WHERE codetarif=:codetarif");
                $reponse->execute(array(
                    "codetarif"=>$codetarif
                ));
                $i=1;
                while($donnees=$reponse->fetch()){
                    $arrTaille[$i]["codeGammeTaille"]=$donnees["codeGammeTaille"];
                    $reponse2=$bdd->prepare("SELECT libelle FROM taille WHERE codegamme=:codeGammeTaille");
                    $reponse2->execute(array(
                        "codeGammeTaille"=>$donnees["codeGammeTaille"]
                    ));
                    $j=0;
                    while($donnees2=$reponse2->fetch()){
                        $arrTaille[$i]["taille"][$j]=$donnees2["libelle"];
                        $j++;
                    }
                    $reponse2->closeCursor();
                    $i++;
                }
                $reponse->closeCursor();
                echo json_encode($arrTaille);
            }
            if($_POST["type"]=="matiere"){ //pour récupérer les couleurs à filtrer 
                $arrColori[0]=true;
                $reponse->closeCursor();
                $reponse=$bdd->prepare("SELECT DISTINCT texteLibre FROM produit WHERE codetarif=:codetarif");
                $reponse->execute(array(
                    "codetarif"=>$codetarif
                ));
                $i=1;
                while($donnees=$reponse->fetch()){
                    $arrMatiere[$i]=$donnees["texteLibre"];
                    $i++;
                }
                $reponse->closeCursor();
                echo json_encode($arrMatiere);
            }
			
			if($_POST["type"]=="ligne"){ //pour récupérer les couleurs à filtrer 
                $arrColori[0]=true;
                $reponse->closeCursor();
                $reponse=$bdd->prepare("SELECT DISTINCT substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3,locate('/',substring(l.libelle,locate('".$_POST['langue']."',l.libelle)+3))-1) as codeLigne FROM produit p LEFT JOIN ligne l ON p.codeLigne = l.idLigne WHERE codetarif=:codetarif");
                $reponse->execute(array(
                    "codetarif"=>$codetarif
                ));
                $i=1;
                while($donnees=$reponse->fetch()){
                    $arrLigne[$i]=$donnees["codeLigne"];
                    $i++;
                }
                $reponse->closeCursor();
                echo json_encode($arrLigne);
            }
		
            if($_POST["type"]=="famille"){ //pour récupérer les couleurs à filtrer 
                $arrColori[0]=true;
                $reponse->closeCursor();
                $reponse=$bdd->prepare("SELECT DISTINCT substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3,locate('/',substring(f.libelle,locate('".$_POST['langue']."',f.libelle)+3))-1) as codeFamille FROM produit p LEFT JOIN famille f ON p.codeFamille = f.idFamille WHERE codetarif=:codetarif");
                $reponse->execute(array(
                    "codetarif"=>$codetarif
                ));
                $i=1;
                while($donnees=$reponse->fetch()){
                    $arrFamille[$i]=$donnees["codeFamille"];
                    $i++;
                }
                $reponse->closeCursor();
                echo json_encode($arrFamille);
            }
		
            if($_POST["type"]=="theme"){ //pour récupérer les couleurs à filtrer 
                $arrColori[0]=true;
                $reponse->closeCursor();
                $reponse=$bdd->prepare("SELECT DISTINCT substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3,locate('/',substring(t.libelle,locate('".$_POST['langue']."',t.libelle)+3))-1) as codeTheme FROM produit p LEFT JOIN theme t ON p.codeTheme = t.idTheme WHERE codetarif=:codetarif");
                $reponse->execute(array(
                    "codetarif"=>$codetarif
                ));
                $i=1;
                while($donnees=$reponse->fetch()){
                    $arrTheme[$i]=$donnees["codeTheme"];
                    $i++;
                }
                $reponse->closeCursor();
                echo json_encode($arrTheme);
            }
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