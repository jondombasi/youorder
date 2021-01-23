<?php
header('Content-Type: application/json');
date_default_timezone_set('Europe/Paris');
require_once("inc_connexion.php");

if(isset($_GET["id"]))              {$id=$_GET["id"];}                          else{$id="";}
if(isset($_GET["id_vehicule"]))     {$id_vehicule=$_GET["id_vehicule"];}        else{$id_vehicule="";}
if(isset($_GET["id_commercant"]))   {$id_commercant=$_GET["id_commercant"];}    else{$id_commercant="";}
if(isset($_GET["action"]))          {$action=$_GET["action"];}                  else{$action="";}

$array_livreur  = [];
$req_sup        = "";

if ($id!="") {
	$req_sup=" AND l.id_livreur=".$sql->quote($id);
}
if ($id_vehicule!="") {
	$req_sup=" AND l.id_vehicule=".$sql->quote($id_vehicule);
}
if ($id_commercant!="") {
	$req_sup=" AND l.id_commercant=".$sql->quote($id_commercant);
}

$i=0;
if ($action=="calendar_presence") {
	$result = $sql->query("SELECT l.*, r.nom as nom_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.nom as nom_vehicule FROM livreurs_connexion l LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE 1 ".$req_sup.$_SESSION["req_resto"]);
	while ($ligne=$result->fetch()) {
		$array_livreur[$i]["allDay"]=false;
	    $array_livreur[$i]["start"]=strtotime($ligne["date_connexion"]);
	    $array_livreur[$i]["end"]=strtotime($ligne["date_deconnexion"]);

	    $cpt_recup=0;
	    $cpt_signe=0;
	    $cpt_echec=0;

	    $result2=$sql->query("SELECT * FROM commandes_historique WHERE id_livreur=".$sql->quote($ligne["id_livreur"])." AND statut IN ('récupéré', 'signé', 'echec') AND date BETWEEN ".$sql->quote($ligne["date_connexion"])." AND ".$sql->quote($ligne["date_deconnexion"]));
	    while ($ligne2=$result2->fetch()) {
	    	if ($ligne2["statut"]=="récupéré") {
	    		$cpt_recup++;
	    	}
	    	else if ($ligne2["statut"]=="signé") {
	    		$cpt_signe++;
	    	}
	    	else if ($ligne2["statut"]=="echec") {
	    		$cpt_echec++;
	    	}
	    }
	    
	    if ($ligne["type"]=="manuel") {
	    	$array_livreur[$i]["title"]='Heures sup<br/>Commercant : '.$ligne["nom_resto"].'<br/>Récupérées : '.$cpt_recup.'<br/>Signées : '.$cpt_signe.'<br/>Echec : '.$cpt_echec;
	    	$array_livreur[$i]["className"]="label-yellow";
	    }
	    else {
	    	$array_livreur[$i]["title"]='Commercant : '.$ligne["nom_resto"].'<br/>Récupérées : '.$cpt_recup.'<br/>Signées : '.$cpt_signe.'<br/>Echec : '.$cpt_echec;
	    	$array_livreur[$i]["className"]="label-green";
	    	
	    }

	    $array_livreur[$i]["tooltip"]=date("H:i", strtotime($ligne["date_connexion"]))." - ".date("H:i", strtotime($ligne["date_deconnexion"]))." \n Commercant : ".$ligne["nom_resto"]." \n Récupérées : ".$cpt_recup." \n Signées : ".$cpt_signe." \n Echec : ".$cpt_echec;
	    $array_livreur[$i]["timezoneParam"]="none";

	    $array_livreur[$i]["timezoneParam"]="none";
	    
	    $i++;
	}
}
if ($action=="calendar_theorique") {
	$result = $sql->query("SELECT l.*, r.nom as nom_resto, li.nom as nom_livreur, li.prenom as prenom_livreur, v.nom as nom_vehicule FROM livreurs_planning l LEFT JOIN restaurants r ON l.id_commercant=r.id LEFT JOIN livreurs li ON l.id_livreur=li.id LEFT JOIN vehicules v ON l.id_vehicule=v.id WHERE 1 ".$req_sup.$_SESSION["req_resto"]);
	while ($ligne=$result->fetch()) {
		$livreur_info=($ligne["id_livreur"]!=0) ? $ligne["prenom_livreur"]." ".$ligne["nom_livreur"] : "Non affecté";
		$array_livreur[$i]["allDay"]=false;
	    $array_livreur[$i]["start"]=strtotime($ligne["date_debut"]);
	    $array_livreur[$i]["end"]=strtotime($ligne["date_fin"]);	
	    if (strtotime($ligne["date_fin"])-strtotime($ligne["date_debut"])<=3600) {
	    	$array_livreur[$i]["title"]='Commercant : '.$ligne["nom_resto"];
	    }   
	    else {
	    	$array_livreur[$i]["title"]='Commercant : '.$ligne["nom_resto"]."<br/>Livreur : ".$livreur_info;
	    } 
	    	    
	    $array_livreur[$i]["id_planning"]=$ligne["id"];

	    if ($ligne["id_livreur"]==0 || $ligne["id_livreur"]=="") {
	    	$array_livreur[$i]["className"]="label-orange";
	    }
	    else if ($ligne["id_vehicule"]==0 || $ligne["id_vehicule"]=="") {
	    	$array_livreur[$i]["className"]="label-yellow";
	    }
	    else {
	    	$array_livreur[$i]["className"]="label-green";
	    }

	    $array_livreur[$i]["tooltip"]=date("H:i", strtotime($ligne["date_debut"]))." - ".date("H:i", strtotime($ligne["date_fin"]))." \n Commercant : ".$ligne["nom_resto"]." \n Livreur : ".$livreur_info;
	    $array_livreur[$i]["timezoneParam"]="none";
	    
	    $i++;
	}
}
echo json_encode($array_livreur);

?>