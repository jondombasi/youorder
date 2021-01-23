<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 25/06/2017
 * Time: 17:33
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);


require_once("inc_header.php");

if(isset($_GET["id"]))		        {$id                = $_GET["id"];}             else{$id="";}
if(isset($_GET["id_livreur"]))      {$id_livreur        = $_GET["id_livreur"];}     else{$id_livreur    ="";}
if(isset($_GET["id_vehicule"]))     {$id_vehicule       = $_GET["id_vehicule"];}    else{$id_vehicule   ="";}
if(isset($_GET["id_planning"]))     {$id_planning       = $_GET["id_planning"];}    else{$id_planning="";}
if(isset($_GET["commercant"]))		{$commercant_txt    = $_GET["commercant"];}     else{$commercant_txt="";}

$Commercant     = new Commercant($sql, $id);
$Livreur        = new Livreur($sql);

$Commercant->getPagination(30, "");
$nbpages        = $Commercant->getNbPages();
$nbres          = $Commercant->getNbRes();

$fiche_planning = $Livreur->getShiftByCommercant($id);
$nom            = $Commercant->getNom();
?>

<style>
    .tab_btn {
        border:1px solid #9fc752 ;
        padding:10px;
        text-align:center;
        cursor:pointer;
    }

    .btn_actif {
        background-color:#9fc752;
        color:white;
    }

    #tab1, #tab2{
        margin-top:15px;
    }

    #info_calendar {
        position: absolute;
        z-index:500;
    }

    .triangle-border {
        position:relative;
        padding:15px;
        margin:1em 0 3em;
        border:1px solid #000;
        color:#333;
        background:#fff;
    }

    .triangle-border:before {
        content:"";
        position:absolute;
        bottom:-14px; /* value = - border-top-width - border-bottom-width */
        left:47px; /* controls horizontal position */
        border-width:13px 13px 0;
        border-style:solid;
        border-color:#000 transparent;
        /* reduce the damage in FF3.0 */
        display:block;
        width:0;
    }

    /* creates the smaller  triangle */
    .triangle-border:after {
        content:"";
        position:absolute;
        bottom:-13px; /* value = - border-top-width - border-bottom-width */
        left:47px; /* value = (:before left) + (:before border-left) - (:after border-left) */
        border-width:13px 13px 0;
        border-style:solid;
        border-color:#fff transparent;
        /* reduce the damage in FF3.0 */
        display:block;
        width:0;
    }

    .triangle-border2:before, .triangle-border2:after {
        left:150px; /* controls horizontal position */
    }

    #tooltip_table th, #tooltip_table td {
        padding:5px 10px;
    }

    .has-error2 {
        border: 1px solid #a94442 !important;
    }

    .tooltips {
        z-index:9999999;
    }

    .tooltip-inner {
        white-space: pre-wrap;
        max-width: 500px;
        width:220px;
        z-index:9999999;
    }

    @media(max-width:600px){
        .liste-livreur span{
            display:block;
            margin-bottom: 10px;
        }
    }

    .select2-container .select2-choice .select2-arrow b{background: none !important;}

    #myModal3{
    }

    .change_planning {
        height:50px;
        line-height: 40px;
        padding-left: 0px;
    }

    .change_planning2 {
        height:40px;
        line-height: 40px;
        padding-left: 0px;
    }

    .select2-container .select2-choice {
        margin-top: 0px !important;
    }

    .has-error {
        border-color: #B94A48 !important;
    }

</style>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h1>Detail des shift pour <?= $nom ?></h1>
                </div>
                <!-- end: PAGE TITLE & BREADCRUMB -->
            </div>
        </div>
        <!-- content -->


        <!-- start: PAGE CONTENT -->
        <div class="row">
            <div class="col-sm-12 liste-livreur">
                <span class="tab_btn btn_actif col-sm-2 col-sm-offset-4"    id="tab1_btn" onclick="show_div( 'tab1' );">Shif Journalier</span>
                <span class="tab_btn col-sm-2"                              id="tab2_btn" onclick="show_div( 'tab2' );">Récapitulatif du mois</span>
            </div>
        </div>




        <div id="tab1">
            <div class="row header-page">
                <div class="col-lg-2">
                    <div class="nb_total"> <?=utf8_encode(strftime("%A %d %B %Y"))?></div>
                </div>

                <div class="col-lg-10 btn-spe">
                    <p style="text-align:right">
                        <a class="btn btn-dark-green" href="#"> Jour précédent</a>
                        <a class="btn btn-dark-green" href="#"> Choix du jour </a>
                        <a class="btn btn-dark-green" href="#"> Jour suivant </a>
                    </p>
                </div>
            </div>


            <div class="row">

                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Tableau des shifts journaliers
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-1">
                                <thead>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Nombre d'heures prévues
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-2">
                                <thead>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Nombre d'heures effectuées
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-2">
                                <thead>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Nombre de livreurs
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-3">
                                <thead>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Nombre de shifts
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-4">
                                <thead>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Nombre de commande
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-5">
                                <thead>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <div id="tab2">
            <div class="row header-page">
                <div class="col-lg-2">
                    <div class="nb_total"> <?=utf8_encode(strftime("%B %Y"))?></div>
                </div>

                <div class="col-lg-10 btn-spe">
                    <p style="text-align:right">
                        <a class="btn btn-dark-green" href="#"> Mois précédent</a>
                        <a class="btn btn-dark-green" href="#"> Mois suivant </a>
                    </p>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Récapitualtif des shifts
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-11">
                                <thead>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

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
    jQuery(document).ready(function() {
        runSelect2();
        tableau_resultat(1);
        tableau_resultat1(1);
        runPaginator();
    });


    function runSelect2() {
        $(".search-select").select2({
            placeholder: "Select a State",
            allowClear: true
        });
    };

    function tableau_resultat(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=get_shift_commerçant&id_commercant=<?=$id?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-11').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function tableau_resultat1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=get_day_commerçant&id_commercant=<?=$id?>&p='+p,
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

    function show_div(div_to_show) {
        $("#info_calendar").hide();
        $("#tab1, #tab2").hide();
        $(".tab_btn").each(function() {
            $(this).removeClass('btn_actif');
        });
        $("#"+div_to_show).show();
        $("#"+div_to_show+"_btn").addClass('btn_actif');

        if (div_to_show=="tab2") {
            $('#calendar_theorique').fullCalendar('render');
        }
    }

    function switch_view(div, type) {
        $("#info_calendar").hide();

        $("."+div).each(function() {
            $(this).hide();
        })

        $("."+div+"_btn").each(function() {
            $(this).removeClass("active");
        })

        $("#"+div+"_"+type).toggle();
        $("#"+div+"_"+type+"_btn").addClass("active");
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