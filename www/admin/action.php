<?php
require_once("inc_connexion.php");

if(isset($_GET["action"]))		{$action=$_GET["action"];}else{
	if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}	
}

switch($action){
	case "get_content":
		echo "map = new GMaps({
				el: '#map',
				zoom: 12,
				lat: 48.861739,
				lng: 2.346888
			  });";

		$req = "SELECT * FROM utilisateurs WHERE appli = 'ON' ORDER BY date_conn DESC";
		$result = $sql->query($req);
		while($ligne = $result->fetch()) {
			$title = $ligne["prenom"].' '.$ligne["nom"];
			$content = '<b>'.$ligne["prenom"].' '.$ligne["nom"].'</b><br/>'.$ligne["numero"].'<br/>MAJ : '.date("d/m H:i",strtotime($ligne["lastUpdate"]));
			?>
			  map.addMarker({
				lat: <?=$ligne["latitude"]?>,
				lng: <?=$ligne["longitude"]?>,
				icon: 'images/velo-detour.png?1',
				title: '<?=$title?>',
				infoWindow: {
				  content: '<p style="cursor:pointer" onclick="centerMap(\'<?=$ligne["latitude"]?>\',\'<?=$ligne["longitude"]?>\')"><?=$content?></p>'
	            }				
			  });	  
			
			<?php
		}

		break;
	case "supputilisateur":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			$result = $sql->exec("DELETE FROM utilisateurs WHERE id = '".$id."'");		
		}	
		header("location: administration.php");
		break;
	case "suppresto":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			$result = $sql->exec("UPDATE restaurants SET statut = 0 WHERE id = '".$id."'");		
		}	
		header("location: restaurants_liste.php?ret=restosup");
		break;

    case "recupresto":
        if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
        if(is_numeric($id)){

            $result = $sql->exec("UPDATE restaurants SET statut= 1 WHERE id = '".$id."'");
        }
        header("location: restaurants_liste.php");
        break;
		
	case "suppclient":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			$result = $sql->exec("UPDATE clients SET statut = 0 WHERE id = '".$id."'");		
		}	
		header("location: clients_liste.php?ret=clientsup");
		break;

	case "suppcommande":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			//$result = $sql->exec("DELETE FROM commandes WHERE id = '".$id."'");		
			$result = $sql->exec("UPDATE commandes SET statut='supprime' WHERE id = '".$id."'");	
		}	
		header("location: commandes_liste.php");
		break;

    case "suppoperation":
        if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
        if(is_numeric($id)){
            //$result = $sql->exec("DELETE FROM piece WHERE id = '".$id."'");
            $result = $sql->exec("DELETE FROM operation WHERE id = '".$id."'");
        }
        header("location: vehicule_operation_liste.php");
        break;

    case "suppmateriel":
        if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
        if(is_numeric($id)){
            //$result = $sql->exec("DELETE FROM piece WHERE id = '".$id."'");
            $result = $sql->exec("UPDATE piece SET etat='supprime' WHERE id = '".$id."'");
        }
        header("location: piece_liste.php");
        break;

    case "supppuce":
        if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
        if(is_numeric($id)){
            //$result = $sql->exec("DELETE FROM piece WHERE id = '".$id."'");
            $result = $sql->exec("UPDATE phone_number SET etat='supprime' WHERE id = '".$id."'");
        }
        header("location: phone_liste.php");
        break;

	case "suppvehicule":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			//$result = $sql->exec("DELETE FROM vehicules WHERE id = '".$id."'");		
			$result = $sql->exec("UPDATE vehicules SET etat='supprime' WHERE id = '".$id."'");		
		}	
		header("location: vehicules_liste.php");
		break;

	case "supplivreur":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			//$result = $sql->exec("DELETE FROM vehicules WHERE id = '".$id."'");		
			$result = $sql->exec("UPDATE livreurs SET statut='supprime' WHERE id = '".$id."'");		
		}	
		header("location: livreurs_liste.php");
		break;


    case "recuplivreur":
        if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
        if(is_numeric($id)){

            $result = $sql->exec("UPDATE livreurs SET statut='OFF' WHERE id = '".$id."'");
        }
        header("location: livreurs_liste.php");
        break;

	case "suppactualite":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			$result = $sql->exec("DELETE FROM actualites WHERE id = '".$id."'");				
		}	
		header("location: actualite_liste.php");
		break;

	case "suppnotif":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			$result = $sql->exec("UPDATE notifications_push SET statut='supprime' WHERE id = '".$id."'");					
		}	
		header("location: notification_liste.php");
		break;

	case "send_email":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			$result = $sql->query("SELECT * FROM utilisateurs WHERE id = '".$id."' AND role != 'inactif'");
			$ligne = $result->fetch();
			if($ligne!=""){
				$password = $ligne["password"];
				$nom	 = $ligne["nom"];
				$prenom	 = $ligne["prenom"];
				$email	 = $ligne["email"];

				$body = "";
				$body .= 'Bonjour, <br/><br/>
							Voici vos identifiants pour accéder à l\'interface You Order et gérer vos commandes : <br/>
							Accès : <a href="http://www.youorder.fr/admin/">http://youorder.fr/admin/</a><br/>
							Identifiant : '.$email.'<b></b><br/>
							Mot de passe : '.$password.'<b></b><br/><br/>
							Merci,<br/>
							L\'équipe YouOrder';
			 
			   // On créé une nouvelle instance de la classe
			   require_once('PHPMailer/class.phpmailer.php');
			   $mail = new PHPMailer();
			   $mail->From = "contact@youorder.fr";
			   $mail->Sender = "contact@youorder.fr";
			   $mail->FromName = "YouOrder";
			   $mail->Subject = "Votre accès YouOrder";
			   $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
			   $mail->MsgHTML($body);
			   $mail->CharSet = 'UTF-8';	
			   $mail->AddReplyTo("contact@youorder.fr","YouOrder");
			   $mail->AddAddress($email, "");
			   //$mail->AddBCC("guillaume@mgmobile.fr","");
			   $mail->send();
				
				$ret = "1";
			}else{
				$ret = "-1";
			}
		}	
		header("location: administration.php?ret=".$ret);
		break;
	case "export_liste_restos":
	    if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
		$req_sup = "";
	    if($nom != ""){
	        $req_sup .= " AND r.nom LIKE '%".$nom."%' ";		
	    }
	    
		$nomfic = 'exports/restaurants_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom','Adresse','Contact','Numero','Date Ajout');
		fputcsv($fp, $titre, ';');

		$vide = true;
	    $req = "SELECT r.nom, r.adresse, r.contact, r.numero, r.date_ajout FROM restaurants r INNER JOIN utilisateurs u ON r.utilisateur=u.id WHERE r.statut = 1 ".$req_sup.$_SESSION["req_resto"];
	    $result = $sql->query($req);
	    while($ligne = $result->fetch()) {
	    	$ligne = array_map('utf8_decode', $ligne);
	    	$lignecsv = array($ligne["nom"],$ligne["adresse"],$ligne["contact"],$ligne["numero"],$ligne["date_ajout"]);
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;
	case "liste_restos":
		if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
		if(isset($_GET["p"]))				{$p=$_GET["p"];}else{$p=1;}
		$nbaff = 30;
		$p = $p - 1;
		$pt = ($p*$nbaff);
		if($pt<0){$pt = 1;}
		?>
        <table class="table table-bordered table-hover" id="sample-table-1">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Contact</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $req_sup = "";
                if($nom != ""){
                    $req_sup .= " AND r.nom LIKE '%".$nom."%' ";		
                }
                
				$vide = true;
                $req = "SELECT r.*,u.nom as u_nom, u.prenom as u_prenom FROM restaurants r INNER JOIN utilisateurs u ON r.utilisateur=u.id WHERE r.statut = 1 ".$req_sup.$_SESSION["req_resto"]." LIMIT ".$pt.",".$nbaff;
                $result = $sql->query($req);
                while($ligne = $result->fetch()) {
					$vide = false;
                    $id = $ligne["id"];
					$date_ajout = date("d/m/Y",strtotime($ligne["date_ajout"]));
					$pseudo = ucfirst(strtolower($ligne["u_prenom"])).' '.strtoupper(substr($ligne["u_nom"],0,1));
                    ?>
                    <tr>
                        <td><?php echo $ligne["nom"]; ?></td>
                        <td><?php echo $ligne["adresse"]; ?></td>
                        <td><?php echo $ligne["contact"].'<br/>'.$ligne["numero"]; ?></td>
                        <td>
                            <a href="restaurants_fiche.php?id=<?php echo $id ?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
                            <a onclick="affecte_suppid('<?php echo $id ?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>
                        </td>
                    </tr>                                            
                    <?php
                }
				if($vide){
					?>
					<tr>
                    	<td colspan="5">Aucun résultat disponible</td>
                    </tr>
					<?php	
				}
                ?>
            </tbody>                                	
        </table>                           
        
        <?php
		break;

	case "export_liste_users":
		if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
		if(isset($_GET["email"]))	{$email=$_GET["email"];}else{$email="";}
		if(isset($_GET["role"]))	{$role=$_GET["role"];}else{$role="";}

		$req_sup = "";
		if($nom != ""){
			$req_sup .= " AND nom LIKE '%".$nom."%' ";		
		}
		if($email != ""){
			$req_sup .= " AND email LIKE '%".$email."%' ";		
		}
		if($role != ""){
			$req_sup .= " AND role = '".$role."' ";		
		}else{
		//	$req_sup .= " AND e.etat != '6' ";
		}
		

		$nomfic = 'exports/users_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom','Prenom','Numero','Email','Role');
		fputcsv($fp, $titre, ';');

		$vide = true;
        $req = "SELECT * FROM utilisateurs WHERE 1 ".$req_sup." ORDER BY id ASC";
        $result = $sql->query($req);
	    while($ligne = $result->fetch()) {
	    	$ligne = array_map('utf8_decode', $ligne);

            if(strtoupper($ligne["role"])=="RESTAURATEUR"){
                $role = "COMMERCANT";
            }else{
                $role = strtoupper($ligne["role"]);
            }

	    	$lignecsv = array($ligne["nom"],$ligne["prenom"],$ligne["numero"],$ligne["email"],$role);
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);


		break;
	case "export_liste_clients":
		if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
		if(isset($_GET["numero"]))	{$numero=$_GET["numero"];}else{$numero="";}
		if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}else{$restaurant="";}

        $req_sup = "";
        if($nom != ""){
            $req_sup .= " AND c.nom LIKE '%".$nom."%' ";		
        }
		if($numero != ""){
			$req_sup .= " AND c.numero LIKE '%".$numero."%' ";		
		}
		if($restaurant != ""){
			$req_sup .= " AND restaurant = '".$restaurant."' ";		
		}
	    
		$nomfic = 'exports/clients_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom','Prenom','Adresse','Numero','Email','Commercant');
		fputcsv($fp, $titre, ';');

		$vide = true;
	    $req = "SELECT c.*, r.nom as nom_resto FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 and r.statut = 1 ".$req_sup.$_SESSION["req_resto"];
        $result = $sql->query($req);
	    while($ligne = $result->fetch()) {
	    	$ligne = array_map('utf8_decode', $ligne);
	    	$lignecsv = array($ligne["nom"],$ligne["prenom"],$ligne["adresse"],$ligne["numero"],$ligne["email"],$ligne["nom_resto"]);
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;
	case "liste_clients":
		if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
		if(isset($_GET["numero"]))	{$numero=$_GET["numero"];}else{$numero="";}
		if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}else{$restaurant="";}
		if(isset($_GET["p"]))		{$p=$_GET["p"];}else{$p=1;}
		$nbaff = 30;
		$p = $p - 1;
		$pt = ($p*$nbaff);
		if($pt<0){$pt = 1;}
		?>
        <table class="table table-bordered table-hover" id="sample-table-1">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Numéro</th>
                    <th style="width:170px;">Commerçant</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $req_sup = "";
                if($nom != ""){
                    $req_sup .= " AND c.nom LIKE '%".$nom."%' ";		
                }
				if($numero != ""){
					$req_sup .= " AND c.numero LIKE '%".$numero."%' ";		
				}
				if($restaurant != ""){
					$req_sup .= " AND restaurant = '".$restaurant."' ";		
				}
                
				$vide = true;
                $req = "SELECT c.*, r.nom as nom_resto FROM clients c INNER JOIN restaurants r ON r.id=c.restaurant WHERE c.statut = 1 and r.statut = 1 ".$req_sup.$_SESSION["req_resto"]." LIMIT ".$pt.",".$nbaff;
                //echo $req;
				$result = $sql->query($req);
                while($ligne = $result->fetch()) {
					$vide = false;
                    $id = $ligne["id"];
					//$date_ajout = date("d/m/Y",strtotime($ligne["date_ajout"]));
					//$pseudo = ucfirst(strtolower($ligne["u_prenom"])).' '.strtoupper(substr($ligne["u_prenom"],0,1));
                    ?>
                    <tr>
                        <td><?php echo $ligne["prenom"].' '.$ligne["nom"]; ?></td>
                        <td><?php echo $ligne["adresse"]; ?></td>
                        <td><?php echo $ligne["numero"]; ?></td>
                        <td><?php echo $ligne["nom_resto"]; ?></td>
                        <td>
                            <a href="commandes_fiche.php?resto=<?=$ligne["restaurant"]?>&client=<?php echo $id ?>" class="btn btn-dark-green tooltips" data-placement="top" data-original-title="Passer commande"><i class="clip-pencil"></i></a>
                            <a href="clients_fiche.php?id=<?php echo $id ?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
                            <a onclick="affecte_suppid('<?php echo $id ?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>
                        </td>
                    </tr>                                            
                    <?php
                }
				if($vide){
					?>
					<tr>
                    	<td colspan="5">Aucun résultat disponible</td>
                    </tr>
					<?php	
				}
                ?>
            </tbody>                                	
        </table>                           
        
        <?php
		break;
	case "select_client":
		if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}else{$restaurant="";}
		if(isset($_GET["client"]))	{$client=$_GET["client"];}else{$client="";}
		?>
        <label class="col-sm-2 control-label" for="form-field-select-1">
            Client<span class="symbol required"></span>
        </label>
        <div class="col-sm-9 margin_label">
            <select name="client" id="client" class="form-control search-select">
                <option value="">&nbsp;</option>
                <?php
                $result = $sql->query("SELECT * FROM clients WHERE restaurant = '".$restaurant."' ORDER BY nom");	// WHERE etat!='6'
                while($ligne = $result->fetch()) {
                    if($client==$ligne["id"]){$sel = 'selected="selected"';}else{$sel = "";}
                    echo '<option value="'.$ligne["id"].'" '.$sel.'>'.$ligne["nom"].' '.$ligne["prenom"].' - '.$ligne["adresse"].'</option>';
                }
                ?>
            </select>
        </div>        
        <?php	
		break;
	case "export_liste_commandes":
		if(isset($_GET["histo"]))		{$histo=$_GET["histo"];}else{$histo="";}
		if(isset($_GET["statut"]))		{$statut=$_GET["statut"];}else{$statut="";}
		if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}else{$restaurant="";}

		$req_sup = "";
		if($restaurant != ""){
			$req_sup .= " AND c.restaurant = '".$restaurant."' ";		
		}
		if($statut != ""){
			$req_sup .= " AND c.statut = '".$statut."' ";		
		}else{
			if($histo=="0"){
				$req_sup .= " AND c.statut in ('ajouté','réservé','récupéré') ";						
			}else{
				$req_sup .= " AND c.statut in ('signé','echec') ";						
			}
		}
		if($_SESSION["role"]=="livreur"){
			$req_sup .= " AND c.livreur IN (0,".$_SESSION["userid"].")";
		}

		if($histo=="0"){
			$tri = " ORDER BY date_debut ASC ";						
		}else{
			$tri = " ORDER BY date_debut DESC ";						
		}

		$nomfic = 'exports/commandes_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom Commercant','Adresse Commercant','Nom Client','Prénom Client','Adresse Client','Numéro Client','Email Client','Créneau début','Créneau fin','Distance','Durée','Statut');
		$titre = array_map('utf8_decode', $titre);
		fputcsv($fp, $titre, ';');

		$vide = true;
        $req = "SELECT c.id,c.date_debut,c.date_fin,c.distance,c.duree,c.statut,r.nom as r_nom,r.adresse as r_adresse,l.nom,l.prenom,l.adresse,l.numero,l.email FROM commandes c INNER JOIN restaurants r ON c.restaurant=r.id INNER JOIN clients l ON c.client = l.id WHERE 1 ".$req_sup.$_SESSION["req_resto"]." ".$tri;
        //echo $req;
		$result = $sql->query($req);
        while($ligne = $result->fetch()) {
			$vide = false;
            $id = $ligne["id"];
			$statut= $ligne["statut"];
			$couleur_statut = couleur_statut($statut);

			$distance = $ligne["distance"];
			$duree = $ligne["duree"];
			$distance_km = round($distance/1000,0).' km';
			$duree_h = gmdate("H",$duree);
			$duree_m = gmdate("i",$duree);
			if($duree_h>0){
			$duree_aff = $duree_h."h".$duree_m;
			}else{
			$duree_aff = $duree_m." min.";
			}
			
	    	$ligne = array_map('utf8_decode', $ligne);
	    	$lignecsv = array($ligne["r_nom"],$ligne["r_adresse"],$ligne["nom"],$ligne["prenom"],$ligne["adresse"],$ligne["numero"],$ligne["email"],$ligne["date_debut"],$ligne["date_fin"],$distance_km,$duree_aff,$ligne["statut"]);
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);


		break;
	case "liste_commandes":
		if(isset($_GET["histo"]))		{$histo     = $_GET["histo"];}      else{$histo="";}
		if(isset($_GET["statut"]))		{$statut    = $_GET["statut"];}     else{$statut="";}
		if(isset($_GET["restaurant"]))	{$restaurant= $_GET["restaurant"];} else{$restaurant="";}
		if(isset($_GET["p"]))		    {$p         = $_GET["p"];}          else{$p=1;}

		$nbaff  = 30;
		$p      = $p - 1;
        $pt     = ($p*$nbaff);

        if($pt<0){$pt = 1;} ?>

        <table class="table table-bordered table-hover" id="sample-table-1">
            <thead>
                <tr>
                    <th>Commerçant</th>
                    <th>Infos client</th>
                    <th>Contact client</th>
                    <th>Créneau de livraison</th>
                    <th style="width:80px;">Infos</th>
                    <th style="width:50px;">Statut</th>
                    <th style="width:190px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
				$req_sup = "";
				if($restaurant != ""){
					$req_sup .= " AND c.restaurant = '".$restaurant."' ";		
				}
				if($statut != ""){
					$req_sup .= " AND c.statut = '".$statut."' ";		
				}else{
					if($histo=="0"){
						$req_sup .= " AND c.statut in ('ajouté','réservé','récupéré') ";						
					}else{
						$req_sup .= " AND c.statut in ('signé','echec') ";						
					}
				}
				if($_SESSION["role"]=="livreur"){
					$req_sup .= " AND c.livreur IN (0,".$_SESSION["userid"].")";
				}

				if($histo=="0"){
					$tri = " ORDER BY date_debut ASC ";						
				}else{
					$tri = " ORDER BY date_debut DESC ";						
				}

				$vide = true;
                $req = "SELECT c.id,c.date_debut,c.date_fin,c.distance,c.duree,c.statut,r.nom as r_nom,r.adresse as r_adresse,l.nom,l.prenom,l.adresse,l.numero,l.email FROM commandes c INNER JOIN restaurants r ON c.restaurant=r.id INNER JOIN clients l ON c.client = l.id WHERE 1 ".$req_sup.$_SESSION["req_resto"]." ".$tri." LIMIT ".$pt.",".$nbaff;
                //echo $req;
				$result = $sql->query($req);
                while($ligne = $result->fetch()) {
					$vide = false;
                    $id = $ligne["id"];
					$statut= $ligne["statut"];
					$couleur_statut = couleur_statut($statut);

					$distance = $ligne["distance"];
					$duree = $ligne["duree"];
					$distance_km = round($distance/1000,0).' km';
					$duree_h = gmdate("H",$duree);
					$duree_m = gmdate("i",$duree);
					if($duree_h>0){
					$duree_aff = $duree_h."h".$duree_m;
					}else{
					$duree_aff = $duree_m." min.";
					}
					
					$aff_modif = false;
					if($statut=="ajouté" || $statut=="réservé"){
						$aff_modif = true;
					}
					//$date_ajout = date("d/m/Y",strtotime($ligne["date_ajout"]));
					//$pseudo = ucfirst(strtolower($ligne["u_prenom"])).' '.strtoupper(substr($ligne["u_prenom"],0,1));
                    ?>
                    <tr>
                        <td><?php echo '<b>'.$ligne["r_nom"].'</b><br/>'.$ligne["r_adresse"]; ?></td>
                        <td><?php echo '<b>'.$ligne["prenom"].' '.$ligne["nom"].'</b><br/>'.$ligne["adresse"]; ?></td>
                        <td><?php echo $ligne["numero"].'<br/>'.$ligne["email"]; ?></td>
                        <td><?php echo "Le ".date('d/m/Y',strtotime($ligne["date_debut"]))."<br/>Entre ".date('H\hi',strtotime($ligne["date_debut"]))." et ".date('H\hi',strtotime($ligne["date_fin"])); ?></td>
                        <td style="text-align:right;"><?php echo $distance_km.'<br/>'.$duree_aff; ?></td>
                        <td><?php echo '<span class="label '.$couleur_statut.'">'.ucfirst(txt_statut($statut)).'</span>'; ?></td>
                        <td>
                        	<?php
							if($_SESSION["role"]=="livreur"){
							?>
                            <a href="commandes_visu.php?id=<?php echo $id ?>" class="btn btn-primary tooltips" data-placement="top" data-original-title="Visualiser"><i class="clip-search"></i></a>
							<?php								
							}else{
							?>
							<a href="commande_affecter.php?livreur=" class="btn btn-main tooltips" data-placement="top" data-original-title="Affecter une commande"><img src="images/give_card.png" style="width:14px;"/></a>
                            <a href="commandes_visu.php?id=<?php echo $id ?>" class="btn btn-primary tooltips" data-placement="top" data-original-title="Visualiser"><i class="clip-search"></i></a>
                            <?php if($aff_modif){ ?>
                            <a href="commandes_fiche.php?id=<?php echo $id ?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
                            <?php 
							} 
							if($statut=="ajouté"){
							?>
                            <a onclick="affecte_suppid('<?php echo $id ?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>
                            <?php 
							} 
							}
							?>
                        </td>
                    </tr>                                            
                    <?php
                }
				if($vide){
					?>
					<tr>
                    	<td colspan="7">Aucun résultat disponible</td>
                    </tr>
					<?php	
				}
                ?>
            </tbody>                                	
        </table>                           
        
        <?php
		break;
	case "take_commande":
		if($_SESSION["userid"]!=""){
			if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
			if(is_numeric($id)){
				$result = $sql->query("SELECT * FROM `commandes` c WHERE c.id = ".$sql->quote($id)." AND statut = 'ajouté' LIMIT 1");
				$ligne = $result->fetch();
				if($ligne!=""){			
					$result = $sql->exec("UPDATE commandes SET livreur = '".$_SESSION["userid"]."', statut = 'réservé', date_statut = NOW() WHERE statut = 'ajouté' AND id = '".$id."'");		

					$res = send_notif($id,$sql);


					header("location: commandes_visu.php?aff_valide=1&id=".$id);	
					exit();
				}else{
					header("location: commandes_visu.php?aff_valide=-2&id=".$id);					
					exit();
				}
			}				
		}else{
			header("location: index.php?cmd=".$id);
			exit();
		}
		break;
	case "detake_commande":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
		if(is_numeric($id)){
			$result = $sql->exec("UPDATE commandes SET livreur = '0', statut = 'ajouté', date_statut = NULL WHERE statut = 'réservé' AND id = '".$id."'");		
		}	
		header("location: commandes_visu.php?aff_valide=-1&id=".$id);	
		break;

	case "recup_commande":
		if($_SESSION["userid"]!=""){		
			if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
			if(is_numeric($id)){
				$result = $sql->exec("UPDATE commandes SET livreur = '".$_SESSION["userid"]."', statut = 'récupéré', date_statut = NOW() WHERE statut = 'réservé' AND id = '".$id."'");		
				$res    = send_notif($id,$sql);
				$result = $sql->query("SELECT l.nom,l.prenom,l.email,l.numero, r.nom FROM `commandes` c inner join clients l on c.client = l.id inner join restaurants r on r.id = c.restaurant WHERE c.id = ".$sql->quote($id)." LIMIT 1");
				$ligne  = $result->fetch();

				if($ligne!=""){
					$email_membre   = $ligne["email"];
					$nom_resto      = $ligne["nom"];
					$numero_membre  = $ligne["numero"];

					$body = "";
					$body .= 'Bonjour, <br/><br/>
								Votre commande de <b>'.$nom_resto.'</b> est en cours de livraison : <br/>
								<a href="http://www.youorder.fr/mobile/?id='.$id.'">Suivre la commande</a><br/><br/>
								Merci,<br/>
								L\'équipe YouOrder';
				 
				   // On créé une nouvelle instance de la classe
				   require_once('PHPMailer/class.phpmailer.php');
				   $mail = new PHPMailer();
				   $mail->From = "contact@youorder.fr";
				   $mail->Sender = "contact@youorder.fr";
				   $mail->FromName = "YouOrder";
				   $mail->Subject = "Votre livraison ".$nom_resto;
				   $mail->MessageID = newChaine(6).".".newChaine(6)."@youorder.fr";
				   $mail->MsgHTML($body);
				   $mail->CharSet = 'UTF-8';	
				   $mail->AddReplyTo("contact@youorder.fr","YouOrder");
				   $mail->AddAddress($email_membre, "");
				   //$mail->AddBCC("guillaume@mgmobile.fr","");
				   $mail->send();

				   if($numero_membre!=""){
					require('lib-mobytsms.inc.php');

				    $numero = "+33".substr($numero_membre,-9);	
				    $texte = "Bonjour, Votre commande ".$nom_resto." est en cours de livraison. Suivre la commande: www.youorder.fr/mobile/?id=".$id." A bientot";
					$sms = new mobytSms('F09623', 'sb48i90v');
					
					$sms->setAuthMd5();
					$sms->setFrom("YouOrder");
					$sms->setDomaine('http://multilevel.mobyt.fr');
					
					$sms->setQualityTop();
					
					$result = $sms->sendSms($numero, $texte, 'TEXT', '','1');
				   }
				}	
			}	
			header("location: commandes_visu.php?aff_valide=2&id=".$id);
			exit();
		}else{
			header("location: index.php?cmd=".$id);
			exit();
		}		
		break;	
	case "refus_commande":
		if($_SESSION["userid"]!=""){		
			if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}	
			if(isset($_GET["raison"]))	{$raison=$_GET["raison"];}else{$raison="";}	
			if(isset($_GET["comm"]))	{$comm=$_GET["comm"];}else{$comm="";}	
			if(is_numeric($id)){
				$result = $sql->exec("UPDATE commandes SET livreur = '".$_SESSION["userid"]."', statut = 'echec', raison_refus='".$raison."', comm_refus=".$sql->quote($comm).", date_statut = NOW() WHERE id = '".$id."'");
				$res = send_notif($id,$sql);
			}	
			header("location: commandes_visu.php?aff_valide=2&id=".$id);	
		}else{
			header("location: index.php?cmd=".$id);
			exit();
		}		
		break;	
}
?>