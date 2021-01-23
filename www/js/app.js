/* Nos valeurs: adapter taille de div sans image */

window.onresize = resize;

function resize(){
    
    var photo_height;
    var obj = document.getElementById('height-txt');

    var photo_size = document.getElementById('valeurs');

    if(obj.offsetHeight){photo_height=obj.offsetHeight;}
    else if(obj.style.pixelHeight){photo_height=obj.style.pixelHeight;}
    
    photo_height = photo_height - 10;

    photo_size.style.height = photo_height+"px";
    
    /* VIDEO */
    if(document.body.clientWidth >= 1024){
        var video = document.getElementById('video');
        var features = document.getElementById('features');
        var difference = window.innerHeight - 850;

        features.style.marginTop = difference+"px"; 
    }
    /* /VIDEO */
    
}

resize();

/* Menu */
$(document).scroll(function(){
    if(document.body.clientWidth >= 1024){
        if(window.scrollY < 80){
            $('.logo').fadeIn(700);
            $('.menu').fadeIn(700);
        } else if(window.scrollY > 80 && window.scrollY < window.innerHeight){
            $('.logo').fadeOut(700);
            $('.menu').fadeOut(700);
            $('#menu-scroll').css('backgroundColor', 'transparent');
        } else if(window.scrollY > window.innerHeight){
            $('.logo').fadeIn(700);
            $('.menu').fadeIn(700);
            $('#menu-scroll').css('backgroundColor', '#1d2226');
        }
    }
});
/* /Menu */

/* Menu Hamburger */

var hamburger = document.getElementById('hamburger');
var menu_mobile = document.getElementById('menu-mobile');

hamburger.addEventListener('click', affiche_menu);

function affiche_menu(){
    hamburger.classList.toggle('transition-burger');
    menu_mobile.classList.toggle('transition');
}

/* /Menu Hamburger */

/* /Nos valeurs: adapter taille de div sans image */

$(document).ready(function(){
    
    /* Effet scroll menu */
    
    $('a[href^=#]').click(function(){ 
        
        cible = $(this).attr('href');
        var menu_mobile = document.getElementById('menu-mobile');
        menu_mobile.classList.remove('transition');
        if(cible !== '#' && cible !== '#solution'){
            hamburger.classList.toggle('transition-burger');
        }
        if($(cible).length >= 1) hauteur = $(cible).offset().top;
        else hauteur = $("a[name="+cible.substr(1,cible.length-1)+"]").offset().top;	
            
        $('html,body').animate({scrollTop:hauteur}, 1500,'easeOutQuint');
        
    });
    
    /* /Effet scroll menu */
    
    /* Slider Partenaires */
    
    $("#owl").owlCarousel({
 
        autoPlay: 3000,
        items: 2,
        itemsCustom: [[0, 1], [400, 1], [700, 2], [1000, 3], [1200, 3], [1600, 3]]
        
 
    });
    
    /* /Slider Partenaires */
    
    /* Slider Blog */
    
    $("#owl-blog").owlCarousel({
 
        autoPlay: 3000,
        itemsDesktop: [1199, 3],
        itemsTablet: [768,2],
        itemsMobile:[479,1],
        items: 4
 
    });
    
    /* /Slider Blog */
    
});

var form_job = document.getElementById('form-job');
var form_job_cacher = document.getElementById('form-job-cacher');
var form_job_visible = document.getElementById('form-job-visible');
var job = document.getElementById('job');

form_job.addEventListener('click', afficher_form);

function afficher_form(){
    form_job_cacher.style.display = 'none';
    form_job.style.display = 'none';
    form_job_visible.style.display = 'block';
    job.style.paddingTop = 0;
}

/* Valeurs */
function affiche_valeur(nbr){
    var val = document.getElementById('valeur'+nbr);
    var val_det = document.getElementById('det_valeur'+nbr);
    
    if(val_det.style.display === 'block'){
        val.style.display = 'block';
        val_det.style.display = 'none';
    } else {
        val.style.display = 'none';
        val_det.style.display = 'block';
    }
}
/* /Valeurs */

/* Contact */
var operation = document.getElementById('operation');
var info = document.getElementById('info');

function change_couleur(nbr){
    if(nbr === 1){
        operation.classList.add('green');
        info.classList.remove('green');
    } else {
        operation.classList.remove('green');
        info.classList.add('green');
    }
    $("#type").val(nbr);
}
/* /Contact */

/* Solutions */
function hover(numero){
    if(document.body.clientWidth >= 768){
        var hover = document.getElementById('hover'+numero);
        var title = document.getElementById('title'+numero);
        
        hover.style.display = 'block';
        title.style.display = 'none';
    }
}

function hover_end(numero){
    if(document.body.clientWidth >= 768){
        var hover = document.getElementById('hover'+numero);
        var title = document.getElementById('title'+numero);
        
        hover.style.display = 'none';
        title.style.display = 'block';
    }
}

function hover_click(numero){
    if(document.body.clientWidth <= 768){
        var hover = document.getElementById('hover'+numero);
        var title = document.getElementById('title'+numero);
        
        hover.style.display = 'block';
        title.style.display = 'none';
        
        for(var i = 1; i < 9; i++){
            
            if(i !== numero){
                var hover2 = document.getElementById('hover'+i);
                var title2 = document.getElementById('title'+i);

                hover2.style.display = 'none';
                title2.style.display = 'block';
            }
        }
        
    }
}

function hover_click_end(numero){
    if(document.body.clientWidth <= 768){
        var hover = document.getElementById('hover'+numero);
        var title = document.getElementById('title'+numero);
        
        hover.style.display = 'none';
        title.style.display = 'block';
    }
}
/* /Solutions */

/* Image Scooter */
if(document.body.clientWidth <= 768){
    var photo_scooter = document.getElementById('photo_scooter');
    photo_scooter.src = 'image/scooter_360.jpg';
}
/* /Image Scooter */
