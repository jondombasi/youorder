<?php
$menu = "actualite";
$sous_menu = "fiche";
require_once("inc_connexion.php");

if(isset($_POST["id"]))			{$id=$_POST["id"];}else{if(isset($_GET["id"])){$id=$_GET["id"];}else{$id="";}}
if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}else{$aff_valide="";}

$aff_erreur = "";
$page="actualites";
$continu = true;

if ($id=="") {
	$titre_page = "Ajouter une actualité";
	$action_text="enregistrer";
}
else {
	$titre_page="Modifier une actualité";
	$action_text="modifier";
	$source_photo="";

	$result = $sql->query("SELECT * FROM actualites WHERE id = ".$sql->quote($id));
	$ligne = $result->fetch();
	if($ligne){
		$titre=$ligne["titre"];
		$desc=$ligne["description"];
		$categorie=$ligne["categorie"];
		if ($ligne["photo"]!="") {
			$source_photo="upload/actualites/".$ligne["photo"];
		}
	}
}

if($action=="enregistrer"){
	$titre = $_POST["titre"];
	$categorie = $_POST["categorie"];
	$desc = $_POST["desc"];
	$photo=$_FILES['ImageFile'];

	if($titre==""){
		$css_titre = "has-error";
		$continu = false;
	}
	if($categorie==""){
		$css_categorie = "has-error";
		$continu = false;
	}
	if($desc==""){
		$css_desc = "has-error";
		$continu = false;
	}

	if ($continu) {		
		$url=slugify($titre);
		$req = $sql->exec("INSERT INTO actualites (titre,description,categorie,date,url) VALUES (".$sql->quote($titre).",".$sql->quote($desc).", ".$sql->quote($categorie).", NOW(), ".$sql->quote($url).")");
		$id=$sql->lastInsertId();
		if ($photo!='') {
			$directory="actualites";
            include("action_photo.php");
        }
		header("location: actualite_liste.php");
		exit();
	}
	else {
		$aff_erreur=1;
	}
	
}

if($action=="modifier"){
	$titre = $_POST["titre"];
	$categorie = $_POST["categorie"];
	$desc = $_POST["desc"];
	$photo=$_FILES['ImageFile'];

	if($titre==""){
		$css_titre = "has-error";
		$continu = false;
	}
	if($categorie==""){
		$css_categorie = "has-error";
		$continu = false;
	}
	if($desc==""){
		$css_desc = "has-error";
		$continu = false;
	}

	if ($continu) {
		$url=slugify($titre);
		$req = $sql->exec("UPDATE actualites SET titre=".$sql->quote($titre).", description=".$sql->quote($desc).", categorie=".$sql->quote($categorie).", url=".$sql->quote($url)." WHERE id=".$sql->quote($id));
		if ($photo!='') {
			$directory="actualites";
            include("action_photo.php");
        }
        else {
        	$req = $sql->exec("UPDATE actualites SET photo='' WHERE id=".$sql->quote($id));
        }
		header("location: actualite_liste.php");
		exit();
	}
	else {
		$aff_erreur=1;
	}
	
}
require_once("inc_header.php");
?>

<link rel="stylesheet" href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css" type="text/css"/>

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1><?=$titre_page?></h1>
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
                <button class="close" data-dismiss="alert"> × </button>
                <i class="clip-cancel-circle-2"></i>   
                Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.           
            </div>                                            
            <?php	
		}
		if($aff_valide==1){
		?>
        <div class="alert alert-success">
            <button class="close" data-dismiss="alert"> × </button>
            <i class="clip-checkmark-circle-2"></i>
            Les modifications ont été enregistrées.
        </div>                    
		<?php } ?>
		<form role="form" name="form" id="form1" method="post" action="actualite_fiche.php" class="form-horizontal"   enctype="multipart/form-data">
			<input type="hidden" id="action" name="action" value="<?=$action_text?>"/>
			<input type="hidden" name="id" value="<?=$id?>">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
	                    <label class="col-sm-2 control-label" for="ImageFile">
	                        Photo
	                    </label>
	                    <div class="col-sm-9">
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
	                        <div class="alert alert-info">
								<i class="fa fa-info-circle"></i>
								L'image doit être au format JPG, PNG ou GIF et ne doit pas faire plus de 2Mb.
							</div>
	                    </div>
	                </div>
					<div class="form-group <?=$css_titre?>">
	                    <label class="col-sm-2 control-label" for="titre">
	                        Titre
	                    </label>
	                    <div class="col-sm-9">
	                        <input type="text" id="titre" name="titre" class="form-control" value="<?=$titre?>"/>
	                    </div>
	                </div>
	                <div class="form-group <?=$css_categorie?>">
	                    <label class="col-sm-2 control-label" for="categorie">
	                        Catégorie
	                    </label>
	                    <div class="col-sm-9">
	                        <select id="categorie" name="categorie" class="form-control">
								<option value="">Catégorie</option>
								<option value="Écologie" <?php if ($categorie=="écologie") echo "selected"; ?>>Écologie</option>
								<option value="Transport" <?php if ($categorie=="Transport") echo "selected"; ?>>Transport</option>
								<option value="Urbain" <?php if ($categorie=="Urbain") echo "selected"; ?>>Urbain</option>
								<option value="Services" <?php if ($categorie=="Services") echo "selected"; ?>>Services</option>
							</select>
	                    </div>
	                </div>
	                <div class="form-group">
						<label class="col-sm-2 control-label" for="desc">
	                        Description
	                    </label>
	                    <div class="col-sm-9">
							<textarea class="ckeditor form-control" cols="10" rows="10" name="desc" id="desc"><?=$desc?></textarea>
						</div>
					</div>
	            </div>
	        </div>

	        <div class="row row_btn">
            	<div class="col-sm-4 col-sm-offset-8" style="text-align:right">
            		<input type="button" onclick="lien('actualite_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
            		&nbsp;
                    <input type="submit" id="bt2" class="btn btn-main" value="Enregistrer" style="width:100px;">
                </div>
            </div> 
		</form>                   
		<!-- end: PAGE CONTENT-->
	</div>
</div>
<!-- end: PAGE -->
<?php
require_once("inc_footer.php");
?>
<script src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="assets/plugins/ckeditor/ckeditor.js"></script>
<script src="assets/plugins/ckeditor/adapters/jquery.js"></script>
<script>
	$(document).ready(function() {
		CKEDITOR.replace('desc');
	})
</script>