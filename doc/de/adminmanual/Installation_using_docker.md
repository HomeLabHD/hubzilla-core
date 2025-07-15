### Installation mittels Docker

Es besteht die Möglichkeit, Hubzilla komfortabel und bequem als Docker-Container zu installieren. Dafür bietet Saiwal ([sk@hub.utsukta.org](https://hub.utsukta.org/channel/sk)) ein vorkonfigurierte Umgebung für einen Hubzilla-Container an.

Hauptfeatures sind:

- Einfache Bereitstellung: Verwenden Sie Docker Compose, um eine voll funktionsfähige Hubzilla-Instanz mit nur wenigen Befehlen einzurichten.
- Benutzerdefinierte Konfiguration: Passen Sie Ihre Hubzilla-Installation ganz einfach mit Umgebungsvariablen für SMTP, Datenbank und mehr an.
- Kontinuierliche Updates: Das Docker-Image ist so aufgebaut, dass es einfach aktualisiert werden kann, wenn neue Änderungen am Hubzilla-Kern oder seinen Abhängigkeiten vorgenommen werden.
- SMTP-Integration: Integrierte Unterstützung für den Versand von E-Mails mit ssmtp, wodurch die Konfiguration von E-Mail-Benachrichtigungen für Ihre Hubzilla-Instanz erleichtert wird.

Das Repository für den Container befindet sich hier: [**skprg/hubzilla-docker**](https://github.com/skprg/hubzilla-docker)

#### Das Image von Grund auf neu erstellen 

- Klonen Sie das Repository:

```
git clone https://github.com/skprg/hubzilla-docker.git
cd hubzilla-docker
```

- Konfigurieren Sie Ihre Umgebung: Aktualisieren Sie die Datei `docker-compose.yml` mit Ihren SMTP- und anderen Einstellungen.
- Erstellen und führen Sie den Container aus:

```
docker-compose up --build -d
```

#### Vorgefertigtes Image verwenden 

- Ersetzen Sie die folgenden Zeilen in `docker-compose.yml`

```
build:
      context: .
      Dockerfile: Dockerfile
```

mit

```
image: ghcr.io/skprg/hubzilla-docker:latest
```

- Starten Sie den Container:

```
docker compose up -d
```

Greifen Sie auf Ihre Hubzilla-Instanz zu: Navigieren Sie zu http://localhost (oder der entsprechenden URL), um Ihre Hubzilla-Instanz anzuzeigen.

#### Weitere Hinweise / Update / Tipps

Weitere Hinweise, Upgrade-Anweisungen und Tipps finden Sie im oben verlinkten Repository.