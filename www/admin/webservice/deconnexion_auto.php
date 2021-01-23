<?php
require_once("inc_connexion.php");

$datemin_ts = time()-(15*60);
$datemin = date("Y-m-d H:i:s",$datemin_ts);
//echo $datemin."<br>";
$result = $sql->query("SELECT * FROM utilisateurs WHERE appli='ON' and lastUpdate<='".$datemin."' "); //
while($ligne = $result->fetch()) {
	echo $ligne["id"].'-'.$ligne["lastUpdate"]."<br>";
		$resultd = $sql->exec("UPDATE utilisateurs SET  appli='OFF' WHERE id = '".$ligne["id"]."'" );
	
}

$result = $sql->query("SELECT * FROM utilisateurs WHERE statut='ON' and date_conn<='".$datemin."' "); //
while($ligne = $result->fetch()) {
	echo $ligne["id"].'-'.$ligne["date_conn"]."<br>";
		$resultd = $sql->exec("UPDATE utilisateurs SET  statut='OFF' WHERE id = '".$ligne["id"]."'" );
	
}


//mail("mhaddad@mgmobile.fr","YO - Deco Auto","Deconnexion automatique");
?>
	
