<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		    {$id        =$_GET["id"];}          else{$id="";}
if(isset($_GET["aff_valide"]))	{$aff_valide=$_GET["aff_valide"];}  else{$aff_valide="";}
if(isset($_POST["action"]))		{$action    =$_POST["action"];}     else{$action="";}

if(isset($_GET["resto"]))		{$restaurant=$_GET["resto"];}       else{$restaurant="";}
if(isset($_GET["client"]))		{$get_client=$_GET["client"];}      else{$get_client="";}
if(isset($_GET["livreur"]))		{$livreur   =$_GET["livreur"];}     else{$livreur="";}

$Commande   = new Commande($sql, $id);
$Commercant = new Commercant($sql, $_POST["restaurant"]);
$Livreur    = new Livreur($sql);
$Client     = new Client($sql, $_POST["client"]);

$menu = "commande";
if($id==""){
	$sous_menu = "fiche";
	$titre_page = "Ajouter une commande";
}else{
	$sous_menu = "liste";
	$titre_page = "Modifier une commande";
}
$aff_erreur = "";

if($id!=""){
	$id_restaurant  = $Commande->getRestaurant();
	$id_client      = $Commande->getClient();
	$id_livreur     = $Commande->getLivreur();
	$commentaire    = $Commande->getCommentaire();
	$date_debut_bdd = $Commande->getDateDebut();
	$date_debut     = date("d-m-Y",strtotime($date_debut_bdd));
	$heure_debut    = date("H:i",strtotime($date_debut_bdd));
	$date_fin_bdd   = $Commande->getDateFin();
	$heure_fin      = date("H:i",strtotime($date_fin_bdd));
	$statut         = $Commande->getStatut();
}
else {
	$date_debut     = date("d-m-Y");
	$heure_debut    = date("H:i",time()+60*30);
	$heure_fin      = date("H:i",time()+60*90);
}

$continu = true;

