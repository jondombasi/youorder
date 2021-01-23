<?php
$menu       = "livreur";
$sous_menu  = "liste";

require_once("inc_header.php");

if(isset($_GET["nom"]))		{$nom   = $_GET["nom"];}        else{$nom="";}
if(isset($_GET["statut"]))	{$statut= $_GET["statut"];}  else{$statut="";}
if(isset($_GET["numero"]))	{$numero= $_GET["numero"];}  else{$numero="";}

if ($nom=="" && $statut=="" && $numero=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}

$Vehicule   = new Vehicule($sql);
$Commercant = new Commercant($sql);
$Livreur    = new Livreur($sql);

$Livreur->getPagination(30, $nom, $statut, $numero);
$nbpages    = $Livreur->getNbPages();
$nbres      = $Livreur->getNbRes();

if ($nbpages==0) {
	$nbpages++;
}
?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
    <div class="container">
        
        <!-- content -->
        <div class="row header-page">
            <div class="col-lg-2">
                <div class="nb_total"><?php echo ($nbres>1) ? $nbres." livreurs" : $nbres." livreur";?></div>
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
                        <form class="form-horizontal" role="form" action="livreurs_liste.php" method="get">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="form-field-1">Nom</label>
                                <div class="col-sm-9">
                                    <input type="text" name="nom" placeholder="Nom" id="form-field-1" class="form-control" value="<?php echo $nom; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="statut" id="statut" value="<?=$statut?>"/>
                                <label class="col-sm-2 control-label" for="form-field-1">Statut</label>
                                <div class="col-sm-9">
                                    <div class="btn-group">
                                        <a class="btn btn-default <?php if ($statut=="ON") echo 'active'?>" href="javascript:void(0);">ON</a>
                                        <a class="btn btn-default <?php if ($statut=="OFF") echo 'active'?>" href="javascript:void(0);">OFF</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="form-field-1">Numero</label>
                                <div class="col-sm-9">
                                      <input type="text" name="numero" placeholder="Numero" id="form-field-1" class="form-control" value="<?php echo $numero; ?>">
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
                    <a href="#myModal2" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Livreur archivé">Livreurs Archivés</a>
                    <?php
                        if($_SESSION["admin"]){
                            ?>
                            <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_livreurs&nom=<?=$nom?>&statut=<?=$statut?>&numero=<?=$numero?>">Exporter en CSV</a>
                            <?php
                        }
                    ?>
                    <a class="btn btn-dark-green" href="livreurs_fiche.php">Ajouter un livreur</a>
                    </p>
            </div>
        </div>

        <div id="div_tab_resultat" class="table-responsive">
        	<table class="table table-bordered table-hover" id="sample-table-1">
	    		<thead>
	        		<th>Nom</th>
	        		<th>Prenom</th>
	        		<th>Numéro</th>
	        		<th style="width:200px">Nombre d'heures par semaine</th>
	        		<th style="width:100px">Statut</th>
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

        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel">
                            Liste des livreurs licencié
                        </h3>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <div>

                            <div id="div_tab_resultat" class="panel-body">
                                <table class="table table-bordered table-hover" id="sample-table-2">
                                    <thead>
                                    <th>Livreur</th>
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
					<div class="modal-body" style="text-align:center">
                        <input type="hidden" name="suppid" id="suppid" value="" />
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <div style="padding:10px">                                            
							<p><b>Etes-vous sûr de vouloir supprimer ce livreur ?</b></p>
						</div>

						<button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Annuler
						</button>
						<button onclick="confirm_suppression('supplivreur')" class="btn btn-default" data-dismiss="modal">
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
                            <p><b>Etes-vous sûr de vouloir reintégrer ce livreur ?</b></p>
                        </div>

                        <button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
                            Annuler
                        </button>
                        <button onclick="confirm_suppression('recuplivreur')" class="btn btn-default" data-dismiss="modal">
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
            data	   : 'action=liste_livreur&nom=<?=$nom?>&statut=<?=$statut?>&numero=<?=$numero?>&p='+p,
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

    function tableau_resultat2(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=livreur_licencie&nom=<?=$nom?>&statut=<?=$statut?>&numero=<?=$numero?>&p='+p,
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
			}
		});
	}

	$(".btn-group").find(".btn-default").click(function() {
		$(".btn-group").find(".btn-default").each(function() {
			$(this).removeClass("active");
		})
		//on efface si on rappuye sur le même bouton
		if ($.trim($(this).text())==$("#statut").val()) {
			$("#statut").val("");
		}
		else {
			$(this).addClass("active");
			$("#statut").val($.trim($(this).text()));
		}
	})

	jQuery(document).ready(function() {
		runSelect2();
		tableau_resultat(1);
        tableau_resultat2(1);
		runPaginator();		
	});
</script>
