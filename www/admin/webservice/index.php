
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no, maximum-scale=1.0, minimum-scale=1.0" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">

	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//maps.google.com/maps/api/js?v=3&sensor=false&libraries=adsense"></script>
    <script src="js/maps.js"></script>
    <script src="js/leaflet-0.7.3/leaflet.js"></script>
    <script src="js/leaflet-plugins/google.js"></script>
    <script src="js/leaflet-plugins/bing.js"></script>
    <link rel="stylesheet" href="js/leaflet-0.7.3/leaflet.css">
        
	<script type='text/javascript' src="js/script.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/popup.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />

	<script type='text/javascript' src="js/jquery.magnific-popup.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/magnific-popup.css" />

  <link rel="stylesheet" href="css/idangerous.swiper.css?ts=1">

	<title>Titre de la page</title>
	<meta name="description" content="Description de la page">
	<style>
		header{
			text-align:center;
			line-height:45px;
			font-weight:bold;
			position:fixed;
			top:0;
			left:0;
			width:100%;
			background-color:#CCCCCC;
			background: linear-gradient(#dadada, #b3b3b3) repeat scroll 0 0 rgba(0, 0, 0, 0);
			border-bottom: 1px solid #6b6b6b;
			box-shadow: 0 0 10px #040505;
			height: 44px;	
			z-index:99999999999999999999999999999999999999999;		
		}

		footer{
			text-align:center;
			line-height:45px;
			font-weight:bold;
			position:fixed;
			bottom:0;
			left:0;
			width:100%;
			background-color:#CCCCCC;
			background: linear-gradient(#dadada, #b3b3b3) repeat scroll 0 0 rgba(0, 0, 0, 0);
			border-bottom: 1px solid #6b6b6b;
			box-shadow: 0 0 10px #040505;
			height: 44px;			
		}
		#body{
			margin-top:45px;
		}
		
		header .logo{
			float:left;
			width:50%;
			
		}
		
		h1{
			margin:0;
			padding:0;
			font-size:16px;
			font-family:helvetica;			
		}
		h2{
			margin:0;
			padding:0;
			font-size:14px;
			font-family:helvetica;			
		}
		
		.prix{
			margin:0;
			padding:0;
			font-size:16px;
			font-family:helvetica;	
			width:100px;
			float:right;		
			background-color:#CCCCCC;
			text-align:center;
			font-weight:bold;
			padding:5px 10px 5px 10px;
			border-radius:10px 10px 0px 0px;
			box-shadow: -1px 2px 5px 1px rgba(0, 0, 0, 0.4);
		}
		.titre{
			height:30px;
			line-height:30px;
			margin-top:8px;
			margin-bottom:8px;
			font-weight:bold;
			font-size:13px;
			padding-left:10px;
			background-color:#CCCCCC;
			border-top:solid 1px #333333;
			border-bottom:solid 1px #333333;
		}
		
		.bouton-gauche-blanc-new {
			background: linear-gradient(#FDFDFD, #EAEAEA) repeat scroll 0 0 rgba(0, 0, 0, 0);
			border-radius: 3px;
			box-shadow: 0 0 0 1px #B4B4B4 inset, 0 2px 3px 0 rgba(113, 113, 113, 0.4);
			color: #CC0022;
			float: left;
			font-size: 13px;
			font-weight: normal;
			padding: 6px 0 7px;
			text-align: center;
			text-decoration: none;
			width: 100%;
		}		
	</style>
	<style>
.swiper-container {
  width: 100%;
  height: 240px;
  color: #fff;
  text-align: center;
}
.swiper-slide .title {
  font-style: italic;
  font-size: 42px;
  margin-top: 80px;
  margin-bottom: 0;
  line-height: 45px;
}
.pagination {
  position: absolute;
  z-index: 20;
  right: 10px;
  bottom: 10px;
}
.swiper-pagination-switch {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 8px;
  background: #222;
  margin-right: 5px;
  opacity: 0.8;
  border: 1px solid #fff;
  cursor: pointer;
}
.swiper-visible-switch {
  background: #aaa;
}
.swiper-active-switch {
  background: #fff;
}
	
	</style>
	<!-- http://www.seloger.com/annonces/achat/maison/cannes-06/croix-des-gardes/83156709.htm?refonte2013=2&contact=0#sc_referrer= -->
<script language="javascript">
$(document).ready(function() {
	$('.popup-with-form').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#name',

		// When elemened is focused, some mobile browsers in some cases zoom in
		// It looks not nice, so we disable it:
		callbacks: {
			beforeOpen: function() {
				if($(window).width() < 700) {
					this.st.focus = false;
				} else {
					this.st.focus = '#name';
				}
			}
		}
	});
	$(document).on('click', '.popup-modal-dismiss', function (e) {
		e.preventDefault();
		$.magnificPopup.close();
	});	
});	

