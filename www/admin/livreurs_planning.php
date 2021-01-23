<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		            {$id                    = $_GET["id"];}                                             else{$id="";}
if(isset($_POST["action"]))		        {$action                = $_POST["action"];}                                        else{$action="";}
if(isset($_GET["aff_valide_livreur"]))	{$aff_valide_livreur    = $_GET["aff_valide_livreur"];}                             else{$aff_valide_livreur="";}
if(isset($_GET["aff_valide_planning"]))	{$aff_valide_planning   = $_GET["aff_valide_planning"];}                            else{$aff_valide_planning="";}
if(isset($_GET["livreur_get"]))	        {$livreur_get           = $_GET["livreur_get"];$id_livreur=$_GET["livreur_get"];}   else{$livreur_get="";$id_livreur="";}
if(isset($_GET["commercant_get"]))	    {$commercant_get        = $_GET["commercant_get"];}                                 else{$commercant_get="";}
if(isset($_GET["vehicule_get"]))	    {$vehicule_get          = $_GET["vehicule_get"];}                                   else{$vehicule_get="";}

if ($_GET["rec"]!=1) {
	$_SESSION["jour"]="";
	$_SESSION["mois"]="";
	$_SESSION["annee"]="";
}

if ($livreur_get=="" && $commercant_get=="" && $vehicule_get=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}

$menu       = "livreur";
$sous_menu  = "planning";
$aff_erreur = "";
$continu    = true;

$Vehicule   = new Vehicule($sql);
$Commercant = new Commercant($sql);
$Livreur    = new Livreur($sql);

