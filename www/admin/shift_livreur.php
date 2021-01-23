<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 25/06/2017
 * Time: 17:33
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

require_once("inc_connexion.php");
require_once("inc_header.php");

if(isset($_GET["id"]))		            {$id                    = $_GET["id"];}                 else{$id="";}
if(isset($_GET["id_livreur"]))          {$id_livreur            = $_GET["id_livreur"];}         else{$id_livreur    ="";}
if(isset($_GET["id_planning"]))         {$id_planning           = $_GET["id_planning"];}        else{$id_planning   ="";}
if(isset($_GET["p"]))                   {$page                  = $_GET["p"];}                  else{$page          =1;}
if(isset($_GET["commercant"]))		    {$commercant_txt        = $_GET["commercant"];}         else{$commercant_txt="";}
if(isset($_GET["statut"]))		        {$statut_txt            = $_GET["statut"];}             else{$statut_txt="";}
if(isset($_GET["periode"]))		        {$periode_txt           = $_GET["periode"];}            else{$periode_txt="";}
if(isset($_GET["aff_valide_livreur"]))	{$aff_valide_livreur    = $_GET["aff_valide_livreur"];} else{$aff_valide_livreur="";}
if(isset($_GET["aff_valide_planning"]))	{$aff_valide_planning   = $_GET["aff_valide_planning"];}else{$aff_valide_planning="";}
if(isset($_GET["tab_actif"]))		    {$tab_actif             = $_GET["tab_actif"];}          else{
    if(isset($_POST["tab_actif"]))  {$tab_actif = $_POST["tab_actif"];}else{$tab_actif="tab1";}
}
if(isset($_POST["action"]))		        {$action=$_POST["action"];}                             else{$action="";}

$Livreur    = new Livreur($sql, $id);

