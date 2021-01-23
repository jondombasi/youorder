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

$l_nom = $_POST["l_nom"];
$l_prenom = $_POST["l_prenom"];
$l_tel = $_POST["l_tel"];
$l_mail = $_POST["l_mail"];
$l_message = $_POST["l_message"];
$type_permis = $_POST["type_permis"];

/*
echo "POST : <br/>";
var_dump($_POST);
echo "<br/>";
echo "FILE : <br/>";
print_r($_FILES);
*/
//exit();

		$texte = "Voici les infos envoyés depuis le formulaire 'Devenir Livreur', "."<br/><br/>";
		$texte .= "<strong>Nom : </strong>".$l_nom."<br/>";
		$texte .= "<strong>Prenom : </strong>".$l_prenom."<br/>";
		$texte .= "<strong>Email : </strong>".$l_mail."<br/>";
		$texte .= "<strong>Tel : </strong>".$l_tel."<br/>";
		$texte .= "<strong>Type de permis : </strong>".$type_permis."<br/>";
		$texte .= "<strong>Message : </strong>".$l_message."<br/>";

		 $to      = "rh@youorder.fr";
//		 $to      = "contact@mgmobile.fr";
		 $subject = 'YOU ORDER - Devenir livreur';
	
		 // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
		 $headers  = 'MIME-Version: 1.0' . "\r\n";
		 $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		 $headers .= 'From: Site internet <contact@mgmobile.fr>' . "\r\n";
		 $headers .= 'Reply-to: '.$l_nom.' <'.$l_mail.'>' . "\r\n";
	
//		 $statut =  mail($to, $subject, $texte, $headers);

   require_once('PHPMailer/class.phpmailer.php');
 
   $mail = new PHPMailer();
 
   $mail->From = "contact@you-order.eu";
   $mail->Sender = "contact@you-order.eu";
   $mail->FromName = "Contact You-Order";
   $mail->Subject = utf8_decode($subject);
   $mail->MessageID = newChaine(6).".".newChaine(6)."@you-order.eu";	
   $mail->MsgHTML(utf8_decode($texte));
   
if (isset($_FILES['fichier']) &&
    $_FILES['fichier']['error'] == UPLOAD_ERR_OK) {
    $mail->AddAttachment($_FILES['fichier']['tmp_name'],
                         $_FILES['fichier']['name']);
}

//   $mail->AddReplyTo("support@eco-voiturage.fr","Eco-voiturage");
   $mail->AddAddress($to, "");
//   $mail->AddAddress("contact@mgmobile.fr", "");
	if($to!=""){

		if(!$mail->Send())
		{
		 //  echo "Error sending: " . $mail->ErrorInfo;
		}
		else
		{
		 //  echo "E-mail sent";
		}
		//$mail->send();
		//echo "OK";
   }


echo '1';

?>