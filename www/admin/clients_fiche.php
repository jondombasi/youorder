<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}

$menu = "client";
if($id==""){
	$sous_menu = "fiche";
	$titre_page = "Ajouter un client";
}else{
	$sous_menu = "liste";
	$titre_page = "Modifier un client";
}
$aff_erreur = "";

$Commercant = new Commercant($sql);
$Client = new Client($sql, $id);

if($id!=""){
	$nom=$Client->getNom();
	$prenom=$Client->getPrenom();
	$adresse=$Client->getAdresse();
	$longitude=$Client->getLongitude();
	$latitude=$Client->getLatitude();
	$numero=$Client->getNumero();
	$email=$Client->getEmail();
	$commentaire=$Client->getCommentaire();
	$restaurant=$Client->getRestaurant();	
}

$continu = true;
if($action=="enregistrer"){
	$nom	 	= $_POST["nom"];
	$prenom	 	= $_POST["prenom"];
	$adresse 	= $_POST["adresse"];
	$longitude	= $_POST["longitude"];
	$latitude	= $_POST["latitude"];
	$numero 	= $_POST["numero"];
	$email		= $_POST["email"];
	$commentaire= $_POST["commentaire"];
	$restaurant	= $_POST["restaurant"];
	
	if($nom==""){
		$css_nom_obl = "has-error";
		$continu = false;
	}
	if($restaurant==""){
		$css_restaurant_obl = "has-error";
		$continu = false;
	}
	if($adresse=="" || $longitude==0 || $latitude==0){
		$css_adresse_obl = "has-error";
		$continu = false;
	}	
	if($numero==""){
		$css_telephone_obl = "has-error";
		$continu = false;
	}else{
		$regexp_mail = "/^0[0-9]([-. ]?\d{2}){4}[-. ]?$/";
		if(!preg_match($regexp_mail, $numero)) {
			$css_telephone_obl = "has-error";
			$continu = false;
		}			
	}

	if($email==""){
	}else{
		$regexp_mail = "/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/";
		if(!preg_match($regexp_mail, $email)) {
			$css_email_obl = "has-error";
			$continu = false;
		}			
	}

	if($continu){
		$id=$Client->setClient($id, $nom, $prenom, $adresse, $latitude, $longitude, $numero, $email, $commentaire, $restaurant);
		header("location: clients_fiche.php?aff_valide=1&id=".$id);
		$aff_valide = "1";
	}else{
		$aff_erreur="1";		
	}
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<style>
    @media (max-width: 767px){
        .form-control1{margin-bottom: 10px;}
    }
</style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places&key=AIzaSyC15w0ru2bvazBjNdaHtVLXngRT6JfSh2s"></script>
<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1><?php echo $titre_page; ?></h1>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
		<?php
		if($aff_erreur=="1"){
			?>
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert">
                    ×
                </button>
                <i class="fa fa-check-circle"></i>
                Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.
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
            Les modifications ont été enregistrées.
        </div>                    
		<?php } ?>
        <div class="row" style="margin-top:40px;">
            <div class="col-sm-12">
                <form role="form" name="form" id="form1" method="post" action="clients_fiche.php?id=<?php echo $id; ?>" class="form-horizontal">
                    <input type="hidden" name="action" value="enregistrer">
                    <input type="hidden" name="longitude" id="longitude" value="<?=$longitude?>">
                    <input type="hidden" name="latitude" id="latitude" value="<?=$latitude?>">
	                <div class="form-group <?php echo $css_nom_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-1">
	                        Nom<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-2">
	                        <input type="text" name="nom" placeholder="Nom" id="form-field-1" class="form-control form-control1" value="<?php echo $nom; ?>">
	                    </div>
	                    <div class="col-sm-2">
	                        <input type="text" name="prenom" placeholder="Prénom" id="form-field-1" class="form-control" value="<?php echo $prenom; ?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_adresse_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-1">
	                        Adresse<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="adresse" placeholder="Adresse" id="adresse" class="form-control" value="<?php echo $adresse; ?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_telephone_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Numéro<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4">
	                    	<input type="text" placeholder="Numéro" class="form-control" id="numero" name="numero" value="<?php echo $numero; ?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_email_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-2">
	                        Email
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="email" placeholder="Email" id="form-field-2" class="form-control" value="<?php echo $email; ?>">
	                    </div>
	                </div>
	                <div class="form-group">
	                    <label class="col-sm-4 control-label" for="form-field-1">
	                        Commentaire
	                    </label>
	                    <div class="col-sm-4">
	                        <textarea class="autosize form-control" id="commentaire" name="commentaire" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;"><?php echo $commentaire; ?></textarea>
	                    </div>
	                </div>    
	                <div id="div_resto" class="form-group <?=$css_restaurant_obl?>">
	                    <label class="col-sm-4 control-label" for="form-field-select-1">
	                        Commerçant<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4 margin_label">
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

	                <div class="row row_btn">
                    	<div class="col-sm-6 col-sm-offset-6" style="text-align:right">
                    		<?php
							if($id!=""){ ?>
	                            <input type="button" onclick="lien('commandes_fiche.php?resto=<?=$restaurant?>&client=<?php echo $id ?>')" id="bt" class="btn btn-main" value="Passer commande" style="width:150px;">
	                            	
							<?php } ?>
                    		<input type="button" onclick="lien('clients_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
	                        <input type="submit" id="bt" class="btn btn-main" value="Enregistrer" style="width:100px;">
                        </div>
                    </div> 	
	        	</form>
	        </div>
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

<script language="javascript" type="text/javascript">
	$(window).load(function() {
	  	autocomplete = new google.maps.places.Autocomplete((document.getElementById('adresse')));
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			fillInAddress();
	  	});
	});

	function fillInAddress() {
		// Get the place details from the autocomplete object.
	  	var place = autocomplete.getPlace();
		console.log(place.geometry.location.lng()+' '+place.geometry.location.lat())
		$("#longitude").val(place.geometry.location.lng());
		$("#latitude").val(place.geometry.location.lat());
	}

	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	};
	jQuery(document).ready(function() {
		runSelect2();
		$("textarea.autosize").autosize();
	});
</script>