if($action=="enregistrer") {
	$id_restaurant  = $_POST["restaurant"];
	$id_client	 	= $_POST["client"];
	$new_client	 	= $_POST["new_client"];
	$id_livreur	 	= $_POST["livreur"];
	$date_debut 	= $_POST["date_debut"];
	$heure_debut	= $_POST["heure_debut"];
	$heure_fin		= $_POST["heure_fin"];
	$commentaire	= $_POST["commentaire"];
	
	$nom_new	    = $_POST["nom_new"];
	$prenom_new	    = $_POST["prenom_new"];
	$adresse_new	= $_POST["adresse_new"];
	$longitude_new	= $_POST["longitude_new"];
	$latitude_new	= $_POST["latitude_new"];
	$numero_new	    = $_POST["numero_new"];
	$email_new	    = $_POST["email_new"];
	$adresse_new	= $_POST["adresse_new"];
	$commentaire_new= $_POST["commentaire_new"];
	
	if($id_restaurant==""){
		$css_restaurant_obl = "has-error";
		$continu = false;
	}
	if ($new_client=="oui") {
		if($nom_new==""){
			$css_nom_new_obl = "has-error2";
			$continu = false;
		}
		if($prenom_new==""){
			$css_prenom_new_obl = "has-error2";
			$continu = false;
		}
		if($adresse_new==""){
			$css_adresse_new_obl = "has-error2";
			$continu = false;
		}
		if ($longitude_new=="" || $latitude_new=="") {
			$css_adresse_new_obl = "has-error2";
			$continu = false;
		}
		if($numero_new==""){
			$css_numero_new_obl = "has-error2";
			$continu = false;
		}
		/*if($email_new==""){
			$css_email_new_obl = "has-error2";
			$continu = false;
		}
		if($commentaire_new==""){
			$css_commentaire_new_obl = "has-error2";
			$continu = false;
		}*/

		//vérifier que le client n'existe pas déjà
		foreach ($Client->getAll("", "", "", "", $id_restaurant) as $client_check) {
			if ($numero_new==$client_check->numero) {
				$css_numero_new_obl = "has-error2";
				$continu = false;
			}
			if ($email_new==$client_check->email && $email_new!="") {
				$css_email_new_obl = "has-error2";
				$continu = false;
			}
		}
	}
	else if($id_client==""){
		$css_client_obl = "has-error";
		$continu = false;
	}
	/*if($commentaire==""){
		$css_commentaire_obl = "has-error";
		$continu = false;
	}*/
	if($date_debut=="" || $heure_debut=="" || $heure_fin==""){
		$css_date_debut_obl = "has-error";
		$continu = false;
	}else{
		$date_debut_bdd = date("Y-m-d H:i:s",strtotime($date_debut." ".$heure_debut.':00'));	
		$date_fin_bdd   = date("Y-m-d H:i:s",strtotime($date_debut." ".$heure_fin.':00'));
	}

	if($continu){
		$adresse1 = $Commercant ->getLatitude().",".$Commercant->getLongitude();
		$adresse2 = $Client     ->getLatitude().",".$Client->getLongitude();

		//calcul de la distance entre le restaurant et le client
		$resultat = getDistance($adresse1,$adresse2);
		$distance = $resultat["distanceEnMetres"];
		$duree = $resultat["dureeEnSecondes"];

		//echo $distance." / ".$duree;

		/*$distance=0;
		$duree=0;*/

		if ($new_client=="oui") {
			$id_client=$Client->setClient($id_client, $nom_new, $prenom_new, $adresse_new, $latitude_new, $longitude_new, $numero_new, $email_new, $commentaire_new, $id_restaurant);
		}

		$id_commande=$Commande->setCommande($id, $id_restaurant, $id_client, $id_livreur, $commentaire, $date_debut_bdd, $date_fin_bdd, $distance, $duree);

		header("location: commandes_visu.php?id=".$id_commande);
		exit();
	}
	else{
		$aff_erreur="1";
	}
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<style>
    @media(max-width: 767px){
        .first-input{margin-right:0 !important;}
    }
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC15w0ru2bvazBjNdaHtVLXngRT6JfSh2s"></script>

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
				<form role="form" name="form" id="form1" method="post" action="commandes_fiche.php?id=<?=$id;?>" class="form-horizontal">
	            	<input type="hidden" name="action" value="enregistrer">
	                <div id="div_resto" class="form-group <?php echo $css_restaurant_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-select-1">
	                        Commerçant<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4 margin_label">
	                        <select name="restaurant" id="restaurant" class="form-control search-select" onchange="change_restaurant()">
	                        	<option value="">&nbsp;</option>
							    <?php 
									foreach ($Commercant->getAll("", "", "", true) as $commercant) {
										$sel=($id_restaurant==$commercant->id || $_GET["resto"]==$commercant->id) ? "selected" : "";
										echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom." - ".$commercant->adresse."</option>";
									}
							    ?>
	                        </select>
	                    </div>
	                </div>                                                
	                <div id="div_client" class="form-group <?php echo $css_client_obl; ?>">
	                	<input type="hidden" id="new_client" name="new_client" value="<?=$new_client?>"/> 
	                	<input type="hidden" id="longitude_new" name="longitude_new" value="<?=$longitude_new?>"/> 
	                	<input type="hidden" id="latitude_new" name="latitude_new" value="<?=$latitude_new?>"/> 

	                    <label class="col-sm-4 col-xs-12 control-label" for="form-field-select-1">
	                        Client<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4 col-xs-10 margin_label">
	                        <select name="client" id="client" class="form-control search-select">
	                        	<option value="">&nbsp;</option>
	                        	<?php if ($id_restaurant!="" || $id_client!="") {
									foreach ($Client->getAll("", "", "", "", $id_restaurant) as $client) {
										$sel=($id_client==$client->id || $_GET["client"]==$client->id) ? "selected" : "";
										echo "<option value='".$client->id."' ".$sel.">".$client->nom." ".$client->prenom." - ".$client->adresse."</option>";
									}
	                        	}?>
	                        </select>
	                        <div id="div_client_new" style="border-left:1px solid #aaa;border-right:1px solid #aaa;border-bottom:1px solid #aaa;padding:15px;display:none">
			                	<div class="form-group">
			                		<div class="col-sm-6 margin_label">
				                        <input type="text" name="nom_new" id="nom_new" class="form-control <?=$css_nom_new_obl?>" value="<?=$nom_new?>" placeholder="Nom*"/>
				                    </div>
				                    <div class="col-sm-6 margin_label">
				                        <input type="text" name="prenom_new" id="prenom_new" class="form-control <?=$css_prenom_new_obl?>" value="<?=$prenom_new?>" placeholder="Prénom*"/>
				                    </div>
				                </div>
				                <div class="form-group">
				                    <div class="col-sm-12 margin_label">
				                        <input type="text" name="adresse_new" id="adresse_new" class="form-control <?=$css_adresse_new_obl?>" value="<?=$adresse_new?>" placeholder="Adresse*"/>
				                    </div>
				                </div>
				                <div class="form-group">
				                    <div class="col-sm-12 margin_label">
				                        <input type="text" name="numero_new" id="numero_new" class="form-control <?=$css_numero_new_obl?>" value="<?=$numero_new?>" placeholder="Numéro*"/>
				                    </div>
				                </div>
				                <div class="form-group">
				                    <div class="col-sm-12 margin_label">
				                        <input type="text" name="email_new" id="email_new" class="form-control <?=$css_email_new_obl?>" value="<?=$email_new?>" placeholder="Email"/>
				                    </div>
				                </div>
				                <div class="form-group">
				                    <div class="col-sm-12 margin_label">
				                        <textarea class="autosize form-control <?=$css_commentaire_new_obl?>" id="commentaire_new" name="commentaire_new" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;" placeholder="Commentaire"><?=$commentaire_new?></textarea>
				                    </div>
				                </div>
				            </div>
	                    </div>
	                    <?php if ($_GET["resto"]=="" && $_GET["client"]=="") { ?>
		                    <div class="col-sm-1 col-xs-2" style="margin-top:3px;padding-left: 0;">
		                    	<a href="javascript:void(0)" id="new_client_btn" class="btn btn-default" style="height:34px;line-height:14px;"><i class="fa fa-plus"></i></a>
		                    </div>
		                <?php } ?>
	                </div>    

	                <?php if ($_SESSION["admin"]) { ?>
		                <div id="div_livreur" class="form-group">
		                    <label class="col-sm-4 control-label" for="form-field-select-1">
		                        Livreur
		                    </label>
		                    <div class="col-sm-4 margin_label">
		                        <select name="livreur" id="livreur" class="form-control search-select">
		                        	<option value="">&nbsp;</option>
								    <?php 
										foreach ($Livreur->getAll("", "", "", "", "", "") as $livreur) {
											$sel=($id_livreur==$livreur->id) ? "selected" : "";
											echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom. " ".$livreur->nom."</option>";
										}
								    ?>
		                        </select>
		                    </div>
		                </div>   
		            <?php } ?>                                         
	                <div class="form-group <?php echo $css_date_debut_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-3">
	                        Créneau de livraison<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4 col-lg-8 col-md-8">
	                        <div class="input-group col-sm-12 col-lg-3 col-md-3 first-input" style="margin-right:10px;float:left;">
	                            <input type="text" name="date_debut" data-date-format="dd-mm-yyyy" value="<?php echo $date_debut ?>" data-date-viewmode="years" class="form-control date-picker">
	                            <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
	                        </div>    
                   
	                        <div style="float:left;margin-right:10px;line-height:30px">Entre</div>                            
	                        <div class="input-group input-append bootstrap-timepicker col-sm-12 col-lg-3 col-md-3" style="margin-right:10px;float:left;">
	                            <input type="text" id="heure_debut" name="heure_debut" class="form-control time-picker" value="<?php echo $heure_debut ?>">
	                            <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
	                        </div>                                    
	                        <div style="float:left;margin-right:10px;line-height:30px">et</div>                            
	                        <div class="input-group input-append bootstrap-timepicker col-sm-12 col-lg-3 col-md-3" style="float:left;">
	                            <input type="text" id="heure_fin" name="heure_fin" class="form-control time-picker" value="<?php echo $heure_fin ?>">
	                            <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
	                        </div>                                  
	                    </div>
	                </div>

	                <div class="form-group <?php echo $css_commentaire_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-1">
	                        Commentaire
	                    </label>
	                    <div class="col-sm-4">
	                        <textarea class="autosize form-control" id="commentaire" name="commentaire" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;"><?php echo $commentaire; ?></textarea>
	                    </div>
	                </div>

	                <div class="row row_btn">
                    	<div class="col-sm-6 col-sm-offset-6" style="text-align:right">
                    		<input type="button" onclick="lien('commandes_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
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
<script src="assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
<script src="assets/plugins/select2/select2.min.js"></script>
<script language="javascript" type="text/javascript">
	$(window).load(function() {
  		autocomplete = new google.maps.places.Autocomplete(
	      (document.getElementById('adresse_new')),
	      { types: ['geocode'] ,componentRestrictions: {country: 'fr'}});
	 
		  google.maps.event.addListener(autocomplete, 'place_changed', function() {
		   fillInAddress();
	   
	  });
	});

	$(document).ready(function() {
		runSelect2();
		$("textarea.autosize").autosize();
		runDatePicker();
		change_restaurant();

		if ($("#new_client").val()=="oui") {
			$("#div_client_new").show();
			$("#new_client_btn").find("i").attr("class", "fa fa-minus");
		}
		else {
			$("#new_client").val("non");
			$("#div_client_new").hide();
			$("#new_client_btn").find("i").attr("class", "fa fa-plus");
		}

		<?php if ($css_restaurant_obl=="has-error") { ?>
			$("#s2id_restaurant").find(".select2-choice").addClass("has-error2");
		<?php } ?>

		<?php if ($css_client_obl=="has-error") { ?>
			$("#s2id_client").find(".select2-choice").addClass("has-error2");
		<?php } ?>
	});

	$("#new_client_btn").click(function() {
		if ($("#new_client").val()=="oui") {
			$("#new_client").val("non");
			$("#div_client_new").hide();
			$(this).find("i").attr("class", "fa fa-plus");
		}
		else {
			$("#new_client").val("oui");
			$("#div_client_new").show();
			$(this).find("i").attr("class", "fa fa-minus");
		}
	})

	function fillInAddress() {
	  // Get the place details from the autocomplete object.
	  var place = autocomplete.getPlace();
		//console.log(place.geometry.location.lng()+' '+place.geometry.location.lat())
		$("#longitude_new").val(place.geometry.location.lng());
		$("#latitude_new").val(place.geometry.location.lat());
	  
	}

	function runDatePicker() {
		$('.date-picker').datepicker({
			autoclose: true
		});
		$('#heure_debut, #heure_fin').timepicker({
			minuteStep: 1,
			showSeconds: false,
			showMeridian: false,
			defaultTime: '00:00'
		});

		$("#heure_debut, #heure_fin").on("focus", function() {
            return $(this).timepicker("showWidget");
        });

		$('#heure_debut').timepicker().on('changeTime.timepicker', function(e) {
			//on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
			//TO DO : changer la date si l'heure passe a 1h du jour suivant ?
			var d = new Date("1970-01-01 "+e.time.value+":00");
			d.setHours(d.getHours() + 1);

			$('#heure_fin').timepicker('setTime', d.getHours()+":"+d.getMinutes());

	    });
	};

	function change_restaurant(){
		restaurant = $("#restaurant").val()
		$.ajax({
		  	url      : 'action_poo.php',
		  	data	   : 'action=select_client&get_client=<?=$get_client?>&client=<?=$id_client;?>&restaurant='+restaurant,
		  	type	   : "GET",
		  	cache    : false,	  
		  	success  : function(transport) {  
		  		//console.log(transport)
				document.getElementById('client').innerHTML = transport;
				runSelect("#client");
			}
		});					
	}
	
	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	};
	function runSelect(nomselect) {
		$(nomselect).select2({
			placeholder: "Select a State",
			allowClear: true
		});
	};
</script>
