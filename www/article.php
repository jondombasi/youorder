<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

$sql_serveur    = "localhost";
$sql_user       = "youorder";
$sql_passwd     = "75LrhfPSOqCv";
$sql_bdd        = "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

if(isset($_GET["id"]))
    $id = $_GET["id"];
else
    $id = "";

$result = $sql->query("SELECT * FROM actualites WHERE id = '".(int)$id."'");
$ligne = $result->fetch();

$result_prec = $sql->query("SELECT id FROM actualites WHERE `date` <= '".$ligne["date"]."' AND id != '".$id."'  ORDER BY `date` DESC");
$ligne_prec = $result_prec->fetch();
if($ligne_prec!=""){
    $id_prec = $ligne_prec["id"];
}else{
    $id_prec = "";
}
$result_suiv = $sql->query("SELECT id FROM actualites WHERE `date` >= '".$ligne["date"]."' AND id != '".$id."'  ORDER BY `date` ASC");
$ligne_suiv = $result_suiv->fetch();
if($ligne_suiv!=""){
    $id_suiv = $ligne_suiv["id"];
}else{
    $id_suiv = "";
}

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

    <head>
        
        <meta charset="utf-8">
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <title>youOrder - <?=$ligne["titre"]?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0,  minimun-scale=1.0, maximum-scale=1.0">
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <link rel="icon" type="image/png" href="image/youOrder_Logo.png" />
        <link rel="stylesheet" data-them="" href="css/styles.css">
        <link rel="stylesheet" data-them="" href="css/style.css">
        <link href="css/owl.carousel.css" rel="stylesheet" type="text/css"/>
        <link href="css/owl.theme.css" rel="stylesheet" type="text/css"/>
        <link href="css/owl.transitions.css" rel="stylesheet" type="text/css"/>
        <!-- Googl Font -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,100i,300,300i,400,700" rel="stylesheet">         
        <!-- /Googl Font -->
        
        <style>
            #menu-scroll-spe{
                position: fixed;
                display:block;
                top:0;
                left:0;
                right:0;
                background-color: #fff;
                border-bottom: 1px solid #9fc752;
                height: 80px;
                z-index:99;
            }
            
            #hamburger{z-index:101;}
            
            #demo-blog-one .header{height:80px;}
            
            .menu-mobile.transition{z-index:100;}
            
            #demo-blog-one header .menu-mobile-lien .active-mobile{color: #FFF !important;}
            
            @media (max-width: 768px){
                .section{overflow:visible;}
                .section .page-section-content{overflow:visible;}
            }
        </style>
        
        

    </head>


<body class="responsive " id="demo-blog-one">

        <!-- header -->
        <header class="header">
            
            
            <div id="menu-scroll-spe">
                
                <a href='index.php'>
                    <img class="logo logo-black logo-article" src="image/logo_original.png" alt="Logo">
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
                    <a href="#" class="active-mobile">Accès client</a>
                </div>
            </nav>
            
            <div class="stop"></div>
            
        </header>
        <!-- header -->

        <!-- content -->
        <div class="dima-main">

            <!-- title -->
            <section class="title_container">
                <div class="page-section-content">
                    <div class="container page-section text-center">
                        <h2 class="uppercase undertitle"><?=$ligne["titre"]?></h2>
                    </div>
                </div>
            </section>
            <section class='navbar'>
                <div class="container page-section">
                    <a class='navbar-left' href='liste.php'><i class="fa fa-th"></i>&nbsp;&nbsp;<span>Retour à la liste de tous les articles</span></a>
                    <div class='navbar-right'>
                        <?php 
                        if($id_prec!=""){
                        ?>
                        <a href='article.php?id=<?=$id_prec?>'><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;<span>Précédent</span></a>
                        <?php
                        }
                        if($id_suiv!=""){
                        ?>
                        <a href='article.php?id=<?=$id_suiv?>'><span>Suivant</span>&nbsp;&nbsp;<i class="fa fa-chevron-right"></i></a>
                        <?php
                        }
                        ?>
                    </div>
                    <div class='stop'></div>
                </div>
            </section>
            <!-- /title -->

            <!-- SECTION -->
            <section class="section ">
                <div class="page-section-content boxed-blog blog-list">
                    <div class="container blog">

                        <div class="main cl_8 first">

                            <!-- article -->
                            <article role="article" class="post ">
                                <div class="post-img ">
                                    <img src="admin/upload/actualites/<?=$ligne["photo"]?>" alt="You Order">
                                </div>
                                <div class="post-meta box">
                                    <ul>
                                        <li class="post-on">Posté le <?=date("d/m/Y", strtotime($ligne["date"]))?></li>
                                    </ul>
                                </div>
                                <div class="post-content text-start box post-article">
                                    <?=$ligne["description"]?>
                                
                                    <ul class="social-media social-big inline clearfix">
                                        <li><a href="https://www.facebook.com/pages/You-Order/1460783804199493" target='_blank'><i class="fa fa-facebook"></i></a>
                                        </li>
                                        <li><a href="https://twitter.com/youOrder_Eu" target='_blank'><i class="fa fa-twitter"></i></a>
                                        </li>
                                        <li><a href="https://fr.linkedin.com/company/you-order" target='_blank'><i class="fa fa-linkedin"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                
                                
                            </article>
                            <!-- /article -->
                        </div>

                        <!-- sidebar -->
                        <aside role="complementary" class="sidebar hidden-tm cl_4 hidden">

                            <!-- categories -->
                            <div class="widget">
                                <h5 class="widget-title uppercase">CATEGORIES</h5>
                                <div class="widget-content">
                                    <ul class="with-border categories-posts">
                                        <?php
                                        $result2 = $sql->query("SELECT categorie, count(*) as NB FROM `actualites` GROUP BY categorie ORDER BY categorie");
                                        while($ligne2 = $result2->fetch()) {
                                            ?>
                                            <li>
                                                <a data-animated-link="fadeOut" href="#">
                                                    <span class="float-start text-start"><?=$ligne2["categorie"]?></span>
                                                    <span class="float-end text-end"><?=$ligne2["NB"]?></span>
                                                </a>
                                            </li>                                
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- /categories -->

                        </aside>
                        <!--! sidebar -->

                    </div>
                </div>
            </section>
            <!-- /section -->

        </div>
        <!-- /contents -->
        
                <!-- footer -->
        <footer class="footer">
            <hr>
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
                           <a href='https://twitter.com/youOrder_Eu'>
                                <div class="footer-icon">
                                    <i class="fa fa-twitter"></i>
                                </div>
                           </a>
                        </div>
                        <div class="footer-icon-mobile">
                            <a href='https://fr.linkedin.com/company/you-order'>
                                <div class="footer-icon">
                                     <i class="fa fa-linkedin"></i>
                                </div>
                            </a>
                        </div>
                       <div class="footer-icon-mobile">
                            <a href='https://www.facebook.com/pages/You-Order/1460783804199493'>
                                <div class="footer-icon">
                                    <i class="fa fa-facebook"></i>
                                </div>
                            </a>
                       </div>
                    </div>
                    <div class="stop"></div>
                </div>
            </div>
        </footer>
        <!-- /footer -->

        
    <!-- 1)Important in all pages -->
    <script src="js/core/jquery-2.1.1.min.js"></script>
    <!-- 
