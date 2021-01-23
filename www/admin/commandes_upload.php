<?php
require_once("inc_connexion.php");

$action=(isset($_POST["action"])) ? $_POST["action"] : "";
$aff_valide=(isset($_GET["aff_valide"])) ? $_GET["aff_valide"] : "";

$Commande = new Commande($sql);
$Commercant = new Commercant($sql);
$Livreur = new Livreur($sql);
$Client = new Client($sql);

$menu = "commande";
$sous_menu = "upload";

function validateDate($date) {
    $d = DateTime::createFromFormat('d/m/Y', $date);
    return $d && $d->format('d/m/Y') === $date;
}

function validateHour($heure) {
	$d = DateTime::createFromFormat('H:i', $heure);
    return $d && $d->format('H:i') === $heure;
}

 
if ($action=="insert_data") {
	$commandes_array = unserialize(stripslashes($_POST["commandes_array"]));

	foreach($commandes_array as $commande) {
		$id_client=$commande["id_client"];
		$Restaurant = new Commercant($sql, $commande["id_resto"]);
		$adresse_resto=$Restaurant->getAdresse();

		if ($id_client==0) {
			$id_client=$Client->verifClient($commande["nom_client"], $commande["prenom_client"], $commande["telephone_client"], $commande["id_resto"]);
			if (!$id_client) {
				$id_client=$Client->setClient("", $commande["nom_client"], $commande["prenom_client"], $commande["adresse_client"], $commande["latitude"], $commande["longitude"], $commande["telephone_client"], $commande["email_client"], $commande["commentaire_client"], $commande["id_resto"]);
			}
		}

		$Client_ = new Client($sql, $id_client);
		$adresse_client=$Client_->getAdresse();

		//calcul de la distance entre le restaurant et le client
		$resultat = getDistance($adresse_resto,$adresse_client);
		$distance = $resultat["distanceEnMetres"];
		$duree = $resultat["dureeEnSecondes"];

		$Commande->setCommande("", $commande["id_resto"], $id_client, $commande["id_livreur"], $commande["commentaire"], $commande["date_debut"], $commande["date_fin"], $distance, $duree);
	}

	header("Location: commandes_upload.php?aff_valide=1");
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
					<h1>Upload de commandes</h1>
					<div class="header-btn">						
						<a href="#myModal" role="button"  data-toggle="modal" class="btn btn-main btn-sm">Règles</a>
						<a href="gabarits/gabarit_commande.csv" class="btn btn-main btn-sm" target="_blank">Gabarit CSV</a>
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
                        <form role="form" name="form" id="form1" method="post" action="commandes_upload.php" class="form-horizontal" enctype="multipart/form-data">
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
		        	<table class="table table-bordered table-hover" id="tableau_commandes">
			    		<thead>
			        		<th>Commerçant nom</th>
			        		<th>Livreur nom</th>
			        		<th>Livreur prenom</th>
			        		<th>Date</th>
			        		<th>Heure début</th>
			        		<th>Heure fin</th>
			        		<th>Commentaire</th>
			        		<th>Client nom</th>
			        		<th>Client prénom</th>
			        		<th>Client téléphone</th>
			        		<th>Client adresse</th>
			        		<th>Client email</th>
			        		<th>Client commentaire</th>
			        		<th>Valide</th>
			        		<th>Statut</th>
			        	</thead>
			        	<tbody>
			        		<?php
							$row = 0;
							$cpt_erreur = 0;
							$array_commandes=[];
							$cpt_commandes = 0;
							$fp = fopen($_FILES["csv"]["tmp_name"],'r') or die("can't open file");

							while($csv_line = fgetcsv($fp,1024, ";")) {
								$csv_line = array_map("utf8_encode", $csv_line);
								if ($row>=1) {
									$commande_ok=true;
									$commande_erreur="";
								    print '<tr>';
								    for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
								        print '<td>'.$csv_line[$i].'</td>';
								    }

								    //vérifier les infos
								    $id_resto=$Commercant->verifCommercant($csv_line[0]);

								    if (!$id_resto) {
								    	$commande_ok=false;
								    	$commande_erreur.="Erreur commerçant<br/>";
								    	$id_resto=0;
								    }

								    $id_livreur=$Livreur->verifLivreur($csv_line[1], $csv_line[2]);
								    if ($csv_line[1]!="" && $csv_line[2]!="" && !$id_livreur) {
								    	$commande_ok=false;
								    	$commande_erreur.="Erreur livreur<br/>";
								    }

								    if (!validateDate($csv_line[3])) {
								    	$commande_ok=false;
								    	$commande_erreur.="Erreur date<br/>";
								    }
								    else {
								    	$date1  = DateTime::createFromFormat("d/m/Y", $csv_line[3]);
										$date2  = DateTime::createFromFormat("d/m/Y", date("d/m/Y"));

								    	if ($date1<$date2) {
								    		$commande_ok=false;
								    		$commande_erreur.="Erreur date<br/>";
								    	}

								    	if (!validateHour($csv_line[4])) {
									    	$commande_ok=false;
									    	$commande_erreur.="Erreur heure début<br/>";
									    }
									    else {
									    	$date_debut = DateTime::createFromFormat('d/m/Y H:i', $csv_line[3]." ".$csv_line[4]);
	    									$date_debut_txt=$date_debut->format('Y-m-d H:i:s');
									    }

									    if (!validateHour($csv_line[5])) {
									    	$commande_ok=false;
									    	$commande_erreur.="Erreur heure fin<br/>";
									    }
									    else {
									    	$date_fin = DateTime::createFromFormat('d/m/Y H:i', $csv_line[3]." ".$csv_line[5]);
	    									$date_fin_txt=$date_fin->format('Y-m-d H:i:s');
									    }

									    if ($date_debut>$date_fin) {
									    	$commande_ok=false;
									    	$commande_erreur.="Erreur date debut > date fin<br/>";
									    }
								    }

								    $id_client=$Client->verifClient($csv_line[7], $csv_line[8], $csv_line[9], $id_resto);

								    if ($csv_line[7]=="" || $csv_line[8]=="" || $csv_line[9]=="") {
								    	$commande_ok=false;
								    	$commande_erreur.="Erreur client<br/>";
								    	$id_client=0;
								    }
								    else if ($csv_line[10]=="" && $csv_line[11]=="" && $csv_line[12]=="" && !$id_client) {
								    	$commande_ok=false;
								    	$commande_erreur.="Erreur ancien client<br/>";
								    	$id_client=0;
								    }
									else if (($csv_line[10]!="" || $csv_line[11]!="" || $csv_line[12]!="") && $csv_line[10]=="") {
									    $commande_ok=false;
									    $commande_erreur.="Erreur nouveau client<br/>";
									    $id_client=0;
								    }
								    else if ($csv_line[10]!="") {
								    	$id_client=0;
								    	$cityclean = str_replace (" ", "+", $csv_line[10]);
										$details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=". $cityclean."&sensor=false";

										$ch = curl_init();
										curl_setopt($ch, CURLOPT_URL, $details_url);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
										$response = json_decode(curl_exec($ch), true);

										if ($response['status'] != 'OK') {
										  	$commande_ok=false;
								    		$commande_erreur.="Erreur nouveau client adresse<br/>";
										} 
										else {
										    $formatted_address = $response['results'][0]['formatted_address'];
										    $geometry = $response['results'][0]['geometry'];
										    $longitude = $geometry['location']['lng'];
										    $latitude = $geometry['location']['lat'];

										    $commande_erreur.=$formatted_address."<br/>".$longitude." / ".$latitude;
										}
								    }

								    if ($commande_ok) {
								    	print "<td><i class='fa fa-check cell-ok'></i></td>";

								    	//ajouter les infos dans le tableau
								    	$array_commandes[$cpt_commandes]["id_resto"]=$id_resto;
								    	$array_commandes[$cpt_commandes]["id_livreur"]=$id_livreur;
								    	$array_commandes[$cpt_commandes]["date_debut"]=$date_debut_txt;
								    	$array_commandes[$cpt_commandes]["date_fin"]=$date_fin_txt;
								    	$array_commandes[$cpt_commandes]["commentaire"]=$csv_line[6];
								    	$array_commandes[$cpt_commandes]["id_client"]=$id_client;
								    	$array_commandes[$cpt_commandes]["nom_client"]=$csv_line[7];
							    		$array_commandes[$cpt_commandes]["prenom_client"]=$csv_line[8];
							    		$array_commandes[$cpt_commandes]["telephone_client"]=$csv_line[9];
							    		$array_commandes[$cpt_commandes]["adresse_client"]=$formatted_address;
							    		$array_commandes[$cpt_commandes]["latitude"]=$latitude;
							    		$array_commandes[$cpt_commandes]["longitude"]=$longitude;
							    		$array_commandes[$cpt_commandes]["email_client"]=$csv_line[11];
							    		$array_commandes[$cpt_commandes]["commentaire_client"]=$csv_line[12];

								    	$cpt_commandes++;
								    }
								    else {
								    	print "<td><i class='fa fa-times cell-ko'></i></td>";
								    	$cpt_erreur++;
								    }
								    echo "<td>".$commande_erreur."</td>";
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
	        				<div class="col-sm-10">Nombre de commandes</div>
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
            		<form action="commandes_upload.php" method="post">
            			<input type="hidden" name="action" value="insert_data"/>
            			<input type="hidden" name="commandes_array" value='<?=addslashes(serialize($array_commandes))?>'/>

            			<input type="button" onclick="lien('commandes_upload.php')" id="bt" class="btn btn-light-grey  btn-sm" value="Annuler" style="width:100px;">
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
                        <p><b>Champs obligatoire :</b> Nom du commercant, jour, heure de début, heure de fin, nom du client, prenom du client, téléphone du client (si ancien client), adresse du client (si nouveau client).</p>
                        <p><b>Format du numéro de téléphone :</b> Changer le format de la colonne pour ne pas perdre le premier 0 (clic droit > Format de cellule > Choisir "texte" comme catégorie)</p>
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
