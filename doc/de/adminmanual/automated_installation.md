### Automatisierte Installation über das Shell-Skript .homeinstall

Es gibt ein Shell-Skript in (`.homeinstall/hubzilla-setup.sh`), das Hubzilla und seine Abhängigkeiten auf einer frischen Installation von Debian stable installiert. Es sollte auf ähnlichen Linux-Systemen funktionieren, aber Ihre Ergebnisse können variieren.

#### Anforderungen 

Das Installationsskript wurde ursprünglich für einen kleinen Hardwareserver hinter Ihrem Heimrouter entwickelt. Es wurde jedoch auf mehreren Systemen mit Debian 9 getestet:

- Home-PC (Debian-9.2-amd64) und Rapberry-Pi 3 (Rasbian = Debian 9.3)
  - Internetanschluss und Router zu Hause
  - Mini-PC / Raspi an den Router angeschlossen
  - USB-Laufwerk für Backups
  - Frische Installation von Debian auf Ihrem Mini-PC
  - Router mit offenen Ports 80 und 443 für Ihr Debian

#### Überblick über die Installationsschritte 

1. `apt-get install git`
2. `mkdir -p /var/www/html`
3. `cd /var/www/html`
4. `git clone https://framagit.org/hubzilla/core.git .`
5. `nano .homeeinstall/hubzilla-config.txt`
6. `cd .homeeinstall/`
7. `./hubzilla-setup.sh`
8. `service apache2 neu laden`
9. Öffnen Sie Ihre Domain mit einem Browser und gehen Sie durch die anfängliche Konfiguration von Hubzilla.