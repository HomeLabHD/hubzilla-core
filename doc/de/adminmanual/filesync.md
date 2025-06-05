### Datei-Synchronisation und Klonen 

Das Klonen von Dateien über mehrere Instanzen eines Channels hinweg ist aufgrund der Natur der PHP-Speicherzuweisung ein sehr schwieriges Problem. Dies muss völlig anders gehandhabt werden als das Klonen oder Synchronisieren anderer Informationen. (Die Verarbeitung einer großen Videodatei oder von 40-50 Fotos kann den Speicher voll auslasten). Daher können wir nicht einfach alle Daten in eine Speicherauszugsdatei auslagern und sie dann sequentiell verarbeiten. Das Laden der Speicherauszugsdatei selbst wird wahrscheinlich den Speicherplatz erschöpfen.

Außerdem gibt es zwei Hauptoperationen, die wir in Betracht ziehen. Die erste ist die schwierigste - das Speichern und anschließende Importieren aller Kanalinformationen in einen neuen Kanal-Klon. Der zweite ist die Synchronisierung von Datei-Änderungen, die in zwei oder mehr „aktiven“ Klonen auftreten.
Beim ersten Versuch mit diesem Tool werden wir uns auf den zweiten Fall konzentrieren, während wir versuchen, ein gewisses Maß an Kompatibilität mit dem ersten Fall aufrechtzuerhalten, damit wir dieselben Tools wiederverwenden können.

#### Metadaten 

Zunächst benötigen wir die Metadaten der Datei, um ihre Struktur auf einer anderen Website genau rekonstruieren zu können. Dazu sind die folgenden Informationen erforderlich:
die „attach“-Struktur (ohne Dateiinhalt - das ist die Standardeinstellung) für die Datei selbst **und** ihre übergeordneten Verzeichnisse, damit wir ihren genauen Platz im Dateisystem wiederherstellen können, da wir nicht wissen, ob das übergeordnete Verzeichnis schon einmal importiert worden ist oder nicht. 
Foto"-Struktur für alle Fotoelemente, die durch das Hochladen dieser Datei in das System erstellt wurden. Diese enthält in der Regel mehrere verschiedene „Skalen“ oder Miniaturbilder, von denen einige für die Verwendung als Profilfoto oder Titelbild beschnitten werden können. Wir müssen die Informationen über den Bildausschnitt aufbewahren, die nicht in den Metadaten, sondern nur in den gespeicherten Daten enthalten sind. Die eigentlichen Miniaturbilddaten können in den Metadaten enthalten sein, müssen es aber nicht. Ein großformatiges Titelbild (Maßstab #7) könnte möglicherweise zu Speicherproblemen führen. Es ist zwar nicht so schlimm wie ein 100-Meter-Video, aber wenn Sie mehrere davon haben, können sie sich summieren.

Objekteinträge, die mit dieser Datei verknüpft sind. Dabei kann es sich um Dateifreigabeaktivitäten, das „übergeordnete Element“, das mit Fotos verknüpft ist, und angehängte Konversationselemente (Foto-Likes, Kommentare usw.) handeln.

Für alle diese Elemente muss die URL ersetzt und das Element neu signiert werden, wenn es auf eine andere Website verschoben wird.

#### Dateidaten 

Dann haben wir die eigentlichen Dateidaten, die wir für die Rekonstruktion der Datei benötigen. Diese müssen getrennt von den Metadaten gespeichert werden, damit der Speicher bei der Verarbeitung nicht überlastet wird. Die eigentlichen Dateidaten können zur Rekonstruktion der Anhangsstruktur und der ersten vier Fotoskalen verwendet werden. Wenn es sich um ein Foto handelt, benötigen wir Zugriff auf die Skala „#4“ (Profilfoto) und die Skala #7 (Titelbild), so wie sie ursprünglich beschnitten wurden. Alle anderen Miniaturansichten können aus diesen generiert werden. 

#### Dateiabgleich 

Wir werden diesen Vorgang zuerst betrachten, weil er wahrscheinlich am einfachsten zu implementieren ist. Wenn ein Foto zum Quellsystem hinzugefügt, entfernt oder geändert wird, senden wir ein Klon-Synchronisierungspaket an alle bekannten Klone, das die Metadaten, aber **keine Dateidaten** enthält. Wir können nur ein Sync-Paket pro zu synchronisierendem Dateivorgang senden.

