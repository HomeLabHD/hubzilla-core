### **Verzeichnis-Konfiguration** 

Verzeichnisse in Hubzilla dienen dem Zweck, Mitglieder überall im Netzwerk zu suchen und zu finden. Sie werden auch verwendet, um „Bewertungen“ von Mitgliedern und Sites zu speichern und abzufragen. Die Verzeichnisdienste sind verteilt und gespiegelt, so dass ein Ausfall eines Dienstes nicht das gesamte Netzwerk lahmlegt oder stört.

#### **Standard-Konfiguration** 

Neue Sites, die als Verzeichnis-Clients arbeiten, wählen bei ihrem ersten Verzeichniszugriff automatisch aus einer fest programmierten Liste von Verzeichnisservern aus. Sie können diese Einstellung überprüfen oder außer Kraft setzen, indem Sie 

```
util/config system directory_server
```

eingeben.

Um einen anderen Server einzustellen, verwenden Sie diesen Befehl:

```
util/config system directory_server https://newdirectory.something
```

#### **Eigenständige Konfiguration** 

Einige Hubs möchten vielleicht im „Standalone“-Modus arbeiten und keine Verbindung zu externen Verzeichnisdiensten herstellen. Dies ist nützlich für isolierte Sites („off the gird“) und Test-Sites, kann aber auch für kleine Organisationen nützlich sein, die keine Verbindung mit anderen Sites im Netzwerk herstellen wollen. 

Um dies zu konfigurieren, suchen Sie bitte in Ihrer Datei .htconfig.php nach dem folgenden Text und stellen Sie die Konfiguration entsprechend ein.

```php
// Configure how we communicate with directory servers.
// DIRECTORY_MODE_NORMAL     = directory client, we will find a directory
// DIRECTORY_MODE_SECONDARY  = caching directory or mirror
// DIRECTORY_MODE_PRIMARY    = main directory server
// DIRECTORY_MODE_STANDALONE = "off the grid" or private directory services

App::$config['system']['directory_mode']  = DIRECTORY_MODE_STANDALONE;
```

#### **Konfiguration des Sekundärservers** 

Sie können Ihre Site auch als Sekundärserver konfigurieren. Dieser arbeitet als Spiegel des primären Verzeichnisses und ermöglicht die Verteilung der Last auf die verfügbaren Server. Es gibt kaum funktionale Unterschiede zwischen einem primären und einem sekundären Server, allerdings darf es nur *einen* primären Verzeichnisserver pro Realm geben (Realms werden später in diesem Dokument behandelt).

Bevor Sie sich für einen Verzeichnisserver entscheiden, sollten Sie ein aktives Mitglied des Netzwerks sein und über die Ressourcen und Zeit verfügen, um diese Dienste zu verwalten. In der Regel ist keine Verwaltung erforderlich, sondern die Anforderung dient eher der Stabilität, da der Ausfall eines Verzeichnisservers zu Problemen bei den Verzeichnis-Clients führen kann, die auf ihn angewiesen sind.

#### **Wechseln des Verzeichnisservers** 

Wenn ein Verzeichnisserver anzeigt, dass er kein Verzeichnisserver mehr ist, sollte dies von der Software erkannt werden, und die Konfiguration für diesen Server wird entfernt (geleert). Wenn er ohne Vorwarnung dauerhaft offline geht, erfahren Sie das nur, wenn die Mitglieder der Website melden, dass die Verzeichnisdienste nicht mehr verfügbar sind. Derzeit kann dies nur manuell durch den Site-Administrator repariert werden, indem er ein neues Verzeichnis auswählt und den Befehl

```
util/config system directory_server https://newdirectory.something
```

Wir hoffen, dass diese Option in Zukunft in der Administrationsoberfläche der Website aktiviert werden kann. 

### **Verzeichnis-Bereiche** 

Große Organisationen möchten vielleicht lieber Verzeichnis-„Realms“ als ein einzelnes, unabhängiges Verzeichnis verwenden. Der Standard-Realm heißt RED_GLOBAL. Durch die Erstellung eines neuen Realms hat Ihre Organisation die Möglichkeit, ihre eigene Hierarchie von primären und sekundären Servern und Clients zu erstellen. 

```
util/config system directory_realm MY_REALM
```

