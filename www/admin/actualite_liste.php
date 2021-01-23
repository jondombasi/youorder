<?php
$menu = "actualite";
$sous_menu = "liste";
require_once("inc_header.php");

$nbpages = 0;
?>

<link rel="stylesheet" type="text/css" href="assets/css/magnific-popup.css">
<div style="display:none;">
    <a class="pop-up-generique" href=""></a>
</div>

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1>Liste des actualités</h1>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
		<div class="row">
            <div class="col-sm-2 col-md-offset-10" style="text-align:right;margin-bottom:20px;">
                <button type="button" class="btn btn-main" onclick="lien('actualite_fiche.php')">Ajouter un article</button>
            </div>
        </div>
		<div class="row">
			<div class="col-sm-12">
				<?php
					$req = "SELECT count(*) as NB FROM actualites WHERE 1";
	                                    
	                $nbaff = 100;
	                $result = $sql->query($req);
	                $ligne = $result->fetch();
	                if($ligne!=""){
	                    $nb_lignes = $ligne["NB"];
	                }
	                else{
	                    $nb_lignes = 0;
	                }

	                $nbpages = $nb_lignes/$nbaff;
	                $nbpages = ceil($nbpages);
	                if ($nbpages==0) $nbpages++;
				?>
				<div id="div_tab" class="table-responsive"></div>
				<div style="text-align:right;">
		        	<ul style="margin:0px;" id="paginator-example-1" class="pagination-purple"></ul>
		        </div>
			</div>
		</div>        

		<!-- MODAL -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">Supprimer un article</h4>
					</div>
					<div class="modal-body">
                        <input type="hidden" name="suppid" id="suppid" value="" />                                                
						<p>
							Etes-vous sûr de vouloir supprimer cet article ?
						</p>
					</div>
					<div class="modal-footer">
						<button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
							Annuler
						</button>
						<button onclick="confirm_suppression('suppactualite')" class="btn btn-default" data-dismiss="modal">
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
<script src="assets/plugins/bootstrap-paginator/src/bootstrap-paginator.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script> 
<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
<script>
	$(document).ready(function() {		
		resultatJeu(1);
		runPaginator();
	});

	function resultatJeu(p){
		$.ajax({
			url      	: 'action_poo.php',
		  	data	   	: 'action=liste_actualites&p='+p,
		  	type	   	: "GET",
		  	cache    	: false,		  
		  	success  	: function(transport) {  
				$("#div_tab").html(transport)
				$('.image-popup-vertical-fit').magnificPopup({
			        type: 'image',
			        closeOnContentClick: true,
			        mainClass: 'mfp-img-mobile',
			        image: {
			            verticalFit: true
			        }
			    }); 
			}
		});					
	}

	function runPaginator() {
		$('#paginator-example-1').bootstrapPaginator({
			bootstrapMajorVersion: 3,
			currentPage: 1,
			totalPages: <?php echo $nbpages; ?>,
			onPageClicked: function (e, originalEvent, type, page) {
				resultatJeu(page);
			}
		});
	}

	$(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });
</script>