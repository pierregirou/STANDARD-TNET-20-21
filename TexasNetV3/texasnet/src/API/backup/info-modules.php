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
        $reponse=$bdd->query("SELECT * FROM modules");
        while($donnees=$reponse->fetch()){
            ?>
            {
                "success":true,
                "visGalerie":<?php echo $donnees['visGalerie']; ?>,
                "ordreAffichage":<?php echo $donnees['ordreAffichage']; ?>,
                "impMarque":<?php echo $donnees['impMarque']; ?>,
                "cumulerSaisonPermanente":<?php echo $donnees['cumulerSaisonPermanente']; ?>,
                "updateAdresse":<?php echo $donnees["updateAdresse"]; ?>,
                "quantiteMax":<?php echo $donnees["quantiteMax"]; ?>,
                "valQteMax":<?php echo $donnees["valQteMax"]; ?>,
                "ordreAffichage":<?php echo $donnees["ordreAffichage"]; ?>,
                "langueAng":<?php echo $donnees["langueAng"]; ?>,
                "soColissimo":<?php echo $donnees["soColissimo"]; ?>,
                "visInformationAff":"<?php echo $donnees["visInformationAff"]; ?>",
                "libelleConstruct":"<?php echo $donnees["libelleConstruct"]; ?>",
                "points":<?php echo $donnees["points"]; ?>,
                "stockCouleur":<?php echo $donnees["stockCouleur"]; ?>,
                "stockDisponible":<?php echo $donnees["stockDisponible"]; ?>,
                "stockIndisponible":<?php echo $donnees["stockIndisponible"]; ?>,
                "minStockLimite":<?php echo $donnees["minStockLimite"]; ?>,
                "maxStockLimite":<?php echo $donnees["maxStockLimite"]; ?>,
                "controleStock":<?php echo $donnees["controleStock"]; ?>,
                "promotion":<?php echo $donnees["promotion"]; ?>,
                "selectionMoment":<?php echo $donnees["selectionMoment"]; ?>,
                "fraisDePort":<?php echo $donnees["fraisDePort"]; ?>,
                "montantPort":<?php echo $donnees["montantPort"]; ?>,
                "portGratuit":<?php echo $donnees["portGratuit"]; ?>,
                "maintenance":<?php echo $donnees["maintenance"]; ?>,
                "cdeMarque":<?php echo $donnees["cdeMarque"]; ?>,
                "statValidPanier":<?php echo $donnees["statValidPanier"]; ?>,
                "gestionGroupe":<?php echo $donnees["gestionGroupe"]; ?>,
                "libelleService":<?php echo $donnees["libelleService"]; ?>,
                "scFoid":"<?php echo $donnees["scFoid"]; ?>",
                "scFraisExpedition":"<?php echo $donnees["scFraisExpedition"]; ?>",
                "scCleSHA1":"<?php echo $donnees["scCleSHA1"]; ?>",
                "scVersionColissimo":"<?php echo $donnees["scVersionColissimo"]; ?>",
                "scRetour":"<?php echo $donnees["scRetour"]; ?>",
                "modeSaisie":"<?php echo $donnees["modeSaisie"]; ?>",
                "visLooks":"<?php echo $donnees["visLooks"]; ?>",
                "timerCommande":<?php echo $donnees["timerCommande"]; ?>,
                "prixVenteConseille":<?php echo $donnees["prixVenteConseille"]; ?>,
                "promoCodeTarif":"<?php echo $donnees["promoCodeTarif"]; ?>",
                "promoPourcentageCodeTarif":"<?php echo $donnees["promoPourcentageCodeTarif"]; ?>",
                "promoPourcentage":"<?php echo $donnees["promoPourcentage"]; ?>",
                "promoMontant":"<?php echo $donnees["promoMontant"]; ?>",
                "CGV":<?php echo $donnees["CGV"]; ?>,
                "cintre":<?php echo $donnees["cintre"]; ?>,
                "filtreCatalogue":<?php echo $donnees["filtreCatalogue"]; ?>,
                "activerVenteCB":<?php echo $donnees["activerVenteCB"]; ?>,
                "fraisDePortJOTT":<?php echo $donnees["fraisDePortJOTT"]; ?>,
                "affLigneVidePanier":<?php echo $donnees["affLigneVidePanier"]; ?>,
                "bloqueModifParamCompte":<?php echo $donnees["bloqueModifParamCompte"]; ?>,
                "dateBloqPanier":<?php echo $donnees["dateBloqPanier"]; ?>,
                "tauxEscompteGlobal":<?php echo $donnees["tauxEscompteGlobal"]; ?>

            }
            <?php
        }
        $reponse->closeCursor();
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