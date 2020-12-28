<?php

  class Telechargement {
    private $upload_dir;
    private $keyFile;
    private $db;
      // In-memory item's data
    private $currentId;
    private $currentIntitule;
    private $currentType;
    private $currentLien;
    private $randNbr;

    function __construct($con) {
      $this->upload_dir = $_SERVER['DOCUMENT_ROOT'].'/'.'telechargements/';
      $this->keyFile    = "multimediaBox";
        // Test folder and if writable
      if (!is_dir($this->upload_dir)) {
        echo "Upload folder not found, can't proceed.";
        $this->upload_dir = '';
      }
      if (!is_writable($this->upload_dir)) {
        echo "Upload folder not writable, can't upload.";
        $this->upload_dir = '';
      }
      $this->db = $con;
      $this->randNbr = '';
    }

      ///////////*
    //   CRUD
    ////////////**

      // Create
    public function create() {
      $req=$this->db->prepare("INSERT INTO `telechargement`(`intitule`, `type`, `lien`) VALUES (:intitule,:type,:lien)");
      if (trim($this->currentIntitule) === '' || trim($this->currentType) === '' || trim($this->currentLien) === '') {
        return false;
      }
       return $req->execute(array(
        "intitule" => $this->currentIntitule,
        "type" => $this->currentType,
        "lien" => $this->currentLien
      ));
    }

      // Query
    public function read($idTel) {
      $idTel = intval($idTel);
      $req=$this->db->prepare("SELECT * FROM `telechargement` WHERE `id`=:id;");
      if ($idTel <= 0) {
        return false;
      }
      return $req->execute(array(
        "id" => $idTel
      ));
    }

    public function readAll() {
      return $this->db->query("SELECT * FROM `telechargement`");
    }

      // Update
    public function update($idTel) {
      $req=$this->db->prepare("UPDATE `telechargement` SET  WHERE `id`=:id;");
      if (trim($this->currentIntitule) === '' || trim($this->currentType) === '' || trim($this->currentLien) === '' || $idTel <= 0) {
        return false;
      }
      return $req->execute(array(
        "id" => $idTel,
        "intitule" => $this->currentIntitule,
        "type" => $this->currentType,
        "lien" => $this->lien
      ));
    }

      // Delete
    public function delete($idTel) {
      $idTel = intval($idTel);
      $req=$this->db->prepare("DELETE FROM `telechargement` WHERE `id`=:id;");
      if ($idTel <= 0) {
        return false;
      }
      return $req->execute(array(
        "id" => $idTel
      ));
    }

      // Getters
    public function getId()       { return $this->currentId; }
    public function getIntitule() { return $this->currentIntitule; }
    public function getType()     { return $this->currentType; }
    public function getLien()     { return $this->currentLien; }
    public function getKeyFile()  { return $this->keyFile; }
    public function getRandNbr()  { return $this->randNbr; }

      // Setters
    public function setId($a)       { $this->currentId = $a; return $this; }
    public function setIntitule($a) { $this->currentIntitule = $a; return $this; }
    public function setType($a)     { $this->currentType = $a; return $this; }
    public function setLien($a)     { $this->currentLien = $a; return $this; }
    public function setRandNbr($a)  { $this->randNbr = $a; return $this; }

      // Helpers
    public function loadFromFile() {
      header('Content-Type: application/json; charset=utf-8');
      if ($_FILES[$this->keyFile]){
        $box_name       = $_FILES[$this->keyFile]["name"];
        $box_tmp_name   = $_FILES[$this->keyFile]["tmp_name"];
        $box_error      = $_FILES[$this->keyFile]["error"];
        if ($box_error > 0) {
          echo "Erreur N°".intval($box_error)." lors de l'import du fichier.";
          return false;
        } else {
            // Random name for the loaded file
            $tmp_rand_nbr = 0;
          do {
            $tmp_rand_nbr = rand(1000,1000000)."-";
            $random_name = $tmp_rand_nbr.$box_name;
            $upload_name = $this->upload_dir.strtolower($random_name);
            $upload_name = preg_replace('/\s+/', '-', $upload_name);
          } while (file_exists($upload_name));
          $this->randNbr = $tmp_rand_nbr;
          if (move_uploaded_file($box_tmp_name , $upload_name)) {
            //echo "Le fichier a bien été uploadé.";
            return true;
          } else {
            //echo "Erreur lors du transfert du fichier.";
            return false;
          }
        }
      } else {
        //echo "Aucun fichier n'a été envoyé.";
      }
      return false;
    }

  }
?>
