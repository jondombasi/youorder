<?php
$sql_serveur	= "localhost";
$sql_user		= "youorder";
$sql_passwd		= "75LrhfPSOqCv";
$sql_bdd		= "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

$result = $sql->query("SELECT * FROM v1_utilisateurs where 1 ORDER BY id");
while($ligne = $result->fetch()) {
	echo $ligne["id"].' - '.$ligne["email"].' - '.$ligne["role"];

	switch ($ligne["role"]) {
		case 'livreur':
		//echo "SELECT * FROM livreurs WHERE email = '".$ligne["email"]."'";
            $result2 = $sql->query("SELECT * FROM livreurs WHERE email = '".$ligne["email"]."'");
            $ligne2 = $result2->fetch();
            if($ligne2!=""){
            	echo "--> EXISTE ".$ligne2["id"];

            	if($ligne2["id"]==$ligne["id"]){
            		echo " IDENTIQUE";
            		$result3 = $sql->exec("UPDATE livreurs SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
            	}else{
            		echo " DIFFERENT";
            		$result3 = $sql->exec("UPDATE livreurs SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
            	}
            }else{
            	echo "--> A CREER";
            	//$result3 = $sql->exec("INSERT");
            }


			break;
		
		default:
			$result2 = $sql->query("SELECT * FROM utilisateurs WHERE email = '".$ligne["email"]."' AND role = '".$ligne["role"]."'");
            $ligne2 = $result2->fetch();
            if($ligne2!=""){
            	echo "--> EXISTE ".$ligne2["id"];

            	if($ligne2["id"]==$ligne["id"]){
            		echo " IDENTIQUE";
            		$result3 = $sql->exec("UPDATE utilisateurs SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
            	}else{
            		echo " DIFFERENT";
            		$result3 = $sql->exec("UPDATE utilisateurs SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
            	}
            }else{
            	echo "--> A CREER";
            	//$result3 = $sql->exec("INSERT");
            }
			break;
	}
	echo '<br/>';
}


?>