### Fehlersuche

#### Logdateien 

Die Systemprotokolldatei ist eine äußerst nützliche Ressource, um Fehler aufzuspüren. Dies kann auf der Konfigurationsseite admin/log aktiviert werden. Eine Loglevel-Einstellung von `LOGGER_DEBUG` wird für stabile Produktionsstandorte bevorzugt. Die meisten Probleme bei der Kommunikation oder der Speicherung werden hier aufgelistet. Eine Einstellung von LOGGER_DATA bietet *viel* mehr Details, kann aber Ihre Festplatte füllen. In jedem Fall empfehlen wir die Verwendung von logrotate auf Ihrem Betriebssystem, um die Protokolle zu überprüfen und ältere Einträge zu löschen.

Am Ende der Datei .htconfig.php befinden sich mehrere (auskommentierte) Zeilen, die die PHP-Fehlerprotokollierung aktivieren. Dies meldet Probleme mit der Codesyntax und der Ausführung des Codes und ist die erste Stelle, an der Sie nach Problemen suchen sollten, die zu einem „weißen Bildschirm“ oder einer leeren Seite führen. Dies ist in der Regel das Ergebnis von Code-/Syntaxproblemen. Datenbankfehler werden in der Systemprotokolldatei gemeldet, aber wir haben es als nützlich empfunden, in Ihrem Hauptverzeichnis eine Datei namens dbfail.out anzulegen, in der *nur* datenbankbezogene Probleme gesammelt werden. Wenn die Datei existiert und beschreibbar ist, werden Datenbankfehler sowohl in dieser Datei als auch in der Systemprotokolldatei protokolliert.

Im Falle von „500“-Fehlern werden die Probleme oft in den Protokollen Ihres Webservers protokolliert, oft in /var/log/apache2/error.log oder ähnlichem. Konsultieren Sie die Dokumentation Ihres Betriebssystems.

Es gibt drei verschiedene Protokollierungsmöglichkeiten.

**Die erste ist das Datenbankfehlerprotokoll**. Dieses wird nur verwendet, wenn Sie eine Datei mit dem Namen `dbfail.out` im Stammverzeichnis Ihrer Website anlegen und sie für den Webserver schreibbar machen. Wenn Datenbankabfragen fehlgeschlagen sind, werden sie hier gemeldet. Sie weisen im Allgemeinen auf Tippfehler in unseren Abfragen hin, treten aber auch auf, wenn der Datenbankserver die Verbindung unterbricht oder Tabellen beschädigt werden. In seltenen Fällen werden hier Race Conditions angezeigt, wenn zwei Prozesse versuchen, einen xchan- oder Cache-Eintrag mit derselben ID zu erstellen. Alle anderen Fehler (insbesondere anhaltende Fehler) sollten untersucht werden.

**Der zweite Bereich ist das PHP-Fehlerprotokoll**. Dieses wird vom Sprachprozessor erstellt und meldet nur Probleme in der Sprachumgebung. Auch hier kann es sich um Syntax- oder Programmierfehler handeln, die jedoch in der Regel fatal sind und zu einem „weißen Bildschirm des Todes“ führen, d. h. PHP wird beendet. Sie sollten sich diese Datei ansehen, wenn etwas schief läuft, das nicht zu einem weißen Bildschirm führt, aber es ist nicht ungewöhnlich, dass diese Datei tagelang leer ist.

Am Ende der mitgelieferten Datei `.htconfig.php` befinden sich einige Zeilen, die, wenn sie nicht auskommentiert werden, ein PHP-Fehlerprotokoll aktivieren (*äußerst* nützlich, um die Ursache von Fehlern beim weißen Bildschirm zu finden). Dies ist standardmäßig nicht der Fall, da es Probleme mit dem Eigentum an der Logdatei und den Schreibrechten geben könnte und die Logdatei standardmäßig nicht rotiert.

**Die dritte Datei ist das „Anwendungsprotokoll“.** Dieses wird von Hubzilla verwendet, um zu berichten, was im Programm vor sich geht und meldet normalerweise alle Schwierigkeiten oder unerwarteten Daten, die wir erhalten haben. Gelegentlich werden auch „Heartbeat“-Statusmeldungen ausgegeben, um anzuzeigen, dass wir einen bestimmten Punkt in einem Skript erreicht haben. **Dies** ist die wichtigste Protokolldatei für uns, da wir sie selbst nur zu dem Zweck erstellen, den Status von Hintergrundaufgaben und alles, was seltsam oder fehl am Platz erscheint, zu melden. Es muss nicht unbedingt etwas Schlimmes sein, aber vielleicht einfach nur unerwartet. Wenn Sie eine Aufgabe ausführen und ein Problem auftritt, lassen Sie uns wissen, was in dieser Datei steht, wenn das Problem auftritt. (Bitte schicken Sie mir keine 100M-Dumps, das würde mich nur verärgern). Nur ein paar relevante Zeilen, damit ich ein paar hunderttausend Codezeilen ausschließen und mich darauf konzentrieren kann, wo das Problem auftritt.

