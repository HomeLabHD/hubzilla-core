### Service classes 

You can use service classes to limit system resources by restricting the options of individual accounts, e.g. the storage of files and the limitation of posts at the top level. Define customised service classes according to your requirements in the `.htconfig.php` file. For example, create a *standard* and a *premium class* with the following lines:

```
// Service classes

App::$config[‘system’][‘default_service_class’]=‘standard’; // this is the default service class that will be associated with each new account

// Configuration for the default service class
App::$config[‘service_class’][‘default’] =
array(‘photo_upload_limit’=>2097152, // total storage limit for photos per channel (here 2MB)
‘total_identities’ =>1, // Number of channels an account can create
‘total_items’ =>0, // Number of top-level items a channel can create. Applies only to top-level posts of the channel user, other posts and comments are not affected
‘total_pages’ =>100, // Number of pages a channel can create
‘total_channels’ =>100, // Number of channels the user can add, other users can still add this channel even if the limit is reached
‘attach_upload_limit’ =>2097152, // total storage space for attachments per channel (here 2MB)
‘chatters_inroom’ =>20);

// Configuration for premium service class
App::$config[‘service_class’][‘premium’] =
array(‘photo_upload_limit’=>20000000000, // total storage limit for photos per channel (here 20GB)
‘total_identities’ =>20, // Number of channels an account can create
‘total_items’ =>20000, // Number of top-level items a channel can create. Only applies to top-level posts of the channel user, other posts and comments are not affected
‘total_pages’ =>400, // Number of pages that a channel can create
‘total_channels’ =>2000, // Number of channels the user can add, other users can still add this channel even if the limit is reached
‘attach_upload_limit’ =>20000000000, // Total storage space for attachments per channel (here 20GB)
‘chatters_inroom’ =>100);
```

To apply a service class to an existing account, use the command line utility from the web root:

`util/service_class` list service classes

`util/config system default_service_class firstclass` sets the default service class to ‘firstclass’

`util/service_class firstclass` lists the services that are part of the ‘firstclass’ service class

`util/service_class firstclass photo_upload_limit 10000000` sets the total photo disc usage of the firstclass to 10 million bytes

`util/service_class --account=5 firstclass` sets the account ID 5 to the service class ‘firstclass’ (with confirmation)

`util/service_class --channel=blogchan firstclass` sets the account to which the channel ‘blogchan’ belongs to the service class ‘firstclass’ (with confirmation)

**Options for limiting the service class**

- photo_upload_limit - maximum total number of bytes for photos
- total_items - maximum total number of top-level posts
- total_pages - maximum number of pages for Comanche
- total_identities - maximum number of channels owned by the account
- total_channels - maximum number of connections
- total_feeds - maximum number of RSS feed connections
- attach_upload_limit - maximum file upload space (bytes)
- minimum_feedcheck_minutes - lowest permissible setting for querying RSS feeds
- chatrooms - maximum number of chatrooms
- chatters_inroom - maximum number of chatters per room
- access_tokens - maximum number of guest access tokens per channel