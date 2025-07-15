### Anforderungen

- Apache mit aktiviertem mod-rewrite und „AllowOverride All“, damit Sie eine lokale .htaccess-Datei verwenden können. Einige Leute haben erfolgreich nginx und lighttpd verwendet. Beispielkonfigurations-Skripte sind für diese Plattformen in doc/install verfügbar. Apache und nginx haben die meiste Unterstützung.
- PHP 8.1 oder höher. Beachten Sie, dass in einigen Shared-Hosting-Umgebungen die *Kommandozeilenversion* von PHP von der *Webserverversion* abweichen kann
- *PHP-Befehlszeilenzugriff*, wenn register_argc_argv in der Datei php.ini auf true gesetzt ist und der Hosting-Provider keine Einschränkungen für die Verwendung von exec() und proc_open() hat.
- curl, gd (mit mindestens jpeg und png Unterstützung), pdo-mysql (oder pdo-postgres), mbstring, zip und openssl Erweiterungen. Die imagick-Erweiterung ist nicht erforderlich, wird aber empfohlen.
- Die xml-Erweiterung ist erforderlich, wenn Sie webdav verwenden möchten.
- eine Art von E-Mail-Server oder E-Mail-Gateway, so dass PHP mail() funktioniert.
- Ein unterstützter Datenbankserver. Die unterstützten Datenbanken sind:
  - Mysql Version 8.0.22 oder höher
  - MariaDB Version 10.4 oder höher
  - PostgreSQL Version 12 oder höher
- Fähigkeit, Aufträge mit Cron zu planen.
- Die Installation in einer Top-Level-Domain oder Sub-Domain (ohne Verzeichnis/Pfad-Komponente in der URL) ist ERFORDERLICH.