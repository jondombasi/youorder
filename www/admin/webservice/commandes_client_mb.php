<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}

if(isset($_GET["resto"]))		{$restaurant=$_GET["resto"];}else{$restaurant="";}
if(isset($_GET["client"]))		{$client=$_GET["client"];}else{$client="";}


$menu = "commande";
$sous_menu = "fiche";
$titre_page = "Fiche d'une commande";

$aff_erreur = "";

if($id!=""){
	$result = $sql->query("SELECT * FROM commandes WHERE id = ".$sql->quote($id)." LIMIT 1");
	$ligne = $result->fetch();
	if($ligne!=""){
		$id 	 	= $ligne["id"];
		$restaurant	= $ligne["restaurant"];
		$client	 	= $ligne["client"];
		$commentaire= $ligne["commentaire"];
		$date_debut = $ligne["date_debut"];
		$date_fin = $ligne["date_fin"];
		$statut= $ligne["statut"];
		$livreur = $ligne["livreur"];
		$ts_date_statut = strtotime($ligne["date_statut"]);
		$comm_refus = $ligne["comm_refus"];
		$signature = $ligne["signature"];
		$distance = $ligne["distance"];
		$duree = $ligne["duree"];
		$distance_km = round($distance/1000,0);
		$duree_h = gmdate("H",$duree);
		$duree_m = gmdate("i",$duree);
		if($duree_h>0){
		$duree_aff = $duree_h."h".$duree_m;
		}else{
		$duree_aff = $duree_m." min.";
		}

		$aff_modif = false;
		if($_SESSION["role"]!="livreur"){
			if($statut=="ajouté" || $statut=="réservé"){
				$aff_modif = true;
			}
		}

	}
	$result = $sql->query("SELECT * FROM clients WHERE id = ".$sql->quote($client)." LIMIT 1");
	$ligne = $result->fetch();
	if($ligne!=""){
		$c_nom	 		= $ligne["nom"];
		$c_prenom	 	= $ligne["prenom"];
		$c_adresse		= $ligne["adresse"];
		$c_longitude	= $ligne["longitude"];
		$c_latitude		= $ligne["latitude"];
		$c_numero 		= $ligne["numero"];
		$c_email	   	= $ligne["email"];
		$c_commentaire 	= $ligne["commentaire"];
	}
	$result = $sql->query("SELECT * FROM restaurants r WHERE r.id = ".$sql->quote($restaurant).$_SESSION["req_resto"]." LIMIT 1");
	$ligne = $result->fetch();
	if($ligne!=""){
		$r_nom	 	= $ligne["nom"];
		$r_adresse	= $ligne["adresse"];
		$r_longitude= $ligne["longitude"];
		$r_latitude	= $ligne["latitude"];
		$r_contact	= $ligne["contact"];
		$r_numero 	= $ligne["numero"];
	}
	if($livreur!="0"){
		$result = $sql->query("SELECT * FROM utilisateurs u WHERE u.id = ".$sql->quote($livreur)." LIMIT 1");
		$ligne = $result->fetch();
		if($ligne!=""){
			$u_nom	 	= $ligne["nom"];
			$u_prenom	= $ligne["prenom"];
			$u_numero 	= $ligne["numero"];
            $u_longitude 	= $ligne["longitude"];
            $u_latitude 	= $ligne["latitude"];
		}		
	}
}

require_once("../inc_header.php");

