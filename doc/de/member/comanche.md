### Comanche Seitenbeschreibungssprache

Comanche ist eine bbCode-ähnliche Auszeichnungssprache, mit der aufwendige und komplexe Webseiten erstellt werden können, indem sie aus  einer Reihe von Komponenten zusammengesetzt werden, von denen einige  vorgefertigt sind und andere spontan definiert werden können. Comanche verwendet eine Seitenbeschreibungssprache, um diese Seiten zu erstellen.


Comanche wählt in erster Linie aus, welche Inhalte in den verschiedenen Bereichen der Seite erscheinen sollen. Die verschiedenen Bereiche haben Namen, und diese Namen können sich je nach gewählter Layoutvorlage  ändern.

#### Seitenvorlagen

Derzeit gibt es fünf Layout-Vorlagen, es sei denn, Ihre Website bietet zusätzliche Layouts.


**Standardvorlage**

Die Standardvorlage definiert einen "nav"-Bereich am oberen Rand, "aside" als Seitenleiste mit fester Breite, "content" für den  Hauptinhaltsbereich und "footer" für einen Seitenfuß.


**Vollständig**

Die vollständige Vorlage entspricht der Standardvorlage mit der Ausnahme, dass es keinen "aside"-Bereich gibt.

**Choklet**

Die Choklet-Vorlage bietet eine Reihe von fließenden Layoutstilen, die nach Geschmack festgelegt werden können:

- (default flavour) - ein zweispaltiges Layout, das der "default"-Vorlage ähnelt, aber flexibler ist
- bannertwo - ein zweispaltiges Layout mit einem Bannerbereich, kompatibel mit der "default"-Vorlage auf kleinen Displays
- three - dreispaltiges Layout (ergänzt die Standardvorlage um einen "right_aside"-Bereich)
- edgestwo - zweispaltiges Layout mit festen Seitenrändern
- edgesthree - dreispaltiges Layout mit festen Seitenrändern
- full - dreispaltiges Layout mit festen Seitenrändern und Hinzufügen eines "Header"-Bereichs unter der Navigationsleiste


**Redable**

Eine Vorlage zum Lesen längerer Texte im Vollbildmodus (also ohne Navigationsleiste). Drei Spalten: aside, content und right_aside.

Für maximale Lesbarkeit ist es ratsam, nur die mittlere Inhaltsspalte zu verwenden.


**Zen**

Gibt Ihnen die Freiheit, alles selbst zu machen. Nur eine leere Seite mit einem Inhaltsbereich.


Um eine Layout-Vorlage auszuwählen, verwenden Sie den Tag 'template'.

```
[template]full[/template]
```

So wählen Sie die Vorlage "choklet" mit der Geschmacksrichtung "three":

```
[template=three]choklet[/template]
```


Die Standardvorlage wird verwendet, wenn keine andere Vorlage angegeben  wird. Die Vorlage kann beliebige Namen für die Inhaltsbereiche  verwenden. Sie werden "region"-Tags verwenden, um zu entscheiden, welche Inhalte in den jeweiligen Regionen platziert werden sollen.

Es wurden drei "Makros" für Ihre Verwendung definiert.

```
$htmlhead - replaced with the site head content.
$nav - replaced with the site navigation bar content.
$content - replaced with the main page content.
```

Standardmäßig wird `$nav` in den Seitenbereich "nav" und `$content` in den Bereich "content" eingefügt. Sie brauchen diese Makros nur zu  verwenden, wenn Sie die Reihenfolge der Elemente ändern oder sie in  andere Bereiche verschieben möchten.


Um ein Thema für Ihre Seite auszuwählen, verwenden Sie den Tag "theme".

```
[theme]suckerberg[/theme]
```

Dadurch wird das Thema "suckerberg" ausgewählt. Standardmäßig wird das von Ihrem Kanal bevorzugte Thema verwendet.

```
[theme=passion]suckerberg[/theme]
```

Dadurch wird das Thema mit dem Namen "suckerberg" ausgewählt und das  Schema "passion" (Themenvariante) gewählt. Alternativ ist es auch  möglich, eine komprimierte Themennotation zu verwenden.

```
[theme]suckerberg:passion[/theme]
```

Die komprimierte Notation ist nicht Teil von Comanche selbst, wird  aber von der Hubzilla-Plattform als Themenspezifizierer erkannt.


**Navbar**

```
[navbar]tucson[/navbar]
```

Verwenden Sie die Vorlage "tucson" für die Navigationsleiste und die  CSS-Regeln. Standardmäßig wird die Vorlage "default" für die  Navigationsleiste verwendet.


**Regions**

Jede Region hat, wie oben erwähnt, einen Namen. Sie spezifizieren die Region, die Sie interessiert, mit einem "region"-Tag, der den Namen enthält. Alle Inhalte, die Sie in diesem Bereich platzieren möchten,  sollten zwischen dem öffnenden Region-Tag und dem schließenden Tag  platziert werden.

```
[region=htmlhead]....content goes here....[/region]
[region=aside]....content goes here....[/region]
[region=nav]....content goes here....[/region]
[region=content]....content goes here....[/region]
```


**CSS und Javascript**

