<?php
include('connect.php');

$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
        $reponseC=$bdd->prepare("SELECT * FROM client WHERE login=:login");
        $reponseC->execute(array(
            "login"=>$_POST['login']
        ));
        $donneesC=$reponseC->fetch();
        $fiscal = $donneesC['codeFiscal'];

        $i = 0;       
        if($fiscal === 3) {
            $fiscal = 2;
        }
        $req = "SELECT * FROM fraisDePortJott WHERE codeFiscal=$fiscal";
        $reponse=$bdd->query($req);
        while($donnees=$reponse->fetch()){

            $arr2[$i]["codeFiscal"]=$donnees["codeFiscal"];
            $arr2[$i]["devise"]=$donnees["devise"];     
            $arr2[$i]["trancheDeb"]=$donnees["trancheDeb"];
            $arr2[$i]["trancheFin"]=$donnees["trancheFin"];
            $arr2[$i]["montantFDP"]=$donnees["montantFDP"]; 
            $arr2[$i]["calculer"]=$donnees["calculer"]; 

            $i++;
        }
        echo json_encode($arr2);
}else{
    ?>
    {
        "success":false,
        "message":"Only POST request allowed"
    }
    <?php
}
?>