### Advanced configurations for administrators 

*This document assumes that you are an administrator.*
Hubzilla contains many configuration options that are hidden in the main administration area. Generally, these are options that are considered too niche, advanced, or confusing.
These settings can be changed via the shell from the top level web directory using the following syntax

`util/config cat key value` 

for a website configuration, or

`util/pconfig channel_id cat key value` 

for a member configuration.

For a site configuration, another option is to insert a line in .htconfig.php with the syntax: 

App::$config[‘cat’][‘key’] = ‘value’;`

#### Member configuration (pconfig)    

- system.always_my_theme 
  Always use your own theme when watching channels in the same hub. This leads to some pretty imaginative problems when viewing channels with themed comanches. 
- system.blocked 
  An array of Xchans that are blocked by this channel. Technically this is a hidden configuration and belongs here, but addons (notably Superblock) have made this available in the UI. 
- system.default_cipher 
  Sets the default cipher used for E2EE elements. 
- system.display_friend_count 
  Defines the number of connections to be displayed in the connection profile widget. 
- system.do_not_track 
  Like the browser header. This will break many identity-based functions. You should really only set permissions that make sense. 
- system.forcepublicuploads 
  Forces uploaded photos to be public when uploaded as wall elements. It makes much more sense to set the permissions correctly in the first place. Do this instead. 
- system.network_page_default 
  Sets the default parameters for displaying the network page. This should contain the same query string as the manual filtering. 
- system.paranoia 
  Defines the security level of the IP check. If the IP address of a logged on session changes, this level is applied to determine if the account should be logged off as a security breach. The options are: 0 - no IP check 1 - check 3 octets 2 - check 2 octets 3 - check for all differences 
- system.prevent_tag_hijacking 
  Prevents external networks from hijacking hashtags in your posts and redirecting them to their own resources. 
- system.startpage 
  Another one of those technically hidden configurations provided by addons. Defines the default page that is displayed when you log in. This is made available to the user interface by the startpage add-on. 
- system.taganyone 
  Requires that the configuration of the same name is activated. Allows @mention tagging of everyone, regardless of whether you are connected or not. This does not scale. 
- system.anonymous_comments 
  By default or if set to 1, custom permissions can be set to allow anonymous (moderated) comments like WordPress, moderated by the channel owner. If set to 0, no member of your site can select or enable this. 
- system.user_scalable 
  Determines whether the app is scalable to touchscreens. Set to on by default, set to zero to deactivate - real zero, not just false. 

#### Configuration of the website    

- randprofile.check 
  When requesting a random profile, first check whether it actually exists 
- randprofile.retry 
  Number of attempts to obtain a random profile 
- system.admin_email 
  Specifies the e-mail address of the administrator for this site. This is defined during installation. 
- system.authlog 
  Log file used for logging authentication errors. Used to connect to server-side software such as fail2ban. Auth errors are also logged in the main logs. 
- system.auto_channel_create 
  Adds the necessary form elements to create the first channel on the account registration page and creates it (possibly after email validation or administrator approval). This excludes the ability to import a channel from another site as the first channel created on this site for a new account. Use with system.default_permissions_role to streamline registration. 
- system.auto_follow 
  The first channel of an account automatically follows the channels listed here - comma separated list of webbies (member@hub addresses). 
- system.blacklisted_sites 
  An array of specific hubs that are to be completely blocked by this hub. 
- system.block_public_search 
  Similar to block_public, with the difference that only public access to the search functions is blocked. Useful for pages that want to be public but are overrun by search engines. 
- system.cron_hour 
  Specify an hour in which cron_daily should be executed. By default, without configuration, this is executed at midnight UTC. 
- system.default_permissions_role 
  If this value is set to a valid name for a permission role, this role will be used for the first channel created by a new account and will not prompt for the ‘channel type’ on the channel creation form. Examples of valid names are: ‘social’, ‘social_restricted’, ‘social_private’, ‘forum’, ‘forum_restricted’ and ‘forum_private’. Read more about authorisation roles [here](https://hub.hubzilla.hu/help/roles). 
- system.default_profile_photo 
  Defines the profile photo with which new channels start. This should contain the name of a directory located under images/default_profile_photos/ or it should not be set. If it is not set, ‘rainbow_man’ is assumed. 
- system.directorytags 
  Specifies the number of keyword tags to display on the directory page. The default is 50 if it is not set to a positive integer. 
- system.disable_directory_keywords 
  If ‘1’, no directory keywords are displayed. If the hub is a directory server, prevent keywords from being returned to all directory clients. Please do not set this for directory servers in the RED_GLOBAL area. 
- system.disable_discover_tab 
  Allows you to completely disable the ability to discover public content from external sites. 
- system.disable_dreport 
  If ‘1’, no delivery reports are saved or linked. 
- system.dlogfile 
  Log file used for logging development errors. Exactly the same as logger otherwise. This is not magic and requires your own logging instructions. Developer tool. 
- system.email_notify_icon_url 
  URL of the image (32x32) to be displayed in email notifications (HTML bodies). 
- system.expire_delivery_reports 
  Expiry date in days for delivery reports - default value 10 
- system.expire_limit 
  Do not expire more than this number of reports per channel per expiry run to avoid exhausting the memory. Default value 5000. 
- system.photo_storage_type 
  If ‘1’, the file system is used instead of the SQL database to store the thumbnails. Default setting is ‘0’. Introduced in 4.2 
- system.hidden_version_siteinfo 
  If ‘true’, the software version is not displayed on the siteinfo pages (system.hide_version hides the version on these pages as well, this setting *only* hides the version on the siteinfo pages). 
- system.hide_help 
  Do not show the link to the help documentation in the navigation bar 
- system.hide_in_statistics 
  Instructs the red statistics servers to completely hide this hub in hub lists. 
- system.hide_version 
  If true, the software version is not displayed on websites and tools. (*) Must be set in .htconfig.php. 
- system.ignore_imagick 
  Ignores imagick and uses GD, even if imagick is installed on the server. Prevents some problems with PNG files in older versions of imagick. 
- system.max_daily_registrations 
  Defines the maximum number of new registrations allowed in a day. Useful to prevent oversubscription when publicising the project. 
- system.max_import_size 
  If configured, the maximum length of an imported text message. This is usually left at 200 KBytes or more to accommodate private Friendica photos that are embedded. 
- system.max_tagged_forums 
  Spam protection. Limits the number of tagged forums recognised in each post. Default is 2, only the first ‘n’ tags are delivered as forums, the others cause no delivery. 
- system.minimum_feedcheck_minutes 
  The minimum interval between checking RSS feeds. If this interval is less than the cron interval, the feeds are checked with every cron job. The default value is 60 if it has not been set. The site setting can also be overridden for each individual channel by a service class setting aptly named ‘minimum_feedcheck_minutes’. 
- system.no_age_restriction 
  Do not restrict registration to persons over the age of 13. In many countries it is required by law that age must be specified and that all personal data of minors must be blocked. 
- system.object_cache_days 
  Defines how long cached embedded content can be used without being retrieved again. The default setting is 30 days. 
- system.openssl_conf_file 
  Specify a file that contains the OpenSSL configuration. Required in some Windows installations to find the openssl configuration file on the system. Read the code first. If you cannot read the code, do not play with it. 
- system.openssl_encrypt 
  Use encryption engine from openssl, default is false (uses mcrypt for AES encryption) 
- system.optimize_items 
  Executes optimise_table during some tasks to keep your database clean and defragmented. This comes at the expense of performance while the operations are running, but also makes things run a bit faster when they are not. There are also CLI utilities to perform this operation that you may prefer, especially if you have a large site. 
- system.override_poll_lockfile 
  Ignores the lock file in the poller process to allow more than one process to run at a time. 
- system.paranoia 
  Like pconfig, but on a site-wide basis. Can be overridden by member settings. 
- system.pin_types 
  Array of allowed element types for pinning. The default values depend on the module, but can be changed here. 
- system.photo_cache_time 
  How long the photos should be cached, in seconds. The default value is 86400 (1 day). A longer time increases performance, but also means that it takes longer for changed authorisations to apply. 
- system.platform_name 
  What should be displayed as the platform name on websites and in statistics. (*) Must be set in .htconfig.php. 
- system.rating_enabled 
  Distributed reputation reports and data collection. This function is currently being revised. 
- system.poke_basic 
  Reduce the number of poke verbs to exactly 1 (‘poke’). Deactivate other verbs. 
- system.proc_run_use_exec 
  If 1, the system call exec in proc_run is used to execute background tasks. By default we use proc_open and proc_close. On some (currently rare) systems this does not work well. 
- system.projecthome 
  Displays the project page on your homepage for logged out viewers. 
- system.projecthome 
  Defines the project home page as the start page of the hub. (Obsolete) 
- system.pubstream_order 
  Defines the pubstream order. Possible values ‘commented’ (default), ‘created’ and ‘edited’. 
- system.register_link 
  Path to which the ‘register’ link in the registration form should point. For closed sites, this will point to ‘pubsites’. For open sites, it will normally redirect to ‘register’, but you can change this to a custom site page that offers subscriptions or similar. 
- system.reserved_channels 
  Do not allow members to register channels with this comma-separated list of names (no spaces). 
- system.sellpage 
  A URL to display in the list of public sites to sell your hub - show classes of service, etc. 
- system.startpage 
  Defines the default page that is displayed for all channels on this website after a login. Can be overridden by user settings. 
- system.sys_expire_days 
  How many days should discovered public content from other websites be kept? 
- system.taganyone 
  Allows @mention tagging of everyone, whether you are connected or not. 
- system.tempdir 
  Location where temporary files are stored (currently unused), default is defined in PHP configuration. 
- system.tos_url 
  Sets an alternative link for the ToS storage location. 
- system.transport_security_header 
  if non-zero and SSL is used, a strict-transport-security header is inserted on the web pages 
- system.uploaddir 
  Storage location for uploading files (default is system.tempdir, currently only used by the js_upload plugin) 
- system.workflow_channel_next 
  The page to which new members should be redirected immediately after creating a channel. 
- system.workflow_register_next 
  The page to which members are redirected immediately after creating an account (only if auto_channel_create or UNO is activated). 

#### Directory configuration   

##### Default values for the directory search    

- directory.globaldir 
  0 or 1. default value 0. If you visit the directory on a site, you will only see the members of this site by default. You have to take an extra step to see the people in the rest of the network; and by doing this, there is a clear delineation that these people are *not* members of this site, but of a larger network. 
- directory.pubforums 
  0 or 1. public forums *should* be 0 by default. 
- directory.safemode 
  0 or 1. 

##### Configuration of the directory server  

- system.directory_mode 
- system.directory_primary 
- system.directory_realm 
- system.directory_server 
- system.realm_token