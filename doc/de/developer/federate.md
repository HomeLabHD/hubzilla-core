### Erstellen von Protokoll- Föderationsdiensten 

Es gibt drei Hauptkomponenten beim Schreiben von Föderations-Plugins. Diese sind:

1. Kanalerkennung und Verbindungsaufbau
2. Senden von Beiträgen/Nachrichten
3. Empfangen von Beiträgen/Nachrichten

Darüber hinaus müssen Verbundtreiber Folgendes bewältigen

4. Unterschiede in den Datenschutzrichtlinien (und Inhaltsformaten)

#### Herstellen von Verbindungen 

Die Kernanwendung bietet eine Kanalerkennung in der folgenden Reihenfolge:

1. Nomad-Kanal-Erkennung
2. Webfinger (channel@hub) Lookup 2.1 RFC7033 Webfinger 2.2 XRD basierter Webfinger (alter Stil)
3. URL-Ermittlung (derzeit nur zur Ermittlung von RSS-Feeds verwendet)
4. Wenn alle diese Methoden fehlschlagen, ist das Netzwerk „unbekannt“ und wir sind nicht in der Lage, mit dem Kanal zu kommunizieren oder eine Verbindung zu ihm herzustellen. Ein „xchan“-Datensatz *kann* dennoch erstellt werden, **wenn** genügend Informationen bekannt sind, um einen eindeutigen Kanal zu identifizieren. 

Jeder der Lookup-Dienste kann an ein Plugin gebunden und erweitert werden. Wenn ein Kanal entdeckt wird, erstellen wir einen „xchan“-Datensatz, der eine plattformneutrale Darstellung der Kanalidentität ist. Wenn wir Informationen an den Kanal senden müssen, ist in der Regel auch ein „hubloc“-Datensatz (Standort des Hubs) erforderlich. Ein 'follow'-Plugin-Hook wird bereitgestellt, um Webfinger und diese Erkennungssequenz vollständig zu umgehen.

