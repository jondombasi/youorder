<?php
session_start();

$menu       = "live";
$sous_menu  = "dashboard";


require_once("inc_header.php");

$Livreur    = new Livreur($sql);
$Commercant = new Commercant($sql);

//récupérer la semaine en cours
$week_start = strtotime('monday this week');
$week_end   = strtotime('sunday this week');
?>
<link rel="stylesheet" href="assets/plugins/fullcalendar/fullcalendar/fullcalendar.css">
<style>
#map{
    width:100%;
    height:450px;
}
#map-canvas{
  display: block;
  width: 95%;
  height: 350px;
  margin: 0 auto;
  -moz-box-shadow: 0px 5px 20px #ccc;
  -webkit-box-shadow: 0px 5px 20px #ccc;
  box-shadow: 0px 5px 20px #ccc;
}
#map-canvas.large{
  height:500px;
}
/** FIX for Bootstrap and Google Maps Info window styes problem **/
img[src*="gstatic.com/"], img[src*="googleapis.com/"] {
    max-width: none;
}
</style>

<!-- start: PAGE -->
<div class="main-content dashboard">
    <div class="container">
        <!-- start: PAGE HEADER -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h1>Tableau de bord <small>Vues et Stats</small></h1>
                </div>
                <!-- end: PAGE TITLE & BREADCRUMB -->
            </div>
        </div>
        <!-- end: PAGE HEADER -->
        <!-- start: PAGE CONTENT -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="icon-align-left"></i>
                        Liste des commerçants
                        <div class="panel-tools">
                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
                        </div>
                    </div>
                    <div id="commercants_service" class="panel-body" >

                    </div>
                </div>                        
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="clip-stats"></i>
                        Nous sommes le <?=utf8_encode(strftime("%A %d %B"))?>
                        <div class="panel-tools">
                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#">
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- ligne1 -->
                        <div class="row">
                            <ul class="mini-stats col-sm-12" style="border:none !important;">
                                <li class="col-sm-4">
                                    <div class="sparkline_bar_good">
                                        <span>3,5,9,8,13,11,14</span>
                                    </div>
                                    <div id="nb_livreurs" class="values">
                                        
                                    </div>
                                </li>
                                <li class="col-sm-4">
                                    <div class="sparkline_bar_good">
                                        <span>3,5,9,8,13,11,14</span>
                                    </div>
                                    <div class="values">
                                        <strong id="tps_retard"></strong>
                                        <div class="label-chart">Cumul des retards</div>
                                    </div>
                                </li>
                                <li class="col-sm-4">
                                    <div class="sparkline_bar_good">
                                        <span>3,5,9,8,13,11,14</span>
                                    </div>
                                    <div class="values">
                                        <strong id="nb_absent"></strong>
                                        <div class="label-chart">Nombre d'absents</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- fin ligne1 -->

                        <!-- ligne2 -->
                        <div class="row" style="margin-top:25px">
                            <ul class="mini-stats col-sm-12" style="border:none !important;">
                                <li class="col-sm-4">
                                    <div class="sparkline_bar_good">
                                        <span>3,5,9,8,13,11,14</span>
                                    </div>
                                    <div class="values">
                                        <strong id="lad"></strong>
                                        <div class="label-chart">Nombre de livraisons</div>
                                    </div>
                                </li>
                                <li class="col-sm-4">
                                    <div class="easy-pie-chart">
                                        <span id="livraison_chart" class="pc number" data-percent="0"> <span id="livraison" class="percent">0</span> </span>
                                        <div class="label-chart">
                                            Livraisons à l'heure
                                        </div>
                                    </div>
                                </li>
                                <li class="col-sm-4">
                                    <div class="easy-pie-chart">
                                        <span id="note_chart" class="note number" data-percent="0"> <span id="note" class="number"></span> </span>
                                        <div class="label-chart">
                                            Moyene des notes des livreurs
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- fin ligne2 -->

                        <!-- ligne3 -->
                        <div class="row" style="margin-top:25px">
                            <ul class="mini-stats col-sm-12" style="border:none !important;">
                                <li class="col-sm-4">
                                    <div class="easy-pie-chart">
                                        <span id="reponse_chart" class="pc number" data-percent="0"> <span id="reponse" class="percent">0</span> </span>
                                        <div class="label-chart">
                                            Taux de réponse
                                        </div>
                                    </div>
                                </li>
                                <li class="col-sm-4">
                                    <div class="sparkline_bar_good">
                                        <span>3,5,9,8,13,11,14</span>
                                    </div>
                                    <div class="values">
                                        <strong id="nb_km"></strong>
                                        <div class="label-chart">KM parcourus en livraison</div>
                                    </div>
                                </li>
                                <li class="col-sm-4">
                                    <div class="sparkline_bar_good">
                                        <span>3,5,9,8,13,11,14</span>
                                    </div>
                                    <div class="values">
                                        <strong id="nb_co2"></strong>
                                        <div class="label-chart">CO2 non généré</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- fin ligne3 -->

                    </div>
                </div>
            </div>                        
        </div>
    </div>                    
    <!-- end: PAGE CONTENT-->
