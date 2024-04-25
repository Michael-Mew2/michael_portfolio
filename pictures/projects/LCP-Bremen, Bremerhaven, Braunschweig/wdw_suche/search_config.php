<?php

//////// Parameter ///////////////////
error_reporting(0);		// zur Fehlersuche diese Zeile ändern in: error_reporting(E_ALL);

$p1 = $_SERVER['SERVER_NAME'];
$p2 = dirname($_SERVER['PHP_SELF']);


if ($p2 == '/' or $p2 == '\\' or $p2 == '.') $p2 = '';

$pfadinfo = $p1.$p2;        	// URL und Verzeichnis, in dem das Script installiert ist
	                            // wird nur bei der Anzeige des Pfads verwendet!
				        		// kann bei Bedarf geändert werden, z.B. $pfadinfo = "www.yourdomain.de";
								

$installation_path = "wdw_suche"; // nur ändern, wenn die Scriptdateien nicht ins Standardverzeichnis wdw_suche installiert werden.

// Die folgende Anweisung entkommentieren, wenn auf UTF-8 kodierten Seiten Entities für Umlaute verwendet werden:
// $encoding = "utf";


//$query = "/index.php?seite=";	// wenn die durchsuchten Seiten per query string
					// dynamisch in ein template eingefügt werden,
					// diese Variable durch Entfernen des führenden # aktivieren
					// und nach Bedarf anpassen
					// in diesem Beispiel heißt die template Seite index.php
					// und der query string: ?seite=
#$query_endung = "no";           // wenn files mit beliebiger Endung encludet werden sollen,
                                        // aber der query string die Endung nicht enthalten soll,
                                        // diese Variable durch Entfernen des führenden # entkommentieren.
                                        // Beispiel: file to include: beispiel.htm
                                        // Ausgabe: ?seite=beispiel

// für E-Mail Benachrichtigung:
$mail = false;					// true für E-Mail Benachrichtigung, false, falls keine E-Mail Benachrichtigung gewünscht.
$adminaddress = "name@yourdomain.de";	// an diese Adresse geht die E-Mail
$seite = "Meine Suche";				// wird im E-Mail Betreff und Text angegeben
$sender = "Suchscript auf ... <name@yourdomain.de>"; // Absenderangabe für die E-Mail
$reloadlimit = 120; 				// Reloadsprerre für Logfile und E-Mail Benachrichtigung in Sekunden

//////// die folgenden Parameter sind optional ///////////
//////// im Normalfall sollte das Suchscript mit den voreingestellten Werten funktionieren ////////
 
// ab PHP 5.3 empfohlen:
if (phpversion() >= "5.1.0" ) date_default_timezone_set('Europe/Berlin');

// Dateiendungen, die von der Suche EINGESCHLOSSEN werden sollen
// WICHTIG!!! Dateiendungen MÜSSEN durch Pipe (|) getrennt werden
// WICHTIG!!! pdf, doc und xls Dateien werden vom Script nicht verarbeitet; also NICHT eintragen!
$dat_type = "htm|html|shtml|php|php3|php4|php5";

// Vorbelegung des Suchfelds (wird nicht ausgewertet) 
$value = "Suchbegriffe";	


// Mindestlänge für Suchstring
$length = 3; // ACHTUNG! Bei Aenderung auch in der Datei searchhi.js die Zeile if (qsip[1].length < 3 ) continue; anpassen


// welche Verzeichnisse sollen durchsucht werden?
// Verzeichnisse mit ./ beginnen, mit slash (/) abschließen, 
// und mit Komma (,) trennen.
// ACHTUNG: letzter Eintrag ohne Komma!
// Muster:

//	$dirs = array(
//		'./',
//		'./subdir/',
//		'./subdir/subsub/'
//	);

// in der folgenden Voreinstellung wird nur das Stammverzeichnis durchsucht
$dirs = array(
	'./'
);	


// Willst Du automatisch alle Verzeichnisse durchsuchen lassen?
$alledirs = true; 		//  $alledirs = true; ACHTUNG: dies verlangsamt die Suche erheblich! Ansonsten: $alledirs = false;


// und so lassen sich einzelne Verzeichnisse ausschließen:
// Sinn macht das besonders, wenn $alledirs = true gesetzt ist.
// jedes auszuschließende Verzeichnis muß mit komplettem Pfad 
// nach folgendem Muster angegeben werden:

//	$exclude_dirs = array(
//		'./test1/',
//		'./test1/subtest4/',
//		'./scripts/'
//	);

// in der folgenden Voreinstellung wird nur ein Beispielverzeichnis ausgeschlossen:
$exclude_dirs = array(
	'./test/geheim/'
);	


// einzelne Dateien von der Suche ausschließen, Dateien ohne Pfadangabe
// es werden alle Dateien ausgeschlossen, die im Dateinamen einen angegebenen String enthalten. 
// z.B. schließt 'x.php' auch index.php aus.
$exclude_files = array(
	'suchen.php',
	'search',
	'reload.txt'
);


