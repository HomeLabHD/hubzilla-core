### Technical introduction 

Nomad is a JSON-based web framework for implementing secure decentralised communication and services. To provide this functionality, Nomad creates a decentralised, globally unique identifier for each node in the network. This global identifier is not inextricably linked to the DNS, ensuring the required mobility. Many of the existing decentralised communication frameworks provide the communication aspect, but no remote access control and authentication. Furthermore, most of these systems are based on the ‘web finger’, which still ties identity to domain names and cannot support nomadic identity.
The main problems that Nomad addresses are 

- Fully decentralised communication
- Independence from DNS-based identity
- node mobility
- seamless remote authentication
- high performance

We will rely on DNS-based user@host addresses as a ‘user-friendly’ mechanism to tell others where you are, namely on a named host with a specific username, and communication with DNS entities is handled over TCP and the web.

However, the underlying protocol provides an abstraction layer on top of this so that a communication node (e.g. ‘identity’) can move to a different DNS location and recover (to the best of its ability) from location changes and/or maintain pre-existing communication relationships. A side effect of this requirement is the ability to communicate from alternative/multiple DNS locations and service providers and still maintain a single online identity.

We call this overlay network the ‘grid’. The servers connected to this network are called ‘hubs’ and can support any number of individual identities.

An identity does not necessarily have to correspond to a person. It is merely something that requires the ability to communicate within the grid.

The ability to recover is achieved by communicating with the original location when a new or replacement identity is created, or as a fallback to a stored file describing the identity and its contacts in the event that the old location no longer responds.

At least in the short term, mobility of existing content is not the highest priority. This may or may not happen at a later date. The most important things we want to keep are your identity and your friends. 
Addresses that are shared by multiple people are user@host and describe the **current** local credentials for a particular identity. They are DNS-based addresses that are used as a seed to localise a particular identity within the network. Machine communication binds this address to a globally unique ID. A single globally unique ID can be attached or bound to any number of DNS locations. Once an identity is mapped or bound to a DNS location, communication consists only of knowing the globally unique address and the DNS (url) currently in use (to recall and verify/complete the current communication). These concepts will be specified in more detail in the future.

For an identity to persist across locations, one must be able to provide or recover 

- the globally unique ID for that identity
- the private key assigned to that identity
- (if the original server no longer exists) an address book of contacts for that identity.

This information can be exported from the original server via an API and/or downloaded to a hard drive or USB stick.

We can also attempt to restore the identity with even less information, but this is vulnerable to account hijacking and requires your contacts to confirm the change.

To enable high performance communication, the data transfer format for all aspects of Nomad is JSON. XML communication requires far too much overhead.

Bi-directional encryption is based on 4096-bit RSA keys expressed in DER/ASN.1 format using the PKCS#8 encoding variant, with AES encryption of variable length or large elements. The exact encryption algorithms are negotiable between sites.

Some aspects of the well-known ‘federation protocols’ (Webfinger, Salmon, Activitystreams, Portablecontacts, etc.) can be used in Nomad, but we are not and will not be bound by them. The Hubzilla project is trying some fairly novel developments in the field of decentralised communication, and if it should be necessary to deviate from such ‘standard protocols’, we will do so without question or hesitation. 
To create a globally unique ID, we will base it on a whirlpool hash of the origin node's identity URL and a pseudo-random number, which should give us a 256-bit ID with an extremely low collision probability (256 bits equals about 115 quattuorviginitillion or 1.16 X 10^77 unique numbers). This is represented in the communication as a base64url encoded string. However, we will not rely on probabilities, and the ID must also be associated with a public key, using public key cryptography to provide an identity guarantee that has not been copied or somehow collided in the whirlpool hash space.

Using the DNS as the basis for the ID provides a globally unique seed, which would not be the case if the ID was based entirely on pseudo-random number generation.

We refer to the encoded globally unique uid string as zot_uid 

As there may be more than one DNS location associated/connected to a particular zot_uid identity, the delivery processes should deliver to all of these locations as we do not know with certainty which hub instance can be accessed at any given time. However, we will designate one DNS location as ‘primary’ which will be the preferred location for displaying web profile information.

We also need to provide the ability to change the primary location to a new location. A lookup of information about the current primary location can provide a ‘redirect pointer’ that instructs us to update our listings and move everything to the new location. There is also the possibility that the primary location no longer exists and is no longer responding. In this case, a location that is already assigned to this zot_uid can take control and declare itself as primary. In both cases, the primary designation is automatically confirmed and moved. A request can also be made from a primary location requesting the removal of another location.

