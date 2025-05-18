### File system layout

| Directory                 | Description                                                  |
| ------------------------- | ------------------------------------------------------------ |
| addon                     | Optional addons/plugins                                      |
| boot.php                  | Each process uses this to boot the application structure     |
| doc                       | Help files                                                   |
| images                    | required images                                              |
| include                   | The ‘model’ in MVC - (back-end functions), also contains PHP ‘executables’ for background processing |
| index.php                 | The front-end controller for web access                      |
| install                   | Installation and upgrade files and DB schema                 |
| library                   | Third-party modules (must be licence-compatible)             |
| mod                       | Control modules based on URL path names (e.g. http://sitename/foo loads mod/foo.php) |
| mod/site/                 | Site-specific mod overrides that are excluded from Git       |
| util                      | Translation tools, main database for English strings and other various utilities |
| version.inc               | contains the current version (which is automatically updated via cron for the main repository and distributed via git) |
| view                      | Theme and language files                                     |
| view/(css,js,img,php,tpl) | Standard theme files                                         |
| view/(en,it,es ...)       | Language strings and resources                               |
| view/theme/               | Single named themes that contain (css,js,img,php,tpl) overrides |