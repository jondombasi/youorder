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
$Livreur=new Livreur($sql, $Commande->getLivreur());
$Client=new Client($sql, $Commande->getClient());

$result = $sql->query("SELECT * FROM livreurs_notes WHERE id_commande=".$sql->quote($id));
$ligne = $result->fetch();
if ($ligne) {
    header("Location: confirmation.php");
}

?>

<!DOCTYPE html>
<html>
    
    <head>
        <title>Profil</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0,  minimun-scale=1.0, maximum-scale=1.0">
        
        <!-- style -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:400,700">
        <link rel="stylesheet" href="css/notation.css">
        <!-- /style -->
    
    </head>
    
    <body>
        
        <!-- page -->
        <div id="page">
        
            <!-- header -->
            <header>
                Merci d'avoir utilisé You Order !
            </header>
            <!-- /header -->

            <!-- content -->
            <div class="content">

                <!-- bilan carbone -->
                <div class="content-item">
                    <h2>bilan carbone de votre commande</h2>
                    <div class="bilan">
                        <span>économie de carbone réalisée grâce au <br>véhicule électrique</span><br>
                        <span class="bilan-total"><?=$Commande->getEcoCarbonne($id)?><sup>kg</sup></span>
                    </div>
                </div>
                <!-- /bilan carbone -->

                <!-- recapitulatif -->
                <div class="content-item">
                    <h2>récapitulatif de votre commande</h2>
                    <img src="images/pin2.png" alt="pin"><span><?=$Client->getAdresse()?></span><br>
                    <img src="images/clock4.png" alt="clock"><span>Aujourd'hui entre <?=date("H:i", strtotime($Commande->getDateDebut()))?> et <?=date("H:i", strtotime($Commande->getDateFin()))?></span>
                </div>
                <!-- /recapitulatif -->

                <!-- notation -->
                <div class="content-item notation">
                    <h2>notation du livreur</h2>
                    <img src="<?=($Livreur->getPhoto()=='') ? '../admin/images/no_avatar.png' : '../admin/upload/livreurs/'.$Livreur->getPhoto()?>" alt="profil"><br>
                    <span><?=$Livreur->getPrenom()?></span>
                    <div class="note">
                        <img id="star1" onclick="noter(1)" src="images/notation-off.png" alt="star">
                        <img id="star2" onclick="noter(2)" src="images/notation-off.png" alt="star">
                        <img id="star3" onclick="noter(3)" src="images/notation-off.png" alt="star">
                        <img id="star4" onclick="noter(4)" src="images/notation-off.png" alt="star">
                        <img id="star5" onclick="noter(5)" src="images/notation-off.png" alt="star">
                        <div id="appreciation"></div>
                    </div>
                    <div style="height:80px;"></div>
                </div>
                <!-- /notation -->

            </div>
            <!-- /content -->

            <!-- confirmation -->
            <a href="javascript:void(0)" onclick="noter_livreur()" id="confirmation">
                Confirmer
            </a>
            <!-- /confirmation -->
        
        </div>
        <!-- /page -->
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script>
            
            var note=0;
            var confirmation = document.getElementById('confirmation');
            var appreciation = document.getElementById('appreciation');
            
            function noter(nbr){
                for(var i=1; i <= 5; i++){
                    var star = document.getElementById('star'+i);
                    if(i <= nbr){
                        star.src = 'images/notation-on.png';
                    } else {
                        star.src = 'images/notation-off.png';
                    }
                }
                
                if(confirmation.style.display !== 'block'){
                    confirmation.style.display = 'block';
                }
                
                switch(nbr) {
                    case 1:
                        appreciation.innerHTML ='Très mauvais';
                        break;
                    case 2:
                        appreciation.innerHTML ='Mauvais';
                        break;
                    case 3:
                        appreciation.innerHTML ='OK';
                        break;
                    case 4:
                        appreciation.innerHTML ='Bon';
                        break;
                    default:
                        appreciation.innerHTML ='Excellent';
                }

                note=nbr;
                
            }

            function noter_livreur() {
                $.ajax({
                    url      : 'https://www.you-order.eu/admin/action_poo.php',
                    data     : 'action=noter_livreur&id_livreur=<?=$Commande->getLivreur()?>&id_commande=<?=$id?>&id_client=<?=$Commande->getClient()?>&note='+note,
                    type     : "GET",
                    cache    : false,
                    timeout: 2000,
                    success: function(transport) {
                        console.log(transport);
                        if (transport=="insert" || transport=="update") {
                            window.location.href="confirmation.php";
                        }
                    },
                    error: function(transport) {
                        console.log(transport);
                    }
                });
            }
            
        </script>
        
    </body>

</html>
