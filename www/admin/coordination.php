<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);
require_once("inc_connexion.php");

if(isset($_GET["id_p1"]))       {$id_p1         = $_GET["id_p1"];}          else{$id_p1="";}
if(isset($_GET["id_livreur1"])) {$id_livreur1   = $_GET["id_livreur1"];}    else{$id_livreur1="";}
if(isset($_GET["id_livreur2"])) {$id_livreur2   = $_GET["id_livreur2"];}    else{$id_livreur2="";}

if(isset($_GET["id_adminL"]))   {$id_adminL     = $_GET["id_adminL"];}      else{$id_adminL="";}
if(isset($_GET["action"]))      {$action        = $_GET["action"];}         else{$action="";}
if(isset($_GET["id_livreur"]))  {$id_livreur    = $_GET["id_livreur"];}     else{$id_livreur="";}
if(isset($_GET["id"]))          {$id            = urldecode($_GET["id"]);}  else{$id="";}

$menu       = "live";
$sous_menu  = "coordination";
$aff_erreur = "";
$continu    = true;

$Livreur    = new Livreur($sql);


if ($nbpages==0) {
    $nbpages++;
}
$fiche_planning=$Livreur->getPlanningFiche($id);

if ($action == "replace"){
    if ($Livreur->replacePlanning($id_p1, $id_livreur1, $id_livreur2, $_SESSION['userid']) == 1)
        header("location: coordination.php");
    else
        header("location: coordination.php?error=true");

}

if ($action == "setLivreurOnline"){
    if(isset($_GET["id_planning"]))     {$id_planning  = $_GET["id_planning"];}    else{$id_planning="";}
    if(isset($_GET["id_livreur"]))      {$id_livreur   = $_GET["id_livreur"];}     else{$id_liveur="";}
    if(isset($_GET["id_commercant"]))   {$id_commercant= $_GET["id_commercant"];}  else{$id_commercant="";}
    if(isset($_GET["id_vehicule"]))     {$id_vehicule  = $_GET["id_vehicule"];}    else{$id_vehicule="";}

    if ($Livreur->setLivreurOnline($id_planning, $id_livreur, $id_commercant, $id_vehicule, new DateTime("now"), new DateTime("now"), "appli") == "ok")
        header("location: coordination.php");
    else
        header("location: coordination.php?error=true");
}

