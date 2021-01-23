<?php
header('Content-type: text/html; charset=UTF-8');
require_once("../admin/inc_connexion.php");

if(isset($_GET["action"]))	{$action=$_GET["action"];}else{$action="";}	
if(isset($_GET["dev"]))		{$dev=$_GET["dev"];}else{$dev="";}	

if(isset($_GET["email"]))	{$email=$_GET["email"];}else{$email="";}	
if(isset($_GET["key"]))		{$key=$_GET["key"];}else{$key="";}	

function is_timestamp($timestamp) {
	if(is_numeric($timestamp)){
	    if(strtotime(date('d-m-Y H:i:s',$timestamp)) === (int)$timestamp) {
	    	if($timestamp>=strtotime(date('d-m-Y H:i:s'))){
	    		return $timestamp;	
	    	}else return false;
	    } else return false;		
	} else return false;
}

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
		$json = json_encode($tab);    	
		break;
	case "getallcurrent":
		$x = 0;
		$vide = true;

	    $result = $sql->query("SELECT c.* FROM commandes c INNER JOIN restaurants r ON c.restaurant=r.id WHERE  c.statut in ('ajouté','réservé','récupéré') ".$req_resto);
	    while($ligne = $result->fetch()) {
	    	$vide = false;

	    	$tab[$x]["id"] = $ligne["id"];
	    	$tab[$x]["commercant"] = $ligne["restaurant"];
	    	$tab[$x]["client"] = $ligne["client"];
	    	$tab[$x]["commentaire"] = $ligne["commentaire"];
	    	$tab[$x]["date_debut"] = strtotime($ligne["date_debut"]);
	    	$tab[$x]["date_fin"] = strtotime($ligne["date_fin"]);
	    	$tab[$x]["date_ajout"] = strtotime($ligne["date_ajout"]);
			$tab[$x]["statut"] = $ligne["statut"];

			$x++;
		}

		if($vide){
	    	$tab[$x]["error_code"] = "-4";
	    	$tab[$x]["error_message"] = "Aucune données";			
		}

		$json = json_encode($tab, JSON_PRETTY_PRINT);
		break;
	case "getallold":
		$x = 0;
		$vide = true;

	    $result = $sql->query("SELECT c.* FROM commandes c INNER JOIN restaurants r ON c.restaurant=r.id WHERE  c.statut in ('signe','echec') ".$req_resto);
	    while($ligne = $result->fetch()) {
	    	$vide = false;

	    	$tab[$x]["id"] = $ligne["id"];
	    	$tab[$x]["commercant"] = $ligne["restaurant"];
	    	$tab[$x]["client"] = $ligne["client"];
	    	$tab[$x]["commentaire"] = $ligne["commentaire"];
	    	$tab[$x]["date_debut"] = strtotime($ligne["date_debut"]);
	    	$tab[$x]["date_fin"] = strtotime($ligne["date_fin"]);
	    	$tab[$x]["date_ajout"] = strtotime($ligne["date_ajout"]);
			$tab[$x]["statut"] = $ligne["statut"];

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
			$result = $sql->query("SELECT c.* FROM commandes c INNER JOIN restaurants r ON c.restaurant=r.id WHERE c.id = ".$sql->quote($id)." ".$req_resto." LIMIT 1");
			$ligne = $result->fetch();
			if($ligne!=""){
		    	$tab[$x]["id"] = $ligne["id"];
		    	$tab[$x]["commercant"] = $ligne["restaurant"];
		    	$tab[$x]["client"] = $ligne["client"];
		    	$tab[$x]["commentaire"] = $ligne["commentaire"];
		    	$tab[$x]["date_debut"] = strtotime($ligne["date_debut"]);
		    	$tab[$x]["date_fin"] = strtotime($ligne["date_fin"]);
		    	$tab[$x]["date_ajout"] = strtotime($ligne["date_ajout"]);
				$tab[$x]["statut"] = $ligne["statut"];
		    	$x++;
			}else{
		    	$tab[$x]["error_code"] = "-3";
		    	$tab[$x]["error_message"] = "Paramètres invalides";
			}
		}
		$json = json_encode($tab, JSON_PRETTY_PRINT);
		break;
	case "create":
		$restaurant	 	= $_GET["commercant"];
		$client	 		= $_GET["client"];
		$ts_debut 		= $_GET["ts_debut"];
		$ts_fin 		= $_GET["ts_fin"];
		$commentaire	= $_GET["commentaire"];

		$continu 	= true;
		$tab_error = "";
		$tab_invalide = "";
		$e = 0;
		$i = 0;
		
		if($restaurant==""){
			$continu = false;
			$tab_error[$e] = "commercant";
			$e++;
		}else{
		    $req = "SELECT r.* FROM restaurants r WHERE r.statut = 1 AND r.id=".$sql->quote($restaurant)." ".$req_resto;
		    $result = $sql->query($req);
		    $ligne = $result->fetch();
		    if($ligne==""){
				$continu = false;
				$tab_invalide[$i] = "commercant";
				$i++;
			}else{
				$r_longitude= $ligne["longitude"];
				$r_latitude	= $ligne["latitude"];				
			}
		}

		if($client==""){
			$continu = false;
			$tab_error[$e] = "client";
			$e++;
		}else{
		    $req = "SELECT c.* FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 and r.statut = 1 AND r.id=".$sql->quote($restaurant)." ".$req_resto;
	    	$result = $sql->query($req);
		    $ligne = $result->fetch();
		    if($ligne==""){
				$continu = false;
				$tab_invalide[$i] = "client";
				$i++;
			}else{
				$c_longitude	= $ligne["longitude"];
				$c_latitude		= $ligne["latitude"];
			}	
		}

		if($commentaire==""){
			$continu = false;
			$tab_error[$e] = "commentaire";
			$e++;
		}
		if($ts_debut=="" || $ts_fin==""){
			$continu = false;
			$tab_error[$e] = "ts_debut/ts_fin";
			$e++;
		}else{
			if(is_timestamp($ts_debut) && is_timestamp($ts_fin)){
				$date_debut_bdd = date("Y-m-d H:i:s",$ts_debut);	
				$date_fin_bdd = date("Y-m-d H:i:s",$ts_fin);					
			}else{
				$continu = false;
				$tab_invalide[$i] = "ts_debut/ts_fin";
				$i++;
			}
		}
		

		if($continu){
			$adresse1 = $r_latitude.",".$r_longitude;
			$adresse2 = $c_latitude.",".$c_longitude;
			$resultat = getDistance($adresse1,$adresse2);
			$distance = $resultat["distanceEnMetres"];
			$duree = $resultat["dureeEnSecondes"];
					
			$reqinsert =  "INSERT INTO commandes (restaurant, client, livreur, commentaire, date_debut, date_fin, date_ajout, statut, date_statut, distance, duree) VALUES (".$sql->quote($restaurant).",".$sql->quote($client).", '0',".$sql->quote($commentaire).",".$sql->quote($date_debut_bdd).", ".$sql->quote($date_fin_bdd).", NOW(),'ajouté', NOW(), '".$distance."', '".$duree."')";
			$result = $sql->exec($reqinsert);		
			$id = $sql->lastInsertId(); 
			$result = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, date, id_user, id_livreur) VALUES (".$sql->quote($id).", 'ajouté', NOW(),".$sql->quote($userid).", '0')");

			$aff_valide = "1";

	    	$tab[$x]["error_code"] = "0";
	    	$tab[$x]["message"] = "OK";
	    	$tab[$x]["detail"] = $id;
		}else{
	    	$tab[$x]["error_code"] = "-3";
	    	$tab[$x]["error_message"] = "Paramètres invalides";
	    	$tab[$x]["error_details"] = $tab_error;
	    	$tab[$x]["invalide_details"] = $tab_invalide;
		}
		$json = json_encode($tab, JSON_PRETTY_PRINT);
		break;
	case "edit":
		$id 			= $_GET["id"];
		$ts_debut 		= $_GET["ts_debut"];
		$ts_fin 		= $_GET["ts_fin"];
		$commentaire	= $_GET["commentaire"];

		$continu 	= true;
		$tab_error = "";
		$tab_invalide = "";
		$e = 0;
		$i = 0;
		
		if($id==""){
			$continu = false;
			$tab_error[$e] = "id";
			$e++;
		}else{
			$result = $sql->query("SELECT c.* FROM commandes c INNER JOIN restaurants r ON c.restaurant=r.id WHERE c.id = ".$sql->quote($id)." ".$req_resto." LIMIT 1");
			$ligne = $result->fetch();
			if($ligne==""){
				$continu = false;
				$tab_invalide[$i] = "id";
				$i++;
			}
		}
		if($commentaire==""){
			$continu = false;
			$tab_error[$e] = "commentaire";
			$e++;
		}
		if($ts_debut=="" || $ts_fin==""){
			$continu = false;
			$tab_error[$e] = "ts_debut/ts_fin";
			$e++;
		}else{
			if(is_timestamp($ts_debut) && is_timestamp($ts_fin)){
				$date_debut_bdd = date("Y-m-d H:i:s",$ts_debut);	
				$date_fin_bdd = date("Y-m-d H:i:s",$ts_fin);					
			}else{
				$continu = false;
				$tab_invalide[$i] = "ts_debut/ts_fin";
				$i++;
			}
		}
		

		if($continu){
			$requpdate =  "UPDATE commandes SET commentaire=".$sql->quote($commentaire).",date_debut=".$sql->quote($date_debut_bdd).", date_fin=".$sql->quote($date_fin_bdd)." WHERE id = ".$sql->quote($id);
			$result = $sql->exec($requpdate);

			$aff_valide = "1";

	    	$tab[$x]["error_code"] = "0";
	    	$tab[$x]["message"] = "OK";
	    	$tab[$x]["detail"] = $id;
		}else{
	    	$tab[$x]["error_code"] = "-3";
	    	$tab[$x]["error_message"] = "Paramètres invalides";
	    	$tab[$x]["error_details"] = $tab_error;
	    	$tab[$x]["invalide_details"] = $tab_invalide;
		}
		$json = json_encode($tab, JSON_PRETTY_PRINT);


		break;
}

if($dev=="1"){
	echo "<pre>";
	echo $json;
	echo "</pre>";
}else{
	echo $json;	
}

?>