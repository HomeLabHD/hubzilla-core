### Webseiten

Mit der App "Webseiten" ist es Ihnen möglich, statische Webseiten in Ihrem Kanal zu erstellen. Webseiten verbleiben auf Ihren Hub und werden nicht föderiert. Sie können aber den Link zur Webseite teilen und es allen Nutzern im Fediverse ermöglichen, Ihre Webseite zu besuchen.

Wenn Sie die App aufrufen, gelangen Sie zur Webseiten-Übersicht. Die Seiten werden unter `<URL-Ihrer-Instanz>/page/<Ihr-Kanalname>/<Seiten-Link-Titel>` zugänglich sein.

![Webseiten 01](/help/de/member/pic/webseiten01.png)

In der linken Seitenleiste befindet sich ein Widget mit den Gestaltungswerkzeugen für "Blöcke", "Menüs", "Layouts" und "Seiten".

Darunter ist ein weiteres Widget vorhanden, mit der Möglichkeit Webseiten zu ex- und importieren.

In der Mitte werden die vorhandenen Webseiten aufgeführt. Sie können diese bearbeiten, teilen und löschen. Außerdem ist dort ein Button vorhanden, um eine neue Webseite zu erstellen: "Erstelle".

Wenn Sie auf diesen Button klicken, öffnet sich der Webseiten-Editor.

![Webseiten 02](/help/de/member/pic/webseiten02.png)

Sie haben nun die Wahl, auf welche Art Sie die Webseite gestalten wollen: mit bbCode, mit HTML, mit Markdown, mit reinem Text oder mit der Comanche-Layout-Sprache.

Sie können außerdem festlegen, welches Layout (sofern Sie eines oder Mehrere mit dem Layout-Gestaltungswerkzeug erstellt haben) für die Darstellung der Webseite verwendet werden soll.

Es folgt das Eingabefeld für den optionalen Seitentitel, sowie (ebenfalls optional) eine Zusammenfassung, sowie (Pflichtfeld) die Seiten-URL.

Darunter befindet sich der Texteditor für den Inhalt der Webseite.

Möchte man lediglich eine ganz einfache Webseite mit Formatierungen und anderen Auszeichnungselementen erstellen, genügt es, diese im Website-Editor mittel reinem Text, HTML, bbCode oder Markdown zu erstellen. Sie erhalten damit eine Webseite ohne besonderes Layout (ohne Seitenleisten, ohne Menüs etc.).

Für anspruchsvollere Webseiten, empfiehlt es sich, dass Sie mit Blöcken, Layouts und Menüs arbeiten.

#### Blöcke

Blöcke können Teile von Webseiten sein. Der grundlegende HTML-Code eines Blocks sieht wie folgt aus

```
    <div>
        Block-Inhalt
    </div>
```

Wenn ein Block den Inhaltstyp text/html hat, kann er auch Menüelemente enthalten. Der Beispielinhalt von

```
    <p>HTML Block-Inhalt</p>
    [menu]menuname[/menu]
```

wird HTML wie dieses erzeugen

```
    <div>
        <p>HTML Block-Inhalt</p>
        <div>
            <ul>
                <li><a href="#">Link 1</a></li>
                <li><a href="#">Link 2</a></li>
                <li><a href="#">Link 3</a></li>
            </ul>
        </div>
    </div>
```

Über das Makro `$content` kann ein Block auch den eigentlichen Inhalt der Webseite enthalten.

Erstellen Sie dazu einen Block nur mit

```
    $content
```

als Inhalt.

Damit ein Block auf der Webseite erscheint, muss er im Seitenlayout innerhalb einer Region definiert werden.

```
    [region=aside]
        [block]blockname[/block]
    [/region]
```

Das Aussehen des Blocks kann im Seitenlayout manipuliert werden.

Es können eigene Klassen zugewiesen werden

```
    [region=aside]
        [block=myclass]blockname[/block]
    [/region]
```

wird folgendes HTML erzeugen

```
    <div class="myclass">
        Block Content
    </div>
```

Über die wrap-Variable kann ein Block von seinem umschließenden `<div></div>`-Tag befreit werden

```
    [region=aside]
        [block][var=wrap]none[/var]blockname[/block]
    [/region]
```

wird dieses HTML erzeugt

```
Blockinhalt
```

Mit dem Block-Editor können Blöcke ebenso einfach erzeugt werden, wie Webseiten.

![Webseiten 03](/help/de/member/pic/webseiten03.png)

#### Menüs

Der Menüeditor dient zum einfachen Erstellen von Navigations-Menüs.

![Webseiten 04](/help/de/member/pic/webseiten04.png)

Dem Menü muss ein eindeutiger Name gegeben werden (mit diesem kann es später in der Webseite und in Blöcken referenziert werden). Die Vergabe des Titels ist optional. Außerdem kann man wählen, ob das Menü für das Hinzufügen von Bookmarks zur Verfügung steht. Mit diesem Feature ist es möglich, als Bookmarks markierte Links aus dem Stream mit einem Klick zum Menü hinzuzufügen.

Klicken Sie auf "Absenden und fortfahren", wird das Menü erstellt.

![Webseiten 05](/help/de/member/pic/webseiten05.png)

Nun öffnet sich der Dialog für das Hinzufügen eines Menüeintrags. Es muss eine Bezeichnung für den Menüeintrag eingegeben werden ("Name des Links") und das Ziel des Links. Dies kann eine URL oder der Name eines anderen Menüs sein (welches dann als Untermenü eingebunden wird).

Über die Eingabe einer Zahl bei "Reihenfolge in der Liste" kann man die Sortierung der Menüeinträge beeinflussen.

Handelt es sich bei der URL um einen externen Link zu einer Quelle auf einem anderen Hub, so kann durch setzen des Schalters "Magic-Auth verwenden, falls verfügbar" dafür gesorgt werden, dass man beim Ziel authentifiziert wird und ggf. eingeschränkte Inhalte verfügbar sind.

Außerdem können Sie festlegen, ob Links in einem neuen Fenster/Tab geöffnet werden sollen.

Mit Klick auf "Absenden und fortfahren" kann man weitere Einträge erstellen. "Absenden und fertigstellen" beendet die Eingabe von Menüeinträgen. Menüs können aber jederzeit nachträglich bearbeitet und ergänzt werden.

#### Layouts

Mit den Layouts kann man den generellen Aufbau von Webseiten festlegen. Die Gestaltung erfolgt mit der Comanche Seitenbeschriebungssprache, einer Variante von bbCode. Man muss einen Namen für das Layout vergeben. Im Textfeld erfolgt dann die Definition des Layouts. Hier können auch die Inhalte der verschiedenen Regionen festgelegt werden.

![Webseiten 06](/help/de/member/pic/webseiten06.png)