To assign a zot_uid to a second (or tertiary) location, a secure exchange is required to verify that the new location is in possession of the private key for that zot_uid. The security of the private key is therefore essential to prevent identity hijacking.

Communication then requires

- zot_uid (character string)
- uid_sig
- callback (current location Nomad endpoint url)
- callback_sig
- spec (int)

is transferred with every message. The spec is a revision number of the current Nomad specification so that communication with hubs that use older and possibly incompatible protocol specifications can be maintained. Communication is verified using a stored public key that was copied to this zot_uid when the connection was first established.

The revocation and replacement of the key must be carried out from the primary hub. The exact form for this is still being worked out, but will likely be a notification to all other bound hubs to ‘phone home’ (to the primary hub) and request a copy of the new key. This notification should be verified with a site or hub key, as the original identity key may have been compromised and cannot be trusted. 
To avoid confusion, there should be exactly one canonical url for each hub so that it can be indexed and uniquely referenced.

To avoid ambiguity of the scheme, it is strongly recommended that all addresses should be https with a browser valid certificate and a single valid host component (either [www.domain.ext](www.domain.ext) or domain.ext, but not both) used in all communication. Multiple URLs can be specified locally, but only a single URL should be used for all Nomad communication within the grid.

Test installations that are not connected to the public network can use non-SSL, but all traffic flowing over public networks should be protected from session hijacking, preferably with a ‘browser recognised’ certificate.

Where possible, the Nomad recommends the use of ‘batching’ to minimise network traffic between two hubs. This means that ‘Site A’ can send multiple messages to ‘Site B’ in a single transaction and also consolidate the delivery of identical messages to multiple recipients in the same hub.

The messages themselves may or may not be encrypted in transit, depending on the private nature of the messages. SSL (strongly recommended) provides unconditional encryption of the data stream, however it makes little sense to encrypt public messages that have been categorised as having unrestricted visibility. Encryption of data storage and so-called ‘end-to-end encryption’ are outside the scope of Nomad. It is assumed that node operators take appropriate security precautions to ensure the security of their data stores, and these are functions of application and site integrity as opposed to protocol integrity. 

#### Messaging   

Given the constraints listed above, a Nomad message should therefore be a json array of individual messages. These can be mixed and combined within the same transmission.

Each message then requires:

- Type
- (optional) recipient list

The absence of a recipient list would indicate an unencrypted public message or a site-level message. The recipient list would contain an array of zot_uid with an individual decryption key and a common iv. The decryption key is encrypted with the public key of the recipient identity. The iv is encrypted with the private key of the sender.

All messages should be digitally signed by the sender.

The type can be one of the following (this list can be extended):

- Mail (or activity)
- Mail
- identity
- authenticate

Identity messages have no recipients and notify the system social graph of an identity update, which can be a new or deleted identity, a new or deleted location, or a change to the primary hub. The signature for these messages uses system keys as opposed to identity-specific keys.

Posts include many different types of activity, such as top-level posts, likes/dislikes, comments, tagging activity, etc. These types are specified in the message structure.

Authentication messages result in mutual authentication and browser redirection to protected resources on the remote hub, such as the ability to post wall-to-wall messages and view private photo albums and events, etc.

#### Exploration   

A known URL is used to probe a hub for Nomad capabilities and identity queries, including discovery of public keys, profile locations, profile photos, and the primary hub location.

The location for this service is /.well-known/zot-info - and must be available in the root directory of the selected domain.

To perform a query, a POST request is made to the discovery location with the following parameters:
Required:

Address => an address on the target system such as ‘john’

Optional:

target => the observer's Nomad ‘guid’ for the evaluation of authorisations

target_sig => an RSA signature (base64url-encoded) of the guid

key => The public key required to verify the signature

token => a character string selected by the requesting service (possibly random). If present, an entry in the discovered packet labelled ‘signed_token’ is provided, which consists of the base64url_encoded RSA signature of the concatenation of the string ‘token.’ and the provided token using the private key of the discovered channel. This can be verified with the provided ‘key’ entry and provides assurance that the server is in possession of the private key for the discovered identity. After 1 January 2017, it is **required** that a server provides a signed_token  *if* a token has been specified in the request.

If no target is specified, the returned authorisations are generic authorisations for unknown or unauthenticated observers

Example of a detection package for ‘`mike@zothub.com`’

