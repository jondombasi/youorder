<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))          {$id        =$_GET["id"];}          else{$id        ="";}
if(isset($_GET["aff_valide"]))	{$aff_valide=$_GET["aff_valide"];}  else{$aff_valide="";}
if(isset($_POST["action"]))		{$action    =$_POST["action"];}     else{$action    ="";}

if(!$_SESSION["admin"] && $id!=$_SESSION["userid"]){
    header("location: home.php");
}


$menu = "compte";
if($id==""){
	$titre_page = "Ajouter un utilisateur";
}else{
	$titre_page = "Modifier un utilisateur";
}
$aff_erreur         = "";
$disp_dept          = "none";
$disp_select_resto  = "none";

$Utilisateur    = new Utilisateur($sql, $id);
$Commercant     = new Commercant($sql);


if($id!=""){
	$password   = $Utilisateur->getPassword();
	$nom        = $Utilisateur->getNom();
	$prenom     = $Utilisateur->getPrenom();
	$email      = $Utilisateur->getEmail();
	$numero     = $Utilisateur->getNumero();
	$restaurant = $Utilisateur->getRestaurant();
	$role       = $Utilisateur->getRole();

	if($role=="restaurateur"){
		$disp_select_resto = "block";
	}

	$liste_resto=$Utilisateur->getListeResto();

	if ($Utilisateur->getPhoto()!="") {
		$source_photo="upload/utilisateurs/".$Utilisateur->getPhoto();
	}

	$affecter_commande  = $Utilisateur->getAffecterCommande();
	$visibilite_map     = $Utilisateur->getVisibiliteMap();
	$planning_livreur   = $Utilisateur->getPlanningLivreur();
	$dispo_api          = $Utilisateur->getDispoAPI();
	$secret_key         = $Utilisateur->getSecretKey();
} else{
	$password = newChaine(9);
}

