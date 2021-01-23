<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		            {$id                    = $_GET["id"];}                 else{$id="";}
if(isset($_GET["commercant"]))		    {$commercant_txt        = $_GET["commercant"];}         else{$commercant_txt="";}
if(isset($_GET["statut"]))		        {$statut_txt            = $_GET["statut"];}             else{$statut_txt="";}
if(isset($_GET["periode"]))		        {$periode_txt           = $_GET["periode"];}            else{$periode_txt="";}
if(isset($_GET["aff_valide_livreur"]))	{$aff_valide_livreur    = $_GET["aff_valide_livreur"];} else{$aff_valide_livreur="";}
if(isset($_GET["aff_valide_planning"]))	{$aff_valide_planning   = $_GET["aff_valide_planning"];}else{$aff_valide_planning="";}
if(isset($_GET["tab_actif"]))		    {$tab_actif             = $_GET["tab_actif"];}          else{
    if(isset($_POST["tab_actif"]))		{$tab_actif             = $_POST["tab_actif"];}else{$tab_actif="tab1";}
}
if(isset($_POST["action"]))		        {$action=$_POST["action"];}                             else{$action="";}

if ($commercant_txt=="" && $statut_txt=="" && $periode_txt=="") {
	$filtre         = 'style="display:none;"';
	$filtre_fleche  = "expand";
}
else {
	$filtre_fleche  = "collapses";
}

$menu       = "livreur";
$sous_menu  = "liste";
$aff_erreur = "";
$continu    = true;

$Vehicule   = new Vehicule($sql);
$Commercant = new Commercant($sql);
$Livreur    = new Livreur($sql, $id);

$Livreur->getPaginationCommande(30, $id, $commercant_txt, $statut_txt, $periode_txt);
$nbpages= $Livreur->getNbPagesCommande();
$nbres  = $Livreur->getNbResCommande();

if ($nbpages==0) {
	$nbpages++;
}

if ($id!="") {
	$nom        = $Livreur->getNom();
	$prenom     = $Livreur->getPrenom();
	$email      = $Livreur->getEmail();
	$password   = $Livreur->getPassword();
	$telephone  = $Livreur->getTelephone();
	$nbheures   = $Livreur->getNbHeures();
	$note       = $Livreur->getNote();
    $situation  = $Livreur->getSituation();
    $etat       = $Livreur->getEtat();

	if ($Livreur->getDateConnexion()!="" && $Livreur->getDateConnexion()!="1970-01-01 00:00:00") {
		$app="Dernière connexion le : ".date("d/m/Y \à H:i", strtotime($Livreur->getDateConnexion()));
	}
	else {
		$app="";
	}
	
	if ($Livreur->getPhoto()!="") {
		$source_photo="upload/livreurs/".$Livreur->getPhoto();
	}
}

if($action=="enregistrer_livreur"){
	$nom        = $_POST["nom"];
	$prenom     = $_POST["prenom"];
	$email      = $_POST["email"];
	$password   = $_POST["password"];
	$telephone  = $_POST["telephone"];
	$nbheures   = $_POST["nbheures"];
    $situation  = $_POST["situation"];
    $etat    = $_POST["etat"];
	$photo      = $_FILES['ImageFile'];
	
	if($nom==""){
		$css_nom_obl = "has-error";
		$continu = false;
	}
	if($prenom==""){
		$css_prenom_obl = "has-error";
		$continu = false;
	}
	if($email==""){
		$css_email_obl = "has-error";
		$continu = false;
	}
	if($password==""){
		$css_password_obl = "has-error";
		$continu = false;
	}
	if($telephone==""){
		$css_telephone_obl = "has-error";
		$continu = false;
	}
	if($nbheures==""){
		$css_nbheures_obl = "has-error";
		$continu = false;
	}
    if($situation==""){
        $css_situation_obl = "has-error";
        $continu = false;
    }
    if($etat==""){
        $css_Etat_obl = "has-error";
        $continu = false;
    }


	if($continu){
		$Livreur->setLivreur($id, $nom, $prenom, $email, $password, $telephone, $nbheures, $situation, $etat);
		if ($photo!='') {
			$directory="livreurs";
            include("action_photo.php");
        }
        else {
        	$Livreur->setPhoto($id, '');
        }
		header("location: livreurs_fiche2.php?aff_valide_livreur=1&id=".$id."&".$situation);
	}else{
		$aff_erreur_livreur="1";		
	}
}

