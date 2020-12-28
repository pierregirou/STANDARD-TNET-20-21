<?php
require 'connect.php';
require 'TelechargementClass.php';
$logJSON = "";
if(isset($_POST) && !empty($_POST)){
  $logJSON .= "POST defini. ";
  $DL = new Telechargement($bdd);
  $logJSON .= "Creation de l'objet telechargement. ";
  switch($_POST["action"]) {
    case "uploadFile":
      $logJSON .= "Action : upload. ";
      if ($_POST["telType"] === "1" || $DL->loadFromFile()) {
        $logJSON .= "Fichier charge sur le FTP. ";
        $DL->setIntitule($_POST["telIntitule"])
           ->setType($_POST["telType"])
           ->setLien($DL->getRandNbr().$_POST["telLien"]);
        if ($DL->create()) {
          $logJSON .= "Ajoute a la base de donnee. ";
        } else {
          $logJSON .= "Erreur lors de l\'ajout en base de donnee. ";
        }
      }
      break;
    case "getAll":
      $logJSON .= "Action : liste les telechargements. ";
      $DL->readAll();
      break;
  }
} else {
  $logJSON .= "Pas de POST defini. Impossible de recuperer l'action. ";
}

echo json_encode($logJSON);

?>
