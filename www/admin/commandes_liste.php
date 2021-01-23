<?php
$menu       = "commande";
$sous_menu  = "liste";
$aff_erreur = "";

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

require_once("inc_connexion.php");

require_once("inc_header.php");

if(isset($_GET["id"]))          {$id        =$_GET  ["id"];}            else{$id="";}
if(isset($_GET["statut"]))		{$statut    =$_GET  ["statut"];}        else{$statut="";}
if(isset($_GET["restaurant"]))	{$restaurant=$_GET  ["restaurant"];}    else{$restaurant="";}
if(isset($_GET["periode"]))	    {$periode   =$_GET  ["periode"];}       else{$periode="";}
if(isset($_GET["aff_valide"]))	{$aff_valide=$_GET  ["aff_valide"];}    else{$aff_valide= "";}
if(isset($_POST["action"]))		{$action    =$_POST ["action"];}        else{$action="";}

if(isset($_GET["p"]))           {$page      = $_GET["p"];}          else{$page=1;}
if(isset($_GET["id_livreur"]))	{$id_livreur= $_GET["id_livreur"];} else{$id_livreur=0;}
if(isset($_GET["restaurant"]))	{$restaurant= $_GET["restaurant"];} else{$restaurant="";}
if(isset($_GET["statut"]))		{$statut    = $_GET["statut"];}     else{$statut="";}
if(isset($_GET["histo"]))		{$histo     = $_GET["histo"];}      else{$histo="";}

if ($statut=="" && $restaurant=="" && $periode=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}

$Commercant = new Commercant($sql);
$Commande   = new Commande($sql);
$Livreur    = new Livreur($sql);
$Utilisateur    = new Utilisateur($sql);

$Commande->getPagination(30, "", $restaurant, $statut, $periode, 0);

$nbpages    = $Commande->getNbPages();
$nbres      = $Commande->getNbRes();

$date_status= date("d-m-Y");
$heure      = date("H:i",time()+60*30);

if ($nbpages==0) {
	$nbpages++;
}

if($action=="valider"){


    $post_commande  = $_POST['commande'];
    $post_statut    = $_POST['statut'];
    $post_livreur   = $_POST['livreur'];
    $id_livreur     = $_POST['id_livreur'];

    if($post_commande==""){
        $css_commande__obl = "has-error";
        $continu = false;
    }

    if($post_statut==""){
        $css_statut_obl = "has-error";
        $continu = false;
    }


    if($post_livreur==""){
        $css_livreur__obl = "has-error";
        $continu = false;
    }


    if($continu){

        $Commande = new Commande($sql, null);
        $Commande->validation($post_commande, $_SESSION["userid"], $id_livreur);
        header("location: commandes_liste.php?aff_valide=1&id=".$id);

//        $Operation = new Operation($sql, null, $post_vehicule);
//        $Operation->setOperation($post_vehicule, $_SESSION["userid"], $commentaire, $post_actions, $post_pieces);
//        header("location: vehicule_operation_fiche.php?aff_valide=1&id=".$id);

    }else{
        $aff_erreur="1";
    }
}



?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css">
<link rel="stylesheet" type="text/css" href="assets/css/magnific-popup.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">

<style>
    .select2-container .select2-choice .select2-arrow b{background: none !important;}
</style>
<!-- start: PAGE -->
<div style="display:none;">
    <a class="pop-up-generique" href=""></a>
