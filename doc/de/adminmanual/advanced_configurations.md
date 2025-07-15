### Erweiterte Konfigurationen für Administratoren

*Dieses Dokument setzt voraus, dass Sie ein Administrator sind.*

Hubzilla enthält viele Konfigurationsoptionen, die im Hauptverwaltungsbereich verborgen sind. Im Allgemeinen handelt es sich dabei um Optionen, die als zu nischenartig, fortgeschritten oder verwirrend angesehen werden.

Diese Einstellungen können über die Shell vom Webverzeichnis der obersten Ebene aus mit folgender Syntax geändert werden

`util/config cat key value` 
für eine Website-Konfiguration, oder 

`util/pconfig channel_id cat key value` 
für eine Mitgliedskonfiguration.

Für eine Site-Konfiguration ist eine weitere Möglichkeit, eine Zeile in .htconfig.php einzufügen, mit der Syntax:
`App::$config['cat']['Schlüssel'] = 'Wert';`



#### Mitgliederkonfiguration (pconfig)   

- system.always_my_theme 

  Verwende immer dein eigenes Theme, wenn du Kanäle im selben Hub anschaust.  Dies führt zu einigen recht einfallsreichen Problemen, wenn Kanäle mit themenabhängigen Comanche angezeigt werden.  

- system.blocked 

  Ein Array von Xchans, die von diesem Channel blockiert werden.  Technisch gesehen ist dies eine versteckte Konfiguration und gehört hierher, aber Addons (insbesondere Superblock) haben dies in der Benutzeroberfläche verfügbar gemacht.  

- system.default_cipher 

  Setzt den Standardchiffre, der für E2EE-Elemente verwendet wird.  

- system.display_friend_count 

  Legt die Anzahl der Verbindungen fest, die im Widget des Verbindungsprofils angezeigt werden sollen.  

- system.do_not_track 

  Wie der Browser-Header.  Dies wird viele identitätsbasierte Funktionen unterbrechen.   Sie sollten wirklich nur Berechtigungen setzen, die Sinn machen.  

- system.forcepublicuploads 

  Erzwingt, dass hochgeladene Fotos öffentlich sind, wenn sie als Wandelemente hochgeladen werden.  Es ist viel sinnvoller, die Berechtigungen von vornherein richtig zu setzen.  Tun Sie das stattdessen.  

- system.network_page_default 

  Legt die Standardparameter für die Anzeige der Netzwerkseite fest.  Dies sollte den gleichen Querystring enthalten wie die manuelle Filterung.  

- system.paranoia 

  Legt die Sicherheitsstufe der IP-Überprüfung fest. Wenn sich die IP-Adresse einer angemeldeten Sitzung ändert, wird diese Stufe angewendet, um festzustellen, ob das Konto als Sicherheitsverletzung abgemeldet werden sollte.      Die Optionen sind: 0 - keine IP-Überprüfung              1 - 3 Oktette prüfen              2 - 2 Oktette prüfen              3 - Prüfung auf alle Unterschiede   

- system.prevent_tag_hijacking 

  Verhindert, dass fremde Netzwerke Hashtags in Ihren Beiträgen klauen und sie auf ihre eigenen Ressourcen leiten.  

- system.startpage 

  Eine weitere dieser technisch versteckten Konfigurationen, die von Addons zur Verfügung gestellt werden. Legt die Standardseite fest, die beim Einloggen angezeigt wird.  Diese wird der Benutzeroberfläche durch das startpage-Addon zur Verfügung gestellt.  

- system.taganyone 

  Erfordert, dass die gleichnamige Konfiguration aktiviert ist.  Erlaubt das @mention tagging von jedem, egal ob man verbunden ist oder nicht.  Dies skaliert nicht.  

- system.anonymous_kommentare 

  Standardmäßig oder wenn auf 1 gesetzt, können benutzerdefinierte Berechtigungen gesetzt werden, um anonyme (moderierte) Kommentare wie bei WordPress zu erlauben, die vom Channel-Besitzer moderiert werden. Ist der Wert auf 0 gesetzt, kann kein Mitglied Ihrer Website dies auswählen oder aktivieren.   

- system.user_scalable 

  Bestimmt, ob die App auf Touchscreens skalierbar ist.  Standardmäßig auf on, zum Deaktivieren auf zero setzen - real zero, nicht nur false.   

#### Konfiguration der Website   

- randprofile.check 

  Bei der Anforderung eines Zufallsprofils zuerst prüfen, ob es tatsächlich existiert  

