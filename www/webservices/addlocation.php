<?php
    date_default_timezone_set('Europe/Paris');

	$sql_serveur	= "localhost";
	$sql_user		= "youorder";
	$sql_passwd		= "75LrhfPSOqCv";
	$sql_bdd		= "youorder";
	$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
    
    $latitude       = isset($_GET['latitude']) ? $_GET['latitude'] : '0';
    $latitude       = (float)str_replace(",", ".", $latitude); // to handle European locale decimals
    $longitude      = isset($_GET['longitude']) ? $_GET['longitude'] : '0';
    $longitude      = (float)str_replace(",", ".", $longitude);
    $utilisateur    = isset($_GET['utilisateur']) ? $_GET['utilisateur'] : '0';

    $resulta = $sql->exec("UPDATE livreurs SET latitude ='".$latitude."', longitude ='".$longitude."', last_update=NOW() WHERE id = '".$utilisateur."'" );

if (!$resulta) {
   echo"-1";
   $insert_etat="KO";

}
else {
	echo"1";
    $insert_etat="OK";
}

//Something to write to txt log
$log  = "UPDATE: ".$_SERVER['REMOTE_ADDR'].' - '.date("d/m/Y H:i:s").PHP_EOL.
"Utilisateur: ".$utilisateur.PHP_EOL.
"Longitude: ".$longitude.PHP_EOL.
"Latitude: ".$latitude.PHP_EOL.
"Insertion BDD: ".$insert_etat.PHP_EOL.
"-------------------------".PHP_EOL;

//Save string to log, use FILE_APPEND to append.
file_put_contents('./log_geoloc'.date("dmY").'.txt', $log, FILE_APPEND);
?>
