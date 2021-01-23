<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

require_once("inc_connexion.php");
if(isset($_GET["id"])){$id=urldecode($_GET["id"]);}else{$id="";}

$Livreur = new Livreur($sql, $id);


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
		<div class="modal-body">
			<button type="button" class="close popup-modal-dismiss" >&times;</button>
			<h3>Détails des votes</h3>
			<div class="row">
				<div class="col-sm-12">
					<table class="table table-bordered table-hover" style="margin-top:20px;" id="sample-table-1">
					    <thead>
					        <tr>
					            <th>Horodatage</th>
					            <th>Client</th>
					            <th>Note</th>
					        </tr>
					    </thead>
					    <tbody>
					    	<?php
					    	$vide=true;
					    	$notes=$Livreur->getAllNote($id);
					    	foreach ($notes as $note) {
					    		$vide=false;
					    		?>
					    		<tr>
					    			<td><?=date("d/m/Y \à H:i", strtotime($note->date))?></td>
									<td><?=$note->prenom." ".$note->nom?></td>
									<td>
										<?php
										for($x=1;$x<=5;$x++){
											if($note->note>=$x){
												$etoile_src = "notation-on.png";
											}else{
												$etoile_src = "notation-off.png";
											}
											?>
				                            <img class="note_etoile" src="images/<?=$etoile_src?>"/>                                
			                                <?php	
										}
										?>
										<span style="margin-left:10px"><?=$note->note?>/5</span></td>
					    		</tr>
					    		<?php
					    	}
					    	if ($vide) {
					    		echo "<tr><td colspan='3'>Il n'y a pas encore de notes</td></tr>";
					    	}
					    	?>
					    </tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
