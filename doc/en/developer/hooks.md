### Hooks 

Hooks allow plugins/addons to ‘hook’ into the code in many places and change the behaviour or otherwise perform independent actions when an activity takes place or certain data structures are accessed. There are many hooks that allow you to hook into the software at almost any point and do something other than what is intended by default. Two variables are passed to these hooks. The first is the app structure, which contains details about the overall state of the page request as we build the resulting page. The second is unique to the specific hook being called and provides specific details about what is happening in the software at the time the hook is called.

[Created index of all hooks and the files they call](https://hub.hubzilla.hu/help/hooks)

[module_mod_aftercontent](https://hub.hubzilla.hu/help/hook/module_mod_aftercontent) General hook for each module, executed after mod_content(). Replace ‘module’ with the name of the module, e.g. ‘photos_mod_aftercontent’.

[module_mod_content](https://hub.hubzilla.hu/help/hook/module_mod_content) General hook for any module, executed before mod_content(). Replace ‘module’ with the module name, e.g. ‘photos_mod_content’.

[module_mod_init](https://hub.hubzilla.hu/help/hook/module_mod_init) General hook for any module, executed before mod_init(). Replace ‘module’ with the module name, e.g. ‘photos_mod_init’.

[module_mod_post](https://hub.hubzilla.hu/help/hook/module_mod_post) General hook for any module, executed before mod_post(). Replace ‘module’ with the name of the module, e.g. ‘photos_mod_post’.

[about_hook](https://hub.hubzilla.hu/help/hook/about_hook) Called from the siteinfo page

[accept_follow](https://hub.hubzilla.hu/help/hook/accept_follow) Called when a connection is accepted (friend request)

[account_downgrade](https://hub.hubzilla.hu/help/hook/account_downgrade) Called when an account has expired, indicating a possible downgrade to the ‘basic’ class of service

[account_settings](https://hub.hubzilla.hu/help/hook/account_settings) Called when the account settings form is created

[account_settings_post](https://hub.hubzilla.hu/help/hook/account_settings_post) Called when posting from the account settings form

[activity_filter](https://hub.hubzilla.hu/help/hook/activity_filter) Called when creating the list of filters for the network page

[activity_mapper](https://hub.hubzilla.hu/help/hook/activity_filter) Called when determining the activity type for the transfer.

[activity_decode_mapper](https://hub.hubzilla.hu/help/hook/activity_filter) Called when the activity type for the transfer is determined.

[activity_obj_mapper](https://hub.hubzilla.hu/help/hook/activity_filter) Called when the object type for the transfer is determined.

[activity_obj_decode_mapper](https://hub.hubzilla.hu/help/hook/activity_filter) Is called when the object type for the transfer is determined.

[activity_order](https://hub.hubzilla.hu/help/hook/activity_order) Called when generating the list of order options for the network page

[addon_app_installed_filter](https://hub.hubzilla.hu/help/hook/addon_app_installed_filter) Called when determining whether an addon_app is[installed](https://hub.hubzilla.hu/help/hook/addon_app_installed_filter)

[activity_received](https://hub.hubzilla.hu/help/hook/activity_received) Called when an activity (post, comment, like, etc.) has been received from a Nomad source

[admin_aside](https://hub.hubzilla.hu/help/hook/admin_aside) Is called when the sidebar widget of the administration page is created

[affinity_labels](https://hub.hubzilla.hu/help/hook/affinity_labels) Is used to generate alternative labels for the affinity slider.

[api_perm_is_allowed](https://hub.hubzilla.hu/help/hook/api_perm_is_allowed) Called when perm_is_allowed() is executed by an API call.

[app_destroy](https://hub.hubzilla.hu/help/hook/app_destroy) Called when an app is deleted.

[app_installed_filter](https://hub.hubzilla.hu/help/hook/app_installed_filter) Called when it is determined whether an app is[installed](https://hub.hubzilla.hu/help/hook/app_installed_filter)

[app_menu](https://hub.hubzilla.hu/help/hook/app_menu) Called when the app_menu dropdown is created (may be deprecated)

[attach_delete](https://hub.hubzilla.hu/help/hook/attach_delete) Called when attachments are deleted from the attach table

[atom_author](https://hub.hubzilla.hu/help/hook/atom_author) Called when an author or owner element is created for an Atom ActivityStream feed

[atom_entry](https://hub.hubzilla.hu/help/hook/atom_entry) Called when generating each entry of an Atom ActivityStream feed

[atom_feed](https://hub.hubzilla.hu/help/hook/atom_feed) Called when an Atom ActivityStreams feed is generated

[atom_feed_end](https://hub.hubzilla.hu/help/hook/atom_feed_end) Called when the generation of an Atom ActivityStreams feed is complete

[attach_upload_file](https://hub.hubzilla.hu/help/hook/attach_upload_file) Called when a file is uploaded

[authenticate](https://hub.hubzilla.hu/help/hook/authenticate) Can provide alternative authentication mechanisms

[author_is_pmable](https://hub.hubzilla.hu/help/hook/author_is_pmable) Called from the thread's action menu to determine if we can send a private email to the author of the post

[bb2diaspora](https://hub.hubzilla.hu/help/hook/bb2diaspora) Called when converting bbcode to Markdown

[bbcode](https://hub.hubzilla.hu/help/hook/bbcode) Called at the end of the conversion from bbcode to HTML

[bbcode_filter](https://hub.hubzilla.hu/help/hook/bbcode_filter) Called at the beginning of the conversion from bbcode to HTML

[bb_translate_video](https://hub.hubzilla.hu/help/hook/bb_translate_video) Called when extracting embedded services from bbcode video elements (rarely used)

[build_pagehead](https://hub.hubzilla.hu/help/hook/build_pagehead) Called when the HTML page header is created

[can_comment_on_post](https://hub.hubzilla.hu/help/hook/can_comment_on_post) Called when deciding whether or not to display a comment field for a post

[change_channel](https://hub.hubzilla.hu/help/hook/change_channel) Called when you log in to a channel (either during login or afterwards via the channel manager)

[channel_remove](https://hub.hubzilla.hu/help/hook/channel_remove) Called when a channel is removed

[channel_links](https://hub.hubzilla.hu/help/hook/channel_links) Is called when the link is generated: HTTP header for a channel

[channel_settings](https://hub.hubzilla.hu/help/hook/channel_settings) Called when the channel settings page is displayed

[chat_message](https://hub.hubzilla.hu/help/hook/chat_message) Called to create a chat message.

[chat_post](https://hub.hubzilla.hu/help/hook/chat_post) Called when a chat message has been posted.

[check_account_email](https://hub.hubzilla.hu/help/hook/check_account_email) Checks the email specified during account registration

[check_account_invite](https://hub.hubzilla.hu/help/hook/check_account_invite) Validation of an invitation code when using website invitations
[check_account_password](https://hub.hubzilla.hu/help/hook/check_account_password) Used to check account passwords (minimum length, inclusion of character sets, etc.)
[check_channelallowed](https://hub.hubzilla.hu/help/hook/check_channelallowed) Used to override or bypass black and white channel block lists.

[check_siteallowed](https://hub.hubzilla.hu/help/hook/check_siteallowed) Is used to override or bypass the black/white block lists for websites.

[collect_public_recipients](https://hub.hubzilla.hu/help/hook/collect_public_recipients) Used to create a list of recipients to send a public message to.

[comment_buttons](https://hub.hubzilla.hu/help/hook/comment_buttons) Called when the comment edit buttons are displayed.

[comments_are_now_closed](https://hub.hubzilla.hu/help/hook/comments_are_now_closed) Called when deciding whether or not to display a comment box for a post

[connect_premium](https://hub.hubzilla.hu/help/hook/connect_premium) Called when a connection to a premium channel is established

[connection_remove](https://hub.hubzilla.hu/help/hook/connection_remove) Called when a connection is deleted/removed

[connector_settings](https://hub.hubzilla.hu/help/hook/connector_settings) Called when the page with the features/addon settings is called up

[construct_page](https://hub.hubzilla.hu/help/hook/construct_page) General hook for providing content for specific page areas. Is called when the Comanche page is created.

[contact_block_end](https://hub.hubzilla.hu/help/hook/contact_block_end) Called when the ‘Connections’ widget is created in the sidebar

[contact_edit](https://hub.hubzilla.hu/help/hook/contact_edit) Called when editing a connection via connedit

[contact_edit_post](https://hub.hubzilla.hu/help/hook/contact_edit_post) Is called when a post is sent to connedit
[contact_selection_options](https://hub.hubzilla.hu/help/hook/contact_select_options) Deprecated/unused

[content_security_policy](https://hub.hubzilla.hu/help/hook/content_security_policy) Called before the Content-Security-Policy header is output

[conversation_start](https://hub.hubzilla.hu/help/hook/conversation_start) Called at the beginning of the rendering of a conversation (message or message collection or stream)

[cover_photo_content_end](https://hub.hubzilla.hu/help/hook/cover_photo_content_end) Called after a cover photo has been uploaded

[create_identity](https://hub.hubzilla.hu/help/hook/create_identity) Called when a channel is created

[cron](https://hub.hubzilla.hu/help/hook/cron) Called when a scheduled task (poller) is executed

[cron_daily](https://hub.hubzilla.hu/help/hook/cron_daily) Called when daily scheduled tasks are executed

[cron_weekly](https://hub.hubzilla.hu/help/hook/cron_weekly) Called when weekly scheduled tasks are executed

[crypto_methods](https://hub.hubzilla.hu/help/hook/crypto_methods) Called when a list of crypto algorithms is created in the locally preferred order

[daemon_addon](https://hub.hubzilla.hu/help/hook/daemon_addon) Called when the extensible background daemon is called

[daemon_master_release](https://hub.hubzilla.hu/help/hook/daemon_master_release) Called at the beginning of the processing of \Zotlabs\Daemon\Master::Release()

[directory_item](https://hub.hubzilla.hu/help/hook/directory_item) Called when creating a directory listing for display

[discover_channel_webfinger](https://hub.hubzilla.hu/help/hook/discover_channel_webfinger) Called when a webfinger lookup is performed

[display_item](https://hub.hubzilla.hu/help/hook/display_item) Called for each element that is displayed in a conversation thread

[display_settings](https://hub.hubzilla.hu/help/hook/display_settings) Called by the settings module when the ‘display settings’ section is displayed

[display_settings_post](https://hub.hubzilla.hu/help/hook/display_settings_post) Called when a post from the ‘display settings’ form of the settings module is displayed

[donate_contributors](https://hub.hubzilla.hu/help/hook/donate_contributors) Called by the ‘donate’ addon when a list of donation recipients is created

[donate_plugin](https://hub.hubzilla.hu/help/hook/donate_plugin) is called by the ‘donate’ addon

[donate_sponsors](https://hub.hubzilla.hu/help/hook/donate_sponsors) Called by the ‘donate’ addon

[dreport_is_storable](https://hub.hubzilla.hu/help/hook/dreport_is_storable) is called before saving a Dreport record to determine if it should be saved

[dreport_process](https://hub.hubzilla.hu/help/hook/dreport_process) is called for each valid delivery report

[dropdown_extras](https://hub.hubzilla.hu/help/hook/dropdown_extras) Add additional items to the dropdown menu when item/threads are displayed.

[drop_item](https://hub.hubzilla.hu/help/hook/drop_item) is called when an ‘item’ is removed

[encode_object](https://hub.hubzilla.hu/help/hook/encode_object) is called when an object is encoded for transmission.

[enotify](https://hub.hubzilla.hu/help/hook/enotify) is called before each notification

[enotify_mail](https://hub.hubzilla.hu/help/hook/enotify_mail) is called when a notification email is sent

[enotify_store](https://hub.hubzilla.hu/help/hook/enotify_store) is called when a notification data record is saved

[enotify_store_end](https://hub.hubzilla.hu/help/hook/enotify_store_end) is called after a notification record has been saved

[event_created](https://hub.hubzilla.hu/help/hook/event_created) is called when an event record is created

[event_store_event](https://hub.hubzilla.hu/help/hook/event_store_event) is called when an event record is created or updated

[event_updated](https://hub.hubzilla.hu/help/hook/event_updated) is called when an event record is changed

[externals_url_select](https://hub.hubzilla.hu/help/hook/externals_url_select) is called when a list of random websites from which to retrieve public posts is created

[feature_enabled](https://hub.hubzilla.hu/help/hook/feature_enabled) is called when ‘feature_enabled()’ is used

[feature_settings](https://hub.hubzilla.hu/help/hook/feature_settings) is called from the settings page when visiting ‘addon/feature settings’

[feature_settings_post](https://hub.hubzilla.hu/help/hook/feature_settings_post) is called from the settings page when posting from ‘addon/feature settings’

[fetch_and_store](https://hub.hubzilla.hu/help/hook/fetch_and_store) is called to enable filtering of ‘decrypted’ elements before saving.

[file_thumbnail](https://hub.hubzilla.hu/help/hook/file_thumbnail) is called when creating thumbnails for the cloud page in ‘show tiles’ mode

[follow](https://hub.hubzilla.hu/help/hook/follow) is called when a follow operation takes place

[follow_from_feed](https://hub.hubzilla.hu/help/hook/follow_from_feed) is called when a follow operation takes place in an RSS feed

[follow_allow](https://hub.hubzilla.hu/help/hook/follow_allow) is called before the results of a follow operation are saved

[gender_selector](https://hub.hubzilla.hu/help/hook/gender_selector) is called when the ‘Gender’ drop-down list is created (extended profile)

[gender_selector_min](https://hub.hubzilla.hu/help/hook/gender_selector_min) is called when the ‘Gender’ drop-down list is created (normal profile)

[generate_map](https://hub.hubzilla.hu/help/hook/generate_map) is called to generate the HTML code for displaying a location on the map by coordinates

[generate_named_map](https://hub.hubzilla.hu/help/hook/generate_named_map) is called to generate the HTML file for displaying a map location by text

[get_all_api_perms](https://hub.hubzilla.hu/help/hook/get_all_api_perms) Called when the permissions for API uses are retrieved

[get_all_perms](https://hub.hubzilla.hu/help/hook/get_all_perms) is called when get_all_perms() is used
[get_best_language](https://hub.hubzilla.hu/help/hook/get_best_language) is called when the preferred language for the page is selected
[get_default_export_sections](https://hub.hubzilla.hu/help/hook/get_default_export_sections) Called to get the default list of function data groups to be exported in identity_basic_export()

[get_features](https://hub.hubzilla.hu/help/hook/get_features) Called when get_features() is called

[get_photo](https://hub.hubzilla.hu/help/hook/get_photo) Called when photo content (except profile photos) is retrieved in mod_photo

[get_profile_photo](https://hub.hubzilla.hu/help/hook/get_profile_photo) Called when the content of the local profile photo is retrieved in mod_photo

[get_role_perms](https://hub.hubzilla.hu/help/hook/get_role_perms) Called when get_role_perms() is called to get permissions for named permission roles

[global_permissions](https://hub.hubzilla.hu/help/hook/global_permissions) Called when the global permissions list is created

[home_content](https://hub.hubzilla.hu/help/hook/home_content) Called by mod_home to replace the content of the home page

[home_init](https://hub.hubzilla.hu/help/hook/home_init) Called by the home_init() function of the home page

[hostxrd](https://hub.hubzilla.hu/help/hook/hostxrd) Called when generating .well-known/hosts-meta for ‘old webfinger’ (used by the Diaspora protocol)

[html2bb_video](https://hub.hubzilla.hu/help/hook/html2bb_video) Called when html2bbcode translation is used to handle embedded media

[html2bbcode](https://hub.hubzilla.hu/help/hook/html2bbcode) Called when using the html2bbcode translation

[identity_basic_export](https://hub.hubzilla.hu/help/hook/identity_basic_export) Called when the basic information of a channel is exported for backup or transfer.

[import_author_xchan](https://hub.hubzilla.hu/help/hook/import_author_xchan) Called when searching for an author of a post with xchan_hash to make sure they have an xchan entry on our website

[import_channel](https://hub.hubzilla.hu/help/hook/import_channel) Called when a channel is imported from a file or API source

[import_directory_profile](https://hub.hubzilla.hu/help/hook/import_directory_profile) Called when processing the delivery of a profile structure from an external source (usually for storage in directories)

[import_xchan](https://hub.hubzilla.hu/help/hook/import_xchan) Called when processing the result of zot_finger() to save the result

[item_photo_menu](https://hub.hubzilla.hu/help/hook/item_photo_menu) Called when the list of actions associated with a displayed conversation item is generated

[item_store](https://hub.hubzilla.hu/help/hook/item_store) Called when item_store() stores a record of type item

[item_stored](https://hub.hubzilla.hu/help/hook/item_stored) Called after item_store() has stored a record of type item in the database.

[item_custom](https://hub.hubzilla.hu/help/hook/item_custom) Is called before item_store() saves a data record of the type item (so that addons can process ITEM_TYPE_CUSTOM elements).

[item_store_update](https://hub.hubzilla.hu/help/hook/item_store_update) Called when item_store_update() is called to update a stored item.

[item_stored_update](https://hub.hubzilla.hu/help/hook/item_stored_update) Called after item_store_update() has updated a[stored](https://hub.hubzilla.hu/help/hook/item_stored_update) item.

[item_translate](https://hub.hubzilla.hu/help/hook/item_translate) Called by item_store and item_store_update after the language of the item has been automatically recognised.

[jot_networks](https://hub.hubzilla.hu/help/hook/jot_networks) Called to generate the list of additional post plugins to be activated from the ACL form

[jot_tool](https://hub.hubzilla.hu/help/hook/jot_tool) Obsolete and possibly superfluous. Enables action buttons to be added to the post editor.

[jot_tpl_filter](https://hub.hubzilla.hu/help/hook/jot_tpl_filter) Called to filter template variables before replacing them in jot.tpl.

[jot_header_tpl_filter](https://hub.hubzilla.hu/help/hook/jot_header_tpl_filter) Called to filter template variables before replacing them in jot_header.tpl.

[legal_webbie](https://hub.hubzilla.hu/help/hook/legal_webbie) Called to validate a channel address

[legal_webbie_text](https://hub.hubzilla.hu/help/hook/legal_webbie_text) Provides an explanation of the text/character restrictions for legal_webbie()

[load_pdl](https://hub.hubzilla.hu/help/hook/load_pdl) Called when we load a PDL file or description

[local_dir_update](https://hub.hubzilla.hu/help/hook/local_dir_update) Called when a directory update is processed by a channel on the directory server

[location_move](https://hub.hubzilla.hu/help/hook/location_move) Called when a UNO channel has been notified of a new location (indicating a move and not a clone)

[logged](https://hub.hubzilla.hu/help/hook/logged_in) Called when authentication was successful in any way

[Logger](https://hub.hubzilla.hu/help/hook/logger) Called when an entry is made in the application's log file

[logging_out](https://hub.hubzilla.hu/help/hook/logging_out) Called when logging[out](https://hub.hubzilla.hu/help/hook/logging_out)

[login_hook](https://hub.hubzilla.hu/help/hook/login_hook) Called when the login form is generated

[magic_auth](https://hub.hubzilla.hu/help/hook/magic_auth) Called when processing a magic-auth sequence

[markdown_to_bb](https://hub.hubzilla.hu/help/hook/markdown_to_bb) Called when processing the Markdown conversion

[match_webfinger_location](https://hub.hubzilla.hu/help/hook/match_webfinger_location) Called when processing webfinger requests

[magic_auth_openid_success](https://hub.hubzilla.hu/help/hook/magic_auth_openid_success) Called when a magic-auth was successful due to openid credentials

[magic_auth_success](https://hub.hubzilla.hu/help/hook/magic_auth_success) Called when a magic-auth was successful

[main_slider](https://hub.hubzilla.hu/help/hook/main_slider) Called when the affinity tool is generated

[marital_selector](https://hub.hubzilla.hu/help/hook/marital_selector) Called when the selection list for the drop-down menu of the ‘Marital status’ profile is created (extended profile)

[marital_selector_min](https://hub.hubzilla.hu/help/hook/marital_selector_min) Called when the selection list for the ‘Marital status’ drop-down profile is created (normal profile)

[module_loaded](https://hub.hubzilla.hu/help/hook/module_loaded) Is called when a module has been successfully localised for a URL request on the server.

[mood_verbs](https://hub.hubzilla.hu/help/hook/mood_verbs) Called when the list of moods is created
[nav](https://hub.hubzilla.hu/help/hook/nav) Called when the navigation bar is created

[network_content_init](https://hub.hubzilla.hu/help/hook/network_content_init) Called when loading the content for the network page

[network_ping](https://hub.hubzilla.hu/help/hook/network_ping) Called when a ping request is made
[network_to_name](https://hub.hubzilla.hu/help/hook/network_to_name) Deprecated

[notifier_end](https://hub.hubzilla.hu/help/hook/notifier_end) Called when a delivery loop is completed

[notifier_hub](https://hub.hubzilla.hu/help/hook/notifier_hub) Called when a hub has been delivered

[notifier_normal](https://hub.hubzilla.hu/help/hook/notifier_normal) Called when the notifier is called for a ‘normal’ delivery

[notifier_process](https://hub.hubzilla.hu/help/hook/notifier_process) Called when the notifier processes a message/event

[obj_verbs](https://hub.hubzilla.hu/help/hook/obj_verbs) Called when the list of verbs available for the ‘things’ profile is created.

[oembed_action](https://hub.hubzilla.hu/help/hook/oembed_action) Called when deciding whether to filter, block or approve an oembed url

[oembed_probe](https://hub.hubzilla.hu/help/hook/oembed_probe) Called when a search for Oembed content is performed.

[other_encapsulate](https://hub.hubzilla.hu/help/hook/other_encapsulate) Called when encrypting content for which the algorithm is unknown (see also crypto_methods)

[other_unencapsulate](https://hub.hubzilla.hu/help/hook/other_unencapsulate) Called when decrypting content for which the algorithm is unknown (see also crypto_methods)

[page_content_top](https://hub.hubzilla.hu/help/hook/page_content_top) Called when we generate a web page (before calling the module content function)

[page_end](https://hub.hubzilla.hu/help/hook/page_end) Called after we have generated the page content

[page_header](https://hub.hubzilla.hu/help/hook/page_header) Called when the navigation bar is generated

[page_meta](https://hub.hubzilla.hu/help/hook/page_header) Called when generating the metadata in the page header.

[parse_atom](https://hub.hubzilla.hu/help/hook/parse_atom) Called when an Atom/RSS feed element is parsed.

[parse_link](https://hub.hubzilla.hu/help/hook/parse_link) Is called when a URL is queried to generate a post from it

[pdl_selector](https://hub.hubzilla.hu/help/hook/pdl_selector) Called when creating a layout selection in a form

[perm_is_allowed](https://hub.hubzilla.hu/help/hook/perm_is_allowed) Called during perm_is_allowed() to determine whether authorisation is permitted for this channel and observer

[permissions_create](https://hub.hubzilla.hu/help/hook/permissions_create) Called when a book entry (connection) is created

[permissions_update](https://hub.hubzilla.hu/help/hook/permissions_update) Is called when an authorisation update is transferred

[permit_hook](https://hub.hubzilla.hu/help/hook/permit_hook) Called before a registered hook is actually executed to determine whether it should be allowed or blocked

[personal_xrd](https://hub.hubzilla.hu/help/hook/personal_xrd) Called when generating the personal XRD for ‘old webfinger’ (Diaspora)

[photo_post_end](https://hub.hubzilla.hu/help/hook/photo_post_end) Called after a photo has been uploaded

[photo_upload_begin](https://hub.hubzilla.hu/help/hook/photo_upload_begin) Called when an attempt is made to upload a photo

[photo_upload_end](https://hub.hubzilla.hu/help/hook/photo_upload_end) Called when a photo upload has been processed

[photo_upload_file](https://hub.hubzilla.hu/help/hook/photo_upload_file) Called to generate alternative file names for an upload

[photo_upload_form](https://hub.hubzilla.hu/help/hook/photo_upload_form) Called when a photo upload form is generated

[photo_view_filter](https://hub.hubzilla.hu/help/hook/photo_view_filter) Called before the data is passed to the photo_view template

[poke_verbs](https://hub.hubzilla.hu/help/hook/poke_verbs) Called when creating the list of actions for the ‘poke’ module

[post_local](https://hub.hubzilla.hu/help/hook/post_local) Called when an article has been set on this computer via mod/item.php (also via API)

[post_local_end](https://hub.hubzilla.hu/help/hook/post_local_end) Is called when a local post process has been completed

[post_local_start](https://hub.hubzilla.hu/help/hook/post_local_start) Is called when a local post process begins
[post_mail](https://hub.hubzilla.hu/help/hook/post_mail) Called when a mail message has been created

[post_mail_end](https://hub.hubzilla.hu/help/hook/post_mail_end) Called when a mail message has been delivered

[post_remote](https://hub.hubzilla.hu/help/hook/post_remote) Called when an activity arrives from another location

[post_remote_end](https://hub.hubzilla.hu/help/hook/post_remote_end) Called after a remote post has been processed

[post_remote_update](https://hub.hubzilla.hu/help/hook/post_remote_update) Called when processing a remote post that includes an edit or update

[post_remote_update_end](https://hub.hubzilla.hu/help/hook/post_remote_update_end) Called after processing a remote post that included an edit or update

[prepare_body](https://hub.hubzilla.hu/help/hook/prepare_body) Is called when the HTML code for a displayed conversation object is generated.

[prepare_body_final](https://hub.hubzilla.hu/help/hook/prepare_body_final) Called after the HTML for a displayed conversation item has been generated

[prepare_body_init](https://hub.hubzilla.hu/help/hook/prepare_body_init) Called before the HTML for a displayed conversation element is generated

[privacygroup_extras](https://hub.hubzilla.hu/help/hook/privacygroup_extras) Called before generating the HTML for the privacy group editing options

[privacygroup_extras_delete](https://hub.hubzilla.hu/help/hook/privacygroup_extras_delete) Called after the privacy group has been deleted.

[privacygroup_extras_post](https://hub.hubzilla.hu/help/hook/privacygroup_extras_post) Is called when the form for editing the privacy group is sent.

[proc_run](https://hub.hubzilla.hu/help/hook/proc_run) Called when PHP sub-processes are called
[process_channel_sync_delivery](https://hub.hubzilla.hu/help/hook/process_channel_sync_delivery) Called when a ‘sync package’ with structure and table updates is received from a channel clone.

[profile_advanced](https://hub.hubzilla.hu/help/hook/profile_advanced) Called when an advanced profile page is generated

[profile_edit](https://hub.hubzilla.hu/help/hook/profile_edit) Called when editing a profile

[profile_photo_content_end](https://hub.hubzilla.hu/help/hook/profile_photo_content_end) Called when a profile photo is changed

[profile_post](https://hub.hubzilla.hu/help/hook/profile_post) Called when an edited profile is posted

[profile_sidebar](https://hub.hubzilla.hu/help/hook/profile_sidebar) Is called up when the ‘channel sidebar’ or the mini profile is created

[profile_sidebar_enter](https://hub.hubzilla.hu/help/hook/profile_sidebar_enter) Called before the ‘channel sidebar’ or mini-profile is created

[queue_deliver](https://hub.hubzilla.hu/help/hook/queue_deliver) Called when a message is delivered in the queue

[register_account](https://hub.hubzilla.hu/help/hook/register_account) Called when an account has been created

[render_location](https://hub.hubzilla.hu/help/hook/render_location) Called to create an inactive inline map

[replace_macros](https://hub.hubzilla.hu/help/hook/replace_macros) Called before the template processor is called

[reverse_magic_auth](https://hub.hubzilla.hu/help/hook/reverse_magic_auth) Called before calling reverse magic auth to send you to your own website so you can authenticate on that website

[settings_account](https://hub.hubzilla.hu/help/hook/settings_account) Called when the account settings form is created

[settings_form](https://hub.hubzilla.hu/help/hook/settings_form) Called when creating the channel settings form

[settings_post](https://hub.hubzilla.hu/help/hook/settings_post) Called when posting from the channel settings form

[sexpref_selector](https://hub.hubzilla.hu/help/hook/sexpref_selector) Called when creating a drop-down menu for sexual preferences (advanced profile)

[sexpref_selector_min](https://hub.hubzilla.hu/help/hook/sexpref_selector_min) Called when a drop-down list of sexual preferences is created (normal profile)

[smilie](https://hub.hubzilla.hu/help/hook/smilie) Called when translating emoticons

[status_editor](https://hub.hubzilla.hu/help/hook/status_editor) Called when the status_editor is created.

[stream_item](https://hub.hubzilla.hu/help/hook/stream_item) Called for each item that is rendered for display via conversation()

[system_app_installed_filter](https://hub.hubzilla.hu/help/hook/system_app_installed_filter) Called when it is determined whether a system app is[installed](https://hub.hubzilla.hu/help/hook/system_app_installed_filter).

[tagged](https://hub.hubzilla.hu/help/hook/tagged) Called when a delivery is processed that results in you being tagged

[thumbnail](https://hub.hubzilla.hu/help/hook/thumbnail) Called when creating thumbnails for the cloud storage tile view

[update_unseen](https://hub.hubzilla.hu/help/hook/update_unseen) Called before automatically tagging programmes loaded in the browser that have been viewed

[validate_channelname](https://hub.hubzilla.hu/help/hook/validate_channelname) Used to validate the names used by a channel

[webfinger](https://hub.hubzilla.hu/help/hook/webfinger) Called when visiting the webfinger service (RFC7033)

[well_known](https://hub.hubzilla.hu/help/hook/well_known) Called when accessing the special ‘.well-known’ site addresses

[wiki_preprocess](https://hub.hubzilla.hu/help/hook/wiki_preprocess) Called before markdown/bcode processors are executed for wiki pages

[zot_best_algorithm](https://hub.hubzilla.hu/help/hook/zot_best_algorithm) Called when negotiating encryption algorithms with remote sites

[zid](https://hub.hubzilla.hu/help/hook/zid) Called when the observer's zid is added to a URL

[zid_init](https://hub.hubzilla.hu/help/hook/zid_init) Called when authenticating a visitor who has used zid

[zot_finger](https://hub.hubzilla.hu/help/hook/zot_finger) Called when a Nomad info packet has been requested (this is our web finger detection mechanism)