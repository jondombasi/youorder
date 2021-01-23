<?php
require_once("inc_connexion.php");

$action     = (isset($_POST["action"]))     ? $_POST["action"] : "";
$aff_valide = (isset($_GET["aff_valide"]))  ? $_GET["aff_valide"] : "";

$Commercant = new Commercant($sql);
$Livreur    = new Livreur($sql);
$Vehicule   = new Vehicule($sql);

$menu       = "livreur";
$sous_menu  = "upload";

function validateDate($date) {
    $d = DateTime::createFromFormat('d/m/Y', $date);
    return $d && $d->format('d/m/Y') === $date;
}

function validateHour($heure) {
	$d = DateTime::createFromFormat('H:i', $heure);
    return $d && $d->format('H:i') === $heure;
}

if ($action=="insert_data") {
	$planning_array = unserialize(stripslashes($_POST["planning_array"]));

	foreach($planning_array as $planning) {
		//print_r($planning);

		//echo "INSERT INTO livreurs_planning (id_livreur, id_commercant, id_vehicule, date_debut, date_fin) VALUES (".$sql->quote($planning["id_livreur"]).", ".$sql->quote($planning["id_resto"]).", ".$sql->quote($planning["id_vehicule"]).", ".$sql->quote($planning["date_debut"]).", ".$sql->quote($planning["date_fin"]).")<br/>";
		$Livreur->setPlanning($planning["id_livreur"], $planning["id_resto"], $planning["id_vehicule"], $planning["date_debut"], $planning["date_fin"], 'non');
	}

	header("Location: livreurs_planning_upload.php?aff_valide=1");
}

require_once("inc_header.php");
?>
<style>
	.panel-label {
		margin-top:5px;
		text-align:right;
	}

	.header-btn {
		float:right;
	}

	.page-header h1 {
		display:inline;
	}

	.cell-ok  {
		color:#92b74b;
	}

	.cell-ko {
		color:red;
	}

	@media (max-width:768px){
		.panel-label {
			text-align:left;
		}
		.panel-button {
			margin-top: 10px;
		}
	}
