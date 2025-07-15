### Ein abgeleitetes Thema erstellen 

**Lektion 1**
Ein abgeleitetes Thema übernimmt die meisten Einstellungen des „übergeordneten“ Themas und ermöglicht es Ihnen, einige Dinge nach Ihren Wünschen zu ändern, ohne ein ganzes Themenpaket zu erstellen. 
Um ein abgeleitetes Thema zu erstellen, wählen Sie zunächst einen Namen. In unserem Beispiel nennen wir unser Thema „mytheme“. Hoffentlich werden Sie etwas kreativer sein. Wo immer Sie in diesem Dokument „mytheme“ sehen, ersetzen Sie es durch den von Ihnen gewählten Namen.

**Verzeichnisstruktur**

Zuerst müssen Sie eine Verzeichnisstruktur für das Thema erstellen. Wir werden es einfach halten. Wir brauchen ein php-Verzeichnis und ein css-Verzeichnis. Hier sind die Unix/Linux-Befehle, um dies zu tun. Gehen Sie davon aus, dass 'mywebsite' Ihr oberster Hubzilla-Ordner ist. 

```
cd mywebsite
mkdir view/theme/mytheme
mkdir view/theme/mytheme/css
mkdir view/theme/mytheme/php
```

Sehr gut. Jetzt brauchen wir noch ein paar Dateien. Die erste ist Ihre Theme-Info-Datei, die das Theme beschreibt.
Sie heißt view/theme/mytheme/php/theme.php (cleverer Name, oder?)
Fügen Sie darin die folgenden Informationen ein - bearbeiten Sie sie nach Bedarf

```
<?php

/**
 * * Name: Mytheme
 * * Description: Beispiel Abgeleitetes Thema
 * * Version: 1.0
 * * Author: Ihr Name
 * * Compat: Red [*]
 *
 */

Funktion mytheme_init(&$a) {

    App::$theme_info['extends'] = 'redbasic';
}
```

Denken Sie daran, die Funktion mytheme_init mit dem Namen Ihres Themas zu benennen. In diesem Fall werden wir das Thema 'redbasic' erweitern.

Erstellen Sie nun eine weitere Datei. Wir nennen dies eine PCSS-Datei, aber in Wirklichkeit ist es eine PHP-Datei.

Die Datei heißt view/theme/mytheme/php/style.php
Fügen Sie darin Folgendes ein:

```
<?php

require_once('view/theme/redbasic/php/style.php');

echo @file_get_contents('view/theme/mytheme/css/style.css');
```

Das war's. Dies weist die Software an, zuerst die PCSS-Informationen für das redbasic-Theme zu lesen und dann unsere CSS-Datei zu lesen, die nur aus den Änderungen besteht, die wir an unserem übergeordneten Thema (redbasic) vornehmen wollen.

Erstellen Sie nun die eigentliche CSS-Datei für Ihr Thema. Legen Sie sie in view/theme/mytheme/css/style.css ab (wo wir der Software gerade gesagt haben, dass sie danach suchen soll). In unserem Beispiel ändern wir nur die Hintergrundfarbe des Körpers, damit Sie sehen können, dass es funktioniert. Sie können jedes CSS verwenden, das Sie möchten. 

```
body {
    background-color: #DDD;
}
```

Sie haben soeben erfolgreich ein abgeleitetes Thema erstellt. Dieses muss im Adminbereich „Themen“ aktiviert werden und kann dann von jedem auf der Website verwendet werden, indem es unter Einstellungen->Anzeigeeinstellungen als Standardthema ausgewählt wird.

**Lektion 2**

Wenn Sie die redbasic-Schemata für Ihr abgeleitetes Thema verwenden möchten, müssen Sie etwas mehr tun.

Machen Sie alles wie oben, aber erstellen Sie nicht view/theme/mytheme/php/style.php, sondern kopieren Sie stattdessen view/theme/redbasic/php/style.php nach view/theme/mytheme/php/style.php. Ändern Sie diese Datei und entfernen Sie diese beiden Zeilen (oder kommentieren Sie sie aus):

```
if(local_channel() && App::$channel && App::$channel['channel_theme'] != 'redbasic')
    set_pconfig(local_channel(), 'redbasic', 'schema', '---');
```

Fügen Sie außerdem diese Zeile am Ende ein:

```
echo @file_get_contents('view/theme/mytheme/css/style.css');
```

Um den Schemaselektor anzuzeigen, müssen Sie view/theme/redbasic/tpl/theme_settings.tpl nach view/theme/mytheme/tpl/theme_settings.tpl kopieren. Ändern Sie diese Datei und ersetzen Sie die Zeilen:

```
{{if $theme == redbasic}}
{{include file=„field_select.tpl“ field=$schema}}
{{/if}}
```

mit:

```
{{include file=„field_select.tpl“ field=$schema}}
```