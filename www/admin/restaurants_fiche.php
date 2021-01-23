<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}

$menu = "resto";
if($id==""){
    $sous_menu = "fiche";
    $titre_page = "Ajouter un commerçant";
}else{
    $sous_menu = "liste";
    $titre_page = "Modifier un commerçant";
}
$aff_erreur = "";

$Commercant = new Commercant($sql, $id);

if($id!=""){
    $nom=$Commercant->getNom();
    $adresse=$Commercant->getAdresse();
    $longitude=$Commercant->getLongitude();
    $latitude=$Commercant->getLatitude();
    $contact=$Commercant->getContact();
    $numero=$Commercant->getNumero();
    $perso_suivi=$Commercant->getPersoSuivi();
    if ($Commercant->getPhoto()!="") {
        $source_photo="upload/restaurants/".$Commercant->getPhoto();
    }
    $sms_client=$Commercant->getSmsClient();
    $sms_client_txt=$Commercant->getSmsClientTxt();
    $type_livraison=$Commercant->getTypeLivraison();
}

$continu = true;
if($action=="enregistrer"){
    $nom	 	= $_POST["nom"];
    $adresse 	= $_POST["adresse"];
    $longitude	= $_POST["longitude"];
    $latitude	= $_POST["latitude"];
    $contact	= $_POST["contact"];
    $numero 	= $_POST["numero"];
    $perso_suivi 	= $_POST["perso_suivi"];
    $perso_suivi_logo 	 = $_FILES['ImageFile'];
    $sms_client 	= $_POST["sms_client"];
    $sms_client_txt 	= $_POST["sms_client_txt"];
    $type_livraison 	= $_POST["type_livraison"];

    if ($perso_suivi=="") {
        $perso_suivi="off";
        $perso_suivi_logo="";
    }
    if ($sms_client=="") {
        $sms_client="off";
        $sms_client_txt="";
    }

    if($nom==""){
        $css_nom_obl = "has-error";
        $continu = false;
    }
    if($numero==""){
    }else{
        $regexp_mail = "/^0[0-9]([-. ]?\d{2}){4}[-. ]?$/";
        if(!preg_match($regexp_mail, $numero)) {
            $css_telephone_obl = "has-error";
            $continu = false;
        }
    }
    if($adresse=="" || $longitude==0 || $latitude==0){
        $css_adresse_obl = "has-error";
        $continu = false;
    }

    if($continu){
        $id=$Commercant->setCommercant($id, $nom, $adresse, $latitude, $longitude, $contact, $numero, $_SESSION["userid"], $perso_suivi, $sms_client, $sms_client_txt, $type_livraison);
        if ($perso_suivi_logo!='') {
            $directory="restaurants";
            include("action_photo.php");
        }
        else {
            $Commercant->setPhoto($id, '');
        }
        //header("location: restaurants_fiche.php?aff_valide=1&id=".$id);
        $aff_valide = "1";
    }else{
        $aff_erreur="1";
    }
}

require_once("inc_header.php");