```
    {

    ‘success’: true,
    ‘signed_token’: "KBJrKTq1qrctNuxF3GwVh3GAGRqmgkirlXANPcJZAeWlvSt_9TMV097slR4AYnYCBEushbVqHEJ9Rb5wHTa0HzMbfRo8cRdl2yAirvvv5d98dtwHddQgX1jB0xEypXtmIYMdPGDLvhI1RNdIBhHkkrRcNreRzoy4xD- -HM6m1W0-A8PJJ9BcNxmGPcBtLzW08wzoP9trJ3M7DQ6Gkk6j7iwVsyApw1ZBaDvabGTdc_SFV-Iegtqw3rjzT_xXWsfzMlKBy-019MYn_KS- gu23YzjvGu5tS_zDfkQb8DMUlPLz5yyxM0yOMlUDtG2qQgIJAU2O0X6T5xDdJ6mtolNyhepg845PvFDEqBQGMIH1nc47CNumeudDi8IWymEALhjG_U8KAK7JVlQTJj2EKUb0au1g6fpiBFab5mmxCMtZEX3Jreyak5GOcFFz- WpxuXJD9TdSoIvaBfBFOoJnXkg2zE4RHXeQzZ2FotmrbBG5dm8B- _6byYGoHBc08ZsWze1K96JIeRnLpBaj6ifUDcVHxZMPcGHHT27dvU2PNbgLiBjlAsxhYqkhN5qOHN8XBcg2KRjcMBaI3V0YMxlzXz5MztmZq3fcB1p-ccIoIyMPMzSj3yMB7J9CEU2LYPSTHMdPkIeDE6GaCkQKviaQJQde346tK_YjA2k7_SOBmvPYE’,
    ‘guid’: ‘sebQ-IC4rmFn9d9iu17m4BXO-kHuNutWo2ySjeV2SIW1LzksUkss12xVo3m3fykYxN5HMcc7gUZVYv26asx-Pg’,
    ‘guid_sig’: "Llenlbl4zHo6-g4sa63MlQmTP5dRCrsPmXHHFmoCHG63BLq5CUZJRLS1vRrrrr_MNxr7zob_Ykt_m5xPKe5H0_i4pDj-UdP8dPZqH2fqhhx00kuYL4YUMJ8gRr5eO17vsZQ3XxTcyKewtgeW0j7ytwMp6- hFVUx_Cq08MrXas429ZrjzaEwgTfxGnbgeQYQ0R5EXpHpEmoERnZx77VaEahftmdjAUx9R4YKAp13pGYadJOX5xnLfqofHQD8DyRHWeMJ4G1OfWPSOlXfRayrV_jhnFlZjMU7vOdQwHoCMoR5TFsRsHuzd- qepbvo3pzvQZRWnTNu6oPucgbf94p13QbalYRpBXKOxdTXJrGdESNhGvhtaZnpT9c1QVqC46jdfP0LOX2xrVdbvvvG2JMWFv7XJUVjLSk_yjzY6or2VD4V6ztYcjpCi9d_WoNHruoxro_br1YO3KatySxJs-LQ7SOkQI60FpysfbphNyvYMkotwUFI59G08IGKTMu3- GPnV1wp7NOQD1yzJbGGEGSEEysmEP0SO9vnN45kp3MiqbffBGc1r4_YM4e7DPmqOGM94qksOcLOJk1HNESw2dQYWxWQTBXPfOJT6jW9_crGLMEOsZ3Jcss0XS9KzBUA2p_9osvvhUKuKXbNztqH0oZIWlg37FEVsDs_hUwUJpv2Ar09k4’,
    ‘key’: "-----BEGIN PUBLIC KEY----- \nMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA7QCwvuEIwCHjhjbpz3Oc\ntyei/Pz9nDksNbsc44Cm8jxYGMXsTPFXDZYCcCB5rcAhPPdZSlzaPkv4vPVcMIrw\n5cdX0tvbwa3rNTng6uFE7qkt15D3YCTkwF0Y9FVZiZ2Ko+G23QeBt9wqb9dlDN1d\nuPmu9BLYXIT/JXoBwf0vjIPFM9WBi5W/EHGaiuqw7lt0qI7zDGw77yO5yehKE4cu\n7dt3SakrXphL70LGiZh2XGoLg9Gmpz98t+gvPAUEotAJxIUqnoiTA8jlxoiQjeRK\nHlJkwMOGmRNPS33awPos0kcSxAywuBbh2X3aSqUMjcbE4cGJ++/13zoa6RUZRObC\nZnaLYJxqYBh13/N8SfH7d005hecDxWnoYXeYuuMeT3a2hV0J84ztkJX5OoxIwk7S\nWmvBq4+m66usn6LNL+p5IAcs93KbvOxxrjtQrzohBXc6+elfLVSQ1Rr9g5xbgpub\npSc+hvzbB6p0tleDRzwAy9X16NI4DYiTj4nkmVjigNo9v2VPnAle5zSam86eiYLO\nt2u9YRqysMLPKevNdj3CIvst+BaGGQONlQalRdIcq8Lin+BhuX+1TBgqyav4XD9K\nd+JHMb1aBk/rFLI9/f2S3BJ1XqpbjXz7AbYlaCwKiJ836+HS8PmLKxwVOnpLMbfH\nPYM8k83Lip4bEKIyAuf02qkCAwEAAQ==\n- ----END PUBLIC KEY-----\n’,
    ‘name’: ‘Mike Macgirvin’,
    ‘name_updated’: ‘2012-12-06 04:59:13’,
    ‘address’: ‘mike@zothub.com’,
    ‘photo_mimetype’: ‘image/jpeg’,
    ‘photo’: ‘https://zothub.com/photo/profile/l/1’,
    ‘photo_updated’: ‘2012-12-06 05:06:11’,
    ‘url’: ‘https://zothub.com/channel/mike’,
    ‘connections_url’: ‘https://zothub.com/poco/mike’,
    ‘target’: ‘’,
    ‘target_sig’: ‘’,
    ‘searchable’: false,
    ‘permissions’: {
        ‘view_stream’: true,
        ‘view_profile’: true,
        ‘view_photos’: true,
        ‘view_contacts’: true,
        ‘view_storage’: true,
        ‘view_pages’: true,
        ‘send_stream’: false,
        ‘post_wall’: false,
        ‘post_comments’: false,
        ‘post_mail’: false,
        ‘post_photos’: false,
        ‘tag_deliver’: false,
        ‘chat’: false,
        ‘write_storage’: false,
        ‘write_pages’: false,
        ‘delegate’: false
    },
    ‘profile’: {
        ‘description’: ‘Freedom Fighter’,
        ‘birthday’: ‘0000-05-14’,
        ‘next_birthday’: ‘2013-05-14 00:00:00’,
        ‘gender’: ‘Male’,
        ‘marital’: ‘It's complicated’,
        ‘sexual’: ‘Females’,
        ‘locale’: ‘’,
        ‘region’: ‘’,
        ‘postcode’: ‘’,
        ‘country’: ‘Australia’
    },
    ‘locations’: [
        {
            ‘host’: ‘zothub.com’,
            ‘address’: ‘mike@zothub.com’,
            ‘primary’: true,
            ‘url’: ‘https://zothub.com’,
            ‘url_sig’: "eqkB_9Z8nduBYyyhaSQPPDN1AhSm5I4R0yfcFxPeFpuu17SYk7jKD7QzvmsyahM5Kq7vDW6VE8nx8kdFYpcNaurqw0_IKI2SWg15pGrhkZfrCnM- g6A6qbCv_gKCYqXvwpSMO8SMIO2mjQItbBrramSbWClUd2yO0ZAceq3Z_zhirCK1gNm6mGRJaDOCuuTQNb6D66TF80G8kGLklv0o8gBfxQTE12Gd0ThpUb5g6_1L3eDHcsArW_RWM2XnNPi_atGNyl9bS_eLI2TYq0fuxkEdcjjYx9Ka0- Ws-lXMGpTnynQNCaSFqy-Fe1aYF7X1JJVJIO01LX6cCs- kfSoz29ywnntj1I8ueYldLB6bUtu4t7eeo__4t2CUWd2PCZkY3PKcoOrrnm3TJP5_yVFV_VpjkcBCRj3skjoCwISPcGYrXDufJxfp6bayGKwgaCO6QoLPtqqjPGLFm- fbn8sVv3fYUDGilaR3sFNxdo9mQ3utxM291XE2Pd0jGgeUtpxZSRzBuhYeOybu9DPusID320QbgNcbEbEbEImO8DuGIxuVRartzEXQF4WSYRdraZzbOqCzmU0O55P836JAfrWjgxTQkXlYCic- DBk-iE75JeT72smCtZ4AOtoFWCjZAABCw42J7JELY9APixZXWriKtjy6JI0G9d3fs6r7SrXr1JMy0’,
            ‘callback’: ‘https://zothub.com/post’,
            ‘sitekey’: "-----BEGIN PUBLIC KEY----- \nMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA1IWXwd/BZuevq8jzNFoR\n3VkenduQH2RpR3Wy9n4+ZDpbrUKGJddUGm/zUeWEdKMVkgyllVA/xHdB7jdyKs1X\nuIet9mIdnzvhdLO/JFD5hgbNG2wpSBIUY6aSNeCFTzszqXmuSXMW5U0Ef5pCbzEA\nnhoCoGL1KAgPqyxnGKUlj7q2aDwC9IRNtAqNyFQL67oT91vOQxuMThjlDhbR/29Q\ncYR4i1RzyahgEPCnHCPkT2GbRrkAPjNZAdlnk9UesgP16o8QB3tE2j50TVrbVc/d\nYRbzC56QMPP9UgUsapNeSJBHji75Ip/E5Eg/kfJC/HEQgyCqjCGfb7XeUaeQ7lLO\nqc7CGuMP+Jqr/cE54/aSHg8boTwxkMp11Ykb+ng17fl57MHTM2RJ99qZ1KBkXezR\nuH1lyvjzeJPxEFr9rkUqc4GH74/AgfbgaFvQc8TS7ovEa5I/7Pg04m7vLSEYc6UF\nYJYxXKrzmZT2TDoKeJzaBBx5MFLhW19l68h9dQ8hJXIpTP0hJrpI+Sr6VUAEfFQC\ndIDRiFcgjz6j7T/x8anqh63/hpsyf2PMYph1+4/fxtSCWJdvf+9jCRM8F1IDfluX\n87gm+88KBNaklYpchmGIohbjivJyru41CsSLe0uinQFvA741W00w6JrcrOAX+hkS\nRQuK1dDVwGKoIY85KtTUiMcCAwEAAQ==\n- ----END PUBLIC KEY-----\n"
        }
    ],
    ‘site’: {
        ‘url’: ‘https://zothub.com’,
        ‘directory_mode’: ‘primary’,
        ‘directory_url’: ‘https://zothub.com/dirsearch’
    }

    }
```

