### Functions 

The following are some of the main features of Hubzilla that are included in the official version. Hubzilla is a highly extensible platform, so more features and capabilities can be added through additional themes and plugins.

#### Affinity slider 

When adding connections in Hubzilla, members have the option to assign ‘affinity levels’ (how close your friendship is) to the new connection. For example, if you add someone whose blog you follow, you can assign their channel an ‘Acquaintance’ affinity level.

On the other hand, if you add a friend's channel, you could assign them the ‘Friends’ affinity level.

At this point, the Hubzilla *affinity slider tool*, which usually appears at the top of your ‘Stream’ page, will adjust the content of the page to display channels within the desired affinity range. Channels outside this range will not be displayed unless you adjust the slider to include them.

The affinity slider allows for instant filtering of large amounts of content, grouped by degree of proximity.

#### Filtering connections 

The optional ‘connection filter’ gives you the ability to control exactly what is displayed in your stream. When enabled, the connection editor provides inputs to select criteria that must be met to include or exclude a specific post from a specific channel. Once a post has been allowed, all comments on that post are allowed, regardless of whether they meet the selection criteria. You can select words that, if present, will block the post or ensure that it is included in your stream. Regular expressions can be used for even finer control, as well as hashtags or even the recognised language of the post.

#### Access control lists 

When sharing content, members have the option to restrict who can see the content. By clicking on the padlock below the share field, you can select the desired recipients of the post by clicking on their name.

Once sent, the message can only be viewed by the sender and the selected recipients. In other words, the message will not appear on public walls.

Access control lists can be applied to content and posts, photos, events, websites, chat rooms and files.

#### Single sign-on 

Access control lists work for all channels in the grid thanks to our unique single sign-on technology. Most internal links provide an identity token that can be verified on other Hubzilla sites and used to control access to private resources. You log in to your home hub once. After that, authentication is ‘magic’ for all Hubzilla resources.

#### WebDAV-enabled file storage 

Files can be uploaded to your personal storage area using your operating system's utilities (in most cases by drag & drop). You can protect these files with access control lists for any combination of Hubzilla members (including some third-party network members) or make them publicly accessible.

#### Photo albums 

Store photos in albums. All your photos can be protected by access control lists.

#### Events calendar 

Create and manage events and tasks, which can also be protected with access control lists. Events can be imported/exported to other software using the industry standard vcalendar/iCal format and shared in posts. Birthday events are automatically added by your friends and converted to your correct time zone so you know exactly when the birthday takes place - regardless of where in the world you are in relation to the birthday person. Events are usually created with attendance counters so that your friends and connections can instantly approve.

#### Chat rooms 

You can create any number of personal chat rooms and allow access via access control lists. These are usually more secure than XMPP, IRC and other instant messaging transports, although we also allow the use of these other services via plugins.

#### Website creation 

Hubzilla has numerous ‘content management’ tools for creating web pages, including layout editing, menus, blocks, widgets and page/content areas. All of these tools can be access controlled so that the resulting pages are only accessible to the intended audience.

#### Apps 

Apps can be created and distributed by members. These differ from traditional ‘vendor lockin’ apps in that they are completely controlled by the author - who can control access to the app's target pages and pay for that access accordingly. Most of the apps in Hubzilla are free and can be easily created by people without programming skills.

#### Layout 

The page layout is based on a description language called Comanche. Hubzilla itself is written in Comanche layouts, which you can change. This allows a level of customisation not normally found in so-called ‘multi-user environments’.

#### Bookmarks 

Share and save/manage bookmarks of links provided in conversations.

#### Encryption of private messages and data protection aspects 

Private messages are saved in an encrypted format. Although this is not 100% secure, it usually prevents the occasional spying by the site administrator or ISP.

Each Hubzilla channel has its own set of private and associated public RSA 4096-bit keys, which are generated when the channel is first created. This is used to protect private messages and posts during transmission.

Additionally, messages can be created with ‘end-to-end encryption’ that cannot be read by Hubzilla operators, ISPs, or anyone else who does not know the passcode.

Public messages are generally not encrypted during transmission or storage.

Private messages can be withdrawn (not sent), although there is no guarantee that the recipient has not yet read them.

Posts and messages can be given an expiry date, after which they are deleted/removed from the recipient's page.

#### Service federation 

As well as additional ‘cross-post connectors’ to a variety of alternative networks, there is native support for importing content from RSS/Atom feeds and using them to create specialised channels. Plugins are also available to communicate with others via the Diaspora and GNU-Social (OStatus) protocols. These networks do not support nomadic identity or cross-domain access control; however, basic communication to/from Diaspora, Friendica, GNU-Social, Mastodon and other providers using these protocols is supported.

There is also experimental support for OpenID authentication that can be used in access control lists. This is a project that is being worked on. Your Hubzilla hub can be used as an OpenID provider to authenticate you to external services that use this technology.

Channels can be authorised to become ‘derivative channels’, where two or more existing channels are combined to create a new thematic channel.

#### Privacy groups 

Our implementation of privacy groups is similar to Google's ‘Circles’ and Diaspora's ‘Aspects’. This allows you to filter your incoming stream by selected groups and automatically restrict the outgoing access control list to members of that group when you post. You can undo this at any time (before sending the post).