function geolocalisation(){
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(
					function(position){
						  var latitude = position.coords.latitude;
						  var longitude = position.coords.longitude;
						  openPopup("#popup-geoloc");
					},
					function(){
					}					
		);
	}else{
		//alert('erreur de localisation')	
	}

}

function openPopup(lien_popup){
	//lien_popup = "popup_zoom.php?m="+membreid+"&p="+photoid;
	$(".pop-up-generique").attr("href", lien_popup)
	$('.pop-up-generique').magnificPopup({
		type: 'inline',
		modal: true,
		fixedBgPos:true,
		fixedContentPos:true,
		overflowY: 'scroll'
	}).magnificPopup('open');			
	
}
<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}

if(isset($_GET["resto"]))		{$restaurant=$_GET["resto"];}else{$restaurant="";}
if(isset($_GET["client"]))		{$client=$_GET["client"];}else{$client="";}

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
        $couleur_statut = couleur_statut($statut);
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


?>



</script>

	<a class="pop-up-generique" href="popup_generique.php"></a>
		<div class="logo">
			<img src="images/logo_globimmo.png" style="max-height:40px;padding-top:2px;" />
		</div>
		<div style="width:15%;float:right;padding-top:5px;"><img src="images/picto_search.png" height="35" /></div>		
		<div style="width:15%;float:right;padding-top:5px;"><img src="images/picto_liste.png" height="35" /></div>
		<div style="width:15%;float:right;padding-top:5px;"><img src="images/picto_home.png" height="35" /></div>
	</header>
	<div id="body" class="div_320_extensible">
		<div class="espace"></div>
		<div class="content">
		  <h1>Vente Maison / Villa 90m² Cannes</h1>
		  <h2>Cannes - Croix des Gardes</h2>
		  <div class="prix">415 000 €</div>
		</div>
		<div class="clear"></div>
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<div class="swiper-slide"><div style="background:url('images/photo1.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
				<div class="swiper-slide"><div style="background:url('images/photo2.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
				<div class="swiper-slide"><div style="background:url('images/photo3.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
				<div class="swiper-slide"><div style="background:url('images/photo4.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
				<div class="swiper-slide"><div style="background:url('images/photo5.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
				<div class="swiper-slide"><div style="background:url('images/photo6.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
				<div class="swiper-slide"><div style="background:url('images/photo7.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
				<div class="swiper-slide"><div style="background:url('images/photo8.jpg') center center;max-width:640px;height:240px;background-size:640px;"></div></div>
			</div>
			<div class="pagination"></div>
		</div>
		<div style="padding:10px 0px;">
			<div class="content">
				Au pied de la croix des gardes, a 5mn des plages, dans petit domaine ferme avec piscine, au calme, très belle maison de 90m², 3 chambres, garage fermé et parking privatif - contactez votre agent local galli christophe à cannes agent mandataire du premier réseau immobilier à domicile optimhome au 06.12.34.56.78 - (réf. XXXXXX).			
			</div>
		</div>
		<div class="titre">Caractéristiques</div>
		<div class="content">
			<div style="float:left;width:50%;line-height:20px;">
				<b>Surface : </b>68m2 <br/>
				<b>Nb de pièces :</b> 4<br/>
				<b>Chanbres :</b> 2<br/>
				<b>Salle de bain :</b> 1<br/>
				<b>Toilette :</b> 1<br/>
				<b>Balcon : </b> 1<br/>
				<b>Etage :</b> 11<br/>
			</div>
			<div style="float:left;width:50%;line-height:20px;">
				Ascenceur<br/>
				Cave<br/>
				Balcon<br/>
				Gardien<br/>
				Interphone<br/>
				Vue<br/>
			</div>
		</div>
		<div class="clear"></div>
		<div class="titre" style="margin-bottom:0px;">Localisation</div>
		<div style="position:relative">
			<div style="position:absolute;width:100%;height:100%;background:url('images/cercle_carte.png') center center no-repeat;"></div>
              <div id="map-canvas"></div>
			<iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.fr/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;ll=48.884304,2.297945&amp;spn=0.033864,0.036478&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
		</div>
		<div class="clear"></div>
		<div class="content">
			<div style="margin-top:5px;" class="bouton-gauche-blanc-new">Découvrir les autres biens autour de vous</div>
		</div>
		<div class="clear"></div>
		<div class="titre">Contact</div>
		<div class="content">
			<div style="float:left;width:50%;line-height:20px;">
				<b>Agence Glob'Immo</b><br/>
				M. Gilles Bernard<br/>
				34 Boulevard Voltaire<br/>
				75004 Paris<br/>
				06.12.34.56.78<br/>
			</div>
			<div style="float:left;width:50%;line-height:20px;padding-top:10px;">
				
				<a class="popup-with-form" href="#form-rappel-immediat"><div class="bouton-gauche-blanc-new">Rappel immédiat</div></a>
				<div class="clear"></div>
				<div class="espace"></div>
				<a class="popup-with-form" href="#form-demande-rdv"><div class="bouton-gauche-blanc-new">Demander un RDV</div></a>
				
			</div>
		</div>
		<div class="clear"></div>
		<div class="titre">DPE</div>
		<div class="content">
			Consommation énergetique de ce bien : <br/>
			Classe D - 195 kWhEP/m².an<br/>
			<img src="images/dpe_D.jpg" />
		</div>
		
	</div>
	<div class="espace"></div>
	<div class="espace"></div>
	<div class="espace"></div>
	<div class="espace"></div>
	<footer>
		<div style="width:25%;float:left;padding-top:5px;" onclick="geolocalisation()"><img src="images/picto_geoloc.png" height="35" /></div>
		<div style="width:25%;float:left;padding-top:5px;"><a href="tel:0139704079"><img src="images/picto_tel.png" height="35" border="0" /></a></div>
		<div style="width:25%;float:left;padding-top:5px;"><a class="popup-with-form" href="#test-form"><img src="images/picto_mail.png" height="35" border="0" /></a></div>
		<div style="width:25%;float:left;padding-top:5px;"><a class="popup-with-form" href="#form-question"><img src="images/picto_quest.png" height="35" /></a></div>
	</footer>	


