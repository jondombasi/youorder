<?php
require_once("inc_connexion.php");

function send_notif($commandeid,$sql){

	$result2 = $sql->query("SELECT c.id,r.nom, c.date_debut, c.date_fin, c.statut, c.date_statut, l.adresse, u.nom as u_nom, u.prenom as u_prenom 
							FROM commandes c INNER JOIN 
									restaurants r ON c.restaurant = r.id INNER JOIN 
									clients l ON c.client = l.id INNER JOIN
									utilisateurs u ON c.livreur = u.id
							WHERE c.id = ".$sql->quote($commandeid)." LIMIT 1");
	$ligne2 = $result2->fetch();
	if($ligne2!=""){
		$id = $ligne2["id"];
		$nom_resto = $ligne2["nom"];

		$date_debut_bdd = $ligne2["date_debut"];
		$date_debut = date("d-m-Y",strtotime($date_debut_bdd));
		$heure_debut = date("H:i",strtotime($date_debut_bdd));
		$date_fin_bdd = $ligne2["date_fin"];
		$heure_fin = date("H:i",strtotime($date_fin_bdd));
		$statut= $ligne2["statut"];
		$adresse_livraison = $ligne2["adresse"];
		$u_nom = $ligne2["u_nom"];
		$u_prenom = $ligne2["u_prenom"];
		$date_statut_bdd = $ligne2["date_statut"];
	}	

	switch($statut){
		case "réservé":	
			$objet = "Commande réservée";
			$corps = 'Bonjour, <br/><br/>
					La commande du commerçant <b>'.$nom_resto.'</b> est réservée par '.$u_prenom.' '.$u_nom.'<br/>
					Détails : <br/>
					 - Adresse de livraison : '.$adresse_livraison.'<br/>
					 - Créneau de livraison : '.$date_debut.' '.$heure_debut.' / '.$heure_fin.'<br/><br/>
					Merci,<br/>
					L\'équipe YouOrder';
			break;
		case "récupéré":	
			$objet = "Commande récupérée";
			$corps = 'Bonjour, <br/><br/>
					La commande du commerçant <b>'.$nom_resto.'</b> est récupérée par '.$u_prenom.' '.$u_nom.'<br/>
					Détails : <br/>
					 - Adresse de livraison : '.$adresse_livraison.'<br/>
					 - Créneau de livraison : '.$date_debut.' '.$heure_debut.' / '.$heure_fin.'<br/><br/>
					Merci,<br/>
					L\'équipe YouOrder';
			break;
		case "signé":	
			$objet = "Commande livrée";
			$corps = 'Bonjour, <br/><br/>
					La commande du commerçant <b>'.$nom_resto.'</b> est signée<br/>
					<a href="http://youorder.fr/admin/commandes_visu.php?id='.$id.'">Voir la signature</a><br/><br/>
					Merci,<br/>
					L\'équipe YouOrder';
			break;
		case "echec":	
			$objet = "Commande en échec";
			$corps = 'Bonjour, <br/><br/>
					La commande du commerçant <b>'.$nom_resto.'</b> est en echec<br/>
					<a href="http://youorder.fr/admin/commandes_visu.php?id='.$id.'">Voir la raison</a><br/><br/>
					Merci,<br/>
					L\'équipe YouOrder';
			break;
		default:
			$objet = "";
			$corps = "";
			break;
	}

	if($corps!=""){
		$result2 = $sql->query("SELECT * FROM utilisateurs WHERE role = 'admin' and id in (12,16) ORDER BY date_conn DESC");
		while($ligne2 = $result2->fetch()) {
			$email = $ligne2["email"];
		   // On créé une nouvelle instance de la classe
		   require_once('PHPMailer/class.phpmailer.php');
		   $mail = new PHPMailer();
		   $mail->From = "contact@youorder.fr";
		   $mail->Sender = "contact@youorder.fr";
		   $mail->FromName = "YouOrder";
		   $mail->Subject = $objet;
		   $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
		   $mail->MsgHTML($corps);
		   $mail->CharSet = 'UTF-8';	
		   $mail->AddReplyTo("contact@youorder.fr","YouOrder");
		   $mail->AddAddress($email, "");
		   //$mail->AddBCC("contact@mgmobile.fr","");
		   $mail->send();
		}
	}
}

$res = send_notif(36,$sql);
?>