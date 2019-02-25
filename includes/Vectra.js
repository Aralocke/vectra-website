var Spry;
if (!Spry) Spry = {};
if (!Spry.Utils) Spry.Utils = {};
if (!Spry.Widget) Spry.Widget = {};

// * This function will check what browser people are using, and language, platform etc.
Spry.Widget.BrowserSniff = function(){
	var b = navigator.appName.toString();
	var up = navigator.platform.toString();
	var ua = navigator.userAgent.toString();
	var ul = navigator.language ? navigator.language : navigator.userLanguage;

	this.mozilla = this.ie = this.opera = r = false;
	var re_opera = /Opera.([0-9\.]*)/i;
	var re_msie = /MSIE.([0-9\.]*)/i;
	var re_gecko = /gecko/i;
	var re_safari = /safari\/([\d\.]*)/i;
	var re_konqueror = /Konqueror/i;

	if (ua.match(re_opera)){
		r = ua.match(re_opera);
		this.opera = true;
		this.version = parseFloat(r[1]);
	}else if (ua.match(re_msie)){
		r = ua.match(re_msie);
		this.ie = true;
		this.version = parseFloat(r[1]);
    }else if (ua.match(re_safari)){
		this.safari = true;
		this.version = 1.4;
    }else if (ua.match(re_gecko)){
		var re_gecko_version = /rv:\s*([0-9\.]+)/i;
		r = ua.match(re_gecko_version);
		this.mozilla = true;
		this.version = parseFloat(r[1]);
    }else if (ua.match(re_konqueror)){
		this.konqueror = true;
	}
	
	// * Check what platform the are using.
	this.windows = this.mac = this.linux = false;
	this.Platform = ua.match(/windows/i) ? "windows" : (ua.match(/linux/i) ? "linux" : (ua.match(/mac/i) ? "mac" : ua.match(/unix/i)? "unix" : "unknown"));
	this[this.Platform] = true;
	this.v = this.version;
	if (this.safari && this.mac && this.mozilla){
		this.mozilla = false;
	}
	this.Language = ul ? ul.toLowerCase().replace(/-[a-z]+$/, "") : 'en';
};

Spry.Widget.CheckArray = function(a, s ,q){
	if(!q){for (i=0; i<a.length; i++){if (a[i] == s)return i;}return null;}
	else{ for (i=0; i<a.length; i++){if (a[i] == s)return i;}return 'noResult';}
};

// * Change it to something more simple : 
// * Method : Spry.is.IE
Spry.is = new Spry.Widget.BrowserSniff();

// * This function will allow u to swap classes easyer
// * Method : Spry.Utils.classSwitch(element,{from:'Fromclassname',to:'newclassname'});
Spry.Utils.classSwitch = function(ele, options){
	if(options){
		Spry.Utils.removeClassName(ele,options.from);
		Spry.Utils.addClassName(ele,options.to);
	}
};

// * This will create link and script tags on the fly
// * Method : Spry.Widget.TagAdd('name(link / script)',{type:"text/css or text/javascript"}
// * - use src if u add scripts
// * - use link if u add css
// * - set REL if u add css !IMPORTANT
// * - set attribute if u add JavaScript !IMPORTANT
Spry.Widget.TagAdd = function(name,options){
	tag = document.createElement(name);
	if (options){
		if (options.src != null) tag.src = options.src;
		if (options.type != null) tag.type = options.type;
		if (options.rel != null) tag.rel = options.rel;
		if (options.attribute != null) tag.setAttribute = options.attribute;
		if (options.href != null) tag.href = options.href;
	}
	document.getElementById('head').appendChild(tag);
};

/*  -------------------------
 *  SPRY COOKIE; HOW TO USE;
 *  -------------------------
 *  constructor: Spry.Widget.Cookie(type,string,{name:'cookie_name',path:'/',days:'number'};
 *  
 *  TYPE:
 *  - create : this creates *saves* the cookie
 *  		 : Spry.Utils.Cookie('create','string or array',{name:'Spry_Cookie'});
 *  - get	 : this will return the cookie in array format
 *  		 : Spry.Utils.Cookie('get','',{name:'Spry_Cookie'});
 *  - destory: this will destroy the cookie
 *  		 : Spry.Utils.Cookie('destroy','',{name:'Spry_Cookie'});
 *  - add	 : this allows u to add value to the cookie with out creating a whole new string
 *  		   it will place the add value behind the older cookie values, it checks if the value is allready in the cookie,
 *  		   if it is, it will NOT add it in the cookie.
 *  
 *  STRING:
 *  This is the data what u want to store in to the cookie, it can be an Array or a normal string / var
 *  
 *  OPTIONS:
 *  - name:	 : this is the name of the cookie so u can identify it
 *  - path:	 : optional path for the cookie;
 *  - days:  : the amount of days for the cookie to be saved.
 *  - domain : the domain thats set for te cookie
 */
