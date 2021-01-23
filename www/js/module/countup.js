function countUp(e,t,n,r,i,s){for(var o=0,u=["webkit","moz","ms"],a=0;a<u.length&&!window.requestAnimationFrame;++a)window.requestAnimationFrame=window[u[a]+"RequestAnimationFrame"],window.cancelAnimationFrame=window[u[a]+"CancelAnimationFrame"]||window[u[a]+"CancelRequestAnimationFrame"];window.requestAnimationFrame||(window.requestAnimationFrame=function(e){var t=(new Date).getTime(),n=Math.max(0,16-(t-o)),r=window.setTimeout(function(){e(t+n)},n);return o=t+n,r}),window.cancelAnimationFrame||(window.cancelAnimationFrame=function(e){clearTimeout(e)}),this.options=s||{useEasing:!0,useGrouping:!0,separator:",",decimal:"."},""==this.options.separator&&(this.options.useGrouping=!1);var f=this;this.d="string"==typeof e?document.getElementById(e):e,this.startVal=Number(t),this.endVal=Number(n),this.countDown=this.startVal>this.endVal?!0:!1,this.startTime=null,this.timestamp=null,this.remaining=null,this.frameVal=this.startVal,this.rAF=null,this.decimals=Math.max(0,r||0),this.dec=Math.pow(10,this.decimals),this.duration=1e3*i||2e3,this.version=function(){return"1.1.2"},this.easeOutExpo=function(e,t,n,r){return 1024*n*(-Math.pow(2,-10*e/r)+1)/1023+t},this.count=function(e){null===f.startTime&&(f.startTime=e),f.timestamp=e;var t=e-f.startTime;if(f.remaining=f.duration-t,f.options.useEasing)if(f.countDown){var n=f.easeOutExpo(t,0,f.startVal-f.endVal,f.duration);f.frameVal=f.startVal-n}else f.frameVal=f.easeOutExpo(t,f.startVal,f.endVal-f.startVal,f.duration);else if(f.countDown){var n=(f.startVal-f.endVal)*(t/f.duration);f.frameVal=f.startVal-n}else f.frameVal=f.startVal+(f.endVal-f.startVal)*(t/f.duration);f.frameVal=Math.round(f.frameVal*f.dec)/f.dec,f.frameVal=f.countDown?f.frameVal<f.endVal?f.endVal:f.frameVal:f.frameVal>f.endVal?f.endVal:f.frameVal,f.d.innerHTML=f.formatNumber(f.frameVal.toFixed(f.decimals)),t<f.duration?f.rAF=requestAnimationFrame(f.count):null!=f.callback&&f.callback()},this.start=function(e){return f.callback=e,isNaN(f.endVal)||isNaN(f.startVal)?(console.log("countUp error: startVal or endVal is not a number"),f.d.innerHTML="--"):f.rAF=requestAnimationFrame(f.count),!1},this.stop=function(){cancelAnimationFrame(f.rAF)},this.reset=function(){f.startTime=null,f.startVal=t,cancelAnimationFrame(f.rAF),f.d.innerHTML=f.formatNumber(f.startVal.toFixed(f.decimals))},this.resume=function(){f.startTime=null,f.duration=f.remaining,f.startVal=f.frameVal,requestAnimationFrame(f.count)},this.formatNumber=function(e){e+="";var t,n,r,i;if(t=e.split("."),n=t[0],r=t.length>1?f.options.decimal+t[1]:"",i=/(\d+)(\d{3})/,f.options.useGrouping)for(;i.test(n);)n=n.replace(i,"$1"+f.options.separator+"$2");return n+r},f.d.innerHTML=f.formatNumber(f.startVal.toFixed(f.decimals))}(function(e){function t(e,t){return e.toFixed(t.decimals)}e.fn.countTo=function(t){t=t||{};return e(this).each(function(){function l(){a+=i;u++;c(a);if(typeof n.onUpdate=="function"){n.onUpdate.call(s,a)}if(u>=r){o.removeData("countTo");clearInterval(f.interval);a=n.to;if(typeof n.onComplete=="function"){n.onComplete.call(s,a)}}}function c(e){var t=n.formatter.call(s,e,n);o.text(t)}var n=e.extend({},e.fn.countTo.defaults,{from:e(this).data("from"),to:e(this).data("to"),speed:e(this).data("speed"),refreshInterval:e(this).data("refresh-interval"),decimals:e(this).data("decimals")},t);var r=Math.ceil(n.speed/n.refreshInterval),i=(n.to-n.from)/r;var s=this,o=e(this),u=0,a=n.from,f=o.data("countTo")||{};o.data("countTo",f);if(f.interval){clearInterval(f.interval)}f.interval=setInterval(l,n.refreshInterval);c(a)})};e.fn.countTo.defaults={from:0,to:0,speed:1e3,refreshInterval:100,decimals:0,formatter:t,onUpdate:null,onComplete:null}})(jQuery)