<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

require_once("inc_connexion.php");
if(isset($_GET["id"])){$id=urldecode($_GET["id"]);}else{$id="";}

$Livreur = new Livreur($sql);
$Vehicule = new Vehicule($sql);
$Commercant = new Commercant($sql);
$fiche_planning=$Livreur->getPlanningFiche($id);
?>
<style type="text/css">
	#body_popup {
		max-width:1000px;
		min-width:300px;
		margin:0 auto;
	}
	.fond_popup_uni {
		width: 100%;
		background: #FFF;
	}

	.change_planning {
		height:50px;
		line-height: 40px;
		padding-left: 0px;
	}

	.change_planning2 {
		height:40px;
		line-height: 40px;
		padding-left: 0px;
	}

	.change_planning p, .change_planning2 p {
		margin: 0px;
	}

	.select2-container .select2-choice {
		margin-top: 0px !important;
	}

	.has-error {
		border-color: #B94A48 !important;
	}
</style>

<div id="body_popup" style="max-height:600px;overflow-y:auto">
	<div class="fond_popup_uni">
		<div class="modal-body">
			<button type="button" class="close popup-modal-dismiss" >&times;</button>
			<h3><?=$fiche_planning[0]->nom_resto?></h3>

			<div class="alert alert-danger erreur_modal" style="display:none">
	            <button class="close" data-dismiss="alert">
	                ×
	            </button>
	            <i class="fa fa-check-circle"></i>
	            <span id="erreur_check">Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.</span>
	        </div>      

			<div class="row" style="margin-top:20px">
				<div class="col-sm-12">
					<form class="form-horizontal" role="form" id="planning_form" action="planning_fiche.php" method="post">
						<input type="hidden" id="id"            name="id"               value="<?=$id?>"/>
						<input type="hidden" id="date_debut"    name="date_debut"       value="<?=$fiche_planning[0]->date_debut?>"/>
						<input type="hidden" id="date_fin"      name="date_fin"         value="<?=$fiche_planning[0]->date_fin?>"/>
						<input type="hidden" id="vehicule_base" name="vehicule_base"    value="<?=$fiche_planning[0]->id_vehicule?>"/>
						<input type="hidden" id="livreur_base"  name="livreur_base"     value="<?=$fiche_planning[0]->id_livreur?>"/>

						<div class="col-sm-6" style="padding-left:0px">
							<div class="col-sm-6 change_planning">
			                	<p><b>Livreur</b></p>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<select name="livreur" id="livreur" class="form-control search-select">
		                            <option value="">&nbsp;</option>
								    <?php 
										foreach ($Livreur->getAll("", "", "", "", "") as $livreur) {
											$sel=($fiche_planning[0]->id_livreur==$livreur->id) ? "selected" : "";
											echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom. " ".$livreur->nom."</option>";
										}
								    ?>
		                        </select>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<p><b>Commerçant</b></p>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<select name="commercant" id="commercant" class="form-control search-select">
								    <?php 
										foreach ($Commercant->getAll("", "") as $commercant) {
											$sel=($fiche_planning[0]->id_commercant==$commercant->id) ? "selected" : "";
											echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom."</option>";
										}
								    ?>
								</select>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<p><b>Véhicule</b></p>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<select name="vehicule" id="vehicule" class="form-control search-select">
								    <option value="">&nbsp;</option>
								    <?php 
										foreach ($Vehicule->getAll("", "", "", "", true) as $vehicule) {
											$sel=($fiche_planning[0]->id_vehicule==$vehicule->id) ? "selected" : "";
											echo "<option value='".$vehicule->id."' ".$sel.">".$vehicule->nom."</option>";
										}
								    ?>
								</select>
			                </div>
						</div>
						<div class="col-sm-6" style="padding-left:0px">
							<div class="col-sm-6 change_planning">
			                	<p><b>Date</b></p>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<div class="input-group">
		                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
									<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date" name="date" value="<?=date("d-m-Y", strtotime($fiche_planning[0]->date_debut))?>">
								</div>
			                	<!-- <p><?=date("d/m/Y", strtotime($fiche_planning[0]->date_debut))?></p> -->
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<p><b>Heure de début</b></p>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<div class="input-group input-append bootstrap-timepicker">
									<input type="text" id="h_debut" name="h_debut" class="form-control" value="<?=date("H:i", strtotime($fiche_planning[0]->date_debut))?>"/>
									<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
								</div>
			                	<!-- <p><?=date("H:i", strtotime($fiche_planning[0]->date_debut))?></p> -->
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<p><b>Heure de fin</b></p>
			                </div>
			                <div class="col-sm-6 change_planning">
			                	<div class="input-group input-append bootstrap-timepicker">
									<input type="text" id="h_fin" name="h_fin" class="form-control" value="<?=date("H:i", strtotime($fiche_planning[0]->date_fin))?>"/>
									<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
								</div>
			                	<!-- <p><?=date("H:i", strtotime($fiche_planning[0]->date_fin))?></p> -->
			                </div>
			                <!-- <div class="col-sm-6 change_planning2">
			                	<p><b>Récurrence</b></p>
			                </div>
			                <div class="col-sm-6 change_planning2">
			                	<p><?=$fiche_planning[0]->recurrence?></p>
			                </div> -->
						</div>
			        </form>
				</div>
			</div>
			<div style="text-align:center;margin-top:20px;">
				<button id="planning_delete_btn" class="btn btn-bricky popup-modal-dismiss">
					Supprimer
				</button>
				<button class="btn btn-default popup-modal-dismiss">
					Annuler
				</button>
				<button id="planning_btn" class="btn btn-default">
					OK
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$("select.search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});

		$('.date-picker').datepicker({
            autoclose: true,
            weekStart: 1
        });
	})

	$('#planning_btn').click(function(){
		var continu=true;

		$(".erreur_modal").hide();
		$("#livreur").removeClass("has-error");
		$("#vehicule").removeClass("has-error");

		if ($("#date").val()=="") {
			$("#erreur_check").html("La date entrée n'est pas valide");
			$("#date").addClass("has-error");
			continu=false;
		}
		if ($("#h_debut").val()=="") {
			$("#erreur_check").html("La date entrée n'est pas valide");
			$("#h_debut").addClass("has-error");
			continu=false;
		}
		if ($("#h_fin").val()=="") {
			$("#erreur_check").html("La date entrée n'est pas valide");
			$("#h_fin").addClass("has-error");
			continu=false;
		}

		if (continu) {
			$.ajax({
	            url      : 'action_poo.php?action=update_planning',
	            data     : $("#planning_form").serialize(),
	            type     : "POST",
	            cache    : false,         
	            success: function(transport) {  
	            	console.log(transport);
	            	if (transport=="ok") {
	            		getWeek("calendar_theorique");
	            		$('#calendar_theorique').fullCalendar('refetchEvents');
	            		$.magnificPopup.close();
	            	}
	            	else {
	            		$("#erreur_check").html("Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.");
	            		if (transport=="erreur_livreur") {
	            			$("#erreur_check").html("Ce livreur est déjà en service pendant cette période");
	            			$("#s2id_livreur").find(".select2-choice").addClass("has-error");
		            	}
		            	else if (transport=="erreur_vehicule") {
		            		$("#erreur_check").html("Ce véhicule est déjà en service pendant cette période");
		            		$("#s2id_vehicule").find(".select2-choice").addClass("has-error");
		            	}
		            	else if (transport=="erreur_date") {
		            		$("#erreur_check").html("La date entrée n'est pas valide");
		            		$("#date").addClass("has-error");
		            	}
		            	else if (transport=="erreur_heure") {
		            		$("#h_debut").addClass("has-error");
		            		$("#h_fin").addClass("has-error");
		            		$("#erreur_check").html("L'heure de début doit être être inférieure à l'heure de fin");
		            	}
		            	$(".erreur_modal").show();
	            	}
	            }
	        });
		}
    })

	$('#planning_delete_btn').click(function(){
		$.ajax({
            url      : 'action_poo.php',
            data     : "action=delete_planning&id=<?=$id?>",
            type     : "GET",
            cache    : false,         
            success: function(transport) {  
            	console.log(transport);
            	getWeek("calendar_theorique");
        		$('#calendar_theorique').fullCalendar('refetchEvents');
        		$.magnificPopup.close();
            }
        });
    });
</script>