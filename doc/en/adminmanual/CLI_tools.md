### CLI Tools (utils)

If you have access to the shell as an administrator, you can use other small CLI tools, which can be found in the ‘utils’ directory.

#### addons

You can use the addons script to display which addons are installed and which are available. You can also install and uninstall add-ons and reinstall all add-ons.

- `util/addons list`
  Display of all installed add-ons
- `util/addons list all`
  Display of all add-ons that are installed (*) and those that are deactivated due to incompatibility (!)
- `util/addons install foo`
  Install the addon with the name ‘foo’
- `util/addons uninstall foo`
  Uninstall the addon with the name ‘foo’
- `util/addons reinstall`
  Reinstall all addons

#### admins

The admins script allows you to display all admins of the hub, add further admins and remove existing admins.

- `util/admins`
- `util/admins list`
- `util/admins add <account_id>`
- `util/admins remove <account_id>`

#### config / pconfig

See: [Advanced configurations for administrators](/help/adminmanualadvanced_configurations.md)

#### connect

You can use connect to establish a connection between one channel of your hub and another channel.

- `util/connect <channel_id> <channel@hub>`
- `util/connect <channel_address> <channel@hub>`

#### dcp

You can use dcp to copy files to the store area of a channel on your hub.

- `util/dcp <source> <destination directory>` where destination directory must be `store/$nickname/path` or `$nickname/path`.

#### dmkdir

You can use dmkdir to create a subdirectory in the store area of a channel on your hub.

- `util/dmkdir <directory>` where directory must ba `store/$nickname/path/<directory>` or `$nickname/path/<directory>`.

#### fresh (The Freaking REd Shell)

This only works under Unix/Linux. If the readline module is installed, it uses this for the input, otherwise it only reads from stdin and writes to stdout.
The commands are processed in sequence until the command ‘exit’, ‘quit’ or the end of the file is reached.

Commands:

- `version`
  Displays the current Fresh version.

- `login <email address>`

- Asks for a password and authenticates `<email address>` as the current user.

  user.

- `finger <channel address>`
  Performs a lookup of `<channel address>` and reports the result.

- `channel <channel nbickname>`
  Switches the current channel to the channel with the specified nickname.

- `conn [<id1> <id2> ...]`

  Without arguments, this lists all connections of the current channel with an ID.
  If IDs are specified, the details of the individual connections are displayed.

#### hz

Simple, minimalist command line tool to send the status to hubzilla via the API. Requires curl.

```
hz [<configuration file>]
```

hz requires a configuration file. You can either use a `.hubzilla` file and omit the `<configuration file>` parameter or create a configuration file under any name in the hubzilla directory, the name of which you then specify when calling hz.

Format of the configuration file:

```
USER=<your username>
PASS=<your password>
HUB=<domain of the hub>
```

You can then enter your posting and finalise the entry with Ctrl-D.

#### storageconf

You can use storageconf to specify the storage location for thumbnails (file system or database) and query the current configuration.

- `util/storageconv stats`
  Shows the currently set status
- `util/storageconv fs`
  Moves the thumbnails from the database to the file system
- `util/storageconv db`
  Moves the thumbnails from the file system to the database

#### thumbrepair

thumbrepair recreates the local thumbnails.
