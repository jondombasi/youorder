<?php
require("../inc_connexion.php");
if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}

	$result = $sql->query("SELECT l.nom,l.prenom FROM commandes c INNER JOIN clients l ON c.client = l.id WHERE c.id = ".$sql->quote($id)." LIMIT 1");
	$ligne = $result->fetch();
	if($ligne!=""){
		$nom	 	= $ligne["nom"];
		$prenom	 	= $ligne["prenom"];
	}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Signature Pad demo</title>
  <meta name="description" content="Signature Pad - HTML5 canvas based smooth signature drawing using variable width spline interpolation.">

  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">

  <link rel="stylesheet" href="css/signature-pad.css">
  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>

</head>
<body onselectstart="return false">
	<form id="form1" name="form1" action="commandes_post_signature.php" method="post">
	<input type="hidden" name="commande" id="commande" value="<?=$id?>" />
    <input type="hidden" name="contenu_image" id="image" value="" />
    </form>
  <div id="signature-pad" class="m-signature-pad">
    <div class="m-signature-pad--header">
      <div class="description">Signature de <?php echo $prenom.' '.$nom; ?></div>
    </div>
    <div class="m-signature-pad--body">
      <canvas></canvas>
    </div>
    <div class="m-signature-pad--footer">
      <button class="button clear" data-action="clear">Effacer</button>
      <button class="button save" data-action="save">Enregistrer</button>
      <button class="button retour" onClick="lien('../commandes_visu.php?id=<?=$id?>')">Retour</button>
    </div>
  </div>

  <script src="js/signature_pad.js"></script>
  <script src="js/app.js"></script>
</body>
</html>
<script language="javascript" type="text/javascript">
function lien(url) {
	window.location.href = url
}
</script>
