### The Nomad protocol 

#### What is Nomad?  

Nomad is the revolutionary protocol that powers Hubzilla, enabling **communication**, **identity management** and **access control** across a fully **decentralised** network of independent websites, often referred to as ‘the grid’. The resulting platform is a robust system that supports privacy and security while enabling the kind of rich web services typically found only in centralised, proprietary solutions.

Consider this typical scenario:

Jaquelina wants to share photos from her blog at **jaquelina.org** with Roberto, but with no one else. Roberto maintains his own family hub at **roberto.net** on a completely independent server. Nomad allows Jaquelina to publish her photos with an *access control list (ACL)* that only includes Roberto. This means that while Roberto can see the photos when he visits her blog, his brother Marco cannot, nor can any other family member who has an account on **roberto.net**.

The twist in this scenario is the fact that Roberto never logged in to Jaquelina's website. Instead, he only had to log in once with his password on his *own* **roberto.net** website. When Roberto visits **jaquelina.org**, he is seamlessly authenticated by their hub by querying his server in the background. 

It's not uncommon for servers to have technical issues or become inaccessible for various reasons. Nomad provides robustness to Roberto's online activities by allowing him to have *clones* of his online identity or *channel* on multiple independent hubs. Imagine that Roberto's server goes down for some reason and he can no longer log in there. He simply logs into one of his clones on **gadfly.com**, a website run by his friend Peter. Once he has authenticated himself at **gadfly.com**, Roberto can view Jaquelina's blog as before, without Jaquelina having to grant additional access!

#### Communication  

Communication and social networks are an essential part of the grid. Any channel (and any service provided by that channel) can take full advantage of feature-rich social communication on a global scale. These communications can be public or private - and private communications include not only fully encrypted transport, but also encrypted storage to protect against accidental snooping and disclosure by rogue system administrators and ISPs.

Nomad supports a wide range of background services in the grid, from friend suggestions to directory services. New content and data updates are passed in the background between hubs across the grid according to the access control lists and permissions set by the *sender and* receiver channels. Data is also synchronised between any number of channel clones so that hub members can access data and continue to collaborate seamlessly even if their primary hub is unavailable or offline.

#### Identity  

Nomad's identity layer is unique. It provides an **invisible single sign-on** for all locations in the grid. 
It also provides a **nomadic identity** so that your communication with friends, family or other people you communicate with is not affected by the loss of your primary communication hub - either temporarily or permanently.

The important parts of your identity and relationships can be backed up on a USB stick or your laptop and appear on any node on the network at any time - with all your friends and preferences. 
Crucially, these nomadic instances are kept synchronised so that any instance can take over if another is compromised or corrupted. This not only protects you from major system failures, but also from temporary website overload and government tampering or censorship.

We believe that Hubzilla's nomadic identity, single sign-on and decentralisation bring a high level of **resilience** and **consistency** to internet communications, which is much needed in the face of global trends towards corporate centralisation and mass and indiscriminate government surveillance and censorship.
When browsing the web, viewing channels and their unique content, you are seamlessly authenticated, even across completely different server hubs. No need to enter passwords. You don't have to type anything. You are simply greeted with your name on every new page you visit.

How does this work with Nomad? We call it **‘magic-auth’** because Hubzilla hides the details of the complexity of single sign-on logins and nomadic identities from web browsing. This is one of Hubzilla's design goals: to increase privacy and freedom on the Internet while reducing the complexity and tedium of having to enter new passwords and login names every time you visit the Internet. You only log in once to your home hub (or a nomadic backup hub of your choice). This allows you to access all authenticated services offered anywhere on the web - such as shopping, blogs, forums and access to private information. Your password is not stored on a thousand different websites, but on servers that you control or trust.

They cannot be silenced. They cannot be removed from the network unless you choose to leave it yourself.

#### Access control  

Nomad's identity layer allows you to assign fine-grained permissions to any content you want to publish - and these permissions extend across the entire grid. It's like having a huge website made up of an army of small individual websites - where each channel in the grid can fully control its privacy and sharing preferences for all the web resources it creates.

Currently, Hubzilla supports access control for many types of data, including discussion posts and comments, photo albums, events, cloud files, web pages, wikis and more. Each item and how and with whom it is shared is completely under your control.

This type of control is trivial with large enterprise providers as they own the user database. In the Grid, you don't need a huge user database on your computer - because the Grid **is** your user database. It has essentially infinite capacity (limited by the total number of hubs online on the Internet) and is distributed across hundreds, possibly even millions of computers.

Access can be granted or denied to any resource, channel or group of channels - anywhere on the grid. Others can access your content if you allow them to, and they don't even need to have an account in your hub.