if ($action == "setLivreurAttente"){
    if(isset($_GET["id_livreur"]))      {$id_livreur   = $_GET["id_livreur"];}    else{$id_liveur="";}

    if ($Livreur->setLivreurAttente($id_livreur) == "ok")
        header("location: coordination.php");
    else
        header("location: coordination.php?error=true");
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css">
<link rel="stylesheet" href="assets/plugins/fullcalendar/fullcalendar/fullcalendar.css">
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

    #tab1, #tab2, #tab3, #tab4 {
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

    @media(max-width:767px){
        .form-group-r p{margin-top:15px;}
        #calendar_theorique_list{margin-top:50px;}
        #calendar_theorique_list .table.table-bordered.table-hover{margin-top: 0 !important;}
        #calendar_presence_list{margin-top:50px;}
        #calendar_presence_list .table.table-bordered.table-hover{margin-top:0 !important;}
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

    .change_planning p, .change_planning2 p {
        margin: 0px;
    }

    .select2-container .select2-choice {
        margin-top: 0px !important;
    }

    .has-error {
        border-color: #B94A48 !important;
    }

</style>





<!-- start: PAGE -->
<link rel="stylesheet" type="text/css" href="assets/css/magnific-popup.css">
<div style="display:none;">
    <a class="pop-up-generique" href=""></a>
</div>
<div class="main-content">
    <div class="container">
        <!-- start: PAGE HEADER -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h1>La Coordination</h1>
                </div>
                <!-- end: PAGE TITLE & BREADCRUMB -->
            </div>
        </div>
        <!-- end: PAGE HEADER -->
        <!-- start: PAGE CONTENT -->
        <div class="row">
            <div class="col-sm-12 liste-livreur">
                <span class="tab_btn col-sm-2 col-sm-offset-2" id="tab1_btn" onclick="show_div('tab1');">Shift Avant 14h</span>
                <span class="tab_btn col-sm-2" id="tab2_btn" onclick="show_div('tab2');">Details de l'activité</span>
                <span class="tab_btn col-sm-2" id="tab3_btn" onclick="show_div('tab3');">Shift Après 14h</span>
                <span class="tab_btn col-sm-2" id="tab4_btn" onclick="show_div('tab4');">Details de l'activité</span>
            </div>
        </div>

        <div id="tab1">
            <div class="row">

                <div class="col-sm-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Liste des livreurs
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" ></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body"  <?php if ($_SESSION["restaurateur"]) echo "style='margin-top:15px'";?>>
                            <table class="table table-bordered table-hover" id="sample-table-1">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <?php if($_SESSION["admin"]) { ?>
                <div class="col-sm-5">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="icon-align-left"></i>
                               Livreur en Dispo
                                <div class="panel-tools">
                                    <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                                </div>
                            </div>

                            <div id="div_tab_resultat" class="panel-body">
                                <table class="table table-bordered table-hover" id="sample-table-3">
                                    <thead></thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                <?php } ?>

                <div class="col-sm-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Livreurs en Attente
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-5">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!--END TAB 1-->

        <div id="tab2" style="display:none">

            <div class="row">

                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Action - Changement
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body">
                            <?php if($_SESSION["admin"]) { ?> <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_activite_one">Exporter en CSV</a> <?php } ?>
                            <table class="table table-bordered table-hover" id="sample-table-10">
                                <thead></thead>
                                <tbody></tbody>
                            </table>

                        </div>

                    </div>
                </div>

                <?php if($_SESSION["admin"]) { ?>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Attribution du véhicule
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_attrVehicule_one">Exporter en CSV</a>
                            <table class="table table-bordered table-hover" id="sample-table-12">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <?php } ?>


                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Livreurs connectés
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <?php if($_SESSION["admin"]) { ?> <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_connectLivreur_one">Exporter en CSV</a><?php } ?>
                            <table class="table table-bordered table-hover" id="sample-table-14">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Mise en Attente
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body">
                            <?php if($_SESSION["admin"]) { ?> <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_activite_one">Exporter en CSV</a> <?php } ?>
                            <table class="table table-bordered table-hover" id="sample-table-10">
                                <thead>
                                    <th>Heure</th>
                                    <th>Livreur</th>
                                    <th>User</th>
                                    <th>Commentaire</th>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>

                    </div>
                </div>

            </div>

        </div>
        <!--END TAB 2-->


        <div id="tab3" style="display:none">

            <div class="row">

                <div class="col-sm-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Liste des livreurs
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-2">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <?php if($_SESSION["admin"]) { ?>
                <div class="col-sm-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Livreur en Dispo
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-4">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <?php } ?>

                <div class="col-sm-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Livreur en Attente
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-6">
                                <thead></thead>
                                <tbody> </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </div>
        <!--END TAB 3-->


        <div id="tab4" style="display:none">
            <div class="row">

                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Action - Changement
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body">
                            <?php if($_SESSION["admin"]) { ?> <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_activite_two">Exporter en CSV</a> <?php } ?>
                            <table class="table table-bordered table-hover" id="sample-table-11">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <?php if($_SESSION["admin"]) { ?>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Attribution du véhicule
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_attrVehicule_two">Exporter en CSV</a>
                            <table class="table table-bordered table-hover" id="sample-table-13">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <?php } ?>

                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Livreurs connectés
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <?php if($_SESSION["admin"]) { ?> <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_connectLivreur_two">Exporter en CSV</a> <?php } ?>
                            <table class="table table-bordered table-hover" id="sample-table-15">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Mise en Attente
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>

                        <div id="div_tab_resultat" class="panel-body">
                            <?php if($_SESSION["admin"]) { ?> <a class="btn btn-light-grey" style="margin-top:0;" target="_blank" href="action_poo.php?action=export_activite_one">Exporter en CSV</a> <?php } ?>
                            <table class="table table-bordered table-hover" id="sample-table-10">
                                <thead>
                                <th>Heure</th>
                                <th>Livreur</th>
                                <th>User</th>
                                <th>Commentaire</th>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>

                    </div>
                </div>

            </div>

        </div>
        <!--END TAB 4-->


        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body" style="text-align:center">
                        <input type="hidden" name="suppid" id="suppid" value="" />
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <form action="coordination.php?">
                            <input type="hidden" name="action" value="replace"/>
                            <input type="hidden" class="id_livreur1" name="id_livreur1">
                            <input type="hidden" class="id_p1" name="id_p1">
                        <div style="padding:10px">
                            <p><b>Choisissez le livreur remplaçant</b></p>
                            <div class="col-sm-6 change_planning">
                                <select name="id_livreur2" class="form-control search-select">
                                    <option value="">&nbsp;</option>
                                    <?php
                                    $Planning=$Livreur->getAllOne("");
                                    foreach ($Planning as $planning) {
                                        echo "<option value='".$planning->id_livreur."'>".$planning->prenom_livreur. " ".$planning->nom_livreur."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="submit" id="bt" class="btn btn-main" value="remplacer" >
                        </div>
                        </form>


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

                        <form action="coordination.php?">
                            <input type="hidden" name="action" value="replace"/>
                            <input type="hidden" class="id_livreur1" name="id_livreur1">
                            <input type="hidden" class="id_p1" name="id_p1">
                            <div style="padding:10px">
                                <p><b>Choisissez le livreur remplaçant</b></p>
                                <div class="col-sm-6 change_planning">
                                    <select name="id_livreur2" class="form-control search-select">
                                        <option value="">&nbsp;</option>
                                        <?php
                                        $Planning=$Livreur->getAllTwo("");
                                        foreach ($Planning as $planning) {
                                            echo "<option onclick='test(this);' value='".$planning->id_livreur."'>".$planning->prenom_livreur. " ".$planning->nom_livreur."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="submit" id="bt" class="btn btn-main" value="remplacer" >
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body" style="text-align:center">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <form action="coordination.php?">
                            <input type="hidden" name="action" value="setLivreurOnline"/>
                            <input type="hidden" name="id_planning" class="id_planning" value="" />
                            <input type="hidden" name="id_livreur" class="id_livreur" value="" />
                            <input type="hidden" name="id_commercant" class="id_commercant" value="" />
                            <input type="hidden" name="id_vehicule" class="id_vehicule" value="" />
                            <div style="padding:10px">
                                <p id="title-online"></p>
                                <input type="submit" id="bt" class="btn btn-main" value="Mettre en ligne" >
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="myModal5" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body" style="text-align:center">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <form action="coordination.php?">
                            <input type="hidden" name="action" value="setLivreurAttente"/>
                            <input type="hidden" name="id_livreur" class="id_livreur" value="" />
                            <div style="padding:10px">
                                <p id="title-attente"></p>
                                <input type="submit" id="bt" class="btn btn-main" value="Mettre en attente" >
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
            <!-- end: PAGE CONTENT-->
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
<script src="assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/fullcalendar/fullcalendar/fullcalendar.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>

<script language="javascript">
    $(document).ready(function() {
        runSelect2();

        tableau_resultat1(1);
        tableau_resultat2(1);

        tableau_dispo1(1);
        tableau_dispo2(1);

        tableaux_attente(1);

        detailtab1(1);
        detailtab2(2);

        attrVehicule1(1);
        attrVehicule2(1);

        livreurConnect1(1);
        livreurConnect2(1);

        runPaginator();


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

        //remplir les heures si elles existent, sinon en mettre par défaut
        var d1 = new Date ();
        var coeff = 1000 * 60 * 5;
        var rounded = new Date(Math.round(d1.getTime() / coeff) * coeff)
        var heure1=rounded.getHours();
        var heure2=rounded.getHours()+1;
        var minute=rounded.getMinutes();

        if ($("#h_debut_txt").val()!="") {
            heure_deb=$("#h_debut_txt").val()
        }
        else {
            heure_deb=heure1+":"+minute;
        }

        if ($("#h_fin_txt").val()!="") {
            heure_fin=$("#h_fin_txt").val()
        }
        else {
            heure_fin=heure2+":"+minute;
        }

        $('.date-picker').datepicker({
            autoclose: true,
            weekStart: 1
        });
        $('input.timepicker').timepicker({
            showMeridian: false,
            minuteStep:5,
            defaultTime: heure_deb

        });
        $('input.timepicker2').timepicker({
            showMeridian: false,
            minuteStep:5,
            defaultTime: heure_fin
        });

    });

    $(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });


    function tableau_resultat1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=liste_planning_calendar_One&id_livreur=<?=$id_livreur?>&p='+p,
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
            data	   : 'action=liste_planning_calendar_Two&id_livreur=<?=$id_livreur?>&p='+p,
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

    function tableau_dispo1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=liste_dispo_one&id_livreur=<?=$id_livreur?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-3').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function tableau_dispo2(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=liste_dispo_two&id_livreur=<?=$id_livreur?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-4').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function tableaux_attente(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=liste_attente&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-5').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
                $('#sample-table-6').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }


    function detailtab1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=detail_shift1&id_livreur=<?=$id_livreur?>&p='+p,
            type	   : "GET",
            cache    : false,
            success  : function(transport) {
                $('#sample-table-10').find("tbody").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });
    }

    function detailtab2(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=detail_shift2&id_livreur=<?=$id_livreur?>&p='+p,
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

    function attrVehicule1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=attr_vehicule1&id_livreur=<?=$id_livreur?>&p='+p,
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

    function attrVehicule2(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=attr_vehicule2&id_livreur=<?=$id_livreur?>&p='+p,
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

    function livreurConnect1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=livreur_connect_one&id_livreur=<?=$id_livreur?>&p='+p,
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

    function livreurConnect2(p){
        $.ajax({
             url      : 'action_poo.php',
            data	   : 'action=livreur_connect_two&id_livreur=<?=$id_livreur?>&p='+p,
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

    function runSelect2() {
        $(".search-select").select2({
            placeholder: "Select a State",
            allowClear: true
        });
    }

    function getWeek(calendar_id) {

        setTimeout(function() {
            $(".fc-event").each(function() {
                $(this).height($(this).height()+7)
            })
        }, 500);
    }


    //fonction affichage tooltips

    function show_div(div_to_show) {
        $("#info_calendar").hide();
        $("#tab1, #tab2, #tab3, #tab4").hide();
        $(".tab_btn").each(function() {
            $(this).removeClass('btn_actif');
        });
        $("#"+div_to_show).show();
        $("#"+div_to_show+"_btn").addClass('btn_actif');

        if (div_to_show=="tab2") {
            $('#calendar_theorique').fullCalendar('render');
        }
        else if (div_to_show=="tab3") {
            $('#sample-table-1');
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

    function loadValueReplaceLivreur(elem){
        $('.id_livreur1').val($(elem).attr('data-id_livreur'));
        $('.id_p1').val($(elem).attr('data-id_planning'))
    }

    function loadValueLivreurOnline(elem){
        $('#title-online').empty();
        $('#title-online').append( "Etes-vous sur de vouloir mettre en ligne " + $(elem).attr('data-name_livreur') +  "?" );
        $('.id_livreur').val($(elem).attr('data-id_livreur'));
        $('.id_planning').val($(elem).attr('data-id_planning'));
        $('.id_commercant').val($(elem).attr('data-id_commercant'));
        $('.id_vehicule').val($(elem).attr('data-id_vehicule'));
    }

    function loadValueAttenteLivreur(elem){
        $('#title-attente').empty();
        $('#title-attente').append( "Etes-vous sur de vouloir mettre en attente " + $(elem).attr('data-name_livreur') +  "?" );
        $('.id_livreur').val($(elem).attr('data-id_livreur'));
    }


</script>

