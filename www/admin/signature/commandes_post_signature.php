<?php
require_once("../inc_connexion.php");

$id = $_POST["commande"];
$img = $_POST["contenu_image"];
//echo $id."<br/>";
//echo $img."<br/>";
//echo '<img src="'.$img.'" />';
if($id!="" && $img!=""){
	$data_uri = $img;
	$data_pieces = explode(",", $data_uri);
	$encoded_image = $data_pieces[1];
	$decoded_image = base64_decode($encoded_image);
	
	$nom_image = "signature_".$id."_".date("YmdHis").".png";
	file_put_contents($nom_image,$decoded_image);	
	
	$result = $sql->exec("UPDATE commandes SET statut = 'signé', signature=".$sql->quote($nom_image).", date_statut = NOW() WHERE id = '".$id."'");		
	$res = send_notif($id,$sql);
	//echo "1";		
	header("location: ../commandes_visu.php?id=".$id);
}else{
	echo "-1";	
}

?>