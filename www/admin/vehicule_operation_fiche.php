<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))              {$id        =$_GET["id"];}          else{$id="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}  else{$aff_valide="";}
if(isset($_POST["action"]))		    {$action    =$_POST["action"];}     else{$action="";}

$menu                = "vehicule";
$sous_menu           = "operation";
$aff_erreur          = "";
$continu             = true;
$display_commentaire = "display:none;";

$Vehicule = new Vehicule($sql, $id);
$Materiel = new Materiel($sql);
$Action   = new Action($sql, $id);

$vehicules  = $Vehicule ->getAll('', '', '', '');
$actions    = $Action   ->getAll();
$pieces     = $Materiel ->getAll('', '', '', '');

$libelle    = $Action   ->getLibelle();

if($id == ""){
    $titre_page = "Effectuer une opération";

    if($action == "ajouter") {

        $libelle = $_POST['libelle'];

        if ($libelle == "") {
            $css_libelle_obl = "has-error2";
            $continu = false;
        }

        $id_actions = $Action->setActions($libelle);
        header("location: vehicule_operation_fiche.php?aff_valide=2");
    }
}

if ($id != ""){
    $titre_page = "Effectuer une opération pour le véhicule " . $Vehicule->getImmatriculation();

    $immatriculation = $Vehicule->getImmatriculation();

    if($action == "ajouter") {

        $libelle = $_POST['libelle'];

        if ($libelle == "") {
            $css_libelle_obl = "has-error2";
            $continu = false;
        }

        $id_actions = $Action->setActions($libelle);

        header("location: vehicule_operation_fiche.php?aff_valide=2&id=".$id);
    }
}



if($action=="enregistrer"){

    $post_vehicule      = $_POST['vehicule'];
    $post_actions       = $_POST['actions'];

    $post_pieces        = $_POST['pieces'];
    $commentaire        = $_POST['commentaire'];

    if($post_vehicule==""){
        $css_vehicule_obl = "has-error";
        $continu = false;
    }

    if($post_actions==""){
        $css_actions_obl = "has-error";
        $continu = false;
    }

//    if($post_pieces==""){
//        $css_pieces_obl = "has-error";
//        $continu = false;
//    }

    if($continu){

        $Operation = new Operation($sql, null, $post_vehicule);
        $Operation->setOperation($post_vehicule, $_SESSION["userid"], $commentaire, $post_actions, $post_pieces);
        header("location: vehicule_operation_fiche.php?aff_valide=1&id=".$id);

    }else{
        $aff_erreur="1";
    }
}


require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places&key=AIzaSyC15w0ru2bvazBjNdaHtVLXngRT6JfSh2s"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

<!--<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css" type="text/css"/>
<script type="text/javascript" src="assets/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>



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
                <?= $message ?>
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
                L'operation a été enregistrée
            </div>
        <?php }

        elseif($aff_valide=="2"){
            ?>
        <div class="alert alert-success">
            <button class="close" data-dismiss="alert">
                ×
            </button>
            <i class="fa fa-check-circle"></i>
            L'action a bien été sauvegardé
        </div>
        <?php } ?>

        <div class="row" style="margin-top:40px;">
            <div class="col-sm-12">
                <form role="form" name="form" id="form1" method="post" action="vehicule_operation_fiche.php?id=<?= $id; ?>" class="form-horizontal">
                    <input type="hidden" name="action" value="enregistrer"/>

                    <?php if($id == ''):?>
                    <div class="form-group <?=$css_vehicule_obl?>">
                        <label class="col-sm-4 control-label">Véhicule</label>
                        <div class="col-sm-4 margin_label">
                            <select name="vehicule" id="type" class="form-control">
                                <option value="">&nbsp;</option>
                                <?php foreach ($vehicules as $v):?>
                                <option value="<?= $v->id?>"><?=$v->immatriculation?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php else:?>
                        <div class="form-group <?=$css_vehicule_obl?>">
                            <input type="hidden" name="vehicule" value="<?=$id?>"/>
                        </div>
                    <?php endif;?>

                    <div class="form-group <?php echo $css_actions_obl; ?>">
                        <label class="col-sm-4 control-label">
                            Action
                        </label>
                        <div class="col-sm-4">
                            <select multiple="multiple" id="select-Actions" name="actions[]">
                                <?php foreach ($actions as $a):?>
                                    <option value="<?=$a->id?>"><?= $a->libelle?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-sm-1 " style="margin-top:3px;padding-left: 0;">
                            <a href="#addActionl3" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Ajouter une action"><i class=" fa-plus fa fa-white"></i></a>
                        </div>
                    </div>
                    <div class="form-group <?php echo $css_pieces_obl; ?>">
                        <label class="col-sm-4 control-label">
                           Pièces
                        </label>
                        <div class="col-sm-4">
                            <select multiple="multiple" id="select-Pieces" name="pieces[]">
                                <?php foreach ($pieces as $p):?>
                                    <option value="<?=$p->id?>"><?= $p->code?> - <?= $p->libelle?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">
                            Commentaire
                        </label>
                        <div class="col-sm-4">
                            <textarea type="text" name="commentaire" placeholder="" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row row_btn">
                        <div class="col-sm-4 col-sm-offset-8" style="text-align:right">
                            <input type="submit" id="bt" class="btn btn-main" value="enregistrer" style="width:100px;">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="addActionl3" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">
                            Ajouter une Action
                        </h4>
                    </div>
                    <!-- End Modal Header -->

                    <div class="modal-body" style="text-align:center">
                        <form role="form" name="form" id="form1" method="post"  class="form-horizontal">
                            <input type="hidden" name="action" value="ajouter"/>

                            <div class="form-group <?php echo $css_libelle_obl; ?>">
                                <label class="col-sm-4 control-label">
                                    Libelle
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" name="libelle" placeholder="Libelle" class="form-control" value="<?=$libelle?>">
                                </div>
                            </div>

                            <div class="row row_btn">
                                <div class="col-sm-4 col-sm-offset-8" style="text-align:right">&nbsp;
                                    <input type="submit" id="bt" class="btn btn-main" value="ajouter" style="width:100px;">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <!-- end: PAGE CONTENT-->
        </div>
    </div>
</div>
<!-- end: PAGE -->

<?php
require_once("inc_footer.php");
?>
<link rel="stylesheet" href="assets/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" type="text/css"/>
<script type="text/javascript" src="assets/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
<script src="assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.multi-select.js"></script>
<script language="javascript">
    function runSelect2() {

        $(document).ready(function() {
            $('#example-multiple-selected').multiselect();
        });

        $(".search-select").select2({
            placeholder: "Select a State",
            allowClear: true
        });
    };


    $('#select-Actions').multiSelect({});
    $('#select-Pieces').multiSelect({});



</script>

