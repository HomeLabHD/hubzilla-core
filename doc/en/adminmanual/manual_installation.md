### Manual installation 

#### Unzip the Hubzilla files into the root directory of your web server document area  

When copying the directory tree to your web server, make sure to include the hidden files such as .htaccess.

If you are able to, we recommend cloning the source code repository with git instead of using a packed tar or zip file. This makes it much easier to update the software. The Linux command to clone the repository into a ‘mywebsite’ directory is as follows:

```
git clone https://framagit.org/hubzilla/core.git mywebsite
```

and then you can pull the latest changes at any time with:

```
git pull
```

Make sure that the folders `store/[data]/smarty3` and `store` exist and are writable by the web server:

```
mkdir -p ‘store/[data]/smarty3’
chmod -R 777 store
```

This authorisation (777) is very dangerous and if you have sufficient privileges and knowledge, you should only change these directories for the web server and, if different, for the user running the cron job (see below). In many shared hosting environments, this can be difficult without opening a trouble ticket with your provider. The above authorisations allow the software to function, but are not optimal.

The following directories must also be writable by the web server for certain web-based management tools to work:

- `addon`
- `extend`
- `view/theme`
- `widget`

#### **Official addons**  

##### **Installation**  

Navigate to your website. Then you should clone the addon repository (separately). We nickname this repository ‘hzaddons’. You can include other Hubzilla addon repositories by giving them different nicknames:

```
cd mywebsite
util/add_addon_repo https://framagit.org/hubzilla/addons.git hzaddons
```

##### **Refresh**  

To keep the addon tree up to date, you should be at the top level of the website directory and enter an update command for this repository::

```
cd mywebsite
util/update_addon_repo hzaddons
```

Create searchable representations of the online documentation. You can do this every time the documentation is updated:

```
cd mywebsite
util/importdoc
```