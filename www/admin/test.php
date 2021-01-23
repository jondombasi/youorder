<?php 
require_once("inc_connexion.php");
error_reporting(E_ALL); 
ini_set('display_errors', '1');

 // URL for sending request
$postUrl = "https://api.infobip.com/sms/1/text/advanced";

$messageId="";
$to="33620261736";
$from="You Order";
$text="Test SMS";


// creating an object for sending SMS
$destination = array("to" => $to);
$message = array("from" => $from,
        "destinations" => array($destination),
        "text" => $text);
$postData = array("messages" => array($message));
// encoding object
$postDataJson = json_encode($postData);

$ch = curl_init();
$header = array("Content-Type:application/json", "Accept:application/json");

curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "MGMOBILE2:M202714e");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);

// response of the POST request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$responseBody = json_decode($response);
curl_close($ch);

echo $response;

/*$result = $sql->query("SELECT * FROM utilisateurs WHERE role='livreur' OR role='inactif'");
$listeLivreur = $result->fetchAll(PDO::FETCH_OBJ);

foreach ($listeLivreur as $livreur) {
	if ($livreur->date_conn=="0000-00-00 00:00:00") {
		$date_conn='1970-01-01 00:00:00';
	}
	else {
		$date_conn=$livreur->date_conn;
	}
	if ($livreur->lastUpdate=="0000-00-00 00:00:00") {
		$lastUpdate='1970-01-01 00:00:00';
	}
	else {
		$lastUpdate=$livreur->lastUpdate;
	}
	if ($livreur->role=="inactif") {
		$statut='supprime';
	}
	else {
		$statut=$livreur->statut;
	}

	//$sql->exec("INSERT INTO livreurs (id, nom, prenom, email, password, telephone, statut, date_connexion, latitude, longitude, last_update) VALUES ('".$livreur->id."', '".$livreur->nom."', '".$livreur->prenom."', '".$livreur->email."', '".$livreur->password."', '".$livreur->numero."', '".$statut."', '".$date_conn."', '".$livreur->longitude."', '".$livreur->latitude."', '".$lastUpdate."')");
}*/
?>