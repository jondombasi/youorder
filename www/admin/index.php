<?php
session_start();
require_once("inc_connexion.php");

if(!isset($_SESSION["login"]))	 {$_SESSION["login"]="";}
if(!isset($_SESSION["password"])){$_SESSION["password"]="";}
if(!isset($_SESSION["acces"]))	 {$_SESSION["acces"]=false;}

if(!isset($_COOKIE["ao_login"])){$memo_chk = "checked";}else{$memo_chk = "";}
if($_SESSION["username"]!=""){
	$login = $_SESSION["username"];
}elseif(isset($_COOKIE["ao_login"])){
	$login = $_COOKIE["ao_login"];
}else{
	$login = "";
}
if($_SESSION["password"]!=""){
	$password = $_SESSION["password"];
}elseif(isset($_COOKIE["ao_password"])){
	$password = $_COOKIE["ao_password"];
}else{
	$password = "";
}



if(isset($_GET["err"])){$err=$_GET["err"];}else{$err="";}
if(isset($_GET["action"])){$action=$_GET["action"];}else{$action="";}
if(isset($_GET["cmd"])){$cmd=$_GET["cmd"];}else{$cmd="";}

switch($err){
	case "1":
		$display_err = "";
		$has_error = "has-error";
		break;
	default:
		$display_err = "no-display";
		$has_error = "";
		break;
}
if($action=="deconnexion"){
	$result = $sql->exec("UPDATE utilisateurs SET statut = 'OFF' WHERE id = '".$_SESSION["userid"]."'");		
	//$_SESSION["acces"] = false;
	session_destroy();		
}
?>
<!DOCTYPE html>
<!-- Template Name: Clip-One - Responsive Admin Template build with Twitter Bootstrap 3.x Version: 1.3 Author: ClipTheme -->
<!--[if IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
	<!--<![endif]-->
	<!-- start: HEAD -->
	<head>
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
		<!--[if IE 7]>
		<link rel="stylesheet" href="assets/plugins/font-awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		<!-- end: MAIN CSS -->
		<!-- start: CSS REQUIRED FOR THIS PAGE ONLY -->
		<!-- end: CSS REQUIRED FOR THIS PAGE ONLY -->
	</head>
	<!-- end: HEAD -->
	<!-- start: BODY -->
	<body class="login example2">
		<div class="main-login col-sm-4 col-sm-offset-4">
			<div class="logo"><img src="images/logo_youorder_new.png" style="max-width:100%;max-height:150px;"></div>
			<!-- start: LOGIN BOX -->
			<div class="box-login">
				<h3>Connexion</h3>
				<p>
					Veuillez entrer votre login et votre mot de passe
				</p>
				<form class="form-login" action="access_ctrl.php">
                	<input type="hidden" name="cmd" id="cmd" value="<?=$cmd?>" />
					<div class="errorHandler alert alert-danger <?php echo $display_err; ?>">
						<i class="fa fa-remove-sign"></i>Les informations saisies sont incomplètes ou erronées
					</div>
                    <?php
					if($action=="deconnexion"){
						?>
                        <div class="errorHandler alert alert-danger">
                            <i class="fa fa-remove-sign"></i>Vous avez été déconnecté de l'interface.
                        </div>                        
                        <?php	
					}
					?>
					<fieldset>
						<div class="form-group <?php echo $has_error; ?>">
							<span class="input-icon">
								<input type="text" class="<?php echo $has_error; ?> form-control" name="username" placeholder="Login" value="<?php echo $login; ?>">
								<i class="fa fa-user"></i> </span>
						</div>
						<div class="form-group form-actions <?php echo $has_error; ?>">
							<span class="input-icon">
								<input type="password" class="<?php echo $has_error; ?> form-control password" name="password" placeholder="Mot de passe" value="<?php echo $password; ?>">
								<i class="fa fa-lock"></i>
								<a style="display:none;"  class="forgot" href="#">
									I forgot my password
								</a> </span>
						</div>
						<div class="form-actions">
							<label for="remember" class="checkbox-inline">
								<input type="checkbox" class="grey remember" id="remember" name="remember" <?php echo $memo_chk; ?>>
								Mémoriser vos identifiants
							</label>
							<button type="submit" class="btn btn-green pull-right" style="background-color:#9fc752">
								Connexion <i class="fa fa-arrow-circle-right"></i>
							</button>
						</div>
						<div class="new-account" style="display:none;">
							Don't have an account yet?
							<a href="#" class="register">
								Create an account
							</a>
						</div>
					</fieldset>
				</form>
			</div>
			<!-- end: LOGIN BOX -->
			<!-- start: FORGOT BOX -->
			<div class="box-forgot">
				<h3>Forget Password?</h3>
				<p>
					Enter your e-mail address below to reset your password.
				</p>
				<form class="form-forgot">
					<div class="errorHandler alert alert-danger no-display">
						<i class="fa fa-remove-sign"></i> You have some form errors. Please check below.
					</div>
					<fieldset>
						<div class="form-group">
							<span class="input-icon">
								<input type="email" class="form-control" name="email" placeholder="Email">
								<i class="fa fa-envelope"></i> </span>
						</div>
						<div class="form-actions">
							<a class="btn btn-light-grey go-back">
								<i class="fa fa-circle-arrow-left"></i> Back
							</a>
							<button type="submit" class="btn btn-bricky pull-right">
								Submit <i class="fa fa-arrow-circle-right"></i>
							</button>
						</div>
					</fieldset>
				</form>
			</div>
			<!-- end: FORGOT BOX -->
			<!-- start: REGISTER BOX -->
			<div class="box-register">
				<h3>Sign Up</h3>
				<p>
					Enter your personal details below:
				</p>
				<form class="form-register">
					<div class="errorHandler alert alert-danger no-display">
						<i class="fa fa-remove-sign"></i> You have some form errors. Please check below.
					</div>
					<fieldset>
						<div class="form-group">
							<input type="text" class="form-control" name="full_name" placeholder="Full Name">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="address" placeholder="Address">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="city" placeholder="City">
						</div>
						<div class="form-group">
							<div>
								<label class="radio-inline">
									<input type="radio" class="grey" value="F" name="gender">
									Female
								</label>
								<label class="radio-inline">
									<input type="radio" class="grey" value="M" name="gender">
									Male
								</label>
							</div>
						</div>
						<p>
							Enter your account details below:
						</p>
						<div class="form-group">
							<span class="input-icon">
								<input type="email" class="form-control" name="email" placeholder="Email">
								<i class="fa fa-envelope"></i> </span>
						</div>
						<div class="form-group">
							<span class="input-icon">
								<input type="password" class="form-control" id="password" name="password" placeholder="Password">
								<i class="fa fa-lock"></i> </span>
						</div>
						<div class="form-group">
							<span class="input-icon">
								<input type="password" class="form-control" name="password_again" placeholder="Password Again">
								<i class="fa fa-lock"></i> </span>
						</div>
						<div class="form-group">
							<div>
								<label for="agree" class="checkbox-inline">
									<input type="checkbox" class="grey agree" id="agree" name="agree">
									I agree to the Terms of Service and Privacy Policy
								</label>
							</div>
						</div>
						<div class="form-actions">
							<a class="btn btn-light-grey go-back">
								<i class="fa fa-circle-arrow-left"></i> Back
							</a>
							<button type="submit" class="btn btn-bricky pull-right">
								Submit <i class="fa fa-arrow-circle-right"></i>
							</button>
						</div>
					</fieldset>
				</form>
			</div>
			<!-- end: REGISTER BOX -->
			<!-- start: COPYRIGHT -->
			<div class="copyright">
				<?=date("Y")?> &copy; youOrder.
			</div>
			<!-- end: COPYRIGHT -->
		</div>
		<!-- start: MAIN JAVASCRIPTS -->
		<!--[if lt IE 9]>
		<script src="assets/plugins/respond.min.js"></script>
		<script src="assets/plugins/excanvas.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<!--<![endif]-->
		<script src="assets/plugins/jquery-ui/jquery-ui-1.10.2.custom.min.js"></script>
		<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
		<script src="assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
		<script src="assets/plugins/blockUI/jquery.blockUI.js"></script>
		<script src="assets/plugins/iCheck/jquery.icheck.min.js"></script>
		<script src="assets/plugins/perfect-scrollbar/src/jquery.mousewheel.js"></script>
		<script src="assets/plugins/perfect-scrollbar/src/perfect-scrollbar.js"></script>
		<script src="assets/plugins/less/less-1.5.0.min.js"></script>
		<script src="assets/plugins/jquery-cookie/jquery.cookie.js"></script>
		<script src="assets/plugins/bootstrap-colorpalette/js/bootstrap-colorpalette.js"></script>
		<script src="assets/js/main.js"></script>
		<!-- end: MAIN JAVASCRIPTS -->
		<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<script src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="assets/js/login.js"></script>
		<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<script>
			jQuery(document).ready(function() {
				Main.init();
				Login.init();
			});
		</script>
	</body>
	<!-- end: BODY -->
</html>