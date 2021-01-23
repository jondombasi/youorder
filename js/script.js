function recharge(){
	location.reload();
}

function retour(){
	history.back();
}

function lien(url) {
	window.location.href = url
}

function createCookie(name,value,days) {
	$.cookie(name, value, {
	   path    : '/',          			//The value of the path attribute of the cookie 
	   domain  : '.mobile.gaymec.com',  //The value of the domain attribute of the cookie
	});
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

function affecte_suppid(id){
    $("#suppid").val(id)
}

function affecte_txt(txt){
	$("#txt_notification_push").html(txt);
}
function affecte_pushid(id, nom, prenom){
	$("#pushid").val(id)
	$("#nom_livreur").html(prenom+" "+nom)
	console.log(prenom+" / "+nom+" / "+id);
}
function confirm_suppression(action){
	id = $("#suppid").val();
    (action=="suppcommande") ? lien('action_poo.php?action='+action+'&id='+id) : lien('action.php?action='+action+'&id='+id);
	
}
function confirm_push(action){
	id = $("#pushid").val();
    $.ajax({
        url      : '//www.you-order.eu/admin/action_poo.php',
        data     : 'action=send_push&id='+id+'&message='+encodeURI('Votre nouveau planning est disponible.')+'&url='+encodeURI("planning.html"),
        type     : "GET",
        cache    : false,
        timeout: 2000,
        success: function(transport) {
            console.log(transport);
        },
        error: function(transport) {
            console.log(transport);
        }
    });
	//lien('action_poo.php?action=send_push&id='+id)
}

function openPopup(lien_popup){
    $(".pop-up-generique").attr("href", lien_popup);
    $('.pop-up-generique').magnificPopup({
        type: 'ajax',
        modal: true,
        fixedBgPos:true,
        fixedContentPos:true,
        overflowY: 'scroll'
    }).magnificPopup('open');
}

$('.image-popup-vertical-fit').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    mainClass: 'mfp-img-mobile',
    image: {
        verticalFit: true
    }
});

$(document).on('click', '.popup-modal-dismiss', function (e) {
    e.preventDefault();
    $.magnificPopup.close();
});

function load_notif() {   
	$.ajax({
        url      : '//www.you-order.eu/admin/action_poo.php',
        data     : 'action=get_notif_nb',
        type     : "GET",
        cache    : false,
        timeout: 2000,
        success: function(transport) {
            //on joue un son s'il y a une nouvelle notification
            if (parseInt(transport) > parseInt($(".nb_notif_txt").html())) document.getElementById('audio_notif').play();
            $(".nb_notif_txt").html(transport);
            $(".notif-title").html((transport>1) ? "Vous avez "+transport+" notifications" : "Vous avez "+transport+" notification");

        },
        error: function(transport) {
            console.log(transport);
        }
    });
    $.ajax({
        url      : '//www.you-order.eu/admin/action_poo.php',
        data     : 'action=get_notif',
        type     : "GET",
        cache    : false,
        timeout: 2000,
        success: function(transport) {
            $("#liste_notif").html(transport);

        },
        error: function(transport) {
            console.log(transport);
        }
    });
}

function vu_notif(id_notif, id_commande, type_notif, link) {
	$.ajax({
        url      : '//www.you-order.eu/admin/action_poo.php',
        data     : 'action=vu_notif&id_notif='+id_notif,
        type     : "GET",
        cache    : false,
        timeout: 2000,
        success: function(transport) {
            load_notif();
            if (link) {
                if (type_notif=="ajout" || type_notif=="modif") {
                    lien("commandes_visu.php?id="+id_commande);
                }
                else if (type_notif=="planning_ajout" || type_notif=="planning_modif") {
                    lien("livreurs_planning.php");
                }
            }
        },
        error: function(transport) {
            console.log(transport);
        }
    });
}