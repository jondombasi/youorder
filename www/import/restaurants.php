<?php
$sql_serveur	= "localhost";
$sql_user		= "youorder";
$sql_passwd		= "75LrhfPSOqCv";
$sql_bdd		= "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

$result = $sql->query("SELECT * FROM v1_restaurants where statut = 1 ORDER BY id");
while($ligne = $result->fetch()) {
	echo $ligne["id"].' - '.$ligne["nom"]." (".$ligne["adresse"].") ".$ligne["utilisateur"];

	$result2 = $sql->query("SELECT * FROM restaurants WHERE nom = ".$sql->quote($ligne["nom"]));
      $ligne2 = $result2->fetch();
      if($ligne2!=""){
            echo "--> EXISTE ".$ligne2["id"]." ".$ligne2["utilisateur"];

            if($ligne2["id"]==$ligne["id"]){
                  echo " IDENTIQUE";
                  $result3 = $sql->exec("UPDATE restaurants SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
            }else{
                  echo " DIFFERENT";
                  $result3 = $sql->exec("UPDATE restaurants SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
            }

            $result3 = $sql->query("SELECT * FROM utilisateurs WHERE v1='".$ligne["utilisateur"]."'");
            $ligne3 = $result3->fetch();
            if(!$ligne3) {
                  echo "<br/>------> ID DIFFERENT";
            }
            else {
                  echo "<br/>------> ID IDENTIQUE";
            }
      }else{
            echo "--> A CREER";
            //$result3 = $sql->exec("INSERT");
      }

	echo '<br/>';
}


?>