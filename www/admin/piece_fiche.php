<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 17/02/2017
 * Time: 14:09
 */

require_once("inc_connexion.php");

if(isset($_GET["id"]))		    {$id_piece  = $_GET["id"];}         else{$id_piece  = "";}
if(isset($_GET["aff_valide"]))	{$aff_valide= $_GET["aff_valide"];} else{$aff_valide= "";}
if(isset($_POST["action"]))		{$action    = $_POST["action"];}    else{$action    = "";}

$menu       = "materiel";
$sous_menu  = "fiche";
$aff_erreur = "";
$continu    = true;

$display_commentaire="display:none;";

$Materiel = new Materiel($sql, $id_piece);

if($id_piece==""){
    $titre_page = "Ajouter une Pièce";
}
else{
    $titre_page = "Modifier une Pièce";


    $code       = $Materiel->getCode();
    $libelle    = $Materiel->getLibelle();
    $quantite   = $Materiel->getQuantite();
    $prix_ht    = $Materiel->getPrixHt();

}

if($action=="enregistrer"){
    $code       =$_POST["code"];
    $libelle    =$_POST["libelle"];
    $quantite   =$_POST["quantite"];
    $prix_ht    =$_POST["prix_ht"];


    if($code==""){
        $css_code_obl = "has-error";
        $continu = false;
    }
    /*elseif (($Materiel->checkCode($code))){
        $continu=false;
        $css_code_obl = "has-error";
    }*/


    if($libelle==""){
        $css_libelle_obl = "has-error";
        $continu = false;
    }


    if($quantite==""){
        $css_quantite_obl = "has-error";
        $continu = false;
    }
    else if (!is_numeric($quantite)) {
        $css_quantite_obl = "has-error";
        $continu = false;
    }


    if($prix_ht==""){
        $css_price_obl = "has-error";
        $continu = false;
    }
    else if (!is_numeric($prix_ht)) {
        $css_price_obl = "has-error";
        $continu = false;
    }


    if($continu){
        if ($id_piece != "") {
            $Materiel->updatePiece($id_piece, $code, $libelle, $quantite, $prix_ht);
            header("location: piece_fiche.php?aff_valide=1&id=" . $id_piece);
        }
        else {
            $Materiel->setPiece($code, $libelle, $quantite, $prix_ht);
            header("location: piece_fiche.php?aff_valide=1");
        }
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
                <form role="form" name="form" id="form1" method="post" action="piece_fiche.php?id=<?php echo $id_piece; ?>" class="form-horizontal">
                    <input type="hidden" name="action" value="enregistrer"/>

                    <div class="form-group <?php echo $css_code_obl; ?>">
                        <label class="col-sm-4 control-label">
                            Code Produit
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="code" placeholder="Code" class="form-control" value="<?=$code?>">
                        </div>
                    </div>
                    <div class="form-group <?php echo $css_libelle_obl; ?>">
                        <label class="col-sm-4 control-label">
                            Libelle
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="libelle" placeholder="Libelle" class="form-control" value="<?=$libelle?>">
                        </div>
                    </div>
                    <div class="form-group <?php echo $css_quantite_obl; ?>">
                        <label class="col-sm-4 control-label">
                            Quantite
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="quantite" placeholder="Quantite" class="form-control" value="<?=$quantite?>">
                        </div>
                    </div>
                    <div class="form-group <?php echo $css_price_obl; ?>">
                        <label class="col-sm-4 control-label">
                            Prix HT
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="prix_ht" placeholder="Prix Hors Taxe" class="form-control" value="<?=$prix_ht?>">
                        </div>
                    </div>

                    <div class="row row_btn">
                        <div class="col-sm-4 col-sm-offset-8" style="text-align:right">
                            <input type="button" onclick="lien('piece_liste.php')" id="bt" class="btn btn-light-grey" value="Retour" style="width:100px;">
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
