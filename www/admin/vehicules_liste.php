<?php
$menu = "vehicule";
$sous_menu = "liste";
require_once("inc_header.php");
if(isset($_GET["type"]))		    {$type=$_GET["type"];}else{$type="";}
if(isset($_GET["immatriculation"]))	{$immatriculation=$_GET["immatriculation"];}else{$immatriculation="";}

if ($type=="" && $immatriculation=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}

$Vehicule   = new Vehicule($sql);
$Vehicule->getPagination(30, $type, $immatriculation);
$nbpages    = $Vehicule->getNbPages();
$nbres      = $Vehicule->getNbRes();
?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
	<div class="container">

            <!-- content -->
            <div class="row header-page">
                <div class="col-lg-2">
                    <div class="nb_total"><?php echo ($nbres>1) ? $nbres." véhicules" : $nbres." véhicule";?></div>
                </div>
                <div class="col-lg-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-external-link-square"></i>
                            Formulaire de recherche
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse <?=$filtre_fleche?>" href="#"></a>
                                <a class="btn btn-xs btn-link panel-refresh" href="#">
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </div>
                        </div>
                        <div class="panel-body" <?=$filtre?>>
	                    <form class="form-horizontal" role="form" action="vehicules_liste.php" method="get">
	                    	<div class="form-group">
                                    <label class="col-sm-4 control-label" for="form-field-1">Type</label>
	                            <div class="col-sm-8">
                                        <select name="type" id="type" class="form-control">
                                            <option value="">&nbsp;</option>
                                            <option value="Scooter" <?php if($type=="Scooter") echo "selected"; ?>>Scooter</option>
                                            <option value="autre" <?php if($type=="autre") echo "selected"; ?>>Autre</option>
                                        </select>
	                            </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label" for="form-field-1">Immatriculation</label>
	                            <div class="col-sm-8">
                                        <input type="text" name="immatriculation" placeholder="Immatriculation" id="form-field-1" class="form-control" value="<?php echo $immatriculation; ?>">
	                            </div>
                                </div>
	                        <div style="text-align:center;">
                                    <input type="submit" id="bt" class="btn btn-main" value="Rechercher">
	                        </div>
                            </form>
                        </div>
                    </div>                        
                </div>
                
                <div class="col-lg-5 btn-spe">
                    <p style="text-align:right">
	            	<?php
                            if($_SESSION["admin"]){
                                ?>
                                <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_vehicules&type=<?=$type?>&immatriculation=<?=$immatriculation?>">Exporter en CSV</a>
                                <?php
                            }
                        ?>
                        <a class="btn btn-dark-green" href="vehicules_fiche.php">Ajouter un véhicule</a>
                    </p>
                </div>
        </div>

        <div id="div_tab_resultat" class="table-responsive">
        	<table class="table table-bordered table-hover" id="sample-table-1">
	    		<thead>
	        		<th>Type</th>
	        		<th>Nom</th>
	        		<th>Immatriculation</th>
                    <th>Kilometrage</th>
	        		<th>Marque</th>
	        		<th>Volume</th>
	        		<th>Etat</th>
	        		<th style="width:185px">Actions</th>
	        	</thead>
	        	<tbody>
	        	</tbody>
	        </table>
        </div>
        <div style="text-align:right;">
        	<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
        </div>

        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body" style="text-align:center">
                        <input type="hidden" name="suppid" id="suppid" value="" />
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <div style="padding:10px">                                            
							<p><b>Etes-vous sûr de vouloir supprimer ce véhicule ?</b></p>
						</div>

						<button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Annuler
						</button>
						<button onclick="confirm_suppression('suppvehicule')" class="btn btn-default" data-dismiss="modal">
							Confirmer
						</button>
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

<script src="assets/plugins/select2/select2.min.js"></script>
<script src="assets/plugins/bootstrap-paginator/src/bootstrap-paginator.js"></script>            
<script language="javascript">
	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	};

	function tableau_resultat(p){
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_vehicule&type=<?=$type?>&immatriculation=<?=$immatriculation?>&p='+p,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
				$('#sample-table-1').find("tbody").html(transport);
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

	jQuery(document).ready(function() {
		runSelect2();
		tableau_resultat(1);
		runPaginator();		
	});
</script>
