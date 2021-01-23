<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}

$Livreur=new Livreur($sql);
$Livreur->getPagination(30, "", "", "");
$nb_total=$Livreur->getNbRes();
$Livreur->getPagination(30, "", "ON", "");
$nb_connecte=$Livreur->getNbRes();

$menu = "notif";
if($id==""){
	$sous_menu = "fiche";
	$titre_page = "Créer une notification";
}else{
	$sous_menu = "liste";
	$titre_page = "Modifier une notification";

	$result = $sql->query("SELECT * FROM notifications_push WHERE id=".$id);
    $liste_notif = $result->fetchAll(PDO::FETCH_OBJ);

    $nom=$liste_notif[0]->nom;
    $message=$liste_notif[0]->message;
    $date_envoi=date("d-m-Y", strtotime($liste_notif[0]->date_envoi));
    $heure_envoi=date("H:i", strtotime($liste_notif[0]->date_envoi));
    $destinataire=$liste_notif[0]->destinataire;
    $type_envoi=$liste_notif[0]->type_envoi;
}
$aff_erreur = "";

$continu = true;

if($action=="enregistrer") {
	$nom=$_POST["nom"];
	$date_envoi=$_POST["date_envoi"];
	$heure_envoi=$_POST["heure_envoi"];
	$message=$_POST["message"];
	$type_envoi=$_POST["type_envoi"];

	if($nom==""){
		$css_nom_obl = "has-error";
		$continu = false;
	}
	if ($date_envoi=="") {
		$css_date_obl = "has-error";
		$continu = false;
	}
	if($heure_envoi=="") {
		$css_heure_obl = "has-error";
		$continu = false;
	}

	if ($continu) {
		$destinataire="";
		$date_envoi_bdd = date("Y-m-d H:i:s",strtotime($date_envoi." ".$heure_envoi.':00'));

		if ($type_envoi=="tous") {
			$destinataire="tous";
		}
		else if ($type_envoi=="en_service") {
			foreach($_POST["livreur_service"] as $livreur_service) {
				$destinataire.=$livreur_service.",";
			}
		}
		else {
			foreach($_POST["livreur_unite"] as $livreur_unite) {
				$destinataire.=$livreur_unite.",";
			}
		}

		$result = $sql->exec("INSERT INTO notifications_push (nom, message, date_envoi, type_envoi, destinataire, date_creation, statut) VALUES (".$sql->quote($nom).",".$sql->quote($message).",".$sql->quote($date_envoi_bdd).", ".$sql->quote($type_envoi).", ".$sql->quote($destinataire).",NOW(), 'pending')");      
        $id=$sql->lastInsertId();
		header("location: notification_fiche.php?id=".$id);
	}

}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">

<style>
	.radio-inline, .radio-inline + .radio-inline, .checkbox-inline, .checkbox-inline + .checkbox-inline {
		margin-right: 0px !important;
		margin-top: 5px !important;
		margin-left: 0 !important;
		margin-bottom: 10px !important;
	}

	.div_notif_livreur {
		padding:15px;
		border:1px solid #ddd;
		background-color:#fff;
	}

	.div_notif_livreur:before {
	  content:"";
	  position:absolute;
	  top:-11px; /* value = - border-top-width - border-bottom-width */
	  left:40%; /* controls horizontal position */
	  border-width:0 10px 10px;
	  border-style:solid;
	  border-color:#d7d7d7 transparent;
	  /* reduce the damage in FF3.0 */
	  display:block;
	  width:0;
	  z-index:500;
	}

	/* creates the smaller  triangle */
	.div_notif_livreur:after {
	  content:"";
	  position:absolute;
	  top:-10px; /* value = - border-top-width - border-bottom-width */
	  left:40%; /* value = (:before left) + (:before border-left) - (:after border-left) */
	  border-width:0 10px 10px;
	  border-style:solid;
	  border-color:#fff transparent;
	  /* reduce the damage in FF3.0 */
	  display:block;
	  width:0;
	  z-index:500;
	}

	#div_livreur_unitaire > .div_notif_livreur:before, #div_livreur_unitaire > .div_notif_livreur:last-child:after {
	  left:70%; /* controls horizontal position */
	}
        
        @media(max-width:767px){
            .input-spe{margin-right: 0 !important;}
        }