</div>
<!-- end: PAGE -->
<?php
require_once("inc_footer.php");
?>
<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
<script src="assets/plugins/flot/jquery.flot.js"></script>
<script src="assets/plugins/flot/jquery.flot.resize.js"></script>
<script src="assets/plugins/flot/jquery.flot.categories.js"></script>
<script src="assets/plugins/flot/jquery.flot.pie.js"></script>
<script src="assets/plugins/jquery.canvasjs.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery.sparkline/jquery.sparkline.js"></script>
<script src="assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.js"></script>
<script src="assets/plugins/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        runSparkline();
        runEasyPieChart();

        get_dashboard();
        get_commercant_service();

        // Polyfill: forEach
        if(NodeList.prototype.forEach === undefined){
            NodeList.prototype.forEach = Array.prototype.forEach;
        };
    })

    // function to initiate Sparkline
    function runSparkline() {
        $(".sparkline_bar_good span").sparkline('html', {
            type: "bar",
            barColor: "#459D1C",
            barWidth: "5",
            height: "24",
            disableTooltips: true,
            disableHighlight: true
        });
    }

    function runEasyPieChart() {
        if (isIE8 || isIE9) {
            if (!Function.prototype.bind) {
                Function.prototype.bind = function (oThis) {
                    if (typeof this !== "function") {
                        // closest thing possible to the ECMAScript 5 internal IsCallable function
                        throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
                    }
                    var aArgs = Array.prototype.slice.call(arguments, 1),
                        fToBind = this,
                        fNOP = function () {}, fBound = function () {
                            return fToBind.apply(this instanceof fNOP && oThis ? this : oThis, aArgs.concat(Array.prototype.slice.call(arguments)));
                        };
                    fNOP.prototype = this.prototype;
                    fBound.prototype = new fNOP();
                    return fBound;
                };
            }
        }
        $('.easy-pie-chart .pc').easyPieChart({
            animate: 1000,
            size: 70,
            barColor: function (percent) {
               return (percent > 50 ? '#5cb85c' : '#cb3935');
            }
        });
        $('.easy-pie-chart .note').easyPieChart({
            animate: 1000,
            lineWidth: 3,
            barColor: function (percent) {
               return (percent > 80 ? '#5cb85c' : '#cb3935');
            },
            size: 70
            
        });
    }

    function get_dashboard() {
        $.ajax({
            url      : 'https://www.you-order.eu/admin/action_poo.php',
            data     : 'action=get_dashboard',
            type     : "GET",
            dataType : "JSON",
            timeout: 5000,
            success: function(transport) {                
                $("#nb_livreurs").  html((transport["nb_livreurs"]>"1") ? '<strong>'+transport["nb_livreurs"]+'</strong><div class="label-chart">Livreurs en service</div>' : '<strong>'+transport["nb_livreurs"]+'</strong><div class="label-chart">Livreur en service</div>');
                $("#tps_retard").   html(transport["tps_retard"]);
                $("#nb_absent").    html(transport["nb_absent"]);
                $("#lad").          html(transport["lad"]);
                $("#nb_km").        html(transport["nb_km"]);
                $("#nb_co2").       html(transport["nb_co2"]);

                $("#note").         html(transport["moyenne"]);
                $('#note_chart').data('easyPieChart').update(transport["moyenne_pc"]);

                $("#livraison").    html(transport["livraison_pc"]);
                $('#livraison_chart').data('easyPieChart').update(transport["livraison_pc"]);

                $("#reponse").      html(transport["reponse_pc"]);
                $('#reponse_chart').data('easyPieChart').update(transport["reponse_pc"]);

                setTimeout(function(){get_dashboard()},10000);
            },
            error: function(transport) {
                console.log(transport);
            }
        });
    }



    function get_commercant_service() {
        $.ajax({
            url      : 'https://www.you-order.eu/admin/action_poo.php',
            data     : 'action=get_commercant_service',
            type     : "GET",
            timeout: 5000,
            success: function(transport) {
                $("#commercants_service").html(transport);

                // Effet accordeon
                var items = document.querySelectorAll('.commercant');

                items.forEach(function(item){

                    item.addEventListener('click', function(){

                        var items_no_active = document.querySelectorAll('.commercant-detail');
                        var item_active = item.querySelector('.commercant-detail');

                        items_no_active.forEach(function(item_no_active){
                            if(item_active !== item_no_active){
                                item_no_active.style.display = "none";
                            }
                        });

                        if(item_active.style.display === "block"){
                            item_active.style.display = "none";
                        } else {
                            item_active.style.display = "block";
                        }

                    });

                });

                // Actif ?
                var items_no_active = document.querySelectorAll('.commercant');

                items_no_active.forEach(function(item_no_active){

                    var items_etat = item_no_active.querySelectorAll('.commercant-detail-item-etat');
                    items_etat.forEach(function(item_etat){
                        if(item_etat.classList.contains('actif')){
                            var test = item_no_active.querySelector('.etat');
                            test.classList.add('actif');
                        }
                    });

                });

                setTimeout(function(){get_commercant_service()},10000);
            },
            error: function(transport) {
                console.log(transport);
            }
        });
    }
</script>