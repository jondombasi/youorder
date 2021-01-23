<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		    {$id=$_GET["id"];}                  else{$id="";}
if(isset($_GET["aff_valide"]))  {$aff_valide=$_GET["aff_valide"];}  else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}         else{$action="";}

$menu       = "vehicule";
$sous_menu  = "liste";

$Vehicule   = new Vehicule($sql, $id);

$Vehicule->getPaginationPlanning(30, $id);
$nbpages    = $Vehicule->getNbPagesPlanning();
if ($nbpages=="" || $nbpages==null || $nbpages==0) {
	$nbpages=1;
}

if ($id!="") {
	$nom            = $Vehicule->getNom();
	$type           = $Vehicule->getType();
	$immatriculation= $Vehicule->getImmatriculation();
	$marque         = $Vehicule->getMarque();
	$volume         = $Vehicule->getVolume();
	$etat           = $Vehicule->getEtat();
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="assets/plugins/fullcalendar/fullcalendar/fullcalendar.css">

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1><?=$type." ".$immatriculation." - ".$marque." ".$volume."L"?></h1>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
		<!-- content -->
        <div class="row">       
        	<div class="col-sm-12">     
	            <div class="col-sm-5 col-sm-offset-6">
	                <p style="text-align:right">
		            	<?php
		                if($_SESSION["admin"]){
		                    ?>
		                    <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_vehicules_histo&id_vehicule=<?=$id?>">Exporter en CSV</a>
		                    <?php
		                }
		                ?>
	                </p>
	            </div>
            </div>
        </div>

    	<div id="div_tab_resultat" class="table-responsive col-sm-10 col-sm-offset-1">
        	<table class="table table-bordered table-hover" id="sample-table-1">
	    		<thead>
	        		<th>Date</th>
	        		<th>Actions</th>
	        		<th>Modifié par</th>
	        		<th>Etat</th>
	        		<th>Commentaire</th>
	        	</thead>
	        	<tbody>
	        	</tbody>
	        </table>
        </div>
        <div style="text-align:right;" class="col-sm-2 col-sm-offset-9">
	        <ul style="margin:0px;" id="paginator-example-1" class="pagination custom"></ul>
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
<script src="assets/plugins/fullcalendar/fullcalendar/fullcalendar.js"></script>
<script language="javascript">
	jQuery(document).ready(function() {
		tableau_resultat(1);
		runPaginator();
	});	

	function tableau_resultat(p){
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_vehicule_planning_histo&id_vehicule=<?=$id?>&p='+p,
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
</script>

