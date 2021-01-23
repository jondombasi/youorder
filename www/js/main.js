/*global window */
(function($) {
    "use strict";

    /*-------------VARIABLES--------------*/
    var w = $(window),
        doc = $(document),
        windowWidth = w.width(),
        nav = $(".dima-nav").outerHeight(),
        windowHeight = w.height(),
        tw = $("#tweet"),
        prefix,
        /*Modernizr Var*/
        isTouch = Modernizr.touch, //detect touch devices
        isTransitions = Modernizr.csstransitions, //detect if brwoesr support transitions
        isFirefox = typeof InstallTrigger !== 'undefined', //detect Firefox version
        //isIE = /*@cc_on!@*/ false || !!document.documentMode, //detect IE version
        isChrome = !!window.chrome, //detect chrome version
        isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0; ////detect chrome isSafari
    /*!------------VARIABLES--------------*/

    window.PIXELDIMA = {};

    doc.ready(function(PIXELDIMA) {

        this.PIXELDIMA = PIXELDIMA || {}; //Main Namespace

        /**
         * [ Main Module (Okab) ]
         */
        PIXELDIMA.NAV = function() {
            /**
             * Our Private Functions
             **/

            var myMenu = function() {
                mobileNav();
                fixNav();
                subMenu();
                searchBox();
                onePage();
            };

            var mobileNav = function() {
                var d = $(".dima-nav"),
                    i = $("a.dima-btn-nav");
                /**
                 * [Click Mobile button]
                 */
                i.click(function(event) {
                    event.preventDefault();
                    if (i.hasClass("btn-active")) {
                        d.slideUp();
                        i.removeClass("btn-active");
                    } else {
                        i.addClass("btn-active");
                        d.slideDown();
                    }
                });
                $('.mobnav-subarrow').click(function() {
                    $(this).parent().toggleClass("xpopdrop");
                });
            };
            var fixNav = function() {

                $.fn.fix_navbar = function(el) {

                    el = $(this);
                    el.addClass("fix_nav");
                    var clean_nav = $(".dima"),
                        topBar = $('.dima-topbar').outerHeight() || 0,

                        fixAll = function(el) {
                            var parent = (el).parent(),
                                offsetTop = parent.offset().top + topBar;
                            if (doc.scrollTop() > offsetTop) {
                                if (doc.scrollTop() > (el.height() - w.height())) {
                                    el.addClass("fixed");
                                    if (windowWidth > 989) {
                                        clean_nav.addClass("clear-nav").css("padding-top", el.outerHeight()).show();
                                    }
                                } else {
                                    $('.dima-navbar-wrap .dima-navbar .logo img.one').addClass('hide');
                                    el.removeClass("fixed");
                                    clean_nav.removeClass("clear-nav").hide();
                                }
                            } else {
                                el.removeClass("fixed");
                                clean_nav.removeClass("clear-nav").hide();
                            }
                        };

                    w.scroll(function() {
                        fixAll(el);
                    });
                    w.ready(function() {
                        fixAll(el);
                    });
                };

                $.fn.show_navbar = function(el) {

                    el = $(this);
                    el.addClass("fix_nav");
                    PIXELDIMA.SLIDE.revolution();

                    var clean_nav = $(".dima"),
                        offsetBy = el.attr("data-offsetBy"),
                        topBar = $('.dima-topbar').outerHeight() || 0,
                        oFFset = $(offsetBy).outerHeight() || 0,
                        menuVal = $('.dima-navbar').outerHeight() || 0,

                        fixAll = function(el) {
                            var parent = (el).parent(),
                                offsetTop = parent.offset().top + topBar + oFFset + menuVal;
                            if (doc.scrollTop() > offsetTop) {
                                if (doc.scrollTop() > (el.height() - w.height())) {
                                    el.addClass("fixed animated fadeInDown");
                                    if (windowWidth > 989) {
                                        clean_nav.addClass("clear-nav").css("padding-top", el.outerHeight()).show();
                                    }
                                } else {
                                    $('.dima-navbar-wrap .dima-navbar .logo img.one').addClass('hide');
                                    el.removeClass("fixed animated fadeInDown");
                                    clean_nav.removeClass("clear-nav").hide();
                                }
                            } else {
                                el.removeClass("fixed animated fadeInDown");
                                clean_nav.removeClass("clear-nav").hide();
                            }
                        };

                    w.scroll(function() {
                        fixAll(el);
                    });
                    w.ready(function() {
                        fixAll(el);
                    });
                };
            };
            var subMenu = function() {

                $("ul.sf-menu").superfish({
                    delay: 650,
                    animation: {
                        opacity: 'show',
                        height: 'show'
                    },
                    hoverClass: 'sfHover',
                    pathClass: 'overrideThisToUse',
                    pathLevels: 1,
                    animationOut: {
                        opacity: 'hide',
                        height: 'hide'
                    },
                    speed: 'normal',
                    eventType: 'toggle',
                    speedOut: 'fast',
                    disableHI: false,
                    autoArrows: 'false'

                });
            };
            var searchBox = function() {
                //search box event
                var bool = true;
                $(".search-btn").click(function(e) {
                    e.preventDefault();
                    if (bool) {
                        $("#search-box").stop().slideDown(250, "easeOutExpo");
                        $("#search-box input[type=text]").focus();
                        bool = false;
                    } else {
                        $("#search-box").stop().slideUp(250, "easeOutExpo");
                        bool = true;
                    }
                });

                function closeSearch() {
                        $("#search-box").stop().slideUp(250, "easeOutExpo");
                    }
                    //close search btn event
                $("#close").click(function(e) {
                    e.preventDefault();
                    closeSearch();
                    bool = true;
                });
            };
            var onePage = function() {
                $('.dima-onepage').onePageNav({
                    currentClass: 'current',
                    changeHash: true,
                    scrollSpeed: 750,
                    scrollThreshold: 0.5,

                    filter: '',
                    easing: 'swing',
                    begin: function() {                        
                        //I get fired when the animation is starting
                        $('body').append('<div id="device-dummy" style="height: 1px;"></div>');
                    },
                    end: function() {
                        //I get fired when the animation is ending
                        $('#device-dummy').remove();
                    },
                    scrollChange: function() {
                        //I get fired when you enter a section and I pass the list item of the section
                    }
                });

                $("a[data-scrollto]").click(function(event) {
                    
                    event.preventDefault();
                    var divScrollToAnchor = $(this).attr('data-scrollto');

                    var topOffsetScroll = 0;

                    $('html, body').stop().animate({
                        'scrollTop': $(divScrollToAnchor).offset().top - topOffsetScroll
                    }, 750, 'easeOutQuint');
                    return false;
                });

            };
            /**
             * Setup Function
             */
            var init = function() {
                myMenu();
                $("html").imagesLoaded(); // Detect when images have been loaded.
            };

            /**
             * [Our Public Function Here]
             */
            var build = {
                init: init,
            };
            return build;
        }();
        // Handles twitter,instagram,flickr API
        PIXELDIMA.API = function() {
            var twitter = function() {
                tw.each(function() {
                    var lem = $(this),
                        id = lem.attr("data-id"),
                        type = lem.attr("data-type"),
                        t = lem.attr("data-count");

                    if (type == "quote") {
                        type = quote
                    } else {
                        type = list
                    }
                    var dima = {
                        "id": id,
                        "domId": lem,
                        "maxTweets": t,
                        "showUser": 0,
                        "showImages": 0,
                        "showRetweet": !0,
                        "customCallback": type,
                        "showInteraction": 0,
                        "enableLinks": 1
                    };

                    function list(tweets) {
                        var x = tweets.length;
                        var n = 0;
                        var element = document.getElementById('tweet');
                        var html = '<ul class="with-border tweet_list slides">';
                        while (n < x) {
                            html += '<li>' + tweets[n] + '</li>';
                            n++;
                        }
                        html += '</ul>';
                        element.innerHTML = html;
                    }

                    function quote(tweets) {
                        var x = tweets.length;
                        var n = 0;
                        var element = document.getElementById('tweet');
                        var html = '';
                        while (n < x) {
                            html += '<div class=\"dima-testimonial quote-style\"><blockquote class=\"without-icon\">' + tweets[n] + '</div></blockquote>';
                            n++;
                        }
                        html += '';
                        element.innerHTML = html;
                    }
                    twitterFetcher.fetch(dima);
                });

            };
            var instagram = function() {

                function createPhotoElement(photo) {
                    var innerHtml = $('<img>')
                        .addClass('instagram-image')
                        .attr('src', photo.images.thumbnail.url);

                    innerHtml = $('<a>')
                        .attr('target', '_blank')
                        .attr('href', photo.link)
                        .append(innerHtml);

                    return $('<li>')
                        .addClass('instagram-placeholder')
                        .attr('id', photo.id)
                        .append(innerHtml);
                }

                function didLoadInstagram(event, response) {
                    /*jshint validthis:true */
                    var that = this;
                    $.each(response.data, function(i, photo) {
                        $(that).append(createPhotoElement(photo));
                    });
                }
                var lem = $(".dima-instagram"),
                    d = lem.attr("data-limit") || 6,
                    i = lem.attr("data-userId"),
                    m = lem.attr("data-hash"),
                    a = lem.attr("data-accessToken"),
                    h = lem.attr("data-clientId") || 'baee48560b984845974f6b85a07bf7d9';

                lem.on('didLoadInstagram', didLoadInstagram);

                if (m != undefined) {
                    lem.instagram({
                        hash: m,
                        count: d,
                        clientId: h
                    });
                } else if (i != undefined && a != undefined) {
                    lem.instagram({
                        userId: i,
                        accessToken: a,
                        count: d,
                    });
                }
            };

            var flickr = function() {
                $(".flkr-cont").each(function() {
                    var lem = $(this),
                        Val = lem.attr("data-limit"),
                        id_val = lem.attr("data-id")
                    lem.jflickrfeed({
                        limit: Val,
                        qstrings: {
                            id: id_val
                        },
                        itemTemplate: '<li>' +
                            '<a rel="colorbox" href="{{image}}" title="{{title}}">' +
                            '<img src="{{image_s}}" alt="{{title}}" />' +
                            '</a>' +
                            '</li>'
                    });
                });
            };
            var init = function() {
                twitter();
                flickr();
                instagram();
            };
            var build = {
                init: init,
            };
            return build;
        }();

        PIXELDIMA.SHOP = function() {
            var toggleBox = function() {
                $('a.show-box').click(function() {
                    var Val = $(this).attr("data-show");
                    $(Val).slideToggle();
                    return false;
                });
                $('.radio').click(function() {
                    var Val = $(this).attr("data-show");
                    $('.toHide').hide();
                    $(Val).slideToggle();
                });
                $('.checkbox').click(function() {
                    var Val = $(this).attr("data-show");
                    $(Val).slideToggle();
                });
            };

            var zoom = function() {
                $('.zoom-it').imagezoomsl({
                    classmagnifier: window.external ? window.navigator.vendor === 'Yandex' ? "" : 'round-loope' : "",
                    zoomrange: [2, 8],
                });
            };
            var showShopList = function() {

                $(".di-grids").click(function() {
                    $("#rows").fadeOut(1000, function() {
                        $("#rows").removeClass('products-list').addClass('products-grids');
                        $("#rows").fadeIn(1000);
                    });
                });
                $(".di-list").click(function() {
                    $("#rows").fadeOut(1000, function() {
                        $("#rows").removeClass('products-grids').addClass('products-list');
                        $("#rows").fadeIn(1000);
                    });
                });
            };
            var shopBtn = function() {

                $(".minus").click(function() {
                    var inputEl = $(this).parent().children().next();
                    var qty = inputEl.val();
                    if ($(this).parent().hasClass("minus"))
                        qty++;
                    else
                        qty--;
                    if (qty < 0)
                        qty = 0;
                    inputEl.val(qty);
                });


                $(".plus").click(function() {
                    var inputEl = $(this).parent().children().next();
                    var qty = inputEl.val();
                    if ($(this).hasClass("plus"))
                        qty++;
                    else
                        qty--;
                    if (qty < 0)
                        qty = 0;
                    inputEl.val(qty);
                });
            };
            var sliderRange = function() {
                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: 40,
                    values: [5, 30],
                    slide: function(event, ui) {
                        $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
                    }
                });
                $("#amount").val("$" + $("#slider-range").slider("values", 0) +
                    " - $" + $("#slider-range").slider("values", 1));
            };

            var init = function() {
                showShopList();
                toggleBox();
                shopBtn();
                sliderRange();
                zoom();
            };
            var build = {
                init: init,
            };
            return build;
        }();

        // Handles scrollable contents using jQuery sly and perfect scrollbar  
        PIXELDIMA.SCROLL = function() {
            var localScroll = function() {
                $(".dima-nav").localScroll({
                    target: "body",
                    axis: "y",
                    duration: 800,
                    margin: true,
                    offset: -(nav - 13)
                });
            };
            var callingSly = function() {
                var slyOptions = {
                    scrollBy: 200,
                    speed: 600,
                    smart: 0,
                    easing: 'easeOutQuart',
                    scrollBar: '.scrollbar',
                    dynamicHandle: 1,

                    dragHandle: 0,
                    clickBar: 1,
                    contentEditable: 1,
                    mouseDragging: 0,
                    touchDragging: 1,
                    releaseSwing: 1,
                    swingSpeed: 0.1,
                    elasticBounds: 1,
                    cycleBy: null,
                    cycleInterval: 4000
                };
                var d = new Sly('#framee', slyOptions);
                d.init();
            };
            var perfectScrollbar = function() {
                $(".quick-view-content").perfectScrollbar();
                $('.quick-view-content').perfectScrollbar('update');
            }

            var parallax = function() {

                if (isFirefox) {
                    prefix = '-moz-';
                } else if (isChrome || isSafari) {
                    prefix = '-webkit-';
                }

                $('.background-image-hide,.background-image-holder').each(function() {
                    var imgSrc = $(this).children('img').attr('src');
                    if (typeof imgSrc !== typeof undefined) {
                        $(this).css('background', 'url("' + imgSrc + '")');
                        $(this).children('img').hide();
                        $(this).css('background-position', '0% 0%');
                    }
                });

                $('.parallax-background').each(function() {
                    $(this).attr('data-bottom-top', prefix + 'transform: translate3d(0px,-200px, 0px);');
                    $(this).attr('data-center', prefix + 'transform: translate3d(0px,0px, 0px);');
                    $(this).attr('data-top-bottom', prefix + 'transform: translate3d(0px,100px, 0px);');
                    $(this).attr('data-direction', 'vertical');
                });
                // Init Skrollr
                if (!(/Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i).test(navigator.userAgent || navigator.vendor || window.opera)) {
                    var skr = skrollr.init({
                        forceHeight: false,
                        smoothScrolling: true,
                    });
                    // Refresh Skrollr after resizing our sections
                    skr.refresh($(".homeSlide"));
                }
            };
            var init = function() {
                localScroll();
                parallax();
                perfectScrollbar();
                callingSly();
                $.scrollToTop();
            }
            var build = {
                init: init,
            };
            return build;
        }();

        PIXELDIMA.ANIMATION = function() {
            var animations = function() {
                var elm = $("[data-animate]");
                elm.each(function() {
                    var elm = $(this),
                        dataDelay = elm.attr("data-delay") || 0,
                        offsetVal = elm.attr("data-offset") || "100%",
                        trgger = elm.attr("data-trgger") || "false",
                        a = 0;
                    var dataAnimate = elm.attr("data-animate");
                    if (a = dataDelay ? Number(dataDelay) + 10 : 300, !elm.hasClass("animated")) {
                        $(this).addClass('opacity-zero');
                        elm.waypoint(function() {
                            var $this = $(this);
                            setTimeout(function() {
                                $this.addClass('show animated ' + dataAnimate);
                            }, a);
                        }, {
                            offset: offsetVal,
                            triggerOnce: trgger
                        });
                    }

                })
            };
            var notAnimations = function() {
                var elm = $("[data-animate]");
                elm.each(function() {
                    var elm = $(this),
                        dataDelay = elm.attr("data-delay") || 0,
                        offsetVal = elm.attr("data-offset") || "100%",
                        trgger = elm.attr("data-trgger") || "false",
                        a = 0;
                    if (a = dataDelay ? Number(dataDelay) + 300 : 300, !elm.hasClass("animated")) {
                        $(this).addClass('opacity-zero');
                        elm.waypoint(function() {
                            var $this = $(this);
                            setTimeout(function() {
                                $this.animate({
                                    opacity: 1
                                }, {
                                    step: function(now, fx) {
                                        var X = 100 * now;
                                        $(fx.elem).css("filter", "alpha(opacity=" + X + ")");
                                    }
                                });
                            }, a);
                        }, {
                            offset: offsetVal,
                            triggerOnce: trgger
                        });
                    }

                })
            };
            var twoLinesHover = function() {
                $('.link_overlay.two_lines').each(function() {
                    var h = $(this).width();
                    var t = (h - 130) / 2;
                    $(this).find('ul').css({
                        'margin-left': t,
                        'margin-right': t
                    })
                });
                $('.work-info').each(function() {
                    var h = $(this).parent().outerHeight();
                    if (h <= 100) {
                        h = $(this).parent().outerHeight();
                    }
                    var t = h - ((h / 2) + 75);
                    //console.log(h)
                    $(this).css({
                        'bottom': t
                    })
                });
            };
            var init = function() {
                if (!Modernizr.mq('only all and (max-width: 480px)')) {
                    if (isTransitions) {
                        animations();
                    } else {
                        notAnimations();
                    }
                }

                twoLinesHover();
            }
            var build = {
                init: init,
                twoLinesHover: twoLinesHover,
            };
            return build;
        }();

        PIXELDIMA.SLIDE = function() {
            var flexSlider = function() {
                var D = $(".flexslider");
                D.each(function() {
                    var elm = $(this);
                    var d = elm.attr("data-animation") || 'slide';
                    var i = elm.attr("data-easing") || 'swing';
                    var m = elm.attr("data-direction") || 'horizontal';
                    var a = elm.attr("data-autoplay");
                    var o = elm.attr("data-show") || 7000;
                    var k = elm.attr("data-speed") || 700;
                    var b = elm.attr("data-video") || 0;
                    var e = elm.attr("data-nav") || 1;
                    var z = elm.attr("data-arrows") || 1;
                    var p = elm.attr("data-loop") || !1;
                    var h = 0;
                    var r = 1;


                    elm.css('height', 'auto').flexslider({
                        selector: ".slides > .slide-item",
                        animation: d,
                        easing: i,
                        slideshow: !a,
                        direction: m,
                        slideshowSpeed: Number(o),
                        animationSpeed: Number(k),
                        video: b,
                        controlNav: e,
                        directionNav: z,
                        animationLoop: p,
                        useCSS: r,
                        smoothHeight: h,
                        pauseOnHover: h,
                        mousewheel: h,
                        multipleKeyboard: r,
                        start: function() {
                            PIXELDIMA.LIGHTBOX.lightBox();
                        },
                        after: function() {}
                    });
                })
            };
            var owlSlider = function() {

                $(".owl-carousel").each(function(e) {
                    var elm = $(this),
                        d = elm.attr("data-owl-namber") || 1,
                        i = elm.attr("data-owl-autoPlay"),
                        m = elm.attr("data-owl-navigation") || false,
                        a = elm.attr("data-owl-pagination"),
                        p = elm.attr("data-owl-mousewheel"),
                        e = elm.attr("data-owl-loop") || true,
                        phone = elm.attr("data-owl-phone") || 1,
                        tablet = elm.attr("data-owl-tablet") || 1,
                        margin_val = parseInt(elm.attr("data-owl-margin")) || 0;

                    if (i === "true") {
                        i = true;
                    } else {
                        i = false;
                    }
                    if (a === "true") {
                        a = true;
                    } else {
                        a = false;
                    }

                    // Custom Navigation Events
                    if (m === "true") {
                        m = true;
                        elm.next().css("display", "block");

                        $(".next").click(function() {
                            elm.trigger('next.owl.carousel');
                        });
                        $(".prev").click(function() {
                            elm.trigger('prev.owl.carousel', [300]);
                        });

                    } else {
                        m = false;
                    }
                    elm.owlCarousel({
                        items: d,
                        margin: margin_val,
                        nav: m,
                        dots: a,
                        slideSpeed: 500,
                        dotsSpeed: 400,
                        autoplay: i,
                        rtl: true,
                        lazyLoad: true,
                        loop: e,
                        responsiveClass: true,
                        responsive: {
                            250: {
                                items: phone
                            },
                            480: {
                                items: tablet
                            },
                            768: {
                                items: tablet
                            },
                            989: {
                                items: tablet
                            },
                            1199: {
                                items: d
                            }
                        }
                    });
                    if (p === "true") {
                        elm.bind('mousewheel', '.owl-stage', function(e) {
                            e.preventDefault();
                            if (e.originalEvent.wheelDelta > 0) {
                                elm.trigger('next.owl.carousel');
                            } else {
                                elm.trigger('prev.owl.carousel', [300]);
                            }
                            e.preventDefault();
                        });
                    }
                });
            };
            var revolution = function() {
                var d, i, m, a, p;

                d = $(".fullscreenOnePage").show().revolution({
                    dottedOverlay: "none",
                    delay: 9000,
                    startwidth: 1170,
                    startheight: 700,
                    hideThumbs: 200,
                    thumbWidth: 100,
                    thumbHeight: 50,
                    thumbAmount: 5,
                    navigationType: "bullet",
                    navigationArrows: "solo",
                    navigationStyle: "none",

                    touchenabled: "on",
                    onHoverStop: "on",

                    parallax: "mouse",
                    parallaxBgFreeze: "on",

                    swipe_velocity: 0.7,
                    swipe_min_touches: 1,
                    swipe_max_touches: 1,
                    drag_block_vertical: false,

                    keyboardNavigation: "on",

                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 0,
                    navigationVOffset: 20,

                    soloArrowLeftHalign: "left",
                    soloArrowLeftValign: "center",
                    soloArrowLeftHOffset: 20,
                    soloArrowLeftVOffset: 0,

                    soloArrowRightHalign: "right",
                    soloArrowRightValign: "center",
                    soloArrowRightHOffset: 20,
                    soloArrowRightVOffset: 0,

                    shadow: 0,
                    fullWidth: "off",
                    fullScreen: "on",

                    spinner: "spinner0",

                    stopLoop: "off",
                    stopAfterLoops: -1,
                    stopAtSlide: -1,

                    shuffle: "off",

                    forceFullWidth: "off",
                    fullScreenAlignForce: "off",
                    minFullScreenHeight: "400",

                    hideThumbsOnMobile: "off",
                    hideNavDelayOnMobile: 1500,
                    hideBulletsOnMobile: "off",
                    hideArrowsOnMobile: "off",
                    hideThumbsUnderResolution: 0,

                    hideSliderAtLimit: 0,
                    hideCaptionAtLimit: 0,
                    hideAllCaptionAtLilmit: 0,
                    startWithSlide: 0,
                    fullScreenOffsetContainer: ""
                });

                i = $(".fullscreen").show().revolution({
                    dottedOverlay: "none",
                    delay: 16000,
                    startwidth: 1140,
                    startheight: 550,
                    hideThumbs: 200,

                    thumbWidth: 100,
                    thumbHeight: 50,
                    thumbAmount: 5,

                    navigationType: "bullet",
                    navigationArrows: "solo",
                    navigationStyle: "none",

                    touchenabled: "on",
                    onHoverStop: "on",

                    swipe_velocity: 0.7,
                    swipe_min_touches: 1,
                    swipe_max_touches: 1,
                    drag_block_vertical: false,

                    parallax: "mouse",
                    parallaxBgFreeze: "on",
                    parallaxLevels: [7, 4, 3, 2, 5, 4, 3, 2, 1, 0],

                    keyboardNavigation: "off",

                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 0,
                    navigationVOffset: 20,

                    soloArrowLeftHalign: "left",
                    soloArrowLeftValign: "center",
                    soloArrowLeftHOffset: 20,
                    soloArrowLeftVOffset: 0,

                    soloArrowRightHalign: "right",
                    soloArrowRightValign: "center",
                    soloArrowRightHOffset: 20,
                    soloArrowRightVOffset: 0,

                    shadow: 0,
                    fullWidth: "on",
                    fullScreen: "off",

                    spinner: "spinner4",

                    stopLoop: "off",
                    stopAfterLoops: -1,
                    stopAtSlide: -1,

                    shuffle: "off",

                    autoHeight: "off",
                    forceFullWidth: "off",

                    hideThumbsOnMobile: "off",
                    hideNavDelayOnMobile: 1500,
                    hideBulletsOnMobile: "off",
                    hideArrowsOnMobile: "off",
                    hideThumbsUnderResolution: 0,

                    hideSliderAtLimit: 0,
                    hideCaptionAtLimit: 0,
                    hideAllCaptionAtLilmit: 0,
                    startWithSlide: 0,
                    fullScreenOffsetContainer: "header"
                });
                m = jQuery('.minfullwidth').show().revolution({
                    delay: 15000,
                    dottedOverlay: "none",
                    startwidth: 1170,
                    startheight: 500,
                    hideThumbs: 10,
                    fullWidth: "on",
                    navigationType: "bullet",
                    navigationArrows: "solo",
                    navigationStyle: "none",
                    touchenabled: "on",
                    onHoverStop: "on",
                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 0,
                    navigationVOffset: 35,
                    fullScreenOffsetContainer: "header"
                });
                a = jQuery('.boxedcontainer').show().revolution({
                    delay: 15000,
                    startwidth: 1170,
                    startheight: 450,
                    hideThumbs: 10,
                    fullWidth: "off",
                    forceFullWidth: "off",
                    navigationType: "bullet",
                    navigationArrows: "solo",
                    navigationStyle: "none",
                    touchenabled: "on",
                    onHoverStop: "on",
                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 0,
                    navigationVOffset: 35,
                    fullScreenOffsetContainer: ""
                });
                p = jQuery('.photoslide').show().revolution({
                    delay: 4000,
                    startwidth: 1170,
                    startheight: 500,
                    hideThumbs: 0,
                    thumbWidth: 42,
                    thumbHeight: 50,
                    thumbAmount: 10,
                    navigationType: "thumb",
                    navigationArrows: "solo",
                    navigationStyle: "round",
                    fullWidth: "off",
                    fullScreen: "on",
                    soloArrowLeftHalign: "left",
                    soloArrowLeftValign: "center",
                    soloArrowLeftHOffset: 20,
                    soloArrowLeftVOffset: 0,
                    soloArrowRightHalign: "right",
                    soloArrowRightValign: "center",
                    soloArrowRightHOffset: 20,
                    soloArrowRightVOffset: 0,
                    shadow: 0,
                    touchenabled: "on",
                    onHoverStop: "on",
                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 0,
                    navigationVOffset: 35,
                    fullScreenOffsetContainer: ""
                });
            };
            var init = function() {
                flexSlider();
                owlSlider();
                revolution();
            }
            var build = {
                init: init,
                flexSlider: flexSlider,
                revolution: revolution,
            };
            return build;
        }();

        PIXELDIMA.LIGHTBOX = function() {
            var lightBox = function() {
                var d = $('[data-lightbox="image"]'),
                    i = $('[data-lightbox="iframe"]'),
                    m = $('[data-lightbox="ajax"]'),
                    a = $('[data-lightbox="gallery"]');
                //image    
                d.magnificPopup({
                    type: 'image',
                    closeOnContentClick: !0,
                    closeBtnInside: !1,
                    fixedContentPos: !0,
                    mainClass: "mfp-zoom-in",
                    image: {
                        verticalFit: !0
                    }
                });
                //gallery
                a.each(function() {
                    $(this).magnificPopup({
                        delegate: 'a.gallery-item',
                        type: 'image',
                        overflowY: 'scroll',
                        closeOnContentClick: !0,
                        closeBtnInside: !1,
                        fixedContentPos: !0,
                        mainClass: "mfp-zoom-in",
                        image: {
                            verticalFit: !0
                        },
                        gallery: {
                            enabled: !0,
                            navigateByImgClick: !0,
                            preload: [0, 1]
                        }
                    });
                });
                //iframe ( map, youtube ...)
                i.magnificPopup({
                    disableOn: 500,
                    type: 'iframe',
                    mainClass: 'mfp-zoom-in',
                    removalDelay: 160,
                    preloader: 0,
                    fixedContentPos: 0
                });

                //Stop suggested video from youtube
                $('a[href*="youtube.com/watch"]').magnificPopup({
                    type: 'iframe',
                    iframe: {
                        patterns: {
                            youtube: {
                                index: 'youtube.com',
                                id: 'v=',
                                src: '//www.youtube.com/embed/%id%?rel=0&autoplay=1'
                            }
                        }
                    }
                });

                //Ajax
                m.magnificPopup({
                    type: 'ajax',
                    closeBtnInside: 0,
                    alignTop: 1,
                    cache: 1,
                    overflowY: 'scroll',
                    mainClass: "mfp-zoom-in",
                    callbacks: {
                        ajaxContentAdded: function() {
                            PIXELDIMA.SLIDE.flexSlider();
                            PIXELDIMA.SHOP.init();
                            PIXELDIMA.UI.init();
                            PIXELDIMA.SCROLL.init();
                        }
                    }
                });
            };

            var init = function() {
                lightBox();
            }

            var build = {
                init: init,
                lightBox: lightBox,
            };

            return build;
        }();

        PIXELDIMA.MEDIA = function() {
            var bigVedio = function() {
                // initialize BigVideo
                var BV = new $.BigVideo({
                        container: $('.video-wrap'),
                        forceAutoplay: isTouch
                    }),
                    V = $('.video-wrap').attr('data-video-wrap'),
                    img = $('.video-wrap').attr('data-img-wrap');
                if (typeof V != typeof undefined) {
                    if (!isTouch) {
                        BV.init();
                        BV.show(V, {
                            ambient: true,
                            doLoop: true
                        });
                    } else {
                        BV.init();
                        BV.show(img);
                    }
                }
            };
            var mediaElement = function() {
                $('.audio-video').each(function() {
                    $(this).mediaelementplayer({});
                })
            };
            var init = function() {
                bigVedio();
                mediaElement();
            }
            var build = {
                init: init,
            };
            return build;
        }();

        PIXELDIMA.UI = function() {
            var flatShadow = function() {
                $(".flat-icon").flatshadow({
                    color: "#FFF",
                    angle: "SE",
                    fade: true,
                    boxShadow: false // Accept full 6 digit hex color (#000000)
                });
            };
            var countUp = function() {
                $(".countUp").each(function() {

                    $(this).waypoint({
                        handler: function() {
                            $(this).find("span").countTo({
                                formatter: function(e) {
                                    return e = e.toFixed(), e = e.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                }
                            });
                        },
                        offset: "100%",
                        triggerOnce: true
                    });
                });
            };
            var progress = function() {
                //progress bar animation
                setTimeout(function() {
                    $(".progress,.dial").waypoint(function() {
                        $(".progress .progress-bar").each(function() {
                            var el = $(this),
                                perc = el.attr("aria-valuenow"),
                                current_percent = 0,
                                progress = setInterval(function() {
                                    if (current_percent >= perc) {
                                        clearInterval(progress);
                                    } else {
                                        current_percent += 1;
                                        el.css("width", (current_percent) + "%");
                                    }
                                }, 1);
                        });

                        //progress bar color
                        $('.progress').append(function() {
                            var elm = $(this),
                                color = elm.attr("data-color-val");
                            elm.css('border-color', color);
                            elm.find('.progress-bar').css('background-color', color);
                            elm.find('.progress-bar .percent').css('background-color', color);
                            elm.find('span').css('border-top-color', color);
                        });

                        //circular                
                        $('.dial').each(function() {

                            var elm = $(this),
                                width = elm.attr("data-width"),
                                perc = elm.attr("value");

                            elm.knob({
                                'value': 0,
                                'min': 0,
                                'max': 100,
                                "skin": "tron",
                                "readOnly": true,
                                "thickness": 0.09,
                                "displayInput": false,
                                "bgColor": "rgba(255,255,255,0)",
                                "linecap": ""
                            });

                            $({
                                value: 0
                            }).animate({
                                value: perc
                            }, {
                                duration: 1000,
                                easing: 'swing',
                                progress: function() {
                                    elm.val(Math.ceil(this.value)).trigger('change');
                                }
                            });

                            //circular progress bar color
                            $(this).append(function() {
                                elm.parent().parent().find('.circular-bar-content').css('top', -(width / 2 + 10));
                                elm.parent().parent().find('.circular-bar-content label').text(perc + '%');
                            });

                        });
                    }, {
                        offset: "100%",
                        triggerOnce: true
                    });
                }, 300);
            };
            var tabs = function() {

                $("body").tooltip({
                    selector: "[data-toggle=tooltip]",
                    animation: true,
                    html: true
                });

                $(".collapse").collapse({
                    toggle: false
                });


                $("#dima-tab-nav a:first").tab("show"); // Select first tab
                $("#dima-tab-nav a").click(function(e) {
                    e.preventDefault();
                    $(this).tab("show");
                });

                $(".dima-tabs.responsive-tab").responsiveTabs();
            };
            var notification = function() {
                $(".dima-alert button.close").click(function() {
                    $(this).parent().fadeOut(200, "easeOutExpo");
                });
            };
            var element_bg = function() {
                var elm = $("[data-element-bg]");
                var b = elm.attr("data-element-bg");
                elm.css({
                    "background-image": "url(" + b + ")",
                    "background-position": "100% 100%",
                    "background-repeat": "no-repeat",
                });
            };

            var init = function() {
                flatShadow();
                countUp();
                progress();
                tabs();
                notification();
                element_bg();
            }
            var build = {
                init: init,
            };
            return build;
        }();

        PIXELDIMA.EVENT = function() {
            var event = function() {
                $('input, textarea').placeholder();
                //Fix The Navbar 
                if (windowWidth > 960) {
                    if ($('.dima-navbar').hasClass('fix-one')) {
                        $(".fix-one").fix_navbar();
                    }
                    if ($('.dima-navbar').hasClass('fix-two')) {
                        $(".fix-two").show_navbar();
                    }
                }
                if (windowWidth < 980) {
                    $('.dima-nav-end ul').css("top", "-1000px");
                } else {
                    $('.dima-nav-end ul').css("top", "100px");
                }
                //Set parent height
                if (windowWidth > 767) {
                    $('.set-parent-height').each(function() {
                        var parentHeight = $(this).parent().height();
                        $(this).css("height", parentHeight);
                    });
                }
                //Add class active Based on URL http://css-tricks.com/snippets/jquery/add-active-navigation-class-based-on-url/
                $('.sidebar li a[href^="' + location.pathname.split("/")[2] + '"]').parent().addClass('active');


                $('.section-colored').each(function() {
                    var bg = $(this).attr("data-bg");
                    $(this).css("background-color", bg);
                });

            };
            var init = function() {
                event();
            }
            var build = {
                init: init,
            };
            return build;
        }();

        // handle the layout reinitialization on window resize
        PIXELDIMA.DOCONRESIZE = function() {

            var nav = function() {
                windowWidth = w.width();
                if ($('.dima-navbar').hasClass('fix-one')) {
                    $(".fix-one").fix_navbar();
                }
                if ($('.dima-navbar').hasClass('fix-two')) {
                    $(".fix-two").show_navbar();
                }

                if (windowWidth < 980) {
                    $('.dima-nav-end ul').css("top", "-1000px");
                } else {
                    $('.dima-nav-end ul').css("top", "100px");
                }
            };

            var responsiveFlexSlider = function() {
                var x = $(".flexslider .slides").outerHeight();
                if (x < windowHeight) {
                    $('.flexslider').height(x);
                } else {
                    $('.flexslider').height(windowHeight);
                }
            };
            var parentSize = function() {
                if (windowWidth > 767) {
                    $('.set-parent-height').each(function() {
                        var parentHeight = $(this).parent().height();
                        $(this).css("height", parentHeight);
                    });
                }
            };

            var init = function() {
                nav();
                parentSize();
                PIXELDIMA.ANIMATION.twoLinesHover();
                responsiveFlexSlider();
            };
            var READY = {
                init: init,
            };
            return READY;
        }();
        // Handles portfolio AJAX and filter using jQuery isotope
        PIXELDIMA.PORTFOLIO = function() {
            //AJAX
            var openAjax = function() {
                $(".ajax-portfolio .isotope-item a[data-load]").click(function(d) {
                    var id = $(this).parents(".isotope-item").attr("id");
                    var url = $(this).attr("href");
                    d.preventDefault()
                    if ($(".portfolio-ajax-expanded").is(":visible")) {
                        closeAjax();
                        setTimeout(function() {
                            loadAjax(url, id);
                        }, 700);
                    } else
                        loadAjax(url, id);


                })
            };
            var loadAjax = function(url, id) {
                /*var s = getNextItem(id),
                    n = getPrevItem(id);*/

                $.ajax({
                    url: url,
                    type: "get",
                    cache: false,
                    data: {},
                    beforeSend: function() {
                        $("#ajax-loader").fadeIn();
                    },
                    success: function(data) {
                        $('.portfolio-ajax-expanded').html(data);
                    },
                    complete: function() {
                        PIXELDIMA.SLIDE.flexSlider();
                        PIXELDIMA.LIGHTBOX.init();
                        initAjax(id);
                        openItem();
                    }
                });
            }
            var closeAjax = function() {
                $(".portfolio-ajax-expanded")
                    .find(".portfolio-ajax-content").slideUp(600, function() {
                        $(".portfolio-ajax-expanded").css({
                            display: "none"
                        });
                    });
            };
            var openItem = function() {
                $("#ajax-loader").fadeOut();
                setTimeout(function() {

                    $.when($(".portfolio-ajax-expanded").slideDown(900, "easeOutQuad"))
                        .then(
                            $("html,body").stop(!0).animate({
                                scrollTop: $(".portfolio-ajax-expanded").offset().top - 150
                            }, 900, "easeOutQuad")
                        );
                }, 400);
            };
            var initAjax = function() {

                $("#next-portfolio, #prev-portfolio").click(function(d) {
                    var id = $(this).attr("data-id");
                    var url = $("#" + id).find("a[data-load]").attr("href");
                    closeAjax();
                    setTimeout(function() {
                        loadAjax(url, id);
                    }, 700);
                    d.preventDefault()
                });

                $(".close-ajax-portfolio").click(function(d) {
                    $(".portfolio-ajax-expanded")
                        .find(".portfolio-ajax-content").slideUp(600, function() {
                            $(".portfolio-ajax-expanded").css({
                                display: "none"
                            });
                        });
                    d.preventDefault();
                })
            };
            //!AJAX


            var filterIsotop = function() {

                var $container = $('.isotope')
                var $containers = $('#infinite');

                $container.imagesLoaded(function() {
                    $container.isotope({
                        filter: '*',
                        itemSelector: '.isotope-item',
                        layoutMode: "masonry",
                        transitionDuration: '0.8s'
                    })
                });

                $('.filters a').click(function() {
                    var li_p = $(this).parent()
                    $('.filters  .current').removeClass('current')
                    $(li_p).addClass('current')
                        //for columns protfolio without margin
                    var selector = $(this).attr('data-filter')
                    $container.isotope({
                        filter: selector,
                    })
                    return false
                });

                // Infinite Scroll
                $containers.infinitescroll({
                        navSelector: '#page_nav', // selector for the paged navigation 
                        nextSelector: '#page_nav a', // selector for the NEXT link (to page 2)
                        itemSelector: '.isotope-item',
                        bufferPx: 200,
                        loading: {
                            finishedMsg: 'We\'re done here.',
                            msgText: "<em>Loading the next set of posts...</em>",

                        }
                    },
                    // Infinite Scroll Callback
                    function(newElements) {
                        PIXELDIMA.LIGHTBOX.lightBox();
                        PIXELDIMA.ANIMATION.twoLinesHover();
                        PIXELDIMA.SLIDE.init();
                        var $newElems = jQuery(newElements).hide();
                        $newElems.imagesLoaded(function() {
                            $newElems.fadeIn();
                            $container.isotope('appended', $newElems);
                        });
                    });

            };


            var init = function() {
                openAjax();
                filterIsotop();

            };
            var READY = {
                init: init,
                filterIsotop: filterIsotop,
            };

            return READY;
        }();
        // Handles Ajax Contact validation  
        PIXELDIMA.CONTACT = function() {
            var contect = function() {
                var contact = $("#contact"),
                    url = contact.attr("action");
                contact.validate({
                    //Callback when the form is valid.
                    submitHandler: function() {
                        $("#contact").addClass('loading-form')

                        // Ajax Submit
                        $.ajax({
                            type: "POST",
                            url: url,
                            datatype: "json",
                            data: {
                                "name": $("#contact #name").val(),
                                "email": $("#contact #email").val(),
                                "subject": $("#contact #subject").val(),
                                "message": $("#contact #message").val()
                            },
                            success: function(data) {
                                if (data === "success") {
                                    $("#contactSuccess").removeClass("hide").addClass("show animated bounceIn");
                                    $("#contactError").addClass("hide").removeClass("show animated bounceIn");
                                } else {
                                    $("#contactError").removeClass("hide").addClass("show animated bounceIn");
                                    $("#contactSuccess").addClass("hide").removeClass("show animated bounceIn");
                                }
                            },

                            complete: function() {

                                $("#contact").removeClass('loading-form');
                                // Reset Form                            
                                $("#contact")
                                    .find('.field')
                                    .removeClass("success")
                                    .removeClass("error")
                                    .find("input")
                                    .val("");
                            }
                        });
                    },

                    rules: {
                        name: {
                            required: true
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        subject: {
                            required: true
                        },
                        message: {
                            required: true
                        }
                    },

                    //error input 
                    highlight: function(element) {
                        $(element)
                            .parent()
                            .removeClass("success")
                            .addClass("error");
                    },

                    //success input 
                    success: function(element) {
                        $(element)
                            .parent()
                            .removeClass("error")
                            .addClass("success")
                            .find("label.error")
                            .remove();
                    },
                });
            };

            var newsletter = function() {
                var newsletter = $("#newsletter-form");

                function mailchimpCallback(response) {
                    if (response.result === 'success') {
                        $("#newsletteSuccess").removeClass("hide").addClass("show animated bounceIn");
                        $("#newsletteError").addClass("hide").removeClass("show animated bounceIn");

                    } else if (response.result === 'error') {
                        $("#newsletteError").removeClass("hide").addClass("show animated bounceIn");
                        $("#newsletteSuccess").addClass("hide").removeClass("show animated bounceIn");
                    }

                }
                newsletter.ajaxChimp({
                    callback: mailchimpCallback,
                    url: 'http://pixeldima.us8.list-manage.com/subscribe/post?u=c6d8aa2f0a313299db3b338b8&id=847c491668'
                });

            };
            var init = function() {
                contect();
                newsletter();
            };
            var build = {
                init: init,
            };
            return build;
        }();

        // runs callback functions
        PIXELDIMA.OKABREADY = function() {

            var init = function() {
                //Please don't change the order
                PIXELDIMA.SLIDE.init();
                PIXELDIMA.LIGHTBOX.init();
                PIXELDIMA.MEDIA.init();
                PIXELDIMA.ANIMATION.init();
                PIXELDIMA.NAV.init();
                PIXELDIMA.SCROLL.init();
                PIXELDIMA.SHOP.init();
                PIXELDIMA.UI.init();
                PIXELDIMA.API.init();
                PIXELDIMA.CONTACT.init();

                PIXELDIMA.PORTFOLIO.init();
                PIXELDIMA.EVENT.init();
                w.resize(function() {
                    PIXELDIMA.DOCONRESIZE.init();
                });
            };

            var READY = {
                init: init,
            };

            return READY;
        }();
        /**
         * Call Our Setups Functions
         */
        PIXELDIMA.OKABREADY.init();

    });


}($));

(function($) {
    /*
    Plugin Name: scrollToTop for jQuery.
    */
    $.extend({

        scrollToTop: function() {
            var _isScrolling = false;
            // Append Button
            $("body").append($("<a />")
                .addClass("scroll-to-top off")
                .attr({
                    "href": "#",
                    "id": "scrollToTop"
                })
                .append(
                    $("<i />")
                    .addClass("fa  fa-angle-up")
                ));
            $("#scrollToTop").click(function(e) {
                e.preventDefault();
                $("body, html").animate({
                    scrollTop: 0
                }, 500);
                return false;
            });
            // Show/Hide Button on Window Scroll event.
            $(window).scroll(function() {

                if (!_isScrolling) {
                    _isScrolling = true;
                    if ($(window).scrollTop() > 150) {
                        $("#scrollToTop").stop(true, true).removeClass("off");
                        $("#scrollToTop").stop(true, true).addClass("on");
                        _isScrolling = false;
                    } else {
                        $("#scrollToTop").stop(true, true).removeClass("on");
                        $("#scrollToTop").stop(true, true).addClass("off");
                        _isScrolling = false;
                    }
                }
            });
        }
    });
}($));
