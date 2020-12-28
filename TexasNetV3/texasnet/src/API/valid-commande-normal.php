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
               // envoi un email au client lors de la validation de la commande sur TNET 
		$destinataire   = $email;
        $expediteur 	= "contact@amateis.fr";
		$subject        = " Confirmation de commande  N° ".$numCommande;
        $titre          = "Confirmation de commande";
        $message        = '
        <html>
            <head>
                <title> Confirmation de commande  N° '.$numCommande .'</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            </head>
            <body>
                <p>Bonjour '.$_POST["nom"].',
                <br><br>
                Nous vous remercions pour votre commande n° '.$numCommande .' .<br><br>
                Vous pouvez consulter votre commande sur votre compte sur le site http://siteInternetDeLaSociete.com/texasnet/.
                <br>
                Une fois connecté, il suffit de cliquer sur  <i class="fas fa-user"></i> "mon compte"  en haut à droite, puis sur "historique des commandes".<br>
                <br>
            <p>Munissez-vous de vos identifiants<p>
            <br>
            <p> Votre login : '.$_POST["login"].' </p>
            <p> Mot de passe : *****  </p>
            <br>
            L\'équipe Amateis.<br>
            </body>
        </html>';
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";        
        mail($destinataire,$subject,$message,$headers);
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