<form id="test-form" class="white-popup-block mfp-hide">
	<div id="body_popup">
	<div class="padding_3">
		<div class="fond_popup">    
			<!-- titre et croix -->
			<div class="border_bottom_1">
				<div class="span_3 float_left margin_8">Transferer la fiche par mail</div>
				<div class="popup-modal-dismiss float_right"><img class="padding_4" src="images/croix_rouge.png" width="26" height="27" /></div>
				<div class="clear"></div>
			</div>
			<div class="padding_5 border_top_1">
				<div class="span_4 text_align_center">
				 Envoyez cette annonce par email pour la consulter à la maison ou avec vos proches<br/>
				<div class="margin_15">   
					<input type="text" name="email" id="email" value="" class="champ-2" placeholder="Votre email" />
				</div>
					
				</div>
				<!-- boutons bas -->
				<div class="margin_6"></div>
					<div class="bouton-gauche">
					 <div class="popup-modal-dismiss bouton-gauche-blanc">Annuler</div>
					</div>
					<div onclick="formNote.submit()" class="bouton-droite">
					 <div class="bouton-droite-rouge">Envoyer</div>
					</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	</div>
</form>

<form id="form-rappel-immediat" class="white-popup-block mfp-hide">
	<div id="body_popup">
	<div class="padding_3">
		<div class="fond_popup">    
			<!-- titre et croix -->
			<div class="border_bottom_1">
				<div class="span_3 float_left margin_8">Rappel immédiat</div>
				<div class="popup-modal-dismiss float_right"><img class="padding_4" src="images/croix_rouge.png" width="26" height="27" /></div>
				<div class="clear"></div>
			</div>
			<div class="padding_5 border_top_1">
				<div class="span_4 text_align_center">
				Vous avez un coup de coeur et vous souhaitez programmer une visite ou demander une information importante, demandez le rappel immédiat en renseignant les champs ci-dessous<br/>
				<div class="margin_15">   
					<input type="text" name="nom" id="nom" value="" class="champ-2" placeholder="Votre nom" />
				</div>
				<div class="margin_15">   
					<input type="text" name="numero" id="numero" value="" class="champ-2" placeholder="Votre numéro de mobile" />
				</div>
					
				</div>
				<!-- boutons bas -->
				<div class="margin_6"></div>
					<div class="bouton-gauche">
					 <div class="popup-modal-dismiss bouton-gauche-blanc">Annuler</div>
					</div>
					<div onclick="formNote.submit()" class="bouton-droite">
					 <div class="bouton-droite-rouge">Envoyer</div>
					</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	</div>
