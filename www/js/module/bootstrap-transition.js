/* ========================================================================
 * Bootstrap: transition.js v3.2.0
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */
+function(e){"use strict";function t(){var e=document.createElement("bootstrap");var t={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var n in t){if(e.style[n]!==undefined){return{end:t[n]}}}return false}e.fn.emulateTransitionEnd=function(t){var n=false;var r=this;e(this).one("bsTransitionEnd",function(){n=true});var i=function(){if(!n)e(r).trigger(e.support.transition.end)};setTimeout(i,t);return this};e(function(){e.support.transition=t();if(!e.support.transition)return;e.event.special.bsTransitionEnd={bindType:e.support.transition.end,delegateType:e.support.transition.end,handle:function(t){if(e(t.target).is(this))return t.handleObj.handler.apply(this,arguments)}}})}(jQuery)