Spry.Widget.Cookie = function(type,string,options){
	if(type == 'create'){
		var expires='';
		if(options.days){
			var date = new Date();
			var UTCString;
			var days = options.days;
			date.setTime(date.getTime()+(days*24*60*60*1000));
			expires = "; expires="+date.toUTCString();
		}
		var thePath = '; path=/';
		if(options.path)thePath = '; path='+options.path;
		var domain = '; domain=/';
		if(options.domain) domain = '; domain='+options.domain;
		document.cookie = options.name+'='+escape(string)+expires+thePath+domain;
	}else if(type == 'get'){
		var nameEQ = options.name + '=';
		var ca = document.cookie.split(';');
		for (var i=0; i<ca.length; i++){
			var c = ca[i];
			while (c.charAt(0)==' '){
			c = c.substring(1,c.length);
		}if (c.indexOf(nameEQ)==0) return unescape(c.substring(nameEQ.length,c.length)).split(",");
		}return null;
	}else if(type == 'destory'){
		Spry.Widget.Cookie('create','',{name: options.name, domain: options.domain});
	}else if(type == 'add'){
		var c = Spry.Widget.Cookie('get','',{name:options.name});
		if (typeof string == 'object') {
			for (i = 0, str; str = string[i], i < string.length; i++) {
				if (Spry.Widget.CheckArray(c, str) == null) c.push(str);
			}
		}else{
			if (Spry.Widget.CheckArray(c, string) == null)c.push(string);
		}
		Spry.Widget.Cookie('create',c,{name:options.name,domain:options.domain});		
	}
};
var gen = false;
var fun = false;
var rs = false;
var chan = false;
var other = false;
var pc = false;
var filterStorrage = new Array();

Spry.Utils.CommandFilter = function(options){
	if(options){
		if(options.administrator){
			e = document.getElementById('cb_adm');
			if(gen == false){
				Spry.Utils.classSwitch(e,{from:'V1',to:'V2'});
				filterStorrage.push('administrator');
				gen = true;
			}else{
				Spry.Utils.classSwitch(e,{from:'V2',to:'V1'});
				var c = Spry.Widget.CheckArray(filterStorrage,'administrator');
				filterStorrage.splice(c,1);
				gen = false;
			}
		}else if(options.nonrunescape){
			e = document.getElementById('cb_nrs');
			if(fun == false){
				Spry.Utils.classSwitch(e,{from:'V1',to:'V2'});
				filterStorrage.push('non-runescape');
				fun = true;
			}else{
				Spry.Utils.classSwitch(e,{from:'V2',to:'V1'});
				var c = Spry.Widget.CheckArray(filterStorrage,'non-runescape');
				filterStorrage.splice(c,1);
				fun = false;
			}
		}else if(options.fun){
			e = document.getElementById('cb_fun');
			if(rs == false){
				Spry.Utils.classSwitch(e,{from:'V1',to:'V2'});
				filterStorrage.push('fun');
				rs = true;
			}else{
				Spry.Utils.classSwitch(e,{from:'V2',to:'V1'});
				var c = Spry.Widget.CheckArray(filterStorrage,'fun');
				filterStorrage.splice(c,1);
				rs = false;
			}
		}else if(options.runescape){
			e = document.getElementById('cb_rs');
			if(chan == false){
				Spry.Utils.classSwitch(e,{from:'V1',to:'V2'});
				filterStorrage.push('runescape');
				chan = true;
			}else{
				Spry.Utils.classSwitch(e,{from:'V2',to:'V1'});
				var c = Spry.Widget.CheckArray(filterStorrage,'runescape');
				filterStorrage.splice(c,1);
				chan = false;
			}
		}else if(options.botrelated){
			e = document.getElementById('cb_brl');
			if(pc == false){
				Spry.Utils.classSwitch(e,{from:'V1',to:'V2'});
				filterStorrage.push('bot-related');
				pc = true;
			}else{
				Spry.Utils.classSwitch(e,{from:'V2',to:'V1'});
				var c = Spry.Widget.CheckArray(filterStorrage,'bot-related');
				filterStorrage.splice(c,1);
				pc = false;
			}
		}else if(options.other){
				e = document.getElementById('cb_oth');
			if(other == false){
				Spry.Utils.classSwitch(e,{from:'V1',to:'V2'});
				filterStorrage.push('other');
				other = true;
			}else{
				Spry.Utils.classSwitch(e,{from:'V2',to:'V1'});
				var c = Spry.Widget.CheckArray(filterStorrage,'other');
				filterStorrage.splice(c,1);
				other = false;
			}
		}
		if(typeof cmdFilter == 'function')dsCommands.removeFilter(cmdFilter,true);
		if(filterStorrage.length > 0){
			cmdFilter = function(ds, row, index){
				var c = row["@id"];
				if(Spry.Widget.CheckArray(filterStorrage,c,true) != 'noResult'){
					return row;
				}else{
					return null
				}
			}//end filter
			dsCommands.addFilter(cmdFilter, true);
		}
	}//end options
};
function getPageScroll(){

	var xScroll, yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
		xScroll = self.pageXOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
		xScroll = document.documentElement.scrollLeft;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
		xScroll = document.body.scrollLeft;	
	}

	arrayPageScroll = new Array(xScroll,yScroll) 
	return arrayPageScroll;
}

