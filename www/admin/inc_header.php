<?php
require_once("inc_connexion.php");

if(!$_SESSION["acces"]){
	if(isset($_GET["id"]))		{$cmd=$_GET["id"];}else{$cmd="";}
	
	header("location: index.php?action=deconnexion&cmd=".$cmd);	
}
else if ($_SESSION["userid"]!=""){
	$result2 = $sql->query("SELECT * FROM utilisateurs WHERE id=".$sql->quote($_SESSION["userid"])." AND statut IN ('ON', 'OFF')");
	$ligne2 = $result2->fetch();
	if($ligne2){
	    $req = $sql->exec("UPDATE utilisateurs SET statut = 'ON', date_conn = NOW() WHERE id = '".$_SESSION["membreid"]."'"); 
	}
}


$menu_live = "";
$ssmenu_live_coordination = "";
$ssmenu_live_dashboard = "";

$menu_client = "";
$ssmenu_client_liste = "";
$ssmenu_client_fiche = "";

$menu_resto = "";
$ssmenu_resto_liste = "";
$ssmenu_resto_fiche = "";

$menu_commande = "";
$ssmenu_commande_liste = "";
$ssmenu_commande_histo = "";
$ssmenu_commande_fiche = "";


$menu_materiel = "";
$ssmenu_materiels_liste = "";
$ssmenu_materiels_fiche = "";

$menu_phone = "";

$menu_notaires = "";
$menu_mails = "";
$menu_campagnes = "";
$menu_compte = "";
$ssmenu_notaires_liste = "";
$ssmenu_mails_liste = "";
$ssmenu_campagnes_liste = "";

switch($menu){
	case "live":
		$menu_live = "active open";
		switch ($sous_menu){
            case "coordination":
                $ssmenu_live_coordination    = "active open";
		        break;

            case "dashboard":
                $ssmenu_live_dashboard       = "active open";
		        break;
        }
		break;

	case "resto":
		$menu_resto = "active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_resto_liste     = "active open";
				break;
			case "fiche":
				$ssmenu_resto_fiche     = "active open";
				break;
		}
		break;

	case "client":
		$menu_client = "active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_client_liste    = "active open";
				break;
			case "fiche":
				$ssmenu_client_fiche    = "active open";
				break;
		}
		break;

	case "vehicule":
		$menu_vehicule = "active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_vehicule_liste  = "active open";
				break;

            case "operation-liste":
                $ssmenu_vehicule_operation_liste = "active open";
                break;

            case "operation":
                $ssmenu_vehicule_operation = "active open";
                break;

			case "fiche":
				$ssmenu_vehicule_fiche = "active open";	
				break;
		}
		break;

	case "livreur":
		$menu_livreur = "active open";
		switch($sous_menu){
			case "planning";
				$ssmenu_livreur_planning = "active open";
				break;
			case "liste":
				$ssmenu_livreur_liste = "active open";	
				break;
			case "fiche":
				$ssmenu_livreur_fiche = "active open";	
				break;
			case "upload":
				$ssmenu_livreur_upload = "active open";	
				break;
		}
		break;

	case "commande":
		$menu_commande = "active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_commande_liste = "active open";	
				break;
			case "histo":
				$ssmenu_commande_histo = "active open";	
				break;
			case "fiche":
				$ssmenu_commande_fiche = "active open";	
				break;
			case "upload":
				$ssmenu_commande_upload = "active open";	
				break;
		}
		break;

    case "materiel" :
        $menu_materiel = "active open";
        switch ($sous_menu){
            case "liste":
                $ssmenu_materiels_piece = "active open";
                break;
            case "fiche":
                $ssmenu_materiels_fiche = "active open";
                break;
        }
        break;

    case "phone":
        $menu_phone = "active open";
        break;

	case "notif":
		$menu_notif="active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_notif_liste = "active open";	
				break;
			case "fiche":
				$ssmenu_notif_fiche = "active open";	
				break;
		}
		break;

	case "notaires":
		$menu_notaires = "active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_notaires_liste = "active open";	
				break;
		}		
		break;
	case "mails":
		$menu_mails = "active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_mails_liste = "active open";	
				break;
			case "fiche":
				$ssmenu_mails_fiche = "active open";	
				break;
		}
		break;
	case "campagnes":
		$menu_campagnes = "active open";
		switch($sous_menu){
			case "liste":
				$ssmenu_campagnes_liste = "active open";	
				break;
			case "fiche":
				$ssmenu_campagnes_fiche = "active open";	
				break;
		}
		break;
	case "compte":
		$menu_compte = "active open";
		break;
	case "operations":
		$menu_operations = "active open";
		break;
	case "statistiques":
		$menu_statistiques = "active open";
		break;

	case "actualite":
		$menu_actualite = "active open";
		switch($sous_menu){
			case "fiche":
				$ssmenu_actualite_ajout = "active open";	
				break;
			case "liste":
				$ssmenu_actualite_liste = "active open";	
				break;
		}
		break;
}

