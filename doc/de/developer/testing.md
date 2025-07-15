### Testen 

Hubzilla verwendet [PHPUnit] für automatisierte Tests, oft auch *Unit-Tests* oder *Integrationstests* genannt. 
Die Tests werden als PHP-Klassen geschrieben und befinden sich im Unterverzeichnis `tests/unit` des Hubzilla-Hauptrepositorys. In diesem Leitfaden wird erklärt, wie man die Tests ausführt, wie die Tests strukturiert sind und wie Sie Ihre eigenen Tests schreiben können.

#### Ausführen der Testsuite 

##### Installieren der Abhängigkeiten 

Um die Tests ausführen zu können, müssen Sie die Entwickler-Abhängigkeiten installieren, die in der `composer.json-Datei` aufgeführt sind. Stellen Sie sicher, dass Sie [composer installiert] auf Ihrem Entwicklungssystem haben, und führen Sie dann aus:

```
% composer install
```

Dies wird phpunit und einige andere Abhängigkeiten installieren. Es wird auch einige Dateien im Unterverzeichnis `vendor/composer` aktualisieren. Dies mag ein wenig laut erscheinen, aber es ist wichtig, dass diese Änderungen *nicht* in das Repository übertragen werden!

**Warnung!** Übertragen Sie die Änderungen an den Dateien im Verzeichnis `vendor/composer` nicht in Ihr Projektarchiv!

##### Einrichten der Testdatenbank 

Wir haben ein Skript (`tests/create_test_db.sh`) beigefügt, das Ihnen hilft, die Testdatenbank einzurichten. Sie können es wie folgt ausführen:

```
% HZ_TEST_DB_TYPE=mysql ./tests/create_test_db.sh
```

Wenn Sie stattdessen PostgreSQL verwenden, erstellen Sie die Testdatenbank wie folgt:

```
% HZ_TEST_DB_TYPE=postgres ./tests/create_test_db.sh
```

Das Skript macht einige Annahmen über Ihre Einrichtung, aber alles ist über Umgebungsvariablen konfigurierbar. Falls Sie Anpassungen benötigen, finden Sie die Details im Quelltext des Skripts.

##### Ausführen der Tests 

Sobald Sie die Entwicklerabhängigkeiten installiert und die Testdatenbank eingerichtet haben, können Sie die Tests wie folgt ausführen:

```
% ./vendor/bin/phpunit -c tests/phpunit.xml
```

Auch hier macht die Konfiguration in `tests/phpunit.xml` standardmäßig einige Annahmen über Ihre Einrichtung, die mit Hilfe von Umgebungsvariablen außer Kraft gesetzt werden können. Siehe `tests/phpunit.xml` für die Details.

Sie können auch einen bestimmten Test oder eine bestimmte Gruppe von Tests ausführen, indem Sie das Argument `--filter` in PHPUnit verwenden:

```
% ./vendor/bin/phpunit -c tests/phpunit.xml --filter Autoname
```

Führt jeden Test mit der Bezeichnung „Autoname“ aus.

##### Erzeugen von Abdeckungsberichten 

