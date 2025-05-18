### Database 

#### Database updates   

On the https://hub.hubzilla.hu/admin/dbsync page, the administrator can check whether an update has failed and retry it if necessary.
If an update has failed, but for some reason is not registered as failed, the administrator can try to run the update again. For example, for DB update #1999, by visiting the web page:
[https://hubzilla.com.bradmin/dbsync/1999](https://hubzilla.com.bradmin/dbsync/1999?f=&zid=pepecyb@hub.hubzilla.hu)

#### Database tables

| Table                                                        | Description                                                  |
| ------------------------------------------------------------ | ------------------------------------------------------------ |
| [abconfig](https://hub.hubzilla.hu/help/database/db_abconfig) | any storage space for connections from local channels        |
| [abook](https://hub.hubzilla.hu/help/database/db_abook)      | Connections of the local channels                            |
| [account](https://hub.hubzilla.hu/help/database/db_account)  | Service provider account                                     |
| [addon](https://hub.hubzilla.hu/help/database/db_addon)      | registered plugins                                           |
| [app](https://hub.hubzilla.hu/help/database/db_app)          | personal app data                                            |
| [attach](https://hub.hubzilla.hu/help/database/db_attach)    | File attachments                                             |
| [auth_codes](https://hub.hubzilla.hu/help/database/db_auth_codes) | OAuth usage                                                  |
| [cache](https://hub.hubzilla.hu/help/database/db_cache)      | OEmbed cache                                                 |
| [cal](https://hub.hubzilla.hu/help/database/db_cal)          | CalDAV container for events                                  |
| [channel](https://hub.hubzilla.hu/help/database/db_channel)  | local channels                                               |
| [chat](https://hub.hubzilla.hu/help/database/db_chat)        | Chat room content                                            |
| [chatpresence](https://hub.hubzilla.hu/help/database/db_chatpresence) | Channel presence information for the chat                    |
| [chatroom](https://hub.hubzilla.hu/help/database/db_chatroom) | Data for the actual chat room                                |
| [clients](https://hub.hubzilla.hu/help/database/db_clients)  | OAuth usage                                                  |
| [config](https://hub.hubzilla.hu/help/database/db_config)    | Main configuration storage                                   |
| [conv](https://hub.hubzilla.hu/help/database/db_conv)        | Meta conversation structure for private messages in Diaspora |
| [event](https://hub.hubzilla.hu/help/database/db_event)      | Events                                                       |
| [pgrp_member](https://hub.hubzilla.hu/help/database/db_pgrp_member) | Data protection groups (collections), group information      |
| [pgrp](https://hub.hubzilla.hu/help/database/db_pgrp)        | Data protection groups (collections), member information     |
| [hook](https://hub.hubzilla.hu/help/database/db_hook)        | Plugin hook register                                         |
| [hubloc](https://hub.hubzilla.hu/help/database/db_hubloc)    | xchan location storage, links a hub location with a xchan    |
| [iconfig](https://hub.hubzilla.hu/help/database/db_iconfig)  | Expandable, arbitrary memory for elements                    |
| [issue](https://hub.hubzilla.hu/help/database/db_issue)      | Upcoming error/problem database                              |
| [item](https://hub.hubzilla.hu/help/database/db_item)        | all articles and websites                                    |
| [item_id](https://hub.hubzilla.hu/help/database/db_item_id)  | (deprecated by iconfig) other identifiers in other services for contributions |
| [likes](https://hub.hubzilla.hu/help/database/db_likes)      | ‘Like‘ things                                                |
| [mail](https://hub.hubzilla.hu/help/database/db_mail)        | private messages                                             |
| [menu](https://hub.hubzilla.hu/help/database/db_menu)        | Website menu data                                            |
| [menu_item](https://hub.hubzilla.hu/help/database/db_menu_item) | Entries for menus on websites                                |
| [notify](https://hub.hubzilla.hu/help/database/db_notify)    | Notifications                                                |
| [obj](https://hub.hubzilla.hu/help/database/db_obj)          | Object data for things (x has y)                             |
| [outq](https://hub.hubzilla.hu/help/database/db_outq)        | Outgoing queue                                               |
| [pconfig](https://hub.hubzilla.hu/help/database/db_pconfig)  | Personal (per channel) configuration memory                  |
| [photo](https://hub.hubzilla.hu/help/database/db_photo)      | Photo store                                                  |
| [poll](https://hub.hubzilla.hu/help/database/db_poll)        | Data for polls                                               |
| [poll_elm](https://hub.hubzilla.hu/help/database/db_poll_elm) | Data for poll elements                                       |
| [profdef](https://hub.hubzilla.hu/help/database/db_profdef)  | Definitions for user-defined profile fields                  |
| [profext](https://hub.hubzilla.hu/help/database/db_profext)  | User-defined profile field data                              |
| [profile](https://hub.hubzilla.hu/help/database/db_profile)  | Channel profiles                                             |
| [profile_check](https://hub.hubzilla.hu/help/database/db_profile_check) | DFRN remote authorisation, may be outdated                   |
| [register](https://hub.hubzilla.hu/help/database/db_register) | Registrations that require an administrative authorisation   |
| [session](https://hub.hubzilla.hu/help/database/db_session)  | Storage of web sessions                                      |
| [shares](https://hub.hubzilla.hu/help/database/db_shares)    | Information on common elements                               |
| [sign](https://hub.hubzilla.hu/help/database/db_sign)        | Diaspora signatures. Is gradually being phased out.          |
| [site](https://hub.hubzilla.hu/help/database/db_site)        | Location table for finding directory peers                   |
| [source](https://hub.hubzilla.hu/help/database/db_source)    | Data from channel sources                                    |
| [sys_perms](https://hub.hubzilla.hu/help/database/db_sys_perms) | Extensible authorisations for OAuth                          |
| [term](https://hub.hubzilla.hu/help/database/db_term)        | Article taxonomy table (categories, tags, etc.)              |
| [tokens](https://hub.hubzilla.hu/help/database/db_tokens)    | OAuth usage                                                  |
| [updates](https://hub.hubzilla.hu/help/database/db_updates)  | Directory synchronisation updates                            |
| [verify](https://hub.hubzilla.hu/help/database/db_verify)    | General verification structure                               |
| [vote](https://hub.hubzilla.hu/help/database/db_vote)        | Voting data for polls                                        |
| [xchan](https://hub.hubzilla.hu/help/database/db_xchan)      | List of known channels in the universe                       |
| [xchat](https://hub.hubzilla.hu/help/database/db_xchat)      | Chat rooms with bookmarks                                    |
| [xconfig](https://hub.hubzilla.hu/help/database/db_xconfig)  | like pconfig, but for channels without a local account       |
| [xign](https://hub.hubzilla.hu/help/database/db_xign)        | Channels ignored by friend suggestions                       |
| [xlink](https://hub.hubzilla.hu/help/database/db_xlink)      | ‘Friends of friends’ links derived from poco, also storage of ratings |
| [xperm](https://hub.hubzilla.hu/help/database/db_xperm)      | OAuth/OpenID-Connect extendable authorisations Authorisation memory |
| [xprof](https://hub.hubzilla.hu/help/database/db_xprof)      | if this node is a directory server, it contains basic public profile information about everyone on the network |
| [xtag](https://hub.hubzilla.hu/help/database/db_xtag)        | if this hub is a directory server, it contains tags or interests from everyone on the network |