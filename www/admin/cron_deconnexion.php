<?php
require_once("inc_connexion.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/PHPMailer/class.phpmailer.php');

$date=date("Y-m-d H:i:s");
//$date="2016-12-15 00:01:00";

//tourne tous les jours a 0h00
echo "SELECT * FROM livreurs WHERE statut='ON'<br/>";
$result         = $sql->query("SELECT * FROM livreurs WHERE statut='ON'");
$liste_livreur  = $result->fetchAll(PDO::FETCH_OBJ);

foreach($liste_livreur as $livreur) {
	echo "-----".$livreur->id."-----<br/><br/>";
	//on récupère les infos du dernier planning en date
	$result = $sql->query("SELECT c.*, p.date_debut, p.date_fin, l.nom as nom_livreur, l.prenom as prenom_livreur, r.nom as nom_resto FROM livreurs_connexion c INNER JOIN livreurs_planning p ON c.id_planning=p.id INNER JOIN livreurs l ON c.id_livreur=l.id INNER JOIN restaurants r ON c.id_commercant=r.id WHERE c.id_livreur=".$sql->quote($livreur->id)." AND c.date_connexion=c.date_deconnexion ORDER BY c.date_connexion DESC LIMIT 1 ");
    $ligne  = $result->fetch();
    if ($ligne) {
    	echo $livreur->id." > ".$ligne["date_connexion"]." - ".$ligne["date_deconnexion"]."<br/>";
    	echo "--> ".$ligne["date_debut"]." - ".$ligne["date_fin"];

    	$date_fin_planning  = $ligne["date_fin"];
        $nom_livreur        = $ligne["prenom_livreur"]." ".$ligne["nom_livreur"];
        $nom_resto          = $ligne["nom_resto"];
        $id_livreur         = $livreur->id;

    	//on mets a jour a date de deconnexion avec la date de fin théorique
    	echo "<br/>UPDATE livreurs_connexion SET date_deconnexion=".$sql->quote($ligne["date_fin"])." WHERE id=".$sql->quote($ligne["id"]);
    	$result = $sql->exec("UPDATE livreurs_connexion SET date_deconnexion=".$sql->quote($ligne["date_fin"])." WHERE id=".$sql->quote($ligne["id"]));

    	//on deconnecte le livreur
    	echo "<br/>UPDATE livreurs SET statut='OFF' WHERE id=".$ligne["id_livreur"];
    	echo "<br/><br/>";
    	$result = $sql->exec("UPDATE livreurs SET statut='OFF' WHERE id=".$ligne["id_livreur"]);

    	//on envoie un email récapitulatif des heures sup du livreur
    	$tps_retard         = (strtotime($date)-strtotime($date_fin_planning));
        $duree_h_retard     = gmdate("H",$tps_retard);
        $duree_m_retard     = gmdate("i",$tps_retard);
        $duree_aff_retard   = ($duree_h_retard>0) ? $duree_h_retard."h".$duree_m_retard : $duree_m_retard." min";

        //on envoie un email pour prévenir des heures supplémentaires
        $body = 'Bonjour, <br/><br/>
                Un livreur a fait des heures supplémentaires : <br/>
                - Nom du livreur : '.$nom_livreur.'<br/>
                - Nom du commercant : '.$nom_resto.'<br/>
                - Date de fin prévue : '.date("d/m/Y - H:i:s", strtotime($date_fin_planning)).'<br/>
                - Date de deconnexion : '.date("d/m/Y - H:i:s", strtotime($date)).' (soit '.$duree_aff_retard.' supplémentaires)<br/>
                --> <a href="https://www.you-order.eu/admin/livreurs_fiche2.php?id='.$id_livreur.'">Lien vers la fiche du livreur</a><br/>';

        $mail = new PHPMailer();
        $mail->From = "contact@youorder.fr";
        $mail->Sender = "contact@youorder.fr";
        $mail->FromName = "YouOrder";
        $mail->Subject = "Heures supplémentaires";
        $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
        $mail->MsgHTML($body);
        $mail->CharSet = 'UTF-8';    
        $mail->AddReplyTo("contact@youorder.fr","YouOrder");
        $mail->AddAddress("ops@youorder.fr", "");
        $mail->AddAddress("myriam@mgmobile.fr", "");
        $mail->send();
    }
}
?>