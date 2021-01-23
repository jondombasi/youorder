<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		    {$id=$_GET["id"];}                  else{$id="";}
if(isset($_GET["aff_valide"]))	{$aff_valide=$_GET["aff_valide"];}  else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}         else{$action="";}

$menu                   = "vehicule";
$sous_menu              = "fiche";
$aff_erreur             = "";
$continu                = true;
$display_commentaire    = "display:none;";

$Vehicule = new Vehicule($sql, $id);

if($id==""){
	$titre_page = "Ajouter un véhicule";
}
else{
	$titre_page = "Modifier un véhicule";

	$nom=$Vehicule->getNom();
	if ($Vehicule->getType()!="Scooter") {
		$type="Autre";
		$type_autre=$Vehicule->getType();
	}
	else {
		$type       = $Vehicule->getType();
		$type_autre = "";
	}
	$immatriculation    = $Vehicule->getImmatriculation();
    $kilometrage        = $Vehicule->getKilometrage();
	$marque             = $Vehicule->getMarque();
	$volume             = $Vehicule->getVolume();
	$etat               = $Vehicule->getEtat();
}

if($action=="enregistrer"){
	$type               = $_POST["type"];
	$type_autre         = $_POST["type_autre"];
	$nom                = $_POST["nom"];
	$immatriculation    = $_POST["immatriculation"];
    $kilometrage        = $_POST["kilometrage"];
	$marque             = $_POST["marque"];
	$volume             = $_POST["volume"];
	$etat               = $_POST["etat"];
	$commentaire        = $_POST["commentaire"];
	
	if($type==""){
		$css_type_obl = "has-error";
		$continu = false;
	}
	else if ($type=="Autre" && $type_autre=="") {
		$css_type_obl = "has-error";
		$continu = false;
	}
	if($nom==""){
		$css_nom_obl = "has-error";
		$continu = false;
	}
	if($immatriculation==""){
		$css_immatriculation_obl = "has-error";
		$continu = false;
	}
	/*else if ($Vehicule->checkImmatriculation($immatriculation)) {
		$continu=false;
		$css_immatriculation_obl = "has-error";
	}*/

    if($kilometrage==""){
        $css_kil_obl = "has-error";
        $continu = false;
    }
	if($marque==""){
		$css_marque_obl = "has-error";
		$continu = false;
	}
	if($volume==""){
		$css_volume_obl = "has-error";
		$continu = false;
	}
	else if (!is_numeric($volume)) {
		$css_volume_obl = "has-error";
		$continu = false;
	}
	if($etat==""){
		$css_etat_obl = "has-error";
		$continu = false;
	}


	if($continu){
		$id=$Vehicule->setVehicule($id, $type, $type_autre, $nom, $immatriculation, $kilometrage, $marque, $volume, $etat);

		if ($Vehicule->getEtat()!=$_POST["etat"]) {
			$Vehicule->changeEtat($id, $etat, $_SESSION["userid"], $commentaire);
		}
		header("location: vehicules_fiche.php?aff_valide=1&id=".$id);			
	}else{
		$aff_erreur="1";		
	}
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
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
				<form role="form" name="form" id="form1" method="post" action="vehicules_fiche.php?id=<?php echo $id; ?>" class="form-horizontal">
	            	<input type="hidden" name="action" value="enregistrer"/>

	            	<div class="form-group <?=$css_type_obl?>">
	                    <label class="col-sm-4 control-label">Type</label>
	                    <div class="col-sm-4 margin_label">
	                        <select name="type" id="type" class="form-control">
	                            <option value="">&nbsp;</option>
	                            <option value="Scooter" <?php if($type=="Scooter")              echo "selected";?>>Scooter</option>
	                            <option value="Autre"   <?php if($type!="Scooter" && $type!="") echo "selected";?>>Autre</option>
	                        </select>
	                        <input type="text" id="type_autre" name="type_autre" value="<?=$type_autre?>" placeholder="Indiquez le type de véhicule" class="form-control" style="display:none;border-top:none!important"/>

	                    </div>
	                </div>

	                <div class="form-group <?php echo $css_nom_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Nom
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="nom" placeholder="Nom" class="form-control" value="<?=$nom?>">
	                    </div>
	                </div>

	                <div class="form-group <?php echo $css_immatriculation_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Immatriculation
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="immatriculation" placeholder="Immatriculation" class="form-control" value="<?=$immatriculation?>">
	                    </div>
	                </div>

                    <div class="form-group <?php echo $css_kil_obl; ?>">
                        <label class="col-sm-4 control-label">
                            Kilometrage
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="kilometrage" placeholder="Kilometrage" class="form-control" value="<?=$kilometrage?>">
                        </div>
                    </div>

	                <div class="form-group <?php echo $css_marque_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Marque Top Case
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="marque" placeholder="Marque" class="form-control" value="<?=$marque?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_volume_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Volume Top Case
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="volume" placeholder="Volume" class="form-control" value="<?=$volume?>">
	                    </div>
	                </div>
	                <div class="form-group <?=$css_etat_obl?>">
	                    <label class="col-sm-4 control-label">Etat</label>
	                    <div class="col-sm-4 margin_label">
	                        <select name="etat" id="etat" class="form-control">
	                            <option value="ok"          <?php if($etat=="ok")           echo "selected";?>>En fonctionnement</option>
	                            <option value="maintenance" <?php if($etat=="maintenance")  echo "selected";?>>En maintenance</option>
	                            <option value="nonrestitue" <?php if($etat=="nonrestitue")  echo "selected";?>>Non restitué</option>
	                            <option value="ko"          <?php if($etat=="ko")           echo "selected";?>>HS</option>
	                        </select>
	                    </div>
	                </div> 
	                <div class="form-group commentaire_etat_div" style="<?=$display_commentaire?>">
	                    <label class="col-sm-4 control-label">Commentaire</label>
	                    <div class="col-sm-4 margin_label">
	                        <textarea class="autosize form-control" id="commentaire" name="commentaire" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;"><?php echo $commentaire; ?></textarea>
	                    </div>
	                </div> 

	                 <div class="row row_btn">
                    	<div class="col-sm-4 col-sm-offset-8" style="text-align:right">
                    		<input type="button" onclick="lien('vehicules_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
                    		&nbsp;
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
<script language="javascript">
	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	};

	jQuery(document).ready(function() {
		runSelect2();
		$("textarea.autosize").autosize();
		if ($("#type").val()=="Autre") {
			$("#type_autre").show();
		}
	});

	$("#type").change(function() {
		if ($(this).val()=="Autre") {
			$("#type_autre").show();
		}
		else {
			$("#type_autre").hide();
		}
	})

	$("#etat").change(function() {
		if ($(this).val()!='<?=$etat?>' && '<?=$etat?>'!="") {
			console.log("DIFFERENT");
			$(".commentaire_etat_div").show();
		}
		else {
			console.log("LE MEME");
			$(".commentaire_etat_div").hide();
		}
	});
</script>

