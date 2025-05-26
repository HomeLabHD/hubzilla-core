### Service-Klassen

Mit Hilfe von Dienstklassen können Sie die Systemressourcen begrenzen, indem Sie die Möglichkeiten einzelner Konten einschränken, z. B. die Speicherung von Dateien und die Begrenzung von Posts auf der obersten Ebene. Definieren Sie benutzerdefinierte Serviceklassen entsprechend Ihren Anforderungen in der Datei `.htconfig.php`. Erstellen Sie zum Beispiel eine *Standard-* und eine *Premiumklasse* mit den folgenden Zeilen:

```
// Dienstklassen

App::$config['system']['default_service_class']='standard'; // dies ist die Standard-Serviceklasse, die mit jedem neuen Konto verbunden wird

// Konfiguration für die Standard-Serviceklasse
App::$config['service_class']['standard'] =
array('photo_upload_limit'=>2097152, // Gesamtspeichergrenze für Fotos pro Kanal (hier 2MB)
'total_identities' =>1, // Anzahl der Kanäle, die ein Konto erstellen kann
'total_items' =>0, // Anzahl der Beiträge der obersten Ebene, die ein Kanal erstellen kann. Gilt nur für Top-Level-Posts des Channel-Benutzers, andere Posts und Kommentare sind davon nicht betroffen
'total_pages' =>100, // Anzahl der Seiten, die ein Channel erstellen kann
'total_channels' =>100, // Anzahl der Channels, die der Benutzer hinzufügen kann, andere Benutzer können diesen Channel immer noch hinzufügen, auch wenn das Limit erreicht ist
attach_upload_limit' =>2097152, // Gesamtspeicherplatz für Anhänge pro Kanal (hier 2MB)
'chatters_inroom' =>20);

// Konfiguration für Premium-Dienstklasse
App::$config['service_class']['premium'] =
array('photo_upload_limit'=>20000000000, // Gesamtspeichergrenze für Fotos pro Kanal (hier 20GB)
'total_identities' =>20, // Anzahl der Kanäle, die ein Konto erstellen kann
'total_items' =>20000, // Anzahl der Beiträge der obersten Ebene, die ein Kanal erstellen kann. Gilt nur für Beiträge der obersten Ebene des Kanalbenutzers, andere Beiträge und Kommentare sind davon nicht betroffen
'total_pages' =>400, // Anzahl der Seiten, die ein Channel erstellen kann
'total_channels' =>2000, // Anzahl der Channels, die der Nutzer hinzufügen kann, andere Nutzer können diesen Channel immer noch hinzufügen, auch wenn das Limit erreicht ist
attach_upload_limit' =>20000000000, // Gesamtspeicherplatz für Anhänge pro Kanal (hier 20GB)
'chatters_inroom' =>100);
```

Um eine Serviceklasse auf ein bestehendes Konto anzuwenden, verwenden Sie das Befehlszeilendienstprogramm aus dem Web-Root:

`util/service_class` list service classes

`util/config system default_service_class firstclass` setzt die Standarddienstklasse auf 'firstclass'

`util/service_class firstclass` listet die Dienste auf, die Teil der Dienstklasse „firstclass“ sind

`util/service_class firstclass photo_upload_limit 10000000` setzt die gesamte Fotoplattennutzung der firstclass auf 10 Millionen Bytes

`util/service_class --account=5 firstclass` setzt die Konto-ID 5 auf die Serviceklasse 'firstclass' (mit Bestätigung)

`util/service_class --channel=blogchan firstclass` setzt das Konto, dem der Kanal „blogchan“ gehört, auf die Serviceklasse „firstclass“ (mit Bestätigung)

**Optionen zur Begrenzung der Dienstklasse**

- photo_upload_limit - maximale Gesamtzahl an Bytes für Fotos
- total_items - maximale Gesamtzahl an Beiträgen auf oberster Ebene
- total_pages - maximale Anzahl von Seiten für Comanche
- total_identities - maximale Anzahl von Kanälen im Besitz des Kontos
- total_channels - maximale Anzahl von Verbindungen
- total_feeds - Maximale Anzahl von RSS-Feed-Verbindungen
- attach_upload_limit - maximaler Datei-Upload-Speicherplatz (Bytes)
- minimum_feedcheck_minutes - niedrigste zulässige Einstellung für die Abfrage von RSS-Feeds
- chatrooms - maximale Anzahl von Chaträumen
- chatters_inroom - maximale Anzahl Chatter pro Raum
- access_tokens - maximale Anzahl von Gastzugangstoken pro Kanal