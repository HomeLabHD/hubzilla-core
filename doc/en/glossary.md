### Glossary   

- **Hub** 
  An instance of this software running on a standard web server 
- **Grid** 
  The global network of hubs that exchange information with each other using the Zot protocol. 
- **Channel** 
  The basic identity in the grid. A channel can represent a person, a blog or a forum, to name but a few. Channels can connect to other channels to share information with very detailed permissions. 
- **Cloning** 
  Channels can have clones that are connected to separate and otherwise unconnected accounts on independent hubs. Communication shared with a channel is synchronised between the channel clones, allowing a channel to send and receive messages and access shared content from multiple hubs. This provides resilience in the event of network and hardware failures, which can be a major problem for self-hosted or resource-constrained web servers. Cloning allows you to completely move a channel from one hub to another, taking your data and connections with you. See nomadic identity. 
- **nomadic identity** 
  The ability to authenticate and easily migrate an identity across independent hubs and web domains. Nomadic identity provides true ownership of an online identity, as the identities of the channels controlled by an account on a hub are not tied to the hub itself. A hub is more of a ‘host’ for channels. With Hubzilla, you don't have an ‘account’ on a server like you do with typical websites, but you own an identity that you can take with you across the web by using clones. 
- **Nomad**
  The new JSON-based protocol for implementing secure decentralised communication and services. It differs from many other communication protocols in that it builds communication on a decentralised identity and authentication framework. The authentication component is similar to the OpenID concept, but is isolated from DNS-based identities. As far as possible, remote authentication is silent and invisible. This provides a mechanism for distributed access control on the Internet that is unobtrusive.
  
  The protocol was originally called Zot. In 2021 it was renamed Nomad by Mike Macgirvin. A distinction is now made between the protocol and the implementation (software) of the protocol. The implementation is still called Zot at Hubzilla (more precisely Zot6, because it continues the implementation of the then identically named protocol in version 6).
  
  In general, the protocol is now only referred to as Nomad. When the term Zot or Zot6 (usually in the form ‘Nomad/Zot6’) is used, the Nomad protocol is meant when referring to the protocol. Zot or Zot6 only appear independently in the area of software development at Hubzilla, because the routines and the programme library that implement Nomad in practice bear this name.
  
  <u>Important:</u> The implementations of Nomad in Hubzilla and in (streams) are partially incompatible with each other. This applies in particular to the nomadic identity. It is therefore not possible to clone a Hubzilla channel (Nomad v. Zot6) on a (streams) instance (Nomad v, Zot12), and vice versa.