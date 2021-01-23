<?php
require_once("inc_connexion.php");

$date=(isset($_GET["date"])) ? $_GET["date"] : date("d-m-Y") ;

$Commande   = new Commande($sql);
$Commercant = new Commercant($sql);
$Livreur    = new Livreur($sql);
$Client     = new Client($sql);

$Commande->getPagination(30, "", "", "ajouté", date("Y-m-d", strtotime($date))." - ".date("Y-m-d", strtotime($date)), 0);
$Livreur->getPagination(30, "", "ON", "");

$menu = "commande";
$sous_menu = "affecter";
$titre_page = "Affecter une commande";

require_once("inc_header.php");

?>

<link rel="stylesheet" href="assets/plugins/select2/select2.css">

<style>
    #map{
        width:100%;
        height:698px;
    }
        
    .div_livreur_nom{
        position:relative;
        padding-top:5px !important;
        margin-top: 0 !important;
    }
    
    .div_livreur_nom .position-span{
        position: absolute;
        top:-10px;
        right: -5px;
    }
    
    @media (max-width:1199px){
        .affecter_div_liste{margin-top:20px;}
        .affecter_div_liste-top{border-left: 1px solid #DDD;}
    }
    
    @media (max-width:767px){
        .affecter_div_liste-top{height: 250px;}
        .affecter_div_liste{border-left:1px solid #d7d7d7;}
        .info-livreur .row .col-sm-10.col-sm-offset-1 div{width:100% !important;}
        .info-livreur .row .col-sm-10.col-sm-offset-1 div:nth-child(3){border-left:1px solid #DDD !important;}
        .tabbable.tabs-left li{width:100% !important; border: 1px solid #DDD;}
        .tabbable.tabs-left li a{border: none !important; margin-right:0 !important;}
        #tab1 .div_info_commande{border:none !important;}
    }
    .select2-container .select2-choice .select2-arrow b{background: none !important;}
    .div_livreur.over{background-color: #f1f1f1;}
</style>

<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">

<!-- start: PAGE -->
<div class="main-content">
    <div class="container">

        <!-- start: PAGE CONTENT -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-12 col-lg-6" style="border:1px solid #d7d7d7;padding:0px">
                    <div id="map"></div>
                </div>

                <div class="col-sm-6 col-lg-3 affecter_div_liste affecter_div_liste-top">
                    <p class="commandes_titre">LISTE DES LIVREURS (<span id="nb_livreurs"><?=$Livreur->getNbRes()?></span>)</p>
                    <div id="div_livreurs">
                        
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 affecter_div_liste affecter_div_liste-bottom">
                    <p class="commandes_titre">COMMANDES A AFFECTER (<span id="nb_commandes_attente"><?=$Commande->getNbRes()?></span>)</p>
                    <div style="padding:10px">
                        Filtre 
                        <span style="position:absolute;right:15px;cursor:pointer" onclick="show_filtres()">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                        <div id="commandes_filtre" style="display:none;">
                        <hr/>
                            <form role="form" name="form" id="form1" method="get" action="commandes_affecter.php" class="form-horizontal">
                                <div class="form-group" style="margin:0;">
                                    <label class="col-sm-4 control-label" for="commercant" style="text-align:left; padding:0;">Commerçant</label>
                                    <div class="col-sm-8" style="padding:0;">
                                        <select name="commercant" id="commercant" class="form-control search-select">
                                            <option value="">&nbsp;</option>
                                                <?php 
                                                    foreach ($Commercant->getAll("", "") as $commercant) {
                                                        $sel=($commercant_get==$commercant->id) ? "selected" : "";
                                                        echo "<option value='".$commercant->id."' ".$sel.">".$commercant->nom."</option>";
                                                    }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" style="margin:0;margin-top:20px;">
                                    <label class="col-sm-4 control-label" for="commercant" style="text-align:left; padding:0;">Date</label>
                                    <div class="col-sm-8" style="padding:0;">
                                        <input type="text" name="date" id="date" data-date-format="dd-mm-yyyy" value="<?php echo $date ?>" data-date-viewmode="years" data-week-start="1" class="form-control date-picker">
                                    </div>
                                </div>
                                <div class="row" style="margin:0;">
                                    <div class="col-sm-12 info-livreur" style="text-align:right; padding-right:0;">
                                        <input type="button" id="bt" class="btn btn-main" value="Rechercher" style="width:100px; margin-top:25px;" onclick="reload_commandes('manuel')">
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                    <div id="div_commandes" style="min-height:100%" ondrop="drop_desaffecte(event)" ondragover="allowDrop(event)">
                    </div>
                </div>
            </div>
        </div>      

        <div class="row" style="margin-top:20px">
            <div class="col-lg-9" id="info_livreur" style="display:none; padding-right: 7px;" >
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-info-circle"></i> Informations livreur</div>
                    <div class="panel-body info-livreur">

                    </div>
                </div>
            </div>
        </div>
        <!-- end: PAGE CONTENT-->
        
    </div>
</div>
<!-- end: PAGE -->

<?php
require_once("inc_footer.php");
?>

<script src="assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC15w0ru2bvazBjNdaHtVLXngRT6JfSh2s"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script language="javascript" type="text/javascript">
    
    var map;
    var markers = [];
    var timerMarker = 0;
    var timerLivreur = 0;
    var timerCommande = 0;
    
    $(document).ready(function() {
        $("body").addClass("navigation-small");
        runSelect2();
        runDatePicker();   

        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 48.8555799, lng: 2.3591637},
            zoom: 13,
        });    

        reload_commandes("auto");
        reload_livreurs(0);

        setTimerMarker();
        setTimerLivreur();
        setTimerCommande();
    }); 

    function runDatePicker() {
        $('.date-picker').datepicker({
            autoclose: true,
            weekStart: 1
        });
    };

    function runSelect2() {
        $(".search-select").select2({
            placeholder: "Select a State",
            allowClear: true
        });
    }

    function show_filtres() {
        $("#commandes_filtre").toggle();
        $("#commandes_filtre").parent().find("i").toggleClass("fa-chevron-down fa-chevron-up");
    }

    function showCommandes(id_div) {
        $("#"+id_div).toggle();
        $("#"+id_div).parent().find(".round_icon_large").toggleClass("fa-plus fa-minus");

        //console.log(timerLivreur)

        if ($("#"+id_div).parent().find(".round_icon_large").hasClass("fa-plus")) {
            console.log("on lance timer")
            setTimerLivreur();
        }
        else {
            console.log("on stop livreur")
            clearInterval(timerLivreur);
            timerLivreur=0; 
        }
    }

    function attribuer_commande(id_livreur, id_commande, ancien_livreur) {
        $.ajax({
            url      : 'action_poo.php?action=affecter_livreur',
            data     : "id_commande="+id_commande+"&id_livreur="+id_livreur,
            type     : "GET",
            cache    : false,         
            success:  function(transport) {
                console.log(transport); 
                if (ancien_livreur!=0 && ancien_livreur!="") id_livreur=ancien_livreur;
                reload_livreurs(id_livreur);
                reload_commandes("auto");
                $("#info_livreur").hide();
                setTimerMarker();
                /*console.log(id_livreur)
                if (id_livreur!=0 && id_livreur!="") {
                    console.log("relancer timer livreurs")
                    setTimerLivreur();
                } */
                setTimerCommande();
            }
        });
    }

    function reload_livreurs(id_livreur) {
        $.ajax({
            url      : 'action_poo.php?action=reload_livreurs',
            data     : "id_livreur="+id_livreur,
            type     : "GET",
            cache    : false,         
            success:  function(transport) { 
                $("#div_livreurs").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });

        $.ajax({
            url      : 'action_poo.php?action=reload_nb_livreurs',
            data     : "",
            type     : "GET",
            cache    : false,         
            success:  function(transport) { 
                $("#nb_livreurs").html(transport);
            }
        });
    }

    function reload_commandes(type) {
        if (type=="manuel") $("#commandes_filtre").hide();
        $.ajax({
            url      : 'action_poo.php?action=filtrer_commandes',
            data     : "id_commercant="+$("#commercant").val()+"&date="+$("#date").val(),
            type     : "GET",
            cache    : false,         
            success:  function(transport) { 
                $("#div_commandes").html(transport);
                if ($(".tooltips").length) {
                    $('.tooltips').tooltip();
                }
            }
        });

        //afficher les markers des clients et des restos
        $.ajax({
            url      : 'action_poo.php?action=get_position_maps',
            data     : "date="+$("#date").val(),
            type     : "GET",
            cache    : false,         
            success:  function(transport) {
                //console.log(transport); 
                var res = $.parseJSON(transport);
                //console.log(res);

                //map.removeMarkers();
                deleteMarkersMap(markers, "points");

                //on boucle sur le tableau des positions des livreurs jusqu'a ce qu'on arrive a la fin
                (function nextRecord() {
                    var row = res.shift();
                    if (row) {
                        //console.log(row)
                        //on créer un marker pour les restos et les clients
                        icon_marker=(row[3]=="client") ? 'images/icon_arrivee.png' : 'images/icon_depart.png';
                        var marker = new google.maps.Marker({
                            position: {lat: parseFloat(row[1]), lng: parseFloat(row[0])},
                            icon: icon_marker,
                            title: row[2],
                            type_marker: "points"
                        });

                        markers.push(marker);
                        //console.log(markers);

                        nextRecord();
                    }
                    else {
                        //ici on attache les markers a la map pour les afficher
                        setMarkersMap(markers, "points");
                    }
                })();
            }
        }); 

        $.ajax({
            url      : 'action_poo.php?action=reload_nb_commandes',
            data     : "id_commercant="+$("#commercant").val()+"&date="+$("#date").val(),
            type     : "GET",
            cache    : false,         
            success:  function(transport) { 
                $("#nb_commandes_attente").html(transport);
            }
        });
    }

    function showInfoLivreur(id_livreur) {
        console.log(id_livreur);
        $.ajax({
            url      : 'action_poo.php?action=show_info_livreur',
            data     : "id_livreur="+id_livreur,
            type     : "GET",
            cache    : false,         
            success:  function(transport) { 
                $("#info_livreur").find(".panel-body").html(transport);
                $("#info_livreur").show();

                $('html, body').animate({scrollTop: $('#info_livreur').offset().top}, 500);
            }
        });
    }

    function setMarkersMap(markers, type) {
        for (var i = 0; i < markers.length; i++) {
            if (markers[i].type_marker==type) {
                markers[i].setMap(map);
            }
        }
    }

    function deleteMarkersMap(markers, type) {
        var i = markers.length;
        while (i--) {
            //si on veut supprimer tous les markers des livreurs
            if (type=="livreurs" && markers[i].type_marker=="livreurs") {
                //on efface le marker de la carte
                markers[i].setMap(null);
                //on supprime l'entrée dans le tableau pour les futures affichages
                markers.splice(i, 1);
            }
            //si on veut livrer tous les markers des clients/restos
            else if (type=="points" && markers[i].type_marker=="points") {
                markers[i].setMap(null);
                markers.splice(i, 1);
            }
        }
    }

    function setTimerMarker() {
        //timerMarker = setInterval(function() {
            $.ajax({
                url      : 'action_poo.php?action=get_position_livreur',
                data     : "",
                type     : "GET",
                cache    : false,         
                success:  function(transport) {
                    //console.log(transport); 
                    var res = $.parseJSON(transport);
                    //console.log(res);

                    deleteMarkersMap(markers, "livreurs");

                    //on boucle sur le tableau des positions des livreurs jusqu'a ce qu'on arrive a la fin
                    (function nextRecord() {
                        var row = res.shift();
                        if (row) {
                            var marker = new google.maps.Marker({
                                position: {lat: parseFloat(row[1]), lng: parseFloat(row[0])},
                                icon: 'images/picto_location_sm.png',
                                title: row[3]+" "+row[4],
                                type_marker: "livreurs",
                                id_livreur: row[2]
                            });
                            //on attache un évènement au click a chaque marker
                            marker.addListener('click', function() {
                                showInfoLivreur(this.id_livreur);
                            });

                            markers.push(marker);
                            nextRecord();
                        }
                        else {
                            setMarkersMap(markers, "livreurs");
                            setTimeout(function() {
                                setTimerMarker();
                            }, 15000);
                        }
                    })();
                }
            });  
        //},15000);
    }

    function setTimerLivreur() {
        timerLivreur = setInterval(function() {
            //console.log('timer livreur');
            reload_livreurs(0);
        },15000);
    }

    function setTimerCommande() {
        timerCommande = setInterval(function() {
            //console.log("timer commande");
            reload_commandes("auto");
        },15000);
    }

       function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
            
            function handleDragOver(e) {
                if (e.preventDefault) {
                  e.preventDefault(); // Necessary. Allows us to drop.
                }

                var that = this;
                this.classList.add('over');
                setTimeout(function(){
                    that.classList.remove('over');
                }, 1500);

                e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.

                return false;
            }

            function handleDragEnter(e) {
              // this / e.target is the current hover target.
                var that = this;
                this.classList.add('over');
                setTimeout(function(){
                    that.classList.remove('over');
                }, 1500);
            }

            function handleDragLeave(e) {
                console.log(this);
                this.classList.remove('over');  // this / e.target is previous target element.
            }

            var cols = document.querySelectorAll('.div_draggable');
            [].forEach.call(cols, function(col) {
                col.addEventListener('dragenter', handleDragEnter, false);
                col.addEventListener('dragover', handleDragOver, false);
                col.addEventListener('dragleave', handleDragLeave, false);
            });
        }

    function drop(ev) {
        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        id_commande=data.replace("commande_", "");
            
        //on stop l'intervalle pour éviter les rechargements pendant l'action de l'utilisateur
        //clearInterval(timerMarker);
        clearInterval(timerLivreur);
        clearInterval(timerCommande);

        console.log(timerLivreur);

        //on récupère la liste des classes de l'élément vers lequel on drop la commande pour récupérer l'id du livreur selon le navigateur
        if ((navigator.userAgent).indexOf("Firefox") !== -1) {
            var className=ev.originalTarget.classList;
        }
        else {
            var className=ev.target.classList;
        }

        //on boucle sur la liste des classes jusqu'a trouver celle qui contient l'id du livreur
        for (i=0;i<className.length;i++) {
            if (className[i].indexOf("livreur_") !== -1) {
                id_livreur=className[i].replace("livreur_", "");
            }
        }

            var bloc_drop = document.getElementById('div_livreur_'+id_livreur);
            bloc_drop.style.backgroundColor = '#F3F3F3';
            setTimeout(function(){
                bloc_drop.style.backgroundColor = '#FFF';
            }, 3000);
            
            attribuer_commande(id_livreur, id_commande, 0);
        }

    function drop_desaffecte(ev) {
        //console.log(ev);
        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        //on récupère la liste des classes de l'élément selectionné
        className=document.getElementById(data).classList;
        id_commande=data.replace("commande_", "");
        ancien_livreur=0;
        
        //on boucle sur la liste des classes jusqu'a trouver celle qui contient l'id du livreur
        for (i=0;i<className.length;i++) {
            if (className[i].indexOf("livreur_") !== -1) {
                ancien_livreur=className[i].replace("livreur_", "");
            }
        }

        console.log(ancien_livreur);

        //on enleve le livreur attribué a la commande
        attribuer_commande(0, id_commande, ancien_livreur);
    }
</script>
