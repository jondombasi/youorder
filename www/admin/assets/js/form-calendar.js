var Calendar = function () {
    //function to initiate Full CAlendar
    var runCalendar = function () {
        /* initialize the calendar
				 -----------------------------------------------------------------*/
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var form = '';
        var calendar = $('#calendar').fullCalendar({
            buttonText: {
                prev: '<i class="fa fa-chevron-left"></i>',
                next: '<i class="fa fa-chevron-right"></i>'
            },
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: [{
                title: 'Meeting with Boss',
                start: new Date(y, m, 1),
                className: 'label-default'
            }, {
                title: 'Bootstrap Seminar',
                start: new Date(y, m, d - 5),
                end: new Date(y, m, d - 2),
                className: 'label-teal'
            }, {
                title: 'Lunch with Nicole',
                start: new Date(y, m, d - 3, 12, 0),
                className: 'label-green',
                allDay: false
            }],
            columnFormat: {
                week: 'ddd d/M'
            },
            editable: false,
            droppable: false, // this allows things to be dropped onto the calendar !!!
            selectable: false,
            selectHelper: false,
            defaultView: 'agendaWeek',
            minTime: "08:00:00",
            maxTime: "24:00:00",
            allDaySlot: false,
            firstDay: 1,
            lang: 'fr'
        });
    };
    return {
        init: function () {
            runCalendar();
        }
    };
}();