Discovery returns a JSON array with the following components:

‘success’ => (true or false) Operation was successful if true. Otherwise, there may be an optional ‘message’ indicating the cause of the error.

‘signed_token’ => If a token parameter was specified in the request, it is prefixed with the text ‘token.’ and then RSA-signed and base64url-encoded with the private key of the channel and returned as ‘signed_token’.

guid' => the guid of the address on the target system

guid_sig’ => the RSA signature of the guid encoded with base64url, signed with the private key associated with this guid.

‘key’ => the public key associated with this guid

name' => name of the channel

name_updated' => timestamp in MySQL style “2012-01-01 00:00:00”, when the name was last changed (UTC)

address' => “webbie” or user@host address associated with this channel

photo' => URL of a profile photo for this channel (ideally 175x175)

photo_mimetype' => content type of the profile photo

photo_updated' => MySQL-style timestamp of when the photo was last updated (UTC) 

‘url’ => location of the channel homepage

‘connections_url’ => location of the URL for portable contacts (extended for Nomad) for this channel

target' => if an authorisation target has been specified, it will be mirrored

‘target_sig’ => if an authorisation target has been specified, the signature is mirrored

‘searchable’ => (true or false) true means that this entry can be searched for in a directory

##### Authorisations   

‘permissions’ => expandable array of permissions suitable for this target, values are true or false
Permissions can include

