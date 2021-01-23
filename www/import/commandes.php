<?php
set_time_limit (21600);

$sql_serveur	= "localhost";
$sql_user		= "youorder";
$sql_passwd		= "75LrhfPSOqCv";
$sql_bdd		= "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

$result = $sql->query("SELECT * FROM v1_commandes ORDER BY id");
while($ligne = $result->fetch()) {
	echo $ligne["id"].' : '.$ligne["restaurant"]." - ".$ligne["client"]." - ".$ligne["livreur"];

	$result2 = $sql->query("SELECT * FROM commandes WHERE date_ajout = ".$sql->quote($ligne["date_ajout"]));
      $ligne2 = $result2->fetch();
      if($ligne2!=""){
            echo "--> EXISTE<br/>".$ligne2["id"].' : '.$ligne2["restaurant"]." - ".$ligne2["client"]." - ".$ligne2["livreur"];

            $result3 = $sql->query("SELECT * FROM restaurants WHERE v1='".$ligne["restaurant"]."'");
            $ligne3 = $result3->fetch();
            if(!$ligne3) {
                  echo "<br/>------> ERROR";
            }
            else {
                  if($ligne3["id"]==$ligne3["v1"]){
                        echo "<br/>------> RESTO IDENTIQUE";

                  }else{
                        echo "<br/>------> RESTO DIFFERENT";
                        //$result_ = $sql->exec("UPDATE commandes SET restaurant = '".$ligne3["id"]."' WHERE id = '".$ligne2["id"]."'");
                  }
            }

            $result4 = $sql->query("SELECT * FROM clients WHERE v1='".$ligne["client"]."'");
            $ligne4 = $result4->fetch();
            if(!$ligne4) {
                  $result8 = $sql->query("SELECT * FROM clients WHERE id='".$ligne["client"]."'");
                  $ligne8 = $result8->fetch();
                  if(!$ligne8) {
                        echo "<br/>------> ERROR";
                  }
                  else {
                        echo "<br/>------> CLIENT IDENTIQUE";
                  }
            }
            else {
                  if($ligne4["id"]==$ligne4["v1"]){
                        echo "<br/>------> CLIENT IDENTIQUE";

                  }else{
                        echo "<br/>------> CLIENT DIFFERENT";
                        //$result_ = $sql->exec("UPDATE commandes SET client = '".$ligne4["id"]."' WHERE id = '".$ligne2["id"]."'");
                  }
            }

            $result5 = $sql->query("SELECT * FROM livreurs WHERE v1='".$ligne["livreur"]."'");
            $ligne5 = $result5->fetch();
            if(!$ligne5) {
                  $result6 = $sql->query("SELECT * FROM utilisateurs WHERE v1='".$ligne["livreur"]."'");
                  $ligne6 = $result6->fetch();
                  if(!$ligne6) {
                        $result7 = $sql->query("SELECT * FROM v1_utilisateurs WHERE id='".$ligne["livreur"]."'");
                        $ligne7 = $result7->fetch();
                        if(!$ligne7) {
                              echo "<br/>------> LIVREUR SUPPRIME";
                              //$result3 = $sql->exec("UPDATE commandes SET livreur = '33' WHERE id = '".$ligne2["id"]."'");
                        }
                        else {
                              echo "<br/>------> LIVREUR IDENTIQUE";
                        }
                  }
                  else {
                       echo "<br/>------> LIVREUR DIFFERENT / ".$ligne6["id"]; 
                       $result3 = $sql->exec("UPDATE commandes SET livreur = '".$ligne6["id"]."' WHERE id = '".$ligne2["id"]."'");
                  }
            }
            else {
                  echo "<br/>------> LIVREUR DIFFERENT / ".$ligne5["id"];
                  $result3 = $sql->exec("UPDATE commandes SET livreur = '".$ligne5["id"]."' WHERE id = '".$ligne2["id"]."'");
            }

      }
      else{
            echo "--> A CREER";

            $result3 = $sql->query("SELECT * FROM restaurants WHERE v1 = ".$sql->quote($ligne["restaurant"])."");
            $ligne3 = $result3->fetch();
            if($ligne3!=""){
                  $new_resto = $ligne3["id"];
            }

            $result3 = $sql->query("SELECT * FROM clients WHERE v1 = ".$sql->quote($ligne["client"])."");
            $ligne3 = $result3->fetch();
            if($ligne3!=""){
                  $new_client = $ligne3["id"];
            }

            /*$result3 = $sql->exec("INSERT INTO commandes 
                                (restaurant, client, commentaire, date_debut, date_fin, date_ajout, statut, raison_refus, comm_refus, date_statut, distance, duree, livreur, signature) 
                                SELECT restaurant, client, commentaire, date_debut, date_fin, date_ajout, statut, raison_refus, comm_refus, date_statut, distance, duree, livreur, signature 
                                FROM v1_commandes WHERE id = '".$ligne["id"]."'");
            $lastid = $sql->lastInsertId();
            $result3 = $sql->exec("UPDATE commandes SET restaurant = '".$new_resto."' WHERE id = '".$lastid."'");
            $result3 = $sql->exec("UPDATE commandes SET client = '".$new_client."' WHERE id = '".$lastid."'");

            echo $ligne["statut"]."<br/>";

            //ajouter l'historique des commandes selon leur statut
            switch($ligne["statut"]) {
                  case "ajouté":
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'ajouté', ".$sql->quote($ligne["date_ajout"]).", '22', 0)");      
                  break;
                  case "réservé":
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'ajouté', ".$sql->quote($ligne["date_ajout"]).", '22', 0)");      
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'réservé', ".$sql->quote($ligne["date_statut"]).", '', ".$sql->quote($ligne["livreur"]).")");  
                  break;
                  case "récupéré":
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'ajouté', ".$sql->quote($ligne["date_ajout"]).", '22', 0)");     
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'réservé', ".$sql->quote($ligne["date_statut"]).", '', ".$sql->quote($ligne["livreur"]).")"); 
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'récupéré', ".$sql->quote($ligne["date_statut"]).", ".$sql->quote($ligne["livreur"]).", ".$sql->quote($ligne["livreur"]).")");      
                  break;
                  case "signé":
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'ajouté', ".$sql->quote($ligne["date_ajout"]).", '22', 0)");     
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'réservé', ".$sql->quote($ligne["date_statut"]).", '22', ".$sql->quote($ligne["livreur"]).")"); 
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'récupéré', ".$sql->quote($ligne["date_statut"]).", ".$sql->quote($ligne["livreur"]).", ".$sql->quote($ligne["livreur"]).")");      
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'signé', ".$sql->quote($ligne["date_statut"]).", ".$sql->quote($ligne["livreur"]).", ".$sql->quote($ligne["livreur"]).")");   
                  break;
                  case "echec":
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'ajouté', ".$sql->quote($ligne["date_ajout"]).", '22', 0)");     
                        $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'réservé', ".$sql->quote($ligne["date_statut"]).", '22', ".$sql->quote($ligne["livreur"]).")"); 
                              $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'récupéré', ".$sql->quote($ligne["date_statut"]).", ".$sql->quote($ligne["livreur"]).", ".$sql->quote($ligne["livreur"]).")");      
                              $result3 = $sql->exec("INSERT INTO commandes_historique (id_commande, statut, `date`, id_user, id_livreur) VALUE (".$sql->quote($lastid).", 'echec', ".$sql->quote($ligne["date_statut"]).", ".$sql->quote($ligne["livreur"]).", ".$sql->quote($ligne["livreur"]).")");   
                  break;
            }*/
      }

	echo '<br/>';
}


?>