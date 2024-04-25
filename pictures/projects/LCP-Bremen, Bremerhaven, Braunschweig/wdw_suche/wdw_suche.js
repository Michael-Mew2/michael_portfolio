/* http://www.kryogenix.org/code/browser/searchhi/ */
/* Modified  by webdesign.weisshart.de/  Stand 16.02.2015*/


var default_value = "Suchbegriffe"; /* muss identisch sein mit $value aus der search_config.php */

var ref = "";
var expression = 0;
var w = 0;

// http://www.robertnyman.com/2005/11/07/the-ultimate-getelementsbyclassname/
function getElementsByClassName(className, tag, elm){



	var testClass = new RegExp("(^|\\\\s)" + className + "(\\\\s|$)");
	var tag = tag || "*";
	var elm = elm || document;
	var elements = (tag == "*" && elm.all)? elm.all : elm.getElementsByTagName(tag);
	var returnElements = [];
	var current;
	var length = elements.length;

	for(var i=0; i<length; i++){
		current = elements[i];
		if(testClass.test(current.className)){
			returnElements.push(current);
		}
	}
	
	return returnElements;
}


function highlightWord(node,word) {

	
	// Iterate into this nodes childNodes
	if (node && node.hasChildNodes) {
		var hi_cn;
		for (hi_cn=0;hi_cn<node.childNodes.length;hi_cn++) {
			highlightWord(node.childNodes[hi_cn],word);
		}
	}


	// And do this node itself
	if (node && node.nodeType == 3) { // text node

		tempNodeVal = node.nodeValue.toLowerCase();
		tempWordVal = word.toLowerCase();

		if (tempNodeVal.indexOf(tempWordVal) != -1) {

			pn = node.parentNode;
			klasse = "searchword"+w; // different colors for differnt searchterms
			
			if (pn.className != klasse) {
				// word has not already been highlighted!
				nv = node.nodeValue;
				ni = tempNodeVal.indexOf(tempWordVal);

				// Create a load of replacement nodes
				before = document.createTextNode(nv.substr(0,ni));
				docWordVal = nv.substr(ni,word.length);
				after = document.createTextNode(nv.substr(ni+word.length));
				hiwordtext = document.createTextNode(docWordVal);
				hiword = document.createElement("em"); // modified from span to em
       			hiword.className = klasse;
				hiword.appendChild(hiwordtext);
				pn.insertBefore(before,node);
				pn.insertBefore(hiword,node);
				pn.insertBefore(after,node);
				pn.removeChild(node);
			}
		}
	}
}



