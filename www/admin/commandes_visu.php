<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		    {$id        = $_GET["id"];}         else{$id="";}
if(isset($_GET["aff_valide"]))	{$aff_valide= $_GET["aff_valide"];} else{$aff_valide="";}
if(isset($_POST["action"]))		{$action    = $_POST["action"];}    else{$action="";}

if(isset($_GET["resto"]))		{$restaurant= $_GET["resto"];}      else{$restaurant="";}
if(isset($_GET["client"]))		{$client    = $_GET["client"];}     else{$client="";}


$menu       = "commande";
$sous_menu  = "fiche";
$titre_page = "Fiche d'une commande";

$aff_erreur = "";

$Commande   = new Commande($sql, $id);

if ($id!="") {
    $restaurant     = $Commande->getRestaurant();
    $client         = $Commande->getClient();
    $commentaire    = $Commande->getCommentaire();
    $date_debut     = $Commande->getDateDebut();
    $date_fin       = $Commande->getDateFin();
    $statut         = $Commande->getStatut();
    $couleur_statut = couleur_statut($statut);
    $livreur        = $Commande->getLivreur();
    $ts_date_statut = strtotime($Commande->getDateStatut());
    $raison_refus   = $Commande->getRaisonRefus();
    $comm_refus     = $Commande->getCommRefus();
    $txt_raison     = txt_raison_refus($raison_refus,$comm_refus,$ts_date_statut);

    $signature      = $Commande->getSignature();
    $signature_crop = explode(".", $signature);

    if (file_exists('signature/'.$signature_crop[0].'_crop.'.$signature_crop[1])) {
        $signature=$signature_crop[0].'_crop.'.$signature_crop[1];
    }

    $distance       = $Commande->getDistance();
    $duree          = $Commande->getDuree();
    $distance_km    = round($distance/1000,0);
    $duree_h        = gmdate("H",$duree);
    $duree_m        = gmdate("i",$duree);
    $duree_aff      = ($duree_h>0) ? $duree_h."h".$duree_m : $duree_m." min.";

    $aff_modif = false;
    if($_SESSION["role"]!="livreur"){
        if($statut=="ajouté" || $statut=="réservé"){
            $aff_modif = true;
        }
    }

    //On créer une nouvelle instance de client
    $Client     = new Client($sql, $client);

    $c_nom          = $Client->getNom();
    $c_prenom       = $Client->getPrenom();
    $c_adresse      = $Client->getAdresse();
    $c_longitude    = $Client->getLongitude();
    $c_latitude     = $Client->getLatitude();
    $c_numero       = $Client->getNumero();
    $c_email        = $Client->getEmail();
    $c_commentaire  = $Client->getCommentaire();

    //On créer une nouvelle instance de commercant
    $Commercant = new Commercant($sql, $restaurant);

    $r_nom          = $Commercant->getNom();
    $r_adresse      = $Commercant->getAdresse();
    $r_longitude    = $Commercant->getLongitude();
    $r_latitude     = $Commercant->getLatitude();
    $r_contact      = $Commercant->getContact();
    $r_numero       = $Commercant->getNumero();

    //Si un livreur est affecté, on créer une instance de la classe livreur
	if($livreur!="0"){
        $Livreur=new Livreur($sql, $livreur);
        $u_nom      = $Livreur->getNom();
        $u_prenom   = $Livreur->getPrenom();
        $u_numero   = $Livreur->getTelephone();
	}
}

require_once("inc_header.php");
?>

<style>
	#map{
		width:100%;
		height:300px;
	}
	
	.btn{
		margin:4px 0px;	
	}
</style>

