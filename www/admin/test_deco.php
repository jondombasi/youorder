<?php
require_once("inc_connexion.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/PHPMailer/class.phpmailer.php');

$id_livreur="232";
$id_planning="3327";
$date_test="2016-12-14 13:30:00";

echo "SELECT c.*, p.date_debut, p.date_fin, l.nom as nom_livreur, l.prenom as prenom_livreur, r.nom as nom_resto FROM livreurs_connexion c INNER JOIN livreurs_planning p ON c.id_planning=p.id INNER JOIN livreurs l ON c.id_livreur=l.id INNER JOIN restaurants r ON c.id_commercant=r.id WHERE (c.id_planning=".$sql->quote($id_planning).") OR (c.id_livreur=".$sql->quote($id_livreur)." AND c.date_connexion=c.date_deconnexion) ORDER BY c.date_connexion DESC LIMIT 1 <br/>";
$result = $sql->query("SELECT c.*, p.date_debut, p.date_fin, l.nom as nom_livreur, l.prenom as prenom_livreur, r.nom as nom_resto FROM livreurs_connexion c INNER JOIN livreurs_planning p ON c.id_planning=p.id INNER JOIN livreurs l ON c.id_livreur=l.id INNER JOIN restaurants r ON c.id_commercant=r.id WHERE (c.id_planning=".$sql->quote($id_planning).") OR (c.id_livreur=".$sql->quote($id_livreur)." AND c.date_connexion=c.date_deconnexion) ORDER BY c.date_connexion DESC LIMIT 1 ");
$ligne = $result->fetch();
if ($ligne) {
    $id_connexion=$ligne["id"];
    $id_planning=$ligne["id_planning"];
    $date_fin_planning=$ligne["date_fin"];
    $nom_livreur=$ligne["prenom_livreur"]." ".$ligne["nom_livreur"];
    $nom_resto=$ligne["nom_resto"];
}

//on vérifie si le livreur a fais des heures supplémentaire
if ($date_fin_planning<$date_test) {
    //si oui, on met a jour avec la date de fin théorique
    //$result = $sql->exec("UPDATE livreurs_connexion SET date_deconnexion=".$sql->quote($date_fin_planning)." WHERE id=".$sql->quote($id_connexion));
    echo "Heures SUP<br/>";

    $tps_retard=(strtotime($date_test)-strtotime($date_fin_planning));
    $duree_h_retard = gmdate("H",$tps_retard);
    $duree_m_retard = gmdate("i",$tps_retard);
    $duree_aff_retard=($duree_h_retard>0) ? $duree_h_retard."h".$duree_m_retard : $duree_m_retard." min";

    //on envoie un email pour prévenir des heures supplémentaires
    $body = 'Bonjour, <br/><br/>
            Un livreur a fait des heures supplémentaires : <br/>
            - Nom du livreur : '.$nom_livreur.'<br/>
            - Nom du commercant : '.$nom_resto.'<br/>
            - Date de fin prévue : '.date("d/m/Y - H:i:s", strtotime($date_fin_planning)).'<br/>
            - Date de deconnexion : '.date("d/m/Y - H:i:s").'(soit '.$duree_aff_retard.' supplémentaires)<br/>
            --> <a href="https://www.you-order.eu/admin/livreurs_fiche2.php?id='.$id_livreur.'">Lien vers la fiche du livreur</a><br/>';

    $mail = new PHPMailer();
    $mail->From = "contact@youorder.fr";
    $mail->Sender = "contact@youorder.fr";
    $mail->FromName = "YouOrder";
    $mail->Subject = "youOrder > heures supplémentaires";
    $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
    $mail->MsgHTML($body);
    $mail->CharSet = 'UTF-8';    
    $mail->AddReplyTo("contact@youorder.fr","YouOrder");
    $mail->AddAddress("myriam@mgmobile.fr", "");
    $mail->AddAddress("myriam.malnoe@gmail.com", "");
    //$mail->AddBCC("myriam.malnoe@gmail.com","");
    $mail->send();
}
else {
    echo "Pas d'heures sup<br/>";
    //si non, on met a jour avec la date actuelle
    //$result = $sql->exec("UPDATE livreurs_connexion SET date_deconnexion=NOW() WHERE id=".$sql->quote($id_connexion));
}       

//$result = $sql->exec("UPDATE livreurs SET statut='OFF' WHERE id = ".$sql->quote($id_livreur));
?>