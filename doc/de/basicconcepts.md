## Grundlegende Konzepte von Hubzilla



### Kanäle (im Sinne von Identitäten im Gegensatz zu einer App)

Als Nutzer (d. h. als Privatperson oder als Verein) können Sie eine oder mehrere Web-**Identitäten** erstellen. Die Webpräsenz einer Identität wird innerhalb von Hubzilla gebündelt: Ein Besucher  der Web-Identität sieht Inhalte, die mit dieser Identität in Verbindung stehen, an einem  Ort, der über das Hauptmenü *Apps* für diese Identität verfügbar ist. 

Intern, innerhalb der Software, wird eine Identität als „Kanal“ bezeichnet. Für einen Besucher bedeutet das Wort „Kanal“ jedoch **eine** bestimmte App, die zu dieser Identität gehört, nämlich die **Pinnwand**, auf der die (möglicherweise) föderierten Beiträge der Identität in einer zeitachsenbasierten Timeline angezeigt werden.

Im Folgenden verstehen wir Kanäle als Identitäten: Als angemeldeter Benutzer können Sie zwischen Ihren Kanälen wechseln, um die Inhalte für jeden Kanal zu bearbeiten, d. h. Beiträge zu veröffentlichen oder ein oder mehrere Kanalprofile, Websites, Wiki-Seiten und mehr zu erstellen. Pro Kanal können Sie auch Dateien in einer Cloud verwalten, Fotos mit Tags versehen und benennen und die Fotos in einer Webgalerie anzeigen. Ereignisse des Kanals können in einem Kalender angezeigt werden.



### Dezentrales Netzwerk: Zugriff über Grenzen hinweg (Zot/Nomad) vs. Bereitstellung über Grenzen hinweg (Zot/Nomad, ActivityPub und Diaspora)

Wenn dies erlaubt ist, verbinden sich Identitäten auf Hubzilla über Server- und Verwaltungsgrenzen hinweg über das Kommunikationsprotokoll Zot/Nomad (und, sofern von Ihrem Server bereitgestellt, auch über die Protokolle ActivityPub und Diaspora). 

Mit Ausnahme von Beiträgen/Nachrichten bleiben alle veröffentlichten Inhalte lokal auf Ihrem Server. Sie können Inhalte zwar öffentlich veröffentlichen, aber auch lokale Inhalte nur mit bestimmten Verbindungen teilen. Letzteres ist nur mit Verbindungen über Zot/Nomad (also mit Identitäten auf Hubzilla und Streams) möglich.



### Zugriffskontrolle

Ihre lokalen Inhalte können von den Zot/Nomad-basierten Verbindungen „besucht“ werden, denen Sie die entsprechende Berechtigung erteilt haben. Indem Sie Ihre Verbindungen  Verbindungslisten (sogenannten „Datenschutzgruppen“) zuordnen, können Sie  allen Mitgliedern dieser Liste Zugriff auf bestimmte Inhalte gewähren. 

Sie können Beiträge/Nachrichten (auch über die Protokolle ActivityPub oder Disaspora) an eine Verbindung, an eine Datenschutzgruppe oder an die Öffentlichkeit senden, indem Sie eine Zugriffskontrollliste verwenden.

Es ist möglich, eine Dauer für das Ablaufen eines Beitrags/einer Nachricht festzulegen. Auf diese Weise können Sie den Zugriff zeitlich begrenzen. 



### Fernauthentifizierung

„Kennst du mich? – Ja, ich kenne dich, willkommen!“
Der Zugriff auf nicht öffentliche Inhalte auf einem Remote-Server sollte nur möglich sein, wenn du dich gegenüber diesem Server authentifizieren kannst. Fediverse-Software, die nur das ActivityPub- oder das Diaspora-Protokoll verwendet, kann jedoch nur Konten vom lokalen Server authentifizieren. 
Das Zot-Protokoll hingegen verfügt über einen integrierten Mechanismus namens MagicAuth, der es einem Server ermöglicht, Identitäten, die auf einem anderen (entfernten) Server registriert sind, Zugriff auf Inhalte und Aktionen zu gewähren oder zu verweigern.



### Nomadische Identität

Das Zot/Nomad-Protokoll ermöglicht es, Ihre Kanäle von dem Hub zu trennen, auf dem Sie sie erstellt haben. Sie können sie auf einen anderen Hub übertragen oder klonen. In diesem Fall existieren die Identität und die Daten des Kanals gleichzeitig an mehreren Orten. Dies sorgt für Ausfallsicherheit der Kanäle, falls ein Hub abgeschaltet wird oder nicht mehr verfügbar ist.



### Modulares Ökosystem durch Apps

 Hubzilla stellt für jede Funktion eine separate **App** zur Verfügung, d. h. die Cloud, Fotos, Galerie, Chat, Wiki, Kalender, Kontakte oder die  Verbindungs-App. Ein Serveradministrator entscheidet, welche Apps  für die Benutzer dieses Servers verfügbar sein sollen. Ein Benutzer kann dann die verfügbaren Apps installieren oder  deinstallieren. 



### Zusammenarbeit

Die folgenden gemeinsamen privaten Bereiche ermöglichen die Arbeit im Team mit Hubzilla:

- Als Benutzer erlauben Sie ausgewählten Verbindungen, Ihre Webseiten und Wiki-Seiten zu lesen und zu bearbeiten.
- Erlauben Sie ihnen, Ihre Dateien, Ihren Kalender und Ihre Kontakte über die Weboberfläche (die Cloud-, Kalender- und Kontakt-Apps) oder WebDAV, CalDAV und CardDAV zu lesen und zu bearbeiten. 
- Verwenden Sie Konversations-Threads (Beiträge), die nur für Ihre Kollaborationsgruppe sichtbar sind, indem Sie die Zugriffskontrollliste entsprechend einstellen.



### Teilen vs. Boosten

Mit Hubzilla können Sie Beiträge anderer Fediverse-Nutzer (erneut) teilen. Das (erneute) Teilen in Hubzilla ist so, als würden Sie Ihren Freunden Jennie und Omar erzählen, was Giaco gesagt hat. Und dann sagen sie: „Cool, das gefällt mir“.

Hubzilla ermöglicht auch das Boosten. Etwas zu boosten ist so, als würdest du deinen Freunden Jennie und Omar erzählen, was Giaco gesagt hat, während Giaco und alle seine Freunde und seine Familie (die du nicht kennst) zuhören. Giaco und alle seine Freunde und seine Familie können jetzt mit dir sprechen.