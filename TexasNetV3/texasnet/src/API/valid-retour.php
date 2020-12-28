<?php
include("connect.php");
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $login=$_POST["login"];
    $type=$_POST["type"];
        $orosId=$_POST['orosId'];
        $key=$_POST['key'];
        $destinationName=$_POST['destinationName'];
        $crName=$_POST['crName'];
        $crFirstName=$_POST['crFirstName'];
        $crFlagProfessional=$_POST['crFlagProfessional'];
        $crCompanyName=$_POST['crCompanyName'];
        $crSiret=$_POST['crSiret'];
        $crCivility=$_POST['crCivility'];
        $crAdress1=$_POST['crAdress1'];
        $crAdress2=$_POST['crAdress2'];
        $crCountryCode=$_POST['crCountryCode'];
        $crTown=$_POST['crTown'];
        $crZipCode=$_POST['crZipCode'];
        $crTel=$_POST['crTel'];
        $crEmail=$_POST['crEmail'];
        $insuranceRange=$_POST['insuranceRange'];
        $flagBulky=$_POST['flagBulky'];
        $returnCause=$_POST['returnCause'];
        $trClientNumber=$_POST['trClientNumber'];
        $trProductRef=$_POST['trProductRef'];
        $trOrderNumber=$_POST['trOrderNumber'];
        $trReturnRef=$_POST['trReturnRef'];
        $trParamPlus=$_POST['trParamPlus'];
        $orderId=$_POST['orderId'];
        $signature=$_POST['signature'];
        ?>
        {
            "orosId":"<?php echo $orosId; ?>",
            "key":"<?php echo $key; ?>",
            "destinationName":"<?php echo $destinationName; ?>",
            "crName":"<?php echo $crName; ?>",
            "crFirstName":"<?php echo $crFirstName; ?>",
            "crFlagProfessional":"<?php echo $crFlagProfessional; ?>",
            "crCompanyName":"<?php echo $crCompanyName; ?>",
            "crSiret":"<?php echo $crSiret; ?>",
            "crCivility":"<?php echo $crCivility; ?>",
            "crAdress1":"<?php echo $crAdress1; ?>",
            "crAdress2":"<?php echo $crAdress2; ?>",
            "crCountryCode":"<?php echo $crCountryCode; ?>",
            "crTown":"<?php echo $crTown; ?>",
            "crZipCode":"<?php echo $crZipCode; ?>",
            "crTel":"<?php echo $crTel; ?>",
            "crEmail":"<?php echo $crEmail; ?>",
            "insuranceRange":"<?php echo $insuranceRange; ?>",
            "flagBulky":"<?php echo $flagBulky; ?>",
            "trClientNumber":"<?php echo $trClientNumber; ?>",
            "trOrderNumber":"<?php echo $trOrderNumber; ?>",
            "trReturnRef":"<?php echo $trReturnRef; ?>",
            "trParamPlus":"<?php echo $trParamPlus; ?>",
            "orderId":"<?php echo $orderId; ?>",
            "signature":"<?php echo $signature; ?>"
        }
        <?php
}else{
    ?>
    {
        "success":false,
        "message":"Only post request allowed"
    }
    <?php
}
?>