// filename oder title-tag ausgeben?
$filename = false;	//true, wenn immer filename ausgegeben werden soll.
				//false, wenn immer title-tag ausgegeben werden soll.
				//wenn die Datei kein title-tag oder ein leeres title-tag enthält, wird in jedem Fall filename ausgegeben. 

// Soll der Inhalt von Tags duchsucht werden?
// Mit dieser Variable wird gesteuert, ob der Inhalt von Tags im <body> durchsucht werden soll. Tags wie z. B. <a href= "http://meineseite.de">zu mir</a>. 
// Die Suche nach "meineseite" ist positiv, wenn $tags=true;
$tags = false;	//true, wenn ALLE tags durchsucht werden sollen, andernfalls false


// welche Meta-Tags in die Suche einschließen?
$key = false;	//true, wenn das meta tag keywords durchsucht werden soll, andernfalls false 
$desc = false;	//true, wenn das meta tag description durchsucht werden soll, andernfalls false 
$tit = false;	//true, wenn das meta tag title durchsucht werden soll, andernfalls false 

// Erst ab dem ersten Vorkommen dieses Strings werden die Dateien durchsucht.
// damit lassen sich z.B. header, Menüs, include files etc. ausschließen.
// hier bieten sich z.B an: '<h1' oder 'body'
// wenn $limit_start nicht im Dokument vorkommt, wird das Dokument von Anfang an durchsucht!
// ACHTUNG! $limit_start = "<body>" wird NICHT erkannt, wenn im Dokument <body onload ... steht; schließende spitzige Klammer!
// Spitzige Klammern in $limit_start und $limit_end "können" merkwürde Ausgabefehler erzeugen. Also möglichst vermeiden.
$limit_start = 'body';  

// ... bis zu diesem String durchsuchen
// damit lassen sich z.B. footer ausschließen
$limit_end  = "";

// unerwünschte Zeichen oder Strings aus dem Suchbegriff entfernen
// weitere Zeichen hinzufügen: wie üblich, letzte Zeile ohne abschließendes Komma.
$exclude_strings = array(
	'Teststring',
   'Teststring2'
);


/////// Ausgabeparameter ////////
// ca. so viele Buchstaben vor dem Treffer anzeigen:
// "ca.", weil das Script versucht, nur ganze Wörter anzuzeigen
// wenn sowohl $vor als auch $nach auf 0 gesetzt wird, wird KEIN Kontext angezeigt,
// sondern nur der Link zur Datei mit dem Treffer

$vor = 17;

// ca. so viele Buchstaben nach dem Treffer anzeigen:
$nach = 90;


// soll versucht werden, anstelle des Kontext das meta-tag description auszugeben:
$show_desc = true;

// Ziel in neuem Fenster öffnen oder einem bestimmten Frame?
$target = "";	// "", dann öffnet sich der Link im selben Fenster.
			// "blank", dann wird target="_blank" für die Links geschrieben.
			// kann bei Framesets hilfreich sein.
			// "FENSTERNAME", dann wird ein im Frameset definiertes Fenster benutzt. 

// soll die Trefferseite direkt angesprungen werden, wenn nur 1 Suchergebnis:
$jump = false; // falls Sie hier true einstellen, lesen Sie bitte unbedingt vorher http://webdesign.weisshart.de/suchen-faq.php#f34

// Anzahl der Treffer anzeigen:
$treffer = true;      // $treffer = false; einsetzen, wenn Anzahl der Treffer nicht angezeigt werden soll.

// Pfad zum Treffer anzeigen:
$pfad = true;       // false einsetzen, wenn der Pfad zum Treffer nicht angezeigt werden soll.

$show_ext = true;	// true: Suche mit Dateiendung  - false: Suche ohne Dateiendung. ACHTUNG! Muss auf true bleiben, außer in ganz speziellen Fällen 
$prot = "http://";   // Das Protokoll beim Pfad angeben. wenn $prot = "" wird kein Protokoll angezeigt.


// Änderungsdatum anzeigen:
$aend_dat = true;      // false einsetzen, wenn das Änderungsdatum nicht angezeigt werden soll.

// Ausgabe auf bestimmte Anzahl Dateien beschränken:
$anz_dat = 10;       // kann beliebig hoch gesetzt werden.

// Anzeige als nummerierte Liste <ol>?
$num_list = true;          // wenn false, dann Aufzählungsliste <ul>

// Suchtipps anzeigen:
$tipps = true;		// wenn false, dann werden die Suchtipps nicht angezeigt

// Anzahl durchsuchter Dateien zeigen:
$zahlzeig = true;		// wenn false, wird die Zahl durchsuchter Dateien nicht angezeigt.

// Suchdauer anzeigen:
$dauer = true;		// wenn false, dann wird die Suchdauer nicht angezeigt.

// wenn die Anzeige während der Suche flackert, folgende Variable auf true setzen:
$gzip = false;

// Anzahl der max. in der searchlog.txt gespeicherten Suchanfragen
$maxmsg = 100;



// die Hintergrundfarbe fuer den Credit-Link
$bg ="#fffff";

