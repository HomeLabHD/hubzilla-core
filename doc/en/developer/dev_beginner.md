### You want to contribute code?

**...and don't know how to start to...**

- debug hubzilla (php on the webserver),
- contribute code to the project,
- optionally - do it all from inside a virtual machine

This manual was tested for Debian (Wheezy) as virtual machine on Lubuntu (Ubuntu 14.0) as host.



#### Install a Virtual Machine (KVM)

[Here](https://wiki.debian.org/KVM) the installation guide for Linux Debian.
The summary:

1. install KVM

```
# apt-get install qemu-kvm libvirt-bin
```

2. add yourself to the group libvirt 
```
# adduser <youruser> libvirt
```

3. install gui to manage virtual machines 
```
# apt-get install virt-manager
```

4. download an operating system to run inside the vm ([mini.iso](http://ftp.nl.debian.org/debian/dists/wheezy/main/installer-amd64/current/images/netboot/mini.iso))
5. start the virt manager
- create new virtual machine (click on icon)
- choose your iso image (just downloaded) as installation source
- optional: configure the new vm: ram, cpu's,...
- start virtual machine > result: linux debian starts in a new window.
6. (optional) avoid network errors after restart of host os

```
# virsh net-start default
# virsh net-autostart default
```



#### Install Apache Webserver

Open a terminal and make yourself root

```
su -l
```


Create the standard group for the Apache webserver

```
groupadd www-data
```

might exist already


```
usermod -a -G www-data www-data
```


Check if the system is really up to date

```
apt-get update
apt-get upgrade
```


Optional restart services after installation

```
reboot
```


If you restarted, make yourself root

```
su -l
```


Install Apache: 
```
apt-get install apache2 apache2-doc apache2-utils
```


Open webbrowser on PC and check [localhost](localhost)
Should show you a page like "It works"

(Source [http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#](http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#))

#### Install PHP, MySQL, phpMyAdmin

##### PHP, MySQL


```
su -l
apt-get install libapache2-mod-php8.2 php8.2 php-pear php8.2-xcache php8.2-curl php8.2-mcrypt php8.2-xdebug
apt-get install php8.2-mysql
apt-get install mysql-server mysql-client
```

enter and note the mysql passwort

Optional since its already enabled during phpmyadmin setup

```
phpenmod mcrypt
```

##### phpMyAdmin

Install php myadmin

```
apt-get install phpmyadmin
```


Configuring phpmyadmin
- Select apache2 (hint: use the tab key to select)
- Configure database for phpmyadmin with dbconfig-common?: Choose Yes

(Source [http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#](http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#))

##### Enable rewrite

The default installation of Apache2 comes with mod_rewrite installed. To check whether this is the case, verify the existence of `/etc/apache2/mods-available/rewrite.load`


```
nano /etc/apache2/mods-available/rewrite.load
```

 (You should find the content: `LoadModule rewrite_module` `/usr/lib/apache2/modules/mod_rewrite.so`)
To enable and load mod_rewrite, do the rest of steps.
Create a symbolic link in `/etc/apache2/mods-enabled`


```
cd /var/www
root@debian /var/www $ a2enmod rewrite
```


Then open up the following file, and replace every occurrence of "`AllowOverride None`" with "`AllowOverride all`".


```
nano /etc/apache2/apache2.conf
```

or

```
gedit /etc/apache2/sites-enabled/000-default
```


Finally, restart Apache2.


```
service apache2 restart
```

##### Test installation


```
cd /var/www
```


create a php file to test the php installation
```
nano phpinfo.php
```


Insert into the file:

```
<?php
  phpinfo();
?>
```

(save CTRL+0, ENTER, CTRL+X)

open webbrowser on PC and try `http://localhost/phpinfo.php` (page shows infos on php)

connect phpMyAdmin with MySQL database 
```
nano /etc/apache2/apache2.conf
```

- CTRL+V... to the end of the file
- Insert at the end of the file:  (save CTRL+0, ENTER, CTRL+X)
```
Include /etc/phpmyadmin/apache.conf
```


restart apache

```
/etc/init.d/apache2 restart
apt-get update
apt-get upgrade
reboot
```

**phpMyAdmin**

open webbrowser on PC and try `http://localhost/phpmyadmin`

(Source [http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#](http://www.manfred-steger.de/tuts/20-der-eigene-webserver-mit-dem-raspberry-pi#))

##### Create an empty database... that is later used by hubzilla


open webbrowser on PC and try `http://localhost/phpmyadmin`

Create an empty database, for example named "red".
Create a database user, for example "red".
Grant all rights for the user "red" to the database "red".

Note the access details (hostname, username, password, database name).

##### Fork the project on github

Please follow the instruction in the offiical [ documentation](http://git-scm.com/book/en/v2/GitHub-Contributing-to-a-Project) of git.
It is a good idea to read the whole manual! Git is different to other version control systems in many ways.

Now you should

- create an account at github.com
- fork `https://framagit.org/hubzilla/core`
- fork `https://framagit.org/hubzilla/addons`



If you not want to use GIT from the command line - there is a usefull Eclipse plugin named ""Eclipse Mylyn to GitHub connector".

#### Install Hubzilla and its Addons

##### Git at your computer / vm

You should have created an account on github and forked the projects befor you procceed.

Delete the directory www

```
rm -R www/
```


Install git (and optionally git-gui a client gui)

```
apt-get install git git-gui
```

##### Download hubzilla and addons

Download the main project hubzilla and hubzilla-addons

```
git clone https://github.com/yourname/hubzilla www
cd www/
git clone https://github.com/yourname/hubzilla-addons addon
```


Make this extra folder

```
mkdir -p "store/[data]/smarty3"
```


Create .htconfig.php and make it writable by the webserver

```
touch .htconfig.php
chmod ou+w .htconfig.php
```


Make user www-data (webserver) is the owner all the project files

```
cd ..
chown -R www-data:www-data www/
```

Add yourself ("surfer" in this example) to the group www-data. Why? Later you want to modify files in eclipse or in another editor.

Then make all files writable by the group www-date you are now a member of.

```
cd www/
usermod -G www-data surfer
chmod -R  g+w www/
```

Restart the computer (or vm)

If you are still not able to modify the project files you can check the members of the group www-data with

```
cat /etc/group
```

##### Register yourself as admin

Open `http://localhost` and init the matrix

Befor you register a first user switch off the registration mails.
Open `/var/www/.htconfig.php`
and make sure "0" is set in this line

```
App::$config['system']['verify_email'] = 0;
```

You should be able to change the file as "yourself" (instead of using root or www-data).

##### Cron and the poller

Important!
Run the poller  to pick up the recent "public" postings of your friends
Set up a cron job or scheduled task to run the poller once every 5-10
minutes to pick up the recent "public" postings of your friends


```
crontab -e
```


Add

```
*/10 * * * * cd /var/www/; /usr/bin/php include/poller.php
```


If you don't know the path to PHP type

```
which php
```



#### Debug the server via eclipse

##### Check the configuration of xdebug

You shoud already have installed xdebug in the steps befor

```
apt-get install php5-xdebug
```


Configuring Xdebug

Open your terminal and type as root (su -l)

```
gedit /etc/php5/mods-available/xdebug.ini
```


if the file is empty try this location

```
gedit /etc/php5/conf.d/xdebug.ini
```


That command should open the text editor gedit with the Xdebug configuration file
At the end of the file content append the following text

```
xdebug.remote_enable=on
xdebug.remote_handler=dbgp
xdebug.remote_host=localhost
xdebug.remote_port=9000
```

Save changes and close the editor.
In you terminal type to restart the web server.

```
service apache2 restart
```



##### Install Eclipse and start debugging

Install eclipse.
Start eclipse with default worspace (or as you like)

Install the PHP plugin
Menu > Help > Install new software...
Install "PHP Developnent Tools ..."

Optionally - Install the GitHub connector plugin
Menu > Help > Install new software...
Install "Eclipse Mylyn to GitHub connector"

Configure the PHP plugin
Menu > Window > Preferences...
> General > Webbrowser > Change to "Use external web browser"
> PHP > Debug > Debug Settings > PHP Debugger > Change to "XDebug"

Create a new PHP project
Menu > File > New Project > Choose PHP > "PHP Project"
> Choose Create project at existing location" and "/var/www"

Start debugging
Open index.php and "Debug as..."
Choose as Launch URL: "`http://localhost/`"

Expected:

- The web browser starts
- The debugger will stop at the first php line



#### Contribute your changes via github

##### Preparations

There is a related page in this docs: [zrl=[baseurl]/help/git_for_non_developers]Git for Non-Developers[/zrl].
As stated befor it is recommended to read the official documentation [GitHub-Contributing-to-a-Project](http://git-scm.com/book/en/v2/GitHub-Contributing-to-a-Project) of git.

Eclipse has a usefull plugin for GIT: "Eclipse Mylyn to GitHub connector".

Make sure you have set your data

```
git config --global user.name "Your Name"
git config --global user.email "your@mail.com"
```

##### Your first contribution

Create a descriptive topic branch

```
git checkout -b dev_beginning
```


Make sure your local repository is up-to-date with the main project.
Add the original repository as a remote named “upstream” if not done yet

```
git remote add upstream https://framagit.org/hubzilla/core/
```


Fetch the newest work from that remote

```
git fetch upstream
git merge upstream/master
```


Hint: You can list the branches

```
git branch -v
```


Make your changes. In this example it is a new doc file.

Check your modifications

```
git status
```


Add (stage) the new file

```
git add doc/dev_beginner.bb
```


Commit the changes to your local branch. This will open an editor to provide a message.

```
git commit -a
```


Push back up to the same topic branch online

```
git push
```


Now you can go to your (online) account at github and create the pull request.

##### Following contributions

In case the main devolpers want you to change something.
Fetch the newest work from the remote upstream/master to be sure you have the latest changes.

```
git fetch upstream
git merge upstream/master
```

Make your changes, test them, commit (to local repository), push (to online repository)

```
git status
git commit -a -m "added modification of branch"
git push
```

