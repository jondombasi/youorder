<?php
ini_set("display_errors", 1);

$sql_serveur    = "localhost";
$sql_user       = "youorder";
$sql_passwd     = "75LrhfPSOqCv";
$sql_bdd        = "youorder";
$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Commande.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Commercant.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Livreur.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/classes/Client.php');

if(isset($_GET["id"])){$id=$_GET["id"];}else{$id="";}

$Commande=new Commande($sql, $id);
$Commercant=new Commercant($sql, $Commande->getRestaurant());
$Livreur=new Livreur($sql, $Commande->getLivreur());
$Client=new Client($sql, $Commande->getClient());
?>

<!DOCTYPE html>
<html>
    
    <head>
        <title>Profil</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0,  minimun-scale=1.0, maximum-scale=1.0">
        
        <!-- style -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:400,700">
        <link rel="stylesheet" href="css/suivi_commande.css">
        <!-- /style -->
    
    </head>
    
    <body>
        
        <!-- header -->
        <header>
            <?php
            if ($Commercant->getPersoSuivi()=="on" && $Commercant->getPhoto()!='') {
                ?>
                <img src="<?='../admin/upload/restaurants/'.$Commercant->getPhoto()?>" alt="logo">
                <?php
            }
            else {
                ?>
                <img src="../image/logo_original.png" alt="logo"/>
                <?php
            }
            ?>
            <h1><?=$Commercant->getNom()?></h1>
        </header>
        <!-- /header -->
        
        <!-- content -->
        <div class="content">
            
            <!-- commande -->
            <div class="content-item">
                <h2>votre commande</h2>
                <img src="images/pin2.png" alt="pin"><span><?=$Client->getAdresse()?></span><br>
                <img class="clock" src="images/clock4.png" alt="clock"><span>Aujourd'hui entre <?=date("H:i", strtotime($Commande->getDateDebut()))?> et <?=date("H:i", strtotime($Commande->getDateFin()))?></span>
            </div>
            <!-- /commande -->
            
            <!-- livreur -->
            <div class="content-item">
                <h2>votre livreur</h2>
                <img class="profil-img" src="<?=($Livreur->getPhoto()=='') ? '../admin/images/no_avatar.png' : '../admin/upload/livreurs/'.$Livreur->getPhoto()?>" alt="profil">
                <div class="profil-detail">
                    <span><?=$Livreur->getPrenom()?></span><br>
                    <!-- <span>Par véhicule électrique &nbsp;<img src="images/power2.png" alt="power"></span> -->
                </div>
                <div class="stop"></div>
            </div>
            <!-- /livreur -->
            
            <!-- map -->
            <div class="content-item map">
                <h2>ma commande en temps réel</h2>
                <div id="map">
                </div>
            </div>
            <!-- /map -->
            
            <!-- footer -->
            <footer>
                <span>Service de livraison youOrder.</span><br>
                <span>Une question ? ops@youorder.fr</span>
            </footer>
            <!-- /footer -->
            
        </div>
        <!-- /content -->
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC15w0ru2bvazBjNdaHtVLXngRT6JfSh2s"></script>
        <script type="text/javascript" src="../admin/assets/js/gmaps.js"></script>

        <script>
            var map;
            $(document).ready(function() {
                map = new GMaps({
                    el: '#map',
                    zoom: 14,
                    lat: 48.8555799,
                    lng: 2.3591637 
                });

                map.addMarker({
                    lat: <?=$Livreur->getLatitude()?>,
                    lng: <?=$Livreur->getLongitude()?>,
                    title: '<?=$Livreur->getPrenom()?>',
                    icon: '../admin/images/picto_location.png'
                });

                map.setCenter({
                    lat: <?=$Livreur->getLatitude()?>,
                    lng: <?=$Livreur->getLongitude()?>
                });

                setTimeout(function() {
                    load_marker();
                },5000);
            });

            function load_marker() {
                $.ajax({
                    url      : 'https://www.you-order.eu/admin/action_poo.php?action=get_position_livreur',
                    data     : "id_livreur=<?=$Commande->getLivreur()?>",
                    type     : "GET",
                    cache    : false,         
                    success:  function(transport) {
                        //console.log(transport); 
                        map.removeMarkers();
                        var res = $.parseJSON(transport);
                        console.log(res[0][0]);
                        map.addMarker({
                            lat: parseFloat(res[0][1]),
                            lng: parseFloat(res[0][0]),
                            title: res[0][3],
                            icon: '../admin/images/picto_location.png'
                        });
                        map.setCenter({
                            lat: parseFloat(res[0][1]),
                            lng: parseFloat(res[0][0])
                        });
                        setTimeout(function() {
                            load_marker();
                        },5000);
                    }
                });
            }
            
        </script>
    </body>
    
</html>