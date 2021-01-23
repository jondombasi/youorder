<?php
require 'autoloader.php'; 
Autoloader::register(); 

session_cache_expire(180);
ini_set('session.gc_maxlifetime', 10800);
session_start();

if($_SESSION["acces"]){
	$_SESSION["acces"] = true;
}

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "fr_FR");

$sql_serveur	= "localhost";
$sql_user		= "root";
$sql_passwd		= "root";
$sql_bdd		= "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

if($_SESSION["userid"]!=""){
	$result = $sql->exec("UPDATE utilisateurs SET statut = 'ON', date_conn = NOW() WHERE id = '".$_SESSION["userid"]."'");		
}

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
function couleur_statut($statut){
	switch($statut){
		case "ajouté":	
			return "label-warning";
			break;
		case "réservé":	
			return "label-info ";
			break;
		case "récupéré":	
			return "label-default ";
			break;
		case "signé":	
			return "label-success";
			break;
		case "echec":	
			return "label-danger";
			break;
		default:
			return $statut;
			break;
	}
}

function txt_statut($statut){
	switch($statut){
		case "ajouté":	
			return "ajoutée";
			break;
		case "réservé":	
			return "réservée";
			break;
		case "récupéré":	
			return "récupérée";
			break;
		case "signé":	
			return "signée";
			break;
		case "echec":	
			return "echec";
			break;
		default:
			return $statut;
			break;
	}
}

function txt_raison_refus($raison,$comm,$ts_date_statut){
	switch($raison){
		case "1":
			return "Adresse inexistante - ".date("d/m H\hi",$ts_date_statut);
			break;
		case "2":
			return "Ne répond pas - ".date("d/m H\hi",$ts_date_statut);
			break;
		case "3":
			return "Refuse la commande - ".date("d/m H\hi",$ts_date_statut);
			break;
		case "4":
			return $comm." - ".date("d/m H\hi",$ts_date_statut);
			break;
		default:
			return "";
			break;
	}
}

function getDistance($adresse1,$adresse2) {
	/*$url='https://maps.google.com/maps/api/directions/xml?language=fr&origin='.urlencode($adresse1).'&destination='.urlencode($adresse2).'&sensor=false';
	//&avoid=highways
	//https://developers.google.com/maps/documentation/directions/
	//echo $url."<br/>";
	$xml=file_get_contents($url);
	//echo($xml);
	$root = simplexml_load_string($xml);
	$distance=$root->route->leg->distance->value;
	$duree=$root->route->leg->duration->value; 
	$etapes=$root->route->leg->step;
	return array(
	   'distanceEnMetres'=>$distance,
	   'dureeEnSecondes'=>$duree
	   //'etapes'=>$etapes,
	   //'adresseDepart'=>$root->route->leg->start_address,
	   //'adresseArrivee'=>$root->route->leg->end_address
	);*/

	$url = 'https://maps.google.com/maps/api/directions/xml?language=fr&origin='.urlencode($adresse1).'&destination='.urlencode($adresse2).'&sensor=false';
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$xml = curl_exec($ch);
	$root = simplexml_load_string($xml);
	$distance=$root->route->leg->distance->value;
	$duree=$root->route->leg->duration->value; 

	return array(
	   'distanceEnMetres'=>$distance,
	   'dureeEnSecondes'=>$duree
	);

	/*if (curl_errno($ch)) {
		echo curl_error($ch);
		echo "\n<br />";
		$contents = '';
	} 
	else {
	  curl_close($ch);
	}

	if (!is_string($contents) || !strlen($contents)) {
		return "Failed to get contents.";
		$contents = '';
	}*/

	//return $contents;

}

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
		$date_debut = date("d/m/Y",strtotime($date_debut_bdd));
		$heure_debut = date("H\hi",strtotime($date_debut_bdd));
		$date_fin_bdd = $ligne2["date_fin"];
		$heure_fin = date("H\hi",strtotime($date_fin_bdd));
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
		$result2 = $sql->query("SELECT * FROM utilisateurs WHERE role = 'admin' ORDER BY date_conn DESC");
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

function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
    /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
        (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
    */
    
    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto = strtotime($dateto, 0);
    }
    $difference = $dateto - $datefrom; // Difference in seconds
     
    switch($interval) {
     
    case 'yyyy': // Number of full years

        $years_difference = floor($difference / 31536000);
        if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
            $years_difference--;
        }
        if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
            $years_difference++;
        }
        $datediff = $years_difference;
        break;

    case "q": // Number of full quarters

        $quarters_difference = floor($difference / 8035200);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $quarters_difference--;
        $datediff = $quarters_difference;
        break;

    case "m": // Number of full months

        $months_difference = floor($difference / 2678400);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $months_difference--;
        $datediff = $months_difference;
        break;

    case 'y': // Difference between day numbers

        $datediff = date("z", $dateto) - date("z", $datefrom);
        break;

    case "d": // Number of full days

        $datediff = floor($difference / 86400);
        break;

    case "w": // Number of full weekdays

        $days_difference = floor($difference / 86400);
        $weeks_difference = floor($days_difference / 7); // Complete weeks
        $first_day = date("w", $datefrom);
        $days_remainder = floor($days_difference % 7);
        $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
        if ($odd_days > 7) { // Sunday
            $days_remainder--;
        }
        if ($odd_days > 6) { // Saturday
            $days_remainder--;
        }
        $datediff = ($weeks_difference * 5) + $days_remainder;
        break;

    case "ww": // Number of full weeks

        $datediff = floor($difference / 604800);
        break;

    case "h": // Number of full hours

        $datediff = floor($difference / 3600);
        break;

    case "n": // Number of full minutes

        $datediff = floor($difference / 60);
        break;

    default: // Number of full seconds (default)

        $datediff = $difference;
        break;
    }    

    return $datediff;

}

function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, '-');
    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function format_date($date) {
	$start_date = new DateTime();
	$since_start = $start_date->diff(new DateTime($date));

	if ($since_start->d==1) {
		//return "Hier";
		if (date("Y-m-d",strtotime($date)) == date('Y-m-d', strtotime('yesterday'))) {
			return "Hier";
		}
		else {
			return ($since_start->d+1)."j";
		}
	}
	else if ($since_start->d>1){
		return $since_start->d."j";
	}
	else if ($since_start->h>0) {
		return $since_start->h."h";
	}
	else if ($since_start->i>0) {
		return $since_start->i.'mn';
	}
	else {
		return $since_start->s.'s';
	}
}

function format_week($strtotime_debut, $strtotime_fin) {
	if (strftime("%B", $strtotime_debut)==strftime("%B", $strtotime_fin)) {
		return strftime("%d", $strtotime_debut)." au ".strftime("%d %B", $strtotime_fin);
	}
	else {
		return strftime("%d %B", $strtotime_debut)." au ".strftime("%d %B", $strtotime_fin);
	}
}

function notif_copie($id_livreur, $texte, $lien, $device_id, $sql) {
	$result = $sql->exec("INSERT INTO notifications_push_copies (id_livreur, texte, lien, device_id, date) VALUES ('".$id_livreur."', '".$texte."', '".$lien."', '".$device_id."', NOW())");
}