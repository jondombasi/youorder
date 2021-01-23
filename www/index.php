<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

$sql_serveur    = "localhost";
$sql_user       = "root";
$sql_passwd     = "root";
$sql_bdd        = "youorder";

$sql = new PDO('mysql:host='.$sql_serveur.';dbname='.$sql_bdd, $sql_user, $sql_passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );

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
        <title>youOrder - Votre marque, livrée</title>
        <meta name="description" content="youOrder est une start-up spécialisée dans le transport responsable. Véritable partenaire logistique qui accompagne et respecte votre marque lors de la livraison.">
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
        
        <style type="text/css">
            .form_erreur{
                border: 1px solid red !important;
            }
        </style>

    </head>


    <body class="responsive">
    
        <!-- header -->
        <header class="header">
            
            <video id="video" class='video_background' preload='auto' autoplay='true' loop='loop' muted='muted' volume='0'>
            <source src='video/youOrder-video.mp4' type='video/mp4'>
            </video>
            
            <div id="menu-scroll">
            
                <img class="logo" class="fade-out" src="image/logo.png" alt="Logo">

                <nav class="menu" class="fade-out-menu">
                    <a href="#solutions" class="menu-item">Solutions</a>
                    <a href="#secteurs" class="menu-item">Secteurs</a>
                    <a href="#valeurs" class="menu-item">Valeurs</a>
                    <a href="#partenaires" class="menu-item">Partenaires</a>
                    <a href="#job" class="menu-item">Job</a>
                    <a href="#blog" class="menu-item">Blog</a>
                    <a href="#contact" class="menu-item">Contact</a>
                    <a href="https://www.you-order.eu/admin/" class="menu-item active">Accès client</a>
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
                    <a href="#solutions">Solutions</a>
                    <a href="#secteurs">Secteurs</a>
                    <a href="#valeurs">Valeurs</a>
                    <a href="#partenaires">Partenaires</a>
                    <a href="#job">Job</a>
                    <a href="#blog">Blog</a>
                    <a href="#contact">Contact</a>
                    <a href="#" class="ative-mobile">Accès client</a>
                </div>
            </nav>
            
            <div class="stop"></div>
            
            <h1>Votre marque, <span>livrée</span></h1>
            
            <div class="scooter">
                <a href="#solution">
                    <img src="image/motorbike.png" alt="Scooter">
                    <img src="image/arrow.png" alt="Arrow">
                </a>
            </div>
            
        </header>
        <!-- header -->
        
        
        <!-- dima main -->
        <div class="dima-main" id='solution'>

            <!-- section solution -->
            <section class="section solution" id="features">
                <div id="solutions" class="page-section-content">
                    <div class="container text-center">

                        <!-- title -->
                        <h2 data-animate="fadeInDown" data-delay="0">
                            Les solutions<br>
                            <span>youOrder</span>
                        </h2>
                        <div class="topaz-line">
                            <img src="image/star.png" alt="star">
                        </div>
                        <p data-animate="fadeInUp" data-delay="100">Des solutions <span>sur mesure</span> qui répondent à l'ensemble de vos besoins logistiques.</p>
                        <!-- /title -->

                        <div class="clear-section"></div>

                        <!-- icon -->
                        <div  class="di_1_of_4 features-box first section-colored section-icon section-icon1" data-bg="#faf9f5" data-animate="fadeInUp" data-delay="100">
                            <div onmouseover="hover(1)" onmouseout="hover_end(1)" class="features-content">
                                <h5 onclick="hover_click(1)" id="title1" class="features-title uppercase">Dédiée</h5>
                                <div onclick="hover_click_end(1)" id="hover1" class="solution-hover">
                                    <h5>L'offre dédiée</h5>
                                    <img src="image/star-pipe.png" alt="star">
                                    <p>
                                        Un service de livraison régulier ?
                                        Mise à disposition de livreurs sur les créneaux que vous souhaitez
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- /icon -->

                        <!-- icon -->
                        <div class="di_1_of_4 features-box section-colored section-icon section-icon2" data-bg="#faf9f5" data-animate="fadeInUp" data-delay="200">
                            <div onmouseover="hover(2)" onmouseout="hover_end(2)" class="features-content">
                                <h5 onclick="hover_click(2)" id="title2" class="features-title uppercase">A la carte</h5>
                                <div onclick="hover_click_end(2)" id="hover2" class="solution-hover">
                                    <h5>L'offre à la carte</h5>
                                    <img src="image/star-pipe.png" alt="star">
                                    <p>
                                        Un besoin ponctuel ?<br>
                                        Nous prenons en charge la commande
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- /icon -->

                        <!-- icon -->
                        <div class="topaz-hover di_1_of_4 features-box section-colored section-icon section-icon3" data-bg="#faf9f5" data-animate="fadeInUp" data-delay="300">
                            <div onmouseover="hover(3)" onmouseout="hover_end(3)" class="features-content">
                                <h5 onclick="hover_click(3)" id="title3" class="features-title uppercase">Rush</h5>
                                <div onclick="hover_click_end(3)" id="hover3" class="solution-hover">
                                    <h5>L'offre rush</h5>
                                    <img src="image/star-pipe.png" alt="star">
                                    <p>
                                        Un pic d’activité ?<br>
                                        Mise à disposition de livreurs en urgence
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- /icon -->

                        <!-- icon -->
                        <div class="topaz-hover di_1_of_4 features-box  section-colored section-icon section-icon4" data-bg="#faf9f5" data-animate="fadeInUp" data-delay="400">
                            <div onmouseover="hover(4)" onmouseout="hover_end(4)" class="features-content">
                                <h5 onclick="hover_click(4)" id="title4" class="features-title uppercase">It</h5>
                                <div onclick="hover_click_end(4)" id="hover4" class="solution-hover">
                                    <h5>L'offre it</h5>
                                    <img src="image/star-pipe.png" alt="star">
                                    <p>
                                        Dispatch, Géolocalisation…<br>
                                        une technologie embarquée au cœur de votre activité
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- /icon -->

                    </div>
                </div>
            </section>
            <!-- /section solution -->
            
            <!-- section secteur -->
            <section id="secteurs" class="section secteur">
                <div class="page-section-content">
                    <div class="container text-center">
                        <h2 data-animate="fadeInDown" data-delay="0">
                            Les secteurs<br>
                            <span>d'activité</span>
                        </h2>
                    </div>
                    <div onmouseover="hover(5)" onmouseout="hover_end(5)" class="secteur-img">
                        <h5 onclick="hover_click(5)" id="title5" class="secteur-h5">food</h5>
                        <div onclick="hover_click_end(5)" id="hover5" class="secteur-img-hover">
                            <h5>Food</h5>
                            <img src="image/star-pipe.png" alt="star">
                            <p>
                                Une prestation dans le respect des normes
                            </p>
                        </div>
                        <img src="image/food.jpg" alt="food">
                    </div>
                    <div onmouseover="hover(6)" onmouseout="hover_end(6)" class="secteur-img">
                        <h5 onclick="hover_click(6)" id="title6" class="secteur-h5">e-commerce</h5>
                        <div onclick="hover_click_end(6)" id="hover6" class="secteur-img-hover">
                            <h5>e-commerce</h5>
                            <img src="image/star-pipe.png" alt="star">
                            <p>
                                Une image de marque respectée lors de la livraison.
                            </p>
                        </div>
                        <img src="image/commerce.jpg" alt="e-commerce">
                    </div>
                    <div onmouseover="hover(7)" onmouseout="hover_end(7)" class="secteur-img">
                        <h5 onclick="hover_click(7)" id="title7" class="secteur-h5">luxe</h5>
                        <div onclick="hover_click_end(7)" id="hover7" class="secteur-img-hover">
                            <h5>luxe</h5>
                            <img src="image/star-pipe.png" alt="star">
                            <p>
                                Une prestation sur mesure
                            </p>
                        </div>
                        <img src="image/luxe.jpg" alt="luxe">
                    </div>
                    <div onmouseover="hover(8)" onmouseout="hover_end(8)" class="secteur-img">
                        <h5 onclick="hover_click(8)" id="title8" class="secteur-h5">colis</h5>
                        <div onclick="hover_click_end(8)" id="hover8" class="secteur-img-hover">
                            <h5>colis</h5>
                            <img src="image/star-pipe.png" alt="star">
                            <p>
                                Une prestation optimisée et flexible
                            </p>
                        </div>
                        <img src="image/colis.jpg" alt="colis">
                    </div>
                    <div class="bgc-secteur-img-50">
                        <div id="valeurs" class="secteur-img-50">
                            <h2>
                                Nos<br>
                                <span>Valeurs</span>
                            </h2>
                            <img src="image/star.png" alt="star">
                            <p>Des convictions partagées par toute l’équipe</p>
                        </div>
                        <div id="height-txt" onclick="affiche_valeur(1)" class="secteur-img-50 secteur-img-1">
                            <img src="image/ciel.jpg" alt="ciel">
                            <div class="ciel" id="valeur1"> 
                                <img src="image/nature.png" alt="nature">
                                <span>L'écologie</span>
                            </div>
                            <div class="digital" id="det_valeur1">
                                <img src="image/nature_vert.png" alt="computer">
                                <h5>L'écologie</h5>
                                <span>Des véhicules écologiques à votre disposition</span>
                                <p>
                                    Scooters électriques, vélos, triporteurs, vélos à assistance électrique, nous vous accompagnons avec des moyens de production responsables
                                </p>
                            </div>
                        </div>
                        <div onclick="affiche_valeur(2)" class="secteur-img-50 secteur-img-2">
                            <img src="image/valeur_social.jpg" alt="collaborateurs">
                            <div class="ciel" id="valeur2">
                                <img src="image/network.png" alt="network">
                                <span>le social</span>
                            </div>
                            <div class="digital" id="det_valeur2">
                                <img src="image/network_vert.png" alt="computer">
                                <h5>Le social</h5>
                                <span>Une internalisation du savoir-faire</span>
                                <p>
                                    L’embauche de l’ensemble de nos collaborateurs est une volonté de sublimer l’expérience client et d’être un acteur majeur de l’insertion par l’emploi
                                </p>
                            </div>
                        </div>
                        <div onclick="affiche_valeur(3)" class="secteur-img-50 secteur-img-3">
                            <img src="image/digital.jpg" alt="digital">
                            <div class="ciel" id="valeur3">
                                <img src="image/computer_blanc.png" alt="computer">
                                <span>le digital</span>
                            </div>
                            <div class="digital" id="det_valeur3">
                                <img src="image/computer.png" alt="computer">
                                <h5>Le digital</h5>
                                <span>Une technologie modulable</span>
                                <p>
                                    Une soluton technique capable de répondre à l'ensemble de vos besoins et d'apporter de la valeur au consommateur.
                                </p>
                            </div>
                        </div>
                        <div class="stop"></div>
                    </div>
                </div>
            </section>
            <!-- /section secteur -->
            
            <!-- section partenaires -->
            <section id="partenaires" class="section partenaires">
                <div class="page-section-content">
                    <div class="container text-center">
                        <h2 data-animate="fadeInDown" data-delay="0">
                            Nos<br>
                            <span>partenaires</span>
                        </h2>
                        <img class='star' src='image/star.png' alt='star'>
                    </div>
                    
                    <div class="partenaires-tablettes">
                        <div id="owl">
                            <div class="item"><img src="image/partenaires/SNCF-developpement.gif" alt="SNCF Développement"></div>
                            <div class="item"><img src="image/partenaires/logo-village-boetie.png" alt="Village Boetie"></div>
                            <div class="item france-active"><img src="image/partenaires/logo_france_active.jpg" alt="France active"></div>
                            <div class="item bpi-france"><img src="image/partenaires/bpifrance.png" alt="BPI France"></div>
                            <div class="item"><img src="image/partenaires/comptoir_innovation.png" alt="Le comptoir de l'innovation"></div>
                            <div class="item"><img src="image/partenaires/initiative95.jpg" alt="Initiative 95"></div>
                            <div class='item'><img src="image/partenaires/retinalogo.png" alt="EMLyon"></div>
                        </div>
                    </div>
                    
                </div>
            </section>
            <!-- /section partenaires -->
            
            <!-- QUOTE SECTION -->
            <section class="section parallax-background-section" id="quote">
                <div  id="job" class="page-section-content">

                    <div class="background-image-hide parallax-background">
                        <img id="photo_scooter" class="background-image" alt="Scooter" src="image/scooter-job.jpg">
                    </div>

                    <div class="dima-section-cover"></div>
                    <div class="container page-section text-center" data-animate="bounceIn" data-delay="0">
                        
                        <div id="form-job-cacher">
                            <span>Notre métier vous <span>intéresse</span> ?</span>
                        </div>
                        <span id="form-job">Devenez livreur</span>
                       
                        <div id="form-job-visible" style="position:relative;">
                            
                            <div class="img-top-job">
                                <img src="image/contact-form.png" alt="form">
                            </div>
        
                            <div id="div_valide2" style="display:none;padding-top:35px;">Votre message a bien été transmis.<br/>Vous serez recontacté très prochainement.</div>
                            
                            <form class="ajax2" action="form2.php" id="form_livreur" method="POST" name="form_livreur" enctype="multipart/form-data">
                                <input class="input-job-top" id="l_nom" name="l_nom" placeholder="Nom">
                                <input class="input-job-top" id="l_prenom" name="l_prenom" placeholder="Prénom">
                                <input class="input-job-full" id="l_mail" name="l_mail" placeholder="Mail">
                                <input class="input-job-full" id="l_tel" name="l_tel" placeholder="Numéro de téléphone">
                                <textarea name="l_message" id="l_message" placeholder="Expliquez-nous votre motivation ..."></textarea>
                                <select class="input-job-top" name="type_permis" id="type_permis" style="line-height:30px;padding-left:4px;background-color:#F9F9FB !important">
                                    <option value="">Type de permis</option>
                                    <option value="B">Permis B (Voiture)</option>
                                    <option value="AM">Permis AM (Scooter)</option>
                                    <option value="A">Permis A (Moto)</option>
                                </select>
                                <div class="file" onclick="upl()">Téléchargez votre CV</div>
                                <div class="stop"></div>
                                <!--<input type="file">-->
                                <input type="submit" id="bt_form2" name="bt_form2" value="Envoyer">
                                <input type="file" name="fichier" id="fichier" style="position:absolute;width:1px;height:1px;right:0px;bottom:0">
                            </form>
                            
                        </div>
                        
                    </div>
                </div>
            </section>
            <!--! QUOTE SECTION -->
            
            <!-- section blog -->
            <section id="blog" class="section blog">
                <div class="page-section-content">
                    <div class="container text-center">
                        <h2 data-animate="fadeInDown" data-delay="0">
                            Notre<br>
                            <span>blog</span>
                        </h2>
                        <img class="star" src="image/star.png" alt="star">
                        <div style="height:50px"></div>
                        
                        <div id="owl-blog">
                            <?php
                            $result = $sql->query("SELECT * FROM `actualites` ORDER BY date DESC");
                            while($ligne = $result->fetch()) {
                                ?>
                                <div class="item">
                                    <a href="article.php?id=<?=$ligne["id"]?>" class="article-item">
                                        <div class="categorie">
                                            <img src="admin/upload/actualites/<?=$ligne["photo"]?>" alt="scooter">
                                            <span class="categorie-orange"><?=$ligne["categorie"]?></span>
                                        </div>
                                        <h2><?=$ligne["titre"]?></h2>
                                        <p><?=substr(strip_tags($ligne["description"]),0,50)."..."?></p>
                                    </a>
                                </div>                                
                                <?php
                            }
                            ?>
                        </div>
                        
                    </div>
                </div>
            </section>
            <!-- /section blog -->

            <!-- map -->
            <div id="contact" class="google-maps ">
                <div id="map" data-lat="48.873108" data-lng="2.312954"></div>
                <div class="form-contact" style="height:auto;">
                    <span  class="title">Contactez <span>Nous</span></span>
                    <img class="star" src="image/star.png" alt="star">
                    <div class="form-btn">
                        <span class='green' id='operation' onclick='change_couleur(1)'>Opérations</span>
                        <span id='info' onclick='change_couleur(2)'>Infos</span>
                        <div class="stop"></div>
                    </div>
                    <hr>
                    <div id="div_valide" style="display:none;">Votre message a bien été transmis.<br/>Vous serez recontacté très prochainement.</div>
                    <form class="ajax" action="form.php" id="form_contact" name="form_contact">
                        <input type="hidden" id="type" name="type" value="1" />
                        <input class="input-top" name="c_prenom" id="c_prenom" type="text" placeholder="Prénom">
                        <input class="input-top" name="c_nom" id="c_nom" type="text" placeholder="Nom">
                        <input class="input-full" name="c_sujet" id="c_sujet" type="text" placeholder="Sujet">
                        <input class="input-full" name="c_mail" id="c_mail" type="text" placeholder="Mail">
                        <textarea  name="c_message" id="c_message"placeholder="Message..."></textarea>
                        <input type="submit" value="envoyer">
                    </form>
                </div>
                <div class="contact-left">
                    <div class="social">
                        <a href='https://www.facebook.com/pages/You-Order/1460783804199493' target='_blank'>
                            <i class="fa fa-facebook"></i>
                            <span>Facebook</span>
                        </a>
                    </div>
                    <div class="stop"></div>
                    <div class="social">
                        <a href='https://fr.linkedin.com/company/you-order' target='_blank'>
                            <i class="fa fa-linkedin"></i>
                            <span>Linkedin</span>
                        </a>
                    </div>
                    <div class="stop"></div>
                    <div class="social">
                        <a href='https://twitter.com/youOrder_Eu' target='_blank'>
                            <i class="fa fa-twitter"></i>
                            <span>Twitter</span>
                        </a>
                    </div>
                    <div class="stop"></div>
                </div>
                <div class="contact-right">
                    <div class="coordonnee">
                        <i class="fa fa-map-marker"></i>
                        <span>
                            55 rue de la Boétie<br>
                            75008 Paris, France
                        </span>
                        <div class="stop"></div>
                    </div>
                    <div class="stop"></div>
                    <div class="coordonnee">
                        <i class="fa fa-envelope"></i>
                        <span>
                            contact@youorder.fr
                        </span>
                        <div class="stop"></div>
                    </div>
                    <div class="stop"></div>
                    <div class="coordonnee">
                        <i class="fa fa-home"></i>
                        <span>
                            www.you-order.eu
                        </span>
                        <div class="stop"></div>
                    </div>
                    <div class="stop"></div>
                    
                </div>
                <div class="stop"></div>
                <div class="footer-espace" style="height:60px; background-color: #1d2226; border-bottom:1px solid #9fc752;"></div>
            </div>
            <!-- /map -->

        </div>
        <!-- /dima main -->
        
        <!-- footer -->
        <footer class="footer">
            <hr>
            <div class="page-section-content">
                <div class="container text-center">
                    <div class="footer-item">
                        <h3>A propos de youOrder</h3>
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
                        <a href="#solutions">Solutions</a>
                        <a href="#secteurs">Secteurs</a>
                        <a href="#valeurs">Valeurs</a>
                        <a href="#partenaires">Partenaires</a>
                        <a href="#job">Job</a>
                        <a href="#blog">Blog</a>
                        <a href="#contact">Contact</a>
                        <a href="#" class="active">Accès client</a>
                    </div>
                </div>
                <div class="container text-center">
                    <div class="footer-bottom-l">
                        © Copyright 2016 youOrder | Tous droits réservés
                    </div>
                   <div class="footer-bottom-r">
                       <div class="footer-icon-mobile">
                           <a href='https://twitter.com/youOrder_Eu' target='_blank'>
                                <div class="footer-icon">
                                    <i class="fa fa-twitter"></i>
                                </div>
                           </a>
                        </div>
                        <div class="footer-icon-mobile">
                            <a href='https://fr.linkedin.com/company/you-order' target='_blank'>
                                <div class="footer-icon">
                                     <i class="fa fa-linkedin"></i>
                                </div>
                            </a>
                        </div>
                       <div class="footer-icon-mobile">
                            <a href='https://www.facebook.com/pages/You-Order/1460783804199493' target='_blank'>
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

        <script src="js/specific/mediaelement/mediaelement-and-player.min.js"></script>
        <script src="js/specific/video.js"></script>
        <script src="js/specific/bigvideo.js"></script>

        <script src="http://maps.google.com/maps/api/js?sensor=true&key=AIzaSyB8VkiE5SAKcEYqK1qazWThEqyyLLWcqMY"></script>
        <script src="js/specific/gmap3.min.js"></script>
        <script src="js/map.js"></script>

        <script type="text/javascript" src="js/specific/revolution-slider/js/jquery.themepunch.tools.min.js"></script>
        <script type="text/javascript" src="js/specific/revolution-slider/js/jquery.themepunch.revolution.min.js"></script>

        <!-- OWL CAROUSEL -->
        <script type="text/javascript" src="js/owl.carousel.min.js"></script>
        <!-- /OWL CAROUSEL -->
        
        <script src="js/main.js"></script>
        <script type="text/javascript" src="js/app.js"></script>

        <script type="text/javascript">
            function upl(){
                $("#fichier").click();
            }
            $('form.ajax').submit( function(e) {
                 continu = true;
                 e.preventDefault(); // on empeche l'envoi du formulaire par le navigateur

                 if($("#c_nom").val()==""){
                    continu = false;
                    rendre_faux("c_nom");
                 }
                 if($("#c_prenom").val()==""){
                    continu = false;
                    rendre_faux("c_prenom");
                 }
                 if($("#c_sujet").val()==""){
                    continu = false;
                    rendre_faux("c_sujet");
                 }
                 if($("#c_mail").val()==""){
                    continu = false;
                    rendre_faux("c_mail");
                 }
                 if($("#c_message").val()==""){
                    continu = false;
                    rendre_faux("c_message");
                 }

                 if(continu){
                     var datas = $(this).serialize();
                     //console.log(datas)
                     
                     $.ajax({
                          type: 'POST',      // envoi des données en POST
                          url: $(this).attr('action'),     // envoi au fichier défini dans l'attribut action
                          data: datas,     // sélection des champs à envoyer
                          success: function(msg) {     // callback en cas de succès
                             //alert('success : '+datas);
                             if(msg=="1"){
                                $("#div_valide").css("display","block");
                                $("#c_nom").val('')
                                $("#c_prenom").val('')
                                $("#c_sujet").val('')
                                $("#c_mail").val('')
                                $("#c_message").val('')
                                setTimeout(function(){
                                    $("#div_valide").css("display","none");
                                },4000)
                             }
                          }
                     });
                 }
            });

            function rendre_faux(chp){
                $("#"+chp).addClass("form_erreur")
                setTimeout(function(){
                    $("#"+chp).removeClass("form_erreur")
                },3000)
            }

            //#bt_form2
            $('form.ajax2').submit( function(e) {
                 continu = true;
                 e.preventDefault(); // on empeche l'envoi du formulaire par le navigateur

                 if($("#l_nom").val()==""){
                    continu = false;
                    rendre_faux("l_nom");
                 }
                 if($("#l_prenom").val()==""){
                    continu = false;
                    rendre_faux("l_prenom");
                 }
                 if($("#l_tel").val()==""){
                    continu = false;
                    rendre_faux("l_tel");
                 }
                 if($("#l_mail").val()==""){
                    continu = false;
                    rendre_faux("l_mail");
                 }
                 if($("#l_message").val()==""){
                    continu = false;
                    rendre_faux("l_message");
                 }

                 if(continu){
                    //alert('form posté')
                    //form_livreur.submit();
                    
 //                    var datas = $(this).serialize();
                     var datas = new FormData($(this)[0]);

                     //console.log(datas)
                     
                     $.ajax({
                          type: 'POST',      // envoi des données en POST
                          url: $(this).attr('action'),     // envoi au fichier défini dans l'attribut action
                          data: datas,     // sélection des champs à envoyer
                          async: false,
                          cache: false,
                          contentType: false,
                          processData: false,
                          success: function(msg) {     // callback en cas de succès
                             //alert('success : '+datas);
                             if(msg=="1"){
                                $("#div_valide2").css("display","block");
                                $("#form-job-visible").css("height","432px");
                                $("#l_nom").val('')
                                $("#l_prenom").val('')
                                $("#l_tel").val('')
                                $("#l_mail").val('')
                                $("#l_message").val('')
                                setTimeout(function(){
                                    $("#div_valide2").css("display","none");
                                    $("#form-job-visible").css("height","410px");
                                },4000)
                             }
                          }
                     });
                    
                 }
            });            
        </script>
    </body>

</html>
