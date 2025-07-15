### CLI Tools (utils)

Wer als Administrator Zugang zur Shell hat, kann weitere kleine CLI Tools verwenden, welche sich im Verzeichnis "utils" finden.

#### addons

Mit dem Script addons können Sie sich anzeigen lassen, welche Addons installiert und welche vorhanden sind. Außerdem können Sie Addons installieren und deinstallieren, sowie alle Addons neu installieren.

- `util/addons list`
  Anzeige aller installierter Addons
- `util/addons list all`
  Anzeige aller Addons , die installiert sind (*) und solcher, die aufgrund von Inkompatibilität deaktiviert sind (!)
- `util/addons install foo`
  Installiere das Addon mit dem Namen "foo"
- `util/addons uninstall foo`
  Deinstalliere das Addon mit dem Namen "foo"
- `util/addons reinstall`
  Reinstalliere alle Addons

#### admins

Das Script admins ermöglicht es Ihnen, sich sämtliche Admins des Hub anzeigen zu lassen, sowie weitere Admins hinzuzufügen und bestehende Admins zu entfernen.

- `util/admins`
- `util/admins list`
- `util/admins add <account_id>`
- `util/admins remove <account_id>`

#### config / pconfig

Siehe: [Erweiterte Konfigurationen für Administratoren](/help/de/adminmanual/advanced_configurations.md)

#### connect

Mit connect können Sie eine Verbindung zwischen einem Kanal Ihres Hubs mit einem anderen Kanal herstellen.

- `util/connect <channel_id> <channel@hub>`
- `util/connect <channel_address> <channel@hub>`

#### dcp

Mit dcp können Sie Dateien in den Store-Bereich eines Kanals auf Ihrem Hub kopieren.

- `util/dcp <quelle> <zielverzeichnis>` wobei Zielverzeichnis `store/$nickname/path` oder `$nickname/path` sein muss.

#### dmkdir

Mit dmkdir können Sie im Store-Bereich eines Kanals auf Ihrem Hub ein Unterverzeichnis anlegen.

- `util/dmkdir <directory>` wobei Directory `store/$nickname/path/<directory>` oder `$nickname/path/<directory>` sein muss.

#### fresh (The Freaking REd Shell)

Dies funktioniert nur unter Unix/Linux. Wenn das readline-Modul installiert ist, verwenden es dieses für die Eingabe, ansonsten liest es nur von stdin und schreibt nach stdout.
Die Befehle werden der Reihe nach abgearbeitet, bis der Befehl „exit“, „quit“ oder das Ende der Datei erreicht ist.

Kommandos:

- `version`
  Zeigt die aktuelle Fresh-Version an.
- `login <E-Mail-Adresse>`
  Fragt nach einem Passwort und authentifiziert `<E-Mail-Adresse>` als den aktuellen 
  Benutzer.
- `finger <Kanal-Adresse>`
  Führt einen Lookup von `<Kanal-Adresse>` durch und meldet das Ergebnis.
- `channel <Kanal-Nickname>`
  Schaltet den aktuellen Kanal auf den Kanal mit dem angegebenen Spitznamen um.
- `conn [<id1> <id2> ...]`
- Ohne Argumente listet dies alle Verbindungen des aktuellen Kanals mit einer ID auf.
  Wenn IDs angegeben werden, werden die Details der einzelnen Verbindungen angezeigt.

#### hz

Einfaches, minimalistisches Kommandozeilentool, um den Status über die API an hubzilla zu senden. Erfordert curl.

`hz [<Konfigurationsdatei>]`

hz benötigt eine Konfigurationsdatei. Sie können entweder eine Datei `.hubzilla` verwenden und den Parameter `<Konfigurationsdatei>` weglassen oder eine Konfigurationsdatei unter einem beliebigen Namen im Hubzilla-Verzeichnis erstellen, deren Namen Sie dann bei Aufruf von hz angeben.

Format der Konfigurationsdatei:

```
USER=<Ihr Benutzername>
PASS=<Ihr Passwort>
HUB=<Domain Ihres Hubs>
```

Anschließend können Sie Ihr Posting eingeben und die Eingabe mit Strg-D abschließen.

#### storageconf

Mit storageconf können Sie den Speicherort für Thumbnails festlegen (Dateisystem oder Datenbank), sowie die aktuelle Konfiguration abfragen.

- `util/storageconv stats`
  Zeigt den aktuell eingestellten Status
- `util/storageconv fs`
  Verschiebt die Thumbnails von der Datenbank ins Dateisystem
- `util/storageconv db`
  Verschiebt die Thumbnails vom Dateisystem in die Datenbank

#### thumbrepair

thumbrepair erstellt die lokalen Thumbnails neu.