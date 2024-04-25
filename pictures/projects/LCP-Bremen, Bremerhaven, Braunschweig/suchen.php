<!DOCTYPE HTML>
	
<html xmlns="http://www.w3.org/1999/xhtml">

	<!-- #BeginTemplate "slider.dwt" -->

	<head>
		<script type="text/javascript" src="js/libraries/jquery-3.1.1.js"></script>
		<script type="text/javascript" src="js/plugins/jssor.slider-22.2.16.debug.js"></script>
		
		
		
		<link href=css/sheets.css rel="stylesheet" type="text/css" media="screen">
		<!-- #BeginEditable "doctitle" -->
		<title>PAGINA INICIALBREMENLink 1 Link</title>
	<!-- #EndEditable -->
	
	<style>

}

/* When the input field gets focus, change its width to 100% */
input[type=text]:focus {
    width: 100%;
}



</style>
	</head>
	
	<body>
<!-- Navigation -->
		<div id="top">
		<ul id="topnav">
			<li><a>Pagina Inicial</a></li>
			<li><a>Bremen</a>
				<ul>
					<li><a>wiki</a></li>
					<li><a>Aulas</a></li>
					<li><a>Contactos</a></li>
				</ul>
			</li>
			<li><a>Bremerhaven</a>
					<ul>
					<li><a>wiki</a></li>
					<li><a>Aulas</a></li>
					<li><a>Contactos</a></li>
				</ul>
			</li>
			<li><a>Braunschweig</a>
				<ul>
					<li><a>wiki</a></li>
					<li><a>Aulas</a></li>
					<li><a>Contactos</a></li>
				</ul>			
			</li>
			<li><a>Informacoes</a></li>
			<li><a>Mais</a></li>
			<li><a>Contactos</a></li>
		</ul>
		</div>
		<!-- Slide Show -->
		<section>
  			<img class="mySlides" src="img/008.jpg"
  				style="width:100%">
  			<img class="mySlides" src="img/006.jpg"
  				style="width:100%">
  			<img class="mySlides" src="img/007.jpg"
  				style="width:100%">
			</section>			
			<!-- #BeginEditable "Content" -->
			
			<div id="content0">
				
					<?php
					
					// Wir deaktivieren Fehlerausgaben. Wenn das Script produktiv eingesetzt wird, soll nicht jeder etwaige Fehler sehen und
					// vielleicht ausnutzen um Schaden anzurichten. Bei Bedarf kann die Zeile auch gelöscht oder mit zwei // am Anfang der Zeile
					// auskommentiert werden. Dies ist besonders local bei der Entwicklung sinnvoll, um Fehler zu finden.
					error_reporting(0);
					
					// Zuerst binden wir die Verbindung zur MySQL-Datenbank ein (db.php, db.inc.php, config.php, config.inc.php etc.)
					// include('config.php'); // Wenn schon eine Konfigurationsdatei vorhanden ist, kann diese hier includet werden. Bitte dazu die beiden // am Anfang entfernen
					
					// oder so - ganz klassisch. Wenn Ihr eine config-Datei verwendet, diese beiden Zeilen bitte auskommentieren mit // am Anfang oder die Zeilen löschen
					mysql_connect('localhost','username','passwort') or die ('Es konnte keine Verbindung zum MySQL-Server aufgebaut werden');
					mysql_select_db('datenbank-name') or die ('Es konnte keine Verbindung zur Datenbank aufgebaut werden');
					
					// Wie viele Ergebnisse wollen wir pro Seite darstellen? In diesem Fall 25 Treffer pro Seite
					$limit = 25;
					
					// Wir sichern die Suchabfrage ab, escapen die Eingabe und entfernen HTML-Tags
					// mysql_real_escape_string() = escaped die Eingabe
					// strip_tags() = entfernt HTML-Eingaben
					$s = mysql_real_escape_string(strip_tags($_POST['s']));
					
					// Wenn der User nichts eingegeben hat, fangen wir das ab und geben einen Fehler aus
					if($s == '') $s = $_GET['s'];
					if($s == '') {
					echo '<font style="color:#BF0000;">Sie haben keinen Suchbegriff eingegeben</font>';
					
					} else {
					
					// Wenn eine Eingabe erfolgt ist, dann suchen wir danach in der Datenbank
					$p = $_GET['p'];
					if($p == '') $p = 1;
					
					// Hier wird unsere Suchabfrage an die Datenbank übergeben. Tabellenname und Spalte bitte anpassen
					$query = mysql_query("SELECT * FROM `tabellenname` WHERE `spaltenname` LIKE '%$s%' ORDER BY `id` DESC") or die (mysql_error());
					$results = mysql_num_rows($query);
					
					// Wenn keine Treffer gefunden wurden, geben wir wieder eine Fehlermeldung aus
					if($results == 0){
					echo '<font style="color:#BF0000;">Keine Treffer gefunden</font>';
					
					}else{
					
					// Wenn Treffer gefunden wurden, wollen wir wissen wie viele Treffer es sind
					echo '<font style="color:#007F00;"><b>' . $results . '</b> Treffer gefunden</font>';
					
					// Die Ergebnisse richten wir ganz klassisch linksbündig aus
					echo '<br /><br /></div><div align=left><hr noshade size=1 width=100% color=#F0F0F0 />';
					
					$pages = ceil($results/$limit);
					
					// Die Abfrage für die Blätterfunktion (Pagination). Tabellenname und Spalte bitte anpassen
					$result = mysql_query("SELECT * FROM `tabellenname` WHERE `spaltenname` LIKE '%$s%' ORDER BY `id` LIMIT " . ($p-1)*$limit . ",$limit") or die (mysql_error());
					
					while($row = mysql_fetch_object($result)) {
					
					// Hier geben wir nun unsere Treffer aus. Das kann man nach belieben formatieren. Je nachdem was man alles ausgeben will und wie
					echo $row->spaltenname . '<br />';
					}
					echo '</div><div align=center>';
					
					// Ab hier beginnt unsere Blätterfunktion, bzw. ab hier werden uns die Links zur Navigation ausgegeben
					$navigation = '';
					if($p > 1) {
					$navigation .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?p=' . ($p-1) . '&s=' .urlencode($s) . '">&laquo; Zur&uuml;ck</a>&nbsp;';
					}
					for($i = 1 ; $i <= $pages ; $i++) {
					if($i == $p) {
					$navigation .= '<b>'.$i.'</b>';
					}else{
					$navigation .= '&nbsp;<a href="'.$_SERVER['SCRIPT_NAME'].'?p=' . $i . '&s=' .urlencode($s) . '">'.$i.'</a>&nbsp;';
					}
					}
					if($p < $pages) {
					$navigation .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?p=' . ($p+1) . '&s=' .urlencode($s) . '">&nbsp;Weiter &raquo;</a>';
					}
					echo '<br /><br />' . $navigation;
					}
					}
					echo '</div>';
					
					?>				
			</div>
			
				<h2>atualidade</h2>
				<p>news noticias rhfeuidbf efeifh upihsupih cuihsciosch icsh csiocsh ics hci cichiosccscskxc x äx                                                                                                                        .</p>
				
			<!-- #EndEditable -->
			
			<div id="footer">
			
				<p>&copy;Copyright 2017 Nome, Instituto</p>
			</div>
			
			<script>
// Automatic Slideshow - change image every 3 seconds
				var myIndex = 0;
				carousel();
	
				function carousel() {
				    var i;
				    var x = document.getElementsByClassName("mySlides");
				    for (i = 0; i < x.length; i++) {
			       x[i].style.display = "none";
    			}
    			myIndex++;
    				if (myIndex > x.length) {myIndex = 1}
    				x[myIndex-1].style.display = "block";
    				setTimeout(carousel, 8000);
				}
				
/* When the user clicks on the button, toggle between hiding and showing the dropdown content */
		/*		function myFunction() {
    				document.getElementById("myDropdown").classList.toggle("show");
				}

// Close the dropdown menu if the user clicks outside of it
				window.onclick = function(event) {
  					if (!event.target.matches('.dropbtn')) {

    					var dropdowns = document.getElementsByClassName("dropdown-content");
    					var i;
    					for (i = 0; i < dropdowns.length; i++) {
      						var openDropdown = dropdowns[i];
      						if (openDropdown.classList.contains('show')) {
        						openDropdown.classList.remove('show');
      							}
    						}
  						}
					}	*/			
			</script>
	</body>
<!-- #EndTemplate -->
</html>