- randprofile.retry 

  Anzahl der Versuche, ein zufälliges Profil zu erhalten  

- system.admin_email 

  Gibt die E-Mail-Adresse des Administrators für diese Site an.  Diese wird bei der Installation festgelegt.  

- system.authlog 

  Logdatei, die für die Protokollierung von Authentifizierungsfehlern verwendet wird.  Wird verwendet, um eine Verbindung zu serverseitiger Software wie fail2ban herzustellen.  Auth-Fehler werden auch in den Hauptprotokollen protokolliert.  

- system.auto_channel_create 

  Fügt die notwendigen Formularelemente hinzu, um den ersten Kanal auf der Kontoregistrierungsseite zu erstellen, und erstellt ihn (möglicherweise nach einer E-Mail-Validierung oder der Genehmigung des Administrators). Dies schließt die Möglichkeit aus, einen Channel von einer anderen Website als ersten auf dieser Website für ein neues Konto erstellten Channel zu importieren.  Verwendung mit system.default_permissions_role zur Rationalisierung der Registrierung.   

- system.auto_follow 

  Der erste Channel eines Accounts folgt automatisch den hier aufgelisteten Channels - kommagetrennte Liste von Webbies (member@hub Adressen).  

- system.blacklisted_sites 

  Ein Array spezifischer Hubs, die von diesem Hub komplett blockiert werden sollen.  

- system.block_public_search 

  Ähnlich wie block_public, mit dem Unterschied, dass nur der öffentliche Zugang zu den Suchfunktionen blockiert wird.  Nützlich für Seiten, die öffentlich sein wollen, aber von Suchmaschinen überrannt werden.  

- system.cron_hour 

  Geben Sie eine Stunde an, in der cron_daily ausgeführt werden soll.  Standardmäßig, ohne Konfiguration, wird dies um Mitternacht UTC ausgeführt.  

