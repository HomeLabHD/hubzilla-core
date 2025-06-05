### Sie möchten Code beisteuern? 

**...und wissen nicht, wie Sie anfangen sollen...**

- Hubzilla debuggen (php auf dem Webserver),
- Code für das Projekt beisteuern,
- optional - das alles von einer virtuellen Maschine aus zu tun

Diese Anleitung wurde für Debian (Wheezy) als virtuelle Maschine auf Lubuntu (Ubuntu 14.0) als Host getestet.

#### Installation einer virtuellen Maschine (KVM) 

[Hier](https://wiki.debian.org/KVM) die Installationsanleitung für Linux Debian. Die Zusammenfassung:

1. KVM installieren

```
# apt-get install qemu-kvm libvirt-bin
```

1. füge dich der Gruppe libvirt hinzu 

```
# adduser <deinBenutzer> libvirt
```

1. Installieren Sie die Benutzeroberfläche zur Verwaltung der virtuellen Maschinen 

```
# apt-get install virt-manager
```

1. Laden Sie ein Betriebssystem herunter, das in der virtuellen Maschine laufen soll ([mini.iso](http://ftp.nl.debian.org/debian/dists/wheezy/main/installer-amd64/current/images/netboot/mini.iso))
2. Starten Sie den Virt-Manager

- Erstellen Sie eine neue virtuelle Maschine (klicken Sie auf das Symbol)
- Wählen Sie Ihr Iso-Image (soeben heruntergeladen) als Installationsquelle
- optional: konfigurieren Sie die neue vm: ram, cpu's,...
- Virtuelle Maschine starten > Ergebnis: Linux Debian startet in einem neuen Fenster.

1. (optional) Vermeiden Sie Netzwerkfehler nach dem Neustart des Host-Betriebssystems

```
# virsh net-start default
# virsh net-autostart standard
```

#### Apache-Webserver installieren 

Öffnen Sie ein Terminal und machen Sie sich zum root

```
su -l
```

Erstellen Sie die Standardgruppe für den Apache-Webserver

```
groupadd www-data
```

könnte bereits existieren

```
usermod -a -G www-data www-data
```

Prüfen Sie, ob das System wirklich auf dem neuesten Stand ist

```
apt-get update
apt-get upgrade
```

Optionaler Neustart der Dienste nach der Installation

```
reboot
```

Wenn Sie neu gestartet haben, machen Sie sich zum root

```
su -l
```

Installieren Sie Apache: 

```
apt-get install apache2 apache2-doc apache2-utils
```

Öffnen Sie den Webbrowser auf dem PC und überprüfen Sie [localhost](localhost) Sollte Ihnen eine Seite wie „It works“ anzeigen
(Quelle http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#)

#### PHP, MySQL und phpMyAdmin installieren 

##### PHP, MySQL 

```
su -l
apt-get install libapache2-mod-php8.2 php8.2 php-pear php8.2-xcache php8.2-curl php8.2-mcrypt php8.2-xdebug
apt-get install php8.2-mysql
apt-get install mysql-server mysql-client
```

das mysql-Passwort eingeben und notieren
Optional, da es bereits während der phpmyadmin-Einrichtung aktiviert wurde

```
phpenmod mcrypt
```

##### phpMyAdmin 

phpMyAdmin installieren

```
apt-get install phpmyadmin
```

Konfigurieren von phpmyadmin

- Wählen Sie apache2 aus (Tipp: benutzen Sie die Tabulator-Taste zum Auswählen)
- Datenbank für phpmyadmin mit dbconfig-common? konfigurieren: Wählen Sie Ja

(Quelle [http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#](http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#))

##### Aktivieren Sie Rewrite 

Bei der Standardinstallation von Apache2 ist mod_rewrite bereits installiert. Um zu prüfen, ob dies der Fall ist, überprüfen Sie die Existenz von `/etc/apache2/mods-available/rewrite.load`

```
nano /etc/apache2/mods-available/rewrite.load
```

(Sie sollten den Inhalt finden: `LoadModule rewrite_module` `/usr/lib/apache2/modules/mod_rewrite.so`) Um mod_rewrite zu aktivieren und zu laden, führen Sie die restlichen Schritte aus. Erstellen Sie einen symbolischen Link in `/etc/apache2/mods-enabled`

```
cd /var/www
a2enmod rewrite
```

Öffnen Sie dann die folgende Datei und ersetzen Sie jedes Vorkommen von „`AllowOverride None`“ durch „`AllowOverride all`“.

```
nano /etc/apache2/apache2.conf
```

oder

```
gedit /etc/apache2/sites-enabled/000-default
```

Starten Sie schließlich Apache2 neu.

```
service apache2 restart
```

##### Testen Sie die Installation 

```
cd /var/www
```

Erstellen Sie eine php-Datei, um die php-Installation zu testen

```
nano phpinfo.php
```

In die Datei einfügen:

```
<?php
  phpinfo();
?>
```

(STRG+0, ENTER, STRG+X speichern)
Webbrowser auf dem PC öffnen und `http://localhost/phpinfo.php` ausprobieren (Seite zeigt Infos zu php)
phpMyAdmin mit der MySQL-Datenbank verbinden 

```
nano /etc/apache2/apache2.conf
```

- CTRL+V... bis zum Ende der Datei
- Am Ende der Datei einfügen: (CTRL+0, ENTER, CTRL+X speichern)

```
Einfügen von /etc/phpmyadmin/apache.conf
```

Apache neu starten

```
/etc/init.d/apache2 neu starten
apt-get update
apt-get upgrade
reboot
```

phpMyAdmin
Webbrowser auf dem PC öffnen und `http://localhost/phpmyadmin` ausprobieren
(Quelle [http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#](http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#))

##### Erstelle eine leere Datenbank... die später von Hubzilla verwendet wird 

öffne den Webbrowser auf dem PC und probiere `http://localhost/phpmyadmin`
Erstelle eine leere Datenbank, zum Beispiel mit dem Namen „red“. Erstelle einen Datenbankbenutzer, z.B. „red“. Erteile dem Benutzer „red“ alle Rechte für die Datenbank „red“.
Notieren Sie sich die Zugangsdaten (Hostname, Benutzername, Passwort, Datenbankname).

##### Forken Sie das Projekt auf github 

Bitte folgen Sie den Anweisungen in der offiziellen [Dokumentation](http://git-scm.com/book/en/v2/GitHub-Contributing-to-a-Project) von git. Es ist eine gute Idee, das gesamte Handbuch zu lesen! Git unterscheidet sich in vielerlei Hinsicht von anderen Versionskontrollsystemen.
Jetzt sollten Sie

- ein Konto bei github.com erstellen
- fork `https://framagit.org/hubzilla/core`
- fork `https://framagit.org/hubzilla/addons`

Wenn Sie GIT nicht von der Kommandozeile aus benutzen wollen - es gibt ein nützliches Eclipse-Plugin namens „Eclipse Mylyn to GitHub connector“.

#### Installieren Sie Hubzilla und seine Addons 

##### Git auf Ihrem Computer / vm 

Sie sollten ein Konto auf GitHub erstellt und die Projekte geforkt haben, bevor Sie fortfahren.
Löschen Sie das Verzeichnis www

```
rm -R www/
```

Installieren Sie git (und optional git-gui, eine Client-Gui)

```
apt-get install git git-gui
```

##### Hubzilla und Addons herunterladen 

Laden Sie das Hauptprojekt hubzilla und hubzilla-addons herunter

```
git clone https://github.com/yourname/hubzilla www
cd www/
git clone https://github.com/yourname/hubzilla-addons addon
```

Erstelle diesen zusätzlichen Ordner

```
mkdir -p „store/[data]/smarty3“
```

Erstellen Sie .htconfig.php und machen Sie sie für den Webserver schreibbar

```
touch .htconfig.php
chmod ou+w .htconfig.php
```

Machen Sie den Benutzer www-data (Webserver) zum Eigentümer aller Projektdateien

```
cd ..
chown -R www-data:www-data www/
```

Fügen Sie sich selbst („surfer“ in diesem Beispiel) zur Gruppe www-data hinzu. Warum das? Wenn Sie später Dateien in eclipse oder einem anderen Editor bearbeiten wollen.
Dann machen Sie alle Dateien für die Gruppe www-data, in der Sie jetzt Mitglied sind, schreibbar.

```
cd www/
usermod -G www-data surfer
chmod -R g+w www/
```

Starten Sie den Computer (oder vm) neu
Wenn Sie immer noch nicht in der Lage sind, die Projektdateien zu ändern, können Sie die Mitglieder der Gruppe www-data überprüfen mit

```
cat /etc/group
```

##### Melden Sie sich als Admin an 

Öffnen Sie `http://localhost` und starten Sie die Matrix
Bevor Sie einen ersten Benutzer registrieren, schalten Sie die Registrierungsmails aus. Öffnen Sie `/var/www/.htconfig.php` und stellen Sie sicher, dass in dieser Zeile „0“ gesetzt ist

```
App::$config['system']['verify_email'] = 0;
```

Sie sollten in der Lage sein, die Datei als „Sie selbst“ zu ändern (anstelle von root oder www-data).

##### Cron und der Poller 

Wichtig! Lassen Sie den Poller laufen, um die jüngsten „öffentlichen“ Beiträge Ihrer Freunde zu erfassen. Richten Sie einen Cron-Job oder eine geplante Aufgabe ein, um den Poller alle 5-10 Minuten laufen zu lassen, um die jüngsten „öffentlichen“ Beiträge Ihrer Freunde zu erfassen

```
crontab -e
```

Fügen Sie  hinzu.

```
*/10 * * * * cd /var/www/; /usr/bin/php include/poller.php
```

Wenn du den Pfad zu PHP nicht kennst, gib ein

```
which php
```

#### Debuggen Sie den Server über eclipse 

##### Überprüfen Sie die Konfiguration von xdebug 

Sie sollten xdebug bereits in den vorhergehenden Schritten installiert haben

```
apt-get install php-xdebug
```

Xdebug konfigurieren
Öffnen Sie Ihr Terminal und geben Sie als root (su -l)

```
gedit /etc/php/mods-available/xdebug.ini
```

Wenn die Datei leer ist, versuchen Sie es an dieser Stelle

```
gedit /etc/php/conf.d/xdebug.ini
```

Dieser Befehl sollte den Texteditor gedit mit der Xdebug-Konfigurationsdatei öffnen Am Ende des Dateiinhalts fügen Sie den folgenden Text ein

```
xdebug.remote_enable=on
xdebug.remote_handler=dbgp
xdebug.remote_host=localhost
xdebug.remote_port=9000
```

Speichern Sie die Änderungen und schließen Sie den Editor. Geben Sie in Ihr Terminal ein, um den Webserver neu zu starten.

```
service apache2 neustart
```

##### Eclipse installieren und Debugging starten 

Installieren Sie Eclipse. Starten Sie Eclipse mit dem Standard-Arbeitsbereich (oder wie Sie möchten)
Installieren Sie das PHP-Plugin Menü > Hilfe > Neue Software installieren... Installieren Sie „PHP-Entwicklungswerkzeuge ...“
Optional - Installieren Sie das GitHub Connector Plugin Menü > Hilfe > Neue Software installieren... Installieren Sie „Eclipse Mylyn zu GitHub Connector“.
Konfigurieren Sie das PHP-Plugin Menü > Fenster > Präferenzen...

> Allgemein > Webbrowser > Ändern Sie auf „Externen Webbrowser verwenden“ PHP > Debug > Debug-Einstellungen > PHP Debugger > Ändern Sie auf „XDebug“.

Erstellen Sie ein neues PHP-Projekt Menü > Datei > Neues Projekt > Wählen Sie PHP > „PHP-Projekt“.

> Wählen Sie „Projekt an bestehendem Ort erstellen“ und „/var/www“.

Debugging starten Öffnen Sie index.php und „Debuggen als...“. Wählen Sie als Start-URL: „`http://localhost/`“
Erwartet:

- Der Webbrowser startet
- Der Debugger wird bei der ersten php-Zeile anhalten

#### Stellen Sie Ihre Änderungen über github zur Verfügung 

##### Vorbereitungen 

Es gibt eine entsprechende Seite in dieser Dokumentation: [zrl=[baseurl]/help/git_for_non_developers]Git for Non-Developers[/zrl]. Wie bereits erwähnt, ist es empfehlenswert, die offizielle Dokumentation [GitHub-Contributing-to-a-Project](http://git-scm.com/book/en/v2/GitHub-Contributing-to-a-Project) von Git zu lesen.
Eclipse hat ein nützliches Plugin für GIT: „Eclipse Mylyn to GitHub connector“.
Stellen Sie sicher, dass Sie Ihre Daten eingestellt haben

```
git config --global user.name „Ihr Name“
git config --global user.email „your@mail.com“
```

##### Dein erster Beitrag 

Erstelle einen beschreibenden Themenzweig

```
git checkout -b dev_beginning
```

Stellen Sie sicher, dass Ihr lokales Repository mit dem Hauptprojekt auf dem neuesten Stand ist. Fügen Sie das ursprüngliche Repository als Remote-Repository mit dem Namen „upstream“ hinzu, falls noch nicht geschehen

```
git remote add upstream https://framagit.org/hubzilla/core/
```

Holen Sie sich die neueste Arbeit von dieser Remote

```
git fetch upstream
git merge upstream/master
```

Tipp: Sie können die Zweige auflisten

```
git branch -v
```

Nehmen Sie Ihre Änderungen vor. In diesem Beispiel ist es eine neue Doc-Datei.
Prüfen Sie Ihre Änderungen

```
git status
```

Fügen Sie die neue Datei hinzu (stage)

```
git add doc/dev_beginner.bb
```

Übertragen Sie die Änderungen in Ihren lokalen Zweig. Dadurch wird ein Editor geöffnet, um eine Nachricht zu übermitteln.

```
git commit -a
```

Pushen Sie online zurück in den gleichen Themenzweig

```
git push
```

Nun können Sie zu Ihrem (Online-)Konto bei github gehen und den Pull Request erstellen.

##### Folgende Beiträge 

Für den Fall, dass die Hauptentwickler wollen, dass du etwas änderst. Holen Sie sich die neueste Arbeit aus dem entfernten Upstream/Master, um sicher zu sein, dass Sie die neuesten Änderungen haben.

```
git fetch upstream
git merge upstream/master
```

Nehmen Sie Ihre Änderungen vor, testen Sie sie, committen Sie (in das lokale Repository), pushen Sie (in das Online-Repository)

```
git status
git commit -a -m „Änderung des Zweigs hinzugefügt“
git push
```