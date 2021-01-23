<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 16/02/2017
 * Time: 00:56
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

$menu       = "materiel";
$sous_menu  = "liste";

if(isset($_GET["code"]))		{$code      = $_GET["code"];}   else{$code="";}
if(isset($_GET["libelle"]))		{$libelle   = $_GET["libelle"];}else{$libelle="";}

if ($code=="" && $libelle=="") {
    $filtre         = 'style="display:none;"';
    $filtre_fleche  = "expand";
}
else {
    $filtre_fleche="collapses";
}

$Materiel   = new Materiel($sql);
$Materiel->getPagination(30, $code, $libelle);
$nbpages    =$Materiel->getNbPages();
$nbres      =$Materiel->getNbRes();

require_once("inc_header.php");

?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
	<div class="container">

            <!-- content -->
            <div class="row header-page">
                <div class="col-lg-2">
                    <div class="nb_total"><?php echo ($nbres>1) ? $nbres." pièces" : $nbres." materiel";?></div>
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
                            <form class="form-horizontal" role="form" action="piece_liste.php" method="get">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="form-field-1">Libelle</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="libelle" placeholder="Libelle" id="form-field-1" class="form-control" value="<?php echo $libelle; ?>">
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
                        <a class="btn btn-dark-green" href="piece_fiche.php">Ajouter une Pièce</a>
                    </p>
                </div>
            </div>

<div id="div_tab_resultat" class="table-responsive">
    <table class="table table-bordered table-hover" id="sample-table-1">
        <thead>
        <th>Code</th>
        <th>Libelle</th>
        <th>Quantité</th>
        <th>Prix HT</th>
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
                    <p><b>Etes-vous sûr de vouloir supprimer cette piece ?</b></p>
                </div>

                <button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
                    Annuler
                </button>
                <button onclick="confirm_suppression('suppmateriel')" class="btn btn-default" data-dismiss="modal">
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
            data	   : 'action=liste_materiel&libelle=<?=$libelle?>&code=<?=$code?>&p='+p,
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