// -----------------------------------------------------------------------------------

//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.com
// Edit for Firefox by pHaez
//
function getPageSize(){
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = window.innerWidth + window.scrollMaxX;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	
//	console.log(self.innerWidth);
//	console.log(document.documentElement.clientWidth);

	if (self.innerHeight) {	// all except Explorer
		if(document.documentElement.clientWidth){
			windowWidth = document.documentElement.clientWidth; 
		} else {
			windowWidth = self.innerWidth;
		}
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

//	console.log("xScroll " + xScroll)
//	console.log("windowWidth " + windowWidth)

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = xScroll;		
	} else {
		pageWidth = windowWidth;
	}
//	console.log("pageWidth " + pageWidth)

	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}
function showLightbox(){
	thelightbox.start();
	var objOverlay = document.getElementById('isFloat');
	
	var arrayPageSize = getPageSize();
	
	objOverlay.style.height = (arrayPageSize[1] + 'px');
	objOverlay.style.display = 'block';
	
	objOverlay.style.filter = 'alpha(opacity="80")'; //FOR IE TRANSP.
    objOverlay.style.opacity = '0.8'; //FOR SAF. TRANSP.
    objOverlay.style.MozOpacity = '0.8'; //FOR MOZILLA TRANSP.
	
	objOverlay.style.position = 'absolute';
	objOverlay.style.top = '0';
	objOverlay.style.left = '0';
	objOverlay.style.zIndex = '999';
 	objOverlay.style.width = '100%';	
}
function displayInformation(id){
  if(!Spry.is.ie)showLightbox();
//Set default settings /  size, and place able.
  var pagesize = getPageSize();
  var pagescroll = getPageScroll();	
  var a = document.createElement('a');
  var div = document.getElementById('megaList');
  setTimeout("document.getElementById('megaList').style.display='';",1000);
  div.style.top = (pagescroll[1] + ((pagesize[3] - 35 - parseInt(div.offsetHeight)) / 2) + 'px');
  div.style.left = (((pagesize[0] - 20 - parseInt(div.offsetWidth)) / 2) + 'px');
  closelightboxid = 'megaList';
}
var closelightboxid;
function displayContact(id){
  if(!Spry.is.ie)showLightbox();
	//Set default settings /  size, and place able.
  var pagesize = getPageSize();
  var pagescroll = getPageScroll();	
  var a = document.createElement('a');
  var div = document.getElementById('contactRegion');
  setTimeout(function(){document.getElementById('contactRegion').style.display='';var objOverlay = document.getElementById('isFloat');
	var arrayPageSize = getPageSize();
	objOverlay.style.height = (arrayPageSize[1] + 'px');},1000);
  div.style.left = (((pagesize[0] - 20 - parseInt(div.offsetWidth)) / 2) + 'px');
  closelightboxid = 'contactRegion';
}
function CloseInformation(){
	document.getElementById('isFloat').style.display = "none";
	document.getElementById(closelightboxid).style.display = "none";
}

	// * Currently using static xmls so the testing goes allot faster, on release these will be replaced by the actual data source.
		var dsCommands = new Spry.Data.XMLDataSet('cmds.xml', 'vectra/commandlist/command');
		var dsSMCMD = new Spry.Data.XMLDataSet('cmds.xml', 'vectra/commandlist/command'); //new Dataset because we dont want the filters to work on this.
		var dsNews = new Spry.Data.XMLDataSet('data/V1.php', 'vectra/news');
		var dsVectra = new Spry.Data.PagedView(dsCommands, {pageSize:10});
		var vectraInfo = dsVectra.getPagingInfo();
		Spry.Data.Region.addObserver("dsNewsRegion", {onPostUpdate: function(notifier, data) { 
			var Acc1 = new Spry.Widget.Accordion('Acc1',{useFixedPanelHeights:false}); 
			}
		});
		Spry.Data.Region.addObserver('CommandRegion',{onPostUpdate:function(){
			var tt1 = new Spry.Widget.Tooltip('tooltip','.trigger');
			}
		});
		Spry.Data.Region.addObserver('contactRegion',{onPostUpdate:function(){
            var text1 = new Spry.Widget.ValidationTextField("validName",'none',{validateOn:['blur','change']});
			var text2 = new Spry.Widget.ValidationTextField("validEmail",'email',{validateOn:['blur','change']});
			var text3 = new Spry.Widget.ValidationTextField("validSubject",'none',{validateOn:['blur','change']});
			var text4 = new Spry.Widget.ValidationTextarea("validMessage",{counterType:"chars_count", counterId:"Counttextarea_min_chars",minChars:20,validateOn:['blur','change']});
			}
		});
		var once = true;
		var dsVectras = { onPostLoad: function(notifier, data) 
		{	
			if(once == true){
				loadingFade.start();
				if(Spry.is.ie){
					document.getElementById('datawrapper').className = '';
					document.getElementById('datawrapper').style.display = 'block';
				}else{
					ContentFade.start();
				}
				once = false;
			}
		}
		};
		dsVectra.addObserver(dsVectras);
		dsCommands.setFilterMode("and");
		function mailcheck(){
			var data = Spry.Widget.Cookie('get','',{name:'vectraMail',domain:'www.vectra-bot.org'});
			if(data){
				if(data[0] == 'send'){
					return 0;
				}
			}
			return 1;
		}
			function updateResponseDiv(req) 
			{
				Spry.Utils.setInnerHTML('contactform', req.xhRequest.responseText);
				var data = 'send';
				Spry.Widget.Cookie('create',data,{name:'vectraMail',domain:'www.vectra-bot.org',days:1});
			}
			
			function validateonsubmit(form){
			
				if (Spry.Widget.Form.validate(form) == true){
					Spry.Utils.submitForm(form, updateResponseDiv);	
				}
				return false;
			}





var thelightbox;
var loadingFade;
Spry.Utils.addLoadListener(function(){
  	Spry.Utils.addEventListener("isFloat", "click", function(e) { CloseInformation(); return false;}, false);
	Spry.Utils.addEventListener("closeContact", "click", function(e) { CloseInformation(); return false;}, false);
	thelightbox = new Spry.Effect.Highlight('isFloat',{duration:1000,from:'#FFF',to:'#000',toggle:false});
	loadingFade = new Spry.Effect.Fade('loading', {duration: 2000, from: 100, to: 0,finish:function(){document.getElementById('loading').style.display = 'none'}});
	ContentFade = new Spry.Effect.Fade('datawrapper', {duration: 2000, from: 0, to: 100});
	/*settings = {
	          tl: {radius: 10},
	          tr: {radius: 10},
	          bl: {radius: 10},
	          br: {radius: 10},
	          antiAlias: true,
	          autoPad: true
	      	  };
	 var myBoxObject = new curvyCorners(settings, "rounded");
	 myBoxObject.applyCornersToAll();*/
});