### Automated installation via the shell script .homeinstall

There is a shell script in (`.homeinstall/hubzilla-setup.sh`) that will install Hubzilla and its dependencies on a fresh installation of Debian stable. It should work on similar Linux systems, but your results may vary.

#### Requirements

The installation script was originally developed for a small hardware server behind your home router. However, it has been tested on several systems running Debian 9:

- Home-PC (Debian-9.2-amd64) and Rapberry-Pi 3 (Rasbian = Debian 9.3)
  - Internet connection and router at home
  - Mini-PC / Raspi connected to the router
  - USB drive for backups
  - Fresh installation of Debian on your mini PC
  - Router with open ports 80 and 443 for your Debian

#### Overview of the installation steps

1. `apt-get install git`
2. `mkdir -p /var/www/html`
3. `cd /var/www/html`
4. `git clone https://framagit.org/hubzilla/core.git .`
5. `nano .homeeinstall/hubzilla-config.txt`
6. `cd .homeeinstall/`
7. `./hubzilla-setup.sh`
8. `Reload apache2 service`
9. Open your domain with a browser and go through the initial configuration of Hubzilla.
