<?php
$menu = "commande";
$sous_menu = "histo";
require_once("inc_header.php");
if(isset($_GET["statut"]))		{$statut=$_GET["statut"];}else{$statut="";}
if(isset($_GET["restaurant"]))	{$restaurant=$_GET["restaurant"];}else{$restaurant="";}

if ($statut=="" && $restaurant=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}

$Commercant = new Commercant($sql);
$Commande = new Commande($sql);
$Commande->getPagination(30, "", $restaurant, $statut, "", 1);
$nbpages=$Commande->getNbPages();
$nbres=$Commande->getNbRes();
if ($nbpages==0) {
	$nbpages++;
}
?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">

<!-- start: PAGE -->
<div class="main-content">
    <div class="container">

        <!-- start: PAGE CONTENT -->
        <div class="row header-page">
            <div class="col-lg-2">
                <div class="nb_total"><?php echo ($nbres>1) ? $nbres." commandes" : $nbres." commande";?></div>
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
                        <form class="form-horizontal" role="form" action="commandes_histo.php" method="get">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="form-field-1">Commerçant</label>
                                <div class="col-sm-9">
                                    <select name="restaurant" id="restaurant" class="form-control search-select">
                                        <option value="">&nbsp;</option>
                                        <?php 
                                            foreach ($Commercant->getAll("", "") as $commercant) {
                                                $sel=($restaurant==$commercant->id) ? "selected" : "";
                                                echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="operation">Statut</label>
                                <div class="col-sm-9">
                                    <select name="statut" id="statut" class="form-control">
                                        <option value="">&nbsp;</option>
                                        <option <?php if($statut=="signé"){echo 'selected="selected"';} ?> value="signé">Signée</option>
                                        <option <?php if($statut=="echec"){echo 'selected="selected"';} ?> value="echec">Echec</option>
                                    </select>
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
                            <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_commandes&histo=1&restaurant=<?=$restaurant?>&statut=<?=$statut?>">Exporter en CSV</a>
                            <?php
                        }
                    ?>
                    <a class="btn btn-dark-green" href="commandes_fiche.php">Ajouter une commande</a>
                </p>
            </div>
        </div>

        <div id="div_tab_resultat" class="table-responsive">
        	<table class="table table-bordered table-hover" id="tableau_commandes">
	    		<thead>
	        		<th>Commerçant</th>
	        		<th>Infos livreur</th>
	        		<th>Client</th>
	        		<th>Créneau de livraison</th>
	        		<th>Infos</th>
	        		<th>Statut</th>
	        		<th style="width:50px">Actions</th>
	        	</thead>
	        	<tbody>
	        	</tbody>
	        </table>
        </div>

        <div style="text-align:right;">
       		<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
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
		  	data	   : 'action=liste_commande&histo=1&restaurant=<?=$restaurant?>&statut=<?=$statut?>&p='+p,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
				$("#div_tab_resultat").find("tbody").html(transport);
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
