### Kanal-Verzeichnis

#### Schlüsselwörter 

Es gibt eine „Schlagwortwolke“ mit Schlüsselwörtern, die auf der Kanalverzeichnisseite erscheinen können. Wenn Sie diese Schlüsselwörter, die vom Verzeichnisserver bezogen werden, ausblenden möchten, können Sie das *Konfigurationswerkzeug* verwenden:

```
util/config system disable_directory_keywords 1
```

Wenn sich Ihr Hub im Standalone-Modus befindet, weil Sie sich nicht mit dem globalen Netz verbinden möchten, können Sie stattdessen sicherstellen, dass die Option *directory_server* system leer ist:

```
util/config system directory_server „“
```