Dies sind Ihre Website-Protokolle, nicht meine. Wir melden ernsthafte Probleme auf jeder Protokollebene. Für die meisten Websites empfehle ich die Log-Ebene `DEBUG` - sie liefert ein paar zusätzliche Informationen und erzeugt keine riesigen Logdateien. Wenn ein Problem auftritt, das sich allen Versuchen entzieht, es zu verfolgen, sollten Sie für einen kurzen Zeitraum die Protokollebene `DATA` verwenden, um alle Details der Strukturen zu erfassen, mit denen wir es zu diesem Zeitpunkt zu tun hatten. Diese Protokollebene verbraucht sehr viel Speicherplatz und wird daher nur für kurze Zeiträume oder für Test-Sites von Entwicklern empfohlen.

Ich empfehle die Konfiguration von logrotate sowohl für das php-Protokoll als auch für das Anwendungsprotokoll. Normalerweise werfe ich alle ein bis zwei Wochen einen Blick auf dbfail.out, behebe alle gemeldeten Probleme und beginne dann mit einer neuen Datei. Das Gleiche gilt für die PHP-Protokolldatei. Ich schaue darin ab und zu nach, ob es etwas gibt, das behoben werden muss.

Wenn etwas schief läuft und es sich nicht um einen schwerwiegenden Fehler handelt, schaue ich mir die Anwendungsprotokolldatei an. Oft werde ich

```
tail -f logfile.out
```

während ich einen Vorgang wiederhole, bei dem es Probleme gibt. Oft füge ich zusätzliche Logging-Anweisungen in den Code ein, wenn es keinen Hinweis darauf gibt, was schief läuft. Selbst so etwas Einfaches wie „got here“ oder der Ausdruck des Wertes einer Variable, die verdächtig sein könnte. Auch das können Sie tun - ich möchte Sie sogar dazu ermutigen, dies zu tun. Sobald Sie gefunden haben, was Sie suchen müssen, können Sie

```
git checkout file.php
```

um sofort alle zusätzlichen Protokollierungsdaten, die Sie hinzugefügt haben, zu löschen. Verwenden Sie die Informationen aus diesem Protokoll und alle Details, die Sie aus Ihrer Untersuchung des Problems liefern können, um Ihren Fehlerbericht einzureichen - es sei denn, Ihre Analyse weist auf die Quelle des Problems hin. In diesem Fall beheben Sie es einfach.

##### Rotierende Protokolldateien 

1. Aktiviere das **logrot** Addon im offiziellen [hubzilla-addons](https://framagit.org/hubzilla/addons) repo
2. Legen Sie in Ihrem Web-Root ein Verzeichnis namens `log` mit Schreibrechten für den Webserver an
3. Gehen Sie zu den **logrot-Administrationseinstellungen** und geben Sie diesen Ordnernamen sowie die maximale Größe und Anzahl der aufbewahrten Protokolldateien ein.

#### Probleme melden 

Wenn Sie Probleme melden, versuchen Sie bitte, so viele Details wie nötig anzugeben, damit die Entwickler das Problem reproduzieren können, und geben Sie den vollständigen Text aller Fehlermeldungen an.

Wir ermutigen Sie, diese Protokolle zusammen mit dem Quellcode, den Sie besitzen, so gut wie möglich zu nutzen, um Probleme zu beheben und ihre Ursache zu finden. Die Community kann oft helfen, aber nur Sie haben Zugang zu den Logdateien Ihrer Website, und es wird als Sicherheitsrisiko angesehen, sie weiterzugeben.

Wenn ein Code-Problem aufgedeckt wurde, melden Sie es bitte im Bugtracker des Projekts (https://framagit.org/hubzilla/core/issues). Geben Sie auch hier so viele Details wie möglich an, damit wir nicht immer wieder Fragen zu Ihrer Konfiguration stellen oder das Problem duplizieren müssen, damit wir das Problem direkt angehen und herausfinden können, was zu tun ist. Sie können auch gerne Ihre eigenen Lösungen anbieten und Patches einreichen. Wir ermutigen Sie sogar dazu, da wir alle Freiwillige sind und nur wenig Zeit zur Verfügung haben. Je mehr Leute mithelfen, desto leichter wird die Arbeit für alle. Es ist in Ordnung, wenn Ihre Lösung nicht perfekt ist. Jedes bisschen hilft, und vielleicht können wir es ja noch verbessern.