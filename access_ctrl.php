<?php
require_once("inc_connexion.php");

$login      = $_GET["username"];
$password   = $_GET["password"];

if(isset($_GET["cmd"]))         {$cmd       = $_GET["cmd"];}        else{$cmd       = "";}

if(isset($_GET["remember"]))    {$remember  = $_GET["remember"];}   else{$remember  = "";}

$_SESSION["login"]      = $login;
$_SESSION["password"]   = $password;

if($remember=="1"){
	setcookie('ao_login', $login, (time() +60*60*24*30));
	setcookie('ao_password', $password, (time() +60*60*24*30));	
}else{
	setcookie("ao_login", '', (time() - 3600));
	setcookie("ao_password", '', (time() - 3600));	
}

$result = $sql->query("SELECT * FROM utilisateurs WHERE email = ".$sql->quote($login)." AND password = ".$sql->quote($password)." AND role != 'inactif'");
$ligne  = $result->fetch();

if($ligne!=""){
	$_SESSION["acces"]      = true;
	$_SESSION["userid"]     = $ligne["id"];
	$_SESSION["login"]      = ucfirst(strtolower($ligne["prenom"]))." ".strtoupper(substr($ligne["nom"],0,1)).".";
	$_SESSION["email"]      = $ligne["email"];
	$_SESSION["username"]   = $ligne["email"];
	$role = $ligne["role"];
	$_SESSION["role"]       = $ligne["role"];
	if ($ligne["photo"]!="" && $ligne["photo"]!=null) {
		$_SESSION["photo"] = "upload/utilisateurs/".$ligne["photo"];	
	}
	else {
		$_SESSION["photo"] = "images/no_avatar.png";
	}
	
	
	$_SESSION["planner"]        = false;
	$_SESSION["livreur"]        = false;
	$_SESSION["restaurateur"]   = false;

	if($role=="admin"){
		$_SESSION["admin"]              = true;
		$_SESSION["planner"]            = true;
		$_SESSION["livreur"]            = true;
		$_SESSION["affecter_commande"]  = true;
		$_SESSION["visibilite_map"]     = true;
		$_SESSION["planning_livreur"]   = true;
        $_SESSION["visibilite_live"]    = true;
	}else{
		$_SESSION["admin"]              = false;
	}
	if($role=="planner"){
		$_SESSION["planner"]            = true;
		$_SESSION["affecter_commande"]  = true;
		$_SESSION["visibilite_map"]     = true;
		$_SESSION["planning_livreur"]   = true;
        $_SESSION["visibilite_live"]    = true;
	}
	if($role=="livreur"){
		$_SESSION["livreur"]            = true;
	}
	
	$_SESSION["req_resto"] = "";
	$_SESSION["req_livreur"] = "";
	$_SESSION["req_commande"] = "";
	if($role=="restaurateur"){
		//$_SESSION["req_resto"] = " AND r.id = '".$ligne["restaurant"]."' ";	
		$_SESSION["restaurateur"] = true;

		$_SESSION["req_resto"]      = " AND r.id IN             (".$ligne["liste_resto"].") ";
		$_SESSION["req_livreur"]    = " AND p.id_commercant IN  (".$ligne["liste_resto"].") ";
		$_SESSION["req_commande"]   = " AND restaurant IN       (".$ligne["liste_resto"].") ";

		$_SESSION["affecter_commande"]  = ($ligne["affecter_commande"]  =="on") ? true : false;
		$_SESSION["visibilite_map"]     = ($ligne["visibilite_map"]     =="on") ? true : false;
		$_SESSION["planning_livreur"]   = ($ligne["planning_livreur"]   =="on") ? true : false;
	}
	
	$result = $sql->exec("UPDATE utilisateurs SET statut = 'ON', date_conn = NOW() WHERE id = '".$ligne["id"]."'");		
	
	if($cmd!=""){
		header("location: commandes_visu.php?id=".$cmd);
	}else{
		header("location: home.php");
	}
	exit();
}else{
	$_SESSION["acces"] = false;
	header("location: index.php?err=1");	
	exit();
}

/*
if($login=="admin" && $password=="admin"){
	$_SESSION["acces"] = true;
	$_SESSION["clientid"] = "1";
	header("location: home.php");
}else{
	$_SESSION["acces"] = false;
	header("location: index.php?err=1");	
}
*/
?>