?>
    <style>
		#map{
			width:100%;
			height:300px;
		}
	</style>
    <link href="../assets/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css"/>
			<!-- start: PAGE -->
			<div class="main-content">
				<div class="container">
					<!-- start: PAGE HEADER -->
					<div class="row">
						<div class="col-sm-12">
							<div class="page-header">
								<h1 style="float:left;"><?php echo $titre_page; ?></h1>
								<div style="float:right;padding:10px 0px;"><span class="label <?=$couleur_statut?>" style="font-size:16px !important;"><?=$statut?></span></div>
                                <div style="clear:both;"></div>
							</div>
							<!-- end: PAGE TITLE & BREADCRUMB -->
						</div>
					</div>
					<!-- end: PAGE HEADER -->
					<!-- start: PAGE CONTENT -->
					<?php
                    if($aff_valide=="1"){
					?>
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa fa-check-circle"></i>
                        Merci <?=$_SESSION["pseudo"]?>, la commande a bien été réservé.
                    </div>                    
					<?php }elseif($aff_valide=="-1"){ ?>
                        <div class="alert alert-danger">
                            <button class="close" data-dismiss="alert">
                                ×
                            </button>
                            <i class="fa fa-check-circle"></i>
                            La commande n'est plus réservé
                        </div>                                                                
                    <?php } ?>

                    <div class="row">
                        <div class="col-md-6">
                            <h3 style="border-bottom: 1px solid #eee;padding-bottom:7px;">Information Restaurant</h3>
                            <p class="lead">
                                <?=$r_nom?>
                            </p>
                            <p>
                                <?php
								echo $r_contact."<br/>";
								echo $r_adresse."<br/>";
								echo $r_numero."<br/>";
								?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h3 style="border-bottom: 1px solid #eee;padding-bottom:7px;">Information Client</h3>
                            <p class="lead">
                                <?=$c_nom.' '.$c_prenom?>
                            </p>
                            <p>
                                <?php
								echo $c_adresse."<br/>";
								echo $c_numero."<br/>";
								echo $c_email;
								?>
                            </p>
                            <div class="well well-sm">
                            <?php echo nl2br($c_commentaire); ?>
                            </div>
                        </div>
                    </div>
  				  	<div class="row">
						<div class="col-sm-6">
                            <h3 style="border-bottom: 1px solid #eee;padding-bottom:7px;">Détail de la commande</h3>
                            <p><i class="fa fa-calendar" style="font-size:1.5em;"></i> <?php echo date("d/m/Y",strtotime($date_debut)); ?></p>
                            <p><i class="clip-clock" style="font-size:1.5em;"></i> <?php echo "Entre ".date("H:i",strtotime($date_debut))." et ".date("H:i",strtotime($date_fin)); ?></p>
                            <p><i class="clip-map" style="font-size:1.5em;"></i> <?php echo $distance_km." km - ".$duree_aff; ?></p>
                            
								<?php 
								echo '<span class="label '.$couleur_statut.'">'.ucfirst($statut)."</span>"; 
								if($statut=="echec"){
									echo '&nbsp;&nbsp;'.$txt_raison;	
								}
								?>
                           	</p>
								<?php 
								if($livreur!="0"){
									echo '<p style="line-height:25px;">';
									echo '<div style="float:left;"><i class="fa fa-motorcycle" style="font-size:1.5em;"></i>&nbsp;&nbsp;&nbsp;'.ucfirst($u_prenom)." ".strtoupper($u_nom)."&nbsp;&nbsp;&nbsp;</div>";
									echo '<div style="float:left;"><i class="clip-phone" style="font-size:1.5em;"></i>&nbsp;&nbsp;&nbsp;'.$u_numero."&nbsp;&nbsp;&nbsp;</div>";
									if($statut!="echec"){
										echo '<div style="float:left;"><i class="clip-clock" style="font-size:1.5em;"></i>&nbsp;&nbsp;&nbsp;'."le ".date("d/m",$ts_date_statut)." à ".date("H:i",$ts_date_statut)."</div>";	
									}
									echo '<div style="clear:both;"></div></p>';
								}
								?>
                            <div style="float:left;margin-right:6px;margin-top:5px;"><i class="clip-pencil" style="font-size:1.5em;"></i></div>
                            <div style="float:left;width:250px;" class="well well-sm">
                            <?php echo nl2br($commentaire); ?>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                        <?php
						if($statut=="signé"){
							?>
							<div class="col-sm-6">
                            	Signature client : <br/>
                                <img src="<?=$signature?>" />
                            </div>
							<?php
							
							
						}
						?>
                        
                        <div style="clear:both;"></div>
                        <div class="col-sm-12">
                        	<div id="map"></div>
                        </div>
                    </div>
                    <div class="row" style="height:25px;">
                    </div>
                    <div class="row">
                    	<div class="col-sm-12">
                            <div class="well" style="text-align:center;">
	                                <?php if($statut=="ajouté"){ ?>
                                        <input type="button" onclick="lien('action.php?action=take_commande&id=<?=$id?>')" id="bt" class="btn btn-green" value="Je prends en livraison" style="min-width:250px;width:20%;">
                                        &nbsp;                                    
                                    <?php }elseif($statut=="réservé" && $livreur==$_SESSION["userid"]){ ?>
                                        <input type="button" onclick="lien('action.php?action=recup_commande&id=<?=$id?>')" id="bt" class="btn btn-green" value="Je récupère la commande" style="min-width:250px;width:20%;">
                                        &nbsp;                                    										
                                        <input type="button" onclick="lien('action.php?action=detake_commande&id=<?=$id?>')" id="bt" class="btn btn-bricky" value="J'annule ma livraison" style="min-width:250px;width:20%;">
                                        &nbsp;                                    										
                                    <?php }elseif($statut=="récupéré" && $livreur==$_SESSION["userid"]){ ?>
                                        <input type="button" onclick="lien('./signature/?id=<?=$id?>')" id="bt" class="btn btn-green" value="Signature client" style="min-width:250px;width:20%;">
                                        &nbsp;                                    										
                                        <div class="btn-group dropup  dropdown-enduring">
                                            <a href="#" data-toggle="dropdown" class="btn btn-bricky dropdown-toggle" style="min-width:250px;width:20%;">
                                                Echec de la commande <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu dropdown-enduring" style="min-width:250px;width:20%;">
                                                <li role="presentation" style="min-width:250px;width:20%;">
                                                    <a href="javascript:void(0)" onclick="lien('action.php?action=refus_commande&raison=1&id=<?=$id?>')" tabindex="-1" role="menuitem">
                                                        Adresse inexistante
                                                    </a>
                                                </li>
                                                <li role="presentation" style="min-width:250px;width:20%;">
                                                    <a href="javascript:void(0)" onclick="lien('action.php?action=refus_commande&raison=2&id=<?=$id?>')" tabindex="-1" role="menuitem">
                                                        Ne répond pas
                                                    </a>
                                                </li>
                                                <li role="presentation" style="min-width:250px;width:20%;">
                                                    <a href="javascript:void(0)" onclick="lien('action.php?action=refus_commande&raison=3&id=<?=$id?>')" tabindex="-1" role="menuitem">
                                                        Refuse la commande
                                                    </a>
                                                </li>
                                                <li class="divider" role="presentation"></li>
                                                <li role="presentation" class="dropdown-enduring" style="min-width:250px;width:20%;">
                                                	Autre : <br/>
                                                	<input type="text" name="comm" id="comm" class="dropdown-enduring" value="" style="width:90%;" />
                                                    <a href="javascript:void(0)" onclick="refuse_commande_autre()" tabindex="-1" role="menuitem">
                                                        Valider
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        &nbsp;                                    										
									<?php } ?>
									<?php if($aff_modif){ ?>
                                        <input type="button" onclick="lien('commandes_fiche.php?id=<?=$id?>')" id="bt" class="btn btn-blue" value="Modifier" style="min-width:250px;width:20%;">
                                        &nbsp;
                                    <?php } ?>
	                                <input type="button" onclick="lien('commandes_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="min-width:250px;width:20%;">
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                    </div>				                    
					<!-- end: PAGE CONTENT-->
				</div>
			</div>
			<!-- end: PAGE -->
			<div id="ajax-modal" class="modal fade" tabindex="-1" style="display: none;"></div>
		
