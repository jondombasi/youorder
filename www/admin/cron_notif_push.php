<?php
require_once("inc_connexion.php");

$result = $sql->query("SELECT * FROM notifications_push WHERE statut='pending' AND date_envoi<=NOW()");
$liste_notif = $result->fetchAll(PDO::FETCH_OBJ);
foreach($liste_notif as $notif) {
	$message=$notif->message;
	$url="notifications.html";
	if ($notif->destinataire=="tous") {
		$Livreur=new Livreur($sql);
		$livreurs=$Livreur->getAll("", "", "", "", "");
		foreach ($livreurs as $livreur) {
			echo "device id=".$livreur->device_id."<br/>";
	        $envoi=file('http://www.you-order.eu/admin/action_poo.php?action=send_push&id='.$livreur->id.'&message='.urlencode($message).'&url='.urlencode($url));
		}
	}
	else {
		$test=explode(',', $notif->destinataire);
		foreach($test as $bar) {
			if ($bar!="") {
				$Livreur=new Livreur($sql, $bar);
				echo "device id=".$Livreur->getDeviceId()."<br/>";
	            $envoi=file('http://www.you-order.eu/admin/action_poo.php?action=send_push&id='.$bar.'&message='.urlencode($message).'&url='.urlencode($url));
			}
		}	
	}
	
	$result = $sql->exec("UPDATE notifications_push SET statut='send' WHERE id=".$sql->quote($notif->id));
}
?>