function Highlight() {

	if (window.location.search.indexOf("nohighlight") !== -1 ) return;
//	if (window.location.search.indexOf("nohighlight") !== -1 || (window.location.href.indexOf('#wdw_suche') == -1 && window.location.href.indexOf('suchen.php') == -1 )) return;

	if (!document.createElement) return;

	if (document.referrer.search(/google.+/) != -1 || document.referrer.search(/lycos.+/) != -1 || document.referrer.search(/yahoo.+/) != -1 || document.referrer.search(/fireball.+/) != -1 ||document.referrer.search(/search\.msn.+/) != -1 || document.referrer.search(/bing.+/) != -1) {
		ref = decodeURIComponent(document.referrer);
		if (ref.indexOf('?') == -1) return;
	}

	// for site search:
	// if your search file is not called suchen.*, you must modify the following line 2x! 
/*	if (document.referrer.search(/suchen.+/) != -1 && document.URL.indexOf("suchen.php") == -1 ) {

//		try { ref = decodeURIComponent(document.referrer); }
//		catch (e) { ref = unescape(document.referrer); }
		if (window.location.search) { 
			try { ref = decodeURIComponent(window.location.search); }
			catch (e) { ref = unescape(window.location.search); }
		}
	}
*/
	if (window.location.search) { 
		try { ref = decodeURIComponent(window.location.search); }
		catch (e) { ref = unescape(window.location.search); }
	}
	
	
	qs = ref.substr(ref.indexOf('?')+1);

	qsa = qs.split('&');


	for (i=0;i<qsa.length;i++) {
		qsip = qsa[i].split('=');
	        if (qsip.length == 1 || qsip.length == 5) continue; // was faengt das ab?
        	if (qsip[0] == 'q' || qsip[0] == 'query' ||qsip[0] == 'p' || qsip[0] == 's' ) { // q= for Google, p= for Yahoo, query= Fireball, Lycos etc., s= for wordpress

			var re = new RegExp(default_value, "g");
			qsip[1] = qsip[1].replace(re,'');

			if (qsip[1].indexOf('"') != -1 ) expression = 1; // Wortgruppen in einer Farbe highlighten

			qsip[1] = qsip[1].replace(/"/gi,'');
			if (qsip[1].length < 3 ) continue;

            if (document.URL.indexOf('blog') >= 1) qsip[1] = qsip[1].replace(/\d\d|\d/g,''); // 1/2-digit number in blogs

			// remove all blanks and '+' before and after searchterm (bugfix: crashes FF & Op) - obsolete?
			// qsip[1] = qsip[1].replace(/^(\s+|\++)/,'').replace(/(\++)$/,'').replace(/(\s+)$/,'');



			if (qsip[1] != '') {

				if (expression == 1) {
					words = unescape(qsip[1].replace(/\+/g,' '));
					
					highlightWord(document.getElementsByTagName("body")[0],words);

                	// alternativ: Tim Reeves: nur div highlighten:
                	// highlightWord(document.getElementById('inhalt'),words);

                	// alternativ: http://www.robertnyman.com/2005/11/07/the-ultimate-getelementsbyclassname/
					// nur Class highlighten:
			    	//highlightWord(getElementsByClassName("test2", "*", document)[0],words);
					
				} else {
					words = unescape(qsip[1].replace(/\+/g,' ')).split(/\s+/);
					

									
					for (w=0;w<words.length;w++) {
						if (words[w].length >= 1) {
							if (words[w] != 'or' && words[w] != 'OR' && words[w] != 'oder' ) {
													
						    	highlightWord(document.getElementsByTagName("body")[0],words[w]);

	                        	// alternativ: Tim Reeves: nur div highlighten:
	                        	// highlightWord(document.getElementById('inhalt'),words[w]);

	                        	// alternativ: http://www.robertnyman.com/2005/11/07/the-ultimate-getelementsbyclassname/
								// nur Class highlighten:
						    	// highlightWord(getElementsByClassName("test2", "*", document)[0],words[w]);
							}
						}
					}
                }
			}
	 	}
	}
}

// war searchterm.js:
function searchzeig() {
	var append, zusatz, searchterma, searchterm;
	var st_content = "";
	var ref = "";
	if (window.location.search) {
		ref = window.location.search;
	}


	// die Umlaute in der URL umwandeln fuer Javascript (case insensitive)
	ref = ref.replace(/%25FC/g, '\u00FC');
	ref = ref.replace(/%25F6/g, '\u00F6');
	ref = ref.replace(/%25E4/g, '\u00E4');
	ref = ref.replace(/%25DF/g, '\u00DF');
	ref = ref.replace(/%2520/g, '\u0020');
	ref = ref.replace(/%2522/g, '\u0022');


	append = ref;
	if (document.referrer.search(/google.+/) != -1 || document.referrer.search(/suche.t-online.de.+/) != -1 || document.referrer.search(/lycos.+/) != -1 || document.referrer.search(/fireball.+/) != -1 || document.referrer.search(/yahoo.+/) != -1 || document.referrer.search(/search\.msn.+/) != -1 || document.referrer.search(/bing.+/) != -1) {
		ref = decodeURIComponent(document.referrer).replace(/\+/g, ' ');
	}
	if (document.referrer.search(/suchen.php.+/) != -1) {
		ref = unescape(document.referrer).replace(/\+/g, ' ');
	}

//	alert (ref);

	zusatz = "?x=";
	if (append.indexOf("?") != -1) {
		zusatz = "&amp;q=";
	}
	searchterma = ref.split('q=');
	if (!searchterma[1]) {
		searchterma = ref.split('s=');
	}
	if (!searchterma[1]) {
		searchterma = ref.split('p=');
	}
	if (!searchterma[1]) {
		searchterma = ref.split('query=');
	}


	if (searchterma[1]) {
		searchterm = searchterma[1].split('&');
		searchterm[0] = unescape(searchterm[0]);
		searchterm[0] = searchterm[0].replace(/</g, '<');
		searchterm[0] = searchterm[0].replace(/>/g, '>');

		//if (window.location.search.indexOf("nohighlight") == -1) {
		if (window.location.search.indexOf("nohighlight") == -1 && window.location.href.indexOf('?q=') !== -1) {
			if (searchterm[0] !== '' && searchterm[0] !== default_value) {
				//st_content = "<em><a class=\"noverweis\" href=\"" + window.location + zusatz + "nohighlight\">Highlighting der Suchbegriffe entfernen&nbsp;[X]</a></em><br />";
				st_content = "<em><a class=\"noverweis\" href=\"" + window.location.pathname +  "\">Highlighting der Suchbegriffe entfernen&nbsp;[X]</a></em><br />";
				//st_content = st_content.replace ('?q=', '');
			}
			if (document.getElementById("searchterm")) {
				document.getElementById("searchterm").innerHTML = st_content;
			}
		}
	}
}



/* Der Throbber */
function noshowWait() {
	if (document.getElementById('wait')){
		document.getElementById('wait').style.visibility='hidden';
		return true;
	}
}
function showWait() {
	if (document.getElementById('wait')){
		document.getElementById('wait').style.visibility='visible';
		return true;
	}
}

/* Klappen */
var i, j, browser, elem;
var zeigen = '<span aria-relevant="all">[+] anzeigen<\/span>';
var aus  = '<span aria-relevant="all">[-] verbergen<\/span>';


/* http://www.dustindiaz.com/getelementsbyclass/ */
function getElementsByClass(searchClass,node,tag) {
   var classElements = [];
   if ( node === null ) {node = document;}
   if ( tag === null ) {tag = '*';}
   var els = node.getElementsByTagName(tag);
   var elsLen = els.length;
   var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
   for (i = 0, j = 0; i < elsLen; i++) {
      if ( pattern.test(els[i].className) ) {
         classElements[j] = els[i];
         j++;
      }
   }
   return classElements;
}

function klapp(nr) {
   if (document.getElementById('kl'+nr)) {
      if ((this['show'+nr]) !== false) {
         document.getElementById('klapp'+nr).innerHTML = zeigen;
         (this['show'+nr]) = false;
         document.getElementById('kl'+nr).style.display = 'none';
      
      } else if ((this['show'+nr]) === false) {
         document.getElementById('klapp'+nr).innerHTML = aus;
         (this['show'+nr]) = true;
         document.getElementById('kl'+nr).style.display = 'block';
      }
   }
}

// Separation of functionality - onclicks schreiben
function on_click(mr){ 
   var links = document.getElementById('klapp'+mr);
   links.onclick = function() {
      klapp(mr); 
      return false;
   };
}


function init_klapp(){
	if ((navigator.userAgent.indexOf("MSIE 6") !== -1) || (navigator.userAgent.indexOf("MSIE 7") !== -1) || (navigator.userAgent.indexOf("MSIE 8") !== -1)) {
      browser = "ms";
   } else {
      browser = "gut";
   } 

   var e2 = getElementsByClass('klappen',document,'*');
   for (var m = 0; m < e2.length; m++) {
   
      // jedem Node eine id zuweisen, damit er angesprochen werden kann:
      var att = document.createAttribute("id");
      att.nodeValue = "kl"+m;
      e2[m].setAttributeNode(att);

      if (e2[m].nodeName == "UL" || e2[m].nodeName == "OL" &&
         e2[m].parentNode.nodeName == "LI") {
         // der typische Anwendungsfall: verschachtelte Liste:
         elem = e2[m].parentNode;
      } else if (browser == "ms") {
         // zwei aufeinanderfolgende Blockelemente:
         elem = e2[m].previousSibling;
      } else if (browser == "gut") {
         elem = e2[m].previousSibling.previousSibling;
      }
           
      // ein a hinzufügen, (das später mit +/- belegt wird):
      var neuS = document.createElement("a");
      var neuSpanText = document.createTextNode("");
      neuS.appendChild(neuSpanText);
      elem.insertBefore(neuS, elem.firstChild.nextSibling);
      
      // dem eben erzeugten a eine id zuweisen:
      var att2 = document.createAttribute("id");
      att2.nodeValue = "klapp"+m;
      elem.firstChild.nextSibling.setAttributeNode(att2);
      
      // und jetzt noch ein href Attribut:
      var att4 = document.createAttribute("href");
      //      att4.nodeValue = "#"; ist void(0) besser?
      att4.nodeValue = "javascript:void(0);";
      elem.firstChild.nextSibling.setAttributeNode(att4);

      // dem Parent bzw. previous aria live Attribut hinzufügen
      elem.setAttribute('aria-live', 'polite'); 

      // erst mal alles zuklappen:
	  if (document.getElementById('klapp'+m)) {
		document.getElementById('klapp'+m).blur();
		document.getElementById('klapp'+m).innerHTML = zeigen;
      	(this['show'+m]) = false;
      	document.getElementById('kl'+m).style.display = 'none';
		on_click(m);
	  }
   }
}

function addHandler() {
if (document.getElementById('q')) {
	
	document.getElementById('q').value=default_value;	
document.getElementById('q').setAttribute('onblur', 'if(this.value=="")this.value=default_value');
document.getElementById('q').setAttribute('onfocus', 'if(this.value==default_value)this.value=""');
}
if (document.getElementById('s')) {
document.getElementById('s').setAttribute('onblur', 'if(this.value=="")this.value="Blogsuche"');
document.getElementById('s').setAttribute('onfocus', 'if(this.value=="Blogsuche")this.value=""');
}
}

// http://ichwill.net/chapter4.html

function addEvent(obj, evType, fn){
 if (obj.addEventListener){
   obj.addEventListener(evType, fn, false);
   return true;
 } else if (obj.attachEvent){
   var r = obj.attachEvent("on"+evType, fn);
   return r;
 } else {
   return false;
 }
}


if (typeof init_klapp === "function") { addEvent(window, 'load', init_klapp); }
if (typeof searchzeig === "function") { addEvent(window, 'load', searchzeig); }
if (typeof Highlight === "function") { addEvent(window, 'load', Highlight); }
if (typeof addHandler === "function") { addEvent(window, 'load', addHandler); }
if (typeof noshowWait === "function") { addEvent(window, 'load', noshowWait); }