if ($action=="enregistrer_calendar_theorique") {
	$id_vehicule        = $_POST["vehicule"];
	$id_commercant      = $_POST["commercant"];
	$date               = $_POST["date"];
	$h_debut            = $_POST["h_debut"];
	$h_fin              = $_POST["h_fin"];
	$check_recurrence   = $_POST["check_recurrence"];
	$date_debut_rec     = $_POST["date_debut_rec_txt"];
	$date_fin_rec       = $_POST["date_fin_rec_txt"];
	$h_debut_rec        = $_POST["h_debut_rec_txt"];
	$h_fin_rec          = $_POST["h_fin_rec_txt"];
	$jours_rec          = $_POST["jours_rec"];

	$msg_erreur_planning="Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.";
	
	if($id_commercant==""){
		$css_commercant_obl="has-error";
		$continu = false;
	}
	if($date==""){
		$css_date_obl="has-error";
		$continu = false;
	}
	if($h_debut==""){
		$css_hdebut_obl="has-error";
		$continu = false;
	}
	if($h_fin==""){
		$css_hfin_obl="has-error";
		$continu = false;
	}
	if($h_debut!="" && $h_fin!="" && $date!="") {
		$datetime   = new DateTime();
		$today      = $datetime->createFromFormat('d-m-Y H:i:s', date('d-m-Y')." 00:00:00");
		$date_check = $datetime->createFromFormat('d-m-Y H:i:s', $date." 00:00:00");
		$date_debut = $datetime->createFromFormat('d-m-Y H:i', $date." ".$h_debut);
		$date_fin   = $datetime->createFromFormat('d-m-Y H:i', $date." ".$h_fin);
		if ($date_debut>=$date_fin) {
			$css_hfin_obl   = "has-error";
			$css_hdebut_obl = "has-error";
			$continu        = false;
		}

		if ($date_check<$today) {
			$css_date_obl   = "has-error";
			$continu        = false;
		}
	}

	if ($check_recurrence=="recurrence") {
		for ($i = strtotime($date_debut_rec); $i <= strtotime($date_fin_rec); $i = strtotime('+1 day', $i)) {
			$jours_rec_tab=explode(";", $jours_rec);
			foreach($jours_rec_tab as $item) {
			  	if (date('N', $i) == $item) {
		    		//echo date('Y-m-d', $i);
		    		if ($id_vehicule!="" && $Vehicule->checkVehicule($id_vehicule, $id, date('Y-m-d', $i)." ".$h_debut_rec, date('Y-m-d', $i)." ".$h_fin_rec)) {
						$css_vehicule_obl   = "has-error";
						$css_hfin_obl       = "has-error";
						$css_hdebut_obl     = "has-error";
						$msg_erreur_planning= "Ce véhicule est déjà utilisé pendant cette période.";
						$continu            = false;
						break;
					}
		    	}
			}
		}
	}
	else {
		if ($id_vehicule!="" && $Vehicule->checkVehicule($id_vehicule, $id, date("Y-m-d", strtotime($date))." ".$h_debut, date("Y-m-d", strtotime($date))." ".$h_fin)) {
			$css_vehicule_obl   = "has-error";
			$css_hfin_obl       = "has-error";
			$css_hdebut_obl     = "has-error";
			$msg_erreur_planning= "Ce véhicule est déjà utilisé pendant cette période.";
			$continu            = false;
		}
	}

	if($continu) {
		if ($check_recurrence=="recurrence") {
			for ($i = strtotime($date_debut_rec); $i <= strtotime($date_fin_rec); $i = strtotime('+1 day', $i)) {
				$jours_rec_tab=explode(";", $jours_rec);
				foreach($jours_rec_tab as $item) {
				  	if (date('N', $i) == $item) {
			    		//echo date('Y-m-d', $i);
			    		$Livreur->setPlanning($id, $id_commercant, $id_vehicule, date('Y-m-d', $i)." ".$h_debut_rec, date('Y-m-d', $i)." ".$h_fin_rec, "oui");
			    	}
				}
			}
			header("location:livreurs_fiche2.php?aff_valide_planning=1&id=".$id."&tab_actif=tab2");
		}
		else {
			$Livreur->setPlanning($id, $id_commercant, $id_vehicule, date("Y-m-d", strtotime($date))." ".$h_debut, date("Y-m-d", strtotime($date))." ".$h_fin, "non");
			header("location:livreurs_fiche2.php?aff_valide_planning=1&id=".$id."&tab_actif=tab2");	
		}	
	}
	else{
		$aff_erreur_planning="1";		
	}
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css">
<link rel="stylesheet" href="assets/plugins/fullcalendar/fullcalendar/fullcalendar.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css" type="text/css"/>
<style>
	.tab_btn {
		border:1px solid #9fc752 ;
		padding:10px;
		text-align:center;
		cursor:pointer;
	}

	.btn_actif {
		background-color:#9fc752;
		color:white;
	}

	#tab1, #tab2, #tab3, #tab4 {
		margin-top:15px;
	}

	#info_calendar {
	    position: absolute;
	    z-index:500;
	}
	
	.triangle-border {
	  position:relative;
	  padding:15px;
	  margin:1em 0 3em;
	  border:1px solid #000;
	  color:#333;
	  background:#fff;
	}

	.triangle-border:before {
	  content:"";
	  position:absolute;
	  bottom:-14px; /* value = - border-top-width - border-bottom-width */
	  left:47px; /* controls horizontal position */
	  border-width:13px 13px 0;
	  border-style:solid;
	  border-color:#000 transparent;
	  /* reduce the damage in FF3.0 */
	  display:block;
	  width:0;
	}

	/* creates the smaller  triangle */
	.triangle-border:after {
	  content:"";
	  position:absolute;
	  bottom:-13px; /* value = - border-top-width - border-bottom-width */
	  left:47px; /* value = (:before left) + (:before border-left) - (:after border-left) */
	  border-width:13px 13px 0;
	  border-style:solid;
	  border-color:#fff transparent;
	  /* reduce the damage in FF3.0 */
	  display:block;
	  width:0;
	}

	.triangle-border2:before, .triangle-border2:after {
	  left:150px; /* controls horizontal position */
	}

	#tooltip_table th, #tooltip_table td {
		padding:5px 10px;
	}

	.has-error2 {
		border: 1px solid #a94442 !important;
	}

	.tooltips {
    	z-index:9999999;
    }

    .tooltip-inner {
		white-space: pre-wrap;
		max-width: 500px;
		width:220px;
		z-index:9999999;
	}
        
    @media(max-width:600px){
        .liste-livreur span{
            display:block;
            margin-bottom: 10px;
        }
    }
    
    @media(max-width:767px){
        .form-group-r p{margin-top:15px;}
        #calendar_theorique_list{margin-top:50px;}
        #calendar_theorique_list .table.table-bordered.table-hover{margin-top: 0 !important;}
        #calendar_presence_list{margin-top:50px;}
        #calendar_presence_list .table.table-bordered.table-hover{margin-top:0 !important;}
    }
    
    .select2-container .select2-choice .select2-arrow b{background: none !important;}
</style>

<!-- start: PAGE -->
<link rel="stylesheet" type="text/css" href="assets/css/magnific-popup.css">
<div style="display:none;">
    <a class="pop-up-generique" href=""></a>