if ($action=="enregistrer_calendar_theorique") {
	$id_livreur         = $_POST["livreur"];
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
		$datetime = new DateTime();
		$today      =$datetime->createFromFormat('d-m-Y H:i:s', date('d-m-Y')." 00:00:00");
		$date_check = $datetime->createFromFormat('d-m-Y H:i:s', $date." 00:00:00");
		$date_debut = $datetime->createFromFormat('d-m-Y H:i', $date." ".$h_debut);
		$date_fin   = $datetime->createFromFormat('d-m-Y H:i', $date." ".$h_fin);
		if ($date_debut>=$date_fin) {
			$css_hfin_obl = "has-error";
			$css_hdebut_obl = "has-error";
			$continu = false;
		}

		if ($date_check<$today) {
			$css_date_obl = "has-error";
			$continu = false;
		}
	}
	if (strpos($id_livreur, "creneau_")!== false) {
		$nb_creneau=str_replace("creneau_","",$id_livreur);
		$id_livreur=0;
	}
	else {
		$nb_creneau=0;
	}

	//echo $id_livreur." / ".$nb_creneau;

	if ($check_recurrence=="recurrence") {
		for ($i = strtotime($date_debut_rec); $i <= strtotime($date_fin_rec); $i = strtotime('+1 day', $i)) {
			$jours_rec_tab=explode(";", $jours_rec);
			foreach($jours_rec_tab as $item) {
			  	if (date('N', $i) == $item) {
		    		//echo date('Y-m-d', $i);
		    		if ($id_vehicule!="" && $Vehicule->checkVehicule($id_vehicule, $id_livreur, date('Y-m-d', $i)." ".$h_debut_rec, date('Y-m-d', $i)." ".$h_fin_rec)) {
						$css_vehicule_obl="has-error";
						$css_hfin_obl = "has-error";
						$css_hdebut_obl = "has-error";
						$msg_erreur_planning="Ce véhicule est déjà utilisé pendant cette période.";
						$continu=false;
						break;
					}
					if ($id_livreur!="" && $Livreur->checkLivreur($id_livreur, $id_vehicule, date('Y-m-d', $i)." ".$h_debut_rec, date('Y-m-d', $i)." ".$h_fin_rec, "insert")) {
						$css_livreur_obl="has-error";
						$css_hfin_obl = "has-error";
						$css_hdebut_obl = "has-error";
						$msg_erreur_planning="Ce livreur est déjà affecté pendant cette période.";
						$continu=false;
						break;
					}
		    	}
			}
		}
	}
	else {
		if ($id_vehicule!="" && $Vehicule->checkVehicule($id_vehicule, $id_livreur, date("Y-m-d", strtotime($date))." ".$h_debut, date("Y-m-d", strtotime($date))." ".$h_fin)) {
			$css_vehicule_obl="has-error";
			$css_hfin_obl = "has-error";
			$css_hdebut_obl = "has-error";
			$msg_erreur_planning="Ce véhicule est déjà utilisé pendant cette période.";
			$continu=false;
		}
		if ($id_livreur!="" && $Livreur->checkLivreur($id_livreur, $id_vehicule, date("Y-m-d", strtotime($date))." ".$h_debut, date("Y-m-d", strtotime($date))." ".$h_fin, "insert")) {
			$css_livreur_obl="has-error";
			$css_hfin_obl = "has-error";
			$css_hdebut_obl = "has-error";
			$msg_erreur_planning="Ce livreur est déjà affecté pendant cette période.";
			$continu=false;
		}
	}

	if($continu) {
		if ($check_recurrence=="recurrence") {
			if ($nb_creneau!=0) {
				for ($j=0;$j<$nb_creneau;$j++) {
					for ($i = strtotime($date_debut_rec); $i <= strtotime($date_fin_rec); $i = strtotime('+1 day', $i)) {
						$jours_rec_tab=explode(";", $jours_rec);
						foreach($jours_rec_tab as $item) {
						  	if (date('N', $i) == $item) {
					    		$Livreur->setPlanning($id_livreur, $id_commercant, $id_vehicule, date('Y-m-d', $i)." ".$h_debut_rec, date('Y-m-d', $i)." ".$h_fin_rec, "oui");
					    	}
						}
					}
				}
			}
			else {
				for ($i = strtotime($date_debut_rec); $i <= strtotime($date_fin_rec); $i = strtotime('+1 day', $i)) {
					$jours_rec_tab=explode(";", $jours_rec);
					foreach($jours_rec_tab as $item) {
					  	if (date('N', $i) == $item) {
				    		$Livreur->setPlanning($id_livreur, $id_commercant, $id_vehicule, date('Y-m-d', $i)." ".$h_debut_rec, date('Y-m-d', $i)." ".$h_fin_rec, "oui");
				    	}
					}
				}
			}
			$_SESSION["jour"]=intVal(date("d", strtotime($date_debut_rec)));
			$_SESSION["mois"]=intVal(date("m", strtotime($date_debut_rec))-1);
			$_SESSION["annee"]=intVal(date("Y", strtotime($date_debut_rec)));

			header("location:livreurs_planning.php?aff_valide_planning=1&rec=1");	
		}
		else {
			if ($nb_creneau!=0) {
				for ($j=0;$j<$nb_creneau;$j++) {
					$Livreur->setPlanning($id_livreur, $id_commercant, $id_vehicule, date("Y-m-d", strtotime($date))." ".$h_debut, date("Y-m-d", strtotime($date))." ".$h_fin, "non");
				}
			}
			else {
				$Livreur->setPlanning($id_livreur, $id_commercant, $id_vehicule, date("Y-m-d", strtotime($date))." ".$h_debut, date("Y-m-d", strtotime($date))." ".$h_fin, "non");
			}
			$_SESSION["jour"]="";
			$_SESSION["mois"]="";
			$_SESSION["annee"]="";
			header("location:livreurs_planning.php?aff_valide_planning=1");	
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

	/*.triangle-border:before {
	  content:"";
	  position:absolute;
	  top:16px; 
	  bottom:auto;
	  left:-22px; 
	  border-width:10px 22px 10px 0;
	  border-color:transparent #000;
	  border-style:solid;
	  display:block;
	  width:0;
	}*/

	/* creates the smaller  triangle */
	/*.triangle-border:after {
	  content:"";
	  position:absolute;
	  top:17px;
	  bottom:auto;
	  left:-21px; 
	  border-width:9px 21px 9px 0;
	  border-color:transparent #fff;
	  border-style:solid;
	  display:block;
	  width:0;
	}*/

	#tooltip_table th, #tooltip_table td {
		padding:5px 10px;
	}

	.has-error2 {
		border: 1px solid #a94442 !important;
	}
        
    .form-horizontal2 .form-group{
        margin-left: 0;
        margin-right: 0;
    }
    
    .form-group p{
        margin-top:10px;
    }
    
    #liste_livreurs .table.table-bordered.table-hover{margin-top: 0 !important;}
    
    #form_calendar_theorique .form-group{margin-bottom:5px;}
    
    #form_calendar_theorique .form-group p{margin: 5px 0 0 0;}
    .select2-container .select2-choice .select2-arrow b{background: none !important;}
    
    .tooltips {
    	z-index:9999999;
    }

	.tooltip-inner {
		white-space: pre-wrap;
		max-width: 500px;
		width:220px;
		z-index:9999999;
	}

    /*.tooltip-inner {
	    white-space: pre-wrap;
	    background-color: #fff;
	    color:#000;
	    border:1px solid #000;
	    min-width:145px;
	}

	.tooltip.tooltip-top .tooltip-arrow {
		border-top-color: #fff;
	}

	.tooltip-arrow {
		border-color:#fff transparent;
	}*/
</style>

<!-- start: PAGE -->
<link rel="stylesheet" type="text/css" href="assets/css/magnific-popup.css">
<div style="display:none;">
    <a class="pop-up-generique" href=""></a>
</div>
<div class="main-content">
    <div class="container">

        <!-- content -->
    	<?php
		if($aff_erreur_planning=="1") {
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

        <div class="alert alert-success" style="<?php if($aff_valide_planning!="1") echo 'display:none';?>">
            <button class="close" data-dismiss="alert">
                ×
            </button>
            <i class="fa fa-check-circle"></i>
            Les modifications ont été enregistrées.
        </div>
        
        <?php if ($_SESSION["restaurateur"]) { ?>
        <!-- start: PAGE HEADER -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header">
                        <h1>Demande de disponibilit&eacute; livreur</h1>
                    </div>
                </div>
            </div>
        <!-- end: PAGE HEADER -->
        <?php } ?>

        <div class="row">
            <div class="col-sm-6 col-sm-offset-3" style="margin-top: 10px;">
				<div class="panel panel-default" style="margin-bottom: 10px;">
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
		                    <form class="form-horizontal form-horizontal2" role="form" action="livreurs_planning.php" method="get">
		                    	<div class="form-group">
									<label class="col-sm-3 control-label" for="form-field-1">
		                                Livreur
		                            </label>
		                            <div class="col-sm-9">
		                            	<select name="livreur_get" id="livreur_get" class="form-control search-select">
				                            <option value="">&nbsp;</option>
										    <?php 
												foreach ($Livreur->getAll("", "") as $livreur) {
													$sel=($livreur_get==$livreur->id) ? "selected" : "";
													echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom. " ".$livreur->nom."</option>";
												}
										    ?>
				                        </select>
		                            </div>
								</div>
		                    	<div class="form-group">
									<label class="col-sm-3 control-label" for="commercant">
		                                Commerçant
		                            </label>
		                            <div class="col-sm-9">
		                            	<select name="commercant_get" id="commercant_get" class="form-control search-select">
										    <option value="">&nbsp;</option>
										    <?php 
												foreach ($Commercant->getAll("", "") as $commercant) {
													$sel=($commercant_get==$commercant->id) ? "selected" : "";
													echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom."</option>";
												}
										    ?>
										</select>
		                            </div>
								</div>
								<?php if (!$_SESSION["restaurateur"]) { ?>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="commercant">
			                                Véhicule
			                            </label>
			                            <div class="col-sm-9">
			                            	<select name="vehicule_get" id="vehicule_get" class="form-control search-select">
											    <option value="">&nbsp;</option>
											    <?php 
													foreach ($Vehicule->getAll("", "", "", "", true) as $vehicule) {
														$sel=($vehicule_get==$vehicule->id) ? "selected" : "";
														echo "<option value='".$vehicule->id."' ".$sel.">".$vehicule->immatriculation."</option>";
													}
											    ?>
											</select>
			                            </div>
									</div>
								<?php } ?>
		                        <div style="text-align:center;">
		                        	<input type="submit" id="bt" class="btn btn-main" value="Rechercher">
		                        </div>
							</form>
						</div>
					</div>
				</div>                        
            </div>
			<div class="col-sm-3"></div>
        </div>
    	<div class="row">
    		<?php if (!$_SESSION["restaurateur"]) { ?>
	    		<div class="col-sm-12">
	    			<div style="border:1px solid #eee;padding:0 10px;margin-bottom:10px">
	                    <form role="form" name="form_calendar_theorique" id="form_calendar_theorique" method="post" action="livreurs_planning.php" class="form-horizontal">
			            	<input type="hidden" name="action" value="enregistrer_calendar_theorique"/>
			            	<input type="hidden" name="h_debut_txt" id="h_debut_txt" value="<?=$h_debut?>"/>
			            	<input type="hidden" name="h_fin_txt" id="h_fin_txt" value="<?=$h_fin?>"/>
			            	<input type="hidden" name="date_debut_rec_txt" id="date_debut_rec_txt" value="<?=$date_debut_rec?>"/>
			            	<input type="hidden" name="date_fin_rec_txt" id="date_fin_rec_txt" value="<?=$date_fin_rec?>"/>
			            	<input type="hidden" name="h_debut_rec_txt" id="h_debut_rec_txt" value="<?=$h_debut_rec?>"/>
			            	<input type="hidden" name="h_fin_rec_txt" id="h_fin_rec_txt" value="<?=$h_fin_rec?>"/>
			            	<input type="hidden" name="jours_rec" id="jours_rec" value="<?=$jours_rec?>"/>

			            	<div class="form-group">
			                    <div class="col-sm-4">
			                    	<p><b>Livreur</b></p>
			                    	<select name="livreur" id="livreur" class="form-control search-select">
			                            <option value="">&nbsp;</option>
			                            <optgroup label="Nombre de créneaux">
			                            	<?php for($i=1;$i<=10;$i++) {
			                            		$creneau_txt=($i==1) ? "créneau" : "créneaux";
			                            		$sel1=($id_livreur=='creneau_'.$i) ? "selected" : "";
			                            		echo '<option value="creneau_'.$i.'" '.$sel1.'>'.$i.' '.$creneau_txt.'</option>';
			                            	} ?>
										</optgroup>
										<?php if ($_SESSION["admin"]) { ?>
											<optgroup label="Livreurs">
											    <?php 
													foreach ($Livreur->getAll("", "", "", "", "") as $livreur) {
														$sel=($id_livreur==$livreur->id) ? "selected" : "";
														echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom. " ".$livreur->nom."</option>";
													}
											    ?>
											</optgroup>
										<?php } ?>
			                        </select>
			                    </div>
			                    <div class="col-sm-4 <?php echo $css_commercant_obl; ?>">
			                    	<p><b>Commerçant</b></p>
			                    	<select name="commercant" id="commercant" class="form-control search-select">
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
			                        <p style="margin-bottom:4px;"><b>Heure de début</b></p>
			                        <div class="input-group input-append bootstrap-timepicker">
                                                    <input type="text" id="h_debut" name="h_debut" class="form-control timepicker">
                                                    <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
                                                </div>
			                    </div>
			                    <div class="col-sm-2 <?php echo $css_hfin_obl; ?>">
			                        <p style="margin-bottom:4px;"><b>Heure de fin</b></p>
			                    	<div class="input-group input-append bootstrap-timepicker">
                                                    <input type="text" id="h_fin" name="h_fin" class="form-control timepicker2">
                                                    <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
                                                </div>
			                    </div>
			                </div>

			                <div class="form-group" style="margin-bottom:15px !important;">
			                	<div class="col-sm-4 <?php echo $css_vehicule_obl; ?>">
			                    	<p><b>Véhicule</b></p>
			                    	<select name="vehicule" id="vehicule" class="form-control search-select">
									    <option value="">&nbsp;</option>
									    <?php 
											foreach ($Vehicule->getAll("", "", "", "", true) as $vehicule) {
												$sel=($id_vehicule==$vehicule->id) ? "selected" : "";
												echo "<option value='".$vehicule->id."' ".$sel.">".$vehicule->immatriculation."</option>";
											}
									    ?>
									</select>
			                    </div>
			                    <div class="col-sm-4 <?php echo $css_date_obl; ?>">
			                    	<p  style="margin-bottom:4px;"><b>Date</b></p>
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
	                                        <input style="margin-top:15px; float:right;" type="submit" id="bt" class="btn btn-main" value="Enregistrer" style="width:150px;">
	                                    </div>
			                </div>
			
			            </form>
			        </div>
	    		</div>
    		<?php } else { ?>
    			<div class="col-sm-12">
                            <div style="border:1px solid #eee;padding:0 10px;margin-bottom:10px">
                                <form role="form" name="form_calendar_theorique" id="form_calendar_theorique" method="post" action="livreurs_planning.php" class="form-horizontal">
			            	<input type="hidden" name="action" value="enregistrer_calendar_theorique"/>
			            	<input type="hidden" name="h_debut_txt" id="h_debut_txt" value="<?=$h_debut?>"/>
			            	<input type="hidden" name="h_fin_txt" id="h_fin_txt" value="<?=$h_fin?>"/>
			            	<input type="hidden" name="date_debut_rec_txt" id="date_debut_rec_txt" value="<?=$date_debut_rec?>"/>
			            	<input type="hidden" name="date_fin_rec_txt" id="date_fin_rec_txt" value="<?=$date_fin_rec?>"/>
			            	<input type="hidden" name="h_debut_rec_txt" id="h_debut_rec_txt" value="<?=$h_debut_rec?>"/>
			            	<input type="hidden" name="h_fin_rec_txt" id="h_fin_rec_txt" value="<?=$h_fin_rec?>"/>
			            	<input type="hidden" name="jours_rec" id="jours_rec" value="<?=$jours_rec?>"/>

			            	<div class="form-group">
                                            <div class="col-sm-3 <?php echo $css_commercant_obl; ?>">
			                    	<p><b>Commerçant</b></p>
			                    	<select name="commercant" id="commercant" class="form-control search-select">
                                                    <?php 
                                                        foreach ($Commercant->getAll("", "") as $commercant) {
                                                            $sel=($id_commercant==$commercant->id) ? "selected" : "";
                                                            echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom."</option>";
                                                        }
                                                    ?>
                                                </select>
			                    </div>
			                    <div class="col-sm-3">
			                    	<p><b>Nb de Créneaux</b></p>
			                    	<select name="livreur" id="livreur" class="form-control search-select">
			                            <option value="">&nbsp;</option>
			                            <optgroup label="Nombre de créneaux">
			                            	<?php for($i=1;$i<=10;$i++) {
                                                            $creneau_txt=($i==1) ? "créneau" : "créneaux";
                                                            $sel1=($id_livreur=='creneau_'.$i) ? "selected" : "";
                                                            echo '<option value="creneau_'.$i.'" '.$sel1.'>'.$i.' '.$creneau_txt.'</option>';
			                            	} ?>
                                                    </optgroup>
                                                    <?php if ($_SESSION["admin"]) { ?>
                                                        <optgroup label="Livreurs">
                                                            <?php 
                                                                foreach ($Livreur->getAll("", "", "", "", "") as $livreur) {
                                                                    $sel=($id_livreur==$livreur->id) ? "selected" : "";
                                                                    echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom. " ".$livreur->nom."</option>";
                                                                }
                                                            ?>
                                                        </optgroup>
                                                    <?php } ?>
			                        </select>
			                    </div>
                                            <div class="col-sm-2 <?php echo $css_date_obl; ?>">
			                    	<p  style="margin-bottom:4px;"><b>Date</b></p>
			                    	<div class="input-group">
                                                    <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                                    <input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date" name="date" value="<?=$date?>">
                                                </div>
			                    </div>
			                    <div class="col-sm-2 <?php echo $css_hdebut_obl; ?>">
			                        <p style="margin-bottom:4px;"><b>Heure de début</b></p>
			                        <div class="input-group input-append bootstrap-timepicker">
                                                    <input type="text" id="h_debut" name="h_debut" class="form-control timepicker">
                                                    <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
                                                </div>
			                    </div>
			                    <div class="col-sm-2 <?php echo $css_hfin_obl; ?>">
			                        <p style="margin-bottom:4px;"><b>Heure de fin</b></p>
			                    	<div class="input-group input-append bootstrap-timepicker">
                                                    <input type="text" id="h_fin" name="h_fin" class="form-control timepicker2">
                                                    <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
                                                </div>
			                    </div>
			                </div>

			                <div class="form-group" style="margin-bottom:15px !important;">
			                    <div class="col-sm-2 col-sm-offset-9">
			                    	<label class="checkbox-inline" style="margin-top: 25px !important;">
                                                    <input type="checkbox" value="recurrence" class="grey" name="check_recurrence" id="check_recurrence" <?php if ($check_recurrence=="recurrence") echo "checked"; ?>>
                                                    <b>Récurrence</b>
                                                </label>
			                    </div>
                                            <div class="col-sm-1">
                                                <input style="margin-top:20px; float:right;" type="submit" id="bt" class="btn btn-main" value="Enregistrer" style="width:150px;">
                                            </div>
			                </div>
			            </form>
			        </div>
	    		</div>
    		<?php } ?>
    	</div>
    		
    	<div class="row">
			<div class="col-sm-12">
                <div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-calendar"></i>Planning</div>
					<div class="panel-body">  
						<div class="btn-group btn-group-sm" style="position:absolute;right:30px">
				    		<a class="btn btn-default active calendar_theorique_btn fc-button-agendaWeek"   id="calendar_theorique_calendar_btn"        href="javascript:void(0);" onclick="switch_view('calendar_theorique', 'calendar', 'Week')"> <i class="fa fa-calendar"></i></a>
				    		<a class="btn btn-default calendar_theorique_btn fc-button-agendaDay"           id="calendar_theorique_day_calendar_btn"    href="javascript:void(0);" onclick="switch_view('calendar_theorique', 'calendar', 'Day')">  <i class="fa fa-calendar-o"></i></a>
				    		<a class="btn btn-default calendar_theorique_btn"                               id="calendar_theorique_list_btn"            href="javascript:void(0);" onclick="switch_view('calendar_theorique', 'list', '')">         <i class="fa fa-align-justify"></i></a>
				    	</div>
				    	<div class="calendar_theorique" id="calendar_theorique_calendar">
							<div id='calendar_theorique'></div>
						</div>
						<div class="calendar_theorique table-responsive" id="calendar_theorique_list" style="display:none">
							
						</div>
                                        
						<div class="col-lg-2 col-sm-offset-8" style="text-align:right;padding:0px">
                            <a class="btn btn-light-grey" href="#myModal2" role="button" data-toggle="modal" style="margin-top:20px">Dupliquer le planning</a>
                        </div>

                        <?php if (!$_SESSION["restaurateur"]) { ?> 
	                        <div class="col-lg-2" style="text-align:right;margin-top:20px;">
	                            <a class="btn btn-light-grey" style="margin-top:0;" id="export_planning_livreur_btn" target="_blank" href="action_poo.php?action=export_planning_livreur">Exporter en CSV</a>
	                        </div>
	                    <?php } ?>
					</div>
				</div>
			</div>
		</div>

		<?php if (!$_SESSION["restaurateur"]) { ?>
			<div class="row">
				<div class="col-sm-12">
	                <div class="panel panel-default">
						<div class="panel-heading"><i class="fa fa-list"></i>Liste des livreurs</div>
						<div class="panel-body">
							<div class="col-sm-4" style="padding:0">
								<span class="nb_total">Total : <span id="liste_livreurs_nb"></span></span>
								<span class="semaine_aff" style="line-height:40px;padding-left:15px"></span>
							</div>
		                	<div class="col-sm-8" style="text-align:right;padding:0px">
				        		<a class="btn btn-light-grey" id="export_liste_livreur_btn" target="_blank" href="action_poo.php?action=export_liste_livreurs_planning">Heures affectées</a>
				        		<a class="btn btn-light-grey" id="export_liste_livreur_effectues_btn" target="_blank" href="action_poo.php?action=export_liste_livreurs_planning_effectues">Heures effectuées</a>
				        	</div>
							<div id="liste_livreurs" class="table-responsive" style='margin-top: 0 !important;'>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

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

		<div class="modal fade" id="myModal2" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Dupliquer le planning</h3>

						<div class="alert alert-danger erreur_modal" style="display:none">
			                <button class="close" data-dismiss="alert">
			                    ×
			                </button>
			                <i class="fa fa-check-circle"></i>
			                Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.
			            </div>      
						
						<div class="row" style="margin-top:40px;padding:0px 20px;">
							<form class="form-horizontal" id="dupliquer_form" role="form" action="livreurs_fiche2.php" method="get">
			                	<input type="hidden" name="date_debut" id="date_debut" value=""/>
			                	<input type="hidden" name="date_fin" id="date_fin" value=""/>

								<div class="form-group">
				                    <div class="col-sm-6">
				                        <p><b>Date de début</b></p>
				                    	<div class="input-group">
				                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
											<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker2" data-date-days-of-week-disabled="0,2,3,4,5,6" id="date_debut_dupliquer" name="date_debut_dupliquer" value="">
										</div>
				                    </div>
				                    <div class="col-sm-6">
				                        <p><b>Date de fin</b></p>
				                    	<div class="input-group">
				                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
											<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker2" id="date_fin_dupliquer" name="date_fin_dupliquer" value="" disabled>
										</div>
				                    </div>
				                </div>

				                <div class="form-group">
				                    <div class="col-sm-12">
				                        <p><b>Livreur</b></p>
				                    	<select name="livreur_dupliquer" id="livreur_dupliquer" class="form-control search-select">
				                            <option value="">&nbsp;</option>
										    <?php 
												foreach ($Livreur->getAll("", "") as $livreur) {
													echo "<option value='".$livreur->id."'>".$livreur->prenom. " ".$livreur->nom."</option>";
												}
										    ?>
				                        </select>
				                    </div>
				                </div>				                
							</form>
						</div>

						<div style="text-align:center;margin-top:20px;">
							<button onclick="" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
								Annuler
							</button>
							<button id="dupliquer_btn" class="btn btn-default">
								OK
							</button>
						</div>
					</div>
				</div>
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
		$("body").addClass("navigation-small");
		runSelect2();
		runCalendar("calendar_theorique");
		getWeek("calendar_theorique");
		//remplir les heures si elles existent, sinon en mettre par défaut
		var d1 = new Date ();
		var coeff = 1000 * 60 * 30;
		var rounded = new Date(Math.ceil(d1.getTime() / coeff) * coeff)
		var heure1=rounded.getHours();
		var heure2=rounded.getHours()+1;
		var minute=rounded.getMinutes();

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
        $('.date-picker2').datepicker({
            autoclose: true,
            weekStart: 1
        });

        $('input.timepicker').timepicker({
        	showMeridian: false,
        	minuteStep:30,
        	defaultTime: heure_deb

    	});
        $('input.timepicker2').timepicker({
        	showMeridian: false,
        	minuteStep:30,
        	defaultTime: heure_fin
    	});

    	$("#h_debut, #h_debut_rec, #h_fin, #h_fin_rec").on("focus", function() {
            return $(this).timepicker("showWidget");
        });

        $('#h_debut').timepicker().on('changeTime.timepicker', function(e) {
			//on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
			//TO DO : changer la date si l'heure passe a 1h du jour suivant ?
			var d = new Date("1970-01-01 "+e.time.value+":00");
			d.setHours(d.getHours() + 1);

			$('#h_fin').timepicker('setTime', d.getHours()+":"+d.getMinutes());

	    });

	    $('#h_debut_rec').timepicker().on('changeTime.timepicker', function(e) {
			//on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
			//TO DO : changer la date si l'heure passe a 1h du jour suivant ?
			var d = new Date("1970-01-01 "+e.time.value+":00");
			d.setHours(d.getHours() + 1);

			$('#h_fin_rec').timepicker('setTime', d.getHours()+":"+d.getMinutes());

	    });

        $('.date-time-range').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                firstDay: 1,
                format: 'DD-MM-YYYY hh:mm A'
        });
			
		//avoir la date de début et de fin de la semaine en cours (pour export et affichage)
		$("#calendar_theorique").find('.fc-button-prev, .fc-button-next').click(function(){
			getWeek("calendar_theorique");
		});

		//récupérer la date de fin de la semaine
		$('.date-picker2').on("changeDate", function(e) {
		    console.log(e.date);
		    var date = e.date;
		    startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
		    endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay()+7);
		    console.log(startDate)
		    $("#date_fin_dupliquer").val(("0"+endDate.getDate()).slice(-2)+'-'+("0"+(endDate.getMonth()+1)).slice(-2)+'-'+endDate.getFullYear());
		    console.log( $("#date_fin_dupliquer").val());
		});

		<?php if ($css_commercant_obl=="has-error") { ?>
			$("#s2id_commercant").find(".select2-choice").addClass("has-error2");
		<?php } ?>
	});	

	$(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });

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
            events: 'feed_livreurs.php?id=<?=$livreur_get?>&id_commercant=<?=$commercant_get?>&id_vehicule=<?=$vehicule_get?>&action='+calendar_id,
            eventRender: function (event, element) {
			    element.find('.fc-event-title').html(event.title);
			    //element.addClass("tooltips");
			    element.attr("data-placement", "top");
			    element.attr("data-original-title", event.tooltip)
			},
			eventMouseover: function(calEvent, jsEvent) {
			    $(this).tooltip('show');
			    //$(".tooltip-inner").css("width", "223px")
			    //$(".tooltip-inner").css("max-width", $(".fc-col0").width()+"px");
			    //$(".tooltip-inner").css("width", $(".fc-col0").width()+"px");
			},
			eventMouseout: function(calEvent, jsEvent) {
			    $(this).tooltip('hide');
			},
            columnFormat: {
                agendaWeek: 'ddd dd/MM',
                agendaDay: 'ddd dd/MM'
            },
            titleFormat: {
			   week: "dd [MMMM][ yyyy]{ ' - ' dd MMMM yyyy}",
			   day: "dd MMMM yyyy"
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
            firstHour : 8,
            lang: 'fr',
            axisFormat: 'HH:mm',
			timeFormat: {
			    agenda: 'H:mm{ - H:mm}'
			},
            eventClick: function(event, jsEvent, view) {
		        if (("<?=$_SESSION['admin']?>"==true) || (event.className[0]=="label-orange" && "<?=$_SESSION['role']?>"=="restaurateur")) openPopup('popup_planning.php?id='+event.id_planning);
		    },  
        });
		
		//setTimeout(function() {
			<?php if ($_GET["rec"]==1) { ?>
				$('#'+calendar_id).fullCalendar('gotoDate', '<?=$_SESSION["annee"]?>','<?=$_SESSION["mois"]?>', '<?=$_SESSION["jour"]?>');
			<?php } ?>
		//}, 500)	
	}

	function switch_view(div, type, format) {
		console.log(div+" / "+type+" / "+format)
		$("#info_calendar").hide();

		$("."+div).each(function() {
			$(this).hide();
		})

		$("."+div+"_btn").each(function() {
			$(this).removeClass("active");
		})

		if (format!="") {
			$('#'+div).fullCalendar('changeView', 'agenda'+format);
			$("#"+div+"_"+format.toLowerCase()+"_"+type+"_btn").addClass("active");
		}
		else {
			$("#"+div+"_"+type+"_btn").addClass("active");
		}

		$("#"+div+"_"+type).toggle();
		//$("#"+div+"_"+type+"_btn").addClass("active");

		// /!\ recharger le calendrier avec la nouvelle vue
		$('#'+div).fullCalendar('render');
		//$('#'+div).fullCalendar('today');

		getWeek(div);
	}

	function getWeek(calendar_id) {
		//avoir la date de début et de fin de la semaine en cours (pour export et affichage)
		console.log($('#'+calendar_id).fullCalendar('getView').visStart+" / "+$('#'+calendar_id).fullCalendar('getView').visEnd)
		week_start=$('#'+calendar_id).fullCalendar('getView').visStart;
		week_end=$('#'+calendar_id).fullCalendar('getView').visEnd;
		//week_end2.setDate(week_end2.getDate() - 1);
		week_start_export=week_start.getFullYear()+"-"+("0"+(week_start.getMonth()+1)).slice(-2)+"-"+("0"+week_start.getDate()).slice(-2);
		week_end_export=week_end.getFullYear()+"-"+("0"+(week_end.getMonth()+1)).slice(-2)+"-"+("0"+(week_end.getDate())).slice(-2);
		//week_end_export.setDate(week_end.getDate() - 1);
		console.log(week_start_export+" / "+week_end_export)

		$("#export_planning_livreur_btn").attr("href", "action_poo.php?action=export_planning_livreur&id_livreur=<?=$livreur_get?>&id_commercant=<?=$commercant_get?>&id_vehicule=<?=$vehicule_get?>&week_start="+week_start_export+"&week_end="+week_end_export);
		$("#date_debut").val(("0"+week_start.getDate()).slice(-2)+"-"+("0"+(week_start.getMonth()+1)).slice(-2)+"-"+week_start.getFullYear());
		$("#date_fin").val(("0"+week_end.getDate()).slice(-2)+"-"+("0"+(week_end.getMonth()+1)).slice(-2)+"-"+week_end.getFullYear());
		setTimeout(function() {
			$(".semaine_aff").html($.trim($('#'+calendar_id).find(".fc-header-title").text()));
			$("#export_liste_livreur_btn").attr("href", "action_poo.php?action=export_liste_livreurs_planning&id_livreur=<?=$livreur_get?>&id_commercant=<?=$commercant_get?>&id_vehicule=<?=$vehicule_get?>&week_start="+week_start_export+"&week_end="+week_end_export+"&semaine="+$(".semaine_aff").html());
			$("#export_liste_livreur_effectues_btn").attr("href", "action_poo.php?action=export_liste_livreurs_planning_effectues&week_start="+week_start_export+"&week_end="+week_end_export);
		},500)

		//recharger la vue tableau avec la nouvelle semaine 
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_planning_'+calendar_id+'&id_livreur=<?=$livreur_get?>&id_commercant=<?=$commercant_get?>&id_vehicule=<?=$vehicule_get?>&week_start='+week_start_export+'&week_end='+week_end_export,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
				$('#'+calendar_id+'_list').html(transport);
				setTimeout(function() {
					$("#"+calendar_id+"_periode").html($.trim($('#'+calendar_id).find(".fc-header-title").text()));
				},500)
			}
		});	

		//charger le tableau avec la liste des livreurs
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_livreurs_heures&id_livreur=<?=$livreur_get?>&id_commercant=<?=$commercant_get?>&id_vehicule=<?=$vehicule_get?>&week_start='+week_start_export+'&week_end='+week_end_export,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
				$('#liste_livreurs').html(transport);
			}
		});

		//charger le nb de livreurs
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_livreurs_nb&id_livreur=<?=$livreur_get?>&id_commercant=<?=$commercant_get?>&id_vehicule=<?=$vehicule_get?>&week_start='+week_start_export+'&week_end='+week_end_export,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
		  		if (transport>1) {
		  			$('#liste_livreurs_nb').html(transport+" livreurs");
		  		}
		  		else {
		  			$('#liste_livreurs_nb').html(transport+" livreur");
		  		}
				
			}
		});

		setTimeout(function() {
			$(".fc-event").each(function() {
				$(this).height($(this).height()+7)
			})
		}, 500);
	}

	$('#recurrence_btn').click(function(){
		var continu=true;

		$(".erreur_modal").hide();
		$("#date_debut_rec").removeClass("has-error2");
		$("#date_fin_rec").removeClass("has-error2");
		$("#h_debut_rec").removeClass("has-error2");
	    $("#h_fin_rec").removeClass("has-error2");
		

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
                    	$("#date_debut_rec_txt").val($("#date_debut_rec").val())
                    	$("#date_fin_rec_txt").val($("#date_fin_rec").val())
                    	$("#h_debut_rec_txt").val($("#h_debut_rec").val())
                    	$("#h_fin_rec_txt").val($("#h_fin_rec").val())
                    	$("#jours_rec").val(res[1]);
                    	$('#myModal').modal('hide');
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

	$('#dupliquer_btn').click(function(){
		var continu=true;

		$(".erreur_modal").hide();
		$("#date_debut_dupliquer").removeClass("has-error2");
		$("#date_fin_dupliquer").removeClass("has-error2");
		$("#s2id_livreur_dupliquer").find("a").removeClass("has-error2");
		

		if ($("#date_debut_dupliquer").val()=="" || $("#date_debut").val()=="") {
			$("#date_debut_dupliquer").addClass("has-error2")
			continu=false;
		}
		if ($("#date_fin_dupliquer").val()=="" || $("#date_fin").val()=="") {
			$("#date_fin_dupliquer").addClass("has-error2")
			continu=false;
		}

		if ($("#livreur_dupliquer").val()=="" || $("#livreur_dupliquer").val()==0) {
			$("#s2id_livreur_dupliquer").find("a").addClass("has-error2")
			continu=false;
		}

		if (continu) {
			$.ajax({
	            url      : 'action_poo.php?action=dupliquer',
	            data     : $("#dupliquer_form").serialize(),
	            type     : "POST",
	            cache    : false,         
	            success: function(transport) {  
	            	if (transport=="ok") {
	            		$('#myModal2').modal('hide');
	            		$(".alert-success").show();
	            		window.scrollTo(0, 0);
	            	}
	            	else if (transport=="ko") {
	            		$(".erreur_modal").show();
	            	}
	            }
	        });
		}
    })
</script>

