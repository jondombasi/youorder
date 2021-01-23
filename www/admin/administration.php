<?php
require_once("inc_connexion.php");
$menu = "compte";

if(!$_SESSION["admin"]){
    header("location: home.php");
}

require_once("inc_header.php");
if(isset($_GET["nom"]))		{$nom=$_GET["nom"];}else{$nom="";}
if(isset($_GET["email"]))	{$email=$_GET["email"];}else{$email="";}
if(isset($_GET["role"]))	{$role=$_GET["role"];}else{$role="";}
if(isset($_GET["ret"]))		{$ret=$_GET["ret"];}else{$ret="";}

if ($nom=="" && $role=="" && $role=="") {
    $filtre='style="display:none;"';
    $filtre_fleche="expand";
}
else {
    $filtre_fleche="collapses";
}

$liste_users = new Utilisateur($sql);

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<!-- start: PAGE -->
<div class="main-content">
    <div class="container">

        <!-- content -->
        <div class="row header-page">
            <?php
                switch($ret){
                    case "1":
                        echo    '<div class="col-sm-12"><div class="alert alert-success">
                                <button class="close" data-dismiss="alert">
                                        ×
                                </button>
                                <i class="fa fa-check-circle"></i>
                                Les identifiants ont bien été ré-envoyés
                                </div></div>';                    
                        break;
                }
            ?>
            <div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-external-link-square"></i>
						Formulaire de recherche
						<div class="panel-tools">
							<a class="btn btn-xs btn-link panel-collapse <?=$filtre_fleche?>" href="#">
							</a>
							<a class="btn btn-xs btn-link panel-refresh" href="#">
								<i class="fa fa-refresh"></i>
							</a>
						</div>
					</div>
					<div class="panel-body" <?=$filtre?>>
                        <form class="form-horizontal" role="form" action="administration.php" method="get">
    						<div class="form-group">
    							<label class="col-sm-2 control-label" for="form-field-1">
                                    Nom
                                </label>
                                <div class="col-sm-9">
                               	  <input type="text" name="nom" placeholder="Nom" id="form-field-1" class="form-control" value="<?php echo $nom; ?>">
                                </div>
    						</div>
    						<div class="form-group">
    							<label class="col-sm-2 control-label" for="form-field-1">
                                    Email
                                </label>
                                <div class="col-sm-9">
                               	  <input type="text" name="email" placeholder="Email" id="form-field-1" class="form-control" value="<?php echo $email; ?>">
                                </div>
    						</div>
    						<div class="form-group">
    							<label class="col-sm-2 control-label" for="operation">
                                    Role
                                </label>
                                <div class="col-sm-9">
                                    <select name="role" id="role" class="form-control">
                                        <option value="">&nbsp;</option>
                                        <option <?php if($role=="admin"){echo 'selected="selected"';} ?> value="admin">Admin</option>
                                        <option <?php if($role=="planner"){echo 'selected="selected"';} ?> value="planner">Planner</option>
                                        <option <?php if($role=="restaurateur"){echo 'selected="selected"';} ?> value="restaurateur">Commerçant</option>
                                        <!-- <option <?php if($role=="inactif"){echo 'selected="selected"';} ?> value="inactif">Inactif</option> -->
                                    </select>
                                </div>
    						</div>
                            <div style="text-align:center;">
                                <input type="submit" id="bt" class="btn btn-main" value="Rechercher">
                            </div>
    					</form>
					</div>
				</div>                        
            </div>
            <div class="col-lg-6 btn-spe">
                <p style="text-align:right">
                    <?php
                    if($_SESSION["admin"]){
                        ?>
                        <a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_liste_users&nom=<?=$nom?>&email=<?=$email?>&role=<?=$role?>">Exporter en CSV</a>
                        <?php
                    }
                    ?>
                    <a class="btn btn-dark-green" href="administration_fiche.php">Ajouter un utilisateur</a>
                </p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="sample-table-1">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th style="width:130px;">Rôle</th>
                        <th>Numéro</th>
                        <th>Email</th>
                        <th style="width:50px;">Statut</th>
                        <th style="width:190px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $users=$liste_users->getAll("", "", $nom, $email, $role);
                    foreach ($users as $user) {
                        $id = $user->id;
						if($user->statut=="ON"){
							$txt_statut = '<span class="label label-success">ON</span>';	
						}else{
							$txt_statut = '<span class="label label-danger">OFF</span>';											
						}
                        if(strtoupper($user->role)=="RESTAURATEUR"){
                            $role = "COMMERCANT";
                        }else{
                            $role = strtoupper($user->role);
                        }
                        ?>
                        <tr>
                            <td><?=strtoupper($user->nom)?></td>
                            <td><?=$user->prenom?></td>
                            <td><?=$role?></td>
                            <td><?=$user->numero;?></td>
                            <td><?=$user->email;?></td>
                            <td><?= $txt_statut;?></td>
                            <td>
                                <a href="administration_fiche.php?id=<?=$id?>" class="btn btn-teal tooltips" data-placement="top" data-original-title="Modifier"><i class="fa fa-edit"></i></a>
                                <a href="action_poo.php?id=<?=$id?>&action=send_email" class="btn btn-teal tooltips" data-placement="top" data-original-title="Renvoyer les identifiants"><i class="fa fa-envelope-o "></i></a>
                                <a onclick="affecte_suppid('<?=$id?>')" href="#myModal" role="button"  data-toggle="modal" class="btn btn-bricky tooltips" data-placement="top" data-original-title="Supprimer"><i class="fa fa-times fa fa-white"></i></a>
                                <?php if ($role=="COMMERCANT") { ?>
                                    <a href="action_poo.php?action=connect_as&id=<?=$id?>" role="button"  data-toggle="modal" class="btn btn-default tooltips" data-placement="top" data-original-title="Se connecter en tant que"><i class="fa fa-long-arrow-right"></i></a>
                                <?php } ?>
                            </td>
                        </tr>                                            
                        <?php
                    }
                    ?>
                </tbody>                                	
            </table>  
		</div>  

        <!-- MODAL -->                       
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">Supprimer un utilisateur</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="suppid" id="suppid" value="" />                                                
                        <p>
                            Etes-vous sûr de vouloir supprimer cet utilisateur ?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button onclick="affecte_suppid('')" aria-hidden="true" data-dismiss="modal" class="btn btn-default">
                            Annuler
                        </button>
                        <button onclick="confirm_suppression('supputilisateur')" class="btn btn-default" data-dismiss="modal">
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>                            

	</div>
</div>
<!-- end: PAGE -->

<?php
require_once("inc_footer.php");
?>