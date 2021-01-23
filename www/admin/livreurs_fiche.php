<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);
require_once("inc_connexion.php");

if(isset($_GET["id"]))		    {$id        =$_GET["id"];}          else{$id="";}
if(isset($_GET["aff_valide"]))	{$aff_valide=$_GET["aff_valide"];}  else{$aff_valide="";}
if(isset($_POST["action"]))		{$action    =$_POST["action"];}     else{$action="";}

$menu       = "livreur";
$sous_menu  = "fiche";
$aff_erreur = "";
$continu    = true;

$Livreur    = new Livreur($sql, $id);

if($id==""){
	$titre_page = "Ajouter un livreur";
}
else{
	$titre_page = "Modifier un livreur";

	$nom        = $Livreur->getNom();
	$prenom     = $Livreur->getPrenom();
	$email      = $Livreur->getEmail();
	$password   = $Livreur->getPassword();
	$telephone  = $Livreur->getTelephone();
	$nbheures   = $Livreur->getNbHeures();
	$situation  = $Livreur->getSituation();
    $etat       = $Livreur->getEtat();

	if ($Livreur->getPhoto()!="") {
		$source_photo="upload/livreurs/".$Livreur->getPhoto();
	}
}

if($action=="enregistrer"){
	$nom        = $_POST["nom"];
	$prenom     = $_POST["prenom"];
	$email      = $_POST["email"];
	$password   = $_POST["password"];
	$telephone  = $_POST["telephone"];
	$nbheures   = $_POST["nbheures"];
	$situation  = $_POST["situation"];
    $etat       = $_POST["etat"];
	$photo      = $_FILES['ImageFile'];
	
	if($nom==""){
		$css_nom_obl = "has-error";
		$continu = false;
	}
	if($prenom==""){
		$css_prenom_obl = "has-error";
		$continu = false;
	}
	if($email==""){
		$css_email_obl = "has-error";
		$continu = false;
	}
	if($password==""){
		$css_password_obl = "has-error";
		$continu = false;
	}
	if($telephone==""){
		$css_telephone_obl = "has-error";
		$continu = false;
	}
	if($nbheures==""){
		$css_nbheures_obl = "has-error";
		$continu = false;
	}
    if($situation==""){
        $css_situation_obl = "has-error";
        $continu = false;
    }
    if($etat==""){
        $css_Etat_obl = "has-error";
        $continu = false;
    }



	if($continu){
		$id=$Livreur->setLivreur($id, $nom, $prenom, $email, $password, $telephone, $nbheures, $situation, $etat);
		if ($photo!='') {
			$directory="livreurs";
            include("action_photo.php");
        }
        else {
        	$Livreur->setPhoto($id, '');
        }
		header("location: livreurs_fiche2.php?aff_valide=1&id=".$id);			
	}else{
		$aff_erreur="1";		
	}
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css" type="text/css"/>
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
				<form role="form" name="form" id="form1" method="post" action="livreurs_fiche.php?id=<?php echo $id; ?>" class="form-horizontal" enctype="multipart/form-data">
	            	<input type="hidden" name="action" value="enregistrer"/>
	                <div class="form-group <?php echo $css_nom_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Nom
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="nom" placeholder="Nom" class="form-control" value="<?=$nom?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_prenom_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Prénom
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="prenom" placeholder="Prénom" class="form-control" value="<?=$prenom?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_email_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Email
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="email" placeholder="Email" class="form-control" value="<?=$email?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_password_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Mot de passe
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="password" placeholder="Mot de passe" class="form-control" value="<?=$password?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_telephone_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Téléphone
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="telephone" placeholder="Téléphone" class="form-control" value="<?=$telephone?>">
	                    </div>
	                </div>
	                <div class="form-group">
	                    <label class="col-sm-4 control-label" for="ImageFile">
	                        Photo
	                    </label>
	                    <div class="col-sm-4">
	                        <div class="fileupload <?php echo ($source_photo=='') ? 'fileupload-new' : 'fileupload-exists' ;?>" data-provides="fileupload">
	                        	<div class="fileupload-new thumbnail" style="max-width: 200px; max-height:200px;">
		                        	<img src="http://www.placehold.it/300x300/EFEFEF/AAAAAA?text=no+image" alt="">
	                            </div>
	                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 200px; line-height: 20px;">
	                            	<?php if ($source_photo!="") { ?>
		                        		<img src="<?=$source_photo?>" alt="">
		                        	<?php } ?>
	                            </div>
	                            <div>  
                            		<span class="btn btn-light-grey btn-file">
                            			<span class="fileupload-new"><i class="fa fa-picture-o"></i> Choisir une image</span>
                            			<span class="fileupload-exists"><i class="fa fa-picture-o"></i> Changer</span>
	                                    <input type="file" name="ImageFile" id="ImageFile"/>
	                                </span>
	                                <a href="#" class="btn fileupload-exists btn-light-grey" data-dismiss="fileupload">
	                                    <i class="fa fa-times"></i> Supprimer
	                                </a>                                     
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_nbheures_obl; ?>">
	                    <label class="col-sm-4 control-label">
	                        Nombre d'heures par semaine
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="nbheures" placeholder="Nombre d'heures par semaine" class="form-control" value="<?=$nbheures?>">
	                    </div>
	                </div>

                    <?php if ($_SESSION["admin"]) { ?>
                    <div class="form-group <?=$css_situation_obl?>">
                        <label class="col-sm-4 control-label">Situation</label>
                        <div class="col-sm-4 margin_label">
                            <select name="situation" id="situation" class="form-control">
                                <option value="">&nbsp;</option>
                                <option value="actif"   <?php if($situation=="actif")   echo "selected";?>>Actif</option>
                                <option value="inactif" <?php if($situation=="inactif") echo "selected";?>>Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group <?=$css_Etat_obl?>">
                        <label class="col-sm-4 control-label">Etat</label>
                        <div class="col-sm-4 margin_label">
                            <select name="etat" id="etat" class="form-control">
                                <option value="">&nbsp;</option>
                                <option value="disponible"  <?php if($etat=="disponible")   echo "selected";?>>Disponible</option>
                                <option value="relayant"    <?php if($etat=="relayant")     echo "selected";?>>Relayant</option>
                                <option value="en attente"  <?php if($etat=="en attente")   echo "selected";?>>En attente</option>
                                <option value="repos"       <?php if($etat=="repos")        echo "selected";?>>Repos</option>
                                <option value="en congé"    <?php if($etat=="en congé")     echo "selected";?>>En congé</option>
                                <option value="malade"      <?php if($etat=="malade")       echo "selected";?>>Malade</option>
                            </select>
                        </div>
                    </div
                    <?php } ?>

                    <select name="id_etat" id="etat" class="form-control">

                    <div class="row row_btn">
                    	<div class="col-sm-4 col-sm-offset-8" style="text-align:right">
                    		<input type="button" onclick="lien('livreurs_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
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
<script src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="assets/plugins/select2/select2.min.js"></script>
<script src="assets/plugins/autosize/jquery.autosize.min.js"></script>
<script>
	$("form").submit(function() {
	    $(this).find('input[type="submit"]').prop("disabled", true);
	});

    function runSelect2() {
        $(".search-select").select2({
            placeholder: "Select a State",
            allowClear: true
        });
    };

    jQuery(document).ready(function() {
        runSelect2();
        $("textarea.autosize").autosize();
//        if ($("#situation").val()=="actif") {
//            $("#s_actif").show();
//        }
//        else
//            $("#s_inactif").show();

    });
//
//    $("#type").change(function() {
//        if ($(this).val()=="Autre") {
//            $("#type_autre").show();
//        }
//        else {
//            $("#type_autre").hide();
//        }
//    })
//
//    $('#situation').change(function(){
//        if ($(this).val() == "actif") {
//            $('#etat').empty();
//            $('#etat').append($("<option />").val(1).text('Disponible'));
//            $('#etat').append($("<option />").val(2).text('Relayant'));
//            $('#etat').append($("<option />").val(3).text('En attente'))
//        } else if ($(this).val() == "inactif") {
//            $('#etat').empty();
//            $('#etat').append($("<option />").val(4).text('Repos'));
//            $('#etat').append($("<option />").val(5).text('En congé'));
//            $('#etat').append($("<option />").val(6).text('Malade'));
//        }
    })





</script>