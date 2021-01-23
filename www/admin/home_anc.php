<?php
session_start();

$menu = "dashboard";
$sous_menu = "";
if($_SESSION["role"]=="restaurateur"){
    header("location: commandes_liste.php");
}

require_once("inc_header.php");

$Livreur=new Livreur($sql);
$Commercant=new Commercant($sql);

//récupérer la semaine en cours
$week_start = strtotime('monday this week');
$week_end = strtotime('sunday this week');
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
                    <div class="panel-body">
                        <?php  
                        foreach($Commercant->getAllService(date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59") as $commercant) { 
                            ?>
                            <div class="commercant">
                                <div class="commercant-item">
                                    <div class="commercant-item-title"><?=$commercant->nom?></div>
                                    <div class="commercant-item-etat">
                                        <div class="etat"></div>
                                        <i class="clip-chevron-down"></i>
                                    </div>
                                    <div class="stop"></div>
                                </div>
                                <div class="commercant-detail">
                                    <?php
                                    foreach ($Commercant->getAllLivreur($commercant->id_commercant, date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59") as $livreur_commercant) {
                                        ?>
                                        <div class="commercant-detail-item">
                                            <span><?=$livreur_commercant->nom." ".$livreur_commercant->prenom?></span>
                                            <div class="commercant-detail-item-etat <?php if($livreur_commercant->statut=='ON') echo 'actif';?>"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>                        
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="clip-stats"></i>
                        Informations: Semaine du <?=utf8_encode(format_week($week_start, $week_end))?>
                        <div class="panel-tools">
                            <a class="btn btn-xs btn-link panel-collapse collapses" href="#">
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">

                        <div class="chart-planning-deskopt">
                            <div class="chart-title">
                                Planning: aujourd'hui
                            </div>
                            <div class="chart-legend">Nombre d'heures</div>
                            <div class="chart-m">
                                <div id="chartContainer4" style="height: 350px; width: 100%;"></div>
                                <div id="chartContainer5" style="height: 350px; width: 100%;"></div>
                            </div>
                            <div class="legend-bottom-l">
                                <div style="float:right;">
                                    <div class="square-green green-spe"></div>
                                    <span>Nb d'heures demandées par les commerçants</span>
                                    <div class="stop"></div>
                                </div>
                            </div>
                            <div class="legend-bottom-r">
                                <div style="float:left">
                                    <div class="square-grey grey-spe"></div>
                                    <span>Nb d'heures disponibles des livreurs</span>
                                    <div class="stop"></div>
                                </div>
                            </div>
                            <div class="stop"></div>

                            <div class="synthese">
                                <h2>Aujourd'hui</h2>
                                <div class='synthese-top'>
                                    <span>Cumul des retards</span>
                                    <span id="cumul_retard"></span>
                                    <div class='stop'></div>
                                </div>
                                <div class='synthese-bottom'>
                                    <span>Nombre de retards</span>
                                    <span id="nb_retard"></span>
                                    <div class='stop'></div>
                                </div>
                            </div>
                        </div>

                        <div class="chart-dashboard">
                            <div class="chart-dashboard-item">
                                <div class="chart-title">
                                    Activité des livreurs
                                </div>
                                <div class="chart-legend">Nombre de livreurs</div>
                                <div class="chart-m">
                                    <div id="chartContainer" style="height: 350px; width: 100%;"></div>
                                </div>
                                <div class="legend-bottom-l">
                                    <div style="float:right">
                                        <div class="square-green"></div>
                                        <span>Livreurs connectés</span>
                                        <div class="stop"></div>
                                    </div>
                                </div>
                                <div class="legend-bottom-r">
                                    <div style="float:left">
                                        <div class="square-grey"></div>
                                        <span>Livreurs absents</span>
                                        <div class="stop"></div>
                                    </div>
                                </div>
                                <div class="stop"></div>
                            </div>
                        </div>

                        <div class="chart-dashboard">
                            <div class="chart-dashboard-item">
                                <div class="chart-title">
                                    Commandes
                                </div>
                                <div class="chart-m">
                                    <div id="chartContainer6" style="height: 350px; width: 100%; margin-top:40px;"></div>
                                </div>
                                <div class="legend-bottom-l">
                                    <div style="float:right">
                                        <div class="square-green"></div>
                                        <span>Nombre de livraison</span>
                                        <div class="stop"></div>
                                    </div>
                                </div>
                                <div class="legend-bottom-r">
                                    <div style="float:left">
                                        <div class="square-grey"></div>
                                        <span>Livraison à l'heure</span>
                                        <div class="stop"></div>
                                    </div>
                                </div>
                                <div class="stop"></div>
                            </div>
                        </div>

                        <div class="chart-dashboard">
                            <div class="chart-dashboard-item">
                                <div class="chart-title note-livreur">
                                    Moyenne des notes des livreurs
                                </div>
                                <div class="chart-legend">Note /5</div>
                                <div class="chart-m">
                                    <div id="chartContainer2" style="height: 350px; width: 100%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="chart-dashboard">
                            <div class="chart-dashboard-item">
                                <div class="chart-title">
                                    Divers
                                </div>
                                <div class="chart-m">
                                    <div id="chartContainer3" style="height: 350px; width: 100%; margin-top:20px;"></div>
                                </div>
                                <div class="legend-bottom-l">
                                    <div style="float:right">
                                        <div class="square-green"></div>
                                        <span>CO2 non-généré</span>
                                        <div class="stop"></div>
                                    </div>
                                </div>
                                <div class="legend-bottom-r">
                                    <div style="float:left">
                                        <div class="square-grey km"></div>
                                        <span>km parcourus</span>
                                        <div class="stop"></div>
                                    </div>
                                </div>
                                <div class="stop"></div>
                            </div>
                        </div>

                    </div>
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
<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
<script src="assets/plugins/flot/jquery.flot.js"></script>
<script src="assets/plugins/flot/jquery.flot.resize.js"></script>
<script src="assets/plugins/flot/jquery.flot.categories.js"></script>
<script src="assets/plugins/flot/jquery.flot.pie.js"></script>
<script src="assets/plugins/jquery.canvasjs.min.js" type="text/javascript"></script>
<script type="text/javascript">

    // Polyfill: forEach
    if(NodeList.prototype.forEach === undefined){
        NodeList.prototype.forEach = Array.prototype.forEach;
    };

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
    
    window.onload = function(){
        var dataPoints=[];
        var dataPoints2=[];
        var dataPoints3=[];
        var dataPoints4=[];
        var dataPoints5=[];
        var dataPoints6=[];
        var dataPoints7=[];
        var dataPoints8=[];
        var dataPoints9=[];

        var chart4 = new CanvasJS.Chart("chartContainer4",
        {
            theme: "theme2",
            animationEnabled: true,
            axisY: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                interval: 10,
                tickColor: '#FFF',
                gridColor: "#ededed"
            },
            axisY2: {
                labelFontColor: "#FFF",
                tickColor: '#FFF'
            },
            axisX: {
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                tickColor: '#FFF',
                lineColor: "#ededed",
                labelFontSize: 10
            },
            data: [{
                type: "column",
                color: "#67ae73",
                dataPoints:[]
            }, {
                type: "column",
                color: "#d7d7d7",
                dataPoints:[]
            }]
        });

        $.getJSON('action_poo.php?action=count_retard&week_start=<?=$week_start?>&week_end=<?=$week_end?>', function (data) {
            console.log(data)
            $("#nb_retard").html(data["cpt_retard"]);
            $("#cumul_retard").html(data["tps_retard_week"]);

        });

        $.getJSON('action_poo.php?action=count_hours_dashboard', function (data) {
            //console.log(data)

            for (var i = 0; i < data.length; i++) {
                //console.log(data[i])
                dataPoints.push({ label: data[i].heure, y: data[i].nb_heures_commercant });
                dataPoints2.push({ label: data[i].heure, y: data[i].nb_heures_livreur });
            }

            chart4.options.data[0].dataPoints = dataPoints;
            chart4.options.data[1].dataPoints = dataPoints2;
            chart4.render();
        });

        var chart5 = new CanvasJS.Chart("chartContainer5",
        {
            theme: "theme2",
            animationEnabled: true,
            axisY: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                interval: 10,
                tickColor: '#FFF',
                gridColor: "#ededed"
            },
            axisY2: {
                labelFontColor: "#FFF",
                tickColor: '#FFF'
            },
            axisX: {
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                tickColor: '#FFF',
                lineColor: "#ededed",
                labelFontSize: 10
            },
            data: [{
                type: "column",
                color: "#67ae73",
                dataPoints:[
                {label: "8/10h", y: 40},
                {label: "10/12h", y: 28},
                {label: "12/14h", y: 35},
                {label: "14/16h", y: 45},
                {label: "16/18h", y: 56},
                {label: "18/20h", y: 54},
                {label: "20/22h", y: 48}
                ]
            }, {
                type: "column",
                color: "#d7d7d7",
                dataPoints:[
                {label: "8/10h", y: 40},
                {label: "10/12h", y: 27},
                {label: "12/14h", y: 32},
                {label: "14/16h", y: 37},
                {label: "16/18h", y: 28},
                {label: "18/20h", y: 51},
                {label: "20/22h", y: 53}
                ]
            }]
        });
        chart5.render();


        var chart = new CanvasJS.Chart("chartContainer",
        {
            theme: "theme2",
            animationEnabled: true,
            axisY: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                interval: 10,
                tickColor: '#FFF',
                gridColor: "#ededed"
            },
            axisY2: {
                labelFontColor: "#FFF",
                tickColor: '#FFF'
            },
            axisX: {
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                tickColor: '#FFF',
                lineColor: "#ededed"
            },
            data: [{
                type: "column",
                color: "#9fc752",
                dataPoints:[]
            }, {
                type: "column",
                color: "#c3c3c3",
                dataPoints:[]
            }]
        });

        var chart2 = new CanvasJS.Chart("chartContainer2",
        {
            theme: "theme2",
            animationEnabled: true,
            axisY: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                interval: 1,
                tickColor: '#FFF',
                maximum: 5,
                gridColor: "#ededed"
            },
            axisX: {
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                tickColor: '#FFF',
                lineColor: "#ededed"
            },
            data: [{
                type: "column",
                color: "#9fc752",
                dataPoints:[]
            }]
        });
        chart2.render();

        var chart3 = new CanvasJS.Chart("chartContainer3",
        {
            theme: "theme2",
            animationEnabled: true,
            axisY: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#50aee2',
                interval: 50,
                tickColor: '#FFF',
                gridColor: "#ededed",
                suffix: "km"
            },
            axisY2: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#9fc752',
                interval: 0.2,
                tickColor: '#FFF',
                gridColor: "#ededed",
                suffix: "t"
            },
            axisX: {
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                tickColor: '#FFF',
                lineColor: "#ededed"
            },
            data: [{
                type: "line",
                color: "#50aee2",
                dataPoints:[]
            }, {
                type: "line",
                axisYType: "secondary",
                color: "#9fc752",
                dataPoints:[]
            }]
        });
        chart3.render();


        var chart6 = new CanvasJS.Chart("chartContainer6",
        {
            theme: "theme2",
            animationEnabled: true,
            axisY: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#9fc752',
                interval: 20,
                tickColor: '#FFF',
                gridColor: "#ededed"
            },
            axisY2: {
                labelAngle: 0,
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#c3c3c3',
                interval: 25,
                maximum: 100,
                tickColor: '#FFF',
                gridColor: "#ededed",
                suffix: "%",
            },
            axisX: {
                labelFontStyle: 'normal',
                labelWeight: 'normal',
                labelFontColor: '#000',
                tickColor: '#FFF',
                lineColor: "#ededed"
            },
            data: [{
                type: "column",
                color: "#9fc752",
                dataPoints:[]
            }, {
                //toolTipContent: "{label} : {y}%",
                type: "line",
                axisYType: "secondary",
                color: "#c3c3c3",
                dataPoints:[]

            }]
        });                       

        $.getJSON('action_poo.php?action=count_stats_week&week_start=<?=$week_start?>&week_end=<?=$week_end?>', function (data) {
            console.log(data)

            for (var i = 0; i < data.length; i++) {
                console.log(data[i])
                pc_commandes_heure=(data[i].commandes_livree!=0) ? (data[i].commandes_heure/data[i].commandes_livree)*100 : 0;
                nb_km=parseFloat(data[i].nb_km/1000);
                nb_km=+nb_km.toFixed(2);

                carbonne_voiture=((nb_km*0.06981)*44)/12;
                carbonne_electrique=((nb_km*0.03946)*44)/12;
                eco_co2=carbonne_voiture-carbonne_electrique;
                eco_co2=+eco_co2.toFixed(2);

                //stats livreurs
                dataPoints3.push({ label: data[i].jour, y: data[i].livreur_connecte });
                dataPoints4.push({ label: data[i].jour, y: data[i].livreur_absent });

                //stats commandes
                dataPoints5.push({ label: data[i].jour, y: data[i].commandes_livree });
                dataPoints6.push({ label: data[i].jour, y: pc_commandes_heure });

                //stats moyenne
                dataPoints7.push({ label: data[i].jour, y: parseFloat(data[i].moyenne) });

                //stats nb km
                dataPoints8.push({ label: data[i].jour, y: nb_km });
                dataPoints9.push({ label: data[i].jour, y: eco_co2 });
            }

            chart.options.data[0].dataPoints = dataPoints3;
            chart.options.data[1].dataPoints = dataPoints4;
            chart.render();

            chart6.options.data[0].dataPoints = dataPoints5;
            chart6.options.data[1].dataPoints = dataPoints6;
            chart6.render();

            chart2.options.data[0].dataPoints = dataPoints7;
            chart2.render();

            chart3.options.data[0].dataPoints = dataPoints8;
            chart3.options.data[1].dataPoints = dataPoints9;
            chart3.render();
        });

    };
</script>