<script src="http://ajax.googleapis.com/ajax/module/jquery/2.1.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/module/jquery-2.1.1.min.js"><\/script>')</script>
 -->
    <script src="js/core/load.js"></script>
    <script src="js/core/jquery.easing.1.3.js"></script>
    <script src="js/core/modernizr-2.8.2.min.js"></script>
    <script src="js/core/imagesloaded.pkgd.min.js"></script>
    <script src="js/core/respond.src.js"></script>

    <script src="js/libs.min.js"></script>
    <!-- ALL THIS FILES CAN BE REPLACE WITH ONE FILE libs.min.js -->
    <!-- 
<script src="js/module/waypoints.min.js"></script>
<script src="js/module/SmoothScroll.js"></script>
<script src="js/module/skrollr.js"></script>
<script src="js/module/sly.min.js"></script>
<script src="js/module/perfect-scrollbar.js"></script>
<script src="js/module/retina.min.js"></script>
<script src="js/module/jquery.localScroll.min.js"></script>
<script src="js/module/jquery.scrollTo-min.js"></script>
<script src="js/module/jquery.nav.js"></script>
<script src="js/module/hoverIntent.js"></script>
<script src="js/module/superfish.js"></script>
<script src="js/module/jquery.placeholder.js"></script>
<script src="js/module/countUp.js"></script>
<script src="js/module/isotope.pkgd.min.js"></script>
<script src="js/module/jquery.flatshadow.js"></script>
<script src="js/module/jquery.knob.js"></script>
<script src="js/module/jflickrfeed.min.js"></script>
<script src="js/module/instagram.min.js"></script>
<script src="js/module/jquery.tweet.js"></script>
<script src="js/module/bootstrap.min.js"></script>
<script src="js/module/bootstrap-transition.js"></script>
<script src="js/module/responsive.tab.js"></script>
<script src="js/module/jquery.magnific-popup.min.js"></script>
<script src="js/module/jquery.validate.min.js"></script>
<script src="js/module/owl.carousel.min.js"></script>
<script src="js/module/jquery.flexslider.js"></script>
<script src="js/module/jquery-ui.min.js"></script>
<script src="js/module/zoomsl-3.0.min.js"></script>
 -->
    <!-- END -->

    <script src="js/specific/mediaelement/mediaelement-and-player.min.js"></script>
    <script src="js/specific/video.js"></script>
    <script src="js/specific/bigvideo.js"></script>

    <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
    <script src="js/specific/gmap3.min.js"></script>
    <script src="js/map.js"></script>

    <script type="text/javascript" src="js/specific/revolution-slider/js/jquery.themepunch.tools.min.js"></script>
    <script type="text/javascript" src="js/specific/revolution-slider/js/jquery.themepunch.revolution.min.js"></script>

    <script src="js/main.js"></script>
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