</div>
<div class="main-content">
    <div class="container">

        <!-- content -->
        <div class="row header-page">
            <div class="col-lg-2">
                <div class="nb_total"><?php echo ($nbres>1) ? $nbres." commandes" : $nbres." commande";?></div>

                <?php

                if($aff_valide=="1"){
                    ?>
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa fa-check-circle"></i>
                        L'état de la commande a bien été modifié
                    </div>
                <?php } ?>

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
                        <form class="form-horizontal" role="form" action="commandes_liste.php" method="get">
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
                                        <option <?php if($statut=="ajouté")     {echo 'selected="selected"';} ?> value="ajouté">    Ajoutée</option>
                                        <option <?php if($statut=="réservé")    {echo 'selected="selected"';} ?> value="réservé">   Réservée</option>
                                        <option <?php if($statut=="récupéré")   {echo 'selected="selected"';} ?> value="récupéré">  Récupérée</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="form-field-1">Période</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input id="periode" name="periode" value="<?php echo $periode; ?>" type="text" class="form-control date-time-range">
                                    </div>
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
                    <a href="#validCommandel3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Valider une commande"><i class=" fa-plus fa fa-white"></i></a>
                    <?php
                        if($_SESSION["role"]!="livreur"){
                                ?>
                                <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_commandes&histo=0&restaurant=<?=$restaurant?>&statut=<?=$statut?>&periode=<?=$periode?>">Exporter en CSV</a>
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
                    <th>Numero de commande</th>
	        		<th>Commerçant</th>
	        		<th>Infos livreur</th>
	        		<th>Client</th>
	        		<th>Créneau de livraison</th>
	        		<th style="width:50px;">Infos</th>
	        		<th>Statut</th>
	        		<th style="width:185px;">Actions</th>
	        	</thead>
	        	<tbody>
	        	</tbody>
	        </table>
        </div>
        <div style="text-align:right;">
        	<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
        </div>

        <!-- MODAL -->
        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body" style="text-align:center">
                        <input type="hidden" name="suppid" id="suppid" value="" />
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <div style="padding:10px">                                            
							<p><b>Etes-vous sûr de vouloir supprimer cette commande ?</b></p>
						</div>

						<button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Annuler
						</button>
						<button onclick="confirm_suppression('suppcommande')" class="btn btn-default" data-dismiss="modal">
							Confirmer
						</button>
					</div>
				</div>
			</div>
		</div>


        <div class="modal fade" id="validCommandel3" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">
                            Valider une commande
                        </h4>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <form role="form" name="form" id="form1" method="post"  class="form-horizontal">
                            <input type="hidden" name="action" value="valider"/>

                            <div class="form-group <?php echo $css_commande_obl; ?>">
                                <label class="col-sm-4 control-label"> Numero de la commande <span class="symbol required"></span> </label>
                                <div class="col-sm-4 margin_label">
                                    <select name="vehicule" id="type" class="form-control">
                                        <option value="">&nbsp;</option>
                                        <?php
                                        $liste_commandes=$Commande->getRecupCommande();
                                        foreach ($liste_commandes as $commandeR){
                                            echo "<option value= ".$commandeR->id." >".$commandeR->id. "</option>";
                                            }
                                        ?>
                                    </select>

                                </div>
                            </div>

                            <div class="form-group <?php echo $css_statut_obl; ?>  ">
                                <label class="col-sm-4 control-label" for="operation">Statut <span class="symbol required"></span> </label>
                                <div class="col-sm-4 margin_label">
                                    <select name="statut" id="statut" class="form-control">
                                        <option value="">&nbsp;</option>
                                        <option <?php if($statut=="réservé")    {echo 'selected="selected"';} ?> value="réservé">   Réservée</option>
                                        <option <?php if($statut=="récupéré")   {echo 'selected="selected"';} ?> value="récupéré">  Récupérée</option>
                                        <option <?php if($statut=="signé")      {echo 'selected="selected"';} ?> value="signé">     Signé</option>
                                        <option <?php if($statut=="echec")      {echo 'selected="selected"';} ?> value="echec">     Echec</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group <?php echo $css_livreur_obl; ?>">
                                <label class="col-sm-4 control-label"> Livreur <span class="symbol required"></span> </label>
                                <div class="col-sm-6 margin_label">
                                    <select name="livreur" id="livreur" class="form-control search-select">
                                        <option value="">&nbsp;</option>
                                        <?php
                                        foreach ($Livreur->getAll("", "", "", "", "", "") as $livreur) {
                                            $sel=($id_livreur==$livreur->id) ? "selected" : "";
                                            echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom. " ".$livreur->nom."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row row_btn">
                                <div class="col-sm-4 col-sm-offset-8" style="text-align:right">&nbsp;
                                    <input type="submit" id="bt" class="btn btn-main" value="Valider" style="width:100px;">
                                </div>
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

<script src="assets/plugins/select2/select2.min.js"></script>
<script src="assets/plugins/bootstrap-paginator/src/bootstrap-paginator.js"></script>  
<script src="assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script> 
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
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
		  	data	   : 'action=liste_commande&histo=0&restaurant=<?=$restaurant?>&statut=<?=$statut?>&p='+p,
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
				//$('#paginator-content-1').text("Page item clicked, type: " + type + " page: " + page);
			}
		});
	}

	jQuery(document).ready(function() {
		runSelect2();
		tableau_resultat(1);
		runPaginator();
        runDatePicker();


		$('.date-time-range').daterangepicker({
			timePicker: true,
			timePickerIncrement: 5,
			timePicker12Hour: false,
			firstDay: 1,
			format: 'DD-MM-YYYY hh:mm A'
		});		
	});


    function runDatePicker() {
        $('.date-picker').datepicker({
            autoclose: true
        });
        $('#heure_debut, #heure_fin').timepicker({
            minuteStep: 1,
            showSeconds: false,
            showMeridian: false,
            defaultTime: '00:00'
        });

        $("#heure_debut, #heure_fin").on("focus", function() {
            return $(this).timepicker("showWidget");
        });

        $('#heure_debut').timepicker().on('changeTime.timepicker', function(e) {
            //on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
            //TO DO : changer la date si l'heure passe a 1h du jour suivant ?
            var d = new Date("1970-01-01 "+e.time.value+":00");
            d.setHours(d.getHours() + 1);

            $('#heure_fin').timepicker('setTime', d.getHours()+":"+d.getMinutes());

        });
    };



    function runSelect(nomselect) {
        $(nomselect).select2({
            placeholder: "Select a State",
            allowClear: true
        });
    };

</script>
