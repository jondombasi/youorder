<?php
require_once("inc_connexion.php");

if(isset($_GET["id"]))		{$id=$_GET["id"];}else{$id="";}
if(isset($_GET["aff_valide"]))		{$aff_valide=$_GET["aff_valide"];}else{$aff_valide="";}
if(isset($_POST["action"]))		{$action=$_POST["action"];}else{$action="";}

$menu = "vehicule";
$sous_menu = "liste";
$aff_erreur = "";
$continu = true;

//on récupère la date du lundi de la semaine en cours
$day = date('w');
if ($day==0) {
	$day=6;
}
else {
	$day--;
}
$week_start = date('Y-m-d', strtotime('-'.$day.' days'))." 00:00:00";

$Vehicule = new Vehicule($sql, $id);
$Livreur = new Livreur($sql);

$Vehicule->getPaginationPlanning(30, $id);
$nbpages=$Vehicule->getNbPagesPlanning();

if ($id!="") {
	$nom=$Vehicule->getNom();
	$type=$Vehicule->getType();
	$immatriculation=$Vehicule->getImmatriculation();
	$marque=$Vehicule->getMarque();
	$volume=$Vehicule->getVolume();
	$etat=$Vehicule->getEtat();
}

if($action=="enregistrer"){
	$date=$_POST["date"];
	$h_debut=$_POST["h_debut"];
	$h_fin=$_POST["h_fin"];
	$livreur_id=$_POST["livreur"];
	
	if($date==""){
		$css_date_obl = "has-error";
		$continu = false;
	}
	if($h_debut==""){
		$css_hdebut_obl = "has-error";
		$continu = false;
	}
	if($h_fin==""){
		$css_hfin_obl = "has-error";
		$continu = false;
	}
	if($h_debut!="" && $h_fin!="" && $date!="") {
		$datetime = new DateTime();
		$today=$datetime->createFromFormat('d-m-Y H:i:s', date('d-m-Y')." 00:00:00");
		$date_check = $datetime->createFromFormat('d-m-Y H:i:s', $date." 00:00:00");
		$date_debut = $datetime->createFromFormat('d-m-Y H:i', $date." ".$h_debut);
		$date_fin = $datetime->createFromFormat('d-m-Y H:i', $date." ".$h_fin);
		if ($date_debut>=$date_fin) {
			$css_hfin_obl = "has-error";
			$css_hdebut_obl = "has-error";
			$continu = false;
		}

		if ($date_check<$today) {
			$css_date_obl = "has-error";
			$continu = false;
		}
	}
	if($livreur_id==""){
		$css_livreur_obl = "has-error";
		$continu = false;
	}

	if($continu){
		$Vehicule->setPlanning($date_debut->format('Y-m-d H:i:s'), $date_fin->format('Y-m-d H:i:s'), $livreur_id, $id, $_SESSION["userid"],$etat);
		header("location: vehicules_fiche2.php?id=".$id);
	}
	else{
		$aff_erreur="1";		
	}
}

require_once("inc_header.php");

?>
<link rel="stylesheet" href="assets/plugins/select2/select2.css">
<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css">
<link rel="stylesheet" href="assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="assets/plugins/fullcalendar/fullcalendar/fullcalendar.css">
<style>
	.tab_btn {
		border:1px solid #9fc752 ;
		padding:10px;
		text-align:center;
		cursor:pointer;
	}

	.btn_actif {
		background-color:#9fc752;
		color:white;
	}

	#tab1, #tab2 {
		margin-top:50px;
	}

	#info_calendar {
	    position: absolute;
	    z-index:500;
	}
	
	.triangle-border {
	  position:relative;
	  padding:15px;
	  margin:1em 0 3em;
	  border:1px solid #000;
	  color:#333;
	  background:#fff;
	}

	.triangle-border:before {
	  content:"";
	  position:absolute;
	  bottom:-14px; /* value = - border-top-width - border-bottom-width */
	  left:47px; /* controls horizontal position */
	  border-width:13px 13px 0;
	  border-style:solid;
	  border-color:#000 transparent;
	  /* reduce the damage in FF3.0 */
	  display:block;
	  width:0;
	}

	/* creates the smaller  triangle */
	.triangle-border:after {
	  content:"";
	  position:absolute;
	  bottom:-13px; /* value = - border-top-width - border-bottom-width */
	  left:47px; /* value = (:before left) + (:before border-left) - (:after border-left) */
	  border-width:13px 13px 0;
	  border-style:solid;
	  border-color:#fff transparent;
	  /* reduce the damage in FF3.0 */
	  display:block;
	  width:0;
	}

	#tooltip_table th, #tooltip_table td {
		padding:5px 10px;
	}
