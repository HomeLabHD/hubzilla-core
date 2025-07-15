#### Bilder einfügen

Es gibt verschiedene Möglichkeiten, Bilder in Beiträge und Kommentare einzufügen.

##### Im Beitragseditor

Im Beitragseditor existieren zwei Schaltflächen, die es ermöglichen, Bilder einzufügen: "Bild einbetten" und "Datei anhängen/hochladen".

Mit "Bild einbetten" kann man ein bereits in der Cloud existierendes Bild in den Beitrag einfügen. Das Bild muss also entweder vorhanden sein, oder mal lädt es zu genau diesem Zweck z.B. mit der App "Dateien" hoch.

![bild01](/help/de/member/pic/bild01.png)

Wählt man diese Schaltfläche, öffnet sich eine Übersicht mit den vorhandenen ´Bilddateien und man muss nur das passende Bild auswählen und anklicken. <u>Beachte:</u> Nach der Auswahl des einzufügenden Bildes muss das Auswahlfenster wieder geschlossen werden. Es schließt sich nicht automatisch nach dem Einfügen eines Bildes, um die Möglichkeit zu haben, in einem Durchgang ggf. mehrere Bilder einzufügen. 

Mit dem Auswählen wird das Bild, auf Originalgröße skaliert, als anklickbarer Link zur Quelldatei mittels der bbCode-Tags `[zrl][zmg] [/zmg][/zrl]` am Ende des bisherigen Beitrags (nicht an der aktuellen Textcursor-Position) eingefügt.

![bild03](/help/de/member/pic/bild03.png)

Mit "Datei anhängen/hochladen" kann ein Bild direkt vom eigenen Gerät hochgeladen und am Ende des Beitrags eingebettet.

![bild02](/help/de/member/pic/bild02.png)

Es öffnet sich ein Dateiauswahl-Dialog mit dem man das Bild auf dem eigenen Gerät auswählen und hochladen kann. Es wird in ein ggf. automatisch erstelltes Unterverzeichnis in der Cloud hochgeladen und wieder mit den bbCode-Tags `[zrl][zmg] [/zmg][/zrl]` am Ende des bisherigen Beitrags eingefügt.

<u>Beachte:</u> Bei dieser Methode wird das Bild, sofern kein Fließtext vorhanden ist, nicht auf Originalgröße skaliert, sondern als kleiners Vorschaubild dargestellt. Das führt dazu, dass um das Bild herum ggf. ein Karomuster-Hintergrund zu sehen ist.

![bild04](/help/de/member/pic/bild04.png)

##### Im Kommentareditor

Im Kommentareditor existiert lediglich die Möglichkeit, ein Bild hochzuladen und einzubetten. Die Schaltfläche für das Einfügen eines vorhandenen Bildes existiert dort nicht.

![bild05](/help/de/member/pic/bild05.png)

Möchte man dennoch ein bereits vorhandenes Bild einfügen, muss man das per Hand mit den Tags `[img][/img]` oder `[img=URL][/img]` tun. Dafür muss man die URL des Bildes kennen. Auf diese Weise kann man auch Bilder aus externen Quellen im Web einfügen.

Verwendet man `[img=BREITExHÖHE][/img]`, kann man das Bild skalieren.

##### Alt-Text

Möchte man einen Alternativtext (Alt-Text), welcher angezeigt wird, wenn das Bild nicht dargestellt werden kann, oder - als Popup - wenn man mit dem Mauszeiger über das Bild fährt, so kann man diesen zwischen die beiden Tags `[img=URL]ALT_TEXT[/img]` tun. Hat man das Bild in einen Beitrag nach der erstem Methode ("Bild einbetten"), muss man selbst Hand anlegen. Man muss die Skalierung, die im öffnenden zmg-Tag eingefügt wurde mit der URL des Bildes ersetzen und die automatisch eingefügte Bild-URL zwischen dem öffnenden und dem schließenden zmg-Tag durch den Alt-Text ersetzen.

Beispiel:

Das Bild wurde eingebettet und es wurde damit der Code

`[zrl=https://klacker.org/photos/tutorial01/image/cd747cd9-3f05-42cd-94cc-91c7368c5a18][zmg=520x520]https://klacker.org/photo/cd747cd9-3f05-42cd-94cc-91c7368c5a18-2.png[/zmg][/zrl]`

automatisch erstellt, dann muss er, um z.B. den Alt-Text "Hubzilla-Symbol auf hellgrauem Hintergrund" einzufügen, so geändert werden:

`[zrl=https://klacker.org/photos/tutorial01/image/cd747cd9-3f05-42cd-94cc-91c7368c5a18][zmg=https://klacker.org/photo/cd747cd9-3f05-42cd-94cc-91c7368c5a18-2.png520x520]Hubzilla-Symbol auf hellgrauem Hintergrund[/zmg][/zrl]`

![bild06](/help/de/member/pic/bild06.png)
