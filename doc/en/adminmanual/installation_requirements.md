### Installation requirements 

- Apache with mod-rewrite enabled and ‘AllowOverride All’ so you can use a local .htaccess file. Some people have successfully used nginx and lighttpd. Example configuration scripts are available for these platforms in doc/install. Apache and nginx have the most support.
- PHP 8.1 or higher. Note that in some shared hosting environments, the *command line version* of PHP may differ from the *web server version*
- *PHP command line access* if register_argc_argv is set to true in the php.ini file and the hosting provider has no restrictions on the use of exec() and proc_open().
- curl, gd (with at least jpeg and png support), pdo-mysql (or pdo-postgres), mbstring, zip and openssl extensions. The imagick extension is not required, but is recommended.
- The xml extension is required if you want to use webdav.
- Some kind of email server or email gateway so that PHP mail() works.
- A supported database server. The supported databases are:
  - Mysql version 8.0.22 or higher
  - MariaDB version 10.4 or higher
  - PostgreSQL version 12 or higher
- Ability to schedule jobs with cron.
- Installation in a top-level domain or sub-domain (without directory/path component in the URL) is REQUIRED.