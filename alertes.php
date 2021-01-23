<?php
$menu = "dashboard";
require_once("inc_header.php");

if(isset($_GET["date_debut"]))		{$date_debut=$_GET["date_debut"];}else{$date_debut=date("d-m-Y",time());}
if(isset($_GET["date_fin"]))		{$date_fin=$_GET["date_fin"];}else{$date_fin=date("d-m-Y",time());}

?>
		<link rel="stylesheet" href="assets/plugins/select2/select2.css">
        <link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
			<!-- start: PAGE -->
			<div class="main-content">
				<div class="container">
					<!-- start: PAGE HEADER -->
					<div class="row">
						<div class="col-sm-12">
							<div class="page-header">
								<h1>Alertes</h1>
							</div>
							<!-- end: PAGE TITLE & BREADCRUMB -->
						</div>
					</div>
					<!-- end: PAGE HEADER -->
					<!-- start: PAGE CONTENT -->
					<div class="row">
						<div class="col-sm-3"></div>
                        <div class="col-sm-6">
							<div class="panel panel-default">
								<div class="panel-heading">
									<i class="fa fa-external-link-square"></i>
									Formulaire de recherche
									<div class="panel-tools">
										<a class="btn btn-xs btn-link panel-collapse collapses" href="#">
										</a>
										<a class="btn btn-xs btn-link panel-refresh" href="#">
											<i class="fa fa-refresh"></i>
										</a>
									</div>
								</div>
								<div class="panel-body">
                                <form class="form-horizontal" role="form" action="alertes.php" method="get">
									<!--
                                    <div class="form-group">
										<label class="col-sm-3 control-label" for="form-field-1">
                                            Date de début
                                        </label>
                                        <div class="col-sm-6">
                                            <div class="input-group" style="margin-bottom:5px;">
                                                <input type="text" name="date_debut" value="<?php //echo $date_debut ?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                                                <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                            </div>
                                        </div>
									</div>
                                    -->
									<div class="form-group">
										<label class="col-sm-3 control-label" for="form-field-1">
                                            Date maximum
                                        </label>
                                        <div class="col-sm-6">
                                            <div class="input-group" style="margin-bottom:5px;">
                                                <input type="text" name="date_fin" value="<?php echo $date_fin ?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                                                <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                            </div>
                                        </div>
									</div>
                                    <div style="text-align:center;">
                                    <input type="submit" id="bt" class="btn btn-light-grey" value="Rechercher">
                                    </div>
								</form>
								</div>
							</div>                        
                        </div>
						<div class="col-sm-3"></div>
                    </div>

                    <table class="table table-bordered table-hover" id="sample-table-1">
                        <thead>
                            <tr>
                                <th>Notaire</th>
                                <th>Commentaire</th>
                                <th style="width:150px;">Date Relance</th>
                                <th style="width:100px;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $date_relance_debut = date("Y-m-d",strtotime($date_debut))." 00:00:00";
                            $date_relance_fin = strtotime(date("Y-m-d",strtotime($date_fin))." 23:59:59");
                            $date_relance_fin = date("Y-m-d H:i:s",$date_relance_fin);
							if($_SESSION["acl_dept"]=="0"){
								$req_sup_dept = " AND left(t.cp,2) IN (".$_SESSION["liste_dept"].") ";
							}else{$req_sup_dept = "";}
							
                            $req = "SELECT a.*,n.id as notaire,t.cp,n.nom,n.prenom,n.telephone,n.email,e.label,e.texte FROM notaires_actions a inner join notaires n on a.notaire=n.id inner join etats e on a.relance=e.id inner join etudes t on t.id = n.etude WHERE `date_relance` <= '".$date_relance_fin."' and traite = 0 ".$req_sup_dept." ORDER BY date_relance ASC";	//`date_relance` >= '".$date_relance_debut."' and 
							//echo $req;
							$result = $sql->query($req);
                            $vide = true;
                            //var_dump($result);
                            while($ligne = $result->fetch()) {
									$vide = false;
                                    $date_creation = date("d/m/Y H:i",strtotime($ligne["date_action"]));
                                    if(!is_null($ligne["date_relance"])){
                                    $date_relance = date("d/m/Y H:i",strtotime($ligne["date_relance"]));
                                    }else{$date_relance = "";}

									$traite = $ligne["traite"];
									if($traite=="1"){
										$css = "opacity:0.30;filter:alpha(opacity=30);";
									}else{
										$css = "";	
									}
									?>
                                    <tr style="<?php echo $css; ?>">
                                        <td>
                                            <?php
                                            echo "<b>".$ligne["nom"].' '.$ligne["prenom"].'</b> <i>('.$ligne["cp"].')</i><br/>';
                                            echo $ligne["email"].'<br/>';
                                            echo $ligne["telephone"];
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo '<span class="label label-sm label-'.$ligne["label"].'">'.$ligne["texte"].'</span>&nbsp;';
                                            echo $ligne["commentaires"] 
                                            ?>
                                        </td>
                                        <td><?php echo $date_relance; ?></td>
                                        <td>
											<?php if($traite!="1"){ ?>
                                            <a href="action.php?action=action_traite&id=<?php echo $ligne["id"] ?>&src=alerte" class="btn btn-green tooltips" data-placement="top" data-original-title="Marquer comme traité"><i class="clip-checkmark-2"></i></a>
                                            <?php } ?>
                                            <a href="notaires_fiche.php?id=<?php echo $ligne["notaire"] ?>#action" class="btn btn-primary tooltips" data-placement="top" data-original-title="Accéder"><i class="fa fa-arrow-circle-o-right"></i></a>
                                        </td>
                                    </tr>                                            
                                <?php
                            }	
                            if($vide){
                                ?>
                                <tr>
                                    <td colspan="4">Aucune alerte en cours pour les 3 prochains jours</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>

				</div>
			</div>
			<!-- end: PAGE -->
<?php
require_once("inc_footer.php");
?>
			<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
            <script language="javascript">
				function runDatePicker() {
					$('.date-picker').datepicker({
						autoclose: true
					});
				};
				jQuery(document).ready(function() {
					runDatePicker();
				});
			</script>
