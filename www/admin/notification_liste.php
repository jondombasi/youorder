<?php
$menu = "notif";
$sous_menu = "liste";
require_once("inc_header.php");

if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
if(isset($_GET["date"]))	{$date=$_GET["date"];}else{$date="";}

$req_sup="";
if ($nom!="") {
	$req = "SELECT * FROM livreurs WHERE nom LIKE'%".$nom."%'";
	$result = $sql->query($req);
	$ligne = $result->fetch();
	if($ligne!=""){
    	$req_sup.=" AND destinataire LIKE '%".$ligne['id'].",%'";
    }
}
if ($date!="") {
    $req_sup.=" AND date_envoi BETWEEN ".$sql->quote(date("Y-m-d", strtotime($date))." 00:00:00")." AND ".$sql->quote(date("Y-m-d", strtotime($date))." 23:59:59");
}

$req = "SELECT count(*) as NB FROM notifications_push WHERE 1 ".$req_sup;
$result = $sql->query($req);
$ligne = $result->fetch();
if($ligne!=""){
    $nbres = $ligne["NB"];
    $nbpages = $nbres/30;
	$nbpages = ceil($nbpages);
	if ($nbpages==0) $nbpages=1;
}else{
    $nbres = 0;
    $nbpages = 1;
}

if ($nom=="" && $date=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}
?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<!-- start: PAGE -->
<div class="main-content">
    <div class="container">

        <!-- content -->

        <div class="row header-page">
            <div class="col-lg-2"><div class="nb_total">Total : <?php echo ($nbres>1) ? $nbres." notifications" : $nbres." notification";?></div></div>

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
                        <form class="form-horizontal" role="form" action="notification_liste.php" method="get">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="form-field-1">Nom</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nom" placeholder="Nom" id="form-field-1" class="form-control" value="<?php echo $nom; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="form-field-1">Date</label>
                                <div class="col-sm-9">
                                    <input type="text" name="date" id="date" data-date-format="dd-mm-yyyy" value="<?php echo $date ?>" data-date-viewmode="years" data-week-start="1" class="form-control date-picker">
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
                            <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_notif_push&nom=<?=$nom?>&date=<?=$date?>">Exporter en CSV</a>
                            <?php
                        }
                    ?>
                    <a class="btn btn-dark-green" href="notification_fiche.php">Créer une notification</a>
                </p>
            </div>
        </div>
          
        <div id="div_tab_resultat" class="table-responsive">
        	<table class="table table-bordered table-hover" id="sample-table-1">
	    		<thead>
	        		<th>Nom de la notification</th>
	        		<th>Date de création</th>
	        		<th>Date d'envoi</th>
	        		<th>Nombre d'envoi</th>
	        		<th>Etat</th>
	        		<th style="width:100px">Actions</th>
	        	</thead>
	        	<tbody>
	        	</tbody>
	        </table>
        </div>
        <div style="text-align:right;">
        	<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
        </div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body" style="text-align:center">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <div style="padding:10px">                                            
							<p id="txt_notification_push"></p>
						</div>

						<button aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Fermer
						</button>
					</div>
				</div>
			</div>
		</div> 

        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body" style="text-align:center">
                        <input type="hidden" name="suppid" id="suppid" value="" />
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <div style="padding:10px">                                            
							<p><b>Etes-vous sûr de vouloir annuler cette notification ?</b></p>
						</div>

						<button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Annuler
						</button>
						<button onclick="confirm_suppression('suppnotif')" class="btn btn-default" data-dismiss="modal">
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
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>           
<script language="javascript">
	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	};

	function runDatePicker() {
        $('.date-picker').datepicker({
            autoclose: true,
            weekStart: 1
        });
    };

	function tableau_resultat(p){
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_notifications_push&p='+p+'&nom=<?=$nom?>&date=<?=$date?>',
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
		runDatePicker();
	});
</script>
