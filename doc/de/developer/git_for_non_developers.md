### Git für Nicht-Entwickler 

Sie kümmern sich um eine Übersetzung oder tragen zu einem Thema bei, und jedes Mal, wenn Sie eine Pull-Anfrage stellen, müssen Sie mit einem der Entwickler sprechen, bevor Ihre Änderungen eingefügt werden können?
Wahrscheinlich haben Sie noch keine Kurzanleitung gefunden, die erklärt, wie Sie die Dinge auf Ihrer Seite synchronisieren können. Es ist wirklich sehr einfach.
Nachdem Sie einen Fork des Repo erstellt haben (klicken Sie einfach auf „fork“ bei github), müssen Sie Ihre eigene Kopie klonen.
Für das Beispiel nehmen wir an, dass du an einem Thema namens redexample arbeitest (das nicht existiert).

```
git clone https://github.com/username/red.git
```

Wechseln Sie anschließend in das Verzeichnis und fügen Sie einen Upstream hinzu.

```
cd rot
git remote add upstream https://framagit.org/hubzilla/core/
```

Von nun an können Sie Änderungen im Upstream mit dem Befehl

```
git fetch upstream
```

Bevor Ihre Änderungen automatisch zusammengeführt werden können, müssen Sie oft Änderungen im Upstream zusammenführen.

```
git merge upstream/master
```

Sie sollten Upstream-Änderungen immer zusammenführen, bevor Sie sie veröffentlichen, und *Sie müssen* Upstream-Änderungen mit allen Pull-Requests zusammenführen, damit sie automatisch zusammengeführt werden können.
In 99% der Fälle wird das alles gut gehen. Das einzige Mal, dass es nicht klappt, ist, wenn jemand anderes die gleichen Dateien wie Sie bearbeitet hat - und oft nur, wenn sie die gleichen Zeilen der gleichen Dateien bearbeitet haben. In diesem Fall wäre es ein guter Zeitpunkt, um Hilfe zu bitten, bis Sie den Umgang mit Ihren eigenen Konflikten beim Zusammenführen in den Griff bekommen haben.
Dann müssen Sie nur noch Ihre Änderungen hinzufügen 

```
git add view/theme/redexample/
```

Dadurch werden alle Dateien in view/theme/redexample und alle Unterverzeichnisse hinzugefügt. Wenn Ihre speziellen Dateien über den gesamten Code verteilt sind, sollten Sie eine nach der anderen hinzufügen. Versuchen Sie nicht, git add -a auszuführen, da dies alles hinzufügt, einschließlich temporärer Dateien (wir fangen diese meist, aber nicht immer, mit .gitignore ab) und lokaler Änderungen, die Sie haben, aber nicht übertragen wollten.
Wenn Sie alle Dateien, die Sie geändert haben, hinzugefügt haben, müssen Sie sie übertragen. 

```
git commit
```

Daraufhin öffnet sich ein Editor, in dem Sie die vorgenommenen Änderungen beschreiben können. Speichern Sie diese Datei und beenden Sie den Editor.
Zum Schluss pushen Sie die Änderungen auf Ihr eigenes Git

```
git push
```

Und das war's, Ihr Projektarchiv ist auf dem neuesten Stand!
Alles, was Sie jetzt noch tun müssen, ist die Pull-Anfrage zu erstellen. Es gibt zwei Möglichkeiten, dies zu tun.
Der einfache Weg, wenn Sie Github benutzen, ist, einfach auf den grünen Button oben in Ihrer eigenen Kopie des Repositorys zu klicken, eine Beschreibung der Änderungen einzugeben und auf „create pull request“ zu klicken. Das Haupt-Repository, Themes und Addons haben alle ihren Hauptzweig bei Github, so dass diese Methode meistens verwendet werden kann.
Die meisten Leute können hier aufhören.
Einige Projekte in der erweiterten RedMatrix-Ökosphäre haben keine Github-Präsenz, um für diese eine Pull-Anfrage zu stellen, ist es etwas anders - Sie müssen Ihre Pull-Anfrage manuell erstellen. Glücklicherweise ist das nicht viel schwieriger.

```
git request-pull -p <start> <url>
```

Start ist der Name eines Commits, mit dem begonnen werden soll. Dieser muss stromaufwärts existieren. Normalerweise wollen Sie nur master.
URL ist die URL *Ihres* Repos.
Man kann auch `<end>` angeben. Die Voreinstellung ist HEAD.
Beispiel:

```
git request-pull master https://example.com/project
```

Und senden Sie die Ausgabe einfach an den Projektbetreuer.