Die empfangende Seite erstellt alle Metadatenstrukturen, führt eine URL-Übersetzung durch und speichert sie. Dann muss es die eigentlichen Daten abrufen. Unter der Voraussetzung, dass CURL Streaming unterstützt, wird eine authentifizierte Anfrage an die ursprüngliche Website gesendet und die Originaldatei wird angefordert und direkt auf die Festplatte gestreamt (unter Umgehung aller Verarbeitungsschritte). Wenn Fotomaßstab Nr. 4 oder Maßstab Nr. 7 erforderlich ist, werden diese angefordert und in den entsprechenden Strukturen gespeichert. Wir gehen in diesem Fall davon aus, dass der große Maßstab des Titelbildes den Speicher nicht erschöpft. Wenn CURL das Streaming nicht unterstützen kann, müssen die Anforderungspakete in eine Warteschlange gestellt und an den Ursprungsort gesendet werden, um „Chunks“ der Datei zu erhalten, und wieder zusammengesetzt werden, sobald alle Chunks abgerufen wurden.

Die authentifizierte Anfrage hängt vom jeweiligen Mechanismus ab. Beim CURL-Streaming muss wahrscheinlich ein signiertes Geheimnis mit einem Zeitstempel erzeugt und an die Ursprungsseite der Datei gesendet werden. Dann können die Daten mit minimaler interner Verarbeitung abgerufen und unter Verwendung von stdio-Pufferung direkt auf die Festplatte ausgegeben werden. Im Falle einer Nomad-Anforderung wird das Nomad-Anforderungspaket validiert, aber die Planung von Chunk-Batches und deren Wiederzusammensetzung könnte sich als schwierig erweisen.

#### Sicherung/Wiederherstellung von Dateien 

Dies ist viel komplizierter, da wir keinen authentifizierten Webserver haben, von dem wir Daten anfordern können. Die Metadaten können größtenteils identisch sein, aber wir brauchen eine Form der Signalisierung, dass wir die Datei nicht über das Web abrufen werden. Dies erfordert wahrscheinlich einen clientseitigen Prozess, um jede Metadaten-Datei zu analysieren und eine Datei auf der Festplatte zu finden, mit der sie verknüpft ist. Anschließend müssten die Daten mit einem speziellen Endpunkt, der für diese Aufgabe vorgesehen ist, an den Zielserver gestreamt werden. Eine Java-Applikation könnte hier die beste Option sein, um die Plattformneutralität zu wahren.

Eine andere Möglichkeit wäre die Verwendung von WebDAV für diesen Schritt. Die Metadaten würden zuerst hochgeladen, dann die Datendateien. Wenn eine Datendatei mit einer vorhandenen Metadaten-Datei übereinstimmt, würden die Metadaten verarbeitet, die Datei entsprechend gespeichert und die Metadaten-Datei anschließend entfernt. In diesem Fall müssten in den Metadaten Fotos der Skalen 4 und 7 bereitgestellt werden.

Optional könnte dieser Schritt auch mit einem lokalen Dateisystem auf dem Zielserver durchgeführt werden. Dies wäre die leistungsfähigste Lösung, und eine Reihe von Shell-basierten Tools (im Falle von Linux) könnte die „Client-Seite“ der Aufgabe übernehmen.

Die Komplexität dieser Aufgabe erfordert eine sorgfältige Planung, wie die Daten organisiert und gespeichert und gegebenenfalls per Fernzugriff gesichert oder von der Quellwebsite zur Sicherung übertragen werden.

#### Rückwärtskompatibilität 

Es gibt einige offensichtliche Probleme bei der Bereitstellung von Daten für die Sicherung oder das Klonen, die auf dem System vor der Existenz von Wiederherstellungs-/Synchronisierungstools vorhanden waren. Um die Werkzeuge selbst relativ unkompliziert zu halten (soweit dies angesichts der Beschränkungen möglich ist), muss die Abwärtskompatibilität möglicherweise durch ein spezielles Plugin oder Addon hergestellt werden. 