<link href="assets/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css"/>

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1 style="float:left;"><?=$titre_page; ?></h1>
					<div style="float:right;padding:10px 0px;"><span class="label <?=$couleur_statut?>" style="font-size:16px !important;"><?=txt_statut($statut)?></span></div>
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
            Merci <?=$_SESSION["pseudo"]?>, la commande a bien été réservée.
        </div>                    
		<?php }elseif($aff_valide=="-1"){ ?>
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert">
                    ×
                </button>
                <i class="fa fa-check-circle"></i>
                La commande n'est plus réservée
            </div>                                                                
		<?php }elseif($aff_valide=="-2"){ ?>
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert">
                    ×
                </button>
                <i class="fa fa-check-circle"></i>
                Cette commande a déjà été réservée
            </div>                                                                
        <?php } ?>

        <div class="row">
            <div class="col-sm-4 col-sm-offset-2">
                <div class="panel panel-default" style="min-height: 260px;">
                    <div class="panel-heading" style="padding-left:10px;text-align:center">
                        INFORMATIONS COMMERCANT
                    </div>
                    <div class="panel-body">
                        <p class="commandes_titre">
                            <?=$r_nom?>
                        </p>
                        <p>
                            <?php
                            echo $r_contact."<br/>";
                            echo "<span class='commandes_icon'><i class='fa fa-map-marker'></i></span>".$r_adresse."<br/>";
                            echo "<span class='commandes_icon'><i class='fa fa-phone'></i></span>".$r_numero."<br/>";
                            ?>
                        </p>
                        <hr/>
                        <select name="type_livraison" class="form-control" disabled>
                            <option value="tournee" <?php if ($type_livraison=="tournee") echo "selected";?>>Tournée</option>
                            <option value="etoile" <?php if ($type_livraison=="etoile") echo "selected";?>>Étoile</option>
                        </select>
                    </div>
                </div> 
            </div>
            
            <div class="col-sm-4">
                <div class="panel panel-default" style="min-height: 260px;">
                    <div class="panel-heading" style="padding-left:10px;text-align:center">
                        INFORMATIONS CLIENT
                    </div>
                    <div class="panel-body">
                        <p class="commandes_titre">
                            <?=$c_nom.' '.$c_prenom?>
                        </p>
                        <p>
                            <?php
                                echo "<span class='commandes_icon'><i class='fa fa-map-marker'></i></span>".$c_adresse."<br/>";
                                echo "<span class='commandes_icon'><i class='fa fa-phone'></i></span>".$c_numero."<br/>";
                                echo "<span class='commandes_icon'><i class='fa fa-envelope'></i></span>".$c_email;
                            ?>
                        </p>
                        <hr/>
                        <div class="commandes_commentaire">
                            <?php echo nl2br($c_commentaire); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="panel panel-default" style="min-height: 260px;">
                    <div class="panel-heading" style="padding-left:10px;text-align:center">
                        <?php echo "LIVRAISON PRÉVUE LE ".date("d/m/Y", strtotime($date_debut))." ENTRE ".date("H\Hi", strtotime($date_debut))." ET ".date("H\Hi", strtotime($date_fin))?>
                    </div>
                    <div class="panel-body" style="padding:0px;display:flex;">
                        <div class="col-sm-12 commande-etat" style="padding:0px">
                            <?php 
                            $show_ajoute    = true;
                            $show_reserve   = false;
                            $show_recupere  = false;
                            $show_signe     = false;
                            $show_echec     = false;

                            foreach ($Commande->getAllStatut($id) as $liste_statut) {     
                                if($liste_statut->statut=="ajouté") {
                                    $ajout_date=date("d/m/Y \à H:i", strtotime($liste_statut->date));
                                    $ajout_user=$liste_statut->user_prenom." ".strtoupper($liste_statut->user_nom);
                                }
                                if($liste_statut->statut=="réservé") {
                                    $show_reserve=true;
                                    $reserve_date=date("d/m/Y \à H:i", strtotime($liste_statut->date));
                                    if ($liste_statut->id_user==$liste_statut->id_livreur) {
                                        $reserve_user=$liste_statut->livreur_prenom." ".strtoupper($liste_statut->livreur_nom);
                                    }
                                    else {
                                        $reserve_user=$liste_statut->user_prenom." ".strtoupper($liste_statut->user_nom);
                                    }
                                    
                                }
                                if($liste_statut->statut=="récupéré") {
                                    $show_recupere=true;
                                    $recupere_date=date("d/m/Y \à H:i", strtotime($liste_statut->date));
                                }
                                if($liste_statut->statut=="signé") {
                                    $show_signe=true;
                                    $signe_date=date("d/m/Y \à H:i", strtotime($liste_statut->date));
                                }
                                if($liste_statut->statut=="echec") {
                                    $show_echec=true;
                                    $echec_date=date("d/m/Y \à H:i", strtotime($liste_statut->date));
                                }
                            }

                            if ($ajout_date=="") {
                                $ajout_date=date("d/m/Y \à H:i", strtotime($Commande->getDateAjout()));
                            }

                            if ($reserve_date=="") {
                                $reserve_date=date("d/m/Y \à H:i", strtotime($Commande->getDateStatut()));
                            }

                            if ($recupere_date=="") {
                                $recupere_date=date("d/m/Y \à H:i", strtotime($Commande->getDateStatut()));
                            }

                            if ($signe_date=="") {
                                $signe_date=date("d/m/Y \à H:i", strtotime($Commande->getDateStatut()));
                            }

                            if ($echec_date=="") {
                                $echec_date=date("d/m/Y \à H:i", strtotime($Commande->getDateStatut()));
                            }
                            ?>
                            <div class="col-sm-2" style="padding:0px;">
                                <div class="etat_commande" onclick="<?php if ($show_ajoute) echo 'showDivEtat(\'Ajoutée\')'?>">Ajoutée</div>
                                <div class="etat_commande" onclick="<?php if ($show_reserve) echo 'showDivEtat(\'Réservée\')'?>">Réservée</div>
                                <div class="etat_commande" onclick="<?php if ($show_recupere) echo 'showDivEtat(\'Récupérée\')'?>">Récupérée</div>
                                <div class="etat_commande" onclick="<?php if ($show_echec) echo 'showDivEtat(\'Echec\')'; else if ($show_signe) echo 'showDivEtat(\'Signée\')'; ?>"><?php echo ($show_echec) ? "Echec" : "Signée" ?></div>
                            </div>
                            <div class="col-sm-10" style="padding:15px;border-left:1px solid #d7d7d7;height:100%">
                                <div id="div_ajoute" class="etat_commande_div">
                                    <p class="commandes_titre">AJOUTÉE</p>
                                    <p>Le <?=$ajout_date?> par <?=$ajout_user?></p>
                                    <hr/>
                                    <div class="commandes_commentaire"><?=$commentaire?></div>
                                </div>
                                <div id="div_reserve" class="etat_commande_div">
                                    <p class="commandes_titre">RÉSERVÉE</p>
                                    <p>Le <?=$reserve_date?> par <?=$reserve_user?></p>
                                    <p>Commande affectée à : <?=$u_prenom." ".strtoupper($u_nom)?></p>
                                    <hr/>
                                    <div class="commandes_commentaire"><?=$commentaire?></div>
                                </div>
                                <div id="div_recupere" class="etat_commande_div">
                                    <p class="commandes_titre">RECUPERÉE</p>
                                    <p>Le <?=$recupere_date?> par <?=$u_prenom." ".strtoupper($u_nom)?></p>
                                    <p>Livraison prévue entre <?=date("H:i", strtotime($date_debut))?> et <?=date("H:i", strtotime($date_fin))?></p>
                                    <hr/>
                                    <div class="commandes_commentaire"><?=$commentaire?></div>
                                </div>
                                <div id="div_signe" class="etat_commande_div">
                                    <p class="commandes_titre">SIGNÉE</p>
                                    <p>Commande livrée le <?=$signe_date?> par <?=$u_prenom." ".strtoupper($u_nom)?></p>
                                    <p>
                                        Note du client : <?php
                                        for($x=1;$x<=5;$x++){
                                            if($x<=$Commande->getNote($id)){
                                                $etoile_src = "notation-on.png";
                                            }else{
                                                $etoile_src = "notation-off.png";
                                            }
                                            ?>
                                            <img class="note_etoile" src="images/<?=$etoile_src?>"/>                                
                                            <?php   
                                        }
                                        ?>
                                    </p>
                                    <hr/>
                                    <div style="margin: 0 auto; width: 25%;">
                                        <img src="signature/<?=$signature?>" alt="signature_client" style="width:100%;"/>
                                    </div>
                                </div>
                                <div id="div_echec" class="etat_commande_div">
                                    <p class="commandes_titre">ECHEC</p>
                                    <p>Commande non livrée</p>
                                    <hr/>
                                    <div class="commandes_commentaire"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

		<div class="row">
            <div class="col-sm-12">
            	<div id="map"></div>
            </div>
        </div>

        <div class="row row_btn">
            <div class="col-sm-6 col-sm-offset-6" style="text-align:right">
                <?php
                if($statut=="réservé" && $_SESSION["planner"] && $livreur!=$_SESSION["userid"]){
                    ?>
                    <input type="button" onclick="lien('action_poo.php?action=affecter_livreur&id_commande=<?=$id?>&id_livreur=0&redirect=oui')" id="bt" class="btn btn-bricky" value="J'annule la livraison" style="width:170px;">
                    &nbsp;                                                                          
                    <?php
                }
                ?>

                <?php if($aff_modif){ ?>
                    <input type="button" onclick="lien('commandes_fiche.php?id=<?=$id?>')" id="bt" class="btn btn-main" value="Modifier" style="width:100px;">
                    &nbsp;
                <?php } ?>

                <input type="button" onclick="lien('commandes_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
            </div>
        </div> 		                    
		<!-- end: PAGE CONTENT-->
	</div>
