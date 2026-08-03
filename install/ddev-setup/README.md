Ddev is a container based development environment. Other hubzilla ddev installations exist. Caveat: This package runs a local development server under mysql/mariadb and apache, Hubzilla's "officially supported" base configuration. Feel free to alter the configuration for your personal needs; e.g. nginx and postgres are probably much more performant.

To setup Hubzilla to run under ddev,
1. Install ddev and docker (see https://ddev.com)
2. Copy this directory (including sub-directories) to ".ddev" in your hubzilla top-level directory.

    `cd hubzilla # (or wherever your hubzilla directory is located)`
    `cp -R install/ddev-setup .`

3. Also from the top-level hubzilla directory, run

    `ddev start`

4. If the database fails to start because hubzilla.ddev.site had previously been configured fora different database driver
    `docker volume rm hubzilla-postgres`
    `ddev start`
