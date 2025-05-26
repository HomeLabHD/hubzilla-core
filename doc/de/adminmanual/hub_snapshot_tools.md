### Hub-Snapshot-Tools

Hubzilla-Entwickler müssen häufig zwischen Zweigen wechseln, die möglicherweise inkompatible Datenbankschemata oder Inhalte haben. Die folgenden beiden Skripte erstellen und restaurieren komplette Snapshots einer Hubzilla-Instanz, einschließlich des Hub-Web-Root und des gesamten Datenbankstatus. Jedes Skript benötigt eine Konfigurationsdatei namens `hub-snapshot.conf`, die sich im selben Ordner befindet und die spezifischen Verzeichnisse und Datenbankdetails Ihres Hubs enthält.

### Konfiguration 

Das Format der Konfigurationsdatei ist sehr streng. Es dürfen keine Leerzeichen zwischen dem Variablennamen und dem Wert stehen. Ersetzen Sie nur den Inhalt innerhalb der Anführungszeichen durch Ihre Konfiguration. Speichern Sie diese Datei als `hub-snapshot.conf` zusammen mit den Skripten.

```
# Ort des Hub-Root. Normalerweise ist dies der Speicherort des Hubzilla Repo-Klons.
HUBROOT=„/var/www/“
# Name der MySQL-Datenbank
DBNAME=„hubzilla“
# MySQL-Datenbank-Benutzer
DBUSER=„hubzilla“
# MySQL-Datenbank-Passwort
DBPWD=„akeufajeuwfb“
# Der Ziel-Snapshot-Ordner, in dem das Git-Repository initialisiert werden soll
SNAPSHOTROOT=„/root/snapshots/hubzilla/“
```

#### Snapshot 

Beispiel für die Verwendung:

```
sh hub-snapshot.sh my-hub.conf „Commit-Meldung für den Snapshot“ 
```

**hub-snapshot.sh**:

```
#!/bin/bash

if ! [ -f "$1" ]; then
    echo "$1 is not a valid file. Aborting..."
    exit 1
fi
source "$1"
#echo "$DBNAME"
#echo "$DBUSER"
#echo "$DBPWD"
#echo "$HUBROOT"
#echo "$SNAPSHOTROOT"
MESSAGE="snapshot: $2"

if [ "$DBPWD" == "" -o "$SNAPSHOTROOT" == "" -o "$DBNAME" == "" -o "$DBUSER" == "" -o "$HUBROOT" == "" ]; then
    echo "Required variable is not set. Aborting..."
    exit 1
fi

if [ ! -d "$SNAPSHOTROOT"/db/ ]; then
    mkdir -p "$SNAPSHOTROOT"/db/
fi
if [ ! -d "$SNAPSHOTROOT"/www/ ]; then
    mkdir -p "$SNAPSHOTROOT"/www/
fi

if [ ! -d "$SNAPSHOTROOT"/www/ ] || [ ! -d "$SNAPSHOTROOT"/db/ ]; then
    echo "Error creating snapshot directories. Aborting..."
    exit 1
fi

echo "Export database..."
mysqldump -u "$DBUSER" -p"$DBPWD" "$DBNAME" > "$SNAPSHOTROOT"/db/"$DBNAME".sql
echo "Copy hub root files..."
rsync -va --delete --exclude=.git* "$HUBROOT"/ "$SNAPSHOTROOT"/www/

cd "$SNAPSHOTROOT"

if [ ! -d ".git" ]; then
    git init
fi
if [ ! -d ".git" ]; then
    echo "Cannot initialize git repo. Aborting..."
    exit 1
fi

git add -A
echo "Commit hub snapshot..."
git commit -a -m "$MESSAGE"

exit 0
```

#### Restore

```
#!/bin/bash
# Restore hub to a previous state. Input hub config and commit hash

if ! [ -f "$1" ]; then
        echo "$1 is not a valid file. Aborting..."
        exit 1
fi
source "$1"
COMMIT=$2

if [ "$DBPWD" == "" -o "$SNAPSHOTROOT" == "" -o "$DBNAME" == "" -o "$DBUSER" == "" -o "$HUBROOT" == "" ]; then
        echo "Required variable is not set. Aborting..."
        exit 1
fi
RESTOREDIR="$(mktemp -d)/"

if [ ! -d "$RESTOREDIR" ]; then
    echo "Cannot create restore directory. Aborting..."
    exit 1
fi
echo "Cloning the snapshot repo..."
git clone "$SNAPSHOTROOT" "$RESTOREDIR"
cd "$RESTOREDIR"
echo "Checkout requested snapshot..."
git checkout "$COMMIT"
echo "Restore hub root files..."
rsync -a --delete --exclude=.git* "$RESTOREDIR"/www/ "$HUBROOT"/
echo "Restore hub database..."
mysql -u "$DBUSER" -p"$DBPWD" "$DBNAME" < "$RESTOREDIR"/db/"$DBNAME".sql

chown -R www-data:www-data "$HUBROOT"/{store,extend,addon,.htlog,.htconfig.php}

echo "Restored hub to snapshot $COMMIT"
echo "Removing temporary files..."

rm -rf "$RESTOREDIR"

exit 0
```