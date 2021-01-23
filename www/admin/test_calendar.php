<?php
require_once("inc_header.php");

?>

<link rel='stylesheet' href='assets/plugins/fullcalendar-3.0.0/fullcalendar.css' />

<!-- start: PAGE -->
<div class="main-content">
	<div class="container">
		<!-- start: PAGE HEADER -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-header">
					<h1>TEST</h1>
				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->
		<!-- start: PAGE CONTENT -->
        <div class="row" style="margin-top:40px;">
			<div class="col-sm-12">
				<div id="calendar"></div>
	        </div>
        </div>				
		<!-- end: PAGE CONTENT-->
	</div>
</div>
<!-- end: PAGE -->

<?php
require_once("inc_footer.php");
?>
<script src='assets/plugins/fullcalendar-3.0.0/lib/jquery.min.js'></script>
<script src='assets/plugins/fullcalendar-3.0.0/lib/moment.min.js'></script>
<script src='assets/plugins/fullcalendar-3.0.0/fullcalendar.js'></script>

<script language="javascript" type="text/javascript">
	$(document).ready(function() {

	    // page is now ready, initialize the calendar...
	    $('#calendar').fullCalendar({
            buttonText: {
                prev: '<i class="fa fa-chevron-left"></i>',
                next: '<i class="fa fa-chevron-right"></i>'
            },
            header: {
                left: 'prev,next title',
                center: '',
                right: ''
            }, 
            events: 'feed_livreurs.php?&action=calendar_theorique',
            eventRender: function (event, element) {
			    element.find('.fc-event-title').html(event.title);
			},
            columnFormat: {
                week: 'ddd d/M'
            },
            titleFormat: {
			   week: "dd [MMMM][ yyyy]{ '&#8212;' dd MMMM yyyy}"
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
			}
        });

	});
</script>

