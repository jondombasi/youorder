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
        <link rel="stylesheet" type="text/css" href="font-awesome-4.6.3/css/font-awesome.min.css">
        <link rel="icon" type="image/png" href="image/youOrder_Logo.png">
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
            
            <div class="bandeau cookie">
                <div class="container">
                    <h1>Politique d'utilisation des cookies</h1>
                </div>
            </div>
            
        </header>
        
        <section class="section">
            <div class="container">
                
                <p>Lors de la consultation de notre site you-order.eu, des cookies sont déposés sur votre ordinateur, votre mobile ou votre tablette.
                    Notre site est conçu pour être attentif aux besoins et attentes de nos lecteurs. C'est entre autres pour cela que nous faisons usage de cookies afin par exemple de vous identifier et d'accéder à votre compte. Cette page vous permet de mieux comprendre comment fonctionnent les cookies et comment les paramétrer.</p>
                
                <h2>Définition d'un cookie</h2>
                <p>
                    Un cookie est un fichier texte déposé sur votre ordinateur lors de la visite d'un site ou de la consultation d'une publicité. Il a pour but de collecter des informations relatives à votre navigation et de vous adresser des services adaptés à votre terminal (ordinateur, mobile ou tablette). Les cookies sont gérés par votre navigateur internet.
                </p>
                
                <h2>Les différents émetteurs</h2>
                
                <em>Les cookies you-order.eu</em>
                <p>Il s'agit des cookies déposés par you-order.eu sur votre terminal pour répondre à des besoins de navigation, d'optimisation et de personnalisation des services sur notre site.</p>
           
                <em>Les cookies tiers</em>
                <p>Il s'agit des cookies déposés par des sociétés tierces (par exemple des partenaires) pour identifier vos centres d'intérêt et éventuellement personnaliser l'offre publicitaire qui vous est adressée sur et en dehors de notre site. Ils peuvent être déposés quand vous naviguez sur notre site ou lorsque vous cliquez dans les espaces publicitaires de notre site. Dans le cadre de partenariat, nous veillons à ce que les sociétés partenaires respectent strictement la loi informatique et libertés du 6 janvier 1978 modifiée et s'engagent à mettre en œuvre des mesures appropriées de sécurisation et de protection de la confidentialité des données.</p>
            
                <h2>Paramétrer votre navigateur internet</h2>
                <p>Vous pouvez à tout moment choisir de désactiver ces cookies. Votre navigateur peut également être paramétré pour vous signaler les cookies qui sont déposés dans votre ordinateur et vous demander de les accepter ou pas. Vous pouvez accepter ou refuser les cookies au cas par cas ou bien les refuser systématiquement. Nous vous rappelons que le paramétrage est susceptible de modifier vos conditions d'accès à nos contenus et services nécessitant l'utilisation de cookies. Si votre navigateur est configuré de manière à refuser l'ensemble des cookies, vous ne pourrez pas profiter d'une partie de nos services. Afin de gérer les cookies au plus près de vos attentes nous vous invitons à paramétrer votre navigateur en tenant compte de la finalité des cookies.</p>
            
                <em>Internet Explorer</em>
                <p>
                    Dans Internet Explorer, cliquez sur le bouton Outils, puis sur Options Internet.<br>
                    Sous l'onglet Général, sous Historique de navigation, cliquez sur Paramètres.<br>
                    Cliquez sur le bouton Afficher les fichiers.<br>
                </p>
                
                <em>Firefox</em>
                <p>
                    Allez dans l'onglet Outils du navigateur puis sélectionnez le menu Options<br>
                    Dans la fenêtre qui s'affiche, choisissez Vie privée et cliquez sur Affichez les cookies
                </p>
                
                <em>Safari</em>
                <p>
                    Dans votre navigateur, choisissez le menu Édition > Préférences.<br>
                    Cliquez sur Sécurité.<br>
                    Cliquez sur Afficher les cookies.
                </p>
                
                <em>Google Chrome</em>
                <p>
                    Cliquez sur l'icône du menu Outils.<br>
                    Sélectionnez Options.<br>
                    Cliquez sur l'onglet Options avancées et accédez à la section Confidentialité.<br>
                    Cliquez sur le bouton Afficher les cookies.<br>
                </p>
                
                
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
