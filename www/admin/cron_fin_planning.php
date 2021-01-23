<?php
require_once("inc_connexion.php");

$date           = date("Y-m-d H:i:s");
//$date_fin=date('Y-m-d H:i:s', strtotime('+1 hour'));

$result         = $sql->query("SELECT * FROM livreurs WHERE statut='ON'");
$liste_livreur  = $result->fetchAll(PDO::FETCH_OBJ);

foreach($liste_livreur as $livreur) {
	echo "-----".$livreur->id."-----<br/><br/>";
	//on vérifie le planning en cours
	$result = $sql->query("SELECT * FROM livreurs_planning WHERE ".$sql->quote($date)." BETWEEN date_debut AND date_fin AND id_livreur=".$sql->quote($livreur->id));
    $ligne  = $result->fetch();
    if ($ligne) {
    	echo($ligne["id"]." : ".$ligne["date_debut"]." > ".$ligne["date_fin"]."<br/>");
    	//s'il y a un planning, on vérifie qu'il y a bien des données de connexion lié en base
    	$result2 = $sql->query("SELECT * FROM livreurs_connexion WHERE id_planning=".$sql->quote($ligne["id"]));
	    $ligne2  = $result2->fetch();
	    if ($ligne2) {
	    	//si oui, on ne fait rien
	    	echo($ligne2["id_planning"]." : ".$ligne2["date_connexion"]." > ".$ligne2["date_deconnexion"]."<br/>");
	    }
	    else {
	    	//si non, on cloture le dernier shift et on créer une nouvelle ligne de connexion
	    	$result3    = $sql->query("SELECT * FROM livreurs_connexion c INNER JOIN livreurs_planning p ON c.id_planning=p.id WHERE c.id_livreur=".$sql->quote($livreur->id)." AND c.date_connexion=c.date_deconnexion ORDER BY c.date_connexion DESC LIMIT 1 ");
		    $ligne3     = $result3->fetch();
		    if ($ligne3) {
	    		echo "-->UPDATE livreurs_connexion SET date_deconnexion=".$sql->quote($ligne3["date_fin"])." WHERE id_planning=".$sql->quote($ligne3["id_planning"])."<--<br/>";
	    		$result = $sql->exec("UPDATE livreurs_connexion SET date_deconnexion=".$sql->quote($ligne3["date_fin"])." WHERE id_planning=".$sql->quote($ligne3["id_planning"]));
	    		echo "-->INSERT INTO livreurs_connexion (id_livreur, id_commercant, id_vehicule, id_planning, date_connexion, date_deconnexion, type) VALUES (".$sql->quote($ligne["id_livreur"]).", ".$sql->quote($ligne["id_commercant"]).", ".$sql->quote($ligne["id_vehicule"]).", ".$sql->quote($ligne["id"]).", ".$sql->quote($ligne["date_debut"]).", ".$sql->quote($ligne["date_debut"]).", 'appli') <--<br/>";
	    		$result = $sql->exec("INSERT INTO livreurs_connexion (id_livreur, id_commercant, id_vehicule, id_planning, date_connexion, date_deconnexion, type) VALUES (".$sql->quote($ligne["id_livreur"]).", ".$sql->quote($ligne["id_commercant"]).", ".$sql->quote($ligne["id_vehicule"]).", ".$sql->quote($ligne["id"]).", ".$sql->quote($ligne["date_debut"]).", ".$sql->quote($ligne["date_debut"]).", 'appli')");
	    	}
	    }
    }
}
?>