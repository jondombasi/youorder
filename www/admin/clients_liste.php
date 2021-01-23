<?php
$menu = "client";
$sous_menu = "liste";

require_once("inc_header.php");

if(isset($_GET["nom"]))		    {$nom       =$_GET["nom"];}         else{$nom       ="";}
if(isset($_GET["numero"]))	    {$numero    =$_GET["numero"];}      else{$numero    ="";}
if(isset($_GET["restaurant"]))  {$restaurant=$_GET["restaurant"];}  else{$restaurant="";}
if(isset($_GET["ret"]))		    {$ret       =$_GET["ret"];}         else{$ret       ="";}

if ($nom=="" && $numero=="" && $restaurant=="") {
	$filtre='style="display:none;"';
	$filtre_fleche="expand";
}
else {
	$filtre_fleche="collapses";
}

$Client = new Client($sql);
$Client->getPagination(30, $nom, $numero, $restaurant);
$nbpages=$Client->getNbPages();
$nbres  =$Client->getNbRes();

?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<style>
    @media (max-width:1279px){
        .form-group label{
            width: 100% !important;
            text-align: left !important;
        }
        .form-res{width:100% !important;}
    }
    .select2-container .select2-choice .select2-arrow b{background: none !important;}
</style>

<!-- start: PAGE -->
<div class="main-content">
    <div class="container">

		<!-- content -->
		<div class="row header-page">
                    <?php
			switch($ret){
                            case "clientsup":
                                echo    '<div class="col-sm-12"><div class="alert alert-success">
                                        <button class="close" data-dismiss="alert">
                                        ×
                                        </button>
                                        <i class="fa fa-check-circle"></i>
                                        Le client a été supprimé
                                        </div></div>';                    
                            break;
			}
                    ?>
                    <div class="col-lg-2">
                        <div class="nb_total"><?php echo ($nbres>1) ? $nbres." clients" : $nbres." client";?></div>
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
                                <form class="form-horizontal" role="form" action="clients_liste.php" method="get">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="form-field-1">Nom</label>
                                        <div class="col-sm-9 form-res">
                                            <input type="text" name="nom" placeholder="Nom" id="form-field-1" class="form-control" value="<?php echo $nom; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="form-field-1">Numéro</label>
                                        <div class="col-sm-9 form-res">
                                            <input type="text" name="numero" placeholder="Numéro de téléphone" id="form-field-1" class="form-control" value="<?php echo $numero; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="form-field-1">Commerçant</label>
                                        <div class="col-sm-9 form-res">
                                            <select name="restaurant" id="restaurant" class="form-control search-select">
                                                <option value="">&nbsp;</option>
                                                <?php
                                                    $result = $sql->query("SELECT * FROM restaurants r WHERE 1 ".$_SESSION["req_resto"]." and r.statut = 1 ORDER BY nom");	// WHERE etat!='6'
                                                    while($ligne = $result->fetch()) {
                                                        if($restaurant==$ligne["id"]){$sel = 'selected="selected"';}else{$sel = "";}
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
	            	<?php
                            if($_SESSION["admin"]){
                                ?>
                                <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_clients&nom=<?=$nom?>&numero=<?=$numero?>&restaurant=<?=$restaurant?>">Exporter en CSV</a>
                                <?php
                            }
                        ?>
		            <a class="btn btn-dark-green" href="clients_fiche.php">Ajouter un client</a>
		                </p>
                    </div>
                </div>

        <div id="div_tab_resultat" class="table-responsive">
        </div>
        <div style="text-align:right;">
        	<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
        </div>

        <!-- MODAL -->
		<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">Supprimer un client</h4>
					</div>
					<div class="modal-body">
                        <input type="hidden" name="suppid" id="suppid" value="" />                                                
						<p>
							Etes-vous sûr de vouloir supprimer ce client ?
						</p>
					</div>
					<div class="modal-footer">
						<button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Annuler
						</button>
						<button onclick="confirm_suppression('suppclient')" class="btn btn-default" data-dismiss="modal">
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
		  	data	   : 'action=liste_clients&nom=<?=$nom?>&numero=<?=$numero?>&restaurant=<?=$restaurant?>&p='+p,
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