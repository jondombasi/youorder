<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

$sql_serveur    = "localhost";
$sql_user       = "youorder";
$sql_passwd     = "75LrhfPSOqCv";
$sql_bdd        = "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Mentions légales</title>
        <meta charset="UTF-8">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0,  minimun-scale=1.0, maximum-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/mentions.css">
        <link rel="icon" type="image/png" href="image/youOrder_Logo.png" />
        <link rel="stylesheet" type="text/css" href="font-awesome-4.6.3/css/font-awesome.min.css">
        <!-- Googl Font -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,100i,300,300i,400,700" rel="stylesheet">         
        <!-- /Googl Font -->
        
        <style>
            #menu-scroll{
                position: fixed;
                top:0;
                left:0;
                right:0;
                background-color: #fff;
                border-bottom: 1px solid #9fc752;
                height: 80px;
            }

.article-footer{
    display: block;
    margin-bottom: 20px;
}

.article-footer img{
    width: 25%;
    display: block;
    float:left;
    margin-right: 5px;
}

.article-footer-txt{
    margin-top: -5px;
    float:left;
    width: 70%;
}

.article-footer-txt h4{
    font-size: 12px;
    color: #FFF;
}

.article-footer-txt p{
    font-size: 12px;
    font-weight: 300;
    line-height: 1;
}               
        </style>
        
    </head>
    <body>
        
        <header class="header">
            
            <div id="menu-scroll">
            
                <a href='index.php'>
                    <img class="logo logo-black" src="image/logo_original.png" alt="Logo">
                </a>

                <nav class="menu">
                    <a href="index.php#solutions" class="menu-item">Solutions</a>
                    <a href="index.php#secteurs" class="menu-item">Secteurs</a>
                    <a href="index.php#valeurs" class="menu-item">Valeurs</a>
                    <a href="index.php#partenaires" class="menu-item">Partenaires</a>
                    <a href="index.php#job" class="menu-item">Job</a>
                    <a href="index.php#blog" class="menu-item">Blog</a>
                    <a href="index.php#contact" class="menu-item">Contact</a>
                    <a href="#" class="menu-item active">Accès client</a>
                </nav>
                 
             </div>
            
            <nav id="hamburger" class="hamburger">
                <div></div>
                <div></div>
                <div></div>
            </nav>
            
            <nav id="menu-mobile" class="menu-mobile">
                <img src="image/logo.png" alt="logo">
                <div class="menu-mobile-lien">
                    <a href="index.php#solutions">Solutions</a>
                    <a href="index.php#secteurs">Secteurs</a>
                    <a href="index.php#valeurs">Valeurs</a>
                    <a href="index.php#partenaires">Partenaires</a>
                    <a href="index.php#job">Job</a>
                    <a href="index.php#blog">Blog</a>
                    <a href="index.php#contact">Contact</a>
                    <a href="#">Accès client</a>
                </div>
            </nav>
            
            <div class="stop"></div>
            
            <div class="bandeau">
                <div class="container">
                    <h1>Mentions légales</h1>
                </div>
            </div>
            
        </header>
        
        <section class="section">
            <div class="container">
                
                <h2>Article 1. Editeur</h2>
                <p>
                    youOrder<br>
                    55 rue de la Boétie<br>
                    75008 Paris, France<br>
                    contact@youorder.fr<br>
                    <br>
                    Hébergeur : SYNTEN - 6 Allée Latécoère<br>
                    78140 Velizy Villacoublay – France<br>
                </p>
                
                <h2>Article 2. Propriété intellectuelle</h2>
                <p>L'ensemble des contenus diffusés sur ce site (image, vidéo, son, texte…) sont protégés par la législation en vigueur en France en matière de propriété intellectuelle et notamment, le droit d'auteur, les droits voisins, le droit des marques. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques. A défaut d'autorisation expresse de youOrder il est strictement interdit d'exploiter ces contenus et notamment de les reproduire, représenter, modifier ou adapter en tout ou partie.</p>
                <br>
                <a href="cookies.php">Découvrir notre politique d'utilisaton des cookies</a>
            </div>
        </section>
        
        <!-- footer -->
        <footer class="footer">
            <div class="page-section-content">
                <div class="container text-center">
                    <div class="footer-item">
                        <h3>A propos de YouOrder</h3>
                        <p>youOrder est une start-up qui innove dans la livraison du dernier kilomètre.<br>
                        Transporteur responsable par ces valeurs, youOrder est un véritable partenaire logistique qui accompagne et respecte votre marque lors de la livraison.</p>
                    </div>
                    <div class="footer-item">
                            <h3>Articles récents</h3>
                            <?php
                            $result = $sql->query("SELECT * FROM `actualites` ORDER BY date DESC LIMIT 2");
                            while($ligne = $result->fetch()) {
                                ?>
                                <a href="article.php?id=<?=$ligne["id"]?>" class='article-footer'>
                                    <img src='admin/upload/actualites/<?=$ligne["photo"]?>'>
                                    <div class="article-footer-txt">
                                        <h4><?=$ligne["categorie"]?></h4>
                                        <p><?=$ligne["titre"]?></p>
                                    </div>
                                    <div class="stop"></div>
                                </a>
                                <?php
                            }
                            ?>
                    </div>
                    <div class="footer-item">
                        <h3>Liens utiles</h3>
                        <a href="mailto:contact@youorder.fr">Contact</a>
                        <a href="mentions-legales.php">Mentions légales</a>
                        <a href="#">CGV</a>
                    </div>
                    <div class="stop"></div>
                </div>
                 <div class="footer-middle">
                    <div class="container text-center">
                        <a href="index.php#solutions">Solutions</a>
                        <a href="index.php#secteurs">Secteurs</a>
                        <a href="index.php#valeurs">Valeurs</a>
                        <a href="index.php#partenaires">Partenaires</a>
                        <a href="index.php#job">Job</a>
                        <a href="index.php#blog">Blog</a>
                        <a href="index.php#contact">Contact</a>
                        <a href="#" class="active">Accès client</a>
                    </div>
                </div>
                <div class="container text-center">
                    <div class="footer-bottom-l">
                        © Copyright 2016 youOrder | Tous droits réservés
                    </div>
                   <div class="footer-bottom-r">
                       <div class="footer-icon-mobile">
                            <div class="footer-icon">
                                <a href='https://twitter.com/youOrder_Eu' target='_blank'>
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                        <div class="footer-icon-mobile">
                            <div class="footer-icon">
                                <a href='https://fr.linkedin.com/company/you-order' target='_blank'>
                                    <i class="fa fa-linkedin"></i>
                                </a>
                             </div>
                        </div>
                       <div class="footer-icon-mobile">
                            <div class="footer-icon">
                                <a href='https://www.facebook.com/pages/You-Order/1460783804199493' target='_blank'>
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </div>
                       </div>
                    </div>
                    <div class="stop"></div>
                </div>
            </div>
        </footer>
        <!-- /footer -->
        
        <script>
            
            var hamburger = document.getElementById('hamburger');
            var menu_mobile = document.getElementById('menu-mobile');

            hamburger.addEventListener('click', affiche_menu);

            function affiche_menu(){
                hamburger.classList.toggle('transition-burger');
                menu_mobile.classList.toggle('transition');
            }
            
        </script>
        
    </body>
</html>
