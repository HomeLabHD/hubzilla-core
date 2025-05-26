### Dateisystem-Layout

| Directory                 | Description                                                  |
| ------------------------- | ------------------------------------------------------------ |
| addon                     | optionale Addons/Plugins                                     |
| boot.php                  | Jeder Prozess verwendet dies, um die Anwendungsstruktur zu booten |
| doc                       | Hilfedateien                                                 |
| images                    | erforderliche Bilder                                         |
| include                   | Das „Modell“ in MVC - (Back-End-Funktionen), enthält auch PHP „Executables“ für die Hintergrundverarbeitung |
| index.php                 | Der Front-End-Controller für den Webzugang                   |
| install                   | Installations- und Upgrade-Dateien und DB-Schema             |
| library                   | Module von Drittanbietern (müssen lizenzkompatibel sein)     |
| mod                       | Steuerungsmodule basierend auf URL-Pfadnamen (z.B. http://sitename/foo lädt mod/foo.php) |
| mod/site/                 | Site-spezifische Mod-Overrides, die von Git ausgeschlossen sind |
| util                      | Übersetzungstools, Hauptdatenbank für englische Zeichenketten und andere verschiedene Dienstprogramme |
| version.inc               | enthält die aktuelle Version (die automatisch über cron für das Haupt-Repository aktualisiert und über git verteilt wird) |
| view                      | Themen- und Sprachdateien                                    |
| view/(css,js,img,php,tpl) | Standard-Theme-Dateien                                       |
| view/(en,it,es ...)       | Sprachstrings und Ressourcen                                 |
| view/theme/               | Einzelne benannte Themen, die (css,js,img,php,tpl) Overrides enthalten |