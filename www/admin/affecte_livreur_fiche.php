<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

require_once("inc_connexion.php");
if(isset($_GET["id_commande"])){$id_commande=urldecode($_GET["id_commande"]);}else{$id_commande="";}
if(isset($_GET["id_livreur"])){$id_livreur=urldecode($_GET["id_livreur"]);}else{$id_livreur="";}
if(isset($_GET["page"])){$page=urldecode($_GET["page"]);}else{$page="";}

$Livreur = new Livreur($sql, $id_livreur);

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
</style>
<div id="body_popup" style="max-height:600px;overflow-y:auto">
	<div class="fond_popup_uni">
		<div class="modal-body" style="text-align:center">
			<button type="button" class="close popup-modal-dismiss" >&times;</button>
			<h3>Affectation de la commande</h3>

			<div class="alert alert-danger erreur_modal" style="display:none">
	            <button class="close" data-dismiss="alert">
	                ×
	            </button>
	            <i class="fa fa-check-circle"></i>
	            <span id="erreur_check">Ce livreur est déjà affecté pendant cette période</span>
	        </div>

			<div class="row" style="margin-top:20px">
				<div class="col-sm-6 col-sm-offset-3">
					<form class="form-horizontal" role="form" id="affecter_form" action="affecte_livreur_fiche.php" method="post">
						<input type="hidden" name="id_commande" id="id_commande" value="<?=$id_commande?>"/>
						<select name="id_livreur" id="id_livreur" class="form-control search-select" style="text-align:left">
							<option value="0">&nbsp;</option>
						    <?php 
								foreach ($Livreur->getAll("", "", "", "", "", "") as $livreur) {
									$sel=($id_livreur==$livreur->id) ? "selected" : "";
									echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom. " ".$livreur->nom."</option>";
								}
						    ?>
	                    </select>
	                </form>
				</div>
			</div>
			<div style="text-align:center;margin-top:20px;">
				<button id="affecte_btn" class="btn btn-default">
					Valider
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
	})
	$('#affecte_btn').click(function(){
		var continu=true;

		$(".erreur_modal").hide();
		$("#id_livreur").removeClass("has-error");

		if (continu) {
			$.ajax({
	            url      : 'action_poo.php?action=affecter_livreur',
	            data     : $("#affecter_form").serialize(),
	            type     : "POST",
	            cache    : false,         
	            success: function(transport) {  
	            	console.log(transport);
	            	if (transport=="erreur") {
	            		$("#s2id_id_livreur").find(".select2-choice").addClass("has-error2");
		            	$(".erreur_modal").show();
	            	}
	            	else if (transport=="desaffecte"){
	            		tableau_resultat('<?=$page?>');
	            		$(".modal-body").html('<button type="button" class="close popup-modal-dismiss" >&times;</button><div class="row" style="margin-top:20px"><div class="col-sm-6 col-sm-offset-3"><div style="font-size:40px;color:green;margin-bottom:10px;"><i class="fa fa-check"></i></div><div>Commande désaffectée</div><div style="text-align:center;margin-top:20px;"><button class="btn btn-default popup-modal-dismiss">OK</button></div></div></div>');
	            	}
	            	else {
	            		//$.magnificPopup.close();
	            		//recharger le tableau avec la page en cours
	            		tableau_resultat('<?=$page?>');
	            		$(".modal-body").html('<button type="button" class="close popup-modal-dismiss" >&times;</button><div class="row" style="margin-top:20px"><div class="col-sm-6 col-sm-offset-3"><div style="font-size:40px;color:green;margin-bottom:10px;"><i class="fa fa-check"></i></div><div>Commande affectée au livreur : </div><div><b>'+transport+'</b></div><div style="text-align:center;margin-top:20px;"><button class="btn btn-default popup-modal-dismiss">OK</button></div></div></div>');
	            	}
	            }
	        });
		}
    })
</script>
