<?php
function newChaine( $chrs = "") {
	if( $chrs == "" ) $chrs = 4;
	$chaine = ""; 

	$list = "23456789abcdefghjkmnpqrstuvwxyz";
	mt_srand((double)microtime()*1000000);
	$newstring="";

	while( strlen( $newstring )< $chrs ) {
		$newstring .= $list[mt_rand(0, strlen($list)-1)];
	}

	return $newstring;
}

$type = $_POST["type"];
$c_nom = $_POST["c_nom"];
$c_prenom = $_POST["c_prenom"];
$c_sujet = $_POST["c_sujet"];
$c_mail = $_POST["c_mail"];
$c_message = $_POST["c_message"];

if($type=="1"){
	$c_type = "Opérations";
	$to = "ops@youorder.fr";
}else{
	$c_type = "Infos";
	$to = "contact@youorder.fr";
}


		$texte = "Voici les infos envoyés depuis le formulaire Contact du site, "."<br/><br/>";
		$texte .= "<strong>Nom : </strong>".$c_nom."<br/>";
		$texte .= "<strong>Prenom : </strong>".$c_prenom."<br/>";
		$texte .= "<strong>Email : </strong>".$c_mail."<br/>";
		$texte .= "<strong>Type : </strong>".$c_type."<br/>";
		$texte .= "<strong>Sujet : </strong>".$c_sujet."<br/>";
		$texte .= "<strong>Message : </strong>".$c_message."<br/>";

		 $subject = 'YOU ORDER - Contact site internet';
	
		 // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
		 $headers  = 'MIME-Version: 1.0' . "\r\n";
		 $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		 $headers .= 'From: Site internet <contact@mgmobile.fr>' . "\r\n";
		 $headers .= 'Reply-to: '.$c_nom.' <'.$c_mail.'>' . "\r\n";
	
//		 $statut =  mail($to, $subject, $texte, $headers);

   require_once('PHPMailer/class.phpmailer.php');
 
   $mail = new PHPMailer();
 
   $mail->From = "contact@you-order.eu";
   $mail->Sender = "contact@you-order.eu";
   $mail->FromName = "Contact You-Order";
   $mail->Subject = utf8_decode($subject);
   $mail->MessageID = newChaine(6).".".newChaine(6)."@you-order.eu";	
   $mail->MsgHTML(utf8_decode($texte));
   
//   $mail->AddReplyTo("support@eco-voiturage.fr","Eco-voiturage");
   $mail->AddAddress($to, "OPS youOrder");
   if($type=="1"){
   //	$mail->AddAddress("contact@mgmobile.fr", "MG Mobile");
   	$mail->AddAddress("joel.ndombasi@youorder.fr", "Joel Ndombasi");
   	$mail->AddAddress("augustin.doumbe@youorder.fr", "Augustin Doumbe");
   }
//   $mail->AddAddress("contact@mgmobile.fr", "");
	if($to!=""){

		if(!$mail->Send())
		{
//		   echo "Error sending: " . $mail->ErrorInfo;
		}
		else
		{
//		   echo "E-mail sent";
		}
		//$mail->send();
		//echo "OK";
   }


echo '1';

?>