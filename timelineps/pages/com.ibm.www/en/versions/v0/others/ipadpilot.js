if((navigator.userAgent.toLowerCase().indexOf("ipad")>-1||document.location.href.indexOf("userAgent=iPad")>-1)&&document.cookie.indexOf("iPadPilot=false;")==-1){window.goiPadCallback=function(a){return a.audience_segment=="Financial Services"?window.location.href="http://www.ibm.com/ibm/us/en/bhome":false};var query=/[\\?&]query=([^&#]*)/.exec(window.location.search);var script=document.createElement("script");script.src="//www.ibm.com/webmaster/dbip/ip?callback=goiPadCallback"+(query?"&query="+query[1]:"");
document.getElementsByTagName("head")[0].appendChild(script);window.setTimeout(function(){window.goiPadCallback=function(){}},2000)};