Wir haben die Möglichkeit, Javascript- und CSS-Bibliotheken in  den htmlhead-Bereich einzubinden. Zurzeit verwenden wir jquery (js),  bootstrap (css/js) und foundation (css/js).

Dies überschreibt den htmlhead des ausgewählten Themas.

```
[region=htmlhead]
    [css]bootstrap[/css]
    [js]jquery[/js]
    [js]bootstrap[/js]
[/region]
```


**Menüs und Blöcke**

Mit den Werkzeugen zur Erstellung von Webseiten können Sie neben  dem Seiteninhalt auch Menüs und Blöcke erstellen. Diese bieten einen  Teil des vorhandenen Inhalts, der in den von Ihnen festgelegten  Bereichen und in der von Ihnen festgelegten Reihenfolge platziert werden kann. Jedes dieser Elemente hat einen Namen, den Sie bei der Erstellung des Menüs oder Blocks festlegen.

```
[menu]mymenu[/menu]
```

Dadurch wird das Menü "mymenu" an dieser Stelle auf der Seite platziert, die sich innerhalb eines Bereichs befinden muss.

```
[menu=horizontal]mymenu[/menu]
```

Dadurch wird das Menü mit dem Namen "mymenu" an dieser Stelle auf der Seite platziert, die sich innerhalb eines Bereichs befinden muss.  Außerdem wird dem Menü die Klasse "horizontal" zugewiesen. Die Klasse  "horizontal" ist im redbasic-Theme definiert. Sie kann in anderen Themes verfügbar sein, muss es aber nicht.

```
[menu][var=wrap]none[/var]mymenu[/menu]
```

Die Variable `[var=wrap]none[/var]` in einem Block entfernt das umschließende div-Element aus dem Menü.

```
[block]contributors[/block]
```

Dadurch wird ein Block mit dem Namen "contributors" in dieser Region platziert.

```
[block=someclass]contributors[/block]
```

Dadurch wird ein Block mit dem Namen "contributors" in dieser Region  platziert. Zusätzlich wird die Klasse "someclass" auf den Block  angewendet. Dies ersetzt die Standard-Blockklassen "bblock widget".

```
[block][var=wrap]none[/var]contributors[/block]
```

Die Variable `[var=wrap]none[/var]` in einem Block entfernt das umschließende div-Element aus dem Block.


**Widgets**

Widgets sind ausführbare Anwendungen, die vom System  bereitgestellt werden und die Sie auf Ihrer Seite platzieren können.  Einige Widgets benötigen Argumente, mit denen Sie das Widget an Ihren  Zweck anpassen können. System-Widgets sind hier aufgelistet. Widgets  können auch von Plugins, Themes oder Ihrem Website-Administrator  erstellt werden, um zusätzliche Funktionen bereitzustellen.

Widgets und Argumente werden mit den Tags "widget" und "var" angegeben.

```
[widget=recent_visitors][var=count]24[/var][/widget]
```

Damit wird das Widget "recent_visitors" geladen und mit dem Argument "count" auf "24" gesetzt.


**Kommentare**

Das Tag "comment" wird zur Abgrenzung von Kommentaren verwendet.  Diese Kommentare werden auf der gerenderten Seite nicht angezeigt.

```
[comment]This is a comment[/comment]
```


**Bedingte Ausführung**

Sie können ein 'if'-Konstrukt verwenden, um Entscheidungen zu  treffen. Diese basieren derzeit auf der Systemkonfigurationsvariable  oder dem aktuellen Beobachter.

```
[if $config.system.foo]
    ... the configuration variable system.foo evaluates to 'true'.
[else]
    ... the configuration variable system.foo evaluates to 'false'.
[/if]

[if $observer]
    ... this content will only be show to authenticated viewers
[/if]
```

Die 'else'-Klausel ist optional.

Neben der booleschen Auswertung werden mehrere Tests unterstützt.

```
[if $config.system.foo == bar]
    ... the configuration variable system.foo is equal to the string 'bar'
[/if]
[if $config.system.foo != bar]
    ... the configuration variable system.foo is not equal to the string 'bar'
[/if]
[if $config.system.foo {} bar ]
    ... the configuration variable system.foo is a simple array containing a value 'bar'
[/if]
[if $config.system.foo {*} bar]
    ... the configuration variable system.foo is a simple array containing a key named 'bar'
[/if]
```


**Komplexes Beispiel**

```
[comment]use an existing page template which provides a banner region plus 3 columns beneath it[/comment]

[template]3-column-with-header[/template]

[comment]Use the "darknight" theme[/comment]

[theme]darkknight[/theme]

[comment]Use the existing site navigation menu[/comment]

[region=nav]$nav[/region]

[region=side]

    [comment]Use my chosen menu and a couple of widgets[/comment]

    [menu]myfavouritemenu[/menu]

    [widget=recent_visitors]
        [var=count]24[/var]
        [var=names_only]1[/var]
    [/widget]

    [widget=tagcloud][/widget]
    [block]donate[/block]

[/region]



[region=middle]

    [comment]Show the normal page content[/comment]

    $content

[/region]



[region=right]

   [comment]Show my condensed channel "wall" feed and allow interaction if the observer is allowed to interact[/comment]

    [widget]channel[/widget]

[/region]
```