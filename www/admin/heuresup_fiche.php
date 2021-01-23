<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

require_once("inc_connexion.php");
if(isset($_GET["id"])){$id=urldecode($_GET["id"]);}else{$id="";}

$Livreur = new Livreur($sql, $id);
$Commercant = new Commercant($sql);

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
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">

<div id="body_popup" style="max-height:600px;overflow-y:auto">
	<div class="fond_popup_uni">
		<div class="modal-body">
			<button type="button" class="close popup-modal-dismiss" >&times;</button>
			<h3>Ajouter des heures supplémentaires</h3>
			
			<div class="row">
				<form class="form-horizontal" role="form" action="livreurs_fiche2.php" method="get">
                	<input type="hidden" name="id" id="id" value="<?=$id?>"/>
                	<input type="hidden" name="tab_actif" id="tab_actif" value="tab4"/>
                	<div class="form-group">
						<label class="col-sm-12 control-label" for="form-field-1">
                            Commerçant
                        </label>
                        <div class="col-sm-12">
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
	                    <div class="col-sm-3 col-sm-offset-3">
	                    	<select name="vehicule" id="vehicule" class="form-control search-select">
							    <option value="">&nbsp;</option>
							</select>
	                    </div>
	                    <div class="col-sm-3">
	                        <p><b>Date</b></p>
	                    	<div class="input-group">
	                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
								<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date" name="date" value="">
							</div>
	                    </div>
	                </div>
				</form>
			</div>
		</div>
	</div>
</div>

<script src="assets/plugins/select2/select2.min.js"></script>  
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>