///////////////////////// Meldungen personalisieren /////////////////////
// im Folgenden können die Textmeldungen des Programms personalisiert werden.
// damit ist auch die Verwendung auf fremdsprachigen Seiten möglich.
// ACHTUNG! die HTML TAGS (z.B: <p>) nur ändern, wenn Du weißt, was Du tust.
// wenn Du eine bestimmte Meldung nicht willst dann schreibe z.B.: $suchtip1 = ""; 
// wenn eine Variable (Zeile) gelöscht oder wegkommentiert wird (durch vorangestelltes #),
// dann wird die Standardmeldung verwendet.

$noresult = "<p><br />Es wurden leider keine exakten &Uuml;bereinstimmungen mit dem eingegebenen Suchbegriff gefunden.<br />Vielleicht wollen Sie es mit einem allgemeineren Suchbegriff versuchen.</p>";
$vorschlag = "Oder Sie versuchen es einfach mal mit";
             // Wenn Sie den Vorschlag nicht wollen, dann setzen Sie: $vorschlag="";

$minlength = "<p><br />Geben Sie bitte einen Suchbegriff von mindestens&nbsp;".$length."&nbsp;Buchstaben L&auml;nge ein.</p>";

$suchhinweis = "Sie suchen nach";

# $foundtxt = "Dokumente gefunden";

$foundtxt1 = "Ihr Suchbegriff wurde auf";
$foundtxt2 = "Seite";
$foundtxt3 = "gefunden";
$plural = "n"; // die Endung für die Mehrzahl von $foundtext2, also "Seiten"


$treff = "Treffer";
$filedate = "zuletzt ge&auml;ndert am ";

$angezeigttxt = ".<br />Angezeigt werden <strong>$anz_dat</strong> Ergebnisse pro Seite";
$sorttxt = ", sortiert nach [Anzahl&nbsp;der&nbsp;Treffer&nbsp;pro&nbsp;Dokument]";

$zuruecktxt = "vorherige Treffer";
$weitertxt = "weitere Treffer zeigen";


$suchtip0 = "Suchtipps:";

$suchtip1 = "Die Suche findet alle Seiten, die Ihren Suchbegriff enthalten - auch als Wortbestandteil. Eine Suche nach &raquo;test&laquo; (ohne &raquo;&nbsp;&laquo;) findet also auch &raquo;sp&auml;<em>test</em>e&laquo;.";


$suchtip3 = "Wenn Sie mehrere W&ouml;rter eingeben, werden alle Dokumente gefunden, die <em>alle</em> eingegebenen W&ouml;rter enthalten, egal in welcher Reihenfolge.
<br /><em>Je mehr W&ouml;rter, desto weniger Treffer.</em>";

$suchtip31 = "Wenn Sie den Suchbegriff mit Anf&uuml;hrungszeichen umschlie&szlig;en, wird exakt dieser Suchbegriff gefunden. Sie k&ouml;nnen auf diese Weise nach mehreren W&ouml;rter in exakt der eingegebenen Reihenfolge suchen.<br />Wenn Sie nur ganze W&ouml;rter suchen, f&uuml;gen Sie vor und nach dem Suchbegriff noch zus&auml;tzlich ein Leerzeichen ein. <br /><em>Beispiel: \"&nbsp;test&nbsp;\"</em> ";



// OR Suche. Falls diese Funktion nicht gewuenscht ist, einfach die folgenden Zeilen loeschen oder auskommentieren oder $suchtip3or = "" .

$suchtip3or = "\"or\" oder \"oder\" (ohne Anf&uuml;hrungszeichen) zwischen mehreren Suchbegriffen findet alle Dokumente, die mindestens einen Suchbegriff enthalten (OR-Verkn&uuml;pfung).
<br /><em>Je mehr W&ouml;rter, desto mehr Treffer.</em>";


$suchtip4 = "Falls die Suche nur einen einzigen Treffer ergibt, wird die Trefferseite direkt angesprungen.";
// Hinweis: $suchtip4 NICHT ändern, sondern nur übersetzen, falls zutreffend!

$anzahldat = "Anzahl durchsuchter Dateien: ";
$timetxt = "Suche ausgef&uuml;hrt in ";

$beschreib = "Beschreibung: ";

$dateform = "de";	// hier "en" einsetzen für Datumsformat month/day/year, oder jedes gueltige Datumsformat, z. B. "M Y"
$suchdauer = "Sekunden.";

##########################################
# wenn du dir eine solche personal_inc_search_config.php-Datei auch anlegst,
# und dort nur die von dir geänderten Variablen reinstellst,
# dann kannst du bei zukünftigen updates die Konfigurationsdatei search_config.php (diese Datei) auch überschreiben lassen.
# deine privaten Einstellungen bleiben dann erhalten
# ein Beispiel liegt bei als personal_inc_muster_config_inc.php.
# In diese Datei kopiere alle Variablen, die du anpassen willst, und benenne die Datei dann in personal_inc_search_config.php um.
##########################################

if (file_exists(dirname(__FILE__).'/personal_inc_search_config.php')) {include dirname(__FILE__).'/personal_inc_search_config.php';}


?>