Der letzte Schritt, um dies alles zusammenzufügen, ist die Erstellung eines „abook“-Datensatzes, der einen xchan in einer Beziehung zu einem lokalen Kanal mit einem bestimmten Satz von Berechtigungen anlegt. 
Bei Netzwerken, die keine nomadische Identität unterstützen, muss Ihr Plugin auch „abook_instance“ setzen, eine durch Kommata getrennte Liste von lokalen URLs, mit denen der entfernte Kanal verbunden ist. Wenn Ihr Mitglied zum Beispiel mit meinem Channel-Klon unter [https://example.com](https://example.com/) verbunden war, würde die abook_instance für diese Verbindung['https://example.com](https://example.com/)' lauten. Wenn Sie sich auch mit meinem Klon unter [https://abc.example.com](https://abc.example.com/) verbunden haben, würde die Zeichenfolge in[„https://example.com,https://abc.example.com](https://example.com,https//abc.example.com)“ geändert werden. Dies ermöglicht es den lokalen Kanälen zu unterscheiden, mit welcher Instanz ein bestimmter entfernter Kanal verbunden ist und vermeidet Zustellungsfehler an diese Kanäle von anderen Kloninstanzen.

Ein Föderations-Plugin für ein Webfinger-basiertes System muss nur die Webfinger- oder XRD-Ergebnisse untersuchen und einen Protokoll-Stack identifizieren, der für die Verbindung oder Kommunikation verwendet werden kann. Dann erstellt es einen xchan-Datensatz mit dem angegebenen 'xchan_network'-Typ und einen hubloc-Datensatz mit demselben 'hubloc_network' mit den angegebenen Informationen. Derzeit muss das Plugin den gesamten Datensatz erstellen, aber in Zukunft wird dies erweitert, so dass das Plugin nur einen Netzwerknamen identifizieren muss und der Datensatz mit allen anderen bekannten Werten gefüllt wird.

Ein xchan-Datensatz ist immer erforderlich, um eine Verbindung herzustellen. Um eine Verbindung herzustellen, erstellen Sie einen abook-Eintrag mit den gewünschten Berechtigungen. 
Zusätzliche Informationen, die Ihr Plugin für die Kommunikation benötigt, können in der xconfig-Tabelle und/oder abconfig-Tabelle gespeichert werden, wenn es in den xchan- oder hubloc-Tabellen keine passende Tabellenspalte gibt.

Wenn eine Verbindung hergestellt wird, rufen wir normalerweise den Notifier (include/notifier.php) auf, um eine Nachricht an den entfernten Kanal zu senden. Dies ist an den Hook 'permissions_create' gebunden. Ihr Plugin muss dies verarbeiten, um eine „Follow“- oder „Make Friends“-Nachricht an das andere Netzwerk zu senden.

Anmerkungen: Die erste Stufe des Nomad lookup wird durch einen webfinger lookup ersetzt. Diese Arbeit ist in Arbeit. Ursprünglich war ein separater Lookup erforderlich, da Webfinger keine Nicht-SSL-Verbindungen zulässt. Um diese Einschränkung zu umgehen, werden wir Nomad-Lookups, die nicht auf SSL basieren (in der Regel Test- und Entwicklungsseiten), über den „alten“ XRD-basierten Webfinger anbieten.

Die Kernanwendung wird versuchen, xchan-Einträge für Projekte zu erstellen, die als Mitglieder des „offenen Webs“ identifiziert wurden; derzeit Hubzilla, Friendica, Diaspora, GNU-Social und Pump.io. Dies geschieht, damit Kommentare zwischen den Projektseiten weitergegeben werden können und das Netzwerk korrekt identifiziert werden kann. Ein Föderations-Plugin ist erforderlich, um eine vollständige Verbindung mit anderen Netzwerken herzustellen, aber Kommentare können auch an Seiten weitergeleitet werden, auf denen ein solches Plugin nicht installiert ist, damit es keine unerklärlichen Löcher in den Unterhaltungen gibt.

Die Kernanwendung muss auch die Möglichkeit bieten, Diaspora-Kommentare zu signieren, da diese ein spezielles Signierformat erfordern und vom Verfasser des Kommentars signiert werden müssen, unabhängig davon, ob dieser Kanal mit Diaspora föderiert ist. Der Eigentümer der Konversation kann mit Diaspora föderieren, so dass die Kommentare signiert werden müssen. Dies ist bedauerlich, aber notwendig. 

#### Versenden von Nachrichten 

Jedes Mal, wenn eine Nachricht gesendet wird (mit der einzigen Ausnahme der Verzeichniskommunikation), rufen wir den Notifier (include/notifier.php) auf und übergeben ihm die Art der Nachricht und im Allgemeinen einen Identifikator, um die Informationen, die gesendet werden, aus der Datenbank abzurufen (Artikel oder Konversationsdinge, private E-Mails, Aktualisierungen von Berechtigungen usw.). 
Der Notifier verfügt über mehrere Hooks, die von Plugins auf unterschiedliche Weise verwendet werden können, je nachdem, wie ihre Zustellschleife funktioniert. Für verschiedene Nachrichtentypen und komplexe Zustellungssituationen müssen Sie möglicherweise mehrere Hooks verwenden. Der „permissions_create“-Hook wurde bereits im ersten Abschnitt erwähnt. Es gibt auch eine „permissions_update“-Nachricht, wenn sich die Berechtigungen geändert haben und das andere Ende der Verbindung darüber informiert werden muss. Nur wenige Dienste werden dies bereitstellen oder handhaben (da ihre Berechtigungen statisch sind), aber es wird z. B. auch verwendet, um Nachrichten zur Aktualisierung von Profilen und Profilfotos zu senden, und Sie möchten dies vielleicht handhaben.
Der nächste Plugin-Hook ist 'notifier_process'. Ihm wird ein Array übergeben, das den kompletten Status des Notifizierers enthält und einmal pro Notifiziereraufruf aufgerufen wird. Er enthält die vollständige Liste der Empfänger (mit jeweils gesetztem xchan_network).

Es gibt auch 'notifier_hub', dem wie 'notifier_process' der komplette Status des Antragstellers übergeben wird, aber der Unterschied besteht darin, dass er für jede einzelne Hub- oder eindeutige URL-Zustellung aufgerufen wird und auf den Typ hubloc_network abgestimmt werden kann. Die Hub-Zustellung ist wesentlich effizienter als die Empfängerzustellung, eignet sich aber möglicherweise nicht für alle Protokollstacks.

Ihr Plugin muss den Nachrichtenstatus und die Empfänger verstehen und das gesendete Element in das gewünschte Format übersetzen. Sie müssen auch den Datenschutz prüfen und die Kommunikation an alle außer den vorgesehenen Empfängern blockieren - *wenn* es sich um eine private Kommunikation handelt. Das Plugin wird an dieser Stelle die Nachricht oft in die Warteschlange stellen und die Warteschlangen-ID an den Melder zurückgeben.

Es gibt bereits Warteschlangen-Handler für einfache gepostete Daten. Wenn Sie einen Warteschlangeneintrag vom Typ 'post' erstellen, öffnen wir eine HTTP-POST-Anfrage und senden die bereitgestellten Daten und bestätigen den Erfolg oder Misserfolg. Sie können andere Kommunikationsformen erstellen, indem Sie einen anderen outq_driver-Typ angeben und die Verarbeitung von Warteschlangenanfragen selbst übernehmen. Wenn Sie verschiedene Fehlerzustände oder etwas anderes als Erfolg/Misserfolg bestätigen möchten, ist eine Zustellungsmeldung verfügbar. Für erweiterte Zustellungsberichte ist ebenfalls ein eigener Warteschlangentyp erforderlich. Der Basistyp „post“ behandelt nur Erfolg (erfolgreiche Kommunikation mit der Gegenstelle) und Misserfolg. 



#### Empfang von Nachrichten 

Für den Empfang von Nachrichten aus dem entfernten Netz ist wahrscheinlich ein „Empfangs“-Endpunkt oder -Modul erforderlich, der bzw. das für Ihr Netzwerk-Kommunikationsprotokoll vorgesehen ist. Dabei handelt es sich um eine URL-Route, die Ihr Plugin möglicherweise mit dem Hook 'module_loaded' registrieren muss. Ihr Modul übernimmt dann die Verantwortung für den Import aller Daten, die an diesem Endpunkt ankommen, und die Übersetzung in das für dieses Projekt erforderliche Format sowie die Speicherung der resultierenden Daten. Die von uns verwendete Grundstruktur ist ein erweiterbares Activitystream-Element, jedoch mit leicht abweichenden Feldnamen und mehreren optionalen Feldern. Sie kann leicht auf einen Activitystream abgebildet werden. Zusätzliche Daten können in der Tabelle „iconfig“ gespeichert werden. item_store() und item_store_update() werden im Allgemeinen verwendet, um die Daten zu speichern und entsprechende Benachrichtigungen zu senden. Ähnliche Möglichkeiten gibt es für private Post und Profilinformationen.