if ($id!="") {
    $nom        = $Livreur->getNom();
    $prenom     = $Livreur->getPrenom();
    $email      = $Livreur->getEmail();
    $password   = $Livreur->getPassword();
    $telephone  = $Livreur->getTelephone();
    $nbheures   = $Livreur->getNbHeures();
    $note       = $Livreur->getNote();
    $situation  = $Livreur->getSituation();
    $id_etat    = $Livreur->getEtat();

    if ($Livreur->getDateConnexion()!="" && $Livreur->getDateConnexion()!="1970-01-01 00:00:00") {
        $app="Dernière connexion le : ".date("d/m/Y \à H:i", strtotime($Livreur->getDateConnexion()));
    }
    else {
        $app="";
    }

    if ($Livreur->getPhoto()!="") {
        $source_photo="upload/livreurs/".$Livreur->getPhoto();
    }
}
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
                    <h1>L'activité de <?=$prenom." ".$nom?></h1>
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
                    <div class="nb_total"> <?=utf8_encode(strftime("%A %d %B %Y" ) )?></div>
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

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Planning théorique
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-11">
                                <thead>
                                    <th>Commerçant</th>
                                    <th>Horaires</th>
                                    <th>Vehicule</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>


                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Planning effectué
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-12">
                                <thead>
                                    <th>Commerçant</th>
                                    <th>Horaires</th>
                                    <th>Vehicule</th>
                                    <th>heure connexion</th>
                                    <th>heures effectuées</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Heures payéss et facturées
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-13">
                                <thead>
                                <th>Total des heures effectuées sur la journée</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            activités
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table">
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



        <div id="tab2" style="display:none">

            <div class="row header-page">

                <div class="col-lg-2">
                    <div class="nb_total"> <?=utf8_encode(strftime("%B %Y"))?></div>
                </div>

                <div class="col-lg-10 btn-spe">
                    <p style="text-align:right">
                        <a class="btn btn-dark-green" href="#">Mois précédent</a>
                        <a class="btn btn-dark-green" href="#">Choix du mois</a>
                        <a class="btn btn-dark-green" href="#">Mois suivant</a>
                    </p>
                </div>
            </div>

            <div class="row">

            <div class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="icon-align-left"></i>
                        Nb de shift
                        <div class="panel-tools">
                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                        </div>
                    </div>


                    <div id="div_tab_resultat" class="panel-body">
                        <table class="table table-bordered table-hover" id="sample-table-15">
                            <thead>
                            <th>Total des heures theoriques</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <a href="#myModal1" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="En savoir plus"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        <a class="btn btn-light-grey"  style="margin-top:0;" target="_blank" href="action_poo.php?action=export_month_presence">Exporter en CSV</a>
                    </div>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="icon-align-left"></i>
                        Nb de presence
                        <div class="panel-tools">
                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                        </div>
                    </div>


                    <div id="div_tab_resultat" class="panel-body">
                        <table class="table table-bordered table-hover" id="sample-table-14">
                            <thead>
                                <th>Total des heures effectuées</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <a href="#myModal2" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="En savoir plus"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        <a class="btn btn-light-grey"  style="margin-top:0;" target="_blank" href="action_poo.php?action=export_month_presence">Exporter en CSV</a>
                    </div>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="icon-align-left"></i>
                        Nb absence
                        <div class="panel-tools">
                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                        </div>
                    </div>


                    <div id="div_tab_resultat" class="panel-body">
                        <p><h4>Total des heures d'absence <span class="semaine_aff"></span> : <span id=""></span></h4></p>
                        <a href="#myModal3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="En savoir plus"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    </div>

                </div>
            </div>

                <div class="col-sm-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Nb heure ajouté
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <p><h4>Total des heures ajoutées <span class="semaine_aff"></span> : <span id=""></span></h4></p>
                            <a href="#myModal4" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="En savoir plus"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        </div>

                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Statistiques
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table">
                                <thead>
                                <p><h4>Nombres d'heures à prévu dans le mois<span class="semaine_aff"></span> : <span id=""></span></h4></p>
                                <p><h4>Total heures effectuées <span class="semaine_aff"></span> : <span id=""></span></h4></p>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- Start: Modal Tab2-->

            <!-- Start Modal1 : Tous les shifts pour un livreur sur un mois-->
        <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel">
                            Liste des shifts théoriques
                        </h3>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <div>
                            <div id="div_tab_resultat" class="panel-body">
                                <table class="table table-bordered table-hover" id="sample-table-1">
                                    <thead>
                                        <th>Date</th>
                                        <th>Commerçants</th>
                                        <th>horaire</th>
                                        <th>Vehicule</th>
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
            <!-- End Modal1 -->



            <!-- Start Modal2 : Toutes les mises en ligne pour un livreur sur un mois-->
        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel">
                            Liste des heures de connexion
                        </h3>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <div>
                            <div id="div_tab_resultat" class="panel-body">
                                <table class="table table-bordered table-hover" id="sample-table-2">
                                    <thead>
                                        <th>Date</th>
                                        <th>horaire</th>
                                        <th>Commerçants</th>
                                        <th>Vehicule</th>
                                        <th>Heure de connexion</th>
                                        <th>heure effectué</th>
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
            <!-- End Modal2 -->


            <!-- Start Modal3 : Toutes les abscense pour un livreur sur un mois-->
        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel">
                            Liste des Retards / Absences
                        </h3>
                    </div>
                    <!-- End Modal Header -->

                    <!-- Start: Modal CONTENT-->
                    <div class="modal-body" style="text-align:center">
                        <div>
                            <p><h4>Total des heures de retards / absence <span class="semaine_aff"></span> : <span id=""></span></h4></p>
                        </div>

                        <div class="row">

                            <div class="col-sm-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="icon-align-left"></i> Retards
                                        <div class="panel-tools">
                                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                                        </div>
                                    </div>

                                    <div id="div_tab_resultat" class="panel-body">
                                        <table class="table table-bordered table-hover" id="sample-table-5">
                                            <thead>
                                                <th>Date</th>
                                                <th>Commerçant + horaire</th>
                                                <th>Heure connexion</th>
                                                <th>Retard(en mn)</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>

                                 </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="icon-align-left"></i> Absences
                                        <div class="panel-tools">
                                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                                        </div>
                                    </div>

                                    <div id="div_tab_resultat" class="panel-body">
                                        <table class="table table-bordered table-hover" id="sample-table-5">
                                            <thead>
                                                <th>Date</th>
                                                <th>Commerçant + horaire</th>
                                                <th>Commentaires</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <!-- end: Modal CONTENT-->
        </div>
            <!-- End Modal3 -->


            <!-- Start Modal4 : Toutes les heures ajoutées pour un livreur sur un mois-->
        <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel">
                            Liste des heures ajoutées
                        </h3>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <div>
                            <p><h4>Total des heures ajoutées <span class="semaine_aff"></span> : <span id=""></span></h4></p>
                            <div id="div_tab_resultat" class="panel-body">
                                <table class="table table-bordered table-hover" id="sample-table-13">
                                    <thead></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end: PAGE CONTENT-->
        </div>
            <!-- End Modal4 -->

        <!-- End: Modal Tab2-->

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
<script src="assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/fullcalendar/fullcalendar/fullcalendar.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script language="javascript">
    jQuery(document).ready(function() {
        runSelect2();
        tableau_resultat1(1);
        tableau_resultat2(1);

        tableau_resultat11(1);
        tableau_resultat12(1);
        tableau_resultat13(1);

        tableau_resultat14(1);
        tableau_resultat15(1);

        tableau_resultat17(1);
        tableau_resultat18(1);

    });


    $('.date-time-range').daterangepicker({
        timePicker: true,
        timePickerIncrement: 5,
        firstDay: 1,
        format: 'DD-MM-YYYY hh:mm A'
    });

    $("select.search-select").select2({
        placeholder: "Select a State",
        allowClear: true
    });

    $('.date-picker').datepicker({
        autoclose: true,
        weekStart: 1
    });


    show_div('<?=$tab_actif?>');
    // START -> Tableau MODAL
    function tableau_resultat1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=getLivreurShift&id_livreur=<?=$id?>&p='+p,
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
            data	   : 'action=get_presence&id_livreur=<?=$id?>&p='+p,
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
    // END -> Tableau MODAL


    function tableau_resultat11(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=getLivreurShiftDay&id_livreur=<?=$id?>&p='+p,
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

    function tableau_resultat12(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=get_presence_day&id_livreur=<?=$id?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-12').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function tableau_resultat13(p){
        //compter le nb d'heures de présence
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=count_presence_day&id_livreur=<?=$id?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-13').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }


    function tableau_resultat14(p){
        //compter le nb d'heures de présence
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=count_presence_month&id_livreur=<?=$id?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-14').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function tableau_resultat15(p){
        //compter le nb d'heures de présence
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=count_theorique_month&id_livreur=<?=$id?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-15').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function tableau_resultat16(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=getLivreurShiftDay&id_livreur=<?=$id?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-16').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function tableau_resultat17(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=get_presence_day&id_livreur=<?=$id?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-17').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function runSelect2() {
        $(".search-select").select2({
            placeholder: "Select a State",
            allowClear: true
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


</script>