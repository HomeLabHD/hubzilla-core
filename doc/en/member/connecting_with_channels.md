### Connecting with channels

Connections in Hubzilla can have many different meanings. A connection is more precisely defined as a set of permissions that you have granted to another person. In traditional social networks, all connections are given the same permissions or at most two levels (friends and ‘followers’). In Hubzilla, a separate set of permissions can be set/customised depending on the situation and the relationship you have with the other channel. You can allow someone to see your posts, but not your photos. You can also deny them permission to comment on your posts or send private messages to you. But let's make it simple: you want to be friends with someone you know from social networks. How do you do that?

You can view the directory. The directory is available on all Hubzilla sites, so if you search from your own site, you'll get results from across the network. You can search by name, interest, location and keyword.
If you already know someone's ‘handle’, you can contact them directly. A handle looks just like an email address (e.g. `bob@example.com)` but refers to a person in the open social network. In order to establish a connection, a compatible network protocol must be used. By default, this software supports the Nomad protocol, but other protocols can be provided via plugins/add-ons. For more information on connecting to channels on other networks, see below.

#### How to connect to other Hubzilla channels:

Visit the desired channel's profile by clicking on their photo in the directory, stream or comments and it will open their channel homepage in the channel viewer. On the left side of the screen you will normally see a link labelled ‘Connect’. Click on it and you're done. Depending on the settings of the channel you want to connect to, you may have to wait for the channel to approve your connection, but no further action is required on your part. Once you have initiated the connection, you will be redirected to the connection editor. Here you can assign specific authorisations for this channel if you want to make changes.

You can also create a connection to any channel by going to the ‘Connections’ page of your website or directory and entering the ‘Handle’ in the ‘Add new connection’ field. Use this method if someone tells you their handle and you want to connect to them. The process is the same as when connecting via the ‘Connect’ button - you will then be redirected to the connection editor to set the authorisations.

#### This is how you establish a connection to channels in other networks:

The process for connecting to ‘channels’ on other networks (such as GNU Social, Mastodon, Misskey, Pleroma and Diaspora) is similar - enter their ‘handle’ in the ‘+Add’ field on the ‘Connections’ page. However, before you do this, please visit the App Management in the app menu and make sure that the appropriate protocol (Diaspora, GNU-Social/OStatus or ActivityPub) is deployed in your hub and ***enabled*** **for your channel**. These networks/protocols do not support account migration and location independence. So if you change location or clone your channel elsewhere, communication with these connections may fail. For this reason, these protocols are not enabled by default, but only with your consent. Enabling these protocols is an important decision between communicating with friends on these networks and account resilience in case your server goes down.

Some communication networks offer more than one protocol. For example, you can connect to someone who uses both the ‘ostatus’ and ‘activitypub’ protocols for communication. In general, the ‘activitypub’ protocol provides a better experience than the ‘ostatus’ protocol, but Hubzilla often chooses the first protocol it detects, and that may not be what you want. You can connect to someone using a specific protocol by putting the protocol name in square brackets before their ‘handle’. For example

`[activitypub]https://foo.bar/foobar`

`[ostatus]foobar@foo.bar`

`[diaspora]foobar@foo.bar[zot]foobar@foo.bar`

`[feed]https://foo.bar/foobar`

#### How to connect to RSS feeds:

Your hub administrator can allow you to connect to RSS feeds. The procedure for connecting to an RSS feed is the same, except that you enter (or paste) the URL of the feed into the ‘Add new connection’ field. The options may be restricted by your hub administrator because connections to feeds can sometimes cause high system loads.

