<?php
    
   require_once("inc_connexion.php");

   
    
    $latitude       = isset($_GET['latitude']) ? $_GET['latitude'] : '0';
    $latitude       = (float)str_replace(",", ".", $latitude); // to handle European locale decimals
    $longitude      = isset($_GET['longitude']) ? $_GET['longitude'] : '0';
    $longitude      = (float)str_replace(",", ".", $longitude);
    $utilisateur    = isset($_GET['utilisateur']) ? $_GET['utilisateur'] : '0';

    $resulta = $sql->exec("UPDATE utilisateurs SET latitude ='".$latitude."', longitude ='".$longitude."', appli='ON', lastUpdate=NOW() WHERE id = '".$utilisateur."'" );
if (!$resulta) {
   echo"-1";
}
else {
	echo"1";
}
?>
