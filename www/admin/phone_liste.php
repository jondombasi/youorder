<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 12/09/2017
 * Time: 15:33
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

require_once("inc_connexion.php");


if(isset($_GET["id"]))		    {$id        = $_GET["id"];}         else{$id = "";}
if(isset($_GET["number"]))		{$number    = $_GET["number"];}     else{$number    = "";}
if(isset($_GET["phone"]))		{$phone     = $_GET["phone"];}      else{$phone     = "";}

if(isset($_GET["marque"]))		{$marque    = $_GET["marque"];}     else{$marque     = "";}

if(isset($_GET["aff_valide"]))	{$aff_valide= $_GET["aff_valide"];} else{$aff_valide= "";}
if(isset($_POST["action"]))		{$action    = $_POST["action"];}    else{$action    = "";}
if(isset($_GET["id_livreur"]))  {$id_livreur= $_GET["id"];}         else{$id_livreur="";}

$menu = "phone";
$aff_erreur = "";
$continu    = true;

if ($number=="") {
    $filtre='style="display:none;"';
    $filtre_fleche="expand";
}
else {
    $filtre_fleche="collapses";
}

$Livreur    = new Livreur($sql);
$Puce       = new PhoneNumber($sql, $id);
$Phone      = new Phone($sql);

$Puce->getPagination(10, $number);
$Phone->getPagination(10, $number);
$nbpages    = $Puce->getNbPages();
$nbres      = $Puce->getNbRes();

if ($nbpages==0) {
    $nbpages++;
}

if($id == ""){
    if($action == "ajouter") {

        $number = $_POST['number'];

        if ($number == "") {
            $css_number_obl = "has-error";
            $continu = false;
        }

        if($continu){

            $Phone      = new PhoneNumber($sql, $id);
            $Phone->setNumber($number);
            header("location: phone_liste.php?aff_valide=1");

        }else{
            $aff_erreur="1";
        }

    }

}

require_once("inc_header.php");
?>


