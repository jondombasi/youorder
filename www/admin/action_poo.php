<?php
require_once("inc_connexion.php");
date_default_timezone_set('Europe/Paris');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

if(isset($_GET["action"]))		{$action=$_GET["action"];}else{
	if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}
}

switch($action){
	case "liste_vehicule":
		if(isset($_GET["p"]))               {$page                  = $_GET["p"];}                  else{$page=1;}
		if(isset($_GET["type"]))		    {$type_get              = $_GET["type"];}               else{$type_get="";}
		if(isset($_GET["immatriculation"]))	{$immatriculation_get   = $_GET["immatriculation"];}    else{$immatriculation_get="";}

		$liste_vehicule = new Vehicule($sql);
		$vehicules      = $liste_vehicule->getAll($page,30, $type_get, $immatriculation_get);

		foreach ($vehicules as $vehicule) {
			?>
			<tr>
				<td><?=$vehicule->type;?></td>
				<td><?=$vehicule->nom;?></td>
				<td><?=$vehicule->immatriculation;?></td>
                <td><?=$vehicule->kilometrage;?></td>
				<td><?=$vehicule->marque;?></td>
				<td><?=$vehicule->volume;?></td>
				<td>
					<?php
					if ($vehicule->etat=="ok") {
						echo "<span class='label label-main'>En fonctionnement</span>";
					}
					else if ($vehicule->etat=="maintenance") {
						echo "<span class='label label-warning'>En maintenance</span>";
					}
					else if ($vehicule->etat=="nonrestitue") {
						echo "<span class='label label-danger'>Non restitué</span>";
					}
					else {
						echo "<span class='label label-danger'>HS</span>";
					}
					?>
				</td>
				<td>
					<a href="vehicules_fiche.php?id=<?=$vehicule->id?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
					<!--<a href="livreurs_planning.php?vehicule_get=<?=$vehicule->id?>" class="btn btn-green tooltips" data-placement="top" data-original-title="Planning"><i class="fa fa-calendar"></i></a>-->
					<a href="vehicules_fiche2.php?id=<?=$vehicule->id?>" class="btn btn-warning tooltips" data-placement="top" data-original-title="Historique"><i class="fa fa-history"></i></a>
                    <a href="vehicule_operation_fiche.php?id=<?=$vehicule->id?>" class="btn btn-green tooltips" data-placement="top" data-original-title="Operation"> <i class="fa fa-wrench"></i></a>


                    <a onclick="affecte_suppid('<?=$vehicule->id?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>

				</td>
			</tr>
			<?php
		}
		break;

    case "liste_operation":

        if(isset($_GET["p"]))               {$page=$_GET["p"];}                                 else{$page=1;}
        if(isset($_GET["type"]))		    {$type_get=$_GET["type"];}                          else{$type_get="";}
        if(isset($_GET["immatriculation"]))	{$immatriculation_get=$_GET["immatriculation"];}    else{$immatriculation_get="";}

        $liste_operation    = new Operation($sql);
        $operations         = $liste_operation->getAll($page,30, $type_get, $immatriculation_get);


        foreach ($operations as $operation) {
            $vehicule       = new Vehicule($sql, $operation->id_vehicule);
            $historiques    = $vehicule->getHistoriqueOperation($operation->id, '', $page);
            foreach ($historiques as $historique) {
                ?>
                <tr>
                    <td><?="Le ".date("d/m/Y \à H:i", strtotime($historique->date));?></td>
                    <td><?=$historique->prenom_admin." ".$historique->nom_admin;?></td>
                    <td><?=$vehicule->getImmatriculation()?></td>
                    <td><?=$vehicule->getKilometrage()?></td>
                    <td>
                        <?php foreach ($liste_operation->getActions($historique->id_operation) as $action): ?>
                            - <?= $action->libelle?> <br>
                        <?php endforeach;?>
                    </td>
                    <td>
                        <?php foreach ($liste_operation->getPieces($historique->id_operation) as $piece): ?>
                            - <?= $piece->code?>  <br>
                        <?php endforeach;?>
                    </td>
                    <td><?=$historique->commentaire?></td>
                    <td>
                        <a onclick="affecte_suppid('<?= $operation->id ?>')" href="#myModal3" role="button" data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer l'opération"> <i class="fa fa-times fa fa-white"></i></a>
<!--                        <a href="vehicules_fiche.php?id=--><?//=$vehicule->getImmatriculation()?><!--" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>-->
                        <a href="vehicule_operation_fiche.php?id=<?=$operation->id?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier l'opération"><i class="fa fa-edit"></i></a>

                    </td>
                </tr>
                <?php
            }
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="4">Pas de résultats</td>
            </tr>
            <?php
        }
        break;


    case "export_operation":
        if(isset($_GET["p"]))               {$page                  = $_GET["p"];}              else{$page=1;}
        if(isset($_GET["type"]))		    {$type_get              = $_GET["type"];}           else{$type_get="";}
        if(isset($_GET["immatriculation"]))	{$immatriculation_get   = $_GET["immatriculation"];}else{$immatriculation_get="";}

        $nomfic             = 'exports/Operation'.date("YmdHis").'.csv';
        $fp                 = fopen($nomfic, 'w');

        $titre              = array('Date operation','Numero du vehicule','user','Actions émise','Pièces utilisé', 'commentaire');
        fputcsv($fp, $titre, ';');

        $liste_operation    = new Operation($sql);
        $operations=$liste_operation->getAll($page,30, $type_get, $immatriculation_get);

        foreach ($operations as $operation) {
            $vehicule       = new Vehicule($sql, $operation->id_vehicule);
            $historiques    = $vehicule->getHistoriqueOperation($operation->id, '', $page);

            foreach ($historiques as $historique) {
                $lignecsv   = array(date("d/m/Y", strtotime($historique->date)), $vehicule->getImmatriculation(), utf8_decode($historique->prenom_admin." ".$historique->nom_admin), utf8_decode($action->libelle), utf8_decode($piece->code));
                fputcsv($fp, $lignecsv, ';');
            }
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;

    case "liste_vehicule_planning_histo":
        if(isset($_GET["p"]))           {$page          = $_GET["p"];}          else{$page=1;}
        if(isset($_GET["id_vehicule"])) {$id_vehicule   = $_GET["id_vehicule"];}else{$id_vehicule="";}

        $liste_historique   = new Vehicule($sql);
        $historiques        = $liste_historique->getHistorique($id_vehicule, 30, $page);
        $operation          = new Operation($sql);

        $vide               = true;
        foreach ($historiques as $historique) {
            $vide=false;
            ?>
            <tr>
                <td><?="Le ".date("d/m/Y \à H:i", strtotime($historique->date));?></td>
                <?php if ($historique->id_operation == '0'):?>
                    <td><img src="images/deliver.png" alt="icon" style="width:15px;margin-right:5px"/> <?=$historique->prenom_livreur." ".$historique->nom_livreur;?></td>
                <?php else:?>
                    <td>
                        <?php foreach ($operation->getActions($historique->id_operation) as $action): ?>
                            - <?= $action->libelle?> <br>
                        <?php endforeach;?>
                    </td>
                <?php endif;?>
                <td><?=$historique->prenom_admin." ".$historique->nom_admin;?></td>
                <td>
                    <?php
                    if ($historique->etat=="ok") {
                        echo "<span class='label label-main'>En fonctionnement</span>";
                    }
                    else if ($historique->etat=="maintenance") {
                        echo "<span class='label label-warning'>En maintenance</span>";
                    }
                    else if ($historique->etat=="nonrestitue") {
                        echo "<span class='label label-danger'>Non restitué</span>";
                    }
                    else {
                        echo "<span class='label label-danger'>HS</span>";
                    }
                    ?>
                </td>
                <td><?=$historique->commentaire?></td>
            </tr>
            <?php
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="4">Pas de résultats</td>
            </tr>
            <?php
        }
        break;


	case "export_liste_vehicules":
	    if(isset($_GET["id_vehicule"]))     {$id_vehicule       = $_GET["id_vehicule"];}    else{$id_vehicule="";}
	    if(isset($_GET["type"]))            {$type              = $_GET["type"];}           else{$type="";}
	    if(isset($_GET["immatriculation"])) {$immatriculation   = $_GET["immatriculation"];}else{$immatriculation="";}

	    $nomfic         = 'exports/vehicules_'.date("YmdHis").'.csv';
		$fp             = fopen($nomfic, 'w');

		$titre          = array('Type','Nom','Immatriculation','Marque','Volume', 'Etat');
		fputcsv($fp, $titre, ';');

	    $liste_vehicule = new Vehicule($sql);
		$vehicules      = $liste_vehicule->getAll("", "", $type, $immatriculation);

		foreach ($vehicules as $vehicule) {
	    	$lignecsv = array(utf8_decode($vehicule->type), utf8_decode($vehicule->nom), utf8_decode($vehicule->immatriculation), utf8_decode($vehicule->marque), utf8_decode($vehicule->volume), utf8_decode($vehicule->etat));
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;

	case "export_presences":
	    if(isset($_GET["id_vehicule"])){$id_vehicule=$_GET["id_vehicule"];}else{$id_vehicule="";}

	    $nomfic         = 'exports/vehicules_histo_'.date("YmdHis").'.csv';
		$fp             = fopen($nomfic, 'w');

		$titre          = array('Nom', 'Type', 'Immatriculation','Date','Actions','Modifie par','Etat','Commentaire');
		fputcsv($fp, $titre, ';');

	    $liste_vehicule = new Vehicule($sql, $id_vehicule);
		$vehicules      = $liste_vehicule->getHistorique($id_vehicule, "", "");

		foreach ($vehicules as $vehicule) {
	    	$lignecsv = array(utf8_decode($liste_vehicule->getNom()), utf8_decode($liste_vehicule->getType()), utf8_decode($liste_vehicule->getImmatriculation()), utf8_decode($vehicule->date), utf8_decode($vehicule->prenom_livreur." ".$vehicule->nom_livreur), utf8_decode($vehicule->prenom_admin." ".$vehicule->nom_admin), utf8_decode($vehicule->etat), utf8_decode($vehicule->commentaire));
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;

	case "export_planning_vehicules":
	    if(isset($_GET["id_vehicule"])) {$id_vehicule   = $_GET["id_vehicule"];}else{$id_vehicule="";}
	    if(isset($_GET["week_start"]))  {$week_start    = $_GET["week_start"];} else{$week_start="";}
	    if(isset($_GET["week_end"]))    {$week_end      = $_GET["week_end"];}   else{$week_end="";}

	    $nomfic         = 'exports/planning_'.date("YmdHis").'.csv';
		$fp             = fopen($nomfic, 'w');

		$titre          = array('Date debut','Date fin','Actions','Modifie par','Etat');
		fputcsv($fp, $titre, ';');

	    $liste_planning = new Vehicule($sql);
		$plannings      = $liste_planning->getPlanning($id_vehicule, $week_start, $week_end);

		foreach ($plannings as $planning) {
	    	$lignecsv = array(date("d/m/Y \a H:i", strtotime($planning->h_debut)), date("d/m/Y \a H:i", strtotime($planning->h_fin)), utf8_decode($planning->prenom_livreur." ".$planning->nom_livreur), utf8_decode($planning->prenom_admin." ".$planning->nom_admin), utf8_decode($planning->etat));
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;



    /*  Materiel's Actions*/

    case "liste_materiel":
        if(isset($_GET["p"]))           {$page          = $_GET["p"];}       else{$page=1;}
        if(isset($_GET["code"]))		{$code_get      = $_GET["type"];}    else{$code_get="";}
        if(isset($_GET["libelle"]))		{$libelle_get   = $_GET["libelle"];} else{$libelle_get="";}

        $liste_materiel = new Materiel($sql);
        $materiels      = $liste_materiel->getAll($page,30, $code_get, $libelle_get);

        foreach ($materiels as $materiel) {
            ?>
            <tr>
                <td><?=$materiel->code;?></td>
                <td><?=$materiel->libelle;?></td>
                <td><?=$materiel->quantite;?></td>
                <td><?=$materiel->prix_ht;?></td>
                <td>
                    <a href="piece_fiche.php?id=<?=$materiel->id?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
                    <!--<a href="piece_historique.php?id=<?=$materiel->id?>" class="btn btn-warning tooltips" data-placement="top" data-original-title="Historique"><i class="fa fa-history"></i></a>-->
                    <a onclick="affecte_suppid('<?=$materiel->id?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>

                </td>
            </tr>
            <?php
        }
        break;

    /* END Materiel's Actions*/




	case "liste_livreur":
		if(isset($_GET["p"]))       {$page  = $_GET["p"];}      else{$page=1;}
		if(isset($_GET["nom"]))		{$nom   = $_GET["nom"];}    else{$nom="";}
		if(isset($_GET["statut"]))	{$statut= $_GET["statut"];} else{$statut="";}
		if(isset($_GET["numero"]))	{$numero= $_GET["numero"];} else{$numero="";}

		$vide           = true;

		$liste_livreur  = new Livreur($sql);
		$livreurs       = $liste_livreur->getAll($page,30, $nom, $statut, $numero);

		foreach ($livreurs as $livreur) {
			$vide=false;
			?>
			<tr>
				<td style="text-align:center"><?=$livreur->nom;?></td>
				<td style="text-align:center"><?=$livreur->prenom;?></td>
				<td style="text-align:center"><?=$livreur->telephone;?></td>
				<td style="text-align:center"><?=$livreur->nb_heures;?></td>
				<td style="text-align:center">
					<?php
					if ($livreur->statut=="ON") {
						echo "<span class='label label-main'>ON</span>";
					}
					else {
						echo "<span class='label label-main-grey'>OFF</span>";
					}
					?>
				</td>
                <td></td>
				<td>
					<a href="livreurs_planning.php?livreur_get=<?=$livreur->id?>" class="btn btn-green tooltips"    data-placement="top" data-original-title="Planning">            <i class="fa fa-calendar "></i></a>
                    <a href="shift_livreur.php?id=<?=$livreur->id?>             " class="btn btn-purple tooltips"   data-placement="top" data-original-title="Activité du livreur"> <i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                    <a href="livreurs_fiche2.php?id=<?=$livreur->id?>           " class="btn btn-primary tooltips"  data-placement="top" data-original-title="Fiche du livreur">    <i class="fa fa-search"></i></a>
					<a onclick="affecte_suppid('<?=$livreur->id?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>

				</td>
			</tr>
			<?php
		}
		if ($vide) {
			?>
			<tr>
				<td colspan="6">Pas de résultats</td>
			</tr>
			<?php
		}
		break;

	case "export_liste_livreurs":
	    if(isset($_GET["nom"]))		{$nom   = $_GET["nom"];}    else{$nom="";}
		if(isset($_GET["statut"]))  {$statut= $_GET["statut"];} else{$statut="";}
		if(isset($_GET["numero"]))	{$numero= $_GET["numero"];} else{$numero="";}

	    $nomfic         = 'exports/livreurs_'.date("YmdHis").'.csv';
		$fp             = fopen($nomfic, 'w');

		$titre          = array('Nom','Prenom','Numero', 'Email','Nb d\'heures par semaine', 'Statut');
		fputcsv($fp, $titre, ';');

	    $liste_livreur  = new Livreur($sql);
		$livreurs       = $liste_livreur->getAll("", "",$nom, $statut, $numero);

		foreach ($livreurs as $livreur) {
	    	$lignecsv = array(utf8_decode($livreur->nom), utf8_decode($livreur->prenom), $livreur->telephone, utf8_decode($livreur->email),$livreur->nb_heures, $livreur->statut);
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;


		// START ACTION COMMANDE
	case "liste_commande":
		if(isset($_GET["p"]))           {$page      = $_GET["p"];}          else{$page=1;}
		if(isset($_GET["id_livreur"]))	{$id_livreur= $_GET["id_livreur"];} else{$id_livreur=0;}
		if(isset($_GET["restaurant"]))	{$restaurant= $_GET["restaurant"];} else{$restaurant="";}
		if(isset($_GET["statut"]))		{$statut    = $_GET["statut"];}     else{$statut="";}
		if(isset($_GET["periode"]))		{$periode   = $_GET["periode"];}    else{$periode="";}
		if(isset($_GET["histo"]))		{$histo     = $_GET["histo"];}      else{$histo="";}

		$vide=true;

		$Commande = new Commande($sql);
		$liste_commandes=$Commande->getAll($page, 30, $id_livreur, $restaurant, $statut, $periode, $histo);

		foreach ($liste_commandes as $commande) {
			$vide=false;
			$distance_km = round($commande->distance/1000,0);
			$duree_h = gmdate("H",$commande->duree);
			$duree_m = gmdate("i",$commande->duree);
			$duree_aff=($duree_h>0) ? $duree_h."h".$duree_m : $duree_m." min";

			$Livreur = new Livreur($sql, $commande->livreur);
			?>
			<tr>
                <td style="width: 50px;" ><?= "<b>".$commande->id?></td>
				<td><?="<b>".$commande->nom_resto."</b><br/>".$commande->adresse_resto?></td>
				<td><?="<b>".$Livreur->getPrenom()." ".$Livreur->getNom()."</b>"?></td>
				<td><?="<b>".$commande->prenom_client." ".$commande->nom_client."</b><br/>".$commande->adresse_client?></td>
				<td style="width:110px;"><?=date("d/m/Y", strtotime($commande->date_debut))?><br><?=date("H\hi", strtotime($commande->date_debut))?> et <?=date("H\hi", strtotime($commande->date_fin))?></td>
				<td><?=$distance_km." km<br/>".$duree_aff?></td>
				<td><span class="label <?=couleur_statut($commande->statut)?>"><?=ucfirst(txt_statut($commande->statut))?></span></td>
				<td>
					<?php if (($commande->statut=="ajouté" || $commande->statut=="réservé") && ($_SESSION["admin"] || $_SESSION["planner"] || ($_SESSION["role"]=="restaurateur" && $commande->affecter_commande=="on"))) { ?>
						<a onclick="openPopup('affecte_livreur_fiche.php?id_commande=<?=$commande->id?>&id_livreur=<?=$commande->livreur?>&page=<?=$page?>')" href="javascript:void(0)" class="btn btn-green tooltips" data-placement="top" data-original-title="Affecter un livreur"><i class="fa fa-calendar"></i></a>
					<?php } ?>
						<a href="commandes_visu.php?id=<?=$commande->id?>" class="btn btn-primary tooltips" data-placement="top" data-original-title="Voir la commande"><i class="fa fa-search"></i></a>
					<?php if ($commande->statut=="ajouté" || $commande->statut=="réservé") { ?>
						<a href="commandes_fiche.php?id=<?=$commande->id?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier la commande"><i class="fa fa-edit"></i></a>
						<a onclick="affecte_suppid('<?=$commande->id?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>
					<?php } ?>
				</td>
			</tr>
			<?php
		}
		if ($vide) {
			?>
			<tr>
				<td colspan="7">Pas de résultats</td>
			</tr>
			<?php
		}
		break;

	case "suppcommande":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
		if(is_numeric($id)){
			//$result = $sql->exec("DELETE FROM commandes WHERE id = '".$id."'");
			$result = $sql->exec("UPDATE commandes SET statut='supprime' WHERE id = '".$id."'");

			$Commande   = new Commande($sql, $id);
			$Livreur    = new Livreur($sql, $Commande->getLivreur());
			$Commercant = new Commercant($sql, $Commande->getRestaurant());

			$message="Annulation de la commande ".$Commercant->getNom()." à livrer entre ".date("H:i", strtotime($Commande->getDateDebut())).' et '.date("H:i", strtotime($Commande->getDateFin()));
            $url="commandes.html";
            $envoi=file('http://www.you-order.eu/admin/action_poo.php?action=send_push&id='.$Commande->getLivreur().'&message='.urlencode($message).'&url='.urlencode($url));
		}

		header("location: commandes_liste.php");
		break;

	case "export_liste_commandes":
	    if(isset($_GET["id_livreur"]))	{$id_livreur= $_GET["id_livreur"];} else{$id_livreur="";}
		if(isset($_GET["commercant"]))	{$commercant= $_GET["commercant"];} else{$commercant="";}
		if(isset($_GET["statut"]))		{$statut    = $_GET["statut"];}     else{$statut="";}
		if(isset($_GET["periode"]))		{$periode   = $_GET["periode"];}    else{$periode="";}
		if(isset($_GET["histo"]))		{$histo     = $_GET["histo"];}      else{$histo="";}

	    $nomfic = 'exports/commandes_'.date("YmdHis").'.csv';
		$fp     = fopen($nomfic, 'w');

		$titre  = array('Commercant','Info client','Contact client', 'Date',utf8_decode('Créneau début'), utf8_decode('Créneau fin'),'Infos', 'Statut');
		fputcsv($fp, $titre, ';');

	    $livreur    = new Livreur($sql, $id_livreur);
		$Commande   = new Commande($sql);
		$commandes  = $Commande->getAll($page, 30, $id_livreur, $commercant, $statut, $periode, $histo);

		foreach ($commandes as $commande) {
			$distance_km    = round($commande->distance/1000,0);
			$duree_h        = gmdate("H",$commande->duree);
			$duree_m        = gmdate("i",$commande->duree);
			$duree_aff      = ($duree_h>0) ? $duree_h."h".$duree_m : $duree_m." min";

	    	$lignecsv = array("".utf8_decode($commande->nom_resto)." \r\r".utf8_decode($commande->adresse_resto)."", "".utf8_decode($commande->prenom_client." ".$commande->nom_client)." \r\r".utf8_decode($commande->adresse_client)."", "".utf8_decode($commande->numero_client)." \r\r".utf8_decode($commande->email_client)."", date("d/m/Y", strtotime($commande->date_debut)), date("H:i", strtotime($commande->date_debut)), date("H:i", strtotime($commande->date_fin)),$distance_km."km \r\r".$duree_aff, utf8_decode($commande->statut));
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;
    // END ACTON COMMANDE


	case "heures_sup":
		$livreur    = new Livreur($sql, $_POST["id"]);
		$vehicule   = new Vehicule($sql, $_POST["vehicule_hsup"]);

		if ($vehicule->checkVehicule($_POST["vehicule_hsup"], $_POST["id"], date("Y-m-d", strtotime($_POST["date_hsup"]))." ".$_POST["h_debut_hsup"], date("Y-m-d", strtotime($_POST["date_hsup"]))." ".$_POST["h_fin_hsup"]) && $_POST["vehicule_hsup"]!="" && $_POST["vehicule_hsup"]!=0) {
			echo "erreur";
		}
		else {
			$livreur->setPlanningPresence($_POST["id"], $_POST["commercant_hsup"], $_POST["vehicule_hsup"], date("Y-m-d", strtotime($_POST["date_hsup"]))." ".$_POST["h_debut_hsup"], date("Y-m-d", strtotime($_POST["date_hsup"]))." ".$_POST["h_fin_hsup"], "manuel");
			echo "ok";
		}
		break;

	case "liste_planning_calendar_presence":
		if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];} else{$id_livreur="";}
		if(isset($_GET["week_start"]))  {$week_start    = $_GET["week_start"];} else{$week_start="";}
	    if(isset($_GET["week_end"]))    {$week_end      = $_GET["week_end"];}   else{$week_end="";}

	    $vide       = true;

	    $Livreur    = new Livreur($sql);
	    $plannings  = $Livreur->getPlanningPresence($id_livreur, $week_start, $week_end);

	    ?>
	    <span id="calendar_presence_periode" style="position:absolute;left:30px;top:60px"></span>
    	<tabe class="table table-bordered table-hover" style="margin-top:50px;">
    		<thead>
        		<th>Livreur</th>
        		<th>Commerçant</th>
        		<th>Véhicule</th>
        		<th>Date</th>
        		<th>Heure de début</th>
        		<th>Heure de fin</th>
        		<th>Type</th>
        	</thead>
        	<tbody>
			    <?php
				foreach ($plannings as $planning) {
					$vide=false;
					?>
					<tr>
						<td><?=$planning->prenom_livreur." ".$planning->nom_livreur?></td>
						<td><?="<b>".$planning->nom_resto."</b><br/>".$planning->adresse_resto?></td>
						<td><?=$planning->nom_vehicule?></td>
						<td><?=date("d/m/y", strtotime($planning->date_connexion))?></td>
						<td><?=date("H:i", strtotime($planning->date_connexion))?></td>
						<td><?=date("H:i", strtotime($planning->date_deconnexion))?></td>
						<td><?=($planning->type=="appli") ? "Présence" : "Heure supplémentaire" ;?></td>
					</tr>
					<?php
				}
				if ($vide) {
					?>
					<tr>
						<td colspan="7">Pas de résultats</td>
					</tr>
					<?php
				}
			?>
			</tbody>
        </tabe>
        <?php
		break;

	case "count_hour_presence":
		if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];}  else{$id_livreur="";}
		if(isset($_GET["week_start"]))  {$week_start    = $_GET["week_start"];}  else{$week_start="";}
	    if(isset($_GET["week_end"]))    {$week_end      = $_GET["week_end"];}      else{$week_end="";}

	    $Livreur    = new Livreur($sql);
	    $plannings  = $Livreur->getHoursPresence($id_livreur, $week_start, $week_end);

	    echo $plannings;
		break;

	case "count_hour_sup":
		if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];} else{$id_livreur="";}
		if(isset($_GET["week_start"]))  {$week_start    = $_GET["week_start"];} else{$week_start="";}
	    if(isset($_GET["week_end"]))    {$week_end      = $_GET["week_end"];}   else{$week_end="";}

	    $Livreur    = new Livreur($sql);
	    $plannings  = $Livreur->getHoursSup($id_livreur, $week_start, $week_end);

	    echo $plannings;
		break;

	case "export_calendar_presence":
		if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];} else{$id_livreur="";}
		if(isset($_GET["week_start"]))  {$week_start    = $_GET["week_start"];} else{$week_start="";}
	    if(isset($_GET["week_end"]))    {$week_end      = $_GET["week_end"];}   else{$week_end="";}

	    $nomfic = 'exports/presence_'.date("YmdHis").'.csv';
		$fp     = fopen($nomfic, 'w');

		$titre  = array('Livreur','Commercant','Vehicule','Date', 'Heure debut', 'Heure fin', 'Type');
		fputcsv($fp, $titre, ';');

	    $Livreur=new Livreur($sql, $id_livreur);
	    $plannings      = $Livreur->getPlanningPresence($id_livreur, $week_start, $week_end);
	    $nb_heures      = $Livreur->getNbHeures();
	    $heure_presence = $Livreur->getHoursPresence($id_livreur, $week_start, $week_end);
		$heure_sup      = $Livreur->getHoursSup($id_livreur, $week_start, $week_end);

	    foreach ($plannings as $planning) {
	    	$lignecsv = array(utf8_decode($planning->prenom_livreur)." ".utf8_decode($planning->nom_livreur), "".utf8_decode($planning->nom_resto)." \r\r".utf8_decode($planning->adresse_resto)."", $planning->nom_vehicule, date("d/m/Y", strtotime($planning->date_connexion)), date("H:i", strtotime($planning->date_connexion)), date("H:i", strtotime($planning->date_deconnexion)), $planning->type);
		    fputcsv($fp, $lignecsv, ';');
	    }

	    fputcsv($fp, array('','','','', '', '', ''), ';');
		fputcsv($fp, array(utf8_decode('Nombre d\'heures contractuelles : '.$nb_heures),'','','', '', '', ''), ';');
		fputcsv($fp, array(utf8_decode('Total des heures de présence du '.date("d/m", strtotime($week_start)).' au '.date("d/m", strtotime($week_end." -1 day")).' : '.$heure_presence),'','','', '', '', ''), ';');
		fputcsv($fp, array(utf8_decode('Total des heures supplémentaires du '.date("d/m", strtotime($week_start)).' au '.date("d/m", strtotime($week_end." -1 day")).' : '.$heure_sup),'','','', '', '', ''), ';');

		fclose($fp);
		header("location: ".$nomfic);
		break;

	case "liste_planning_calendar_theorique":
		if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur="";}
		if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant="";}
		if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule="";}
		if(isset($_GET["week_start"]))      {$week_start    = $_GET["week_start"];}     else{$week_start="";}
	    if(isset($_GET["week_end"]))        {$week_end      = $_GET["week_end"];}       else{$week_end="";}

	    $vide=true;

	    $Livreur    = new Livreur($sql);
	    $plannings  = $Livreur->getPlanning($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

	    ?>
	    <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
    	<table class="table table-bordered table-hover" style="margin-top:50px;">
    		<thead>
        		<th>Livreur</th>
        		<th>Commerçant</th>
        		<th>Véhicule</th>
        		<th>Heure de début</th>
        		<th>Heure de fin</th>
                <th style="width:185px">Actions</th>
        	</thead>
        	<tbody>
			    <?php
				foreach ($plannings as $planning) {
					$vide=false;
					?>
					<tr>
						<td><?=$planning->prenom_livreur." ".$planning->nom_livreur?></td>
						<td><?="<b>".$planning->nom_resto."</b><br/>".$planning->adresse_resto?></td>
						<td><?=$planning->immatriculation_vehicule?></td>
						<td><?=date("H:i", strtotime($planning->date_debut))?></td>
						<td><?=date("H:i", strtotime($planning->date_fin))?></td>
                        <td>
                            <a href="livreurs_fiche2.php?id=<?=$planning->id_livreur?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Voir le Profil"><i class="fa fa-edit"></i></a>
                            <!--<a href="livreurs_planning.php?vehicule_get=<?=$vehicule->id?>" class="btn btn-green tooltips" data-placement="top" data-original-title="Planning"><i class="fa fa-calendar"></i></a>-->
                            <a href="vehicules_fiche2.php?id=<?=$vehicule->id?>" class="btn btn-warning tooltips" data-placement="top" data-original-title="remplacer"><i class="fa fa-history"></i></a>
                            <a onclick= "" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Remplacer"><i class="fa fa-history"></i></a>

                            <a onclick="" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>

                        </td>
					</tr>
					<?php
				}
				if ($vide) {
					?>
					<tr>
						<td colspan="7">Pas de résultats</td>
					</tr>
					<?php
				}
			?>
			</tbody>
        </table>
        <?php
		break;



    case "liste_planning_calendar_One":
        if(isset($_GET["id_livreur"]))      {$id_livreur    =$_GET["id_livreur"];}      else{$id_livreur="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant =$_GET["id_commercant"];}   else{$id_commercant="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   =$_GET["id_vehicule"];}     else{$id_vehicule="";}
        if(isset($_GET["week_start"]))      {$week_start    =$_GET["week_start"];}      else{$week_start="";}
        if(isset($_GET["week_end"]))        {$week_end      =$_GET["week_end"];}        else{$week_end="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getPlanningOne($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Livreur</th>
            <th>Commerçant</th>
            <th>Véhicule</th>
			<th>Horaire</th>
            <th style="width:185px">Actions</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning){
                $vide=false;
                ?>
                <tr>
                    <td><a href="livreurs_fiche2.php?id=<?=$planning->id_livreur?>" data-original-title="Voir le Profil"><?=$planning->prenom_livreur." ".$planning->nom_livreur?></a></td>
                    <td><?=$planning->nom_resto?></td>
                    <td><?=$planning->immat_vehicule?></td>
					<td><?= date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                    <td>
                        <a data-id_livreur="<?=$planning->id_livreur?>" data-id_planning="<?=$planning->id?>" id="button_replace" onclick="loadValueReplaceLivreur(this);" data-target="#myModal2" role="button"  data-toggle="modal" class="btn tooltips" data-placement="top" data-original-title="Remplacer"><i class="fa fa-history"></i></a>
                        <a onclick="$(document).ready(
                            function () {
                                openPopup('popup_planning.php?id=<?= $planning->id?>');
                            }
                        );"  role="button"  data-toggle="modal" class="btn tooltips" data-placement="top" data-original-title="Modifier le planning"><i class="fa fa-calendar"></i></a>
                        <a data-id_livreur="<?=$planning->id_livreur?>" data-name_livreur="<?=$planning->prenom_livreur." ".$planning->nom_livreur?>" data-id_planning="<?=$planning->id?>" data-id_commercant="<?=$planning->id_commercant?>" data-id_vehicule="<?=$planning->id_vehicule?>" onclick="loadValueLivreurOnline(this);" href="#myModal4" role="button"  data-toggle="modal" class="btn btn-green tooltips" data-placement="top" data-original-title="Mettre en ligne"><i class="fa fa-toggle-on" aria-hidden="true"></i></a>
						<a href="#myModal5" role="button"  data-id_livreur="<?=$planning->id_livreur?>" data-name_livreur="<?=$planning->prenom_livreur." ".$planning->nom_livreur?>" onclick="loadValueAttenteLivreur(this);" data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Mettre en attente"><i class="fa fa-pause" aria-hidden="true"></i></a>
                    </td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        break;

    case "liste_planning_calendar_Two":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}      else{$id_livreur="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}   else{$id_commercant="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}     else{$id_vehicule="";}
        if(isset($_GET["week_start"]))      {$week_start    = $_GET["week_start"];}      else{$week_start="";}
        if(isset($_GET["week_end"]))        {$week_end      = $_GET["week_end"];}        else{$week_end="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getPlanningTwo($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Livreur</th>
            <th>Commerçant</th>
            <th>Véhicule</th>
            <th>Horaire</th>
            <th style="width:185px">Actions</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning) {
                $vide=false;
                $pseudo     = ucfirst(strtolower($commercant->u_prenom)).' '.strtoupper(substr($commercant->u_nom,0,1));
                ?>
                <tr>
                    <td><a href="livreurs_fiche2.php?id=<?=$planning->id_livreur?>"><?=$planning->prenom_livreur." ".$planning->nom_livreur?></a></td>
                    <td><?=$planning->nom_resto?></td>
                    <td><?=$planning->immat_vehicule?></td>
					<td><?=date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                    <td>
                        <a data-id_livreur="<?=$planning->id_livreur?>" data-id_planning="<?=$planning->id?>" id="button_replace" onclick="loadValueReplaceLivreur(this);" data-target="#myModal3" role="button"  data-toggle="modal" class="btn tooltips" data-placement="top" data-original-title="Remplacer"><i class="fa fa-history"></i></a>

                        <a onclick="$(document).ready(
                                function () {
                                openPopup('popup_planning.php?id=<?= $planning->id?>');
                                }
                                );"  role="button"  data-toggle="modal" class="btn tooltips" data-placement="top" data-original-title="Modifier le planning"><i class="fa fa-calendar"></i></a>
						<a data-id_livreur="<?=$planning->id_livreur?>" data-name_livreur="<?=$planning->prenom_livreur." ".$planning->nom_livreur?>" data-id_planning="<?=$planning->id?>" data-id_commercant="<?=$planning->id_commercant?>" data-id_vehicule="<?=$planning->id_vehicule?>" onclick="loadValueLivreurOnline(this);" href="#myModal4" role="button"  data-toggle="modal" class="btn btn-green tooltips" data-placement="top" data-original-title="Mettre en ligne"><i class="fa fa-toggle-on" aria-hidden="true"></i></a>
						<a href="#myModal4" role="button"  data-id_livreur="<?=$planning->id_livreur?>" onclick="loadValueAttenteLivreur(this);" data-name_livreur="<?=$planning->prenom_livreur." ".$planning->nom_livreur?>" data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Mettre en attente"><i class="fa fa-pause" aria-hidden="true"></i></a>
                    </td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        break;


    case "liste_dispo_one":
        if(isset($_GET["id_livreur"]))      {$id_livreur    =$_GET["id_livreur"];}      else{$id_livreur="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant =$_GET["id_commercant"];}   else{$id_commercant="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   =$_GET["id_vehicule"];}     else{$id_vehicule="";}
        if(isset($_GET["week_start"]))      {$week_start    =$_GET["week_start"];}      else{$week_start="";}
        if(isset($_GET["week_end"]))        {$week_end      =$_GET["week_end"];}        else{$week_end="";}

        $vide=true;

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getDispoOne($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Livreur</th>
            <th>Horaires</th>
            <th>Vehicule</th>
            <th style="width:185px">Actions</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning) {
                $vide=false;
                ?>
                <tr>
                    <td><?=$planning->prenom_livreur." ".$planning->nom_livreur?></td>
                    <td><?="<b>".date("H:i", strtotime($planning->date_debut))."-".date("H:i", strtotime($planning->date_fin))?></td>
                    <td><?=$planning->immat_vehicule?></td>
                    <td>
                        <a href="livreurs_fiche2.php?id=<?=$planning->id_livreur?>" class="btn tooltips" data-placement="top" data-original-title="Voir le Profil"><i class="fa fa-eye"></i></a>
                        <!--<a href="livreurs_planning.php?vehicule_get=<?=$vehicule->id?>" class="btn btn-green tooltips" data-placement="top" data-original-title="Planning"><i class="fa fa-calendar"></i></a>-->
                        <!--<a href="vehicules_fiche2.php?id=--><?//=$vehicule->id?><!--" class="btn btn-warning tooltips" data-placement="top" data-original-title="Mettre en attente"><i class="fa fa-times fa fa-white"></i></a>-->
                        <a onclick="$(document).ready(
                                function () {
                                openPopup('popup_planning.php?id=<?= $planning->id?>');
                                }
                                );"  role="button"  data-toggle="modal" class="btn tooltips" data-placement="top" data-original-title="Modifier le planning"><i class="fa fa-calendar"></i></a>
						<a href="#myModal4" role="button"  data-toggle="modal" class="btn btn-green tooltips" data-placement="top" data-original-title="Mettre en ligne"><i class="fa fa-toggle-on" aria-hidden="true"></i></a>
						<a href="#myModal5" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Mettre en attente"><i class="fa fa-pause" aria-hidden="true"></i></a>
                    </td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        break;

    case "liste_dispo_two":
        if(isset($_GET["id_livreur"]))      {$id_livreur    =$_GET["id_livreur"];}      else{$id_livreur="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant =$_GET["id_commercant"];}   else{$id_commercant="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   =$_GET["id_vehicule"];}     else{$id_vehicule="";}
        if(isset($_GET["week_start"]))      {$week_start    =$_GET["week_start"];}      else{$week_start="";}
        if(isset($_GET["week_end"]))        {$week_end      =$_GET["week_end"];}        else{$week_end="";}

        $vide=true;

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getDispoTwo($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Livreur</th>
            <th>Horaires</th>
            <th>Vehicule</th>
            <th style="width:185px">Actions</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning) {
                $vide=false;
                ?>
                <tr>
                    <td><?=$planning->prenom_livreur." ".$planning->nom_livreur?></td>
                    <td><?="<b>".date("H:i", strtotime($planning->date_debut))."-".date("H:i", strtotime($planning->date_fin))?></td>
                    <td><?=$planning->immat_vehicule?></td>
                    <td>
                        <a href="livreurs_fiche2.php?id=<?=$planning->id_livreur?>" class="btn tooltips" data-placement="top" data-original-title="Voir le Profil"><i class="fa fa-eye"></i></a>
                        <a onclick="$(document).ready(
                                function () {
                                openPopup('popup_planning.php?id=<?= $planning->id?>');
                                }
                                );"  role="button"  data-toggle="modal" class="btn tooltips" data-placement="top" data-original-title="Modifier le planning"><i class="fa fa-calendar"></i></a>
						<a href="#myModal4" role="button"  data-toggle="modal" class="btn btn-green tooltips" data-placement="top" data-original-title="Mettre en ligne"><i class="fa fa-toggle-on" aria-hidden="true"></i></a>
						<a href="#myModal5" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Mettre en attente"><i class="fa fa-pause" aria-hidden="true"></i></a>
                    </td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        break;

    case "liste_attente":
        if(isset($_GET["week_start"]))      {$week_start    =$_GET["week_start"];}      else{$week_start="";}
        if(isset($_GET["week_end"]))        {$week_end      =$_GET["week_end"];}        else{$week_end="";}

        $vide=true;

        $Livreur=new Livreur($sql);
        $livreurs=$Livreur->getAttente();
        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Livreur</th>
            <th style="width:185px">Actions</th>
            </thead>
            <tbody>
            <?php
            foreach ($livreurs as $livreur) {
                $vide=false;
                ?>
                <tr>
                    <td><?=$livreur->prenom." ".$livreur->nom?></td>
                    <td>
                        <a href="livreurs_fiche2.php?id=<?=$livreur->id_livreur?>" class="btn tooltips" data-placement="top" data-original-title="Voir le Profil"><i class="fa fa-eye"></i></a>
                        <a href="#myModal4" role="button"  data-toggle="modal" class="btn btn-green tooltips" data-placement="top" data-original-title="Mettre en ligne"><i class="fa fa-toggle-on" aria-hidden="true"></i></a>
                        <a href="#myModal5" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Mettre en attente"><i class="fa fa-pause" aria-hidden="true"></i></a>
                    </td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        break;

    case "detail_shift1":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["last_id_livreur"])) {$last_id_livreur       =$_GET["last_id_livreur"];} else{$last_id_livreur   ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["week_start"]))      {$week_start            =$_GET["week_start"];}      else{$week_start        ="";}
        if(isset($_GET["week_end"]))        {$week_end              =$_GET["week_end"];}        else{$week_end          ="";}

        $vide=true;

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getHistoriquePlanningOne($id_planning);
        $now = new DateTime("now");
        $date = $now->format('Y-m-d H:i:s');

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Heure</th>
            <th>User</th>
            <th>Commerçant</th>
            <th>Shift</th>
            <th>Changement</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning) {
                $vide=false;
                ?>
                <tr>
                    <td><?=date( "H:i", strtotime($planning->last_update))?></td>
                    <td><?=$planning->prenom_admin." ".$planning->nom_admin;?></td>
                    <td><?=$planning->nom_resto?></td>
                    <td><?=date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                    <td><?="Old -> ". $planning->lastprenom_livreur ." ". $planning->lastnom_livreur . "</b><br/>" . "New -> ". $planning->prenom_livreur ." ". $planning->nom_livreur?></td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        break;

    case "detail_shift2":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["last_id_livreur"])) {$last_id_livreur       =$_GET["last_id_livreur"];} else{$last_id_livreur   ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["week_start"]))      {$week_start            =$_GET["week_start"];}      else{$week_start        ="";}
        if(isset($_GET["week_end"]))        {$week_end              =$_GET["week_end"];}        else{$week_end          ="";}

        $vide=true;

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getHistoriquePlanningtwo($id_planning);
        $now = new DateTime("now");
        $date = $now->format('Y-m-d H:i:s');

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Heure</th>
            <th>User</th>
            <th>Commerçant</th>
            <th>Shift</th>
            <th>Changement</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning) {
                $vide=false;
                ?>
                <tr>
                    <td><?=date( "H:i", strtotime($planning->last_update))?></td>
                    <td><?=$planning->prenom_admin." ".$planning->nom_admin;?></td>
                    <td><?=$planning->nom_resto?></td>
                    <td><?=date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                    <td><?="Old -> ". $planning->lastprenom_livreur ." ". $planning->lastnom_livreur . "</b><br/>" . "New -> ". $planning->prenom_livreur ." ". $planning->nom_livreur?></td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        break;


    case "attr_vehicule1":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["id_admin"]))        {$id_admin              =$_GET["id_admin"];}        else{$id_admin          ="";}

        $vide=true;

        $Livreur    = new Livreur($sql);
        $vehicule   = new Vehicule($sql);

        $plannings  = $Livreur->getAttributionVehiculeOne($id_planning);
        $now        = new DateTime("now");
        $date       = $now->format('Y-m-d H:i:s');

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Heure</th>
            <th>User</th>
            <th>Livreur</th>
            <th>Shift</th>
            <th>Vehicule</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning) {
                $vide=false;
                ?>
                <tr>
                    <td><?=date("H:i", strtotime($planning->attribution_vehicule))?></td>
                    <td><?=$planning->prenom_admin." ".$planning->nom_admin ?></td>

                    <td><?=$planning->nom_livreur ." ". $planning->prenom_livreur ?></td>
                    <td><?="<b>".$planning->nom_resto."</b><br/>". date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                    <td><?=$planning->immatriculation?></td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php

        break;

    case "attr_vehicule2":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["week_start"]))      {$week_start            =$_GET["week_start"];}      else{$week_start        ="";}
        if(isset($_GET["week_end"]))        {$week_end              =$_GET["week_end"];}        else{$week_end          ="";}

        $vide=true;

        $Livreur    = new Livreur($sql);
        $vehicule   = new Vehicule($sql);

        $plannings  = $Livreur->getAttributionVehiculeTwo($id_planning);
        $now        = new DateTime("now");
        $date       = $now->format('Y-m-d H:i:s');

        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Heure</th>
            <th>User</th>
            <th>Livreur</th>
            <th>Shift</th>
            <th>Vehicule</th>
            </thead>
            <tbody>
            <?php
            foreach ($plannings as $planning) {
                $vide=false;
                ?>
                <tr>
                    <td><?=date( "H:i", strtotime($planning->attribution_vehicule))?></td>
                    <td><?=$planning->prenom_admin." ".$planning->nom_admin;?></td>
                    <td><?=$planning->nom_livreur ." ". $planning->prenom_livreur ?></td>
                    <td><?="<b>".$planning->nom_resto."</b><br/>". date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                    <td><?=$planning->immatriculation?></td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php

        break;


    case "livreur_connect_one":
        if(isset($_GET["p"]))       {$page  = $_GET["p"];}       else{$page=1;}
        if(isset($_GET["nom"]))		{$nom   = $_GET["nom"];}     else{$nom="";}
        if(isset($_GET["statut"]))	{$statut= $_GET["statut"];}  else{$statut="";}

        $vide           =true;

        $liste_livreur  = new Livreur($sql);
        $livreurs       = $liste_livreur->getConnexionOne($page, 10, $nom, $statut);
        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
                <th>Heure</th>
                <th>Livreur</th>
            </thead>
            <tbody>
                <?php foreach ($livreurs as $livreur)
                    { $vide=false;
                ?>
                    <tr>
                        <td><?=date("H:i", strtotime($livreur->date_connexion))?></td>
                        <td><?=$livreur->prenom_livreur ." ". $livreur->nom_livreur ?></td>
                    </tr>
            <?php
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="7">Pas de résultats</td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php

    break;

    case "livreur_connect_two":
        if(isset($_GET["p"]))       {$page      =$_GET["p"];}       else{$page=1;}
        if(isset($_GET["nom"]))		{$nom       =$_GET["nom"];}     else{$nom="";}
        if(isset($_GET["statut"]))	{$statut    =$_GET["statut"];}  else{$statut="";}

        $vide           =true;

        $liste_livreur  = new Livreur($sql);
        $livreurs       =$liste_livreur->getConnexionTwo($page, 10, $nom, $statut);



        ?>
        <span id="calendar_theorique_periode" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th>Heure</th>
            <th>Livreur</th>
            </thead>
            <tbody>
            <?php
            foreach ($livreurs as $livreur) {
                $vide=false;
                ?>
                <tr>
                    <td><?=date("H:i", strtotime($livreur->date_connexion))?></td>
                    <td><?=$livreur->prenom_livreur ." ". $livreur->nom_livreur ?></td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">Pas de résultats</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php

        break;


    case "get_shift_commerçant":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
        if(isset($_GET["id_planning"]))      {$id_planning        = $_GET["id_planning"];}         else{$id_planning="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getShiftByCommercant($id_commercant);

        ?>
        <span id="shift_by_commercant" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
                <th style="width: 70px">Nº du shift</th>
                <th style="width: 150px">Livreur</th>
                <th>Date</th>
                <th style="width: 100px">Shift</th>
                <th style="width: 70px">Heure mise en service</th>
                <th style="width: 40px">retard(en h)</th>
                <th style="width: 70px">heures ajouté(en h)</th>
                <th style="width: 70px">statut</th>
                <th style="width: 70px">Total heure effectué</th>
            </thead>

            <tbody>
                <?php
                foreach ($plannings as $planning){
                    $vide=false;
                    ?>
                    <tr>
                        <td><?=$planning->matricule?></td>
                        <td><a href="livreurs_fiche2.php?id=<?=$planning->id_livreur?>" data-original-title="Voir le Profil"><?=$planning->prenom_livreur." ".$planning->nom_livreur?></a></td>
                        <td><?=date("d/m/y",strtotime($planning->debut))?></td>
                        <td><?=date("H:i",  strtotime($planning->debut)) . " - " . date("H:i", strtotime($planning->fin))?></td>
                        <td><?=date("H:i:s",strtotime($planning->connexion))?></td>
                        <td><?=$planning->retard.""?></td>
                        <td></td>
                        <td></td>
                        <td><?=$planning->travail?></td>
                    </tr>
                    <?php
                }
                if ($vide) {
                    ?>
                    <tr>
                        <td colspan="7">il n'y a pas de shift pour ce commercant</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php

        break;

    case "get_day_commerçant":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
        if(isset($_GET["id_planning"]))      {$id_planning        = $_GET["id_planning"];}         else{$id_planning="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getShiftByCommercant($id_commercant, true);

        ?>
        <span id="shift_by_commercant" style="position:absolute;left:30px;top:60px"></span>
        <table class="table table-bordered table-hover" style="margin-top:50px;">
            <thead>
            <th style="width: 70px">Nº du shift</th>
            <th style="width: 150px">Livreur</th>
            <th style="width: 50px">Date</th>
            <th style="width: 90px">Shift</th>
            <th style="width: 70px">Heure mise en service</th>
            <th style="width: 40px">retard(en h)</th>
            <th style="width: 70px">heures ajouté(en h)</th>
            <th style="width: 70px">Total heure effectué</th>
            </thead>

            <tbody>
            <?php
            foreach ($plannings as $planning){
                $vide=false;
                ?>
                <tr>
                    <td><?=$planning->matricule?></td>
                    <td><a href="livreurs_fiche2.php?id=<?=$planning->id_livreur?>" data-original-title="Voir le Profil"><?=$planning->prenom_livreur." ".$planning->nom_livreur?></a></td>
                    <td><?=date("d/m/y",strtotime($planning->debut))?></td>
                    <td><?=date("H:i",  strtotime($planning->debut)) . " - " . date("H:i", strtotime($planning->fin))?></td>
                    <td><?=date("H:i:s",strtotime($planning->connexion))?></td>
                    <td><?=$planning->retard.""?></td>
                    <td></td>
                    <td><?=$planning->travail?></td>
                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">il n'y a pas de shift pour ce commercant</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php

        break;


    case "export_activite_one":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["last_id_livreur"])) {$last_id_livreur       =$_GET["last_id_livreur"];} else{$last_id_livreur   ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["week_start"]))      {$week_start            =$_GET["week_start"];}      else{$week_start        ="";}
        if(isset($_GET["week_end"]))        {$week_end              =$_GET["week_end"];}        else{$week_end          ="";}

        $nomfic = 'exports/activiteShift_Matin'.date("YmdHis").'.csv';
        $fp = fopen($nomfic, 'w');

        $titre = array('Date Annuel', 'heure', 'User', 'Commercant', 'Heure de debut', 'Heure de fin', 'Ancien livreur', 'Nouveau livreur');
        fputcsv($fp, $titre, ';');

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getHistoriquePlanningOne($id_planning);

        foreach ($plannings as $planning) {
            $lignecsv = array(date("d/m/Y", strtotime($planning->date_debut)), date( "H:i", strtotime($planning->last_update)),utf8_decode($planning->prenom_admin ." ". $planning->nom_admin), utf8_decode($planning->nom_resto." ".$planning->adresse_resto), date("H:i", strtotime($planning->date_debut)), date("H:i", strtotime($planning->date_fin)), utf8_decode($planning->lastprenom_livreur ." ". $planning->lastnom_livreur), utf8_decode($planning->prenom_livreur ." ". $planning->nom_livreur));
            fputcsv($fp, $lignecsv, ';');
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;

    case "export_activite_two":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["last_id_livreur"])) {$last_id_livreur       =$_GET["last_id_livreur"];} else{$last_id_livreur   ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["week_start"]))      {$week_start            =$_GET["week_start"];}      else{$week_start        ="";}
        if(isset($_GET["week_end"]))        {$week_end              =$_GET["week_end"];}        else{$week_end          ="";}

        $nomfic = 'exports/activiteShift_Soir'.date("YmdHis").'.csv';
        $fp = fopen($nomfic, 'w');

        $titre = array('Date Annuel', 'heure', 'User', 'Commercant', 'Heure de debut', 'Heure de fin', 'Ancien livreur', 'Nouveau livreur');
        fputcsv($fp, $titre, ';');

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getHistoriquePlanningTwo($id_planning);

        foreach ($plannings as $planning) {
            $lignecsv = array(date("d/m/Y", strtotime($planning->date_debut)), date( "H:i", strtotime($planning->last_update)),utf8_decode($planning->prenom_admin ." ". $planning->nom_admin), utf8_decode($planning->nom_resto." ".$planning->adresse_resto), date("H:i", strtotime($planning->date_debut)), date("H:i", strtotime($planning->date_fin)), utf8_decode($planning->lastprenom_livreur ." ". $planning->lastnom_livreur), utf8_decode($planning->prenom_livreur ." ". $planning->nom_livreur));
            fputcsv($fp, $lignecsv, ';');
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;


    case "export_attrVehicule_one":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["week_start"]))      {$week_start            =$_GET["week_start"];}      else{$week_start        ="";}
        if(isset($_GET["week_end"]))        {$week_end              =$_GET["week_end"];}        else{$week_end          ="";}

        $nomfic = 'exports/attributionVehicule_Matin'.date("YmdHis").'.csv';
        $fp = fopen($nomfic, 'w');

        $titre = array('Date Annuel', 'heure', 'User', 'livreur', 'Commercant', 'Heure de debut', 'Heure de fin', 'Vehicule');
        fputcsv($fp, $titre, ';');

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getAttributionVehiculeOne($id_planning);

        foreach ($plannings as $planning) {
            $lignecsv = array(date("d/m/Y", strtotime($planning->date_debut)), date( "H:i", strtotime($planning->attribution_vehicule)),utf8_decode($planning->prenom_admin ." ". $planning->nom_admin), utf8_decode($planning->prenom_livreur ." ". $planning->nom_livreur), utf8_decode($planning->nom_resto), date("H:i", strtotime($planning->date_debut)), date("H:i", strtotime($planning->date_fin)), utf8_decode($planning->immatriculation));
            fputcsv($fp, $lignecsv, ';');
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;

    case "export_attrVehicule_two":
        if(isset($_GET["id_livreur"]))      {$id_livreur            =$_GET["id_livreur"];}      else{$id_livreur        ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant         =$_GET["id_commercant"];}   else{$id_commercant     ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule           =$_GET["id_vehicule"];}     else{$id_vehicule       ="";}
        if(isset($_GET["week_start"]))      {$week_start            =$_GET["week_start"];}      else{$week_start        ="";}
        if(isset($_GET["week_end"]))        {$week_end              =$_GET["week_end"];}        else{$week_end          ="";}

        $nomfic = 'exports/attributionVehicule_Soir'.date("YmdHis").'.csv';
        $fp = fopen($nomfic, 'w');

        $titre = array('Date Annuel', 'heure', 'User', 'livreur', 'Commercant', 'Heure de debut', 'Heure de fin', 'Vehicule');
        fputcsv($fp, $titre, ';');

        $Livreur=new Livreur($sql);
        $plannings=$Livreur->getAttributionVehiculeTwo($id_planning);

        foreach ($plannings as $planning) {
            $lignecsv = array(date("d/m/Y", strtotime($planning->date_debut)), date( "H:i", strtotime($planning->attribution_vehicule)),utf8_decode($planning->prenom_admin ." ". $planning->nom_admin), utf8_decode($planning->prenom_livreur ." ". $planning->nom_livreur), utf8_decode($planning->nom_resto), date("H:i", strtotime($planning->date_debut)), date("H:i", strtotime($planning->date_fin)), utf8_decode($planning->immatriculation));
            fputcsv($fp, $lignecsv, ';');
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;


    case "export_connectLivreur_one":
        if(isset($_GET["p"]))       {$page      =$_GET["p"];}       else{$page=1;}
        if(isset($_GET["nom"]))		{$nom       =$_GET["nom"];}     else{$nom="";}
        if(isset($_GET["statut"]))	{$statut    =$_GET["statut"];}  else{$statut="";}

        $nomfic = 'exports/connexionLivreur_Matin'.date("YmdHis").'.csv';
        $fp = fopen($nomfic, 'w');

        $titre = array('Date Annuel', 'heure', 'nom', 'prenom');
        fputcsv($fp, $titre, ';');

        $liste_livreur  = new Livreur($sql);
        $livreurs       =$liste_livreur->getConnexionOne($page, 10, $nom, $statut);

        foreach ($livreurs as $livreur) {
            $lignecsv = array(date("d/m/Y", strtotime($livreur->date_debut)), date( "H:i", strtotime($livreur->date_connexion)), utf8_decode($livreur->nom_livreur), utf8_decode($livreur->prenom_livreur));
            fputcsv($fp, $lignecsv, ';');
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;


	case "recurrence":
		$tab_day    = array();
		$continu    = true;
		$jours_rec;

		$heure_deb = DateTime::createFromFormat('H:i', $_POST["h_debut_rec"]);
		$heure_fin = DateTime::createFromFormat('H:i', $_POST["h_fin_rec"]);

		if (strtotime($_POST["date_debut_rec"])>strtotime($_POST["date_fin_rec"])) {
			$continu=false;
			array_push($tab_day,"erreur_date");
		}

		if ($heure_deb > $heure_fin) {
			$continu=false;
			array_push($tab_day,"erreur_heure");
		}

		if ($continu) {
			array_push($tab_day,"ok");
			foreach($_POST['day_rec'] as $item){
			  	$jours_rec.=$item.";";
			}
			array_push($tab_day,$jours_rec);
		}

		echo json_encode($tab_day);
		break;

	case "update_planning":
		$livreur    = new Livreur($sql);
		$vehicule   = new Vehicule($sql);

		$date_debut = date("Y-m-d H:i:s",strtotime($_POST["date"]." ".$_POST["h_debut"].":00"));
		$date_fin   = date("Y-m-d H:i:s",strtotime($_POST["date"]." ".$_POST["h_fin"].":00"));

		$type       = ($_POST["livreur"]==$_POST["livreur_base"]) ? "update" : "insert";

		/*if ($date_debut<date("Y-m-d H:i:s")) {
			echo "erreur_date";
		}*/
		if ($_POST["h_debut"]>$_POST["h_fin"]) {
			echo "erreur_heure";
		}
		else {
			if ($_POST["livreur"]!="" && $_POST["livreur"]!=0 && $livreur->checkLivreur($_POST["livreur"], $_POST["vehicule_base"],$date_debut, $date_fin, $type)) {
				echo "erreur_livreur";
			}
			else {
				$livreur->updatePlanningFiche($_POST["id"], "", $_POST["livreur"], $_POST["commercant"],$_SESSION["userid"], $date_debut, $date_fin);
				if ($_POST["vehicule"]!="" && $_POST["vehicule"]!=0 && $vehicule->checkVehicule($_POST["vehicule"], $_POST["livreur"],$date_debut, $date_fin)) {
					echo "erreur_vehicule";
				}
				else {
					$livreur->updatePlanningFiche($_POST["id"], $_POST["vehicule"], $_POST["livreur"], $_POST["commercant"], $_SESSION["userid"], $date_debut, $date_fin);
					echo "ok";
				}

			}
		}
		break;

    case "change_planning":
        $livreur = new Livreur($sql);
        break;

	case "delete_planning":
		$livreur = new Livreur($sql);

		$livreur->deletePlanningFiche($_GET["id"]);
		break;

	case "liste_livreurs_heures":
		if(isset($_GET["id_livreur"]))      {$id_livreur    =$_GET["id_livreur"];}      else{$id_livreur="";}
		if(isset($_GET["id_commercant"]))   {$id_commercant =$_GET["id_commercant"];}   else{$id_commercant="";}
		if(isset($_GET["id_vehicule"]))     {$id_vehicule   =$_GET["id_vehicule"];}     else{$id_vehicule="";}
		if(isset($_GET["week_start"]))      {$week_start    =$_GET["week_start"];}      else{$week_start="";}
	    if(isset($_GET["week_end"]))        {$week_end      =$_GET["week_end"];}        else{$week_end="";}

	    $vide       = true;

	    $Livreur    = new Livreur($sql);
	    $plannings  = $Livreur->getListeLivreur($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

	    ?>
    	<table class="table table-bordered table-hover" style="margin-top:50px;">
    		<thead>
        		<th>Nom</th>
        		<th>Prenom</th>
        		<th>Numero</th>
        		<th>Statut</th>
        		<th>Nombre d'heures affectées</th>
        		<th>Nombre d'heures contractuelles</th>
        	</thead>
        	<tbody>
			    <?php
				foreach ($plannings as $planning) {
					$vide                   =false;
					$nb_heures_affectees    =$Livreur->getHoursAffecte($planning->id_livreur, $week_start, $week_end);
					$nb_heures_planning     =($planning->nb_heures=="" || $planning->nb_heures==null) ? "00:00:00" : $planning->nb_heures.":00:00";

					//calcul du temps total affecté en secondes
					sscanf($nb_heures_affectees, "%d:%d:%d", $hours, $minutes, $seconds);
					$time_seconds_affecte   = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

					//calcul du temps contractuel en secondes
					sscanf($nb_heures_planning, "%d:%d:%d", $hours2, $minutes2, $seconds2);
					$time_seconds_planning  = isset($seconds2) ? $hours2 * 3600 + $minutes2 * 60 + $seconds2 : $hours2 * 60 + $minutes2;

					?>
					<tr>
						<td><?=$planning->nom_livreur?></td>
						<td><?=$planning->prenom_livreur?></td>
						<td><?=$planning->telephone?></td>
						<td><?=($planning->statut=="ON") ? "<span class='label label-main'>ON</span>" : "<span class='label label-main-grey'>OFF</span>";?></td>
						<td><?=($time_seconds_affecte<$time_seconds_planning) ? "<span class='label label-main'>".right2("0".$hours,2)."h".right2("0".$minutes, 2)."</span>" : "<span class='label label-danger'>".right2("0".$hours,2)."h".right2("0".$minutes, 2)."</span>"?></td>
						<td><?=$planning->nb_heures?></td>
					</tr>
					<?php
				}
				if ($vide) {
					?>
					<tr>
						<td colspan="7">Pas de résultats</td>
					</tr>
					<?php
				}
			?>
			</tbody>
        </table>
        <?php
		break;


    case "export_liste_livreurs_planning":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
        if(isset($_GET["week_start"]))      {$week_start    = $_GET["week_start"];}     else{$week_start    ="";}
        if(isset($_GET["week_end"]))        {$week_end      = $_GET["week_end"];}       else{$week_end      ="";}
        if(isset($_GET["semaine"]))         {$semaine       = $_GET["semaine"];}        else{$semaine       ="";}

        $nomfic     = 'exports/liste_livreurs_'.date("YmdHis").'.csv';
        $fp         = fopen($nomfic, 'w');

        $titre      = array('Semaine','Nom','Prenom','Numero','Statut', 'Nb d\'heures affectees', 'Nb d\'heures contractuelles');
        fputcsv($fp, $titre, ';');

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getListeLivreur($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

        foreach ($plannings as $planning) {
            $lignecsv = array(utf8_decode($semaine),utf8_decode($planning->nom_livreur), utf8_decode($planning->prenom_livreur), utf8_decode($planning->telephone), utf8_decode($planning->statut), utf8_decode($Livreur->getHoursAffecte($planning->id_livreur, $week_start, $week_end)),utf8_decode($planning->nb_heures));
            fputcsv($fp, $lignecsv, ';');
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;

	case "liste_livreurs_nb":
		if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}       else{$id_livreur      ="";}
		if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}    else{$id_commercant   ="";}
		if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}      else{$id_vehicule     ="";}
		if(isset($_GET["week_start"]))      {$week_start    = $_GET["week_start"];}       else{$week_start      ="";}
	    if(isset($_GET["week_end"]))        {$week_end      = $_GET["week_end"];}         else{$week_end        ="";}

	    $vide   = true;

	    $Livreur= new Livreur($sql);

		echo $Livreur->getListeLivreurNb($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);
		break;

	case "export_liste_livreurs_planning_effectues":
		if(isset($_GET["week_start"]))  {$week_start    = $_GET["week_start"];} else{$week_start    ="";}
	    if(isset($_GET["week_end"]))    {$week_end      = $_GET["week_end"];}   else{$week_end      ="";}

	    $nomfic = 'exports/liste_livreurs_effectue_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom', 'Prenom', 'Commercant', 'Vehicule', 'Date', utf8_decode('Heure debut planifiée'), utf8_decode('Heure fin planifiée'), utf8_decode('Heure debut effectuée'), utf8_decode('Heure fin effectuée'));
		fputcsv($fp, $titre, ';');

	    $Livreur=new Livreur($sql);
	    $plannings=$Livreur->getFullPlanning($week_start, $week_end);

	    foreach ($plannings as $planning) {
	    	if ($planning->date_connexion!=null && $planning->date_deconnexion!=null) {
	    		$date_co    = date("H:i", strtotime($planning->date_connexion));
	    		$date_deco  = date("H:i", strtotime($planning->date_deconnexion));
	    	}
	    	else {
	    		$date_co="Absent";
	    		$date_deco="Absent";
	    	}

	    	$lignecsv = array(utf8_decode($planning->nom_livreur),utf8_decode($planning->prenom_livreur),utf8_decode($planning->nom_resto), utf8_decode($planning->nom_vehicule), utf8_decode(date("d/m/Y", strtotime($planning->date_debut))), utf8_decode(date("H:i", strtotime($planning->date_debut))), utf8_decode(date("H:i", strtotime($planning->date_fin))),utf8_decode($date_co), utf8_decode($date_deco));
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);
		break;

	case "export_planning_livreur":
		if(isset($_GET["id_livreur"]))      {$id_livreur    =$_GET["id_livreur"];}      else{$id_livreur="";}
		if(isset($_GET["id_commercant"]))   {$id_commercant =$_GET["id_commercant"];}   else{$id_commercant="";}
		if(isset($_GET["id_vehicule"]))     {$id_vehicule   =$_GET["id_vehicule"];}     else{$id_vehicule="";}
		if(isset($_GET["week_start"]))      {$week_start    =$_GET["week_start"];}      else{$week_start="";}
	    if(isset($_GET["week_end"]))        {$week_end      =$_GET["week_end"];}        else{$week_end="";}

	    $nomfic = 'exports/planning_livreurs_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Commercant', 'Nom', 'Prenom', 'Vehicule', 'Date', 'Heure debut', 'Heure fin');

		fputcsv($fp, $titre, ';');

	    $Livreur=new Livreur($sql);
	    $plannings=$Livreur->getPlanning($id_livreur, $week_start, $week_end, $id_vehicule, $id_commercant);

	    foreach ($plannings as $planning) {
            $lignecsv = array(utf8_decode($planning->nom_resto), utf8_decode($planning->nom_livreur), utf8_decode($planning->prenom_livreur), utf8_decode($planning->nom_vehicule),  date("d/m/Y", strtotime($planning->date_debut)), date("H:i", strtotime($planning->date_debut)), date("H:i", strtotime($planning->date_fin)));

	    	fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);
		break;

	case "dupliquer":

		$Livreur=new Livreur($sql);
	    $plannings=$Livreur->dupliquer($_POST["livreur_dupliquer"], date("Y-m-d", strtotime($_POST["date_debut"])), date("Y-m-d", strtotime($_POST["date_fin"])), date("Y-m-d", strtotime($_POST["date_debut_dupliquer"])));
	    echo $plannings;

		break;

	case "liste_actualites":
		if(isset($_GET["p"])){$p=$_GET["p"];}else{$p=1;}

		$vide=true;
		$nbaff = 30;
		$p = $p - 1;
		$pt = ($p*$nbaff);
		if($pt<0){$pt = 1;}
		?>
		<table class="table table-bordered table-hover" id="sample-table-1">
	        <thead>
	            <tr>
	                <th style="width:100px">Image</th>
	                <th style="width:150px">Date</th>
	                <th>Titre</th>
	                <th>Catégorie</th>
	                <th style="width:105px">Actions</th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php
	        	$result = $sql->query("SELECT * FROM actualites ORDER BY date DESC LIMIT ".$pt.",".$nbaff);
		        while($ligne = $result->fetch()) {
		        	$vide=false;
	        		?>
	        		<tr>
		        		<td>
		        			<?php
		        			if ($ligne["photo"]!="") {
		        				?>
		        				<a class="image-popup-vertical-fit" href="upload/actualites/<?=$ligne["photo"]?>">
									<div class="avatar_4" style="background:url('upload/actualites/<?=$ligne["photo"]?>') center center no-repeat;margin-bottom: 0px;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;"></div>
								</a>
		        				<?php
		        			}
		        			?>
		        		</td>
		        		<td><?=date("d/m/Y",strtotime($ligne["date"]))?></td>
		        		<td><?=$ligne["titre"]?></td>
		        		<td><?=$ligne["categorie"]?></td>
		        		<td>
		        			<a href="actualite_fiche.php?id=<?=$ligne["id"]?>" class="btn btn-teal"><i class="fa fa-edit"></i></a>
		        			<a onclick="affecte_suppid('<?=$ligne["id"]?>')" href="#myModal" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>
		        		</td>
		        	</tr>
		        	<?php
		        }
		        if ($vide) {
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

	case "actualite_sup":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
		$req = $sql->exec("DELETE FROM actualites WHERE id=".$sql->quote($id));
		header('Location: actualite_liste.php');
		break;

	case "liste_clients":
		if(isset($_GET["nom"]))		    {$nom       =$_GET["nom"];}         else{$nom       ="";}
		if(isset($_GET["numero"]))	    {$numero    =$_GET["numero"];}      else{$numero    ="";}
		if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}  else{$restaurant="";}
		if(isset($_GET["p"]))		    {$p         =$_GET["p"];}           else{$p         =1;}

		$vide=true;
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
                	$liste_client = new Client($sql);
					$clients=$liste_client->getAll($p,30, $nom, $numero, $restaurant);

					foreach ($clients as $client) {
						$vide=false;
	                    ?>
	                    <tr>
	                        <td><?=$client->prenom.' '.$client->nom; ?></td>
	                        <td><?=$client->adresse; ?></td>
	                        <td><?=$client->numero; ?></td>
	                        <td><?=$client->nom_resto; ?></td>
	                        <td>
	                            <a href="commandes_fiche.php?resto=<?=$client->restaurant?>&client=<?=$client->id?>" class="btn btn-dark-green tooltips" data-placement="top" data-original-title="Passer commande"><i class="clip-pencil"></i></a>
	                            <a href="clients_fiche.php?id=<?=$client->id?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
	                            <a onclick="affecte_suppid('<?=$client->id?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>
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

	case "export_liste_clients":
		if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
		if(isset($_GET["numero"]))	{$numero=$_GET["numero"];}else{$numero="";}
		if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}else{$restaurant="";}

		$nomfic = 'exports/clients_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom','Prenom','Adresse','Numero','Email','Commercant');
		fputcsv($fp, $titre, ';');

		$liste_client = new Client($sql);
		$clients=$liste_client->getAll("", "", $nom, $numero, $restaurant);

		foreach ($clients as $client) {
	    	$lignecsv = array(utf8_decode($client->nom),utf8_decode($client->prenom),utf8_decode($client->adresse),utf8_decode($client->numero),utf8_decode($client->email),utf8_decode($client->nom_resto));
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;

	case "liste_restos":
		if(isset($_GET["nom"]))	{$nom=$_GET["nom"];}else{$nom="";}
		if(isset($_GET["p"]))   {$page=$_GET["p"];} else{$page=1;}

		$vide=true;
		?>
        <table class="table table-bordered table-hover" id="sample-table-1">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Contact</th>
                    <?php if ($_SESSION["admin"]) { ?><th style="width:170px;">Ajouté par</th><?php } ?>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $liste_commercant = new Commercant($sql);
				$commercants=$liste_commercant->getAll($page,30, $nom);

				foreach ($commercants as $commercant) {
					$vide       = false;
					$date_ajout = date("d/m/Y",strtotime($commercant->date_ajout));
					$pseudo     = ucfirst(strtolower($commercant->u_prenom)).' '.strtoupper(substr($commercant->u_nom,0,1));
					?>
                    <tr>
                        <td><?=$commercant->nom?></td>
                        <td><?=$commercant->adresse?></td>
                        <td><?=$commercant->contact.'<br/>'.$commercant->numero; ?></td>
                        <?php if ($_SESSION["admin"]) { ?><td><?=$pseudo.'<br/>le '.$date_ajout; ?></td><?php } ?>
                        <td>
                            <?php if ($_SESSION["admin"]) { ?><a href="livreurs_planning.php?commercant_get=<?=$commercant->id?>" class="btn btn-green tooltips"  data-placement="top" data-original-title="Affecter une commande"><img src="images/give_card.png" style="width:14px;"/></a><?php } ?>
                            <a href="shift_commercant.php?id=<?=$commercant->id?>" class="btn btn-purple tooltips"  data-placement="top" data-original-title="Statistiques"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                            <a href="restaurants_fiche.php?id=<?=$commercant->id?>" class="btn btn-primary tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
                            <?php if ($_SESSION["admin"]) { ?><a onclick="affecte_suppid('<?=$commercant->id?>')" href="#myModal3" role="button"  data-toggle="modal"class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a><?php } ?>
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

    case "resto_supprime":
        if(isset($_GET["p"]))   {$page=$_GET["p"];} else{$page=1;}
        if(isset($_GET["nom"]))	{$nom=$_GET["nom"];}else{$nom="";}

        $vide           = true;

        $liste_commercants  = new Commercant($sql);
        $commercants       = $liste_commercants->getAllDelete($page, "", $nom);

        foreach ($commercants as $commercant) {
            $vide=false;
            ?>
            <tr>
                <td><?="<b>".$commercant->nom."</b><br>". $commercant->adresse ?></td>
                <td>
                    <?php if ($_SESSION["admin"]) { ?><a href="livreurs_planning.php?commercant_get=<?=$commercant->id?>" class="btn btn-green tooltips"  data-placement="top" data-original-title="Affecter une commande"><img src="images/give_card.png" style="width:14px;"/></a><?php } ?>
                    <a href="shift_commercant.php?id=<?=$commercant->id?>" class="btn btn-purple tooltips"  data-placement="top" data-original-title="Statistiques"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                    <a href="restaurants_fiche.php?id=<?=$commercant->id?>" class="btn btn-primary tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
                    <?php if ($_SESSION["admin"]) { ?><a onclick="affecte_suppid('<?=$commercant->id?>')" href="#myModal4" role="button"  data-toggle="modal"class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-reply fa fa-white"></i></a><?php } ?>
                </td>
            </tr>
            <?php
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="6">Pas de résultats</td>
            </tr>
            <?php
        }
        break;

	case "export_liste_restos":
	    if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}

	    $nomfic = 'exports/restaurants_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom','Adresse','Contact','Numero','Date Ajout');
		fputcsv($fp, $titre, ';');

	    $liste_commercant=new Commercant($sql);
	    $commercants=$liste_commercant->getAll("", "", $nom);

	    foreach ($commercants as $commercant) {
	    	$lignecsv = array(utf8_decode($commercant->nom), utf8_decode($commercant->adresse), utf8_decode($commercant->contact), utf8_decode($commercant->numero), date("d/m/Y", strtotime($commercant->date_ajout)));
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;

	case "export_liste_users":
		if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
		if(isset($_GET["email"]))	{$email=$_GET["email"];}else{$email="";}
		if(isset($_GET["role"]))	{$role=$_GET["role"];}else{$role="";}


		$nomfic = 'exports/users_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom','Prenom','Numero','Email','Role');
		fputcsv($fp, $titre, ';');

		$liste_utilisateur=new Utilisateur($sql);
	    $utilisateurs=$liste_utilisateur->getAll("", "", $nom, $email, $role);

	    foreach ($utilisateurs as $utilisateur) {
	    	if(strtoupper($utilisateur->role)=="RESTAURATEUR") {
                $role = "COMMERCANT";
            }
            else {
                $role = strtoupper($utilisateur->role);
            }

	    	$lignecsv = array(utf8_decode($utilisateur->nom), utf8_decode($utilisateur->prenom), utf8_decode($utilisateur->numero), utf8_decode($utilisateur->email), $role);
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);


		break;

	case "send_email":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}

		if(is_numeric($id)){
			$Utilisateur=new Utilisateur($sql, $id);
			$Utilisateur->sendEmail($id);

			$ret = "1";
		}else{
			$ret = "-1";
		}
		header("location: administration.php?ret=".$ret);
		break;

	case "connect_as":
		if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}

		$Utilisateur=new Utilisateur($sql, $id);
		$username=$Utilisateur->getEmail();
		$password=$Utilisateur->getPassword();

		$_SESSION["acces"] = false;
		$result = $sql->exec("UPDATE utilisateurs SET statut = 'OFF' WHERE id = '".$_SESSION["userid"]."'");

		header("Location: access_ctrl.php?username=".$username."&password=".$password);

		break;

	case "select_client":
		if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}else{$restaurant="";}
		if(isset($_GET["client"]))	{$client_id=$_GET["client"];}else{$client_id="";}
		if(isset($_GET["get_client"]))	{$get_client=$_GET["get_client"];}else{$get_client="";}

		$Client=new Client($sql);

		?>
    	<option value="">&nbsp;</option>
    	<?php foreach ($Client->getAll("", "", "", "", $restaurant) as $client) {
			$sel=($client_id==$client->id || $get_client==$client->id) ? "selected" : "";
			echo "<option value='".$client->id."' ".$sel.">".$client->nom." ".$client->prenom." - ".$client->adresse."</option>";
    	}
		break;

	case "affecter_livreur":
		if(isset($_POST["id_commande"])){
			$id_commande=$_POST["id_commande"];
		}
		else if (isset($_GET["id_commande"])){
			$id_commande=$_GET["id_commande"];
		}
		else {
			$id_commande=="";
		}

		if(isset($_POST["id_livreur"])){
			$id_livreur=$_POST["id_livreur"];
		}
		else if (isset($_GET["id_livreur"])){
			$id_livreur=$_GET["id_livreur"];
		}
		else {
			$id_livreur=="";
		}
		if(isset($_GET["redirect"])){$redirect=$_GET["redirect"];}else{$redirect="";}

		$Livreur=new Livreur($sql, $id_livreur);
		$Commande=new Commande($sql, $id_commande);
		$Commercant=new Commercant($sql, $Commande->getRestaurant());

		if ($id_livreur=="") {
			$id_livreur=0;
		}

		if(is_numeric($id_commande) && is_numeric($id_livreur)){
			if ($id_livreur==0) {
				//envoyer push notif
				$message="Annulation de la commande ".$Commercant->getNom()." à livrer entre ".date("H:i", strtotime($Commande->getDateDebut())).' et '.date("H:i", strtotime($Commande->getDateFin()));
                $url="commandes.html";
                $envoi=file('http://www.you-order.eu/admin/action_poo.php?action=send_push&id='.$Commande->getLivreur().'&message='.urlencode($message).'&url='.urlencode($url));

				$Commande->setLivreur($id_commande, $id_livreur);
				echo "desaffecte";
				if ($redirect=="oui") {
					header("location: commandes_visu.php?aff_valide=-1&id=".$id_commande);
				}
			}
			else {
				//if ($Livreur->checkLivreur($id_livreur, "", $Commande->getDateDebut(), $Commande->getDateFin(), "insert")) {
					//echo "erreur";
				//}
				//else {
					$message="Nouvelle commande de ".$Commercant->getNom()." à livrer entre ".date("H:i", strtotime($Commande->getDateDebut())).' et '.date("H:i", strtotime($Commande->getDateFin()));
	                $url="commandes.html?tab=2";
	                $envoi=file('http://www.you-order.eu/admin/action_poo.php?action=send_push&id='.$id_livreur.'&message='.urlencode($message).'&url='.urlencode($url));

					$Commande->setLivreur($id_commande, $id_livreur);
					echo $Livreur->getPrenom()." ".$Livreur->getNom();
				//}
			}

		}
		break;

	case "filtrer_commandes":
		$vide           = true;
		$Commande       = new Commande($sql);

		$id_commercant  = ($_GET["id_commercant"]=="" || (!isset($_GET["id_commercant"]))) ? "" : $_GET["id_commercant"];
		$date           = ($_GET["date"]=="" || (!isset($_GET["date"]))) ? "" : $_GET["date"];

		$liste_commandes= $Commande->getAll("", "", 0, $id_commercant, "ajouté", date("Y-m-d", strtotime($date))." - ".date("Y-m-d", strtotime($date)), 0);
		foreach($liste_commandes as $commande) {
			$vide=false;
			$tooltip_title=($commande->prenom_client!="") ? substr($commande->prenom_client, 0, 1).". ".$commande->nom_client : $commande->nom_client ;
			?>
			<div class="div_commande tooltips" data-placement="top" data-original-title="<?=$tooltip_title?>" draggable="true" ondragstart="drag(event)" id="commande_<?=$commande->id?>" ondrop="drop_desaffecte(event)" ondragover="allowDrop(event)">
                <p class="div_commande_titre"><b class="date_commande1"><?=$commande->nom_resto?></b><b class="date_commande2"><?=date("d/m", strtotime($commande->date_debut))?>, <?=date("H:i", strtotime($commande->date_debut))?> et <?=date("H:i", strtotime($commande->date_fin))?></b><div class="stop"></div></p>
				<p><img src="images/icon_depart.png"/><?=$commande->adresse_resto?></p>
				<p><img src="images/icon_arrivee.png"/><?=$commande->adresse_client?></p>
			</div>
			<?php
		}

		if ($vide) {
			echo "Il n'y a pas de commandes en attente.";
		}
		break;

	case "reload_nb_commandes":
		$id_commercant=($_GET["id_commercant"]=="" || (!isset($_GET["id_commercant"]))) ? "" : $_GET["id_commercant"];
		$date=($_GET["date"]=="" || (!isset($_GET["date"]))) ? "" : $_GET["date"];

		$Commande=new Commande($sql);

		$Commande->getPagination(30, "", $id_commercant, "ajouté", date("Y-m-d", strtotime($date))." - ".date("Y-m-d", strtotime($date)), 0);

		echo $Commande->getNbRes();
		break;

	case "reload_livreurs":
		$id_livreur_open=(isset($_GET["id_livreur"])) ? $_GET["id_livreur"] : "";

		$vide=true;
		$Livreur=new Livreur($sql);
		$Commande=new Commande($sql);

		$liste_livreurs=$Livreur->getAll("", "", "", "ON", "");
		foreach($liste_livreurs as $livreur) {
			$vide=false;
			$Livreur->getPaginationCommande(30, $livreur->id, "", "réservé", "");
			$Commande->getPagination(30, $livreur->id, "", "", "", 0);
			//il faut mettre la classe livreur_[id] sur tous les éléments pour pouvoir récupérer l'id du livreur quelque soit l'élément vers lequel on drop la commande.

			//verifier $id_livreur_open pour afficher les commandes d'un livreur si besoin
			if ($id_livreur_open==$livreur->id) {
				$show_commandes_livreur="block";
				$show_commandes_icon="fa-minus";
			}
			else {
				$show_commandes_livreur="none";
				$show_commandes_icon="fa-plus";
			}
			?>
                            <div  class="div_livreur div_draggable livreur_<?=$livreur->id?>" id="div_livreur_<?=$livreur->id?>" ondrop="drop(event)" ondragover="allowDrop(event)">
				<p class="div_livreur_nom livreur_<?=$livreur->id?>"><?=$livreur->prenom." ".$livreur->nom?> (<?=$Commande->getNbRes()?>) <i onclick="showInfoLivreur('<?=$livreur->id?>')" class="fa fa-info round_icon"></i><span style="float:right" onclick="showCommandes('commande_livreur_<?=$livreur->id?>')"><i class="fa <?=$show_commandes_icon?> round_icon_large"></i></span></p>
				<div id="commande_livreur_<?=$livreur->id?>" class="livreur_<?=$livreur->id?>" style="display:<?=$show_commandes_livreur?>">
					<?php
					$liste_livreurs_commandes=$Commande->getAll("", "", $livreur->id, "", "", "", 0);
					foreach ($liste_livreurs_commandes as $commande) {
						$style_bg="";
						$is_draggable="true";
						if($commande->statut=="réservé") $style_bg="style='background-color:#e8f6fa'";
						if($commande->statut=="récupéré") { $style_bg="style='background-color:#add2e4'";$is_draggable="false";}

						$tooltip_title=($commande->prenom_client!="") ? substr($commande->prenom_client, 0, 1).". ".$commande->nom_client : $commande->nom_client ;
						?>
						<div class="div_commande livreur_<?=$livreur->id?> tooltips" data-placement="top" data-original-title="<?=$tooltip_title?>" draggable="<?=$is_draggable?>" ondragstart="drag(event)" id="commande_<?=$commande->id?>" <?=$style_bg?>>
                            <p class="div_commande_titre livreur_<?=$livreur->id?>"><b class="date_commande1"><?=$commande->nom_resto?></b><b  class="date_commande2"><?=date("d/m", strtotime($commande->date_debut))?>, <?=date("H:i", strtotime($commande->date_debut))?> et <?=date("H:i", strtotime($commande->date_fin))?></b><div class="stop"></div></p>
                            <p class="livreur_<?=$livreur->id?>"><img src="images/icon_depart.png"/><?=$commande->adresse_resto?></p>
                            <p class="livreur_<?=$livreur->id?>"><img src="images/icon_arrivee.png"/><?=$commande->adresse_client?></p>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		<?php }

		if ($vide) {
			?>
			<div class="div_livreur">Il n'y a pas de livreurs connectés</div>
			<?php
		}

		break;

	case "reload_nb_livreurs":
		$Livreur=new Livreur($sql);

		$Livreur->getPagination(30, "", "ON", "");

		echo $Livreur->getNbRes();
		break;

	case "show_info_livreur":
		if(isset($_GET["id_livreur"]))  {$id_livreur = $_GET["id_livreur"];}    else{$id_livreur="";}
		$Livreur    = new Livreur($sql, $id_livreur);
		$Commande   = new Commande($sql);
		$Livreur->getPaginationCommande(30, $id_livreur, "", "réservé", "");
		$Commande->getPagination(30, $id_livreur, "", "", "", 0);

		?>

		<div class="row">
			<div class="col-sm-10 col-sm-offset-1">
				<p>Livreur : <?=strtoupper($Livreur->getNom())." ".$Livreur->getPrenom()." - ".$Livreur->getTelephone();?></p>
				<div style="width:201px;border:1px solid #ddd;padding: 10px 15px;display:inline-block;background-color:#f9f9f9"><?= ($Commande->getNbRes()>1) ? 'Commandes affectées' : 'Commande affectée';?> (<?=$Commande->getNbRes()?>)</div><div style="padding: 10px 15px;display:inline-block;border:1px solid #ddd;border-left:none;width: -moz-calc(100% - 201px);width: -webkit-calc(100% - 201px);width: calc(100% - 201px);">Informations sur la commande</div>
				<div class="tabbable tabs-left">
					<ul id="myTab3" class="nav nav-tabs">
						<?php for ($i=1;$i<=$Commande->getNbRes();$i++) {
							?>
							<li class="<?php if ($i==1) echo 'active'; ?>">
								<a href="#tab<?=$i?>" data-toggle="tab">Commande <?=$i?></a>
							</li>
							<?php
						}

						?>
					</ul>
					<div class="tab-content">
						<?php
						$i=0;
						//foreach ($Livreur->getAllCommande("", "", $id_livreur, "", "réservé", "") as $commande) {
						foreach ($Commande->getAll("", "", $id_livreur, "", "", "", 0) as $commande) {
							$i++;
							$distance_km = round($commande->distance/1000,0);
						    $duree_h = gmdate("H",$commande->duree);
						    $duree_m = gmdate("i",$commande->duree);
						    $duree_aff=($duree_h>0) ? $duree_h."h".$duree_m : $duree_m." min";

						    $date_ajout     ="";
						    $date_reserve   ="";
						    $user_ajout     ="";
						    $user_reserve   ="";
							?>
							<div class="tab-pane <?php if ($i==1) echo 'active'; ?>" id="tab<?=$i?>">
								<ul id="myTab4" class="nav nav-tabs">
									<li class="tab_commande active">
										<a href="#panel_commercant_<?=$commande->id?>" data-toggle="tab">
											COMMERCANT
										</a>
									</li>
									<li class="tab_commande tab_commande_bis">
										<a href="#panel_client_<?=$commande->id?>" data-toggle="tab">
											CLIENT
										</a>
									</li>
								</ul>
								<div class="tab-content tab_commande_body">
									<div class="tab-pane in active" id="panel_commercant_<?=$commande->id?>">
										<p>
				                            <?php
				                            echo "<span class='commandes_titre'>".$commande->nom_resto."</span><br/>";
				                            echo "<span class='commandes_icon'><i class='fa fa-map-marker'></i></span>".$commande->adresse_resto."<br/>";
				                            echo "<span class='commandes_icon'><i class='fa fa-phone'></i></span>".$commande->numero_resto."<br/>";
				                            ?>
				                        </p>
									</div>
									<div class="tab-pane" id="panel_client_<?=$commande->id?>">
										<?php
				                            echo "<span class='commandes_titre'>".$commande->prenom_client." ".$commande->nom_client."</span><br/>";
				                            echo "<span class='commandes_icon'><i class='fa fa-map-marker'></i></span>".$commande->adresse_client."<br/>";
				                            echo "<span class='commandes_icon'><i class='fa fa-phone'></i></span>".$commande->numero_client."<br/>";
				                            ?>
									</div>
								</div>
								<div class="div_info_commande">
									<?php
									//récupérer les informations concernant les différents statuts
									foreach($Commande->getAllStatut($commande->id) as $commande_statut) {
										if ($commande_statut->statut=="ajouté") {
											$date_ajout=date("d/m/Y \à H:i", strtotime($commande_statut->date));
											$user_ajout=$commande_statut->user_prenom." ".strtoupper($commande_statut->user_nom);
										}
										else if ($commande_statut->statut=="réservé") {
											$date_reserve=date("d/m/Y \à H:i", strtotime($commande_statut->date));
											$user_reserve=$commande_statut->user_prenom." ".strtoupper($commande_statut->user_nom);
										}
									}
									?>
									<p class="commandes_titre" style="margin-top:15px">Détail de la commande</p>
									<p>Ajout : le <?=$date_ajout?> par <?=$user_ajout?></p>
									<p>Affectation : le <?=$date_reserve?> par <?=$user_reserve?></p>
									<p style="margin-bottom:15px"><?=$distance_km?> km - <?=$duree_aff?></p>
									<div class="label <?=couleur_statut($commande->statut);?>" style="font-size:16px !important;"><?=ucfirst(txt_statut($commande->statut))?></div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<?php
		break;

	case "get_position_livreur":
		if(isset($_GET["id_livreur"])){$id_livreur=$_GET["id_livreur"];}else{$id_livreur="";}

		$Livreur=new Livreur($sql, $id_livreur);
		$tab_position = array();

		if ($id_livreur==0 || $id_livreur=="") {
			foreach($Livreur->getAll("", "", "", "ON", "") as $livreur_marker) {
	    		if ($livreur_marker->latitude!=0 && $livreur_marker->longitude!=0) {
	    			array_push($tab_position, array($livreur_marker->longitude,$livreur_marker->latitude,$livreur_marker->id, $livreur_marker->prenom, $livreur_marker->nom));
	            }
	    	}
		}
		else {
			array_push($tab_position, array($Livreur->getLongitude(),$Livreur->getLatitude(),$id_livreur, $Livreur->getPrenom(), $Livreur->getNom()));
		}

		echo json_encode($tab_position);
		break;

	case "get_position_maps":
		$Commande=new Commande($sql);
		$tab_position = array();

		$date=($_GET["date"]=="" || (!isset($_GET["date"]))) ? date("d-m-Y") : $_GET["date"];

		$liste_commandes=$Commande->getAll("", "", 0, "", "ajouté", date("Y-m-d", strtotime($date))." - ".date("Y-m-d", strtotime($date)), 0);
		foreach($liste_commandes as $commande) {
			array_push($tab_position, array($commande->client_lng,$commande->client_lat,$commande->nom_client.' '.$commande->prenom_client,"client"));
			array_push($tab_position, array($commande->lng_resto,$commande->lat_resto,$commande->nom_resto,"restos"));
		}

		echo json_encode($tab_position);
		break;

	case "liste_notification":
		if(isset($_GET["p"])){$page=$_GET["p"];}else{$page=1;}
		$vide=true;

		if ($vide) {
		?>
			<tr>
            	<td colspan="6">Aucun résultat disponible</td>
            </tr>
		<?php
		}
		break;

	case "get_notif":
		$result = $sql->query("SELECT n.* FROM notifications n LEFT JOIN notifications_lecture l ON n.id=l.id_notif AND l.id_utilisateur=".$sql->quote($_SESSION["userid"])." WHERE l.id_utilisateur!=".$sql->quote($_SESSION["userid"])." OR l.id_utilisateur IS NULL ORDER BY n.date DESC");
        $ligne = $result->fetchAll(PDO::FETCH_OBJ);
		foreach($ligne as $notif) {
			if (date("Y-m-d",strtotime($notif->date)) >= date('Y-m-d', strtotime('-7 days'))) {
				$txt_notif="";
				$Commercant=new Commercant($sql, $notif->id_commercant);
				if ($notif->type=="ajout") {
					$txt_notif="Nouvelle commande";
					$icon_notif="clip-list";
				}
				else if ($notif->type=="modif") {
					$txt_notif="Modification commande";
					$icon_notif="clip-list";
				}
				else if ($notif->type=="planning_ajout") {
					$txt_notif="Nouvelle demande dispo livreur";
					$icon_notif="fa fa-cutlery";
				}
				else if ($notif->type=="planning_modif") {
					$txt_notif="Modification demande dispo livreur";
					$icon_notif="fa fa-cutlery";
				}
				?>
				<a id="notif_<?=$notif->id?>" href="javascript:void(0)" class="notif-detail">
	                <i class="<?=$icon_notif?>"></i>
	                <span onclick="vu_notif(<?=$notif->id?>, <?=$notif->id_commande?>, '<?=$notif->type?>', true)"><?=$txt_notif?> : <?=$Commercant->getNom()?></span>
                        <div style="float:right">
                            <span class="notif-date"><?=format_date($notif->date)?></span>
                            <i class="fa fa-close close-notif" onclick="vu_notif(<?=$notif->id?>, <?=$notif->id_commande?>, '<?=$notif->type?>', false)"></i>
                        </div>
	            </a>
				<?php
			}
			else {
				$result = $sql->exec("DELETE FROM notifications WHERE id=".$sql->quote($notif->id));
			}
		}
		break;

	case "get_notif_nb":
		$result = $sql->query("SELECT COUNT(*) as NB FROM `notifications` n LEFT JOIN notifications_lecture l ON n.id=l.id_notif AND l.id_utilisateur=".$sql->quote($_SESSION["userid"])." WHERE (l.id_utilisateur!=".$sql->quote($_SESSION["userid"])." OR l.id_utilisateur IS NULL) AND n.date >= DATE(NOW()) - INTERVAL 7 DAY ORDER BY l.date DESC");
        $ligne = $result->fetchAll(PDO::FETCH_OBJ);
		echo $ligne[0]->NB;
		break;

	case "vu_notif":
		$id_notif=(isset($_GET["id_notif"])) ? $_GET["id_notif"] : "";

		$result = $sql->exec("INSERT INTO notifications_lecture (id_notif, id_utilisateur, date) VALUES (".$sql->quote($id_notif).",".$sql->quote($_SESSION["userid"]).", NOW())");

		var_dump($_SESSION);
		echo "INSERT INTO notifications_lecture (id_notif, id_utilisateur, date) VALUES (".$sql->quote($id_notif).",".$sql->quote($_SESSION["userid"]).", NOW())";
		break;

	case "noter_livreur":
		$id_commande    =(isset($_GET["id_commande"]))  ? $_GET["id_commande"]  : "";
		$id_client      =(isset($_GET["id_client"]))    ? $_GET["id_client"]    : "";
		$id_livreur     =(isset($_GET["id_livreur"]))   ? $_GET["id_livreur"]   : "";
		$note           =(isset($_GET["note"]))         ? $_GET["note"]         : 0;

		$result = $sql->query("SELECT * FROM livreurs_notes WHERE id_commande=".$sql->quote($id_commande));
        $ligne = $result->fetch();
        if ($ligne) {
        	$result = $sql->exec("UPDATE livreurs_notes SET note=".$sql->quote($note)." WHERE id_commande=".$sql->quote($id_commande));
        	echo "update";
        }
        else {
        	$result = $sql->exec("INSERT INTO livreurs_notes (id_client, id_commande, id_livreur, note, date) VALUES (".$sql->quote($id_client).", ".$sql->quote($id_commande).", ".$sql->quote($id_livreur).", ".$sql->quote($note).", NOW())");
        	echo "insert";
        }

        $result2 = $sql->query("SELECT AVG(note) as moyenne FROM `livreurs_notes` WHERE id_livreur=".$sql->quote($id_livreur));
        $ligne2 = $result2->fetch();
        if ($ligne2) {
        	$result = $sql->exec("UPDATE livreurs SET note=".$sql->quote($ligne2["moyenne"])." WHERE id=".$sql->quote($id_livreur));
        }

		break;

	case "send_push":
		$id         = (isset($_GET["id"]))       ? $_GET["id"] : "";
		$message    = (isset($_GET["message"]))  ? $_GET["message"] : "";
		$url        = (isset($_GET["url"]))      ? $_GET["url"] : "";

		$Livreur    = new Livreur($sql, $id);

		$url_notif = "http://www.you-order.eu/admin/android.php?registration_id=".$Livreur->getDeviceId()."&title=youOrder&message=".urlencode($message)."&urlappli=".urlencode($url);
		$envoi_tab = file($url_notif);
		notif_copie($id, $message, $url, $Livreur->getDeviceId(), $sql);
		break;

	case "liste_notifications_push":
		if(isset($_GET["p"]))       {$page  = $_GET["p"];}       else{$page  = 1;}
		if(isset($_GET["nom"]))     {$nom   = $_GET["nom"];}     else{$nom   = "";}
		if(isset($_GET["date"]))    {$date  = $_GET["date"];}    else{$date  = "";}

		$Livreur=new Livreur($sql);

		$vide=true;

		$page = $page - 1;
        $pt = ($page*30);
        if($pt<0){$pt = 1;}

		$req_sup="";
		if ($nom!="") {
			$req = "SELECT * FROM livreurs WHERE nom LIKE'%".$nom."%'";
			$result = $sql->query($req);
			$ligne = $result->fetch();
			if($ligne!=""){
		    	$req_sup.=" AND destinataire LIKE '%".$ligne['id'].",%'";
		    }
		}
		if ($date!="") {
		    $req_sup.=" AND date_envoi BETWEEN ".$sql->quote(date("Y-m-d", strtotime($date))." 00:00:00")." AND ".$sql->quote(date("Y-m-d", strtotime($date))." 23:59:59");
		}

		$result = $sql->query("SELECT * FROM notifications_push WHERE 1 ".$req_sup." ORDER BY date_creation LIMIT ".$pt.", 30");
        $notifications = $result->fetchAll(PDO::FETCH_OBJ);

        foreach ($notifications as $notif) {
        	$vide=false;

        	if ($notif->statut=="pending") $statut_txt="<span class='label label-warning'>En attente</span>";
        	else if ($notif->statut=="send") $statut_txt="<span class='label label-main'>Envoyé</span>";
        	else if ($notif->statut=="supprime") $statut_txt="<span class='label label-danger'>Annulé</span>";

        	$txt_notif=$notif->message."<br/>";

        	if ($notif->type_envoi=="tous") $nb_envoi=$Livreur->getNbRes();
        	else {
        		$test=explode(',', $notif->destinataire);
        		$nb_envoi=sizeof($test)-1;
        		$txt_notif.=($nb_envoi>1) ? "Destinataires : " : "Destinataire : ";
        		foreach($test as $dest) {
        			$Livreur=new Livreur($sql, $dest);
        			$txt_notif.=$Livreur->getPrenom()." ".$Livreur->getNom().", ";
        		}
        		$txt_notif=rtrim($txt_notif, ", ");
        	}

        	?>
	        	<tr>
	        		<td><?=$notif->nom?></td>
	        		<td><?=date("d/m/Y", strtotime($notif->date_creation))?></td>
	        		<td><?=date("d/m/Y", strtotime($notif->date_envoi))?></td>
	        		<td><?=$nb_envoi?></td>
	        		<td><?=$statut_txt?></td>
	        		<td>
	        			<a onclick="affecte_txt('<?=$txt_notif?>')" href="#myModal" role="button"  data-toggle="modal" class="btn btn-teal tooltips" data-placement="top" data-original-title="Voir le message"><i class="clip-eye"></i></a>
	        			<a onclick="affecte_suppid('<?=$notif->id?>')" href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Annuler"><i class="fa fa-times fa fa-white"></i></a>
	        		</td>
	        	</tr>
        	<?php
        }

        if ($vide) {
        	echo '<tr><td colspan="6">Aucun résultat disponible</td></tr>';
        }
		break;

	case "export_liste_notif_push":
		if(isset($_GET["nom"]))     {$nom   = $_GET["nom"];}    else{$nom   = "";}
		if(isset($_GET["date"]))    {$date  = $_GET["date"];}   else{$date  = "";}

	    $nomfic = 'exports/notifs_push_'.date("YmdHis").'.csv';
		$fp = fopen($nomfic, 'w');

		$titre = array('Nom', 'Message','Date creation','Date envoi','Nb envoi','Etat');
		fputcsv($fp, $titre, ';');

		$Livreur=new Livreur($sql);

		$req_sup="";
		if ($nom!="") {
			$req = "SELECT * FROM livreurs WHERE nom LIKE'%".$nom."%'";
			$result = $sql->query($req);
			$ligne = $result->fetch();
			if($ligne!=""){
		    	$req_sup.=" AND destinataire LIKE '%".$ligne['id'].",%'";
		    }
		}
		if ($date!="") {
		    $req_sup.=" AND date_envoi BETWEEN ".$sql->quote(date("Y-m-d", strtotime($date))." 00:00:00")." AND ".$sql->quote(date("Y-m-d", strtotime($date))." 23:59:59");
		}

	    $result = $sql->query("SELECT * FROM notifications_push WHERE 1 ".$req_sup." ORDER BY date_creation");
        $notifications = $result->fetchAll(PDO::FETCH_OBJ);

        foreach ($notifications as $notif) {
        	if ($notif->type_envoi=="tous") $nb_envoi=$Livreur->getNbRes();
        	else {
        		$test=explode(',', $notif->destinataire);
        		$nb_envoi=sizeof($test)-1;
        	}
	    	$lignecsv = array(utf8_decode($notif->nom), utf8_decode($notif->message), utf8_decode(date("d/m/Y", strtotime($notif->date_creation))), utf8_decode(date("d/m/Y", strtotime($notif->date_envoi))), $nb_envoi,$notif->statut);
		    fputcsv($fp, $lignecsv, ';');
	    }

		fclose($fp);
		header("location: ".$nomfic);

		break;

	case "count_hours_dashboard":
		$array = array();
		$j=8;
		for ($i=0;$i<15;$i++) {
	        $array[$i]["heure"]=$j.":00";
	        $array[$i]["nb_heures_commercant"]=2;
	        $array[$i]["nb_heures_livreur"]=5;
	        $j++;
        }
        echo json_encode($array);
		break;


	case "count_stats_week":
		if(isset($_GET["week_start"]))  {$week_start    = date("Y-m-d", $_GET["week_start"]);}  else{$week_start="";}
		if(isset($_GET["week_end"]))    {$week_end      = date("Y-m-d", $_GET["week_end"]);}    else{$week_end="";}

		$array = array();

		for ($i=0;$i<7;$i++) {
	        $array[$i]["jour"]=date("d", strtotime(date("Y-m-d", strtotime($week_start." +".$i." days"))));
	        $array[$i]["livreur_connecte"]  = 0;
	        $array[$i]["livreur_absent"]    = 0;
	        $array[$i]["commandes_livree"]  = 0;
	        $array[$i]["commandes_heure"]   = 0;
	        $array[$i]["moyenne"]           = 0;
	        $array[$i]["nb_km"]             = 0;
        }

        //STATS CONNEXIONS LIVREURS ET ABSCENCES
        $result = $sql->query("SELECT COUNT(DISTINCT p.id_livreur) as nb_connecte, DAY(p.date_debut) as day FROM livreurs_planning p LEFT JOIN livreurs_connexion c ON p.id=c.id_planning WHERE p.date_debut BETWEEN ".$sql->quote($week_start)." AND NOW() AND c.id_planning IS NOT NULL GROUP BY DAY(p.date_debut) ");
        $lignes = $result->fetchAll(PDO::FETCH_OBJ);
        foreach ($lignes as $ligne) {
        	for ($i=0;$i<7;$i++) {
                if ($array[$i]["jour"]==$ligne->day) $array[$i]["livreur_connecte"]=intVal($ligne->nb_connecte);
            }
        }

        $result = $sql->query("SELECT COUNT(DISTINCT p.id_livreur) as nb_absent, DAY(p.date_debut) as day FROM livreurs_planning p LEFT JOIN livreurs_connexion c ON p.id=c.id_planning WHERE p.date_debut BETWEEN ".$sql->quote($week_start)." AND NOW() AND c.id_planning IS NULL GROUP BY DAY(p.date_debut) ");
        $lignes = $result->fetchAll(PDO::FETCH_OBJ);
        foreach ($lignes as $ligne) {
        	for ($i=0;$i<7;$i++) {
                if ($array[$i]["jour"]==$ligne->day) $array[$i]["livreur_absent"]=intVal($ligne->nb_absent);
            }
        }

        //STATS LIVRAISONS DES COMMANDES ET RETARD
        $result = $sql->query("SELECT COUNT(h.id) as nb_commande, DAY(h.date) as day FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote($week_start)." AND NOW() AND h.statut='signé' GROUP BY DAY(h.date) ");
        $lignes = $result->fetchAll(PDO::FETCH_OBJ);
        foreach ($lignes as $ligne) {
        	for ($i=0;$i<7;$i++) {
                if ($array[$i]["jour"]==$ligne->day) $array[$i]["commandes_livree"]=intVal($ligne->nb_commande);
            }
        }

        $result = $sql->query("SELECT COUNT(h.id) as nb_commande_heure, DAY(h.date) as day FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote($week_start)." AND NOW() AND h.statut='signé' AND (h.date>=c.date_debut AND h.date<=c.date_fin) GROUP BY DAY(h.date) ");
        $lignes = $result->fetchAll(PDO::FETCH_OBJ);
        foreach ($lignes as $ligne) {
        	for ($i=0;$i<7;$i++) {
                if ($array[$i]["jour"]==$ligne->day) $array[$i]["commandes_heure"]=intVal($ligne->nb_commande_heure);
            }
        }

        //STATS MOYENNES DES NOTES ATTRIBUEES AUX LIVREURS
        $result = $sql->query("SELECT DAY(date) as day, AVG(note) as note FROM livreurs_notes WHERE date BETWEEN ".$sql->quote($week_start)." AND NOW() GROUP BY DAY(date)");
        $lignes = $result->fetchAll(PDO::FETCH_OBJ);
        foreach ($lignes as $ligne) {
        	for ($i=0;$i<7;$i++) {
                if ($array[$i]["jour"]==$ligne->day) $array[$i]["moyenne"]=$ligne->note;
            }
        }

        //STATS NB KILOMETRES
        $result = $sql->query("SELECT SUM(c.distance) as nb_km, DAY(h.date) as day FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote($week_start)." AND NOW() AND h.statut='signé' GROUP BY DAY(h.date)");
        $lignes = $result->fetchAll(PDO::FETCH_OBJ);
        foreach ($lignes as $ligne) {
        	for ($i=0;$i<7;$i++) {
                if ($array[$i]["jour"]==$ligne->day) $array[$i]["nb_km"]=$ligne->nb_km;
            }
        }

        echo json_encode($array);
		break;

	case "count_retard":
		if(isset($_GET["week_start"])){$week_start=date("Y-m-d", $_GET["week_start"]);}else{$week_start="";}
		if(isset($_GET["week_end"])){$week_end=date("Y-m-d", $_GET["week_end"]);}else{$week_end="";}

		$array = array();

		$array["cpt_retard"]=0;
	    $array["tps_retard_week"]=0;

        //STATS NB DE RETARD ET TEMPS DE RETARD
		$result3 = $sql->query("SELECT p.*, c.date_connexion, c.date_deconnexion FROM livreurs_planning p LEFT JOIN livreurs_connexion c ON p.id=c.id_planning WHERE p.date_debut BETWEEN ".$sql->quote(date("Y-m-d")." 00:00:00")." AND NOW()");
		while ($ligne3 = $result3->fetch()) {
		    if ($ligne3["date_connexion"]=="" || $ligne3["date_connexion"]==null) {
		        $array["cpt_retard"]++;
		        $array["tps_retard_week"]+=(strtotime($ligne3["date_fin"])-strtotime($ligne3["date_debut"]));
		    }
		    else if (strtotime($ligne3["date_connexion"])-strtotime($ligne3["date_debut"])>0) {
		        $array["cpt_retard"]++;
		        $array["tps_retard_week"]+=(strtotime($ligne3["date_connexion"])-strtotime($ligne3["date_debut"]));

		    }
		}

		$hours = floor($array["tps_retard_week"] / 3600);
		$minutes = floor(($array["tps_retard_week"] / 60) % 60);

		$array["tps_retard_week"]=($hours!=0) ? $hours."h".$minutes : $minutes."mn" ;

		echo json_encode($array);
		break;


	case "get_dashboard":
		$array          = array();
		$array_livreur  = array();

		$array["nb_absent"]     = 0;
		$array["tps_retard"]    = 0;
		$array["reponse_pc"]    = 0;
		$array["livraison_pc"]  = 0;

		$add_livreurs   = true;

        //STATS CONNEXIONS LIVREURS ET ABSCENCES
//        $result = $sql->query("SELECT COUNT(*) AS NB
//                                        FROM livreurs_connexion lc
//                                        LEFT JOIN restaurants r ON r.id = lc.id_commercant
//                                        LEFT JOIN livreurs l ON l.id = lc.id_livreur
//                                        WHERE lc.date_deconnexion = lc.date_connexion
//                                        AND DATE(lc.date_connexion) = DATE ( NOW() )" . $_SESSION["req_resto"]);
        $result = $sql->query("SELECT COUNT(*) AS NB FROM livreurs WHERE statut='ON'");
        $ligne = $result->fetch();
		if ($ligne) {
            $array["nb_livreurs"]=intVal($ligne["NB"]);
        }

        //STATS NB DE RETARD ET TEMPS DE RETARD
        $result3 = $sql->query("SELECT p.id, ANY_VALUE(p.id_livreur) as livreurs, ANY_VALUE(p.date_debut) as date_debut, ANY_VALUE(p.date_fin) as date_fin, ANY_VALUE(c.date_connexion) as date_connexion, ANY_VALUE(c.date_deconnexion) as date_deconnexion FROM livreurs_planning p LEFT JOIN livreurs_connexion c ON p.id=c.id_planning WHERE p.date_debut BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW() GROUP BY p.id");


//		$result3 = $sql->query("SELECT p.id, ANY_VALUE(p.id_livreur) as livreurs, ANY_VALUE(p.date_debut) as date_debut, ANY_VALUE(p.date_fin) as date_fin, ANY_VALUE(c.date_connexion) as date_connexion, ANY_VALUE(c.date_deconnexion) as date_deconnexion FROM livreurs_planning p LEFT JOIN livreurs_connexion lc ON p.id_commercant=lc.id LEFT JOIN restaurants r ON r.id = lc.id_commercant WHERE p.date_debut BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW() ".$_SESSION["req_resto"]." GROUP BY p.id");
		while ($ligne3 = $result3->fetch()) {
			//si le shift est terminé = on compte une absence et qu'il n'y a pas de données de connexion
			if ((strtotime($ligne3["date_fin"])<strtotime(date("Y-m-d H:i:s"))) && ($ligne3["date_connexion"]=="" || $ligne3["date_connexion"]==null)) {
				for($i=0;$i<sizeof($array_livreur);$i++) {
					if($array_livreur[$i]==$ligne["livreurs"]) {
						$add_livreurs=false;
						return;
					}
				}
				if ($add_livreurs) {
					$array_livreur[] = $ligne3["livreurs"];
					$array["nb_absent"]++;
					//compter un absence dans le temps de retard ??
				}
			}
			//sinon on vérifie s'il a du retard
			else if (strtotime($ligne3["date_connexion"])-strtotime($ligne3["date_debut"])>0) {
		        $array["tps_retard"]+=(strtotime($ligne3["date_connexion"])-strtotime($ligne3["date_debut"]));

		    }
		}

		$hours      = floor($array["tps_retard"] / 3600);
		$minutes    = sprintf("%02d", floor(($array["tps_retard"] / 60) % 60));

		$array["tps_retard"]=($hours!=0) ? $hours."h".$minutes : $minutes."mn";

		//STATS LIVRAISONS DES COMMANDES ET PC COMMANDES LIVREES A L'HEURE
        $result = $sql->query("SELECT COUNT(h.id) as nb_commande FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW() AND h.statut='signé'");

//        $result = $sql->query("SELECT COUNT(h.id) as nb_commande FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW() AND h.statut='signé'".$_SESSION["req_resto"]);
        $ligne  = $result->fetch();
		if ($ligne) {
            $array["lad"]=intVal($ligne["nb_commande"]);
        }


        $result = $sql->query("SELECT COUNT(h.id) as nb_commande_heure FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW() AND h.statut='signé' AND (h.date>=c.date_debut AND h.date<=c.date_fin) ");

//        $result = $sql->query("SELECT COUNT(h.id) as nb_commande_heure FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW() AND h.statut='signé' AND (h.date>=c.date_debut AND h.date<=c.date_fin) ".$_SESSION["req_resto"]);
        $ligne  = $result->fetch();
		if ($ligne) {
			if ($ligne["nb_commande_heure"]==$array["lad"] && $array["lad"]!=0){
				$array["livraison_pc"]=100;
			}
			else if ($array["lad"]!=0 && $ligne["nb_commande_heure"]!=0) {
				$array["livraison_pc"]=round(($ligne["nb_commande_heure"]/$array["lad"])*100,1);
			}
        }

        //STATS MOYENNES DES NOTES ATTRIBUEES AUX LIVREURS
        $result = $sql->query("SELECT coalesce(AVG(note),0) as note FROM livreurs_notes WHERE date BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW()".$_SESSION["req_resto"]);
        $ligne = $result->fetch();
		if ($ligne) {
			$array["moyenne_pc"]=round($ligne["note"]*20);
			$array["moyenne"]=round($ligne["note"],1);
        }

        //STATS NB KILOMETRES + CO2 ECONOMISE
        $result = $sql->query("SELECT SUM(c.distance) as nb_km FROM commandes_historique h LEFT JOIN commandes c ON h.id_commande=c.id WHERE h.date BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND NOW() AND h.statut IN ('signé', 'echec')".$_SESSION["req_resto"]);
        $ligne = $result->fetch();
		if ($ligne) {
            $array["nb_km"]=round($ligne["nb_km"]/1000);

            $carbonne_voiture=(($array["nb_km"]*0.06981)*44)/12;
            $carbonne_electrique=(($array["nb_km"]*0.03946)*44)/12;
            $array["nb_co2"]=round($carbonne_voiture-$carbonne_electrique, 3)."kg";
        }

        //STATS PC HORAIRES COMMERCANTS
        $result = $sql->query("SELECT COUNT(*) as reponse_ok, (SELECT COUNT(*)FROM livreurs_planning WHERE date_debut BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND ".$sql->quote(date("Y-m-d").' 23:59:59')." AND id_livreur=0) as reponse_ko FROM livreurs_planning WHERE date_debut BETWEEN ".$sql->quote(date("Y-m-d").' 00:00:00')." AND ".$sql->quote(date("Y-m-d").' 23:59:59')." AND id_livreur!=0 ".$_SESSION["req_resto"]);
        $ligne = $result->fetch();
		if ($ligne) {
			$total=$ligne["reponse_ok"]+$ligne["reponse_ko"];
			if ($ligne["reponse_ok"]==$total && ($ligne["reponse_ok"]!=0 && $total!=0)) {
				$array["reponse_pc"]=100;
			}
			else if ($ligne["reponse_ok"]!=0 && $total!=0){
				$array["reponse_pc"]=round(($ligne["reponse_ok"]/$total)*100,1);
			}
        }

        echo json_encode($array);
		break;


    case "get_commercant_service":
        $Livreur=new Livreur($sql);
        $Commercant=new Commercant($sql);
        foreach($Commercant->getAllService() as $commercant) {
            ?>
            <div class="commercant">
                <div class="commercant-item">
                    <div class="commercant-item-title"><?=$commercant->nom?></div>
                    <div class="commercant-item-etat">
                        <div class="etat"></div>
                        <i class="clip-chevron-down"></i>
                    </div>
                    <div class="stop"></div>
                </div>
                <div class="commercant-detail">
                    <?php
                    foreach ($Commercant->getAllLivreur($commercant->id_commercant) as $livreur_commercant) {
                        ?>
                        <div class="commercant-detail-item">
                            <span><?=$livreur_commercant->nom." ".$livreur_commercant->prenom?></span>
                            <div class="commercant-detail-item-etat <?php if($livreur_commercant->statut=='ON') echo 'actif';?>"></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php }
        break;


    case "getLivreurShift":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
        if(isset($_GET["id_planning"]))     {$id_planning   = $_GET["id_planning"];}    else{$id_planning="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getShiftByLivreur($id_livreur);

        foreach ($plannings as $planning){
            $vide=false;
            ?>
            <tr>
                <td><?=date("d/m/Y", strtotime($planning->date_debut))?></td>
                <td><?=$planning->nom_resto?></td>
                <td><?= date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                <td><?=$planning->nom_vehicule?></td>
            </tr>
            <?php
            }
        if ($vide) {
            ?>
            <tr>
                <td colspan="7">il n'y a pas de shift pour le moment </td>
            </tr>
            <?php
            }

        ?>

        <?php
        break;

    case "getLivreurShiftDay":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
        if(isset($_GET["id_planning"]))     {$id_planning   = $_GET["id_planning"];}    else{$id_planning="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getShiftByLivreur($id_livreur, true);

        foreach ($plannings as $planning){
            $vide=false;
            ?>
            <tr>
                <td><b><?=$planning->nom_resto."</b><br>".$planning->adresse_resto?></td>
                <td><?= date("H:i", strtotime($planning->date_debut)) . " - " . date("H:i", strtotime($planning->date_fin))?></td>
                <td><?=$planning->nom_vehicule?></td>
            </tr>
            <?php
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="7">il n'y a pas de shift pour le moment </td>
            </tr>
            <?php
        }

        ?>


        <?php
        break;

    case "get_presence":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
        if(isset($_GET["id_planning"]))     {$id_planning   = $_GET["id_planning"];}    else{$id_planning="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getPresence($page, 10, $id_livreur);

            foreach ($plannings as $planning){
                $vide=false;
                ?>
                <tr>
                    <td><?=date("d/m/y",strtotime($planning->debut))?></td>
                    <td><?=date("H:i",  strtotime($planning->debut)) . " - " . date("H:i", strtotime($planning->fin))?></td>
                    <td><?=$planning->libelle_commercant?></td>
                    <td><?=$planning->nom_vehicule?></td>
                    <td><?=date("H:i:s",strtotime($planning->connexion))?></td>
                    <td><?=$planning->travail?></td>

                </tr>
                <?php
            }
            if ($vide) {
                ?>
                <tr>
                    <td colspan="7">il n'y a pas de connexion pour le moment</td>
                </tr>
                <?php
            }
            ?>

        <?php

        break;

    case "get_presence_day":
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_commercant"]))   {$id_commercant = $_GET["id_commercant"];}  else{$id_commercant ="";}
        if(isset($_GET["id_vehicule"]))     {$id_vehicule   = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
        if(isset($_GET["id_planning"]))     {$id_planning   = $_GET["id_planning"];}    else{$id_planning="";}

        $vide       = true;

        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getPresence($page, 10, $id_livreur, true);

        foreach ($plannings as $planning){
            $vide=false;
            ?>
            <tr>
                <td><b><?=$planning->libelle_commercant."</b><br>".$planning->adresse?></td>
                <td><?=date("H:i",  strtotime($planning->debut)) . " - " . date("H:i", strtotime($planning->fin))?></td>
                <td><?=$planning->nom_vehicule?></td>
                <td><?=date("H:i:s",strtotime($planning->connexion))?></td>
                <td><?=$planning->travail?></td>
            </tr>
            <?php
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="7">il n'y a pas de connexion pour le moment</td>
            </tr>
            <?php
        }
        ?>

        <?php

        break;

    case "count_presence_month":
    if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur="";}
    if(isset($_GET["id_planning"])) {$id_planning   = $_GET["id_planning"];}    else{$id_planning="";}


    $Livreur    = new Livreur($sql);
    $plannings  = $Livreur->getHoursMonth($id_livreur, false);

    echo $plannings;

    break;

    case "count_presence_day":
        if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur="";}
        if(isset($_GET["id_planning"])) {$id_planning   = $_GET["id_planning"];}    else{$id_planning="";}


        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getHoursMonth($id_livreur, true);

        echo $plannings;

        break;


    case "count_theorique_month":
        if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur="";}
        if(isset($_GET["id_planning"])) {$id_planning   = $_GET["id_planning"];}    else{$id_planning="";}


        $Livreur    = new Livreur($sql);
        $plannings  = $Livreur->getHoursTheoriqueMonth($id_livreur);

        echo $plannings;

        break;


    case "export_month_presence":
        if(isset($_GET["p"]))               {$page          = $_GET["p"];}              else{$page          =1;}
        if(isset($_GET["id_livreur"]))      {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur    ="";}
        if(isset($_GET["id_planning"]))     {$id_planning   = $_GET["id_planning"];}    else{$id_planning   ="";}
        if(isset($_GET["id"]))		        {$id            = $_GET["id"];}             else{$id="";}


        $nomfic = 'exports/presence_'.date("Ymd-His").'.csv';
        $fp     = fopen($nomfic, 'w');

        $titre  = array('Livreur', 'Date', 'Commercant', 'horaire', 'Vehicule', 'heure connexion', 'heure effectue');
        fputcsv($fp, $titre, ';');

        $Livreur    = new Livreur($sql);
        $presences  = $Livreur->getPresence("", "", $id_livreur);

        foreach ($presences as $presence) {
            $lignecsv = array( utf8_decode($presence->prenom_livreur)." ".utf8_decode($presence->nom_livreur), date("d/m/Y", strtotime($presence->date_connexion)), "".utf8_decode($presence->libelle_commercant), date("H:i",  strtotime($presence->debut)). " - " .date("H:i", strtotime($presence->fin)), "".$presence->nom_vehicule,  "".date("H:i:s",strtotime($presence->connexion)), "".$presence->travail);
            fputcsv($fp, $lignecsv, ';');
        }

        fclose($fp);
        header("location: ".$nomfic);
        break;


    case "livreur_licencie":
        if(isset($_GET["p"]))       {$page  = $_GET["p"];}      else{$page=1;}
        if(isset($_GET["nom"]))		{$nom   = $_GET["nom"];}    else{$nom="";}
        if(isset($_GET["statut"]))	{$statut= $_GET["statut"];} else{$statut="";}
        if(isset($_GET["numero"]))	{$numero= $_GET["numero"];} else{$numero="";}

        $vide           = true;

        $liste_livreur  = new Livreur($sql);
        $livreurs       = $liste_livreur->getLicencie();

        foreach ($livreurs as $livreur) {
            $vide=false;
            ?>
            <tr>
                <td><?=$livreur->prenom ." ". $livreur->nom ?></td>
                <td>
                    <a href="livreurs_planning.php?livreur_get=<?=$livreur->id?>" class="btn btn-green tooltips"    data-placement="top" data-original-title="Planning">            <i class="fa fa-calendar "></i></a>
                    <a href="shift_livreur.php?id=<?=$livreur->id?>             " class="btn btn-purple tooltips"   data-placement="top" data-original-title="Activité du livreur"> <i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                    <a href="livreurs_fiche2.php?id=<?=$livreur->id?>           " class="btn btn-primary tooltips"  data-placement="top" data-original-title="Fiche du livreur">    <i class="fa fa-search"></i></a>
                    <a onclick="affecte_suppid('<?=$livreur->id?>')" href="#myModal4" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Désarchiver"><i class="fa fa-reply fa fa-white"></i></a>

                </td>
            </tr>
            <?php
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="6">Pas de résultats</td>
            </tr>
            <?php
        }
        break;


    case "puce_liste":
    if(isset($_GET["p"]))       {$page  = $_GET["p"];}      else{$page=1;}
    if(isset($_GET["number"]))	{$number = $_GET["number"];} else{$number="";}

    $vide   = true;
    $puce_liste = new PhoneNumber($sql);
    $Puces = $puce_liste->getAll($page, 10, $number);

    foreach ($Puces as $puce){
        $vide=false;
        ?>
        <tr>
            <td><?=$puce->number?></td>
            <td>
                <a href="#" class="btn btn-primary tooltips"  data-placement="top" data-original-title="Historique de la puce">    <i class="fa fa-search"></i></a>
                <a href="#" class="btn btn-primary tooltips"  data-placement="top" data-original-title="Modifier le numéro">    <i class="fa fa-search"></i></a>
                <a href="#myModal2" role="button" data-toggle="modal" class="btn btn-primary tooltips"  data-placement="top" data-original-title="Attribuer un livreur">    <i class="fa fa-search"></i></a>

                <a onclick="affecte_suppid('<?= $puce->id ?>')" href="#myModal3" role="button" data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer la puce"> <i class="fa fa-times fa fa-white"></i></a>
            </td>
        </tr>
        <?php
    }
    if ($vide) {
        ?>
        <tr>
            <td colspan="6">Il n'y a aucune puce</td>
        </tr>
        <?php
    }

    break;

    case "phone_liste":
        if(isset($_GET["p"]))       {$page  = $_GET["p"];}      else{$page=1;}
        if(isset($_GET["modele"]))	{$modele = $_GET["modele"];} else{$modele="";}

        $vide   = true;
        $phone_liste = new Phone($sql);
        $Phones = $phone_liste->getAll($page, 10, $modele);

        foreach ($Phones as $phone){
            $vide=false;
            ?>
            <tr>
                <td><?=$phone->marque?></td>
                <td><?=$phone->modele?></td>
                <td><?=$phone->quantite?></td>
                <td>
                    <a href="#" class="btn btn-primary tooltips"  data-placement="top" data-original-title="Historique de la puce">    <i class="fa fa-search"></i></a>
                    <a href="#" class="btn btn-primary tooltips"  data-placement="top" data-original-title="Modifier le numéro">    <i class="fa fa-search"></i></a>
                    <a href="#myModal2" role="button" data-toggle="modal" class="btn btn-primary tooltips"  data-placement="top" data-original-title="Attribuer un livreur">    <i class="fa fa-search"></i></a>

                    <a onclick="affecte_suppid('<?= $phone->id ?>')" href="#myModal3" role="button" data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer la puce"> <i class="fa fa-times fa fa-white"></i></a>
                </td>
            </tr>
            <?php
        }
        if ($vide) {
            ?>
            <tr>
                <td colspan="6">Il n'y a aucune puce</td>
            </tr>
            <?php
        }

        break;


}
?>