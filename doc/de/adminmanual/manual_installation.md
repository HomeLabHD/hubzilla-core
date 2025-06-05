### Manuelle Installation

#### Entpacken Sie die Hubzilla-Dateien in das Stammverzeichnis Ihres Webserver-Dokumentbereichs 

Wenn Sie den Verzeichnisbaum auf Ihren Webserver kopieren, stellen Sie sicher, dass Sie die versteckten Dateien wie .htaccess mit einbeziehen.

Wenn Sie dazu in der Lage sind, empfehlen wir, das Quellcode-Repository mit git zu klonen, anstatt eine gepackte tar- oder zip-Datei zu verwenden. Dadurch lässt sich die Software viel einfacher aktualisieren. Der Linux-Befehl zum Klonen des Repositorys in ein Verzeichnis „mywebsite“ lautet wie folgt:

```
git clone https://framagit.org/hubzilla/core.git mywebsite
```

und dann können Sie die neuesten Änderungen jederzeit mit:

```
git pull
```

Stellen Sie sicher, dass die Ordner `store/[data]/smarty3` und `store` existieren und vom Webserver beschreibbar sind:

```
mkdir -p „store/[data]/smarty3“
chmod -R 777 store
```

Diese Berechtigung (777) ist sehr gefährlich und wenn Sie über ausreichende Privilegien und Kenntnisse haben, sollten Sie diese Verzeichnisse nur für den Webserver und, falls abweichend, für den Benutzer, der den cron-Job ausführt (siehe unten). In vielen Shared-Hosting-Umgebungen kann dies schwierig sein, ohne ein Trouble-Ticket bei Ihrem Provider zu eröffnen. Die obigen Berechtigungen ermöglichen das Funktionieren der Software, sind aber nicht optimal.

Die folgenden Verzeichnisse müssen ebenfalls vom Webserver beschreibbar sein, damit bestimmte webbasierte Verwaltungstools funktionieren:

- `addon`
- `extend`
- `view/theme`
- `widget`

#### Offizielle Addons 

##### Installation 

Navigieren Sie zu Ihrer Website. Dann sollten Sie das Addon-Repository klonen (separat). Wir geben diesem Repository den Spitznamen 'hzaddons'. Du kannst andere Hubzilla-Addon-Repositories einbinden, indem du ihnen andere Nicknames gibst:

```
cd mywebsite
util/add_addon_repo https://framagit.org/hubzilla/addons.git hzaddons
```

##### Aktualisieren 

Um den Addon-Baum auf dem neuesten Stand zu halten, sollten Sie sich auf der obersten Ebene des Website-Verzeichnisses befinden und einen Update-Befehl für dieses Repository eingeben::

```
cd mywebsite
util/update_addon_repo hzaddons
```

Erstellen Sie durchsuchbare Darstellungen der Online-Dokumentation. Sie können dies jedes Mal tun, wenn die Dokumentation aktualisiert wird:

```
cd mywebsite
util/importdoc
```