<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h1>Liste des Puces télephoniques</h1>
                </div>
                <!-- end: PAGE TITLE & BREADCRUMB -->
            </div>
        </div>
        <!-- content -->

        <!-- start: PAGE CONTENT -->

            <div class="row header-page">
                <div class="col-lg-2">
                    <div class="nb_total"><?php echo ($nbres>1) ? $nbres." Puces" : $nbres." Puce";?></div>
                </div>

                <div class="col-lg-2">
                    <div class="nb_total"><?php echo ($nbres>1) ? $nbres." Téléphones" : $nbres." Téléphone";?></div>
                </div>

                <div class="col-lg-8 btn-spe">
                    <p style="text-align:right">
                        <a href="#addPucel3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="puce">Ajouter une puce</a>
                    </p>
                </div>
            </div>

        <?php
        if($aff_erreur=="1"){
            ?>
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert">
                    ×
                </button>
                <i class="fa fa-check-circle"></i>
               Format du Numéro invalide ou déjà utilisé. Pensez à mettre des points entre chaque numéro et vérifier que celui-ci n'existe pas
                <?= $message ?>
            </div>
            <?php
        }

        if($aff_valide=="1"){
            ?>
            <div class="alert alert-success">
                <button class="close" data-dismiss="alert">
                    ×
                </button>
                <i class="fa fa-check-circle"></i>
                Le numéro a bien été enregistré
            </div>
        <?php } ?>

            <div class="row">

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Liste des puces
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-1">
                                <thead>
                                <th>Numéro</th>
                                <th>Action</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div style="text-align:right;">
                            <ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
                        </div>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Liste des téléphones
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-2">
                                <thead>
                                <th>Marque</th>
                                <th>Modele</th>
                                <th>Quantite</th>
                                <th>Action</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div style="text-align:right;">
                            <ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
                        </div>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Liste des téléphones
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-3">
                                <thead>
                                <th>Marque</th>
                                <th>Modele</th>
                                <th>Quantite</th>
                                <th>Action</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div style="text-align:right;">
                            <ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
                        </div>

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Attribution des puces
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-1">
                                <thead>
                                <th>Date</th>
                                <th>User</th>
                                <th>Numero de la Puce</th>
                                <th>Livreur</th>
                                <th>Commentaires</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div style="text-align:right;">
                            <ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
                        </div>

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-align-left"></i>
                            Changement des puces
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                            </div>
                        </div>


                        <div id="div_tab_resultat" class="panel-body">
                            <table class="table table-bordered table-hover" id="sample-table-1">
                                <thead>
                                <th>Date</th>
                                <th>User</th>
                                <th>Numero de telephone/th>
                                <th>Livreur</th>
                                <th>Commentaires</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div style="text-align:right;">
                            <ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
                        </div>

                    </div>
                </div>



            </div>


        <div class="modal fade" id="addPucel3" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">
                            Ajouter un numéro de télephone
                        </h4>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <form role="form" name="form" id="form1" method="post"  class="form-horizontal">
                            <input type="hidden" name="action" value="ajouter"/>

                            <div class="form-group <?php echo $css_number_obl; ?>">
                                <label class="col-sm-4 control-label">
                                    Numéron de téléphone
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" name="number" placeholder="number" class="form-control" value="<?=$number?>">
                                </div>
                            </div>

                            <div class="row row_btn">
                                <div class="col-sm-4 col-sm-offset-8" style="text-align:right">&nbsp;
                                    <input type="submit" id="bt" class="btn btn-main" value="ajouter" style="width:100px;">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- end: PAGE CONTENT-->
        </div>

        <div class="modal fade" id="addPucel3" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">
                            Ajouter un téléphone
                        </h4>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <form role="form" name="form" id="form1" method="post"  class="form-horizontal">
                            <input type="hidden" name="action" value="ajouter"/>

                            <div class="form-group <?php echo $css_number_obl; ?>">
                                <label class="col-sm-4 control-label">
                                   Marque
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" name="number" placeholder="number" class="form-control" value="<?=$number?>">
                                </div>
                            </div>

                            <div class="form-group <?php echo $css_number_obl; ?>">
                                <label class="col-sm-4 control-label">
                                    Modele
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" name="number" placeholder="number" class="form-control" value="<?=$number?>">
                                </div>
                            </div>

                            <div class="form-group <?php echo $css_number_obl; ?>">
                                <label class="col-sm-4 control-label">
                                    Quantité
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" name="number" placeholder="number" class="form-control" value="<?=$number?>">
                                </div>
                            </div>

                            <div class="row row_btn">
                                <div class="col-sm-4 col-sm-offset-8" style="text-align:right">&nbsp;
                                    <input type="submit" id="bt" class="btn btn-main" value="ajouter" style="width:100px;">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- end: PAGE CONTENT-->
        </div>

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
                                <p><b>Attribuer la puce à un livreur</b></p>
                                <div class="col-sm-6 change_planning">
                                    <select name="id_livreur" class="form-control search-select">
                                        <option value="">&nbsp;</option>
                                        <?php
                                        foreach ($Livreur->getAll("", "", "", "", "") as $livreur) {
                                            echo "<option onclick='test(this);' value='".$livreur->id_livreur."'>".$livreur->prenom_livreur. " ".$livreur->nom_livreur."</option>";
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

                        <div style="padding:10px">
                            <p><b>Etes-vous sûr de vouloir supprimer cette puce ?</b></p>
                        </div>

                        <button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
                            Annuler
                        </button>
                        <button onclick="confirm_suppression('supppuce')" class="btn btn-default" data-dismiss="modal">
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- End: Modal Tab2-->

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

    function tableau_resultat(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=puce_liste&number=<?=$number?>&p='+p,
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

    function tableau_resultat1(p){
        $.ajax({
            url      : 'action_poo.php',
            data	   : 'action=phone_liste&modele=<?=$number?>&p='+p,
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

    jQuery(document).ready(function() {
        runSelect2();
        tableau_resultat(1);
        tableau_resultat1(1);
        runPaginator();
    });

</script>