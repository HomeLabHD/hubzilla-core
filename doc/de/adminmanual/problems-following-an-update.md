### Probleme nach einer Aktualisierung 

Gut 90 % aller Fehler, die unmittelbar nach der Aktualisierung des Codes auf die neueste Version auftreten, sind einfache Cache-Fehler der einen oder anderen Art. Wenn Sie ein Update durchführen und feststellen, dass etwas sehr Offensichtliches kaputt ist - z. B. dass Ihre Matrix-Seite nicht geladen wird, dass Benachrichtigungen fehlen oder dass Kommentarboxen fehlen - dann ist es wahrscheinlich gar kein Fehler. Das Brechen grundlegender Funktionen ist die Art von Dingen, die Entwickler eher bemerken.
Wenn Ihnen das passiert, können Sie einige einfache Schritte unternehmen, bevor Sie sich an die Support-Foren wenden:

**Browser-Cache**
Symptome: Menüs lassen sich nicht ausklappen, die ACL-Auswahl öffnet sich nicht, die Fortschrittsanzeige wird nicht angezeigt (oder läuft in einer Endlosschleife), Matrix- und Kanalseiten werden nicht geladen.
Erzwingen Sie das Neuladen der Seite. Gelegentlich, aber sehr, sehr selten, müssen Sie auch die Sitzungsdaten löschen, was durch einen Neustart des Browsers erreicht wird.

**FastCGI**
Symptome: Falsche Variablen. Die grundlegende Benutzeroberfläche funktioniert meistens, zeigt aber falsche Inhalte an oder es fehlen ganz Inhalte. 
Wenn Sie php-fpm verwenden, lässt sich dieses Problem in der Regel beheben mit 

```
service php-fpm neu starten
```

**Smarty-Cache**
Die Symptome: 

1. Weißer Bildschirm des Todes. Dies tritt vor allem auf den Einstellungs- und Verwaltungsseiten auf.
2. Fehlende Icons, Registerkarten, Menüs oder Funktionen.

Wir verwenden die Smarty3-Vorlagen-Engine, um Seiten zu generieren. Diese Vorlagen werden kompiliert, bevor sie angezeigt werden. Es kann vorkommen, dass eine neue oder geänderte Vorlage die alte kompilierte Version nicht überschreibt. Um den Smarty-Cache zu löschen, löschen Sie alle Dateien in store/[data]/smarty3/compiled **, aber nicht das Verzeichnis selbst**. Die Vorlagen werden dann bei ihrem nächsten Zugriff neu kompiliert.

**Theme-Probleme**
Es gibt viele Themes für Hubzilla. Nur Redbasic wird offiziell von den Kernentwicklern unterstützt. Dies gilt *auch dann, wenn ein Kernentwickler zufällig ein weiteres Theme unterstützt*. Das bedeutet, dass neue Funktionen garantiert nur in Redbasic funktionieren.

Redbasic verwendet einige Javascript-Bibliotheken, die in anderen Themes anders oder gar nicht verwendet werden. Dies bedeutet, dass neue Funktionen möglicherweise nur in Redbasic richtig funktionieren. Bevor Sie ein Problem melden, sollten Sie daher zu Redbasic wechseln, um zu sehen, ob das Problem dort existiert. Wenn das Problem verschwindet, handelt es sich nicht um einen Fehler, sondern um ein Theme, das nicht auf dem neuesten Stand ist.

Sollten Sie das Problem dann den Theme-Entwicklern melden? Nein. Theme-Entwickler verwenden ihre Themes. Die Chancen stehen gut, dass sie es wissen. Geben Sie ihnen zwei oder drei Tage Zeit, um sich auf den neuesten Stand zu bringen, und melden Sie das Problem, wenn es *dann* immer noch nicht behoben ist. Für diese Situation gibt es zwei Lösungsmöglichkeiten. Erstens können Sie vorübergehend Redbasic verwenden. Zweitens sind die meisten Themes auch Open Source - öffnen Sie einen Pull Request und machen Sie sich einen Freund.