</div>
<!-- end: PAGE -->
<div id="ajax-modal" class="modal fade" tabindex="-1" style="display: none;"></div>
		
<?php
require_once("inc_footer.php");
?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC15w0ru2bvazBjNdaHtVLXngRT6JfSh2s"></script>
<script type="text/javascript" src="./assets/js/gmaps.js"></script>

<script src="assets/plugins/bootstrap-modal/js/bootstrap-modal.js"></script>
<script src="assets/plugins/bootstrap-modal/js/bootstrap-modalmanager.js"></script>

<script type="text/javascript">
    var map;
    $(document).ready(function(){
        map = new GMaps({
            el: '#map',
            zoom: 13,
            lat: <?=$r_latitude?>,
            lng: <?=$r_longitude?>
        });
        map.drawRoute({
            origin: [<?=$r_latitude.', '.$r_longitude?>],
            destination: [<?=$c_latitude.', '.$c_longitude?>],
            travelMode: 'driving',
            strokeColor: '#131540',
            strokeOpacity: 0.8,
            strokeWeight: 6
        });
        map.addMarker({
            lat: <?=$r_latitude?>,
            lng: <?=$r_longitude?>,
            icon: 'images/start.png'
        });	  
        map.addMarker({
            lat: <?=$c_latitude?>,
            lng: <?=$c_longitude?>,
            icon: 'images/end.png'
        });	  

        initModals();

        showDivEtat('<?=ucfirst(txt_statut($statut))?>');
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

    function showDivEtat(etat) {
        $(".etat_commande_div").each(function() {
            $(this).hide();
            switch (etat) {
                case "Ajoutée":
                    $("#div_ajoute").show();
                    break;
                case "Réservée":
                    $("#div_reserve").show();
                    break;
                case "Récupérée":
                    $("#div_recupere").show();
                    break;
                case "Signée":
                    $("#div_signe").show();
                    break;
                case "Echec":
                    $("#div_echec").show();
                    break;
            }
        });

        $(".etat_commande").each(function() {
            $(this).removeClass("etat_commande_actif");
        });
        $(".etat_commande:contains("+etat+")").addClass("etat_commande_actif");
    }

</script>