</style>

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1><?=$type." ".$immatriculation." - ".$marque." ".$volume."L"?></h1>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
		<?php
		if($aff_erreur=="1"){
			?>
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert">
                    ×
                </button>
                <i class="fa fa-check-circle"></i>
                Le formulaire comporte des erreurs, veuillez les corriger et valider à nouveau.
            </div>                                            
            <?php	
		}

		if($aff_valide=="1"){
		?>
        <div class="alert alert-success">
            <button class="close" data-dismiss="alert">
                ×
            </button>
            <i class="fa fa-check-circle"></i>
            Les modifications ont été enregistrées.
        </div>                    
		<?php } ?>
		<div class="row">
			<div class="col-sm-12">
				<span class="tab_btn btn_actif col-sm-2 col-sm-offset-4" id="tab1_btn" onclick="show_div('tab1', 'tab2');">Planning</span>
				<span class="tab_btn col-sm-2" id="tab2_btn" onclick="show_div('tab2', 'tab1');">Historique</span>
			</div>
		</div>

		<div id="tab1">
	        <div class="row">
	        	<div class="col-sm-2 col-sm-offset-10" style="text-align:right;margin-bottom:20px;">
		    		<a class="btn btn-light-grey" target="_blank" href="javascript:void(0)" id="export_btn">Exporter en CSV</a>
		    	</div>
				<div class="col-sm-12">
	                <div class="panel panel-default">
						<div class="panel-heading" style="padding-left:10px">Affecter un véhicule</div>
						<div class="panel-body">
		                    <form role="form" name="form_affecter" id="form_affecter" method="post" action="vehicules_fiche2.php?id=<?php echo $id; ?>" class="form-horizontal">
				            	<input type="hidden" name="action" value="enregistrer"/>
				            	<input type="hidden" name="hdebut_txt" id="hdebut_txt" value="<?=$h_debut?>"/>
				            	<input type="hidden" name="hfin_txt" id="hfin_txt" value="<?=$h_fin?>"/>
				            	<input type="hidden" name="week_start" id="week_start" value=""/>
				            	<input type="hidden" name="week_end" id="week_end" value=""/>
				                <div class="form-group">
				                    <div class="col-sm-3 col-sm-offset-3  <?php echo $css_date_obl; ?>">
				                    	<p><b>Date</b></p>
				                    	<div class="input-group">
				                    		<span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
											<input type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" id="date" name="date" value="<?=$date?>">
										</div>
				                    </div>
				                    <div class="col-sm-3  <?php echo $css_hdebut_obl; ?>">
				                        <p><b>Heure de début</b></p>
				                        <div class="input-group input-append bootstrap-timepicker">
											<input type="text" id="h_debut" name="h_debut" class="form-control timepicker">
											<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
										</div>
				                    </div>
				                </div>

				                <div class="form-group">
				                    <div class="col-sm-3 col-sm-offset-3 ">
				                    	<p><b>Livreur</b></p>
				                    	<select name="livreur" id="livreur" class="form-control search-select <?php echo $css_livreur_obl; ?>">
				                            <option value="">&nbsp;</option>
				                            <?php 
												foreach ($Livreur->getAll() as $livreur) {
													if ($livreur_id==$livreur->id) {
														$sel="selected";
													}
													else {
														$sel="";
													}
													echo "<option value='".$livreur->id."' ".$sel.">".$livreur->prenom." ".strtoupper($livreur->nom)."</option>";
												}
				                            ?>
				                        </select>
				                    </div>
				                    <div class="col-sm-3  <?php echo $css_hfin_obl; ?>">
				                        <p><b>Heure de fin</b></p>
				                    	<div class="input-group input-append bootstrap-timepicker">
											<input type="text" id="h_fin" name="h_fin" class="form-control timepicker2">
											<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
										</div>
				                    </div>
				                </div>

				                <div class="form-group">
				                	<div class="col-sm-3 col-sm-offset-6" style="text-align:right">
						        		<input type="submit" id="bt" class="btn btn-main" value="Enregistrer" style="width:150px;">
						        	</div>
						        </div>
				            </form>
						</div>
					</div>    
				</div>
			</div>  

			<div class="row">
				<div class="col-sm-12">
	                <div class="panel panel-default">
						<div class="panel-heading" style="padding-left:10px">Planning de l'utilisation du véhicule</div>
						<div class="panel-body">
							<div id='calendar'></div>
						</div>
					</div>
				</div>
			</div>			
	    </div>
	    <div id="tab2" style="display:none">
	    	<div class="col-sm-2 col-sm-offset-9" style="text-align:right;margin-bottom:20px;">
	    		<a class="btn btn-light-grey" target="_blank" href="action_poo.php?action=export_planning_vehicules&id_vehicule=<?=$id?>&week_start=<?=$week_start?>&week_end=">Exporter en CSV</a>
	    	</div>
	    	<div id="div_tab_resultat" class="table-responsive col-sm-10 col-sm-offset-1">
	        	<table class="table table-bordered table-hover" id="sample-table-1">
		    		<thead>
		        		<th>Date</th>
		        		<th>Actions</th>
		        		<th>Modifié par</th>
		        		<th>Etat</th>
		        	</thead>
		        	<tbody>
		        	</tbody>
		        </table>
	        </div>
	        <div class="col-sm-1"></div>
	        <div style="text-align:right;" class="col-sm-2 col-sm-offset-9">
		        <ul style="margin:0px;" id="paginator-example-1" class="pagination custom"></ul>
		    </div>
	    </div>
		<!-- end: PAGE CONTENT-->
	</div>

	<!-- TOOLTIP --> 
	<div id="info_calendar" style="display:none">
		<div class="triangle-border">
			<div style="position:absolute;right:5px;top:0;cursor:pointer" onclick="$('#info_calendar').hide()"><i class="fa fa-times"></i></div>
			<div id="info_content"></div>
		</div>
	</div>
