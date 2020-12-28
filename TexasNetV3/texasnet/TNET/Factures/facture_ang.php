<?php 
    ob_start();
    include('facturehtml_ang.php');
    $content = ob_get_clean();
	$view=$_GET['view'];
	if(isset($_GET['numcmd'])){
		$num=$_GET['numcmd'];
	}
    require_once(dirname(__FILE__).'/factures/html2pdf.class.php');    
    try{
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');        
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		if($view=="P"){  
			$date=date("Y-m-d_H-i",time());
			$html2pdf->Output('Facture_'.$num."_".$date.'.pdf','P'); 
		} else {        			$html2pdf->Output('./Facture_'.$num.'.pdf','F');
		}    }catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>