<?php
$sql_serveur	= "localhost";
$sql_user		= "youorder";
$sql_passwd		= "75LrhfPSOqCv";
$sql_bdd		= "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

$result = $sql->query("SELECT * FROM v1_clients where 1 ORDER BY id");
while($ligne = $result->fetch()) {
	echo $ligne["id"].' - '.$ligne["nom"].' '.$ligne["prenom"].' '.$ligne["numero"].' ('.$ligne["restaurant"].")";

	$result2 = $sql->query("SELECT c.*, r.id as new_resto, r.v1 as v1_resto FROM clients c INNER JOIN restaurants r ON c.restaurant=r.id WHERE c.nom = ".$sql->quote($ligne["nom"])." AND c.prenom = ".$sql->quote($ligne["prenom"])." AND c.numero = '".$ligne["numero"]."' AND c.adresse = ".$sql->quote($ligne["adresse"])." AND r.v1 = '".$ligne["restaurant"]."'");
	$ligne2 = $result2->fetch();
	if($ligne2!=""){
      	echo "--> EXISTE ".$ligne2["id"];

		if($ligne2["id"]==$ligne["id"]){
    		echo " IDENTIQUE";
    		$result3 = $sql->exec("UPDATE clients SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
    	}else{
            if($ligne2["v1"]==$ligne["id"]){
                echo " REMPLACE";
            }else{
        		echo ' <span style="color:red">DIFFERENT</span>';
        		$result3 = $sql->exec("UPDATE clients SET v1 = '".$ligne["id"]."' WHERE id = '".$ligne2["id"]."'");
            }
    	}

    	if($ligne2["v1_resto"]==$ligne2["restaurant"]){
    		echo " RESTO_IDENT";
    	}elseif ($ligne2["new_resto"]==$ligne2["restaurant"]) {
            echo " RESTO_REMP";
        }else{
    		echo ' <span style="color:red">RESTO_DIFF</span>';
            $result3 = $sql->exec("UPDATE clients SET restaurant = '".$ligne2["new_resto"]."' WHERE id = '".$ligne2["id"]."'");
    	}
    }else{
        //echo "SELECT * FROM restaurants WHERE v1 = ".$sql->quote($ligne["restaurant"])."<br/>";
        $result3 = $sql->query("SELECT * FROM restaurants WHERE v1 = ".$sql->quote($ligne["restaurant"])."");
        $ligne3 = $result3->fetch();
        if($ligne3!=""){
            $new_resto = $ligne3["id"];
        }
        //echo "<br/>NEW : $new_resto <br/>";

    	echo '--> <span style="color:orange">A CREER</span>';

    	$result3 = $sql->exec("INSERT INTO clients 
                                (`nom`, `prenom`, `adresse`, `longitude`, `latitude`, `numero`, `email`, `commentaire`, `restaurant`, `statut`, `date_ajout`, `v1`) 
                                SELECT `nom`, `prenom`, `adresse`, `longitude`, `latitude`, `numero`, `email`, `commentaire`, `restaurant`, `statut`, `date_ajout`, id 
                                FROM v1_clients WHERE id = '".$ligne["id"]."'");
        $lastid = $sql->lastInsertId();
        //echo "UPDATE clients SET restaurant = '".$new_resto."' WHERE id = '".$lastid."'";
        $result3 = $sql->exec("UPDATE clients SET restaurant = '".$new_resto."' WHERE id = '".$lastid."'");
    }

	echo '<br/>';
}


?>