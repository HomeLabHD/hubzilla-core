### Erweiterte Verzeichnissuche 

Die erweiterte Verzeichnissuche wird im „Expertenmodus“ auf der Seite Einstellungen => Zusätzliche Funktionen aktiviert.

Auf der Verzeichnisseite wird eine Option namens „Erweitert“ im Widget „Kanäle suchen“ (normalerweise in der Seitenleiste) angezeigt. Wenn Sie auf „Erweitert“ klicken, wird ein weiteres Suchfeld geöffnet, in das Sie erweiterte Suchanfragen eingeben können.
Zu den erweiterten Suchanfragen gehören

- name=xxx [Kanalname enthält xxx]
- address=xxx [Kanaladresse (Webbie) enthält xxx]
- locale=xxx [Gebietsschema (typischerweise 'Stadt') enthält xxx]
- region=xxx [Region (Bundesland/Territorium) enthält xxx]
- postcode=xxx [Postleitzahl enthält xxx]
- country=xxx [Ländername enthält xxx]
- gender=xxx [Geschlecht enthält xxx]
- marital=xxx [Familienstand enthält xxx]
- sexual=xxx [Sexuelle Präferenz enthält xxx]
- keywords=xxx [Schlüsselwörter enthalten xxx]

Es gibt viele Gründe, warum ein Treffer nicht das liefert, wonach Sie suchen, da viele Kanäle in ihrem (öffentlichen) Standardprofil keine detaillierten Informationen bereitstellen und viele dieser Felder Freitext-Eingaben in mehreren Sprachen zulassen - und dies kann schwierig sein, einen genauen Treffer zu erzielen. Zum Beispiel können Sie bessere Ergebnisse erzielen, wenn Sie jemanden in den USA mit 'country=u' finden (zusammen mit einigen seltsamen Kanälen aus Deutschland, Bulgarien und Australien), da dies in einem Profil als US, U.S.A, USA, Vereinigte Staaten, usw. dargestellt werden könnte.

Künftige Überarbeitungen dieses Tools könnten versuchen, einige dieser Schwierigkeiten zu beseitigen. 
Anfragen können mit 'und', 'oder' und 'und nicht' verbunden werden.

Begriffe, die Leerzeichen enthalten, müssen in Anführungszeichen gesetzt werden.

Beispiel: 

```
name=„charlie brown“ und country=canada und nicht gender=female
```