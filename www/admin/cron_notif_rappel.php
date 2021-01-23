<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 12/09/2017
 * Time: 12:45
 */

require_once("inc_connexion.php");

$date_debut = date("Y-m-d H:i:s");
$date_fin   = date('Y-m-d H:i:s', strtotime('+5 minutes'));

$result         = $sql->query("SELECT p.*, l.device_id FROM `livreurs_planning` p LEFT JOIN livreurs l ON p.id_livreur=l.id WHERE date_debut BETWEEN ".$sql->quote($date_debut)." AND ".$sql->quote($date_fin)." AND notif='non'");
$liste_notif    = $result->fetchAll(PDO::FETCH_OBJ);
foreach($liste_notif as $notif) {
    if ($notif->device_id!="" && $notif->device_id!=null) {
        $url_notif  = "http://www.you-order.eu/admin/android.php?registration_id=".$notif->device_id."&title=youOrder&message=".urlencode("Rappel : pensez à vous mettre en ligne avant le début de votre shift. Appuyer sur APPARAITRE EN SERVICE lorsque vous êtes chez le commerçant")."&urlappli=".urlencode("planning.html");
        $envoi_tab  = cfile($url_notif);
        $result     = $sql->exec("UPDATE livreurs_planning SET notif='oui' WHERE id=".$sql->quote($notif->id));
        $result     = $sql->exec("INSERT INTO notifications_push_copies (id_livreur, texte, lien, device_id, date) VALUES ('".$notif->id_livreur."', 'Rappel : pensez à vous mettre en ligne avant le début de votre shift. Appuyer sur APPARAITRE EN SERVICE lorsque vous êtes chez le commerçant', 'planning.html', '".$notif->device_id."', NOW())");
    }
}