</form>

<form id="form-demande-rdv" class="white-popup-block mfp-hide">
	<div id="body_popup">
	<div class="padding_3">
		<div class="fond_popup">    
			<!-- titre et croix -->
			<div class="border_bottom_1">
				<div class="span_3 float_left margin_8">Demande de RDV</div>
				<div class="popup-modal-dismiss float_right"><img class="padding_4" src="images/croix_rouge.png" width="26" height="27" /></div>
				<div class="clear"></div>
			</div>
			<div class="padding_5 border_top_1">
				<div class="span_4 text_align_center">
				Vous êtes intéressé par le produit et vous souhaitez programmer une visite, demandez un rendez-vous en renseignant les champs ci-dessous<br/>
				<div class="margin_15">   
					<input type="text" name="nom" id="nom" value="" class="champ-2" placeholder="Votre nom" />
				</div>
				<div class="margin_15">   
					<input type="text" name="numero" id="numero" value="" class="champ-2" placeholder="Votre numéro de mobile" />
				</div>
				<div class="margin_15">   
					<input type="date" name="date" id="date" value="" class="champ-2" placeholder="Date souhaitée" />
				</div>
					
				</div>
				<!-- boutons bas -->
				<div class="margin_6"></div>
					<div class="bouton-gauche">
					 <div class="popup-modal-dismiss bouton-gauche-blanc">Annuler</div>
					</div>
					<div onclick="formNote.submit()" class="bouton-droite">
					 <div class="bouton-droite-rouge">Envoyer</div>
					</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	</div>
</form>

<form id="form-question" class="white-popup-block mfp-hide">
	<div id="body_popup">
	<div class="padding_3">
		<div class="fond_popup">    
			<!-- titre et croix -->
			<div class="border_bottom_1">
				<div class="span_3 float_left margin_8">Posez une question au vendeur</div>
				<div class="popup-modal-dismiss float_right"><img class="padding_4" src="images/croix_rouge.png" width="26" height="27" /></div>
				<div class="clear"></div>
			</div>
			<div class="padding_5 border_top_1">
				<div class="span_4 text_align_center">
				Vous avez une demande d'information complémentaire ? Nous sommes à votre disposition<br/>
				<div class="margin_15">   
					<input type="text" name="nom" id="nom" value="" class="champ-2" placeholder="Votre nom" />
				</div>
				<div class="margin_15">   
					<input type="text" name="numero" id="numero" value="" class="champ-2" placeholder="Votre numéro de mobile" />
				</div>
				<div class="margin_15">   
					<input type="text" name="email" id="email" value="" class="champ-2" placeholder="Votre email" />
				</div>
				<div class="margin_15">   
					<input type="text" name="message" id="message" value="" class="champ-2" placeholder="Votre message" />
				</div>
					
				</div>
				<!-- boutons bas -->
				<div class="margin_6"></div>
					<div class="bouton-gauche">
					 <div class="popup-modal-dismiss bouton-gauche-blanc">Annuler</div>
					</div>
					<div onclick="formNote.submit()" class="bouton-droite">
					 <div class="bouton-droite-rouge">Envoyer</div>
					</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	</div>
</form>




<div id="popup-geoloc" class="white-popup-block mfp-hide">
	<div id="body_popup">
	<div class="padding_3">
		<div class="fond_popup">    
			<!-- titre et croix -->
			<div class="border_bottom_1">
				<div class="span_3 float_left margin_8">Géolocalisation</div>
				<div class="popup-modal-dismiss float_right"><img class="padding_4" src="images/croix_rouge.png" width="26" height="27" /></div>
				<div class="clear"></div>
			</div>
			<div class="padding_5 border_top_1">
				<div class="span_4 text_align_center">
					Vous vous situez à moins de 300m. de ce bien.
				</div>
				<!-- boutons bas -->
				<div class="margin_6"></div>
					<div class="bouton-gauche">
					 <div class="popup-modal-dismiss bouton-gauche-blanc-new">Fermer</div>
					</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	</div>
    </div>
</form>


  <script src="js/idangerous.swiper-2.1.min.js"></script>
  <script>
  var mySwiper = new Swiper('.swiper-container',{
    pagination: '.pagination',
    paginationClickable: true,
	loop: true,
	autoplay:5000
  })
  </script>

