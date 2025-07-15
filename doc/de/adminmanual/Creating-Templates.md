### Erstellen von Seitenvorlagen 

Für eine Seitenvorlage zur Verwendung mit Comanche sind zwei Dateien erforderlich - eine PHP-Vorlage und eine CSS-Datei. Die Seitenvorlagen müssen vom Systemadministrator Ihrer Website installiert werden. 
Wählen Sie zunächst einen Namen. In diesem Fall werden wir eine Vorlage erstellen und sie „demo“ nennen.
Sie müssen die Dateien „view/php/demo.php“ und „view/css/demo.css“ erstellen, um die PHP-Vorlage bzw. das CSS zu speichern. 
Um eine bessere Vorstellung von diesem Prozess zu bekommen, schauen wir uns eine bestehende Vorlage an - die „Standard“-Vorlage. Diese wird standardmäßig in der gesamten Anwendung verwendet. 

#### **view/php/default.php** 

```
<!DOCTYPE html >
<html>
<head>
    <title><?php if(x($page,'title')) echo $page['title'] ?></title>
    <script>var baseurl=„<?php echo z_root() ?>“;</script>
    <?php if(x($page,'htmlhead')) echo $page['htmlhead'] ?>
</head>
<body>
    <?php if(x($page,'nav')) echo $page['nav']; ?>
    <aside id=„region_1“><?php if(x($page,'aside')) echo $page['aside']; ?></aside>
    <section id=„region_2“><?php if(x($page,'content')) echo $page['content']; ?>
        <div id=„page-footer“></div>
        <div id=„pause“></div>
    </abschnitt>
<aside id=„region_3“><?php if(x($page,'right_aside')) echo $page['right_aside']; ?></aside>      
<footer><?php if(x($page,'footer')) echo $page['footer']; ?></footer>
</body>
</html>    
```

Hier ist die entsprechende CSS-Datei

#### **view/php/default.css** 

```
aside#region_1 {
    Anzeige: block;
    Breite: 210px;
    Position: absolut;
    oben: 65px;
    links: 0;
    margin-left: 10px;
}

beiseite input[type='text'] {
    Breite: 174px;
}
section {
    Position: absolut;
    top: 65px;
    links: 250px;
    Anzeige: block;
    rechts: 15px;
    padding-bottom: 350px;
}
```

Wenn Sie sich diese Definitionen ansehen, fallen Ihnen vielleicht einige Dinge auf:

- Wir haben kein CSS für die Bereiche „nav“, „right_aside“ oder „footer“ angegeben. In dieser Vorlage werden „nav“ und „footer“ die volle Seitenbreite einnehmen, und wir lassen die Größe und Platzierung dieser Elemente durch das Thema steuern. „right_aside“ wird derzeit nicht verwendet. 
- Es gibt Elemente auf der Seite wie „page-footer“ und „pause“, für die es keinen sichtbaren Inhalt gibt. Dieser Inhalt wird von Javascript-Elementen stammen. 
- Unsere Standardvorlage verwendet eine absolute Positionierung. Modernes Webdesign verwendet häufig „float“-Div-Container, so dass bei der Anzeige auf Geräten mit kleinen Bildschirmen in der Regel keine Bildlaufleisten erforderlich sind. 

Um eine neue Vorlage zu entwerfen, ist es am besten, mit einer bestehenden Vorlage zu beginnen und sie nach Wunsch zu ändern. Genau das werden wir hier tun. 
Die Art und Weise, wie Comanche Inhalte innerhalb einer bestimmten Region bereitstellt, ist die Verwendung eines Region-Tags.

```
[region=aside][widget=profile][/widget][/region]
```

In diesem Beispiel wird ein „Profil“-Widget in der Region „aside“ platziert. In Wirklichkeit wird jedoch der HTML-Code für das Widget in der Code-Variablen **$page['aside']** abgelegt. Unsere Standardseitenvorlage definiert einen Bereich auf der Seite (das CSS positioniert diesen als absolute Seitenleiste) und fügt dann den Inhalt von $page['aside'] ein (falls vorhanden). 
Wenn Sie also eine Vorlage mit einem Bereich namens „foo“ erstellen wollten, würden Sie einen Platz dafür auf der Seite vorsehen, dann den Inhalt von $page['foo'] dort einfügen, wo Sie ihn verwenden wollen, und dann mit Comanche angeben 

```
[region=foo][widget=profile][/widget][/region]
```

und dies würde ein Profil-Widget in der von Ihnen erstellten „foo“-Region platzieren. 
Verwenden Sie die CSS-Datei, um den Bereich an der gewünschten Stelle auf der Seite zu positionieren und optional seine Größe zu steuern.