</div>
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1>Profil de <?=$prenom." ".$nom?></h1>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
		<div class="row">
			<div class="col-sm-12 liste-livreur">
                <span class="tab_btn btn_actif col-sm-2 col-sm-offset-2"    id="tab1_btn" onclick="show_div('tab1');">Informations</span>
                <span class="tab_btn col-sm-2"                              id="tab2_btn" onclick="show_div('tab2');">Planning thérorique</span>
                <span class="tab_btn col-sm-2"                              id="tab3_btn" onclick="show_div('tab3');">Planning de présence</span>
                <span class="tab_btn col-sm-2"                              id="tab4_btn" onclick="show_div('tab4');">Commandes</span>
			</div>
		</div>

		<div id="tab1">
			<?php
			if($aff_erreur_livreur=="1"){
				?>
	            <div class="alert alert-danger">
	                <button class="close" data-dismiss="alert">
	                    ×
	                </button>
	                <i class="fa fa-check-circle"></i>
	                Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.
	            </div>                                            
	            <?php	
			}
			?>
			<?php
			if($aff_valide_livreur=="1"){
			?>
	        <div class="alert alert-success">
	            <button class="close" data-dismiss="alert">
	                ×
	            </button>
	            <i class="fa fa-check-circle"></i>
	            Les modifications ont été enregistrées.
	        </div>                    
			<?php } ?>
			<div class="row">
				<div class="col-sm-12">
					<form role="form" name="form" id="form1" method="post" action="livreurs_fiche2.php?id=<?php echo $id; ?>" class="form-horizontal" enctype="multipart/form-data">
		            	<input type="hidden" name="action" value="enregistrer_livreur"/>
		            	<div class="form-group">
		                    <label class="col-sm-4 control-label">
		                        Note
		                    </label>
		                    <div class="col-sm-4" style="padding-top:4px">
		                        <?php
								for($x=1;$x<=5;$x++){
									if($note>=$x){
										$etoile_src = "notation-on.png";
									}else{
										$etoile_src = "notation-off.png";
									}
									?>
		                            <img class="note_etoile" src="images/<?=$etoile_src?>"/>                                
	                                <?php	
								}
								?>
								<span class="note_texte">Moyenne : <?=$note+0?>/5 <span style="text-decoration:underline;cursor:pointer;" onclick="openPopup('popup_note.php?id=<?=$id?>')">(<?=($Livreur->getNbNote($id)>1) ? $Livreur->getNbNote($id)." votes" : $Livreur->getNbNote($id)." vote"?>)</span></span>
		                    </div>
		                </div>
		                <div class="form-group">
		                    <label class="col-sm-4 control-label">
		                        Application
		                    </label>
		                    <div class="col-sm-4">
		                        <input type="text" name="app" placeholder="Dernière connexion" class="form-control" value="<?=$app?>" disabled>
		                    </div>
		                </div>
		                <div class="form-group <?php echo $css_nom_obl; ?>">
		                    <label class="col-sm-4 control-label">
		                        Nom
		                    </label>
		                    <div class="col-sm-4">
		                        <input type="text" name="nom" placeholder="Nom" class="form-control" value="<?=$nom?>">
		                    </div>
		                </div>
		                <div class="form-group <?php echo $css_prenom_obl; ?>">
		                    <label class="col-sm-4 control-label">
		                        Prénom
		                    </label>
		                    <div class="col-sm-4">
		                        <input type="text" name="prenom" placeholder="Prénom" class="form-control" value="<?=$prenom?>">
		                    </div>
		                </div>
		                <div class="form-group <?php echo $css_email_obl; ?>">
		                    <label class="col-sm-4 control-label">
		                        Email
		                    </label>
		                    <div class="col-sm-4">
		                        <input type="text" name="email" placeholder="Email" class="form-control" value="<?=$email?>">
		                    </div>
		                </div>
		                <div class="form-group <?php echo $css_password_obl; ?>">
		                    <label class="col-sm-4 control-label">
		                        Mot de passe
		                    </label>
		                    <div class="col-sm-4">
		                        <input type="password" name="password" placeholder="Mot de passe" class="form-control" value="<?=$password?>">
		                    </div>
		                </div>
		                <div class="form-group <?php echo $css_telephone_obl; ?>">
		                    <label class="col-sm-4 control-label">
		                        Téléphone
		                    </label>
		                    <div class="col-sm-4">
		                        <input type="text" name="telephone" placeholder="Téléphone" class="form-control" value="<?=$telephone?>">
		                    </div>
		                </div>
		                <div class="form-group">
		                    <label class="col-sm-4 control-label" for="ImageFile">
		                        Photo
		                    </label>
		                    <div class="col-sm-4">
		                        <div class="fileupload <?php echo ($source_photo=='') ? 'fileupload-new' : 'fileupload-exists' ;?>" data-provides="fileupload">
		                        	<div class="fileupload-new thumbnail" style="max-width: 200px; max-height:200px;">
			                        	<img src="https://www.placehold.it/300x300/EFEFEF/AAAAAA?text=no+image" alt="">
		                            </div>
		                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 200px; line-height: 20px;">
		                            	<?php if ($source_photo!="") { ?>
			                        		<img src="<?=$source_photo?>" alt="">
			                        	<?php } ?>
		                            </div>
		                            <div>  
	                            		<span class="btn btn-light-grey btn-file">
	                            			<span class="fileupload-new"><i class="fa fa-picture-o"></i> Choisir une image</span>
	                            			<span class="fileupload-exists"><i class="fa fa-picture-o"></i> Changer</span>
		                                    <input type="file" name="ImageFile" id="ImageFile"/>
		                                </span>
		                                <a href="#" class="btn fileupload-exists btn-light-grey" data-dismiss="fileupload">
		                                    <i class="fa fa-times"></i> Supprimer
		                                </a>                                     
		                            </div>
		                        </div>
		                        <div class="alert alert-info">
									<i class="fa fa-info-circle"></i>
									L'image doit être au format JPG, PNG ou GIF et ne doit pas faire plus de 2Mb.
								</div>
		                    </div>
		                </div>
		                <div class="form-group <?php echo $css_nbheures_obl; ?>">
		                    <label class="col-sm-4 control-label">
		                        Nombre d'heures par semaine
		                    </label>
		                    <div class="col-sm-4">
		                        <input type="text" name="nbheures" placeholder="Nombre d'heures par semaine" class="form-control" value="<?=$nbheures?>">
		                    </div>
		                </div>

                        <div class="form-group <?=$css_situation_obl?>">
                            <label class="col-sm-4 control-label">Situation</label>
                            <div class="col-sm-4 margin_label">
                                <select name="situation" id="situation" class="form-control">
                                    <option value="">&nbsp;</option>
                                    <option value="actif"   <?php if($situation=="actif")   echo "selected";?>>Actif</option>
                                    <option value="inactif" <?php if($situation=="inactif") echo "selected";?>>Inactif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group <?=$css_Etat_obl?>">
                            <label class="col-sm-4 control-label">Etat</label>
                            <div class="col-sm-4 margin_label">
                                <select name="etat" id="etat" class="form-control">
                                    <option value="">&nbsp;</option>
                                    <option value="disponible"  <?php if($etat=="disponible")   echo "selected";?>>Disponible</option>
                                    <option value="relayant"    <?php if($etat=="relayant")     echo "selected";?>>Relayant</option>
                                    <option value="en attente"  <?php if($etat=="en attente")   echo "selected";?>>En attente</option>
                                    <option value="repos"       <?php if($etat=="repos")        echo "selected";?>>Repos</option>
                                    <option value="en congé"    <?php if($etat=="en congé")     echo "selected";?>>En congé</option>
                                    <option value="malade"      <?php if($etat=="malade")       echo "selected";?>>Malade</option>
                                </select>
                            </div>
                        </div>

		                 <div class="row row_btn">
	                    	<div class="col-sm-4 col-sm-offset-8" style="text-align:right">
	                    		<input type="button" onclick="lien('livreurs_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
	                    		&nbsp;
		                        <input type="submit" id="bt" class="btn btn-main" value="Enregistrer" style="width:100px;">
	                        </div>
	                    </div> 
		            </form>                                              
		        </div>
	        </div>
	    </div>

	    <div id="tab2" style="display:none">
	    	<?php
			if($aff_erreur_planning=="1"){
				?>
	            <div class="alert alert-danger">
	                <button class="close" data-dismiss="alert">
	                    ×
	                </button>
	                <i class="fa fa-check-circle"></i>
	                <?=$msg_erreur_planning?>
	            </div>                                            
	            <?php	
			}
			?>
			<?php
			if($aff_valide_planning=="1"){
			?>
	        <div class="alert alert-success">
	            <button class="close" data-dismiss="alert">
	                ×
	            </button>
	            <i class="fa fa-check-circle"></i>
	            Les modifications ont été enregistrées.
	        </div>                    
			<?php } ?>
	    	<div class="row">
	    		<div class="col-sm-12">
	    			<div style="border:1px solid #eee;padding:25px;margin-bottom:25px">
	                    <form role="form" name="form_calendar_theorique" id="form_calendar_theorique" method="post" action="livreurs_fiche2.php?id=<?php echo $id;?>&tab_actif=tab2" class="form-horizontal">
			            	<input type="hidden" name="action"  value="enregistrer_calendar_theorique"/>
			            	<input type="hidden" name="h_debut_txt"         id="h_debut_txt"        value="<?=$h_debut?>"/>
			            	<input type="hidden" name="h_fin_txt"           id="h_fin_txt"          value="<?=$h_fin?>"/>
			            	<input type="hidden" name="date_debut_rec_txt"  id="date_debut_rec_txt" value="<?=$date_debut_rec?>"/>
			            	<input type="hidden" name="date_fin_rec_txt"    id="date_fin_rec_txt"   value="<?=$date_fin_rec?>"/>
			            	<input type="hidden" name="h_debut_rec_txt"     id="h_debut_rec_txt"    value="<?=$h_debut_rec?>"/>
			            	<input type="hidden" name="h_fin_rec_txt"       id="h_fin_rec_txt"      value="<?=$h_fin_rec?>"/>
			            	<input type="hidden" name="jours_rec"           id="jours_rec"          value="<?=$jours_rec?>"/>

			            	<div class="form-group form-group-r">
			                    <div class="col-sm-4">
			                    	<p><b>Livreur</b></p>
			                    	<select name="livreur" id="livreur" class="form-control" disabled>
			                            <option value="<?=$id?>"><?=$prenom." ".$nom?></option>
			                        </select>
			                    </div>
			                    <div class="col-sm-4">
			                    	<p><b>Commerçant</b></p>
			                    	<select name="commercant" id="commercant" class="form-control search-select <?php echo $css_commercant_obl; ?>">
									    <option value="">&nbsp;</option>
									    <?php 
											foreach ($Commercant->getAll("", "") as $commercant) {
												$sel=($id_commercant==$commercant->id) ? "selected" : "";
												echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom."</option>";
											}
									    ?>
									</select>
			                    </div>
			                    <div class="col-sm-2 <?php echo $css_hdebut_obl; ?>">
			                        <p><b>Heure de début</b></p>
			                        <div class="input-group input-append bootstrap-timepicker">
										<input type="text" id="h_debut" name="h_debut" class="form-control timepicker">
										<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
									</div>
			                    </div>
			                    <div class="col-sm-2 <?php echo $css_hfin_obl; ?>">
			                        <p><b>Heure de fin</b></p>
			                    	<div class="input-group input-append bootstrap-timepicker">
										<input type="text" id="h_fin" name="h_fin" class="form-control timepicker2">
										<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
									</div>
			                    </div>
			                </div>

			                <div class="form-group form-group-r">
			                	<div class="col-sm-4 <?php echo $css_vehicule_obl; ?>">
			                    	<p><b>Véhicule</b></p>
			                    	<select name="vehicule" id="vehicule" class="form-control search-select">
									    <option value="">&nbsp;</option>
									    <?php 
											foreach ($Vehicule->getAll("", "", "", "", true) as $vehicule) {
												$sel=($id_vehicule==$vehicule->id) ? "selected" : "";
												echo "<option value='".$vehicule->id."' ".$sel.">".$vehicule->nom."</option>";
											}
									    ?>
									</select>
			                    </div>
			                    <div class="col-sm-4  <?php echo $css_date_obl; ?>">
			                    	<p><b>Date</b></p>
			                    	<div class="input-group">
			                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
										<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date" name="date" value="<?=$date?>">
									</div>
			                    </div>
			                    <div class="col-sm-2">
			                    	<label class="checkbox-inline" style="margin-top: 25px !important;">
                                                        <input type="checkbox" value="recurrence" class="grey" name="check_recurrence" id="check_recurrence" <?php if ($check_recurrence=="recurrence") echo "checked"; ?>>
                                                        <b>Récurrence</b>
                                                </label>
			                    </div>
                                            <div class="col-sm-2">
                                                <input type="submit" id="bt" class="btn btn-main" value="Enregistrer" style="margin-top:15px; float:right;">
                                            </div>
			                </div>
			            </form>
			        </div>
	    		</div>
	    	</div>
	    	<div class="row">
				<div class="col-sm-12">
	                <div class="panel panel-default">
						<div class="panel-heading" style="padding-left:10px">Planning</div>
						<div class="panel-body">
							<div class="btn-group btn-group-sm" style="position:absolute;right:30px">
					    		<a class="btn btn-default active calendar_theorique_btn"    id="calendar_theorique_calendar_btn"    href="javascript:void(0);" onclick="switch_view('calendar_theorique', 'calendar')"> <i class="fa fa-calendar"></i></a>
					    		<a class="btn btn-default calendar_theorique_btn"           id="calendar_theorique_list_btn"        href="javascript:void(0);" onclick="switch_view('calendar_theorique', 'list')">     <i class="fa fa-align-justify"></i></a>

                            </div>
					    	<div class="calendar_theorique" id="calendar_theorique_calendar">
								<div id='calendar_theorique'></div>
							</div>
							<div class="calendar_theorique table-responsive" id="calendar_theorique_list" style="display:none;">

							</div>
						</div>
					</div>
				</div>
			</div>
	    </div>

	    <div id="tab3" style="display:none">
	    	<div class="col-sm-5 col-sm-offset-7" style="text-align:right;margin-bottom:15px;padding-right:0px">
	    		<a class="btn btn-main" href="#myModal2" role="button"  data-toggle="modal">Ajouter des heures supplémentaires</a>
                <a class="btn btn-light-grey" target="_blank" id="export_calendar_presence" style="margin:0 !important;" href="">Exporter en CSV</a>
	    	</div>
	    	<div class="row">
				<div class="col-sm-12">
	                <div class="panel panel-default">
						<div class="panel-heading" style="padding-left:10px">Planning</div>
						<div class="panel-body">
							<div class="btn-group btn-group-sm" style="position:absolute;right:30px">
					    		<a class="btn btn-default active calendar_presence_btn" id="calendar_presence_calendar_btn" href="javascript:void(0);"  onclick="switch_view('calendar_presence', 'calendar')"> <i class="fa fa-calendar"></i></a>
					    		<a class="btn btn-default calendar_presence_btn"        id="calendar_presence_list_btn"     href="javascript:void(0);"  onclick="switch_view('calendar_presence', 'list')">     <i class="fa fa-align-justify"></i></a>
                            </div>

					    	<div class="calendar_presence" id="calendar_presence_calendar">
								<div id='calendar_presence'></div>
							</div>

							<div class="calendar_presence table-responsive" id="calendar_presence_list" style="display:none">
								<span id="calendar_presence_periode"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<p>Total des heures de présence du      <span class="semaine_aff"></span> : <span id="heures_presence"></span></p>
					<p>Total des heures supplémentaires du  <span class="semaine_aff"></span> : <span id="heures_sup_txt"></span></p>
				</div>
			</div>
	    </div>

	    <div id="tab4" style="display:none">
	    	<div class="row header-page">
                    <div class="col-lg-2">
                        <div class="nb_total"><?php echo ($nbres>1) ? $nbres." commandes" : $nbres." commande";?></div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-external-link-square"></i>
                                Formulaire de recherche
                                <div class="panel-tools">
                                    <a class="btn btn-xs btn-link panel-collapse <?=$filtre_fleche?>" href="#">
                                    </a>
                                    <a class="btn btn-xs btn-link panel-refresh" href="#">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="panel-body" <?=$filtre?>>
							<div class="row">
			                    <form class="form-horizontal" role="form" action="livreurs_fiche2.php" method="get">
			                    	<input type="hidden" name="id"          id="id"         value="<?=$id?>"/>
			                    	<input type="hidden" name="tab_actif"   id="tab_actif"  value="tab4"/>
			                    	<div class="form-group">
										<label class="col-sm-3 control-label" for="commercant">
			                                Commerçant
			                            </label>
			                            <div class="col-sm-9">
			                            	<select name="commercant" id="commercant" class="form-control search-select">
											    <option value="">&nbsp;</option>
											    <?php 
													foreach ($Commercant->getAll("", "") as $commercant) {
														$sel=($commercant_txt==$commercant->id) ? "selected" : "";
														echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom."</option>";
													}
											    ?>
											</select>
			                            </div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="form-field-1">
			                                Statut
			                            </label>
			                            <div class="col-sm-9">
			                           	  	<select name="statut" id="statut" class="form-control search-select">
											    <option value="">&nbsp;</option>
											    <option value="ajouté"  <?php if($statut_txt=="ajouté")     echo "selected";?>>Ajoutée</option>
											    <option value="réservé" <?php if($statut_txt=="réservé")    echo "selected";?>>Réservée</option>
											    <option value="récupéré"<?php if($statut_txt=="récupéré")   echo "selected";?>>Récupérée</option>
											    <option value="signé"   <?php if($statut_txt=="signé")      echo "selected";?>>Signée</option>
											    <option value="echec"   <?php if($statut_txt=="echec")      echo "selected";?>>Echec</option>
											</select>
			                            </div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="form-field-1">
			                                Période
			                            </label>
			                            <div class="col-sm-9">
			                           		<div class="input-group">
	                                            <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
	                                            <input id="periode" name="periode" value="<?php echo $periode_txt; ?>" type="text" class="form-control date-time-range">
	                                        </div>
			                            </div>
									</div>
			                        <div style="text-align:center;">
			                        	<input type="submit" id="bt" class="btn btn-main" value="Rechercher">
			                        </div>
								</form>
							</div>
						</div>
					</div>                        
	            </div>
                    
                    <div class="col-lg-4 btn-spe">
	            	<p style="text-align:right">
                            <?php
                                if($_SESSION["admin"]){
                                    ?>
                                    <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_commandes&id_livreur=<?=$id?>&commercant=<?=$commercant_txt?>&statut=<?=$statut_txt?>&periode=<?=$periode_txt?>">Exporter en CSV</a>
                                    <?php
                                }
                            ?>
                        </p>
	            </div>
                    
	        </div>

	          
	        <div id="div_tab_resultat" class="table-responsive">
	        	<table class="table table-bordered table-hover" id="tableau_commandes">
		    		<thead>
                        <th>Immatriculation</th>
		        		<th>Commerçant</th>
		        		<th>Infos client</th>
		        		<th>Contact client</th>
		        		<th>Créneau de livraison</th>
		        		<th>Infos</th>
		        		<th>Statut</th>
		        		<th style="width:50px">Actions</th>
		        	</thead>
		        	<tbody>
		        	</tbody>
		        </table>
	        </div>
	        <div style="text-align:right;">
	        	<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
	        </div>
	    </div>

	    <!-- MODAL -->
	    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="$('#check_recurrence').iCheck('uncheck');">&times;</button>
						<h3>Récurrence</h3>

						<div class="alert alert-danger erreur_modal" style="display:none">
			                <button class="close" data-dismiss="alert">
			                    ×
			                </button>
			                <i class="fa fa-check-circle"></i>
			                Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.
			            </div>      
						
						<div class="row" style="margin-top:40px;padding:0px 20px;">
							<form class="form-horizontal" id="recurrence_form" role="form" action="livreurs_fiche2.php" method="get">
			                	<input type="hidden" name="id" id="id" value="<?=$id?>"/>
			                	<input type="hidden" name="tab_actif" id="tab_actif" value="tab2"/>

			                	<div class="form-group">
				                    <div class="col-sm-12">
		                                Répéter tous les : 
		                            	<label class="checkbox-inline">
											<input type="checkbox" value="1" class="grey" name="day_rec[]">
											L
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" value="2" class="grey" name="day_rec[]">
											M
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" value="3" class="grey" name="day_rec[]">
											M
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" value="4" class="grey" name="day_rec[]">
											J
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" value="5" class="grey" name="day_rec[]">
											V
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" value="6" class="grey" name="day_rec[]">
											S
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" value="7" class="grey" name="day_rec[]">
											D
										</label>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-6">
				                        <p><b>Date de début</b></p>
				                    	<div class="input-group">
				                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
											<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date_debut_rec" name="date_debut_rec" value="">
										</div>
				                    </div>
				                    <div class="col-sm-6">
				                        <p><b>Date de fin</b></p>
				                    	<div class="input-group">
				                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
											<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date_fin_rec" name="date_fin_rec" value="">
										</div>
				                    </div>
				                </div>

				                <div class="form-group">
				                    <div class="col-sm-6">
				                    	<p><b>Heure de début</b></p>
				                        <div class="input-group input-append bootstrap-timepicker">
											<input type="text" id="h_debut_rec" name="h_debut_rec" class="form-control timepicker">
											<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
										</div>
				                    </div>
				                    <div class="col-sm-6">
				                    	<p><b>Heure de fin</b></p>
				                    	<div class="input-group input-append bootstrap-timepicker">
											<input type="text" id="h_fin_rec" name="h_fin_rec" class="form-control timepicker2">
											<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
										</div>
				                    </div>
				                </div>
							</form>
						</div>

						<div style="text-align:center;margin-top:20px;">
							<button onclick="$('#check_recurrence').iCheck('uncheck');" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
								Annuler
							</button>
							<button id="recurrence_btn" class="btn btn-default">
								OK
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>  

	    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Ajouter des heures supplémentaires</h3>

						<div class="alert alert-danger erreur_modal" style="display:none">
			                <button class="close" data-dismiss="alert">
			                    ×
			                </button>
			                <i class="fa fa-check-circle"></i>
			                Ce véhicule est déjà utilisé pendant cette période.
			            </div>      
						
						<div class="row" style="margin-top:40px;padding:0px 20px;">
							<form class="form-horizontal" id="heures_sup_form" role="form" action="livreurs_fiche2.php" method="get">
			                	<input type="hidden" name="id" id="id" value="<?=$id?>"/>
			                	<input type="hidden" name="tab_actif" id="tab_actif" value="tab3"/>
			                	<div class="form-group">
			                        <div class="col-sm-12">
			                        	<p><b>Commerçant</b></p>
			                        	<select name="commercant_hsup" id="commercant_hsup" class="form-control search-select">
										    <option value="">&nbsp;</option>
										    <?php 
												foreach ($Commercant->getAll("", "") as $commercant) {
													echo "<option value='".$commercant->id."' >".$commercant->nom."</option>";
												}
										    ?>
										</select>
			                        </div>
								</div>

								<div class="form-group">
				                    <div class="col-sm-6">
				                    	<p><b>Véhicule</b></p>
				                    	<select name="vehicule_hsup" id="vehicule_hsup" class="form-control search-select">
										    <option value="">&nbsp;</option>
										    <?php 
												foreach ($Vehicule->getAll("", "", "", "", true) as $vehicule) {
													echo "<option value='".$vehicule->id."' >".$vehicule->nom."</option>";
												}
										    ?>
										</select>
				                    </div>
				                    <div class="col-sm-6">
				                        <p><b>Date</b></p>
				                    	<div class="input-group">
				                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
											<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date_hsup" name="date_hsup" value="">
										</div>
				                    </div>
				                </div>

				                <div class="form-group">
				                    <div class="col-sm-6">
				                    	<p><b>Heure de début</b></p>
				                        <div class="input-group input-append bootstrap-timepicker">
											<input type="text" id="h_debut_hsup" name="h_debut_hsup" class="form-control timepicker">
											<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
										</div>
				                    </div>
				                    <div class="col-sm-6">
				                    	<p><b>Heure de fin</b></p>
				                    	<div class="input-group input-append bootstrap-timepicker">
											<input type="text" id="h_fin_hsup" name="h_fin_hsup" class="form-control timepicker2">
											<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
										</div>
				                    </div>
				                </div>
							</form>
						</div>

						<div style="text-align:center;margin-top:20px;">
							<button onclick="" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
								Annuler
							</button>
							<button id="heures_sup_btn" class="btn btn-default">
								OK
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>  

	    <!-- TOOLTIP --> 
		<div id="info_calendar" style="display:none">
			<div class="triangle-border">
				<div style="position:absolute;right:5px;top:0;cursor:pointer" onclick="$('#info_calendar').hide()"><i class="fa fa-times"></i></div>
				<div id="info_content"></div>
			</div>
		</div>
		<!-- end: PAGE CONTENT-->
	</div>
