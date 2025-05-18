### Hub snapshot tools 

Hubzilla developers often need to switch between branches that may have incompatible database schemas or content. The following two scripts create and restore complete snapshots of a Hubzilla instance, including the Hub web root and the entire database state. Each script requires a configuration file called `hub-snapshot.conf`, which is located in the same folder and contains the specific directories and database details of your Hub.

#### Configuration file  

The format of the configuration file is very strict. There must be no spaces between the variable name and the value. Replace only the content inside the inverted commas with your configuration. Save this file as `hub-snapshot.conf` together with the scripts.

```
# Location of the hub root. Normally this is the location of the Hubzilla repo clone.
HUBROOT=‘/var/www/’
# Name of the MySQL database
DBNAME=‘hubzilla’
# MySQL database user
DBUSER=‘hubzilla’
# MySQL database password
DBPWD=‘akeufajeuwfb’
# The target snapshot folder in which the Git repository is to be initialised
SNAPSHOTROOT=‘/root/snapshots/hubzilla/’
```

#### Snapshot  

Example of use:

```
sh hub-snapshot.sh my-hub.conf ‘Commit message for the snapshot’ 
```

**hub-snapshot.sh**:

```
#!/bin/bash

if ! [ -f ‘$1’ ]; then
    echo ‘$1 is not a valid file. Aborting..."
    exit 1
fi
source ‘$1’
#echo ‘$DBNAME’
#echo ‘$DBUSER’
#echo ‘$DBPWD’
#echo ‘$HUBROOT’
#echo ‘$SNAPSHOTROOT’
MESSAGE=‘snapshot: $2’

if [ ‘$DBPWD’ == ‘’ -o ‘$SNAPSHOTROOT’ == ‘’ -o ‘$DBNAME’ == ‘’ -o ‘$DBUSER’ == ‘’ -o ‘$HUBROOT’ == ‘’ ]; then
    echo ‘Required variable is not set. Aborting..."
    exit 1
fi

if [ ! -d ‘$SNAPSHOTROOT’/db/ ]; then
    mkdir -p ‘$SNAPSHOTROOT’/db/
fi
if [ ! -d ‘$SNAPSHOTROOT’/www/ ]; then
    mkdir -p ‘$SNAPSHOTROOT’/www/
fi

if [ ! -d ‘$SNAPSHOTROOT’/www/ ] || [ ! -d ‘$SNAPSHOTROOT’/db/ ]; then
    echo ‘Error creating snapshot directories. Aborting..."
    exit 1
fi

echo ‘Export database...’
mysqldump -u ‘$DBUSER’ -p‘$DBPWD’ ‘$DBNAME’ > ‘$SNAPSHOTROOT’/db/‘$DBNAME’.sql
echo ‘Copy hub root files...’
rsync -va --delete --exclude=.git* ‘$HUBROOT’/ ‘$SNAPSHOTROOT’/www/

cd ‘$SNAPSHOTROOT’

if [ ! -d ‘.git’ ]; then
    git init
fi
if [ ! -d ‘.git’ ]; then
    echo ‘Cannot initialise git repo. Aborting..."
    exit 1
fi

git add -A
echo ‘Commit hub snapshot...’
git commit -a -m ‘$MESSAGE’

exit 0
```

#### Restore 

```
/bin/bash
# Restore hub to a previous state. Input hub config and commit hash

if ! [ -f ‘$1’ ]; then
        echo ‘$1 is not a valid file. Aborting..."
        exit 1
fi
source ‘$1’
COMMIT=$2

if [ ‘$DBPWD’ == ‘’ -o ‘$SNAPSHOTROOT’ == ‘’ -o ‘$DBNAME’ == ‘’ -o ‘$DBUSER’ == ‘’ -o ‘$HUBROOT’ == ‘’ ]; then
        echo ‘Required variable is not set. Aborting..."
        exit 1
fi
RESTOREDIR=‘$(mktemp -d)/’

if [ ! -d ‘$RESTOREDIR’ ]; then
    echo ‘Cannot create restore directory. Aborting..."
    exit 1
fi
echo ‘Cloning the snapshot repo...’
git clone ‘$SNAPSHOTROOT’ ‘$RESTOREDIR’
cd ‘$RESTOREDIR’
echo ‘Checkout requested snapshot...’
git checkout ‘$COMMIT’
echo ‘Restore hub root files...’
rsync -a --delete --exclude=.git* ‘$RESTOREDIR’/www/ ‘$HUBROOT’/
echo ‘Restore hub database...’
mysql -u ‘$DBUSER’ -p‘$DBPWD’ ‘$DBNAME’ < ‘$RESTOREDIR’/db/‘$DBNAME’.sql

chown -R www-data:www-data ‘$HUBROOT’/{store,extend,addon,.htlog,.htconfig.php}

echo ‘Restored hub to snapshot $COMMIT’
echo ‘Removing temporary files...’

rm -rf ‘$RESTOREDIR’

exit 0
```