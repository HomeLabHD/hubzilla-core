### Grundlegende Widgets 

Einige/viele dieser Widgets haben Einschränkungen, die die Art der Seite, auf der sie erscheinen können, einschränken oder eine Anmeldung erfordern können

- clock - zeigt die aktuelle Zeit an
  - args: military (1 oder 0) - verwendet 24-Stunden-Zeit anstelle von AM/PM
- profile - zeigt eine Profil-Seitenleiste auf Seiten an, die Profile laden (Seiten mit Nickname in der URL)
- tagcloud - zeigt eine Tagcloud von Webseiten-Elementen an
  - args: count - Anzahl der Elemente, die zurückgegeben werden sollen (Standardwert 24)
- collections - Auswahl der Privatsphärengruppe für den aktuell eingeloggten Kanal
  - args: mode - eine der Optionen „conversation“, „group“, „abook“ je nach Modul
- suggestions - Freundesvorschläge für den aktuell angemeldeten Kanal
- follow - zeigt ein Textfeld zum Folgen eines anderen Channels an
- notes - privater Notizbereich für den aktuell eingeloggten Kanal, wenn die Funktion private_notes aktiviert ist
- savedsearch - Netzwerk-/Matrixsuche mit Speicherung - muss eingeloggt und die savedsearch-Funktion aktiviert sein
- filer - Auswahl abgelegter Elemente aus dem Netzwerk/Matrix-Stream - muss eingeloggt sein
- archive - Datumsbereichswähler für Netzwerk- und Kanalseiten
  - args: 'wall' - 1 oder 0, Begrenzung auf Wall-Posts oder Netzwerk-/Matrix-Posts (Standard)
- fullprofile - wie das aktuelle Profil
- categories - Kategorien-Filter (Channel-Seite)
- tagcloud_wall - Tagcloud nur für Channelseite
  - args: 'limit' - Anzahl der Tags, die zurückgegeben werden sollen (Standardwert 50)
- catcloud_wall - Tagcloud für die Kategorien der Kanalseite
  - args: 'limit' - Anzahl der zurückzugebenden Kategorien (Standardwert 50)
- affinity - Affinitäts-Schieberegler für die Netzwerk-Seite - muss eingeloggt sein
- settings_menu - Seitenleistenmenü für die Einstellungsseite, muss eingeloggt sein
- mailmenu - Seitenleistenmenü für die Seite mit den privaten Nachrichten - muss eingeloggt sein
- design_tools - Menü für Design-Tools für Seiten zur Erstellung von Webseiten, muss eingeloggt sein
- findpeople - Werkzeuge, um andere Kanäle zu finden
- photo_albums - Liste der Fotoalben des aktuellen Seitenbesitzers mit einem Auswahlmenü
- vcard - Mini-Profil-Seitenleiste für die Person von Interesse (Seitenbesitzer, was auch immer)
- dirsafemode - Werkzeug zur Verzeichnisauswahl - nur auf Verzeichnisseiten
- dirsort - Werkzeug zur Verzeichnisauswahl - nur auf Verzeichnisseiten
- dirtags - Verzeichnis-Werkzeug - nur auf Verzeichnisseiten
- menu_preview - Vorschau eines Menüs - nur auf Menübearbeitungsseiten
- chatroom_list - Liste der Chaträume für den Seitenbesitzer
- bookmarkedchats - Liste der Chaträume mit Lesezeichen, die auf dieser Seite für den aktuellen Betrachter gesammelt wurden
- suggestedchats - „interessante“ Chaträume für den aktuellen Betrachter ausgewählt
- item - zeigt ein einzelnes Webpage-Element nach Mitte oder Seitentitel an
  - args:
  - channel_id - Kanal, dem der Inhalt gehört, Standard ist die profile_uid 
  - mid - message_id der anzuzeigenden Webseite (muss eine Webseite sein, kein Konversationselement)
  - title - URL-Seitentitel der Webseite (muss entweder title oder mid enthalten)
- photo - Anzeige eines einzelnen Fotos
  - args: 
  - src - URL des Fotos, muss http oder https sein
  - zrl - zid-authentifizierter Link verwenden
  - style - CSS-Style-String
- cover_photo - zeigt das Titelbild für den ausgewählten Kanal an
  - args:
  - channel_id - zu verwendender Kanal, Standard ist die profile_uid 
  - style - CSS-Style-String (standardmäßig wird die Größe dynamisch an die Breite des Bereichs angepasst)

- photo_rand - zeigt ein zufälliges Foto aus einem Ihrer Fotoalben an. Fotoberechtigungen werden beachtet
  - args: 
  - album - Name des Albums (sehr zu empfehlen, wenn Sie viele Fotos haben)
  - scale - typischerweise 0 (Originalgröße), 1 (1024px), 2, (640px) oder 3 (320px)
  - style - CSS-Style-String
  - channel_id - wenn nicht Ihre eigene
- random_block - zeigt ein zufälliges Blockelement aus Ihrer Sammlung von Webpage Design Tools an. Erlaubnisse werden beachtet.
  - args: 
  - contains - gibt nur Blöcke zurück, die den String contains im Blocknamen enthalten
  - channel_id - wenn nicht Ihre eigene
- tasklist - liefert eine Aufgaben- oder To-Do-Liste für den aktuell eingeloggten Channel.
  - args:
  - all - zeigt erledigte Aufgaben an, wenn all ungleich Null ist.
- forums - liefert eine Liste der verbundenen öffentlichen Foren mit ungesehenen Beiträgen für den aktuell eingeloggten Channel.
- activity - liefert eine Liste der Autoren von ungelesenen Netzwerkinhalten für den aktuell eingeloggten Channel.
- album - stellt ein Widget zur Verfügung, das ein komplettes Fotoalbum aus den Alben des Seitenbesitzers enthält; dies kann zu groß sein, um in einem Seitenleistenbereich dargestellt zu werden, und wird am besten als Widget für einen Inhaltsbereich implementiert. 
  - args:
  - album - Name des Albums
  - title - optionaler Titel, wenn nicht vorhanden wird der Albumname verwendet

#### **Neue Widgets erstellen** 

#### **Klassen-Widgets** 

Um ein klassenbasiertes Widget namens 'slugfish' zu erstellen, erstellen Sie eine Datei mit folgendem Inhalt:

```php
<?php
namespace Zotlabs\Widget;
class Slugfish {
    
	function widget($args) {
    
		... der Widget-Code kommt hier hin.
    	... Die Funktion gibt einen String zurück, der den HTML-Inhalt des Widgets darstellt.
    	... $args ist ein benanntes Array, dem alle [var]-Variablen aus dem Layout-Editor übergeben werden
    	... Zum Beispiel füllt [widget=slugfish][var=count]3[/var][/widget] $args mit
    	... [ 'count' => 3 ]
	}
    
}

?>
```

Die resultierende Datei kann in Zotlabs/Widgets/Slugfish.php abgelegt werden. Sie kann auch von einem Git-Repository mit util/add_widget_repo verlinkt werden.