Ihr Realm *muss* ein primäres Verzeichnis haben. Erstellen Sie dieses zuerst. Setzen Sie dann den Realm auf allen Sites innerhalb Ihres Verzeichnisbereichs (Server und Clients) auf denselben Wert. 

Sie können auch einen „Sub-Realm“ einrichten, der unabhängig vom RED_GLOBAL-Realm (oder jedem anderen Realm) arbeitet, aber eine übergreifende Mitgliedschaft und die Möglichkeit bietet, Mitglieder des gesamten Verzeichnisraums zu suchen. Dies wurde bisher nur leicht getestet, also seien Sie bereit, mitzuhelfen und eventuell auftretende Probleme zu beheben. Ein Sub-Realm enthält seinen übergeordneten Realm im Realm-Namen.

```
util/config system directory_realm RED_GLOBAL:MY_REALM
```

#### **Realm-Zugang** 

Sie möchten vielleicht, dass Ihre Verzeichnisserver und -dienste nur von Mitgliedern Ihres Realms genutzt werden. Zu diesem Zweck muss ein Token oder Passwort für den Zugriff auf die Verzeichnisdienste des Realms angegeben werden. Dieses Token wird während der Übertragung nicht verschlüsselt, reicht aber aus, um den gelegentlichen Zugriff auf Ihre Verzeichnisserver zu verhindern. Folgendes muss für alle Sites (Clients und Verzeichnisserver) innerhalb des Realms konfiguriert werden:

```
util/config system realm_token my-secret-realm-password
```

### **Verzeichnis-Spiegelung** 

Die Spiegelung erfolgt mit einem täglichen Transaktionsprotokoll der Aktivitäten, das von den Verzeichnisservern gemeinsam genutzt wird. Bei Verzeichnis- und Profilaktualisierungen wird die Adresse des Kanals, der die Aktualisierung durchführt, übermittelt, und die anderen Verzeichnisserver prüfen diesen Kanal an seiner Quelle auf Änderungen. Wir können und sollten den Informationen, die uns von anderen Verzeichnisservern gegeben werden, nicht vertrauen. Wir überprüfen die Informationen immer an der Quelle. 

Bewertungen werden etwas anders gehandhabt - ein verschlüsseltes Paket (signiert von dem Kanal, der die Bewertung erstellt hat) wird zwischen den Servern übertragen. Diese Signatur muss überprüft werden, bevor die Bewertung akzeptiert wird. Bewertungen werden immer auf dem primären Verzeichnisserver veröffentlicht und von dort aus an alle anderen Verzeichnisserver weitergegeben. Aus diesem Grund kann es nur einen primären Server in einem Bereich geben. Wenn eine falsch konfigurierte Site behauptet, ein primäres Verzeichnis zu sein, wird sie im RED_GLOBAL-Bereich ignoriert. Für andere Realms gibt es derzeit keinen solchen Schutz. Seien Sie sich dessen bewusst, wenn Sie mit alternativen Realms arbeiten. 

Neu erstellte Verzeichnisserver erhalten keinen „Full Dump“, sondern werden aus Leistungsgründen und um die anderen Server im Netz möglichst wenig zu stören, langsam online gestellt. Es kann bis zu einem Monat dauern, bis ein neuer sekundärer Verzeichnisserver einen vollständigen Überblick über das Netzwerk bietet. Bitte fügen Sie keine sekundären Server zur fest kodierten Liste der Fallback-Verzeichnisserver hinzu, bevor er nicht mindestens einen Monat lang als Verzeichnis in Betrieb war.

Alle Channels sind so konfiguriert, dass sie ihren Verzeichnisserver einmal im Monat „anpingen“, und zwar zu einem zufälligen Zeitpunkt im Monat. Auf diese Weise kann das Verzeichnis tote Channels und Sites entdecken (sie hören auf zu pingen). In der Folge werden sie als tot oder unerreichbar markiert und mit der Zeit aus den Verzeichnisergebnissen entfernt.

Kanäle können so konfiguriert werden, dass sie vor dem Verzeichnis „versteckt“ werden. Diese Kanäle sind zwar noch im Verzeichnis vorhanden, aber nicht mehr durchsuchbar, und einige „sensible“ persönliche Informationen werden nicht gespeichert.