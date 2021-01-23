<?php
$menu = "resto";
$sous_menu = "liste";
require_once("inc_header.php");
if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
if(isset($_GET["etat"]))	{$etat=$_GET["etat"];}else{$etat="";}
if(isset($_GET["dept"]))	{$dept=$_GET["dept"];}else{$dept="";}
if(isset($_GET["ret"]))		{$ret=$_GET["ret"];}else{$ret="";}


if ($nom=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}

$Commercant = new Commercant($sql);
$Commercant->getPagination(30, $nom);
$nbpages    = $Commercant->getNbPages();
$nbres      = $Commercant->getNbRes();

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
            
            <!-- header -->
<!--            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header">
                        <h1>Liste des commerçants</h1>
                    </div>
                </div>
            </div> -->
            <!-- /header -->
                
            <!-- start: PAGE CONTENT -->
            <div class="row header-page">
                <?php
                    switch($ret){
                        case "restosup":
                            echo '<div class="col-sm-12"><div class="alert alert-success">
                                <button class="close" data-dismiss="alert">
                                ×
                                </button>
                                <i class="fa fa-check-circle"></i>
                                Le commerçant a été supprimé
                                </div></div>';                    
                        break;
                    }
                ?>
                
                <div class="col-lg-2">
                    <div class="nb_total"><?php echo ($nbres>1) ? $nbres." commerçants" : $nbres." commerçant";?></div>
                </div>
                
                <?php if($_SESSION["admin"]) { ?>
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
                                <form class="form-horizontal" role="form" action="restaurants_liste.php" method="get">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="form-field-1">Nom</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="nom" placeholder="Nom" id="form-field-1" class="form-control" value="<?php echo $nom; ?>">
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
                            <a href="#myModal2" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Livreur archivé">Commerçants Archivés</a>
                            <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_restos&nom=<?=$nom?>">Exporter en CSV</a>
                            <a class="btn btn-dark-green" href="restaurants_fiche.php">Ajouter un commerçant</a>
                        </p>
                    </div>
                <?php } ?>
            </div>
                
        <div id="div_tab_resultat" class="table-responsive" <?php if ($_SESSION["restaurateur"]) echo "style='margin-top:15px'";?>></div>

        <div style="text-align:right;">
        	<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
        </div>

        <!-- MODAL -->

        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel">
                            Liste des commerçant supprimés
                        </h3>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <div>

                            <div id="div_tab_resultat" class="panel-body">
                                <table class="table table-bordered table-hover" id="sample-table-2">
                                    <thead>
                                    <th>Libellé</th>
                                    <th>Actions</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end: PAGE CONTENT-->
        </div>


		<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">Supprimer un commerçant</h4>
					</div>
					<div class="modal-body">
                        <input type="hidden" name="suppid" id="suppid" value="" />                                                
						<p>
							Etes-vous sûr de vouloir supprimer ce commerçant ?
						</p>
					</div>
					<div class="modal-footer">
						<button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Annuler
						</button>
						<button onclick="confirm_suppression('suppresto')" class="btn btn-default" data-dismiss="modal">
							Confirmer
						</button>
					</div>
				</div>
			</div>
		</div>

        <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body" style="text-align:center">
                        <input type="hidden" name="suppid" id="suppid" value="" />
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <div style="padding:10px">
                            <p><b>Etes-vous sûr de vouloir reintégrer ce commerçant ?</b></p>
                        </div>

                        <button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
                            Annuler
                        </button>
                        <button onclick="confirm_suppression('recupresto')" class="btn btn-default" data-dismiss="modal">
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
		  	data	   : 'action=liste_restos&nom=<?=$nom?>&p='+p,
		  	type	   : "GET",
		  	cache    : false,
			headers: {'Expect': ''},		  
		  	success  : function(transport) {  
				document.getElementById('div_tab_resultat').innerHTML = transport;
				if ($(".tooltips").length) {
					$('.tooltips').tooltip();
				}
			}
		});					
	}

    function tableau_resultat2(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=resto_supprime&nom=<?=$nom?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-2').find("tbody").html(transport);
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
                //$('#paginator-content-1').text("Page item clicked, type: " + type + " page: " + page);
            }
        });
    }



	jQuery(document).ready(function() {
		runSelect2();
		tableau_resultat(1);
        tableau_resultat2(1);
		runPaginator();
	});
</script>