$continu    = true;
$doublons   = false;
if($action=="enregistrer"){
	$nom	            = $_POST["nom"];
	$prenom	            = $_POST["prenom"];
	$email	            = $_POST["email"];
	$numero	            = $_POST["numero"];
	$password           = $_POST["password"];
	$role               = $_POST["role"];
	$photo 	            = $_FILES['ImageFile'];
	$affecter_commande  = $_POST['affecter_commande'];
	$visibilite_map     = $_POST['visibilite_map'];
	$planning_livreur   = $_POST['planning_livreur'];
	$dispo_api          = $_POST['dispo_api'];
    $secret_key         = $_POST['secret_key'];

//	if ($secret_key =="") $secret_key=newChaine(20);

	if($role=="restaurateur"){
		$restaurant         = $_POST["restaurant"];
		$disp_select_resto  = "block";
	}else{
		$restaurant = 0;
	}
	
	if($email==""){
		$css_email_obl = "has-error";
		$continu = false;
	}else{
		if ($Utilisateur->checkEmail($email, $id)) {
			$css_email_obl = "has-error";
			$continu    = false;
			$doublons   = true;
		}
	}

	if($password==""){
		$css_password_obl = "has-error";
		$continu = false;
	}

	if ($affecter_commande=="") {
		$affecter_commande="off";
	}
	if ($visibilite_map=="") {
		$visibilite_map="off";
	}
	if ($planning_livreur=="") {
		$planning_livreur="off";
	}
	if ($dispo_api=="") {
		$dispo_api="off";
	}
	
	if($continu){
		$liste_resto = "";			
		if($role=="restaurateur"){
			foreach($Commercant->getAll("", "") as $commercant) {
				$nomchp="resto_".$commercant->id;
				if($_POST[$nomchp]=="1"){
					$liste_resto .= "'".$commercant->id."',";
				}
			}
			$liste_resto = substr($liste_resto,0,-1);
		}		
		 	
		$id=$Utilisateur->setUtilisateur($id, $nom, $prenom, $email, $numero, $password, $role, $restaurant, $liste_resto, $affecter_commande, $visibilite_map, $planning_livreur, $dispo_api, $secret_key);
		if ($photo!='') {
			$directory="utilisateurs";
            include("action_photo.php");
        }
        else {
        	$Utilisateur->setPhoto($id, '');
        }
		header("location: administration_fiche.php?aff_valide=1&id=".$id);
		exit();

	}else{
		$aff_erreur="1";		
	}
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css" type="text/css"/>
<link rel="stylesheet" href="assets/plugins/switchery/dist/switchery.css"/> 
<style>
	.radio-inline, .radio-inline + .radio-inline, .checkbox-inline, .checkbox-inline + .checkbox-inline {
		margin-right: 0px !important;
		margin-top: 5px !important;
		margin-left: 0 !important;
		margin-bottom: 10px !important;
	}

	.option_commercant{
            position: relative;
            height: 50px;
	}

	.option_commercant_child{
            top: 50%;
            transform: translateY(-50%);
	}

	.option_commercant_top{border:1px solid #eee;}
	.option_commercant_bottom{border-top:none;}

	.option_commercant_middle{
		border-left:1px solid #eee;
		border-right:1px solid #eee;
		border-bottom:1px solid #eee;
	}

	.option_commercant_child_right {
		text-align:right;
	}

	.option_commercant .col-sm-2{text-align:right;}
        
    @media (max-width:800px){
        .r-stop{clear:both}
        .option_commercant_child{transform:translateY(0);}
        .option_commercant{border: none !important; height: auto;}
        .option_commercant_child_left{
            width:80% !important;
            float: left;
            margin-bottom: 15px;
        }
        .option_commercant_child_right{
            width:20% !important;
            float: right;
            margin-bottom: 15px;
        }
        .option_commercant_child_bottom{
            float:left;
            width:50% !important;
        }
    }
</style>
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
                <?php
				if($doublons){
					echo 'Cette email possède déjà un compte';								
				}else{
					echo 'Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.';
				}
				?>
                
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
        <div class="row">
			<div class="col-sm-12">
				<form role="form" name="form" id="form1" method="post" action="administration_fiche.php?id=<?php echo $id; ?>" class="form-horizontal" enctype="multipart/form-data">
	            	<input type="hidden" name="action" value="enregistrer">
	                <input type="hidden" name="restaurant" value="<?=$restaurant?>">
	                <div class="form-group <?php echo $css_nom_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-1">
	                        Nom
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="nom" placeholder="Nom" id="form-field-1" class="form-control" value="<?php echo $nom; ?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_prenom_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-2">
	                        Prénom
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="prenom" placeholder="prénom" id="form-field-2" class="form-control" value="<?php echo $prenom; ?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_email_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-2">
	                        Email<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="email" placeholder="email" id="form-field-2" class="form-control" value="<?php echo $email; ?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_password_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-4">
	                        Mot de passe<span class="symbol required"></span>
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="password" placeholder="password" id="form-field-4" class="form-control" value="<?php echo $password; ?>">
	                    </div>
	                </div>
	                <div class="form-group <?php echo $css_login_obl; ?>">
	                    <label class="col-sm-4 control-label" for="form-field-3">
	                        Numéro
	                    </label>
	                    <div class="col-sm-4">
	                        <input type="text" name="numero" placeholder="numéro" id="form-field-3" class="form-control" value="<?php echo $numero; ?>">
	                    </div>
	                </div>

	                <?php if ($dispo_api=="on") { ?>
	                	<div class="form-group">
	                        <label class="col-sm-4 control-label" for="form-field-select-1">
	                            Clé API
	                        </label>
	                        <div class="col-sm-4">
	                        	<input type="text" name="secret_key" id="form-field-10" class="form-control" value="<?php echo $secret_key; ?>" disabled/>
	                        </div>
	                    </div>
	                <?php } ?>

	                <?php
	                if($_SESSION["admin"]){
	                ?>
	                    <div class="form-group">
	                        <label class="col-sm-4 control-label" for="form-field-select-1">
	                            Rôle
	                        </label>
	                        <div class="col-sm-4">
	                        <select name="role" id="role" class="form-control" onchange="change_role()">
								<option <?php if($role=="admin")        {echo 'selected="selected"';} ?> value="admin">         Admin</option>
								<option <?php if($role=="planner")      {echo 'selected="selected"';} ?> value="planner">       Planner</option>
	                            <option <?php if($role=="restaurateur") {echo 'selected="selected"';} ?> value="restaurateur">  Commerçant</option>
								<option <?php if($role=="inactif")      {echo 'selected="selected"';} ?> value="inactif">       Inactif</option>
	                        </select>
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
		                        <div class="alert alert-info">
									<i class="fa fa-info-circle"></i>
									L'image doit être au format JPG, PNG ou GIF et ne doit pas faire plus de 2Mb.
								</div>
		                    </div>
		                </div>

	                    <div class="form-group div_resto" style="display:<?=$disp_select_resto?>">
	                        <label class="col-sm-4 control-label" for="form-field-select-1">
	                            Commerce
	                        </label>
	                        <div class="col-sm-12 margin_label" style="margin-top:15px">
								<?php
								$liste_commercant=$Commercant->getAll("", "", "", true);
								foreach ($liste_commercant as $commercant) {
									if(strpos($liste_resto,"'".$commercant->id."'")!==false){$sel = 'checked="checked"';}else{$sel = "";}
									?>
									<label class="col-sm-3 checkbox-inline check_resto">
	                                    <input type="checkbox" class="green" value="1" name="<?="resto_".$commercant->id;?>" <?php echo $sel; ?>>
	                                    <?php echo $commercant->nom; ?>
	                                </label>
									<?php
								}
	                            ?>
	                        </div>
	                    </div>

	                    <div class="form-group div_resto" style="display:<?=$disp_select_resto?>">
		                    <div class="col-sm-8 col-sm-offset-2">
				                <div class="panel panel-default">
									<div class="panel-heading" style="padding-left:10px">Options</div>
									<div class="panel-body">
										<div class="option_commercant option_commercant_top">
			                                <div class="col-sm-9 option_commercant_child option_commercant_child_left">
			                                    Affecter la commande aux livreurs
			                                </div>
			                                <div class="col-sm-3 option_commercant_child option_commercant_child_right">
			                                    <input type="checkbox" class="js-switch" name="affecter_commande" <?php if ($affecter_commande=="on") echo "checked";?>/>
			                                </div>
										</div>
										<div class="option_commercant option_commercant_middle">
											<div class="col-sm-9 option_commercant_child option_commercant_child_left">
												Autoriser l'accès à l'API
											</div>
											<div class="col-sm-3 option_commercant_child option_commercant_child_right">
												<input type="checkbox" class="js-switch" name="dispo_api" <?php if ($dispo_api=="on") echo "checked";?>/>
											</div>
										</div>
										<div class="option_commercant option_commercant_top option_commercant_bottom">
											<div class="col-sm-9 option_commercant_child option_commercant_child_left">
												Planning livreur
											</div>
											<div class="col-sm-3 option_commercant_child option_commercant_child_right">
												<input type="checkbox" class="js-switch" name="planning_livreur" <?php if ($planning_livreur=="on") echo "checked";?>/>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
	                <?php
					}
					else{
					?>
						<input type="hidden" name="role" id="role" value="<?=$role?>" />
						<input type="hidden" name="restaurant" id="restaurant" value="<?=$restaurant?>" />
					<?php	
					}
	                ?>

	                <?php if ($_SESSION["restaurateur"]) { ?>
	                	<div class="form-group div_resto">
		                    <div class="col-sm-12">
				                <div class="panel panel-default">
									<div class="panel-heading" style="padding-left:10px">Options</div>
									<div class="panel-body">
										<div class="option_commercant option_commercant_top">
			                                <div class="col-sm-9 option_commercant_child option_commercant_child_left">
			                                    Affecter la commande aux livreurs
			                                </div>
			                                <div class="col-sm-3 option_commercant_child option_commercant_child_right">
			                                    <?=($affecter_commande=="on") ? "<i class='clip-checkmark-2' style='color:#9fc752'></i>" : "<i class='clip-close' style='color:red'></i>" ;?>
			                                </div>
										</div>
										<div class="option_commercant option_commercant_middle">
											<div class="col-sm-9 option_commercant_child option_commercant_child_left">
												Accès à l'API
											</div>
											<div class="col-sm-3 option_commercant_child option_commercant_child_right">
												<?=($dispo_api=="on") ? "<i class='clip-checkmark-2' style='color:#9fc752'></i>" : "<i class='clip-close' style='color:red'></i>" ;?>
											</div>
										</div>
										<div class="option_commercant option_commercant_top option_commercant_bottom">
											<div class="col-sm-9 option_commercant_child option_commercant_child_left">
												Planning livreur
											</div>
											<div class="col-sm-3 option_commercant_child option_commercant_child_right">
												<?=($planning_livreur=="on") ? "<i class='clip-checkmark-2' style='color:#9fc752'></i>" : "<i class='clip-close' style='color:red'></i>" ;?>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
	                <?php } ?>

	                <div class="row row_btn">
                    	<div class="col-sm-6 col-sm-offset-6" style="text-align:right">
                    		<?php if($_SESSION["admin"]){ ?>
                    			<input type="button" onclick="lien('administration.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
                    		<?php } ?>
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

<script src="assets/plugins/select2/select2.min.js"></script>
<script src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="assets/plugins/switchery/dist/switchery.js"></script> 
<script language="javascript">
	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	}

	function change_role(){
		role = $("#role").val();
		if(role=="restaurateur"){
			$(".div_resto").css("display","block");	
		}else{
			$(".div_resto").css("display","none");							
		}
	}

	jQuery(document).ready(function() {
		runSelect2();
		var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
		elems.forEach(function(html) {
		  var switchery = new Switchery(html, {color: '#9fc752', jackColor: '#fff', size: 'small' });
		});
	});
</script>