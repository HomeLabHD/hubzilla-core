### Dokumentation erstellen 

Um eine Dokumentation zu erstellen, müssen Sie nur ein paar Wörter in eine geschickte Reihenfolge bringen und ihre Existenz einem Entwickler bekannt geben. Sie können dies buchstäblich überall tun, solange ein Entwickler es sehen kann. Sobald Sie es bekannt gemacht haben, wird es jemand für Sie einchecken. Sie sollten versuchen, proprietäre Formate oder Orte, die eine Authentifizierung mit anderen Methoden als Nomad erfordern, zu vermeiden, um einem Entwickler den Zugang zu erleichtern, aber selbst das ist keine strikte Voraussetzung.
Wenn Sie direkt beitragen wollen, ist das auch in Ordnung. Um direkt beizutragen, sollte die Dokumentation in einem der folgenden Formate vorliegen:

- Markdown
- BBCode
- HTML
- Einfacher Text
- Andere Formate sind ebenfalls erlaubt, aber die Unterstützung für das Format muss zuerst zu mod/help.php hinzugefügt werden.

Wenn Sie eine reine Textdatei bearbeiten, halten Sie bitte die Spaltenbreite auf 80. Dies liegt daran, dass reiner Text in Fällen verwendet wird, in denen wir keine funktionierende Installation haben - zum Beispiel die Installationsdokumentation - und es sollte einfach sein, diese mit einem CLI-Texteditor zu lesen.
Der Vorteil von Markdown ist, dass es für Menschen lesbar ist.

Der Vorteil von BBCode ist, dass es identitätsbewusst ist.

Wenn Sie also BBCode verwenden, versuchen Sie, das Beste daraus zu machen:

- Verwenden Sie gegebenenfalls ZRL-Links, um sicherzustellen, dass ein Link zu einer anderen Website die Authentifizierung beibehält und die identitätsbasierte Dokumentation funktioniert
- Verwenden Sie baseurl- oder observer.baseurl-Tags, wo es angebracht ist, anstelle von example.com für authentifizierte Betrachter.
- Unterstützen Sie nicht-authentifizierte Benutzer mit observer=0-Tags. Aufgrund historischer Versäumnisse tun wir dies derzeit nicht. Dies muss überall hinzugefügt werden.

**Übersetzungen**
Um die Dokumentation zu übersetzen oder in anderen Sprachen als Englisch bereitzustellen:

- Erstellen Sie ein Verzeichnis in doc/ mit Ihrem zweistelligen Ländercode, falls es noch nicht existiert (z.B. doc/de/ für Deutsch oder doc/fr/ für Französisch)
- Erstellen Sie ein Dokument mit demselben Dateinamen wie die englische Version, aber mit dem Inhalt in Ihrer eigenen Sprache. So können wir auf die englische Version zurückgreifen, wenn die Übersetzung für eine bestimmte Seite nicht vorhanden ist.

Um eine Dokumentation zu erstellen, für die es keine gleichwertige Datei auf Englisch gibt, können Sie eine neue Datei mit einem Namen Ihrer Wahl erstellen - allerdings müssen Sie auch eine lokalisierte Version der Indexseite (main.bb auf Englisch) bereitstellen, damit sie über das Menü zugänglich ist.