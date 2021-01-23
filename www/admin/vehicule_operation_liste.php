<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 26/02/2017
 * Time: 22:07
 */

$menu = "vehicule";
$sous_menu = "operation-liste";
require_once("inc_header.php");
if(isset($_GET["id_operation"]))    {$page=$_GET["id_operation"];}                  else{$page=1;}
if(isset($_GET["immatriculation"]))	{$immatriculation=$_GET["immatriculation"];}    else{$immatriculation="";}

if ($immatriculation=="") {
    $filtre='style="display:none;"';
    $filtre_fleche="expand";
}
else {
    $filtre_fleche="collapses";
}

$Operation  = new Operation($sql);
$Operation->getPagination(30, $commentaire);
$nbpages    =$Operation->getNbPages();
$nbres      =$Operation->getNbRes();

?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
    <div class="container">

        <!-- content -->
        <div class="row header-page">
            <div class="col-lg-2">
                <div class="nb_total"><?php echo ($nbres>1) ? $nbres." Operations" : $nbres." operation-liste";?></div>
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
                        <form class="form-horizontal" role="form" action="vehicule_operation_liste.php" method="get">

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="form-field-1">Véhicule</label>
                                <div class="col-sm-9 form-res">
                                    <select name="restaurant" id="restaurant" class="form-control search-select">
                                        <option value="">&nbsp;</option>
                                        <?php
                                        $result = $sql->query("SELECT * FROM vehicules v WHERE 1 ".$_SESSION["req_resto"]." and v.etat = 'ok' ORDER BY immatriculation");	// WHERE etat!='6'
                                        while($ligne = $result->fetch()) {
                                            if($vehicule==$ligne["id"]){$sel = 'selected="selected"';}else{$sel = "";}
                                            echo '<option value="'.$ligne["id"].'" '.$sel.'>'.$ligne["nom"].'</option>';
                                        }
                                        ?>
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
                    <a class="btn btn-dark-green" href="vehicule_operation_fiche.php">Effectuer une Opération</a>
                    <?php
                    if($_SESSION["admin"]){
                        ?>
                        <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_operation&type=<?=$type?>&immatriculation=<?=$immatriculation?>">Exporter en CSV</a>
                        <?php
                    }
                    ?>
                </p>
            </div>
        </div>
        <div class="row">
        </div>
        <div id="div_tab_resultat" class="table-responsive">
            <table class="table table-bordered table-hover" id="sample-table-1">
                <thead>
                <th>Date</th>
                <th>Modifié par</th>
                <th>Véhicule</th>
                <th>Kilometrage</th>
                <th>Actions sur le vehicules</th>
                <th>Piece</th>
                <th>Commentaire</th>
                <th style="width:100px"></th>
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
                            <p><b>Etes-vous sûr de vouloir supprimer cette opération ?</b></p>
                        </div>

                        <button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
                            Annuler
                        </button>
                        <button onclick="confirm_suppression('suppoperation')" class="btn btn-default" data-dismiss="modal">
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
            data	   : 'action=liste_operation&p='+p,
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
