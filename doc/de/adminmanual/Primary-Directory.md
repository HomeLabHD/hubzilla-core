### Primäres Verzeichnis 

Standardmäßig verwendet Hubzilla verfügbare Verzeichnisse im Internet, die Ihnen weltweit verfügbare Kanäle anzeigen.

Es gibt bestimmte Szenarien, in denen Sie Ihren eigenen Verzeichnis-Server benötigen, mit dem Sie mehrere Hubs verbinden können. Dadurch werden die Kanäle, die in allen Ihren Hubs erscheinen, auf die Kanäle der Hubs beschränkt, die mit Ihrem Verzeichnis-Server verbunden sind.

#### Anleitungen zum Einrichten eines Hubs als primäres Verzeichnis für eine Reihe von privaten Hubs. 

- Öffnen Sie auf dem Hub, der der Directory Server sein wird, die Datei .htconfig.php und setzen Sie
  `App::$config['system']['directory_mode'] = DIRECTORY_MODE_PRIMARY;`

```
Standardmäßig sollte dies bereits **DIRECTORY_MODE_NORMAL** sein, also ändern Sie diese Zeile einfach in **DIRECTORY_MODE_PRIMARY**.
```

- Als Nächstes gehen Sie für jeden Hub (einschließlich des Verzeichnisservers) von einem Terminal aus in den Ordner, in dem er installiert ist, und führen Folgendes aus:

  `util/config system directory_realm YOURREALMNAME`
  (**YOURREALMNAME** kann jeder beliebige Name für Ihren Realm sein)

  dann:

  `util/config system realm_token THEPASSWORD`
  (**THEPASSWORD** ist das Passwort, das Sie für Ihren Realm verwenden möchten)
  **HINWEIS:** Verwenden Sie für jeden Hub denselben Realm-Namen und dasselbe Passwort.

- Führen Sie schließlich für jeden „Client“-Hub (von einem Terminal aus) aus:

  `util/config system directory_server https://theaddressofyourdirectoryserver.com`

Wenn Sie sich nun das Verzeichnis jedes Hubs ansehen, sollten nur die Kanäle angezeigt werden, die auf den Hubs in Ihrem Realm existieren. Ich habe bisher mit zwei Hubs getestet, und es scheint gut zu funktionieren. Channels, die in jedem Hub erstellt wurden, werden im Primärverzeichnis und anschließend im Verzeichnis aller Client-Hubs angezeigt.

#### Probleme 

Als ich den ersten Hub erstellte, lief er etwa eine Stunde lang, bevor ich ihn auf PRIMARY_MODE umstellte, und nachdem ich ihn umgestellt hatte, waren noch einige Kanäle aus der gesamten Matrix im Verzeichnis vorhanden. Ich habe sie aus der xchan-Tabelle gelöscht, und das scheint das Problem behoben zu haben. 