</style>

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1><?php echo $titre_page; ?></h1>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
		<?php
		if($aff_erreur=="1"){
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

		if($aff_valide=="1"){
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
				<form role="form" name="form" id="form1" method="post" action="notification_fiche.php?id=<?=$id;?>" class="form-horizontal">
	            	<input type="hidden" name="action" value="enregistrer">
	            	<div class="form-group <?php echo $css_nom_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-1">
	                        Nom <span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="nom" placeholder="Nom de la notification" class="form-control" value="<?=$nom?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_date_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-3">
	                        Date d'envoi <span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4">
	                        <div class="input-group col-sm-12 input-spe" style="margin-right:10px;float:left;">
	                            <input type="text" name="date_envoi" data-date-format="dd-mm-yyyy" value="<?php echo $date_envoi?>" data-date-viewmode="years" class="form-control date-picker">
	                            <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
	                        </div>    
                   
	                        <div style="float:left;margin-right:10px;line-height:30px">Heure </div>                            
	                        <div class="input-group input-append bootstrap-timepicker col-sm-12" style="margin-right:10px;float:left;">
	                            <input type="text" name="heure_envoi" class="form-control time-picker" value="<?php echo $heure_envoi ?>">
	                            <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
	                        </div>                                                                     
	                    </div>
	                </div>
	            	<div class="form-group <?php echo $css_message_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-1">
	                        Message
	                    </label>
	                    <div class="col-sm-4">
	                        <textarea class="autosize form-control" id="message" name="message" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;" placeholder="Message"><?php echo $message; ?></textarea>
	                    </div>
	                </div>
	                <div class="form-group">
                        <label class="col-sm-4 control-label" for="form-field-select-1">
                            Livreurs
                        </label>
                        <div class="col-sm-4 margin_label">
							<label class="col-xs-12 checkbox-inline">
                                <input type="radio" class="green radio_box" value="tous" name="type_envoi" <?php if($type_envoi=="tous") echo "checked";?>/>
                                Tous (<?=$nb_total?>)
                            </label>
                            <!-- <label class="col-xs-12 checkbox-inline">
                                <input type="radio" class="green radio_box" value="en_service" name="type_envoi" <?php if($type_envoi=="en_service") echo "checked";?>/>
                                En service (<?=$nb_connecte?>)
                            </label> -->
                            <label class="col-xs-12 checkbox-inline">
                                <input type="radio" class="green radio_box" value="unitaire" name="type_envoi" <?php if($type_envoi=="unitaire") echo "checked";?>/>
                                Unitaire
                            </label>
                        </div>
                    </div>

                    <div id="div_livreur_service" style="display:none;">
                    	<div class="col-sm-4 col-sm-offset-4 div_notif_livreur margin_label">
							<?php
							$livreurs=$Livreur->getAll("", "", "", "ON", "");
							foreach ($livreurs as $livreur) {
								$sel="";
								$test=explode(',', $destinataire);
								foreach($test as $bar) {
									if ($bar==$livreur->id) {
										$sel="checked";
									}
									//$sel=(strpos($destinataire,"'".$livreur->id."'")!==false) ? "checked" :  "";
								}
								?>
								<label class="col-sm-4 checkbox-inline">
                                    <input type="checkbox" class="green" value="<?=$livreur->id?>" name="livreur_service[]" <?php echo $sel; ?>>
                                    <?php echo $livreur->prenom." ".$livreur->nom; ?>
                                </label>
								<?php
							}
                            ?>
                        </div>
                    </div>

                    <div id="div_livreur_unitaire" style="display:none;">
                    	<div class="col-sm-4 col-sm-offset-4 div_notif_livreur margin_label">
							<?php
							$livreurs=$Livreur->getAll("", "", "", "", "");
							foreach ($livreurs as $livreur) {
								$sel="";
								$test=explode(',', $destinataire);
								foreach($test as $bar) {
									if ($bar==$livreur->id) {
										$sel="checked";
									}
									//$sel=(strpos($destinataire,"'".$livreur->id."'")!==false) ? "checked" :  "";
								}
								?>
								<label class="col-sm-4 checkbox-inline">
                                    <input type="checkbox" class="green" value="<?=$livreur->id?>" name="livreur_unite[]" <?php echo $sel; ?>>
                                    <?php echo $livreur->prenom." ".$livreur->nom; ?>
                                </label>
								<?php
							}
                            ?>
                        </div>
                    </div>

	                <div class="row row_btn">
                    	<div class="col-sm-6 col-sm-offset-6" style="text-align:right">
                    		<input type="button" onclick="lien('notification_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
	                        <input type="submit" id="bt" class="btn btn-main" value="Enregistrer" style="width:100px;">
                        </div>
                    </div> 
			    </form>
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
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$("textarea.autosize").autosize();

		$('.date-picker').datepicker({
			autoclose: true,
			weekStart: 1
		});

		$('.time-picker').timepicker({
        	showMeridian: false,
        	minuteStep:5,
    	});

		$(".time-picker").on("focus", function() {
		    return $(this).timepicker("showWidget");
		});

		if("<?=$type_envoi?>"=="en_service") {
			$("#div_livreur_service").show();
		}
		else if ("<?=$type_envoi?>"=="unitaire") {
			$("#div_livreur_unitaire").show();
		}
	});

	$('.radio_box').on('ifChanged', function (event) {
		$("#div_livreur_unitaire").hide();
		$("#div_livreur_service").hide();

		if (event.currentTarget.defaultValue=="en_service") {
			$("#div_livreur_service").show();
		}
		else if (event.currentTarget.defaultValue=="unitaire") {
			$("#div_livreur_unitaire").show();
		}
	});
</script>