#### Directory services 

We provide easy access to a member directory and provide decentralised tools that can make ‘suggestions’ to friends. The directories are normal Hubzilla sites that have decided to take on the role of directory server. This requires more resources than most typical sites and is therefore not the default setting. The directories are synchronised and mirrored so that they all contain up-to-date information across the network (subject to normal propagation delays).

#### TLS/SSL 

For Hubzilla hubs that use TLS/SSL, communication between client and server is encrypted via TLS/SSL. In light of recent media revelations about widespread global surveillance and encryption circumvention by the NSA and GCHQ, it is reasonable to assume that HTTPS-protected communications can be compromised in a number of ways. Private communication is therefore encrypted at a higher level before it is sent to the outside world.

#### Channel settings 

When a channel is created, a role is selected that applies a set of pre-configured security and privacy settings. These are selected according to best practice to maintain the desired level of data protection.

If you select a ‘custom’ privacy role, you can set fine-grained permissions for different aspects of communication for each channel. For example, under the heading ‘Security and Privacy Settings’, there are six (6) possible display/access options for each aspect on the left-hand side, which can be selected by clicking on the drop-down menu. There are also a number of other privacy settings that you can edit.

The options are:

\- No one but yourself. - Only those you explicitly authorise. - Anyone in your address book. - Everyone on this website. - Everyone on this network. - Everyone who is authenticated. - Anyone who is on the internet.

#### Public and private forums 

Forums are usually channels in which several authors can participate. There are currently two ways to publish posts in forums: 1) ‘wall-to-wall’ posts and 2) via @mention tags in the forum. Forums can be created by anyone and used for any purpose. The directory includes an option to search for public forums. Private forums can only contain posts that are often only seen by members.

#### Cloning accounts 

Accounts in Hubzilla are referred to as *nomadic identities* because a member's identity is not tied to the hub where the identity was originally created. For example, if you create a Facebook or Gmail account, it is tied to those services. You cannot function without Facebook.com or Gmail.com.

Let's say you created a Hubzilla identity called **`tina@hubzillahub.com`**. You can clone it to another Hubzilla hub by choosing the same or a different name: **`liveForever@somehubzillahub.info`**

Both channels will now be synchronised, i.e. all your contacts and settings will be duplicated on your clone. It does not matter whether you send a post from your original hub or from the new hub. The posts will be mirrored on both accounts.

This is a pretty revolutionary feature if we consider a few scenarios:

- What happens if the hub an identity is in suddenly goes offline? Without cloning, a member is unable to communicate until the hub is back online (no doubt many of you have seen and cursed Twitter's ‘Fail Whale’). With cloning, you simply log into your cloned account and life goes merrily on.
- The administrator of your hub can no longer afford to pay for his free and public Hubzilla hub. He announces that the hub will be shut down in a fortnight. This gives you plenty of time to clone your identity(ies) and preserve your Hubzilla relationships, friends and content.
- What if your identity is subject to government censorship? Your Hub provider may be forced to delete your account and all identities and associated data. With cloning, Hubzilla offers **resistance to censorship**. If you want, you can have hundreds of clones, each with a different name, scattered across many different hubs on the Internet.

Hubzilla offers interesting new possibilities for privacy. For more information, see the [Tips for protecting your privacy](./usermanual/protection_of_privacy.md) page.

Some caveats apply. A full explanation of identity cloning can be found on the [Clone](./usermanual/clone.md) page.

#### Multiple profiles 

Any number of profiles can be created with different information that can be made visible to certain of your connections/friends. A ‘standard’ profile can be viewed by anyone and can contain limited information, while more information is accessible to selected groups or individuals. This means that the profile (and site content) that your beer-drinking friends see may be different from what your peers see, and also completely different from what is visible to the general public.

#### Account backup 

Hubzilla offers a simple one-click account backup where you can download a full backup of your profile. The backups can then be used to clone or restore a profile.

#### Account deletion 

Accounts can be deleted instantly by clicking on a link. That's it. All associated content will then be deleted from the network (including posts and any other content created by the deleted profile). Depending on the number of connections you have, the process of deleting removed content may take some time, but it will be done as quickly as possible.

#### Deletion of content 

All content created in Hubzilla remains under the control of the member (or channel) who originally created it. A member can delete a message or a series of messages at any time. The deletion process ensures that the content is deleted regardless of whether it was posted on a channel's primary (home) hub or on another hub where the channel has been remotely authenticated via Nomad (Hubzilla communication and authentication protocol).

#### Media 

Similar to any other modern blogging system, social network or micro-blogging service, Hubzilla supports uploading files, embedding videos and linking to websites.

#### Preview/editing 

Posts and comments can be previewed before sending and edited after sending.

#### Voting/polls 

Posts can be converted into ‘poll’ elements that allow readers to provide feedback summarised in ‘agree’, ‘disagree’ and ‘abstain’ counters. This allows you to gauge interest in ideas and create informal polls.

#### Extending Hubzilla 

Hubzilla can be extended in various ways: website customisation, personalisation, option settings, themes and addons/plugins.

#### API 

An API is available for use by third-party services. A plugin also provides a basic implementation of the Twitter API (for which there are hundreds of third-party tools). Access can be via login/password or OAuth, and client registration of OAuth applications is provided.