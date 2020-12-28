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
		// Ajout de la commande dans la BDD
    $login=$_POST["login"];
    $type=$_POST["type"];
    if(in_array($login,$login_array)){
        $req=$bdd->prepare("UPDATE commande SET valid=1, nom=:nom,prenom=:prenom, dateValidation=NOW(), adresselivraison=:adresselivraison, adressefacturation=:adressefacturation,commentaire1=:commentaire1, commentaire5=:commentaire5, service=:service, etat=:etat WHERE login=:login AND valid=0");
        $req->execute(array(
            "nom"=>$_POST["nom"],
            "prenom"=>$_POST["prenom"],
            "adressefacturation"=>$_POST["adressefac"],
            "adresselivraison"=>$_POST["adresseliv"],
            "commentaire1"=>$_POST["commentaire"],
            "commentaire5"=>$_POST["cintre"],
            "service"=>$_POST["libelleServ"],
            "login"=>$_POST["login"],
            "etat"=>$_POST["etatInitial"]
        ));

        $reponse2=$bdd->query("SELECT * FROM modules");
        $donnees2=$reponse2->fetch();
        $IDBoutique = $donnees2['IDBoutique'];
        $idTransacts = $donnees2['dernierIDTransaction'] + 1;
        $idTransact = str_pad($idTransacts, 6, "0", STR_PAD_LEFT);

        $reqUpd=$bdd->prepare("UPDATE modules SET dernierIDTransaction=:dernierIDTransaction");
        $reqUpd->execute(array(
            "dernierIDTransaction"=>$idTransacts
        ));
    }

	// Récupération des infos pour traitement CB
    $arrayParam = [];



    $currentDateTime = date('YmdHis');

    $arrayParam["success"] = true;
	$arrayParam["vads_action_mode"] = "INTERACTIVE"; // Ne pas changer.
	$arrayParam["vads_amount"] = $_POST["montantPanier"]*100; // *100 car il n'y a pas de virgule, Ne pas changer.
	$arrayParam["vads_ctx_mode"] = "TEST"; // A changer en PRODUCTION
	$arrayParam["vads_currency"] = "978"; // correspond à la devise €, Ne pas changer.
	$arrayParam["vads_page_action"] = "PAYMENT"; // Ne pas changer.
	$arrayParam["vads_payment_config"] = "SINGLE"; // Ne pas changer.
	$arrayParam["vads_site_id"] = $IDBoutique; // ID Boutique, A prendre dans la BDD
	$arrayParam["vads_trans_date"] = $currentDateTime;
	$arrayParam["vads_trans_id"] = $idTransact; // 6 chiffres, ne899999 A prendre dans la BDD
    $arrayParam["vads_version"] = "V2"; // Ne pas changer.

    $signature = "";
    $signatureEncoded = "";

    foreach ($arrayParam as $key => $value) {
        if (substr($key,0,5) == 'vads_') {
            $signature .= $value."+";
        }
    }
        // Ajout de la clef boutique
    $signature .= "IBKE4ZutvUSzvVPF";
        // Encodage
    $signatureEncoded = base64_encode(hash_hmac('sha256',$signature, "IBKE4ZutvUSzvVPF", true));

    $arrayParam["signature"] = $signatureEncoded;


	echo json_encode($arrayParam);

}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>
