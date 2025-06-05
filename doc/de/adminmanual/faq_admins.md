### Hubzilla FAQ 

#### Gibt es eine Möglichkeit, das Admin-Konto zu ändern? 

#### Gibt es eine Möglichkeit, mehrere Administratoren zu haben? 

Ja, aber im Moment ist es noch etwas umständlich, da es noch nicht in der Benutzeroberfläche sichtbar ist. Um ein Konto zu einem administrativen Konto zu machen, muss man 4096 zum account_roles-Eintrag in der Kontotabelle der Datenbank hinzufügen. Um administrative Berechtigungen zu entfernen, muss man 4096 von den Kontorollen abziehen.

#### Ich kann mich einloggen, aber es gibt keine Beiträge oder Webseiten 

Höchstwahrscheinlich ist Ihre Artikeltabelle abgestürzt. Führen Sie den MySQL-Befehl 

```
repair table item;
```

#### Das Einloggen funktioniert nicht, sofort nach dem Einloggen wird die Seite neu geladen und ich werde ausgeloggt 

Höchstwahrscheinlich ist Ihre Sitzungstabelle abgestürzt. Führen Sie den MySQL-Befehl 

```
repair table session;
```

aus.

#### Wenn ich das Thema wechsle, werden manchmal Elemente des einen Themas über das andere gelegt 

a) store/[data]/smarty3 ist für den Webserver nicht beschreibbar. Machen Sie es möglich.
b) Sie verwenden Midori, oder bei bestimmten Themes Konqueror im KHTML-Modus. 

#### Mein Netzwerk-Tab wird nicht geladen, es scheint durch ein Foto oder Video verursacht zu werden 

Ihr PHP-Speicherlimit ist zu niedrig. Erhöhen Sie die Größe der memory_limit-Anweisung in Ihrer php.ini 
Entgegen der landläufigen Meinung hat die Anzahl der Nutzer eines Hubs keinen Einfluss auf das benötigte Speicherlimit, vielmehr zählt der Inhalt einer individuellen Matrix. Streams mit vielen Fotos und Videos benötigen mehr Speicher als Streams mit viel Text.

#### Ich habe keine Kommunikation mit jemandem 

Sie lauschen auf Port 443, haben aber kein gültiges SSL-Zertifikat. Dies gilt auch, wenn Ihre Basisurl http lautet.
Lauschen Sie nicht auf Port 443, wenn Sie ihn nicht verwenden können. Es wird dringend empfohlen, dieses Problem durch die Installation eines gültigen SSL-Zertifikats für den Browser zu lösen, anstatt Port 443 zu deaktivieren.

#### Wie kann ich eine Nicht-Git-Installation aktualisieren? 

1. Sichern Sie .htconfig.php
2. Sichern Sie alles in store/
3. Sichern Sie alle benutzerdefinierten Änderungen in mod/site/ und view/site
4. Löschen Sie Ihre bestehende Installation
5. Laden Sie die neue Version hoch.
6. Laden Sie die neue Version der Themes und Addons hoch.
7. Stellen Sie alles wieder her, was Sie zuvor gesichert haben.

#### Was muss ich tun, wenn ich meinen Hub auf einen anderen Server verschiebe? 

1. Erstellen Sie einen Git-Klon auf dem neuen Server. Wiederholen Sie den Vorgang für alle benutzerdefinierten Themes und Addons.
2. Rsync .htconfig.php
3. Rsync alles in store/
4. Rsync alles in mod/site/ und view/site (diese sind nur vorhanden, wenn Sie eigene Module haben)
5. Dump und Wiederherstellung der DB.

#### Wie installiere ich einen bestehenden Hub auf demselben Server neu?  

```
git reset --hard HEAD
```

setzt alle Dateien auf ihre Upstream-Standardwerte zurück. Dabei werden keine lokalen Dateien zurückgesetzt, die nicht auch im Upstream existieren. Wenn Sie z.B. lokale Änderungen an mod/channel.php vorgenommen haben, werden diese zurückgesetzt - nicht aber die Änderungen in mod/site/channel.php.

1. Wenn Sie unbedingt eine Neuinstallation durchführen müssen - z.B. wenn Sie das Betriebssystem aktualisieren müssen - befolgen Sie die Schritte für den Umzug auf einen anderen Server, aber anstatt rsync zu verwenden, erstellen Sie ein Backup und stellen Sie es auf andere Weise wieder her.

Installieren Sie einen Hub nicht mit einer neuen Datenbank und einer neuen .htconfig.php neu, es sei denn, dies ist der allerletzte Ausweg. Das Anlegen eines temporären Accounts und das Anfordern von Hilfe über einen Support-Kanal für nicht-triviale Neuinstallationen ist einer Neuinstallation vorzuziehen.

#### Wie kann ich die Standard-Homepage für abgemeldete Besucher festlegen? 

Verwenden Sie das Addon custom_home, das im Hauptrepository für Addons verfügbar ist.

#### Was bedeuten die verschiedenen Einstellungen für den Verzeichnismodus? 

```
// Konfigurieren Sie, wie wir mit Verzeichnisservern kommunizieren.
// DIRECTORY_MODE_NORMAL = Verzeichnis-Client, wir finden ein Verzeichnis (alle Abfragen Ihrer Mitglieder werden an andere Stellen geleitet)
// DIRECTORY_MODE_SECONDARY = Zwischenspeicherverzeichnis oder Mirror (hält sich synchron mit dem primären Realm [fügt erhebliche Cron-Ausführungszeit hinzu])
// DIRECTORY_MODE_PRIMARY = Hauptverzeichnisserver (dies ist nur sinnvoll, wenn Sie einen eigenen Realm betreiben. einer pro Realm)
// DIRECTORY_MODE_STANDALONE = „Off the grid“ oder private Verzeichnisdienste (nur lokale Site-Mitglieder im Verzeichnis)
```

- Die Voreinstellung ist NORMAL. Damit werden die meisten Verzeichnisdienste auf einen anderen Server verlagert. Der verwendete Server ist der config:system/directory_server. Diese Einstellung KANN durch den Code auf einen der Projektsekundärserver aktualisiert werden, wenn der aktuelle Server nicht erreichbar ist. Sie sollten entweder die Kontrolle über diesen anderen Server haben oder ihm vertrauen, dass er diese Einstellung verwendet.
- SEKUNDÄR. Dies ermöglicht es Ihrer lokalen Site, als Verzeichnisserver zu fungieren, ohne die Abfragen Ihrer Mitglieder einem anderen Server zu überlassen. Es erfordert zusätzliche Verarbeitungszeit während der Cron-Abfrage und wird nicht empfohlen, auf einem gemeinsam genutzten Webhost ausgeführt zu werden.
- PRIMÄR. Dies ermöglicht es Ihnen, ein komplett separates 'Netzwerk' von Verzeichnisservern mit einem eigenen Realm zu betreiben. Standardmäßig befinden sich alle Server im Realm RED_GLOBAL, es sei denn, die Einstellung config:system/directory_realm wird überschrieben. *Verwenden Sie dies nicht, wenn Sie keinen eigenen directory_realm haben.*
- STANDALONE. Dies ist wie primary, außer dass es sich um ein eigenständiges „Netzwerk“ handelt, das mit keinem anderen Server kommuniziert. Verwenden Sie dies, wenn Sie nur einen Server haben und von den Red#Matrix-Verzeichnislisten getrennt sein wollen.