- view_stream
- view_profile
- view_photos
- view_contacts
- view_storage
- view_pages
- send_stream
- post_wall
- post_comments
- post_mail
- post_photos
- tag_deliver
- chat
- write_storage
- write_pages
- delegate

##### profile    

‘Profile’ => Array with important profile fields

- description
- birthday YYYY-MM-DD , all fields are optional, any field (such as year) may be zero
- next_birthday => MySQL datetime string representing the next upcoming birthday, converted from the channel's default timezone to UTC. 
- gender (free form)
- marital (marital status)
- sexual (preference)
- locale (city)
- region (state)
- postcode
- country

##### locations    

‘locations’ => Array of registered locations (DNS locations) from which this channel can be visible or can be booked

Each location is an array of

‘host’ => DNS host name, e.g. example.com

‘address’ => the webbie or user@host identifier associated with this location

‘primary’ => (true or false) whether this is the primary location for this channel where files and web pages are generally found

‘url’ => url of the root directory of this DNS location, e.g. `https://example`.

url_sig' => base64url encoded RSA signature of the URL, signed with the private key of the channel

‘callback’ => Nomad communication endpoint on this site, normally `https://example.com/post`

sitekey' => public key of this site/host

##### site    

‘site’ => array specifying the directory role of the site responding to this request

url' => url of this site e.g. `https://example`.

directory_mode' => one of “primary”, “secondary”, “normal” or “standalone”

directory_url' => if it is a directory server or a standalone site, the URL for the directory search module