</div>
<!-- end: PAGE -->

<?php
require_once("inc_footer.php");
?>
<script src="assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="assets/plugins/select2/select2.min.js"></script>
<script src="assets/plugins/bootstrap-paginator/src/bootstrap-paginator.js"></script>   
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/fullcalendar/fullcalendar/fullcalendar.js"></script>
<script src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script> 
<script language="javascript">
	$(document).ready(function() {
		runSelect2();
		tableau_resultat(1);
		runPaginator();
        $('.date-time-range').daterangepicker({
			timePicker: true,
			timePickerIncrement: 5,
			firstDay: 1,
			format: 'DD-MM-YYYY hh:mm A'
		});
		runCalendar("calendar_presence");
		runCalendar("calendar_theorique");
		show_div('<?=$tab_actif?>');

		//remplir les heures si elles existent, sinon en mettre par défaut
		var d1      = new Date ();
		var coeff   = 1000 * 60 * 5;
		var rounded = new Date(Math.round(d1.getTime() / coeff) * coeff)
		var heure1  = rounded.getHours();
		var heure2  = rounded.getHours()+1;
		var minute  = rounded.getMinutes();

		if ($("#h_debut_txt").val()!="") {
			heure_deb=$("#h_debut_txt").val()
		}
		else {
			heure_deb=heure1+":"+minute;
		}

		if ($("#h_fin_txt").val()!="") {
			heure_fin=$("#h_fin_txt").val()
		}
		else {
			heure_fin=heure2+":"+minute;
		}

		$('.date-picker').datepicker({
            autoclose: true,
            weekStart: 1
        });
        $('input.timepicker').timepicker({
        	showMeridian: false,
        	minuteStep:5,
        	defaultTime: heure_deb

    	});
        $('input.timepicker2').timepicker({
        	showMeridian: false,
        	minuteStep:5,
        	defaultTime: heure_fin
    	});

    	$("#h_debut, #h_debut_hsup, #h_debut_rec, #h_fin, #h_fin_hsup, #h_fin_rec").on("focus", function() {
		    return $(this).timepicker("showWidget");
		});

		$('#h_debut').timepicker().on('changeTime.timepicker', function(e) {
			//on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
			//TO DO : changer la date si l'heure passe a 1h du jour suivant ?
			var d = new Date("1970-01-01 "+e.time.value+":00");
			d.setHours(d.getHours() + 1);

			$('#h_fin').timepicker('setTime', d.getHours()+":"+d.getMinutes());

	    });
	    $('#h_debut_hsup').timepicker().on('changeTime.timepicker', function(e) {
			//on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
			//TO DO : changer la date si l'heure passe a 1h du jour suivant ?
			var d = new Date("1970-01-01 "+e.time.value+":00");
			d.setHours(d.getHours() + 1);

			$('#h_fin_hsup').timepicker('setTime', d.getHours()+":"+d.getMinutes());

	    });
	    $('#h_debut_rec').timepicker().on('changeTime.timepicker', function(e) {
			//on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
			//TO DO : changer la date si l'heure passe a 1h du jour suivant ?
			var d = new Date("1970-01-01 "+e.time.value+":00");
			d.setHours(d.getHours() + 1);

			$('#h_fin_rec').timepicker('setTime', d.getHours()+":"+d.getMinutes());

	    });
			
		//avoir la date de début et de fin de la semaine en cours (pour export et affichage)
		$("#calendar_theorique").find('.fc-button-prev, .fc-button-next').click(function(){
			getWeek("calendar_theorique");
		});
		$("#calendar_presence").find('.fc-button-prev, .fc-button-next').click(function(){
			getWeek("calendar_presence");
		});

		<?php if ($css_commercant_obl=="has-error") { ?>
			$("#s2id_commercant").find(".select2-choice").addClass("has-error2");
		<?php } ?>
	});	

	$(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });

	function tableau_resultat(p){
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_commande&id_livreur=<?=$id?>&commercant=<?=$commercant_txt?>&statut=<?=$statut_txt?>&periode=<?=$periode_txt?>&histo=1&p='+p,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
				$('#tableau_commandes').find("tbody").html(transport);
				if ($(".tooltips").length) {
					$('.tooltips').tooltip();
				}
			}
		});					
	}

	function runPaginator() {
		$('#paginator-example-1').bootstrapPaginator({
			bootstrapMajorVersion: 3,
			currentPage: 1,
			totalPages: <?php echo $nbpages; ?>,
			onPageClicked: function (e, originalEvent, type, page) {
				tableau_resultat(page);
			}
		});
	}

	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	}

	function runCalendar(calendar_id) {
       	$('#'+calendar_id).fullCalendar({
            buttonText: {
                prev: '<i class="fa fa-chevron-left"></i>',
                next: '<i class="fa fa-chevron-right"></i>'
            },
            header: {
                left: 'prev,next title',
                center: '',
                right: ''
            }, 
            events: 'feed_livreurs.php?id=<?=$id?>&action='+calendar_id,
            eventRender: function (event, element) {
			    element.find('.fc-event-title').html(event.title);
			    element.attr("data-placement", "top");
			    element.attr("data-original-title", event.tooltip)
			},
			eventMouseover: function(calEvent, jsEvent) {
			    $(this).tooltip('show');
			},
			eventMouseout: function(calEvent, jsEvent) {
			    $(this).tooltip('hide');
			},
            columnFormat: {
                agendaWeek: 'ddd dd/MM'
            },
            titleFormat: {
			   week: "dd [MMMM][ yyyy]{ ' - ' dd MMMM yyyy}"
			},
            editable: false,
            droppable: false,
            selectable: false,
            selectHelper: false,
            defaultView: 'agendaWeek',
            minTime: "08:00:00",
            maxTime: "24:00:00",
            allDaySlot: false,
            firstDay: 1,
            lang: 'fr',
            axisFormat: 'HH:mm',
			timeFormat: {
			    agenda: 'H:mm{ - H:mm}'
			},
            eventClick: function(event, jsEvent, view) {
            	//console.log(jsEvent)
		        //show_info(jsEvent.pageX, jsEvent.pageY, event.tooltip, calendar_id);	        
		    },  
        });
	}

	//fonction affichage tooltips
	function show_info(coor_x, coor_y, texte, calendar_id) {

		if (calendar_id=="calendar_presence") {
			marge_tooltips=230;
		}
		else {
			marge_tooltips=260;
		}
		$("#info_content").html(texte);

		menu_width=$(".main-navigation").width();
		panel_width=$(".panel-body").width();
		info_width=$("#info_calendar").width();

		var panel_body=parseInt(menu_width+panel_width+30);
		var panel_body2=parseInt(coor_x+info_width-70);
		console.log(panel_body+" / "+panel_body2);

		if (panel_body<panel_body2) {
			$("#info_calendar").css("right", 0);
			$("#info_calendar").css("left", "");
			$(".triangle-border").addClass("triangle-border2");
		}
		else {
			$("#info_calendar").css("left", coor_x-60);
			$("#info_calendar").css("right", "");
			$(".triangle-border").removeClass("triangle-border2");
		}
		
		//$("#info_calendar").css("left", coor_x-60);
		$("#info_calendar").css("top", coor_y-marge_tooltips);
		$("#info_calendar").show();
	}

	function show_div(div_to_show) {
		$("#info_calendar").hide();
		$("#tab1, #tab2, #tab3, #tab4").hide();
		$(".tab_btn").each(function() {
			$(this).removeClass('btn_actif');
		});
		$("#"+div_to_show).show();
		$("#"+div_to_show+"_btn").addClass('btn_actif');

		if (div_to_show=="tab2") {
			$('#calendar_theorique').fullCalendar('render');
			getWeek("calendar_theorique");
		}
		else if (div_to_show=="tab3") {
			$('#calendar_presence').fullCalendar('render');
			getWeek("calendar_presence");
		}
	}

	function switch_view(div, type) {
		$("#info_calendar").hide();

		$("."+div).each(function() {
			$(this).hide();
		})

		$("."+div+"_btn").each(function() {
			$(this).removeClass("active");
		})

		$("#"+div+"_"+type).toggle();
		$("#"+div+"_"+type+"_btn").addClass("active");
	}

	function getWeek(calendar_id) {
		//avoir la date de début et de fin de la semaine en cours (pour export et affichage)
		week_start          = $('#'+calendar_id).fullCalendar('getView').start;
		week_end            = $('#'+calendar_id).fullCalendar('getView').end;
		//week_end.setDate(week_end.getDate() - 1);
		week_start_export   = week_start.getFullYear()+"-"+("0"+(week_start.getMonth()+1)).slice(-2)+"-"+("0"+week_start.getDate()).slice(-2);
		week_end_export     = week_end.getFullYear()+"-"+("0"+(week_end.getMonth()+1)).slice(-2)+"-"+("0"+week_end.getDate()).slice(-2);

		//recharger la vue tableau avec la nouvelle semaine
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_planning_'+calendar_id+'&id_livreur=<?=$id?>&week_start='+week_start_export+'&week_end='+week_end_export,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
				$('#'+calendar_id+'_list').html(transport);
				setTimeout(function() {
					$("#"+calendar_id+"_periode").html($.trim($('#'+calendar_id).find(".fc-header-title").text()));
				},500)	
			}
		});	

		if (calendar_id=="calendar_presence") {
			//compter le nb d'heures de présence
			$.ajax({
                url      : 'action_poo.php',
                data	   : 'action=count_hour_presence&id_livreur=<?=$id?>&week_start='+week_start_export+'&week_end='+week_end_export,
                type	   : "GET",
                cache    : false,
                success  : function(transport) {
                    $('#heures_presence').html(transport);
                }
            });
			//compter le nb d'heures sup
			$.ajax({
				url      : 'action_poo.php',
			  	data	   : 'action=count_hour_sup&id_livreur=<?=$id?>&week_start='+week_start_export+'&week_end='+week_end_export,
			  	type	   : "GET",
			  	cache    : false,		  
			  	success  : function(transport) {  
					$('#heures_sup_txt').html(transport);
				}
			});
			$("#export_"+calendar_id).attr("href", "action_poo.php?action=export_"+calendar_id+"&id_livreur=<?=$id?>&week_start="+week_start_export+"&week_end="+week_end_export);
			setTimeout(function() {
				$(".semaine_aff").html($.trim($('#'+calendar_id).find(".fc-header-title").text().replace(" - ", "au")));
			},500)
		}
		setTimeout(function() {
			$(".fc-event").each(function() {
				$(this).height($(this).height()+7)
			})
		}, 500);
	}

	$('#heures_sup_btn').click(function(){
		var continu=true;

		$(".erreur_modal").hide();
		$(".heures_sup_error").hide();
		$("#date_hsup").removeClass("has-error2");
		$("#h_debut_hsup").removeClass("has-error2");
	    $("#h_fin_hsup").removeClass("has-error2");
		$("#s2id_commercant_hsup").find(".select2-choice").removeClass("has-error2");
		

		if ($("#commercant_hsup").val()=="") {
			$("#s2id_commercant_hsup").find(".select2-choice").addClass("has-error2");
			continu=false;
		}
		if ($("#date_hsup").val()=="") {
			$("#date_hsup").addClass("has-error2")
			continu=false;
		}

		if (continu) {
			$.ajax({
	            url      : 'action_poo.php?action=heures_sup',
	            data     : $("#heures_sup_form").serialize(),
	            type     : "POST",
	            cache    : false,         
	            success: function(transport) {  
	            	if (transport=="ok") {
	            		document.location = "livreurs_fiche2.php?id=<?=$id?>&tab_actif=tab3";
	            	}
	            	else {
	            		$(".erreur_modal"). show();
	            		$("#date_hsup").    addClass("has-error2")
	            		$("#h_debut_hsup"). addClass("has-error2");
	            		$("#h_fin_hsup").   addClass("has-error2");
	            	}
	            }
	        });
		}
        else {
        	$(".heures_sup_error").show();
        } 
    })

	$('#recurrence_btn').click(function(){
		var continu=true;

		$(".erreur_modal").hide();
		$("#date_debut_rec").   removeClass("has-error2");
		$("#date_fin_rec").     removeClass("has-error2");
		$("#h_debut_rec").      removeClass("has-error2");
	    $("#h_fin_rec").        removeClass("has-error2");
		

		if ($("#date_debut_rec").val()=="") {
			$("#date_debut_rec").addClass("has-error2")
			continu=false;
		}
		if ($("#date_fin_rec").val()=="") {
			$("#date_fin_rec").addClass("has-error2")
			continu=false;
		}
		if ($("#h_debut_rec").val()=="") {
			$("#h_debut_rec").addClass("has-error2")
			continu=false;
		}
		if ($("#h_fin_rec").val()=="") {
			$("#h_fin_rec").addClass("has-error2")
			continu=false;
		}

		if (continu) {
			$.ajax({
	            url      : 'action_poo.php?action=recurrence',
	            data     : $("#recurrence_form").serialize(),
	            type     : "POST",
	            cache    : false,         
	            success: function(transport) {  
	            	var res = $.parseJSON(transport)
                    if (res[0]=="erreur_date") {
                    	$(".erreur_modal").show();
                    	$("#date_debut_rec").addClass("has-error2")
            			$("#date_fin_rec").addClass("has-error2")
                    }
                    if (res[0]=="erreur_heure") {
                    	$(".erreur_modal").show();
                    	$("#h_debut_rec").addClass("has-error2")
            			$("#h_fin_rec").addClass("has-error2")
                    }
                    if (res[0]=="ok") {
                    	$("#date_debut_rec_txt").   val($("#date_debut_rec").val())
                    	$("#date_fin_rec_txt").     val($("#date_fin_rec").val())
                    	$("#h_debut_rec_txt").      val($("#h_debut_rec").val())
                    	$("#h_fin_rec_txt").        val($("#h_fin_rec").val())
                    	$("#jours_rec").            val(res[1]);
                    	$('#myModal').              modal('hide');
                    }
	            }
	        });
		}
    })


	$('#check_recurrence').on('ifChanged', function (event) {
	    if ($(this).prop('checked')) {
	        $('#myModal').modal('show');
	    }
	});

    jQuery(document).ready(function() {
        runSelect2();
        $("textarea.autosize").autosize();
//        if ($("#situation").val()=="actif") {
//            $("#s_actif").show();
//        }
//        else
//            $("#s_inactif").show();

    });

//    $("#type").change(function() {
//        if ($(this).val()=="Autre") {
//            $("#type_autre").show();
//        }
//        else {
//            $("#type_autre").hide();
//        }
//    })
//
//    $('#situation').change(function(){
//        if ($(this).val() == "actif") {
//            $('#etat').empty();
//            $('#etat').append($("<option />").val(1).text('Disponible'));
//            $('#etat').append($("<option />").val(2).text('Relayant'));
//            $('#etat').append($("<option />").val(3).text('En attente'))
//        } else if ($(this).val() == "inactif") {
//            $('#etat').empty();
//            $('#etat').append($("<option />").val(4).text('Repos'));
//            $('#etat').append($("<option />").val(5).text('En congé'));
//            $('#etat').append($("<option />").val(6).text('Malade'));
//        }
//    })

</script>