</style>

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1>Upload de planning</h1>
					<div class="header-btn">						
						<a href="#myModal" role="button"  data-toggle="modal" class="btn btn-main btn-sm">Règles</a>
						<a href="gabarits/gabarit_planning.csv" class="btn btn-main btn-sm" target="_blank">Gabarit CSV</a>
					</div>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
		<?php if($aff_valide=="1") {	?>
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
				<div class="panel panel-default">
                    <div class="panel-heading">
                    	<i class="fa fa-file-excel-o"></i>
                        CSV
                    </div>
                    <div class="panel-body">
                        <form role="form" name="form" id="form1" method="post" action="livreurs_planning_upload.php" class="form-horizontal" enctype="multipart/form-data">
                        	<input type="hidden" name="action" value="import_csv"/>
                        	<div class="col-sm-3 panel-label">
                        		<label for="csv">Importer le fichier</label>
                        	</div>
							<div class="col-sm-7">
		                        <input type="file" class="filestyle" data-buttonText="Choisir" data-iconName="fa fa-folder-open-o" data-size="sm" id="csv" name="csv" accept=".csv"/>
		                    </div>
			            	<div class="col-sm-2 panel-button">
								<input type="submit" class="btn btn-main btn-sm" value="Envoyer"/>
							</div>
					    </form>
                    </div>
                </div>
            </div>
        </div>		

        <?php 
		if ($action=="import_csv" && $_FILES["csv"]["tmp_name"]!="") { ?>
	        <div class="row">
	        	<div class="col-sm-12 table-responsive">
		        	<table class="table table-bordered table-hover">
			    		<thead>
			        		<th>Commerçant nom</th>
			        		<th>Livreur nom</th>
			        		<th>Livreur prénom</th>
			        		<th>Immatriculation véhicule</th>
			        		<th>Jour</th>
			        		<th>Heure début</th>
			        		<th>Heure fin</th>
			        		<th>Valide</th>
			        		<th>Statut</th>
			        	</thead>
			        	<tbody>
			        		<?php
							$row = 0;
							$cpt_erreur = 0;
							$array_planning=[];
							$cpt_planning = 0;
							$fp = fopen($_FILES["csv"]["tmp_name"],'r') or die("can't open file");

							while($csv_line = fgetcsv($fp,1024, ";")) {
								$csv_line = array_map("utf8_encode", $csv_line);
								if ($row>=1) {
									$planning_ok=true;
									$planning_erreur="";
								    print '<tr>';
								    for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
								        print '<td>'.$csv_line[$i].'</td>';
								    }

								    //vérifier les infos
								    $id_resto=$Commercant->verifCommercant($csv_line[0]);

								    if (!$id_resto) {
								    	$planning_ok=false;
								    	$planning_erreur.="Erreur commerçant<br/>";
								    	$id_resto=0;
								    }

								    $id_livreur=$Livreur->verifLivreur($csv_line[1], $csv_line[2]);
								    if ($csv_line[1]!="" && $csv_line[2]!="" && !$id_livreur) {
								    	$planning_ok=false;
								    	$planning_erreur.="Erreur livreur<br/>";
								    }

								    $id_vehicule=$Vehicule->verifVehicule($csv_line[3]);
								    if ($csv_line[3]!="" && !$id_vehicule) {
								    	$planning_ok=false;
								    	$planning_erreur.="Erreur véhicule<br/>";
								    }

								    if (!validateDate($csv_line[4])) {
								    	$planning_ok=false;
								    	$planning_erreur.="Erreur date<br/>";
								    }
								    else {
								    	$date1 = DateTime::createFromFormat("d/m/Y", $csv_line[4]);
										$date2 = DateTime::createFromFormat("d/m/Y", date("d/m/Y"));

								    	if ($date1<$date2) {
								    		$planning_ok=false;
								    		$planning_erreur.="Erreur date<br/>";
								    	}

								    	if (!validateHour($csv_line[5])) {
									    	$planning_ok=false;
									    	$planning_erreur.="Erreur heure début<br/>";
									    }
									    else {
									    	$date_debut = DateTime::createFromFormat('d/m/Y H:i', $csv_line[4]." ".$csv_line[5]);
	    									$date_debut_txt=$date_debut->format('Y-m-d H:i:s');
									    }

									    if (!validateHour($csv_line[6])) {
									    	$planning_ok=false;
									    	$planning_erreur.="Erreur heure fin<br/>";
									    }
									    else {
									    	$date_fin = DateTime::createFromFormat('d/m/Y H:i', $csv_line[4]." ".$csv_line[6]);
	    									$date_fin_txt=$date_fin->format('Y-m-d H:i:s');
									    }

									    if ($date_debut>$date_fin) {
									    	$planning_ok=false;
									    	$planning_erreur.="Erreur date debut > date fin<br/>";
									    }
								    }

								    if ($planning_ok) {
								    	print "<td><i class='fa fa-check cell-ok'></i></td>";

								    	//ajouter les infos dans le tableau
								    	$array_planning[$cpt_planning]["id_resto"]      =$id_resto;
								    	$array_planning[$cpt_planning]["id_livreur"]    =$id_livreur;
								    	$array_planning[$cpt_planning]["id_vehicule"]   =$id_vehicule;
								    	$array_planning[$cpt_planning]["date_debut"]    =$date_debut_txt;
								    	$array_planning[$cpt_planning]["date_fin"]      =$date_fin_txt;

								    	$cpt_planning++;
								    }
								    else {
								    	print "<td><i class='fa fa-times cell-ko'></i></td>";
								    	$cpt_erreur++;
								    }
								    echo "<td>".$planning_erreur."</td>";
								    print "</tr>\n";
								}
							    $row++;
							}
							fclose($fp) or die("can't close file");
							?>
			        	</tbody>
			        </table>
				</div>
	        </div>		

	        <div class="row">
	        	<div class="col-sm-4 col-sm-offset-8">
		        	<div class="well">
		        		<div class="row">
	        				<div class="col-sm-10">Nombre de planning</div>
	        				<div class="col-sm-2"><?=$row-1?></div>
	        			</div>
	        			<div class="row">
	        				<div class="col-sm-10">Valide</div>
	        				<div class="col-sm-2"><?=($row-1)-$cpt_erreur?></div>
	        			</div>
	        			<div class="row">
	        				<div class="col-sm-10">Invalide</div>
	        				<div class="col-sm-2"><?=$cpt_erreur?></div>
	        			</div>
		            </div>
		        </div>
	        </div>	

	        <div class="row">
            	<div class="col-sm-6 col-sm-offset-6" style="text-align:right">
            		<form action="livreurs_planning_upload.php" method="post">
            			<input type="hidden" name="action" value="insert_data"/>
            			<input type="hidden" name="planning_array" value='<?=addslashes(serialize($array_planning))?>'/>

            			<input type="button" onclick="lien('livreurs_planning_upload.php')" id="bt" class="btn btn-light-grey  btn-sm" value="Annuler" style="width:100px;">
                    	<input type="submit" id="bt" class="btn btn-main btn-sm" value="Valider" style="width:100px;">
                    </form>
                </div>
            </div> 
	    <?php } ?>

		<!-- MODAL -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">Règles</h4>
					</div>
					<div class="modal-body">
                        <p><b>Champs obligatoire :</b> Nom du commercant, jour, heure de début, heure de fin</p>
                        <p><b>Format du jour :</b> DD/MM/YYYY (ex : 27/03/2017)</p>
                        <p><b>Format des heures de début et de fin :</b> HH:MM (ex : 18:00)</p>
                        <p>Lors de la validation des données, seule les commandes affichées valides seront ajoutées.</p>
						<div style="text-align:center;margin-top:20px;">
                        	<button aria-hidden="true" data-dismiss="modal" class="btn btn-main btn-sm">OK</button>
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

<script type="text/javascript" src="assets/js/bootstrap-filestyle.min.js"></script>
