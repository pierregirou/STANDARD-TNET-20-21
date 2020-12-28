<?php
include("connect.php");
$login_array=[];

$reponse=$bdd->query("SELECT * FROM client");
while($donnees=$reponse->fetch()){
    $login_array[]= $donnees["login"];
}
$reponse->closeCursor();



$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $login=$_POST["login"];
    $type=$_POST["type"];
    if(in_array($login,$login_array)){
        if(!empty($nom)){
            $nom = $_POST["nom"];
        } else {
            $nom = "";
        }
        if(!empty($prenom)){
            $prenom = $_POST["prenom"];
        } else {
            $prenom = "";
        }
        if(!empty($commentaire5)){
            $commentaire5 = $_POST["cintre"];
        }else {
            $commentaire5 = "";
        }
		
		if(!empty($_POST["loginRep"])){
            $loginRep = $_POST["loginRep"];
        }else {
            $loginRep = $login;
        }
		echo $commentaire4;
        if(!empty($service)){
            $service = $_POST["libelleServ"];
        }else {
            $service = "";
        }
        $date = date_create($_POST["date"]);
        $datelivraison = date_format($date, 'Y-m-d');
        $adressefacturation = $_POST["adressefac"];
        $adresselivraison = $_POST["adresseliv"];
        $commentaire1 = $_POST["commentaire"];
        $fraisDePort = $_POST["montantPort"];
        $montantEscompte = $_POST["montantEscompte"];

        $reqInfo = $bdd->prepare("SELECT numCommande FROM commande WHERE login=:login AND valid=0");
        $reqInfo->execute(array(
            "login"=>$_POST["login"]
        ));
        $donneesInfos=$reqInfo->fetch();
        $numCommande = $donneesInfos['numCommande'];
		
		
		$reqInfo2 = $bdd->prepare("SELECT * FROM client WHERE login=:login");
        $reqInfo2->execute(array(
            "login"=>$login
        ));
        $donneesInfos2=$reqInfo2->fetch();
        $email 		= $donneesInfos2['email'];
        $telephone 	= $donneesInfos2['telephone'];
		
		/*echo "UPDATE commande SET valid=1, nom='".$_POST["nom"]."',prenom='".$_POST["prenom"]."', dateValidation=NOW(), datelivraison='".$datelivraison."', adresselivraison='".$_POST["adresseliv"]."', adressefacturation='".$_POST["adressefac"]."',commentaire1='".$_POST["commentaire"]."', commentaire4='".$commentaire4."', commentaire5='".$commentaire5."', service='".$_POST["libelleServ"]."', fraisport='".$_POST["montantPort"]."', escompte='".$_POST["montantEscompte"]."', etat='".$_POST["etatInitial"]."', mail='$email', tel='$telephone' WHERE login='$login' AND valid=0";*/
	
        $req=$bdd->prepare("UPDATE commande SET valid=1, nom=:nom,prenom=:prenom, dateValidation=NOW(), datelivraison=:datelivraison, adresselivraison=:adresselivraison, adressefacturation=:adressefacturation,commentaire1=:commentaire1, commentaire4=:commentaire4, commentaire5=:commentaire5, service=:service, fraisport=:fraisDePort, escompte=:montantEscompte, etat=:etat, mail=:email, tel=:telephone WHERE login=:login AND valid=0");
        $req->execute(array(
            "nom"=>$_POST["nom"],
            "prenom"=>$_POST["prenom"],
            "datelivraison"=>$datelivraison,
            "adressefacturation"=>$_POST["adressefac"],
            "adresselivraison"=>$_POST["adresseliv"],
            "commentaire1"=>$_POST["commentaire"],
            "commentaire4"=>$loginRep,
            "commentaire5"=>$_POST["cintre"],
            "service"=>$_POST["libelleServ"],
            "fraisDePort"=>$_POST["montantPort"],
            "montantEscompte"=>$_POST["montantEscompte"],
            "login"=>$_POST["login"],
            "etat"=>$_POST["etatInitial"],
            "email"=>$email,
            "telephone"=>$telephone
        ));
        ?>
        {
            "success":true,
            "message":"commande validée",
            "numCommande":<?php echo $numCommande; ?>
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
