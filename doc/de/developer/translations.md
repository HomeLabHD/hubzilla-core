### Übersetzungen

Unsere Übersetzungen werden über Transifex verwaltet. Wenn Sie bei der Übersetzung von Hubzilla in eine andere Sprache helfen möchten, melden Sie sich auf transifex.com an, besuchen Sie [Transifex](https://www.transifex.com/Friendica/hubzilla/?f=&zid=pepecyb@hub.hubzilla.hu) und beantragen Sie die Mitgliedschaft in einem der bestehenden Sprachteams oder erstellen Sie ein neues. Benachrichtigen Sie einen der Hauptentwickler, wenn Sie ein Übersetzungsupdate haben, das zusammengeführt werden muss, oder fragen Sie, ob Sie es selbst zusammenführen können, wenn Sie mit Git und PHP vertraut sind. Wir haben eine Zeichenkettendatei namens 'messages.po', die gettext-kompatibel ist, und eine Handvoll E-Mail-Vorlagen, aus denen wir automatisch die Sprachdateien der Anwendung generieren.   

#### Der Übersetzungsprozess  

Die in der Benutzeroberfläche von Hubzilla verwendeten Strings werden bei [Transifex](https://www.transifex.com/Friendica/hubzilla/?f=&zid=pepecyb@hub.hubzilla.hu) übersetzt und dann
in das Git-Repository auf github aufgenommen. Wenn Sie bei der Übersetzung helfen möchten
einer Sprache helfen wollen, sei es bei der Korrektur von Begriffen oder bei der Übersetzung von Hubzilla in eine derzeit nicht unterstützte Sprache übersetzen möchten, registrieren Sie bitte ein Konto auf transifex.com und kontaktieren Sie dort das Übersetzungsteam.

Hubzilla zu übersetzen ist ganz einfach. Verwenden Sie einfach das Online-Tool auf transifex. Wenn Sie
Wenn Sie sich nicht mit Git & Co. herumschlagen wollen, ist das kein Problem, wir überprüfen den Status der Übersetzungen regelmäßig und importieren sie in den Quellbaum auf github, damit andere sie nutzen können.

Wir nehmen nicht jede Übersetzung von transifex in den Quellbaum auf, um eine eine verstreute und gestörte Gesamterfahrung zu vermeiden. Als ungefähre Schätzung haben wir eine Untergrenze von 50% übersetzter Strings, bevor wir die Sprache einbeziehen. Diese Grenze richtet sich nur nach der Menge der übersetzten Zeichenfolgen, wobei wir davon ausgehen dass die wichtigsten Zeichenfolgen für die Benutzeroberfläche zuerst von einem Übersetzungsteam übersetzt Team übersetzt werden. Wenn Sie der Meinung sind, dass Ihre Übersetzung vor dieser Grenze brauchbar ist, kontaktieren Sie uns bitte und wir werden die Arbeit Ihres Teams wahrscheinlich in den Quellbaum aufnehmen.

Wenn Sie Ihre Arbeit selbst in den Quellbaum aufnehmen möchten, können Sie dies gerne tun und kontaktieren Sie uns bei allen Fragen, die sich ergeben. Der Prozess ist einfach und Hubzilla wird mit allen notwendigen Werkzeugen geliefert.

Der Speicherort der übersetzten Dateien im Quellbaum ist

`/view/LNG-CODE/`

wobei LNG-CODE der Code der verwendeten Sprache ist, z. B. de für Deutsch oder fr für Französisch.
Für die E-Mail-Vorlagen (die *.tpl-Dateien) legen Sie sie einfach in das Verzeichnis und Sie sind fertig. Die übersetzten Strings kommen als „hmessages.po“-Datei von Transifex, die in die PHP-Datei übersetzt werden muss, die Hubzilla verwendet.  Dazu Sie die Datei in das oben genannte Verzeichnis und verwenden Sie das Dienstprogramm „po2php“ aus dem aus dem Verzeichnis util Ihrer Hubzilla-Installation.

Angenommen, Sie möchten die deutsche Lokalisierung konvertieren, die sich in view/de/hmessages.po steht, würden Sie wie folgt vorgehen.

1. Navigieren Sie an der Eingabeaufforderung in das Basisverzeichnis Ihrer

Hubzilla-Installation

2. Führen Sie das po2php-Skript aus, das die Übersetzung

in die Datei hstrings.php einfügt, die von Hubzilla verwendet wird.

`$> php util/po2php.php view/de/hmessages.po`

Die Ausgabe des Skripts wird in der Datei view/de/hstrings.php platziert, wo froemdoca es erwartet, so dass Sie Ihre Übersetzung sofort testen können.

3. Besuchen Sie Ihre Hubzilla-Seite und überprüfen Sie, ob sie in der Sprache ist, die Sie gerade übersetzt haben. Wenn nicht, versuchen Sie, den Fehler zu finden, wahrscheinlich gibt PHP Ihnen einen Hinweis im Log/Warnings.über den Fehler.

Zur Fehlersuche können Sie auch versuchen, die Datei mit PHP „auszuführen“. Dies sollte keine Ausgabe geben, wenn die Datei in Ordnung ist, aber vielleicht einen Hinweis für die Fehler in der Datei zu finden.

`$> php view/de/hstrings.php`

4. Übertragen Sie die beiden Dateien mit einer aussagekräftigen Commit-Nachricht in Ihr Git Repository, pushen Sie es zu Ihrem Fork des Hubzilla Repository auf github und stellen einen Pull Request für diesen Commit.

#### Dienstprogramme  

Zusätzlich zum po2php-Skript gibt es einige weitere Hilfsprogramme für die Übersetzung im Verzeichnis „util“ des Hubzilla-Quellbaums.  Wenn Sie nur Hubzilla in eine andere Sprache übersetzen wollen, werden Sie wahrscheinlich keines dieser Werkzeuge aber es gibt Ihnen eine Vorstellung davon, wie der Übersetzungsprozess von Hubzilla funktioniert.

Weitere Informationen finden Sie in der Datei utils/README.

#### Bekannte Probleme  

- Hubzilla verwendet die Spracheinstellung des Browsers des Besuchers, um die Sprache für die Benutzeroberfläche. Meistens funktioniert dies, aber es gibt einige bekannte Macken.

- Die frühen Übersetzungen basieren auf den Friendica-Übersetzungen, wenn Sie einige grobe Übersetzungen finden, lassen Sie es uns bitte wissen oder korrigieren Sie sie bei Transifex.