if($_SESSION["role"]=="restaurateur"){
	$txt_role = "Commerçant";
}else{
	$txt_role = $_SESSION["role"];	
}
?>
<!DOCTYPE html>
<!-- Template Name: Clip-One - Responsive Admin Template build with Twitter Bootstrap 3.x Version: 1.3 Author: ClipTheme -->
<!--[if IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
	<!--<![endif]-->
	<!-- start: HEAD --><head>
		<title>youOrder - Interface d'administration</title>
		<!-- start: META -->
		<meta charset="utf-8" />
		<!--[if IE]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta content="" name="description" />
		<meta content="" name="author" />
		<!-- end: META -->
		<!-- start: MAIN CSS -->
		<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="assets/plugins/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="assets/fonts/style.css">
		<link rel="stylesheet" href="assets/css/main.css">
		<link rel="stylesheet" href="assets/css/main-responsive.css">
		<link rel="stylesheet" href="assets/plugins/iCheck/skins/all.css">
		<link rel="stylesheet" href="assets/plugins/bootstrap-colorpalette/css/bootstrap-colorpalette.css">
		<link rel="stylesheet" href="assets/plugins/perfect-scrollbar/src/perfect-scrollbar.css">
		<link rel="stylesheet" href="assets/css/theme_light.css" type="text/css" id="skin_color">
		<link rel="stylesheet" href="assets/css/print.css" type="text/css" media="print"/>
		<link rel="stylesheet" type="text/css" href="assets/css/magnific-popup.css">
        <link rel="stylesheet" href="assets/css/multi-select.css">
                <link rel="icon" type="image/png" href="../image/youOrder_Logo.png">
		<!--[if IE 7]>
		<link rel="stylesheet" href="assets/plugins/font-awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		<!-- end: MAIN CSS -->
		<!-- start: CSS REQUIRED FOR THIS PAGE ONLY -->
		<!-- end: CSS REQUIRED FOR THIS PAGE ONLY -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
	<!-- end: HEAD -->
	<!-- start: BODY -->
    
	<style>
		.well {
			padding: 5px 19px;
		}
                
                .notif-header{
                    position:relative;
                    float:left;
                    padding:16px 10px;
                    cursor: pointer;
                    margin-right: 35px;
                }
                
                .notif-header i{
                    font-size:30px;
                    color: grey;
                }
                
                .notif-header i:hover{color:#666666;}
                
                .nbr-notif{
                    position:absolute;
                    display:block;
                    font-size:10px;
                    top:12px;
                    left:0px;
                    background-color:#9fc752;
                    color:#FFF;
                    border-radius:50%;
                    width: 20px;
                    height:20px;
                    text-align:center;
                    padding-top:0 !important;
                    line-height:20px;
                }
                
                #liste-notif{
                    display:none;
                    position:absolute;
                    height:277px;
                    width:250px;
                    background-color:#F3F3F3;
                    top:66px;
                    right:0;
                    overflow-y:auto;
                    border-left:1px solid #d9d9d9;
                    border-bottom:1px solid #d9d9d9;
                    border-right:1px solid #d9d9d9;
                }
                
                @media(min-width:1024px){
                    #liste-notif{
                        width:515px;
                    }
                }
                
                .notif-title{
                    background-color: #d9d9d9;
                    color: #555;
                    font-weight:700;
                    padding: 5px 10px;
                }
                
                .notif-detail:hover{background-color:#f7f7f7;}
                
                .notif-detail i{
                    padding:6px 4px;
                    font-size:16px;
                    color: #9fc752;
                }
                
                .notif-detail span{
                    display: inline-block;
                    clear:both;
                    float:none;
                    padding:6px 4px;
                    max-width: 65%;
                }
                
                .notif-date{
                    color: grey;
                    font-weight: 700;
                }
                
                .notif-detail{
                    border-bottom: 1px solid #d9d9d9;
                    background-color:#FFF;
                    display:block;
                    text-decoration:none;
                    color:#000;
                }
                
                .notif-detail:hover,
                .notif-detail:active,
                .notif-detail:focus{
                    color:#000;
                    text-decoration:none;
                }
                
                #notif-header:hover{background-color:#d9d9d9;}
                
                #notif-header:hover #liste-notif{
                    display:block;
                }
                
                .notif-block{
                    display:block !important;}
                
                .hover-bgc{
                    background-color: #d9d9d9;         
                }
                
                .close-notif{
                    display:block;
                    float:right;
                    font-size:12px !important;
                    color:#000 !important;
                    opacity:0.2;
                    padding-top:9px !important;
                }
                
                .close-notif:hover{opacity:0.5;}
                
                @media (max-width:500px){
                    #notif-header{margin-right:20px;}
                    #liste-notif{left:0;}
                }
	</style>
	<body>
            
            <div style="display:none;">
                <a class="pop-up-generique" href=""></a>
            </div>
            
            <!-- header -->
            <div class="navbar navbar-inverse navbar-fixed-top">
                
                <div class="container" style="background-color:#FFF;">
                    <div class="navbar-header">
                        <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                            <span class="clip-list-2"></span>
                        </button>
                        <a class="navbar-brand" href="home.php">
                            <img src="images/logo_youorder_new.png" alt="youOrder">
                        </a>
                    </div>
                    <div class="navbar-header-right">
                        <!--accessible seulement aux admins et planners-->
                        <?php if($_SESSION["planner"]){ ?>
                            <div class="notif-header" id="notif-header">
                                <i class="clip-notification-2"></i>
                                <span class="nbr-notif nb_notif_txt"></span>
                                <div id="liste-notif">
                                    <div class="notif-title"></div>
                                    <div id="liste_notif"></div>
                                </div>
                            </div>
                        <?php } ?>
                        <span>Bonjour, <?php echo $_SESSION["login"] ?> (<?=ucfirst($txt_role);?>)</span> 
                        <a class="image-popup-vertical-fit" href="<?=$_SESSION["photo"]?>">
                            <div class="avatar_3" style="background:url('<?=$_SESSION["photo"]?>') center center no-repeat;"></div>
                        </a>
                        <div style="clear:both;"></div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
                    
            </div>
            <!-- /header -->
                
		<!-- start: MAIN CONTAINER -->
		<div class="main-container">
			<div class="navbar-content">
				<!-- start: SIDEBAR -->
				<div class="main-navigation navbar-collapse collapse">
					<!-- start: MAIN MENU TOGGLER BUTTON -->
					<div class="navigation-toggler">
						<i class="clip-chevron-left"></i>
						<i class="clip-chevron-right"></i>
					</div>
					<!-- end: MAIN MENU TOGGLER BUTTON -->
					<!-- start: MAIN NAVIGATION MENU -->
					<ul class="main-navigation-menu">
                    	<?php if($_SESSION["role"]!="restaurateur"){ ?>
                            <li class="<?php echo $menu_live ?>">
                                <a href="javascript:void(0)"><i class="clip-home-3"></i>
                                    <span class="title"> LIVE </span><i class="icon-arrow"></i>
                                    <span class="selected"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="<?php echo $ssmenu_live_coordination ; ?>">
                                        <a href="coordination.php">
                                            <span class="active title"> La coordination </span>
                                        </a>
                                    </li>
                                    <li class="<?php echo $ssmenu_live_dashboard; ?>">
                                        <a href="home.php">
                                            <span class="title"> Tableau de Bord </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php } else { ?>
                            <li class="<?php echo $menu_live ?>">
                                <a href="javascript:void(0)"><i class="clip-home-3"></i>
                                    <span class="title"> LIVE </span><i class="icon-arrow"></i>
                                    <span class="selected"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="<?php echo $ssmenu_live_coordination ; ?>">
                                        <a href="coordination.php">
                                            <span class="active title"> Vos livreurs </span>
                                        </a>
                                    </li>
                                    <li class="<?php echo $ssmenu_live_dashboard; ?>">
                                        <a href="home.php">
                                            <span class="title"> Tableau de bord </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        <?php } ?>
                        <?php if($_SESSION["planner"]){ ?>
                        <li class="<?php echo $menu_resto ?>">
							<a href="javascript:void(0)"><i class="fa fa-cutlery"></i>
								<span class="title"> Commerçants </span><i class="icon-arrow"></i>
								<span class="selected"></span>
							</a>
							<ul class="sub-menu">
								<li class="<?php echo $ssmenu_resto_liste; ?>">
									<a href="restaurants_liste.php">
										<span class="active title"> Liste des commerçants </span>
									</a>
								</li>
								<li class="<?php echo $ssmenu_resto_fiche; ?>">
									<a href="restaurants_fiche.php">
										<span class="title"> Nouveau commerçant </span>
									</a>
								</li>
							</ul>
						</li>
                        <?php } else if ($_SESSION["restaurateur"]) {?>
                            <li class="<?php echo $menu_resto ?>">
                                <a href="javascript:void(0)"><i class="fa fa-cutlery"></i>
                                    <span class="title"> Commerces </span><i class="icon-arrow"></i>
                                    <span class="selected"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="<?php echo $ssmenu_resto_liste; ?>">
                                        <a href="restaurants_liste.php">
                                            <span class="active title"> Liste des commerces </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php } ?>
                    	<?php if($_SESSION["role"]!="livreur"){ ?>                        
                        <li class="<?php echo $menu_client ?>">
							<a href="javascript:void(0)"><i class="clip-users"></i>
								<span class="title"> Clients </span><i class="icon-arrow"></i>
								<span class="selected"></span>
							</a>
							<ul class="sub-menu">
								<li class="<?php echo $ssmenu_client_liste; ?>">
									<a href="clients_liste.php">
										<span class="active title"> Liste des clients </span>
									</a>
								</li>
								<li class="<?php echo $ssmenu_client_fiche; ?>">
									<a href="clients_fiche.php">
										<span class="title"> Nouveau client </span>
									</a>
								</li>
							</ul>
						</li>
                        <?php } ?>
                        <?php if($_SESSION["planner"]){ ?>
                            <li class="<?php echo $menu_vehicule ?>">
                                <a href="javascript:void(0)"><i class="fa fa-motorcycle"></i>
                                    <span class="title"> Véhicules </span><i class="icon-arrow"></i>
                                    <span class="selected"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="<?php echo $ssmenu_vehicule_liste; ?>">
                                        <a href="vehicules_liste.php">
                                            <span class="active title"> Liste des véhicules </span>
                                        </a>
                                    </li>
                                    <li class="<?php echo $ssmenu_vehicule_operation_liste; ?>">
                                        <a href="vehicule_operation_liste.php">
                                            <span class="title"> Listes des opérations </span>
                                        </a>
                                    </li>
                                    <li class="<?php echo $ssmenu_vehicule_operation; ?>">
                                        <a href="vehicule_operation_fiche.php">
                                            <span class="title"> Effectuer une opération </span>
                                        </a>
                                    </li>
                                    <li class="<?php echo $ssmenu_vehicule_fiche; ?>">
                                        <a href="vehicules_fiche.php">
                                            <span class="title"> Nouveau véhicule </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
						<li class="<?php echo $menu_livreur ?>">
							<a href="javascript:void(0)"><i class="fa fa-street-view "></i>
								<span class="title"> Livreurs </span><i class="icon-arrow"></i>
								<span class="selected"></span>
							</a>
							<ul class="sub-menu">
								<li class="<?php echo $ssmenu_livreur_planning; ?>">
									<a href="livreurs_planning.php">
										<span class="active title"> Planning </span>
									</a>
								</li>
								<li class="<?php echo $ssmenu_livreur_upload; ?>">
									<a href="livreurs_planning_upload.php">
										<span class="active title"> Upload de planning </span>
									</a>
								</li>
								<li class="<?php echo $ssmenu_livreur_liste; ?>">
									<a href="livreurs_liste.php">
										<span class="active title"> Liste des livreurs </span>
									</a>
								</li>
								<li class="<?php echo $ssmenu_livreur_fiche; ?>">
									<a href="livreurs_fiche.php">
										<span class="title"> Nouveau livreur </span>
									</a>
								</li>
							</ul>
						</li>
                        <?php } ?>
                        <?php if ($_SESSION["restaurateur"] && $_SESSION["planning_livreur"]) { ?>
							<li class="<?php echo $menu_livreur ?>">
								<a href="livreurs_planning.php"><i class="clip-calendar"></i>
									<span class="title"> Planning </span><i class="icon-arrow"></i>
									<span class="selected"></span>
								</a>
							</li>
						<?php  } ?>
                        <li class="<?php echo $menu_commande ?>">
							<a href="javascript:void(0)"><i class="clip-list"></i>
								<span class="title"> Commandes </span><i class="icon-arrow"></i>
								<span class="selected"></span>
							</a>
							<ul class="sub-menu">
								<?php if ($_SESSION["affecter_commande"]) { ?>
									<li class="<?php echo $ssmenu_commande_affecter; ?>">
										<a href="commandes_affecter.php">
											<span class="active title"> Affecter une commande </span>
										</a>
									</li>
								<?php } ?>
								<li class="<?php echo $ssmenu_commande_liste; ?>">
									<a href="commandes_liste.php">
										<span class="active title"> Liste des commandes </span>
									</a>
								</li>
								<li class="<?php echo $ssmenu_commande_histo; ?>">
									<a href="commandes_histo.php">
										<span class="active title"> Historique des commandes </span>
									</a>
								</li>
                                <?php if($_SESSION["role"]!="livreur"){ ?>     
                                    <li class="<?php echo $ssmenu_commande_fiche; ?>">
                                        <a href="commandes_fiche.php">
                                            <span class="title"> Nouvelle commande </span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION["planner"]){ ?>
                                	<li class="<?php echo $ssmenu_commande_upload; ?>">
                                        <a href="commandes_upload.php">
                                            <span class="title"> Upload de commandes </span>
                                        </a>
                                    </li>
                                <?php } ?>
							</ul>
						</li>

                        <?php if($_SESSION["admin"]){?>
                            <li class="<?php echo $menu_materiel ?>">
                                <a href="javascript:void(0)"><i class="fa fa-wrench"></i>
                                    <span class="title"> Gestion du stock </span><i class="icon-arrow"></i>
                                    <span class="selected"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="<?php echo $ssmenu_materiels_piece?>">
                                        <a href="piece_liste.php">
                                            <span class="active title"> Liste des pieces </span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="sub-menu">
                                    <li class="<?php echo $ssmenu_materiels_fiche?>">
                                        <a href="piece_fiche.php">
                                            <span class="active title"> Ajouter une piece </span>
                                        </a>
                                    </li>
                                </ul>

                            </li>
                        <?php } ?>

                        <?php if($_SESSION["admin"]){?>
                            <li class="<?php echo $menu_phone; ?>">
                                <a href="phone_liste.php"><i class="fa fa-phone" aria-hidden="true"></i>
                                    <span class="title"> Gestion des téléphones </span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if($_SESSION["admin"]){ ?>
                        <li class="<?php echo $menu_notif ?>">
							<a href="javascript:void(0)"><i class="fa fa-bell-o"></i>
								<span class="title"> Notifications </span><i class="icon-arrow"></i>
								<span class="selected"></span>
							</a>
							<ul class="sub-menu">
								<li class="<?php echo $ssmenu_notif_liste; ?>">
									<a href="notification_liste.php">
										<span class="active title"> Liste des notifications </span>
									</a>
								</li>    
                                <li class="<?php echo $ssmenu_notif_fiche; ?>">
                                    <a href="notification_fiche.php">
                                        <span class="title"> Nouvelle notification </span>
                                    </a>
                                </li>
							</ul>
						</li>

						<li class="<?php echo $menu_compte; ?>">
							<a href="administration.php"><i class="clip-user-3"></i>
								<span class="title"> Administration </span>
                                <span class="selected"></span>
							</a>
						</li>
                        <?php } ?>
                        <?php if($_SESSION["role"]=="restaurateur"){ ?>
						<li class="<?php echo $menu_compte; ?>">
							<a href="administration_fiche.php?id=<?=$_SESSION["userid"];?>"><i class="clip-user-3"></i>
								<span class="title"> Mon compte </span>
                                <span class="selected"></span>
							</a>
						</li>
                        <?php } ?>
                        <?php if ($_SESSION["admin"]) { ?>
                        <li class="<?php echo $menu_actualite ?>">
							<a href="javascript:void(0)"><i class="clip-pencil-3"></i>
								<span class="title"> Actualités </span><i class="icon-arrow"></i>
								<span class="selected"></span>
							</a>
							<ul class="sub-menu">
								<li class="<?php echo $ssmenu_actualite_ajout; ?>">
									<a href="actualite_fiche.php">
										<span class="active title"> Ajouter </span>
									</a>
								</li>
								<li class="<?php echo $ssmenu_actualite_liste; ?>">
									<a href="actualite_liste.php">
										<span class="title"> Liste </span>
									</a>
								</li>
							</ul>
						</li>
                        <?php } ?>
						<li>
							<a href="index.php?action=deconnexion"><i class="clip-exit"></i>
								<span class="title"> Déconnexion </span>
                                <span class="selected"></span>
							</a>
						</li>
					</ul>
					<!-- end: MAIN NAVIGATION MENU -->
				</div>
				<!-- end: SIDEBAR -->
			</div>

			<!-- START BALISE POUR SON NOTIF -->
			<audio id="audio_notif" src="sounds/son_bip_bip.wav" preload="auto"></audio>
			<!-- END BALISE POUR SON NOTIF -->
                    
            <script>
                
                if ('<?=$_SESSION["admin"]?>') {
	                var notif_header = document.getElementById('notif-header');
	                var liste_notif = document.getElementById('liste-notif');
	                
	                notif_header.addEventListener('click', show_notif);
	                
	                function show_notif(){
	                    notif_header.classList.toggle('hover-bgc');
	                    liste_notif.classList.toggle("notif-block");
	                }   
	            }            
            </script>