?>
<style>
    #map{
        width:100%;
        height:300px;
        background-color:#DFDFDF;
    }

    textarea {
        margin:0px;
    }

    .option_commercant{
        position: relative;
        height: 50px;
    }

    .option_commercant_lg  {
        position: relative;
        height:150px;
    }

    .option_commercant .option_commercant_child{
        top: 50%;
        transform: translateY(-50%);
    }

    .option_commercant_lg .option_commercant_child{
        margin-top:10px;
    }

    .option_commercant_top{border:1px solid #eee;}

    .option_commercant_middle{
        border-left:1px solid #eee;
        border-right:1px solid #eee;
    }

    .option_commercant_child_right {
        text-align:right;
    }

    .option_commercant .col-sm-2, .option_commercant_lg .col-sm-2{text-align:right;}

    @media (max-width:800px){
        .r-stop{clear:both}
        .option_commercant_child{transform:translateY(0);}
        .option_commercant, .option_commercant_lg{border: none !important;}
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
<link rel="stylesheet" href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css" type="text/css"/>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC15w0ru2bvazBjNdaHtVLXngRT6JfSh2s"></script>
<link rel="stylesheet" href="assets/plugins/switchery/dist/switchery.css"/>
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
        <div class="row"  style="margin-top:40px;">
            <div class="col-sm-12">
                <form role="form" name="form" id="form1" method="post" action="restaurants_fiche.php?id=<?php echo $id; ?>" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="enregistrer">
                    <input type="hidden" name="longitude" id="longitude" value="<?=$longitude?>">
                    <input type="hidden" name="latitude" id="latitude" value="<?=$latitude?>">
                    <div class="form-group <?php echo $css_nom_obl; ?>">
                        <label class="col-sm-4 control-label" for="form-field-1">
                            Nom<span class="symbol required"></span>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="nom" id="nom" placeholder="Nom" class="form-control" value="<?php echo $nom; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">
                            Contact
                        </label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Contact" class="form-control" id="contact" name="contact" value="<?php echo $contact; ?>">
                        </div>
                    </div>
                    <div class="form-group <?php echo $css_telephone_obl; ?>">
                        <label class="col-sm-4 control-label">
                            Numéro
                        </label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Numéro" class="form-control" id="numero" name="numero" value="<?php echo $numero; ?>">
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
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <div id="map"></div>
                        </div>
                    </div>

                    <?php if ($_SESSION["admin"]) { ?>
                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding-left:10px">Options</div>
                                    <div class="panel-body">
                                        <div class="<?=($perso_suivi=='on') ? 'option_commercant_lg' : 'option_commercant' ?> option_commercant_top">
                                            <div class="col-sm-9 option_commercant_child option_commercant_child_left">
                                                Personnaliser la page de suivi de la commande
                                            </div>
                                            <div class="col-sm-3 option_commercant_child option_commercant_child_right">
                                                <input type="checkbox" class="js-switch" id="perso_suivi" name="perso_suivi" <?php if ($perso_suivi=="on") echo "checked";?>/>
                                            </div>
                                            <div id="perso_suivi_div" class="col-sm-12" style="margin-top:10px;<?php if ($perso_suivi!="on") echo 'display:none';?>">
                                                <div class="fileupload <?php echo ($source_photo=='') ? 'fileupload-new' : 'fileupload-exists' ;?>" data-provides="fileupload">
                                                    <div class="fileupload-new thumbnail" style="max-width: 50px; max-height:50px;">
                                                        <img src="http://www.placehold.it/300x300/EFEFEF/AAAAAA?text=no+image" alt="">
                                                    </div>
                                                    <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 50px; max-height: 50px; line-height: 20px;">
                                                        <?php if ($source_photo!="") { ?>
                                                            <img src="<?=$source_photo?>" alt="">
                                                        <?php } ?>
                                                    </div>
                                                    <div>
											    		<span class="btn btn-xs btn-light-grey btn-file">
											    			<span class="fileupload-new"><i class="fa fa-picture-o"></i> Choisir une image</span>
											    			<span class="fileupload-exists"><i class="fa fa-picture-o"></i> Changer</span>
											                <input type="file" name="ImageFile" id="ImageFile"/>
											            </span>
                                                        <a href="#" class="btn btn-xs fileupload-exists btn-light-grey" data-dismiss="fileupload">
                                                            <i class="fa fa-times"></i> Supprimer
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="<?=($sms_client=='on') ? 'option_commercant_lg' : 'option_commercant' ?> option_commercant_middle">
                                            <div class="col-sm-9 option_commercant_child option_commercant_child_left">
                                                Envoyer un SMS de suivi de commande vers le client final
                                            </div>
                                            <div class="col-sm-3 option_commercant_child option_commercant_child_right">
                                                <input type="checkbox" class="js-switch" id="sms_client" name="sms_client" <?php if ($sms_client=="on") echo "checked";?>/>
                                            </div>
                                            <div class="col-sm-12" style="margin-top:10px;<?php if ($sms_client!="on") echo 'display:none';?>" id="sms_client_div">
                                                <textarea class="autosize form-control" id="sms_client_txt" name="sms_client_txt" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 95px;" placeholder="Texte du SMS (max 100 caractères)" maxlength="100"><?=$sms_client_txt?></textarea>
                                            </div>
                                        </div>
                                        <div class="option_commercant option_commercant_top">
                                            <div class="col-sm-9 option_commercant_child option_commercant_child_left">
                                                Type de livraison
                                            </div>
                                            <div class="col-sm-3 option_commercant_child option_commercant_child_right">
                                                <select name="type_livraison" class="form-control">
                                                    <option value="tournee" <?php if ($type_livraison=="tournee") echo "selected";?>>Tournée</option>
                                                    <option value="etoile" <?php if ($type_livraison=="etoile") echo "selected";?>>Étoile</option>
                                                </select>
                                            </div>
                                            <div class='r-stop'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($_SESSION["restaurateur"]) { ?>
                        <input type="hidden" id="perso_suivi" name="perso_suivi" value="<?=$perso_suivi?>"/>
                        <input type="hidden" id="sms_client" name="sms_client" value="<?=$sms_client?>"/>
                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding-left:10px">Options</div>
                                    <div class="panel-body">
                                        <div class="<?=($perso_suivi=='on') ? 'option_commercant_lg' : 'option_commercant' ?> option_commercant_top">
                                            <div class="col-sm-9 option_commercant_child option_commercant_child_left">
                                                Personnaliser la page de suivi de la commande
                                            </div>
                                            <div class="col-sm-3 option_commercant_child option_commercant_child_right">
                                                <?=($perso_suivi=="on") ? "<i class='clip-checkmark-2' style='color:#9fc752'></i>" : "<i class='clip-close' style='color:red'></i>";?>
                                            </div>
                                            <?php if ($perso_suivi=="on") {?>
                                                <div id="perso_suivi_div" class="col-sm-12" style="margin-top:10px">
                                                    <div class="fileupload <?php echo ($source_photo=='') ? 'fileupload-new' : 'fileupload-exists' ;?>" data-provides="fileupload">
                                                        <div class="fileupload-new thumbnail" style="max-width: 50px; max-height:50px;">
                                                            <img src="http://www.placehold.it/300x300/EFEFEF/AAAAAA?text=no+image" alt="">
                                                        </div>
                                                        <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 50px; max-height: 50px; line-height: 20px;">
                                                            <?php if ($source_photo!="") { ?>
                                                                <img src="<?=$source_photo?>" alt="">
                                                            <?php } ?>
                                                        </div>
                                                        <div>
												    		<span class="btn btn-xs btn-light-grey btn-file">
												    			<span class="fileupload-new"><i class="fa fa-picture-o"></i> Choisir une image</span>
												    			<span class="fileupload-exists"><i class="fa fa-picture-o"></i> Changer</span>
												                <input type="file" name="ImageFile" id="ImageFile"/>
												            </span>
                                                            <a href="#" class="btn btn-xs fileupload-exists btn-light-grey" data-dismiss="fileupload">
                                                                <i class="fa fa-times"></i> Supprimer
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="<?=($sms_client=='on') ? 'option_commercant_lg' : 'option_commercant' ?> option_commercant_middle">
                                            <div class="col-sm-9 option_commercant_child option_commercant_child_left">
                                                Envoyer un SMS de suivi de commande vers le client final
                                            </div>
                                            <div class="col-sm-3 option_commercant_child option_commercant_child_right">
                                                <?=($sms_client=="on") ? "<i class='clip-checkmark-2' style='color:#9fc752'></i>" : "<i class='clip-close' style='color:red'></i>";?>
                                            </div>
                                            <?php if ($sms_client=="on") {?>
                                                <div class="col-sm-12" style="margin-top:10px">
                                                    <textarea class="autosize form-control" id="commentaire_new" name="sms_client_txt" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 95px;" placeholder="Texte du SMS"><?=$sms_client_txt?></textarea>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="option_commercant option_commercant_top">
                                            <div class="col-sm-9 option_commercant_child option_commercant_child_left">
                                                Type de livraison
                                            </div>
                                            <div class="col-sm-3 option_commercant_child option_commercant_child_right">
                                                <select name="type_livraison" class="form-control">
                                                    <option value="tournee" <?php if ($type_livraison=="tournee") echo "selected";?>>Tournée</option>
                                                    <option value="etoile" <?php if ($type_livraison=="etoile") echo "selected";?>>Étoile</option>
                                                </select>
                                            </div>
                                            <div class='r-stop'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row row_btn">
                        <div class="col-sm-6 col-sm-offset-6" style="text-align:right">
                            <input type="button" onclick="lien('restaurants_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
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
<script src="assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="assets/plugins/switchery/dist/switchery.js"></script>
<script type="text/javascript" src="./assets/js/gmaps.js"></script>
<script language="javascript" type="text/javascript">
    $(window).load(function(){
        autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('adresse')));

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            fillInAddress();

        });
    });

    var map;
    $(document).ready(function(){
        $("textarea.autosize").autosize();
        <?php if($latitude!="" && $longitude!=""){ ?>
        map = new GMaps({
            el: '#map',
            zoom: 13,
            lat: <?=$latitude?>,
            lng: <?=$longitude?>
        });
        map.addMarker({
            lat: <?=$latitude?>,
            lng: <?=$longitude?>
        });
        <?php } else {?>
        map = new GMaps({
            el: '#map',
            zoom: 13,
            lat: 48.8555799,
            lng: 2.3591637
        });
        <?php } ?>

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html, {color: '#9fc752', jackColor: '#fff', size: 'small' });
        });

        var changeCheckbox = document.querySelector('#perso_suivi');
        changeCheckbox.onchange = function() {
            console.log(changeCheckbox.checked)
            $("#perso_suivi_div").toggle();
            $("#perso_suivi_div").parent().toggleClass("option_commercant_lg option_commercant");
        };

        var changeCheckbox2 = document.querySelector('#sms_client');
        changeCheckbox2.onchange = function() {
            $("#sms_client_div").toggle();
            $("#sms_client_txt").parent().parent().toggleClass("option_commercant_lg option_commercant");
        };
    });

    function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
        $("#longitude").val(place.geometry.location.lng());
        $("#latitude").val(place.geometry.location.lat());
        map = new GMaps({
            el: '#map',
            zoom: 13,
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng()
        });
        map.addMarker({
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng()
        });
    }
</script>