- system.default_permissions_role 

  Wenn dieser Wert auf einen gültigen Namen für eine Berechtigungsrolle gesetzt ist, wird diese Rolle für den ersten Channel verwendet, der von einem neuen Konto erstellt wird, und es wird nicht nach dem „Channel-Typ“ im Formular zur Channel-Erstellung gefragt. Beispiele für gültige Namen sind: 'social', 'social_restricted', 'social_private', 'forum', 'forum_restricted' und 'forum_private'.  Lesen Sie [hier](https://hub.hubzilla.hu/help/roles) mehr über Berechtigungsrollen.  

- system.default_profile_photo 

  Legt das Profilfoto fest, mit dem neue Channels beginnen. Dies sollte den Namen eines Verzeichnisses enthalten, das sich unter images/default_profile_photos/ befindet, oder es sollte nicht gesetzt werden. Wenn es nicht gesetzt ist, wird 'rainbow_man' angenommen.  

- system.directorytags 

  Legt die Anzahl der Schlüsselwort-Tags fest, die auf der Verzeichnisseite angezeigt werden. Die Voreinstellung ist 50, wenn sie nicht auf eine positive ganze Zahl gesetzt wird.  

- system.disable_directory_keywords 

  Wenn '1', werden keine Verzeichnisschlüsselwörter angezeigt. Wenn der Hub ein Verzeichnisserver ist, verhindern Sie die Rückgabe von Schlüsselwörtern an alle Verzeichnis-Clients. Bitte setzen Sie dies nicht für Verzeichnisserver im Bereich RED_GLOBAL.   

- system.disable_discover_tab 

  Hiermit können Sie die Fähigkeit, öffentliche Inhalte von externen Sites zu entdecken, vollständig deaktivieren.  

- system.disable_dreport 

  Wenn '1', werden keine Zustellungsberichte gespeichert oder verlinkt.  

- system.dlogfile 

  Logdatei, die für die Protokollierung von Entwicklungsfehlern verwendet wird.  Genau dasselbe wie logger sonst.  Dies ist keine Magie und erfordert Ihre eigenen Logging-Anweisungen.  Entwickler-Tool.  

- system.email_notify_icon_url 

  URL des Bildes (32x32), das in E-Mail-Benachrichtigungen (HTML-Bodies) angezeigt werden soll.  

- system.expire_delivery_reports 

  Verfallsdatum in Tagen für Zustellungsberichte - Standardwert 10  

- system.expire_limit 

  Nicht mehr als diese Anzahl von Beiträgen pro Kanal pro Ablauflauf ablaufen lassen, um den Speicher nicht zu erschöpfen. Voreinstellung 5000.  

- system.photo_storage_type 

  Wenn '1', wird das Dateisystem anstelle der SQL-Datenbank zum Speichern der Thumbnails verwendet. Voreinstellung ist '0'. Eingeführt in 4.2  

- system.hidden_version_siteinfo 

  Wenn 'true', wird die Softwareversion nicht auf den Seiten von siteinfo angezeigt (system.hide_version verbirgt die Version auch auf diesen Seiten, diese Einstellung verbirgt *nur* die Version auf den Seiten von siteinfo).  

- system.hide_help 

  Link zur Hilfedokumentation nicht in der Navigationsleiste anzeigen  

- system.hide_in_statistiken 

  Weist die roten Statistikserver an, diesen Hub in Hublisten komplett auszublenden.  

- system.hide_version 

  Wenn true, wird die Softwareversion auf Webseiten und Tools nicht angezeigt. (*) Muss in .htconfig.php eingestellt werden.  

- system.ignore_imagick 

  Ignoriert imagick und verwendet GD, auch wenn imagick auf dem Server installiert ist. Verhindert einige Probleme mit PNG-Dateien in älteren Versionen von imagick.  

- system.max_daily_registrations 

  Legt die maximale Anzahl der an einem Tag erlaubten Neuanmeldungen fest. Nützlich, um eine Überzeichnung zu verhindern, wenn das Projekt in der Öffentlichkeit bekannt gemacht wird.  

- system.max_import_size 

  Falls konfiguriert, die maximale Länge einer importierten Textnachricht. Diese wird normalerweise auf 200 KByte oder mehr belassen, um private Friendica-Fotos, die eingebettet sind, unterzubringen.  

- system.max_tagged_forums 

  Spam-Schutz. Begrenzt die Anzahl der getaggten Foren, die in jedem Beitrag erkannt werden. Standard ist 2. Nur die ersten 'n' Tags werden als Foren zugestellt, die anderen verursachen keine Zustellung.   

- system.minimum_feedcheck_minutes 

  Das minimale Intervall zwischen der Abfrage von RSS-Feeds.  Wenn dieses Intervall kleiner als das Cron-Intervall ist, werden die Feeds bei jedem Cronjob abgefragt. Der Standardwert ist 60, wenn er nicht festgelegt wurde. Die Site-Einstellung kann auch für jeden einzelnen Kanal durch eine Serviceklassen-Einstellung mit dem treffenden Namen „minimum_feedcheck_minutes“ außer Kraft gesetzt werden.  

- system.no_age_restriction 

  Beschränken Sie die Registrierung nicht auf Personen über 13 Jahren. In vielen Ländern ist es gesetzlich vorgeschrieben, dass das Alter angegeben werden muss und dass alle persönlichen Daten von Minderjährigen gesperrt werden müssen.    

- system.object_cache_days 

  Legt fest, wie lange zwischengespeicherte eingebettete Inhalte ohne erneutes Abrufen verwendet werden können. Die Voreinstellung ist 30 Tage.   

- system.openssl_conf_file 

  Geben Sie eine Datei an, die die OpenSSL-Konfiguration enthält. Wird in einigen Windows-Installationen benötigt, um die openssl-Konfigurationsdatei auf dem System zu finden.  Lesen Sie zuerst den Code. Wenn Sie den Code nicht lesen können, spielen Sie nicht mit ihm.  

- system.openssl_encrypt 

  Verschlüsselungs-Engine von openssl verwenden, Standard ist false (verwendet mcrypt für AES-Verschlüsselung)  

- system.optimize_items 

  Führt optimise_table während einiger Aufgaben aus, um Ihre Datenbank sauber und defragmentiert zu halten.  Dies geht zu Lasten der Leistung, während die Operationen laufen, sorgt aber auch dafür, dass die Dinge etwas schneller laufen, wenn sie nicht laufen.   Es gibt auch CLI-Hilfsprogramme zur Durchführung dieser Operation, die Sie vielleicht bevorzugen, besonders wenn Sie eine große Site haben.  

- system.override_poll_lockfile 

  Ignoriert die Sperrdatei im Poller-Prozess, damit mehr als ein Prozess gleichzeitig laufen kann.  

- system.paranoia 

  Wie pconfig, aber auf einer site-weiten Basis.  Kann durch Mitgliedereinstellungen überschrieben werden.  

- system.pin_types 

  Array der zulässigen Elementtypen für die Anheftung. Die Standardwerte hängen vom Modul ab, können aber hier geändert werden.  

- system.photo_cache_time 

  Wie lange die Fotos zwischengespeichert werden sollen, in Sekunden. Standardwert ist 86400 (1 Tag). Eine längere Zeit erhöht die Leistung, bedeutet aber auch, dass es länger dauert, bis geänderte Berechtigungen gelten.  

- system.plattform_name 

  Was als Plattformname auf Webseiten und in Statistiken angezeigt werden soll. (*) Muss in .htconfig.php eingestellt werden.  

- system.rating_enabled 

  Verteilte Reputationsberichte und Datenerfassung. Diese Funktion wird derzeit überarbeitet.  

- system.poke_basic 

  Reduziert die Anzahl der poke-Verben auf genau 1 („poke“). Deaktivieren Sie andere Verben.   

- system.proc_run_use_exec 

  Wenn 1, wird der Systemaufruf exec in proc_run verwendet, um Hintergrundaufgaben auszuführen. Standardmäßig verwenden wir proc_open und proc_close. Auf einigen (derzeit seltenen) Systemen funktioniert dies nicht gut.  

- system.projecthome 

  Zeigt die Projektseite auf Ihrer Homepage für abgemeldete Betrachter an.  

- system.projekthome 

  Legt die Projekthomepage als Startseite des Hubs fest. (Veraltet)  

- system.pubstream_bestellung 

  Legt die Pubstream-Reihenfolge fest. Mögliche Werte 'commented' (Standard), 'created' und 'edited'.  

- system.register_link 

  Pfad, auf den der „register“-Link im Anmeldeformular verweisen soll. Bei geschlossenen Sites wird dies auf 'pubsites' verweisen. Bei offenen Sites wird er normalerweise auf 'register' umgeleitet, aber Sie können dies auf eine benutzerdefinierte Site-Seite ändern, die Abonnements oder ähnliches anbietet.   

- system.reserved_channels 

  Erlaube den Mitgliedern nicht, Kanäle mit dieser durch Kommata getrennten Liste von Namen (keine Leerzeichen) zu registrieren.  

- system.sellpage 

  Eine URL, die in der Liste der öffentlichen Sites angezeigt wird, um Ihren Hub zu verkaufen - Serviceklassen anzeigen, usw.  

- system.startpage 

  Legt die Standardseite fest, die nach einer Anmeldung für alle Channels auf dieser Website angezeigt wird.  Kann durch Benutzereinstellungen überschrieben werden.  

- system.sys_expire_days 

  Wie viele Tage sollen entdeckte öffentliche Inhalte von anderen Websites aufbewahrt werden?  

- system.taganyone 

  Erlaubt das @mention-Tagging von jedem, egal ob Sie verbunden sind oder nicht.  

- system.tempdir 

  Ort, an dem temporäre Dateien gespeichert werden (derzeit unbenutzt), Standard ist in der PHP-Konfiguration definiert.  

- system.tos_url 

  Setzt einen alternativen Link für den ToS-Speicherort.  

- system.transport_security_header 

  wenn ungleich Null und SSL verwendet wird, wird ein strict-transport-security-Header auf den Webseiten eingefügt  

- system.uploaddir 

  Speicherort für das Hochladen von Dateien (Standard ist system.tempdir, derzeit nur vom js_upload-Plugin verwendet)  

- system.workflow_channel_next 

  Die Seite, zu der neue Mitglieder unmittelbar nach der Erstellung eines Channels weitergeleitet werden sollen.  

- system.arbeitsablauf_register_next 

  Die Seite, auf die Mitglieder direkt nach dem Erstellen eines Kontos geleitet werden (nur wenn auto_channel_create oder UNO aktiviert ist).    

#### Verzeichnis-Konfiguration  

##### Standardwerte für die Verzeichnissuche   

- directory.globaldir 

  0 oder 1. Standardwert 0. Wenn Sie das Verzeichnis auf einer Site besuchen, sehen Sie standardmäßig nur die Mitglieder dieser Site. Man muss einen zusätzlichen Schritt machen, um die Leute im Rest des Netzwerks zu sehen; und indem man das tut, gibt es eine klare Abgrenzung, dass diese Leute *nicht* Mitglieder dieser Site sind, sondern eines größeren Netzwerks.  

- directory.pubforums 

  0 oder 1. Öffentliche Foren *sollten* standardmäßig 0 sein.  

- directory.safemode 

  0 oder 1.     

##### Konfiguration des Verzeichnisservers 

- system.directory_mode   
- system.directory_primary   
- system.directory_realm   
- system.verzeichnis_server   
- system.realm_token