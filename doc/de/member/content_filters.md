### Inhaltsfilter

Mit dem Inhaltsfilter können Sie eingehende Inhalte aus allen Quellen oder von bestimmten Verbindungen filtern. Die Filterung kann auf Wörtern, Tags, regulären Ausdrücken oder Sprache basieren.

In den Stream-Einstellungen können Sie globale Filter für alle eingehenden Inhalte festlegen.

![contfilter01](/help/de/member/pic/contfilter01.png)

Sie können auch individuelle Filter für jede Ihrer Verbindungen festlegen. Wählen Sie „Bearbeiten“ für eine beliebige Verbindung und suchen Sie dann unter „Filter für den Inhalt“.

![contfilter02](/help/de/member/pic/contfilter02.png)

Wenn Sie Filter unter **„Beiträge mit diesem Text nicht importieren**“ („Filterung ablehnen“) hinzufügen, werden diese zuerst angewendet. Alle übereinstimmenden Inhalte werden entfernt.

Anschließend werden alle Filter unter **„Nur Beiträge mit diesem Text importieren“** („Filterung akzeptieren“) angewendet. Nur übereinstimmende Inhalte werden beibehalten und alles, was nicht übereinstimmt, wird entfernt.

Im Allgemeinen werden Sie den einen oder den anderen verwenden und selten, wenn überhaupt, beide gleichzeitig.



#### Grundlegende Filter

**TEXT**

Jeder Text, der nicht **mit einem der folgenden Zeichen beginnt**: '#', '$', '?', '/', '@', 'lang=', 'lang!=' führt eine Textübereinstimmung durch, bei der die Groß-/Kleinschreibung nicht berücksichtigt wird.

Beispiel: `apple` (stimmt mit "apple", "APPLE", "Apples", "pineapple", "applesauce", etc. überein)

Beispiel: `low sub` (stimmt mit "low sub", "low sub-zero", "Yellow Submarine", etc. überein)

HINWEIS: Achten Sie darauf, keine kurzen Zeichenfolgen zu verwenden, die mit vielen  unterschiedlichen Wörtern übereinstimmen. Sie können Reguläre Ausdrücke verwenden (siehe unten).



**SPRACHE**

**lang=** Übereinstimmung der Sprache (sofern diese identifiziert werden kann) Beispiel: `lang=de` (stimmt mit deutschsprachigen Inhalten überein)

**lang!=** Alles außer dieser Sprache (sofern diese identifiziert werden kann) Beispiel: `lang!=en` (stimmt mit nicht-englischen Inhalten überein)



**HASHTAG**

**#**

Hashtag abgleichen

Beispiel: `#cats`

**#>n**

Größer als die Gesamtzahl der Hashtags abgleichen

Beispiel: `#>10`



**ERWÄHNUNG**

**@**
Erwähnung stimmt überein
Beispiel: `@alice`

**@>n**
Größer als die Gesamtzahl der Erwähnungen
Beispiel: `@>10`



**KATEGORIE**

**$**
Übereinstimmung der Kategorie
Beispiel: `$Science`



**ITEMS UND FELDER**

**?**
Nachstehend unter „**Erweiterte Filter“** erläutert



**REGULÄRE AUSDRÜCKE (REGEX)**

**/**
Übereinstimmung einer "regular expression". Zahlreiche Online-Hilfeseiten wie [Regular-Expressions.info](https://www.regular-expressions.info/) und [regexr.com](https://regexr.com/) bieten Unterstützung bei der Verwendung von regulären Ausdrücken.

Beispiel: `/gr[ae]y/` (entspricht "gray" and "grey")
Beipiel: `/\b[Ww]ar\b/` (entspricht dem gesamten Worten "war" und "War", jedoch nicht "wars", "warning", "software", etc.)



#### Erweiterte Filter

**ITEMS**

**?**
Sie können eine Zeichenketten-/Zahlen-/Array-/Boolesche-Abgleichung für die Datenbankfelder eines Elements (eines Beitrags, eines Kommentars usw.) durchführen. Eine vollständige Auflistung würde den  Rahmen dieses Dokuments sprengen, aber sehen Sie sich `install/schema_mysql.sql` an und suchen Sie nach `CREATE TABLE IF NOT EXISTS `item``. Hier ist ein paar Beispiele:

- `body` (Inhalt der Nachricht)
- `verb` (in der Regel identisch mit dem ActivityPub-Aktivitätstyp, z. B. Create, Listen)
- `obj_type` (normalerweise dasselbe wie das ActivityPub-Objekt „object.type“, z. B. Note, Event)
- `item_thread_top` (erster Beitrag in einem Thread, boolean)
- `item_private` (0 = öffentliche Nachricht, 1 = eingeschränkte Nachricht, 2 = Direktnachricht)
- `ua` (Benutzeragenten-Zeichenfolge des Zustellungsagenten – benutzerdefiniertes Artikelattribut, das nicht in der Datenbank enthalten ist)
- ...etc...

Verfügbare Vergleichsoperatoren sind:

- `?foo ~= baz` -- item.foo enthält den String 'baz'
- `?foo == baz` -- item.foo ist der String 'baz'
- `?foo != baz` -- item.foo ist nicht der String 'baz'
- `?foo // regex` -- item.foo erfüllt die regular expression 'regex' - automatisch in Schrägstriche eingeschlossen, es sei denn, Sie schließen es mit einem anderen Zeichen ein, z. B. '=regex='.
- `?foo >= 3` -- item.foo ist größer oder gleich 3
- `?foo > 3` -- item.foo ist größer 3
- `?foo <= 3` -- item.foo ist kleiner oder gleich 3
- `?foo < 3` -- item.foo ist kleiner 3
- `?foo {} baz` -- 'baz' ist ein Array-Element in item.foo
- `?foo {*} baz` -- 'baz' ist ein Array-Schlüssel in item.foo
- `?foo` -- wahre Bedingung für item.foo
- `?!foo` -- falsche Bedingung für item.foo (Die Werte 0, '', ein leeres Array, und ein ungesetzter Wert werden alle zu falsch aufgelöst)

Beispiel: `?verb == Announce` (entspricht ActivityPub "boosts")



**FELDER**

**?+**
Übereinstimmung von ActivityPub/ActivityStreams-Objekten. Dies ist buchstäblich das kopierte Objektattribut aus der eingehenden Aktivität.

Unterstützt alle oben aufgeführten Operatoren.

Beispiele: 

`?+type == Question` -- entspricht ActivityPub Umfragen 

`?+attributedTo ~= spammer` -- entspricht ActivityPub attributedTo: https://o3r56t3c.example.com/u/cryptospammer2355
