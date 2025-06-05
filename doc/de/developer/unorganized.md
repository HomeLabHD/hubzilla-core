### Noch zu organisierende Informationen 

**Hier ist, wie Sie sich uns anschließen können.**
Richten Sie sich zunächst ein funktionierendes Git-Paket auf dem System ein, auf dem Sie entwickeln werden.

Erstellen Sie Ihr eigenes Github-Konto.

Sie können das Red-Repository von https://framagit.org/hubzilla/core.git forken/klonen.
Folgen Sie den Anweisungen hier: [http://help.github.com/fork-a-repo/](http://help.github.com/fork-a-repo/), um Ihren eigenen Tracking-Fork auf github zu erstellen und zu verwenden.

Gehen Sie dann auf Ihre Github-Seite und erstellen Sie einen „Pull request“, wenn Sie bereit sind, uns mitzuteilen, dass Ihre Arbeit zusammengeführt werden soll.

**Wichtig**

Bitte ziehen Sie alle Änderungen aus dem Projekt-Repository ein und fügen Sie sie mit Ihrer Arbeit zusammen **, bevor** Sie einen Pull-Request erstellen. Wir behalten uns das Recht vor, jeden Patch abzulehnen, der zu einer großen Anzahl von Merge-Konflikten führt. Dies gilt insbesondere für Sprachübersetzungen, bei denen wir möglicherweise nicht in der Lage sind, die feinen Unterschiede zwischen den konfligierenden Versionen zu verstehen.

**Testen** Sie außerdem **Ihre Änderungen**. Gehen Sie nicht davon aus, dass eine einfache Korrektur nicht etwas anderes kaputt macht. Bitten Sie nach Möglichkeit einen erfahrenen Red-Entwickler, den Code zu überprüfen. 

**Wie man Hubzilla thematisiert**
Dies ist eine kurze Dokumentation über das, was ich beim Versuch, das Aussehen von Hubzilla zu ändern, herausgefunden habe.

Zuerst müssen Sie ein neues Thema erstellen. Dieses befindet sich in /view/theme, und ich habe mich für „redbasic“ entschieden, da es im Moment das einzig verfügbare ist. Nehmen wir an, ich nenne es .
Oh, und vergessen Sie nicht, die Funktion _init in /php/theme.php in _init() statt redbasic_init() umzubenennen.

Wenn Sie nun Javascript- oder CSS-Dateien hinzufügen müssen, fügen Sie sie zu /js oder /css hinzu und „registrieren“ sie dann in `_init()` durch `head_add_js('file.js')` und `head_add_css('file.css')`.
Jetzt werden Sie wahrscheinlich eine Vorlage ändern wollen. Diese sind in /view/tpl ODER view//tpl zu finden. Alles, was Sie tun müssen, ist, das, was Sie ändern wollen, in das tpl-Verzeichnis Ihres Themes zu kopieren.

Wir sind ziemlich entspannt, wenn es um Entwickler geht. Wir haben nicht viele Regeln. Einige von uns sind überlastet, und wenn Sie helfen wollen, lassen wir Sie gerne helfen. Wenn Sie sich jedoch an ein paar Richtlinien halten, wird der Prozess reibungsloser und die Zusammenarbeit einfacher. Wir haben Entwickler aus der ganzen Welt mit unterschiedlichen Fähigkeiten, unterschiedlichen kulturellen Hintergründen und unterschiedlicher Geduld. Unsere oberste Regel ist es, andere zu respektieren. Manchmal ist das schwierig, und manchmal haben wir sehr unterschiedliche Ansichten darüber, wie die Dinge funktionieren sollten, aber wenn sich jeder bemüht, werden wir gut miteinander auskommen. 

**Hier ist, wie Sie sich uns anschließen können.**

Besorgen Sie sich zunächst ein funktionierendes Git-Paket auf dem System, auf dem Sie die Entwicklung durchführen werden.

Erstellen Sie Ihr eigenes Github-Konto.

Du kannst das Hubzilla-Repository von https://framagit.org/hubzilla/core.git forken/klonen.
Folgen Sie den Anweisungen hier: [http://help.github.com/fork-a-repo/](http://help.github.com/fork-a-repo/), um Ihren eigenen Tracking-Fork auf github zu erstellen und zu verwenden.

Gehen Sie dann auf Ihre Github-Seite und erstellen Sie eine „Pull-Anfrage“, wenn Sie bereit sind, uns mitzuteilen, dass Ihre Arbeit zusammengeführt werden soll.

**Wichtig**

Bitte ziehen Sie alle Änderungen aus dem Projekt-Repository ein und fügen Sie sie mit Ihrer Arbeit zusammen **, bevor** Sie einen Pull-Request erstellen. Wir behalten uns das Recht vor, jeden Patch abzulehnen, der zu einer großen Anzahl von Merge-Konflikten führt. Dies gilt insbesondere für Sprachübersetzungen, bei denen wir möglicherweise nicht in der Lage sind, die feinen Unterschiede zwischen den konfligierenden Versionen zu verstehen.

**Testen** Sie außerdem **Ihre Änderungen**. Gehen Sie nicht davon aus, dass eine einfache Korrektur nicht etwas anderes kaputt macht. Bitten Sie nach Möglichkeit einen erfahrenen Red-Entwickler, den Code zu überprüfen. 

Weitere Dokumentation finden Sie auf den Github-Wikiseiten unter: https://github.com/friendica/red/wiki

**Konsensbildung**

Codeänderungen, die einen offensichtlichen Fehler beheben, sind ziemlich einfach. Wenn Sie zum Beispiel auf „Speichern“ klicken und das, was Sie zu speichern versuchen, nicht gespeichert wird, ist es ziemlich offensichtlich, was das beabsichtigte Verhalten sein sollte. Bei der Entwicklung von Funktionsanfragen kann es vorkommen, dass eine große Anzahl von Community-Mitgliedern davon betroffen ist und dass andere Mitglieder der Community nicht mit der Notwendigkeit der Funktion oder der von Ihnen vorgeschlagenen Implementierung einverstanden sind. Es kann sein, dass sie etwas nicht als Fehler oder als wünschenswerte Funktion ansehen.

Wir ermutigen zur Konsensbildung innerhalb der Gemeinschaft, wenn es um eine Funktion geht, die als kontrovers angesehen werden könnte oder wenn es keine einstimmige Entscheidung gibt, dass die vorgeschlagene Funktion der richtige Weg ist, um die Aufgabe zu erfüllen. Die erste Anlaufstelle für Ihre Ideen ist [Channel One](https://zothub.com/channel/one)]. Andere haben vielleicht Anregungen oder können Sie auf Aspekte Ihres Konzepts hinweisen, die in unserem Umfeld problematisch sein könnten. Vielleicht stoßen Sie aber auch auf Widerstand gegen Ihren Plan. Das bedeutet nicht, dass Sie aufhören und/oder die Funktion ignorieren sollten. Hören Sie sich die Bedenken der anderen an und versuchen Sie, alle Probleme bei der Umsetzung zu lösen. 

Es gibt Stellen, an denen Widerstände nicht ausgeräumt werden können. In diesen Fällen sollten Sie in Erwägung ziehen, Ihre Funktion **optional** zu machen oder ein nicht standardmäßiges Verhalten vorzusehen, das ausdrücklich aktiviert werden muss. Diese Technik kann oft verwendet werden, wenn eine Funktion eine bedeutende, aber nicht einstimmige Unterstützung hat. Diejenigen, die die Funktion wünschen, können sie einschalten, und diejenigen, die sie nicht wollen, können sie ausgeschaltet lassen.
Wenn eine Funktion andere Netzwerke oder Websites nutzt oder nur von einer kleinen Minderheit der Gemeinschaft als wünschenswert angesehen wird, sollten Sie in Erwägung ziehen, die Funktion über ein Addon oder Plugin verfügbar zu machen. Auch hier gilt, dass diejenigen, die die Funktion nicht wünschen, sie auch nicht installieren müssen. Plugins sind relativ einfach zu erstellen und „Haken“ können leicht hinzugefügt oder geändert werden, wenn die aktuellen Haken nicht das tun, was nötig ist, damit Ihr Plugin funktioniert. 