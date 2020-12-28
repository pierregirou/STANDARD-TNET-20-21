<?php
include("connect.php");
$_POST = json_decode(file_get_contents("php://input"),true);
if(isset($_POST) && !empty($_POST)){
    $login=$_POST["login"];
    $type=$_POST["type"];
        $pudoFOId=$_POST['pudoFOId'];
        $key=$_POST['key'];
        $trReturnUrlKo=$_POST['trReturnUrlKo'];
        $trReturnUrlok=$_POST['trReturnUrlok'];
        $dyForwardingCharges=$_POST['dyForwardingCharges'];
        //$dyPreparationTime='';
        $orderId=$_POST['orderId'];
        $numVersion=$_POST['numVersion'];
        $trClientNumber=$_POST['trClientNumber'];
        $signature=$_POST['signature'];
        $ceCivility=$_POST['ceCivility'];
        $ceName=$_POST['ceName'];
        $ceFirstName=$_POST['ceFirstName'];
        $ceAdress3=$_POST['ceAdress3'];
        $ceAdress2=$_POST['ceAdress2'];
        $ceZipCode=$_POST['ceZipCode'];
        $ceTown=$_POST['ceTown'];
        $ceEmail=$_POST['ceEmail'];
        $cePhoneNumber=$_POST['cePhoneNumber'];
        $trParamPlus=$_POST['trParamPlus'];
        $commentaire=$_POST['ceDeliveryInformation'];
        ?>
        {
            "pudoFOId":"<?php echo $pudoFOId; ?>",
            "key":"<?php echo $key; ?>",
            "trReturnUrlKo":"http://192.202.7.40/texasnetdev2",
            "trReturnUrlok":"http://192.202.7.40/texasnetdev",
            "dyForwardingCharges":"<?php echo $dyForwardingCharges; ?>",
            "orderId":"<?php echo $orderId; ?>",
            "numVersion":"<?php echo $numVersion; ?>",
            "trClientNumber":"<?php echo $trClientNumber; ?>",
            "signature":"<?php echo $signature; ?>",
            "ceCivility":"<?php echo $ceCivility; ?>",
            "ceName":"<?php echo $ceName; ?>",
            "ceFirstName":"<?php echo $ceFirstName; ?>",
            "ceAdress3":"<?php echo $ceAdress3; ?>",
            "ceAdress2":"<?php echo $ceAdress2; ?>",
            "ceZipCode":"<?php echo $ceZipCode; ?>",
            "ceTown":"<?php echo $ceTown; ?>",
            "ceEmail":"<?php echo $ceEmail; ?>",
            "cePhoneNumber":"<?php echo $cePhoneNumber; ?>",
            "trParamPlus":"2019-06-01",
            "ceDeliveryInformation":"<?php echo $commentaire; ?>"
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