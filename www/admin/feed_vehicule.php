<?php
header('Content-Type: application/json');
date_default_timezone_set('Europe/Paris');
require_once("inc_connexion.php");

if(isset($_GET["id"]))      {$id=$_GET["id"];}else{$id="";}
$array_vehicule=[];

$i=0;
$result = $sql->query("SELECT vp.*, u.nom, u.prenom, v.immatriculation FROM vehicules_planning vp LEFT JOIN utilisateurs u ON vp.id_livreur=u.id LEFT JOIN vehicules v ON vp.id_vehicule=v.id WHERE vp.id_vehicule=".$sql->quote($id));
while ($ligne=$result->fetch()) {
	$array_vehicule[$i]["allDay"]=false;
    $array_vehicule[$i]["title"]='Utilisation : '.left($ligne["prenom"],1)." ".$ligne["nom"];
    $array_vehicule[$i]["id"]=$ligne["id"];
    $array_vehicule[$i]["start"]=strtotime($ligne["h_debut"]);
    $array_vehicule[$i]["end"]=strtotime($ligne["h_fin"]);
    $array_vehicule[$i]["className"]="label-green";
    $array_vehicule[$i]["timezoneParam"]="none";
    $array_vehicule[$i]["tooltip"]="<table id='tooltip_table'><tr><th>Livreur</th><td>".$ligne["prenom"]." ".$ligne["nom"]."</td></tr><tr><th>Date</th><td>".date("d/m/Y", strtotime($ligne["h_debut"]))."</td></tr><tr><th>Heure de début</th><td>".date("H:i", strtotime($ligne["h_debut"]))."</td></tr><tr><th>Heure de fin</th><td>".date("H:i", strtotime($ligne["h_fin"]))."</td></tr><tr><th>Immatriculation</th><td>".$ligne["immatriculation"]."</td></tr></table>";
    $i++;
}

echo json_encode($array_vehicule);

?>