Um Abdeckungsberichte zu generieren, benötigen Sie einen Treiber, der in der Lage ist, die Abdeckungsinformationen zu generieren, die PHPUnit sammelt. Wir empfehlen [Xdebug], aber sehen Sie sich die PHPUnit [Seite zur Codeabdeckungsanalyse](https://docs.phpunit.de/en/9.6/code-coverage-analysis.html) für Alternativen und Details an.

Wenn Xdebug richtig eingestellt ist, können Sie den Abdeckungsbericht wie folgt erzeugen:

```
% XDEBUG_MODE=coverage ./vendor/bin/phpunit -c ./tests/phpunit.xml
```

Dies erzeugt eine Reihe von HTML-Dateien in den Verzeichnissen des Unterverzeichnisses `tests/results/coverage/`.

Öffnen Sie die Datei „index.php“ in Ihrem Webbrowser, um die Statistiken zu sehen.

##### Fehlersuche 

Wenn Xdebug installiert ist, können Sie auch Step-Debugging und eine Reihe anderer Dinge tun, um die Tests zu debuggen und Informationen über ihre Ausführung zu erhalten. Lesen Sie auf den [Xdebug]-Seiten und in Ihrem bevorzugten Editor nach, wie Sie dies einrichten können.

#### Teststruktur und Organisation 

Die Tests befinden sich im Unterverzeichnis `tests/unit` und in weiteren Unterverzeichnissen, die mehr oder weniger das Verzeichnislayout des Core Code Repository widerspiegeln.

Tests werden als PHP-Klassen geschrieben, die die UnitTestCase-Klasse erweitern (zu finden in `tests/unit/UnitTestcase.php`). Der Dateiname und die darin enthaltene Testklasse sollten gleich lauten und *müssen* mit `Test.php` enden.

Beispiele sind:

- `tests/unit/includes/AccountTest.php`
- `tests/unit/Lib/ActivityTest.php`

Die Testklassen enthalten eine oder mehrere Testfunktionen, diese müssen öffentlich sein und mit dem Präfix `test` versehen werden.

Hier ist ein Beispiel:

```
use Zotlabs\Tests\Unit\UnitTestCase;

class AccountTest extends UnitTestCase {
    public function test_get_account_by_id_returns_existing_account() {
        $Konto = get_account_by_id(42);
        $this->assertNotFalse($account);
        $this->assertEquals(
            $this->fixtures['account'][0]['account_email'], 
            $account['account_email']
        );
    }
```

Beachten Sie, dass wir versuchen, den Namen der Testfunktion so aussagekräftig wie möglich zu gestalten.
Die Klasse kann auch eine beliebige Anzahl anderer Funktionen enthalten, um die Dinge ordentlich und übersichtlich zu halten. Diese können je nach Bedarf des Testcodes privat, geschützt oder öffentlich sein.
Die Ergebnisse und Artefakte der Testläufe werden im Verzeichnis `tests/results/` abgelegt. Dazu gehören in der Regel das Testprotokoll, der Code Coverage Report usw.

#### Hubzilla-spezifische Funktionen 

##### Testdatenbankzugriff: 

Idealerweise sollte es in der Lage sein, jeden Teil des Codes isoliert zu testen, wobei alle Abhängigkeiten durch Stubs, Mocks oder Fakes ersetzt werden sollten.

Dies ist bei einer Legacy-Codebasis wie Hubzilla, die nicht im Hinblick auf Testbarkeit geschrieben wurde, nicht möglich. Aus diesem Grund verwenden wir eine separate Testdatenbank und stellen sie dem zu testenden Code mit denselben Mechanismen zur Verfügung, die auch Hubzilla normalerweise verwendet.
Das bedeutet, dass jeder Code, der Datenbankabfragen ausführt, sich normal verhält, wenn er von einem Test aufgerufen wird.

Damit dies funktioniert, stellen wir vor der Ausführung jeder Testfunktion eine Verbindung zur Datenbank her. Wir laden auch einige anfängliche Daten in die Datenbank, um sicherzustellen, dass die Dinge wie erwartet funktionieren und dass wir einen bekannten Zustand der Datenbank haben, wenn der Test läuft.
Nach Beendigung des Tests wird die Testdatenbank gelöscht, so dass bei der nächsten Testdurchführung derselbe konsistente Zustand vorliegt.

All dies wird von der Basisklasse `UnitTestCase` gehandhabt.

##### Datenbank-Fixtures: 

Wir benötigen einige vorhersehbare Inhalte in der Datenbank, um Dinge wie die Protokollierung und andere Inhalte, die für die Tests im Allgemeinen nützlich sind, einzurichten. Dies sind Datenbank-Fixtures, die für jeden Testlauf in die Datenbank geladen werden.

Die Datenbankfixtures befinden sich im Verzeichnis `tests/unit/include/dba/_files` und bestehen aus YAML-Dateien, wobei jede Datei den Inhalt einer Datenbanktabelle mit dem gleichen Namen wie die Datei selbst darstellt.

Obwohl Datenbank-Fixtures sehr nützlich sind, versuchen wir, die Anzahl der Fixtures so gering wie möglich zu halten. In der Regel ist es besser, alle für einen bestimmten Test benötigten Inhalte im Test selbst hinzuzufügen.

##### Fakes: 

Fakes sind Klassen, die wir an den zu testenden Code übergeben können und die genauso aussehen und sich genauso verhalten wie die Originalklasse (für den zu testenden Code). Sie sind nützlich, wenn wir Objekte an den zu testenden Code übergeben können, da wir sie vom Testcode aus vollständig kontrollieren können.

Fakes befinden sich im Verzeichnis `tests/fakes`.