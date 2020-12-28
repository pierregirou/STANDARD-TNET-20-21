<?php
	function sendEmail($from, $to, $reply_to, $title, $message_html, $cc="", $bcc="", $attachs=NULL){   
		$__ = '-----='.md5(uniqid(mt_rand())); 
		
		$headers ='From: '.$from."\n"; 
		$headers.='Cc: '.$cc."\n";
		$headers.='Bcc: '.$bcc."\n";
		$headers.='Reply-To: '.$reply_to."\n"; 
		$headers.='X-Mailer: PHP/'.phpversion()."\n";
		$headers.='MIME-Version: 1.0'."\n";
	 	$headers.='Content-Type: multipart/mixed; boundary="'.$__.'"'."\n";		
		$message ="";
		$message.="--".$__."\n";
		$message.='Content-Type: text/html; charset="utf-8"'."\n";
	 	$message.='Content-Transfer-Encoding: 8bit'."\n\n";
	 	$message.=$message_html."\n\n"; 
		if($attachs!=NULL){
			$l=count($attachs);		
			for($i=0;$i<$l;$i++){
				$att=$attachs[$i];			
				$message.= "--".$__."\n";
				$message.= 'Content-Type: '.$att['content-type'].'; name="'.$att['name'].'"'."\n";
				$message.= 'Content-Transfer-Encoding: base64'."\n";
				$message.= 'Content-Disposition:attachement; filename="'.$att['name'].'"'."\n\n";
				$message.= chunk_split(base64_encode(file_get_contents($att['full_path'])))."\n";
			}			
			$message.= "--".$__."\n";	
		}
		return @mail($to, $title, $message, $headers);
	}
	
	$expediteur = "contact@inwitex.eu";
	$destinataires = "c.davulcu@asti-net.eu";
	$titre = "Des commandes à approuver pour ELIVIE/ASDIA";
	$message = "
		Bonjour,<br><br>
		
		Vous avez des nouvelles commandes à approuver. <br>
		Pour accéder à votre espace, veuillez cliquer sur le lien suivant : <br>
		<a href=\"http://inwitex3.eu/texasnet\">Accéder à votre espace</a><br><br>
		
		<i>Munissez vous de vos identifiants</i>
	";
	
	sendEmail($expediteur, $destinataire, "", $titre, $message,"","","");
?>