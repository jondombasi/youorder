<?php
session_cache_expire(30);
session_start();

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

$sql_serveur	= "youorderzawp.mysql.db";
$sql_user		= "youorderzawp";
$sql_passwd		= "Youorder2014";
$sql_bdd		= "youorderzawp";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );


if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
   array_walk_recursive($_GET, 'stripslashes_gpc');
   array_walk_recursive($_POST, 'stripslashes_gpc');
   array_walk_recursive($_COOKIE, 'stripslashes_gpc');
   array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}


function right2($str,$nbr) {
   return substr($str,-$nbr);
}
function left($str,$nbr) {
   return substr($str,0,$nbr);
}
function newChaine( $chrs = "") {
	if( $chrs == "" ) $chrs = 4;
	$chaine = ""; 

	$list = "23456789abcdefghjkmnpqrstuvwxyz";
	mt_srand((double)microtime()*1000000);
	$newstring="";

	while( strlen( $newstring )< $chrs ) {
		$newstring .= $list[mt_rand(0, strlen($list)-1)];
	}
	return $newstring;
}
