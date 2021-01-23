<?php
header('Content-type: text/html; charset=UTF-8');
require_once("../admin/inc_connexion.php");

if(isset($_GET["action"]))	{$action=$_GET["action"];}else{$action="";}	
if(isset($_GET["dev"]))		{$dev=$_GET["dev"];}else{$dev="";}	

if(isset($_GET["email"]))	{$email=$_GET["email"];}else{$email="";}	
if(isset($_GET["key"]))		{$key=$_GET["key"];}else{$key="";}	


$req_resto = "";
$result = $sql->query("SELECT * FROM utilisateurs WHERE email=".$sql->quote($email)." AND dispo_api='on' AND secret_key=".$sql->quote($key)." AND role in ('admin','restaurateur')");
$ligne = $result->fetch();
if($ligne!=""){
	$userid = $ligne["id"];
	$role = $ligne["role"];
	$liste_resto = $ligne["liste_resto"];

	if($role=="restaurateur"){
		if($ligne["liste_resto"]==""){
			$req_resto = " AND r.id IN ('') ";
		}else{
			$req_resto = " AND r.id IN (".$ligne["liste_resto"].") ";
		}
		
	}

}else{
	$action = "erreur_ident";
}
switch($action){
	case "erreur_ident":
    	$tab[$x]["error_code"] = "-1";
    	$tab[$x]["error_message"] = "Identifiants invalides";
		$json = json_encode($tab, JSON_PRETTY_PRINT);    	
		break;
	case "getall":
		$x = 0;
		$vide = true;
        $req = "SELECT c.*, r.nom as nom_resto FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 and r.statut = 1 ".$req_resto;
	    $result = $sql->query($req);
	    while($ligne = $result->fetch()) {
	    	$vide = false;
	    	$tab[$x]["id"] = $ligne["id"];
	    	$tab[$x]["nom"] = $ligne["nom"];
	    	$tab[$x]["prenom"] = $ligne["prenom"];
	    	$tab[$x]["adresse"] = $ligne["adresse"];
	    	$tab[$x]["longitude"] = $ligne["longitude"];
	    	$tab[$x]["latitude"] = $ligne["latitude"];
	    	$tab[$x]["email"] = $ligne["email"];
	    	$tab[$x]["numero"] = $ligne["numero"];
	    	$tab[$x]["commentaire"] = $ligne["commentaire"];
	    	$tab[$x]["restaurant"] = $ligne["restaurant"];
	    	$tab[$x]["date_ajout"] = strtotime($ligne["date_ajout"]);
			$x++;
		}

		if($vide){
	    	$tab[$x]["error_code"] = "-4";
	    	$tab[$x]["error_message"] = "Aucune données";			
		}
		$json = json_encode($tab, JSON_PRETTY_PRINT);
		break;

	case "get":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	

		$x = 0;
		if($id==""){
		    $tab[$x]["error_code"] = "-2";
		    $tab[$x]["error_message"] = "Paramètres manquants";
		}else{
			$req = "SELECT c.*, r.nom as nom_resto FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 and r.statut = 1 AND c.id=".$sql->quote($id)." ".$req_resto;
		    $result = $sql->query($req);
		    $ligne = $result->fetch();
		    if($ligne!=""){
		    	$tab[$x]["id"] = $ligne["id"];
		    	$tab[$x]["nom"] = $ligne["nom"];
		    	$tab[$x]["prenom"] = $ligne["prenom"];
		    	$tab[$x]["adresse"] = $ligne["adresse"];
		    	$tab[$x]["longitude"] = $ligne["longitude"];
		    	$tab[$x]["latitude"] = $ligne["latitude"];
		    	$tab[$x]["email"] = $ligne["email"];
		    	$tab[$x]["numero"] = $ligne["numero"];
		    	$tab[$x]["commentaire"] = $ligne["commentaire"];
		    	$tab[$x]["restaurant"] = $ligne["restaurant"];
		    	$tab[$x]["date_ajout"] = strtotime($ligne["date_ajout"]);
				$x++;
			}else{
		    	$tab[$x]["error_code"] = "-3";
		    	$tab[$x]["error_message"] = "Paramètres invalides";
			}
		}
		$json = json_encode($tab, JSON_PRETTY_PRINT);
		break;

	case "create":
		$nom	 		= $_GET["nom"];
		$prenom	 		= $_GET["prenom"];
		$adresse 		= $_GET["adresse"];
		$longitude		= $_GET["longitude"];
		$latitude		= $_GET["latitude"];
		$numero 		= $_GET["numero"];
		$email_client	= $_GET["email_client"];
		$commentaire	= $_GET["commentaire"];
		$commercant		= $_GET["commercant"];
		
		$continu 	= true;
		$tab_error = "";
		$e = 0;

		if($nom==""){
			$continu = false;
			$tab_error[$e] = "nom";
			$e++;
		}
		if($prenom==""){
			$continu = false;
			$tab_error[$e] = "prenom";
			$e++;
		}
		if($commercant==""){
			$continu = false;
			$tab_error[$e] = "commercant";
			$e++;
		}else{
		    $req = "SELECT r.* FROM restaurants r WHERE r.statut = 1 AND r.id=".$sql->quote($commercant)." ".$req_resto;
		    $result = $sql->query($req);
		    $ligne = $result->fetch();
		    if($ligne==""){
				$continu = false;
				$tab_error[$e] = "commercant";
				$e++;
			}			
		}
		if($adresse==""){
			$continu = false;
			$tab_error[$e] = "adresse";
			$e++;
		}
		if($longitude==0){
			$continu = false;
			$tab_error[$e] = "longitude";
			$e++;
		}
		if($latitude==0){
			$continu = false;
			$tab_error[$e] = "latitude";
			$e++;
		}
		if($numero==""){
			$continu = false;
			$tab_error[$e] = "numero";
			$e++;
		}else{
			$regexp_mail = "/^0[0-9]([-. ]?\d{2}){4}[-. ]?$/";
			if(!preg_match($regexp_mail, $numero)) {
				$continu = false;
				$tab_error[$e] = "numero";
				$e++;
			}			
		}

		if($email_client==""){
		}else{
			$regexp_mail = "/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/";
			if(!preg_match($regexp_mail, $email_client)) {
				$continu = false;
				$tab_error[$e] = "email";
				$e++;
			}			
		}

		if($continu){
			$reqinsert =  "INSERT INTO clients (nom, prenom, adresse, longitude, latitude, numero, email, commentaire, restaurant, statut, date_ajout) VALUES (".$sql->quote($nom).",".$sql->quote($prenom).",".$sql->quote($adresse).",".$sql->quote($longitude).",".$sql->quote($latitude).",".$sql->quote($numero).",".$sql->quote($email_client).",".$sql->quote($commentaire).",".$sql->quote($commercant).",1,NOW())";
			$result = $sql->exec($reqinsert);		
			$id = $sql->lastInsertId(); 

	    	$tab[$x]["error_code"] = "0";
	    	$tab[$x]["message"] = "OK";
	    	$tab[$x]["detail"] = $id;

			$aff_valide = "1";
		}else{
	    	$tab[$x]["error_code"] = "-3";
	    	$tab[$x]["error_message"] = "Paramètres invalides";
	    	$tab[$x]["error_details"] = $tab_error;
		}

		$json = json_encode($tab, JSON_PRETTY_PRINT);
		break;

	case "edit":
		$id 			= $_GET["id"];
		$nom	 		= $_GET["nom"];
		$prenom	 		= $_GET["prenom"];
		$adresse 		= $_GET["adresse"];
		$longitude		= $_GET["longitude"];
		$latitude		= $_GET["latitude"];
		$numero 		= $_GET["numero"];
		$email_client	= $_GET["email_client"];
		$commentaire	= $_GET["commentaire"];
		
		$continu 	= true;
		$tab_error = "";
		$e = 0;

		if($id==""){
			$continu = false;
			$tab_error[$e] = "id";
			$e++;
		}else{
			$req = "SELECT c.*, r.nom as nom_resto FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 and r.statut = 1 AND c.id=".$sql->quote($id)." ".$req_resto;
		    $result = $sql->query($req);
		    $ligne = $result->fetch();
		    if($ligne==""){
				$continu = false;
				$tab_error[$e] = "id invalide";
				$e++;		    	
		    }
		}

		if($nom==""){
			$continu = false;
			$tab_error[$e] = "nom";
			$e++;
		}
		if($prenom==""){
			$continu = false;
			$tab_error[$e] = "prenom";
			$e++;
		}
		if($adresse==""){
			$continu = false;
			$tab_error[$e] = "adresse";
			$e++;
		}
		if($longitude==0){
			$continu = false;
			$tab_error[$e] = "longitude";
			$e++;
		}
		if($latitude==0){
			$continu = false;
			$tab_error[$e] = "latitude";
			$e++;
		}
		if($numero==""){
			$continu = false;
			$tab_error[$e] = "numero";
			$e++;
		}else{
			$regexp_mail = "/^0[0-9]([-. ]?\d{2}){4}[-. ]?$/";
			if(!preg_match($regexp_mail, $numero)) {
				$continu = false;
				$tab_error[$e] = "numero";
				$e++;
			}			
		}

		if($email_client==""){
		}else{
			$regexp_mail = "/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/";
			if(!preg_match($regexp_mail, $email_client)) {
				$continu = false;
				$tab_error[$e] = "email";
				$e++;
			}			
		}

		if($continu){
			$requpdate =  "UPDATE clients SET nom=".$sql->quote($nom).",prenom=".$sql->quote($prenom).",adresse=".$sql->quote($adresse).",longitude=".$sql->quote($longitude).",latitude=".$sql->quote($latitude).",email=".$sql->quote($email_client).",numero=".$sql->quote($numero).",commentaire=".$sql->quote($commentaire)." WHERE id = ".$sql->quote($id);
			$result = $sql->exec($requpdate);


	    	$tab[$x]["error_code"] = "0";
	    	$tab[$x]["message"] = "OK";
	    	$tab[$x]["detail"] = $id;
			$aff_valide = "1";
		}else{
	    	$tab[$x]["error_code"] = "-3";
	    	$tab[$x]["error_message"] = "Paramètres invalides";
	    	$tab[$x]["error_details"] = $tab_error;
		}

		$json = json_encode($tab, JSON_PRETTY_PRINT);

		break;
}

if($dev=="1"){
	echo "<pre>";
	print_r($json);
	echo "</pre>";
}else{
	echo $json;	
}

?>