</div>
<!-- end: PAGE -->

<?php
require_once("inc_footer.php");
?>
<script src="assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="assets/plugins/select2/select2.min.js"></script>
<script src="assets/plugins/bootstrap-paginator/src/bootstrap-paginator.js"></script>   
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="assets/plugins/fullcalendar/fullcalendar/fullcalendar.js"></script>
<script language="javascript">
	jQuery(document).ready(function() {
		runSelect2();
		runCalendar();

		//avoir la date de début et de fin de la semaine en cours (pour export)
		week_start=$('#calendar').fullCalendar('getView').start;
		week_end=$('#calendar').fullCalendar('getView').end;
		week_end.setDate(week_end.getDate() - 1);
		week_start=week_start.getFullYear()+"-"+("0"+(week_start.getMonth()+1)).slice(-2)+"-"+("0"+week_start.getDate()).slice(-2);
		week_end=week_end.getFullYear()+"-"+("0"+(week_end.getMonth()+1)).slice(-2)+"-"+("0"+week_end.getDate()).slice(-2);
		$("#week_start").val(week_start);
		$("#week_end").val(week_end);

		$("#export_btn").attr("href", "action_poo.php?action=export_planning_vehicules&id_vehicule=<?=$id?>&week_start="+week_start+"&week_end="+week_end);

		//remplir les heures si elles existent, sinon en mettre par défaut
		var d1 = new Date ();
		var coeff = 1000 * 60 * 5;
		var rounded = new Date(Math.round(d1.getTime() / coeff) * coeff)
		var heure1=rounded.getHours();
		var heure2=rounded.getHours()+1;
		var minute=rounded.getMinutes();

		if ($("#hdebut_txt").val()!="") {
			heure_deb=$("#hdebut_txt").val()
		}
		else {
			heure_deb=heure1+":"+minute;
		}

		if ($("#hfin_txt").val()!="") {
			heure_fin=$("#hfin_txt").val()
		}
		else {
			heure_fin=heure2+":"+minute;
		}
		
		$('.date-picker').datepicker({
            autoclose: true
        });
        $('input.timepicker').timepicker({
        	showMeridian: false,
        	defaultTime: heure_deb

    	});
        $('input.timepicker2').timepicker({
        	showMeridian: false,
        	defaultTime: heure_fin
    	});

    	$("#h_debut, h_fin").on("focus", function() {
		    return $(this).timepicker("showWidget");
		});
		$("input").on("focus", function() {
			$(this).removeClass("has-error");
		});

		$('#h_debut').timepicker().on('changeTime.timepicker', function(e) {
			//on récupère la nouvelle date a laquelle on ajoute 1h pour mettre à jour l'heure de fin
			//TO DO : changer la date si l'heure passe a 1h du jour suivant ?
			var d = new Date("1970-01-01 "+e.time.value+":00");
			d.setHours(d.getHours() + 1);

			$('#h_fin').timepicker('setTime', d.getHours()+":"+d.getMinutes());

	    });

		tableau_resultat(1);
		runPaginator();
		$('.fc-button-prev, .fc-button-next, .fc-button-today').click(function(){
			week_start=$('#calendar').fullCalendar('getView').start;
			week_end=$('#calendar').fullCalendar('getView').end;
			week_end.setDate(week_end.getDate() - 1);
			week_start=week_start.getFullYear()+"-"+("0"+(week_start.getMonth()+1)).slice(-2)+"-"+("0"+week_start.getDate()).slice(-2);
			week_end=week_end.getFullYear()+"-"+("0"+(week_end.getMonth()+1)).slice(-2)+"-"+("0"+week_end.getDate()).slice(-2);
			$("#week_start").val(week_start);
			$("#week_end").val(week_end);

			$("#export_btn").attr("href", "action_poo.php?action=export_planning_vehicules&id_vehicule=<?=$id?>&week_start="+week_start+"&week_end="+week_end);
		});
	});	

	function runSelect2() {
		$(".search-select").select2({
			placeholder: "Select a State",
			allowClear: true
		});
	}

	function runCalendar() {
        var calendar = $('#calendar').fullCalendar({
            buttonText: {
                prev: '<i class="fa fa-chevron-left"></i>',
                next: '<i class="fa fa-chevron-right"></i>'
            },
            header: {
                left: 'prev,next title',
                center: '',
                right: ''
            }, 
            events: 'feed_vehicule.php?id=<?=$id?>',
            columnFormat: {
                agendaWeek: 'ddd dd/MM'
            },
            titleFormat: {
			   week: "dd [MMMM][ yyyy]{ ' - ' dd MMMM yyyy}"
			},
            editable: false,
            droppable: false,
            selectable: false,
            selectHelper: false,
            defaultView: 'agendaWeek',
            minTime: "08:00:00",
            maxTime: "24:00:00",
            allDaySlot: false,
            firstDay: 1,
            lang: 'fr',
            axisFormat: 'HH:mm',
			timeFormat: {
			    agenda: 'H:mm{ - H:mm}'
			},
            eventClick: function(event, jsEvent, view) {
		        show_info(jsEvent.pageX, jsEvent.pageY, event.tooltip);
		    },  
        });
	}

	//fonction affichage tooltips
	function show_info(coor_x, coor_y, texte) {
		$("#info_content").html(texte);
		navbar_height=$(".navbar").height()+130; 
		$("#info_calendar").css("left", coor_x-60);
		$("#info_calendar").css("top", coor_y-navbar_height);
		$("#info_calendar").show();
	}

	function show_div(div_to_show, div_to_hide) {
		$("#info_calendar").hide();
		$("#"+div_to_hide).hide();
		$("#"+div_to_show).show();

		$("#"+div_to_hide+"_btn").removeClass('btn_actif');
		$("#"+div_to_show+"_btn").addClass('btn_actif');
	}

	function tableau_resultat(p){
		$.ajax({
			url      : 'action_poo.php',
		  	data	   : 'action=liste_vehicule_planning_histo&id_vehicule=<?=$id?>&week_start=<?=$week_start?>&p='+p,
		  	type	   : "GET",
		  	cache    : false,		  
		  	success  : function(transport) {  
				$('#sample-table-1').find("tbody").html(transport);
				if ($(".tooltips").length) {
					$('.tooltips').tooltip();
				}
			}
		});					
	}

	function runPaginator() {
		$('#paginator-example-1').bootstrapPaginator({
			bootstrapMajorVersion: 3,
			currentPage: 1,
			totalPages: <?php echo $nbpages; ?>,
			onPageClicked: function (e, originalEvent, type, page) {
				tableau_resultat(page);
			}
		});
	}
</script>

