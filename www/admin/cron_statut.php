<?php
	
	require_once("inc_connexion.php");
	$date_connexion = date("Y-m-d H:i:s",strtotime("-15 minutes"));
	$date_now = date("Y-m-d H:i:s");
	echo $date_now;

	$result = $sql->query("SELECT * FROM utilisateurs WHERE statut = 'ON' AND (date_conn <= '".$date_connexion."' OR date_conn is null)");
	while($ligne = $result->fetch()) {
		echo $ligne["id"]." : ".$ligne["pseudo"]."<br/>";
		$req = $sql->exec("UPDATE utilisateurs SET statut = 'OFF' WHERE id=".$ligne["id"]);
	}
?>