<?php
require_once("../inc_footer.php");
?>

  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
  <script type="text/javascript" src="./assets/js/gmaps.js"></script>

  <script src="../adminassets/plugins/bootstrap-modal/js/bootstrap-modal.js"></script>
  <script src="../adminassets/plugins/bootstrap-modal/js/bootstrap-modalmanager.js"></script>

  <script type="text/javascript">
	var map;
    $(document).ready(function(){
      map = new GMaps({
        el: '#map',
		zoom: 13,
        lat: <?=$u_latitude?>,
        lng: <?=$u_longitude?>
      });
      map.drawRoute({
        origin: [<?=$u_latitude.', '.$u_longitude?>],
        destination: [<?=$c_latitude.', '.$c_longitude?>],
        travelMode: 'driving',
        strokeColor: '#131540',
        strokeOpacity: 0.8,
        strokeWeight: 6
      });
	  map.addMarker({
	    lat: <?=$u_latitude?>,
	    lng: <?=$u_longitude?>,
		icon: 'images/velo_detour.png'
	  });	  
	  map.addMarker({
	    lat: <?=$c_latitude?>,
	    lng: <?=$c_longitude?>,
		icon: 'images/end.png'
	  });	  
    
	initModals();
	});
	
	function refuse_commande_autre(){
		comm = $("#comm").val()
		lien('action.php?action=refus_commande&raison=4&comm='+comm+'&id=<?=$id?>')
	}


    function initModals() {
        $.fn.modalmanager.defaults.resize = true;
        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';
        var $modal = $('#ajax-modal');
        $('.demo').on('click', function () {
            // create the backdrop and wait for next modal to be triggered
            $('body').modalmanager('loading');
            setTimeout(function () {
                $modal.load('test2.php', '', function () {
                    $modal.modal();
                });
            }, 1000);
        });
        $modal.on('click', '.update', function () {
            $modal.modal('loading');
            setTimeout(function () {
                $modal
                    .modal('loading')
                    .find('.modal-body')
                    .prepend('<div class="alert alert-info fade in">' +
                        'Updated!<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                        '</div>');
            }, 1000);
        });
    };

  </script>
